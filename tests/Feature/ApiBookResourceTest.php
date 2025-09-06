<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ApiBookResourceTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a user and authenticate
        $user = User::factory()->create();
        Sanctum::actingAs($user);
    }

    public function test_books_index_returns_resource_collection(): void
    {
        // Create some books
        Book::factory()->count(3)->create();

        $response = $this->getJson('/api/books');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'data' => [
                         '*' => [
                             'id',
                             'title',
                             'author',
                             'published_year',
                             'isbn',
                             'stock',
                             'available_stock',
                             'is_available',
                             'created_at',
                             'updated_at'
                         ]
                     ],
                     'links',
                     'meta'
                 ]);

        // Verify that the response contains the expected fields
        $data = $response->json('data');
        $this->assertCount(3, $data);

        foreach ($data as $book) {
            $this->assertArrayHasKey('id', $book);
            $this->assertArrayHasKey('title', $book);
            $this->assertArrayHasKey('author', $book);
            $this->assertArrayHasKey('available_stock', $book);
            $this->assertArrayHasKey('is_available', $book);
        }
    }

    public function test_books_show_returns_single_resource(): void
    {
        $book = Book::factory()->create([
            'title' => 'Test Book',
            'author' => 'Test Author',
            'stock' => 5
        ]);

        $response = $this->getJson("/api/books/{$book->id}");

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'data' => [
                         'id',
                         'title',
                         'author',
                         'published_year',
                         'isbn',
                         'stock',
                         'available_stock',
                         'is_available',
                         'created_at',
                         'updated_at'
                     ]
                 ])
                 ->assertJson([
                     'data' => [
                         'id' => $book->id,
                         'title' => 'Test Book',
                         'author' => 'Test Author',
                         'stock' => 5,
                         'available_stock' => 5,
                         'is_available' => true
                     ]
                 ]);
    }

    public function test_books_store_returns_resource(): void
    {
        $bookData = [
            'title' => 'New Book',
            'author' => 'New Author',
            'published_year' => 2024,
            'isbn' => '1234567890123',
            'stock' => 10
        ];

        $response = $this->postJson('/api/books', $bookData);

        $response->assertStatus(201)
                 ->assertJsonStructure([
                     'data' => [
                         'id',
                         'title',
                         'author',
                         'published_year',
                         'isbn',
                         'stock',
                         'available_stock',
                         'is_available',
                         'created_at',
                         'updated_at'
                     ]
                 ])
                 ->assertJson([
                     'data' => [
                         'title' => 'New Book',
                         'author' => 'New Author',
                         'published_year' => 2024,
                         'isbn' => '1234567890123',
                         'stock' => 10,
                         'available_stock' => 10,
                         'is_available' => true
                     ]
                 ]);

        $this->assertDatabaseHas('books', [
            'title' => 'New Book',
            'author' => 'New Author',
            'isbn' => '1234567890123'
        ]);
    }

    public function test_books_update_returns_resource(): void
    {
        $book = Book::factory()->create([
            'title' => 'Original Title',
            'stock' => 5
        ]);

        $updateData = [
            'title' => 'Updated Title',
            'stock' => 8
        ];

        $response = $this->putJson("/api/books/{$book->id}", $updateData);

        $response->assertStatus(200)
                 ->assertJson([
                     'data' => [
                         'id' => $book->id,
                         'title' => 'Updated Title',
                         'stock' => 8,
                         'available_stock' => 8
                     ]
                 ]);

        $this->assertDatabaseHas('books', [
            'id' => $book->id,
            'title' => 'Updated Title',
            'stock' => 8
        ]);
    }

    public function test_books_with_loans_show_correct_available_stock(): void
    {
        $book = Book::factory()->create(['stock' => 5]);
        $user = User::factory()->create();

        // Create some loans (1 unreturned, 1 returned)
        \DB::table('book_loans')->insert([
            [
                'user_id' => $user->id,
                'book_id' => $book->id,
                'loaned_at' => now(),
                'expected_return_at' => now()->addDays(7),
                'returned_at' => null,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);

        \DB::table('book_loans')->insert([
            [
                'user_id' => $user->id,
                'book_id' => $book->id,
                'loaned_at' => now()->subDays(1),
                'expected_return_at' => now()->addDays(6),
                'returned_at' => now(),
                'created_at' => now()->subDays(1),
                'updated_at' => now()
            ]
        ]);

        $response = $this->getJson("/api/books/{$book->id}");

        $response->assertStatus(200)
                 ->assertJson([
                     'data' => [
                         'stock' => 5,
                         'available_stock' => 4, // 5 - 1 unreturned loan
                         'is_available' => true
                     ]
                 ]);
    }

    public function test_books_search_and_filter_work_with_resources(): void
    {
        Book::factory()->create(['title' => 'Laravel Book', 'author' => 'John Doe', 'published_year' => 2023]);
        Book::factory()->create(['title' => 'PHP Book', 'author' => 'Jane Smith', 'published_year' => 2022]);
        Book::factory()->create(['title' => 'JavaScript Book', 'author' => 'John Doe', 'published_year' => 2023]);

        // Test search by title
        $response = $this->getJson('/api/books?search=Laravel');
        $response->assertStatus(200);
        $data = $response->json('data');
        $this->assertCount(1, $data);
        $this->assertEquals('Laravel Book', $data[0]['title']);

        // Test filter by author
        $response = $this->getJson('/api/books?author=John Doe');
        $response->assertStatus(200);
        $data = $response->json('data');
        $this->assertCount(2, $data);

        // Test filter by year
        $response = $this->getJson('/api/books?year=2023');
        $response->assertStatus(200);
        $data = $response->json('data');
        $this->assertCount(2, $data);
    }
}
