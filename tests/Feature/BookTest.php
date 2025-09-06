<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class BookTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_can_list_books(): void
    {
        Book::factory(5)->create();

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
                            'updated_at',
                        ]
                    ]
                ]);
    }

    public function test_can_create_book(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $bookData = [
            'title' => 'Test Book',
            'author' => 'Test Author',
            'published_year' => 2023,
            'isbn' => '978-0-123456-78-9',
            'stock' => 5,
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
                    ]
                ]);

        $this->assertDatabaseHas('books', $bookData);
    }

    public function test_can_show_book(): void
    {
        $book = Book::factory()->create();

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
                    ]
                ]);
    }

    public function test_can_update_book(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $book = Book::factory()->create();

        $updateData = [
            'title' => 'Updated Book Title',
            'stock' => 10,
        ];

        $response = $this->putJson("/api/books/{$book->id}", $updateData);

        $response->assertStatus(200);

        $this->assertDatabaseHas('books', [
            'id' => $book->id,
            'title' => 'Updated Book Title',
            'stock' => 10,
        ]);
    }

    public function test_can_delete_book(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $book = Book::factory()->create();

        $response = $this->deleteJson("/api/books/{$book->id}");

        $response->assertStatus(200);

        $this->assertDatabaseMissing('books', ['id' => $book->id]);
    }

    public function test_can_search_books_by_title(): void
    {
        Book::factory()->create(['title' => 'Laravel Guide']);
        Book::factory()->create(['title' => 'PHP Basics']);
        Book::factory()->create(['title' => 'JavaScript Advanced']);

        $response = $this->getJson('/api/books?search=Laravel');

        $response->assertStatus(200);
        $response->assertJsonCount(1, 'data');
    }

    public function test_can_filter_books_by_author(): void
    {
        Book::factory()->create(['author' => 'John Doe']);
        Book::factory()->create(['author' => 'Jane Smith']);
        Book::factory()->create(['author' => 'John Smith']);

        $response = $this->getJson('/api/books?author=John');

        $response->assertStatus(200);
        $response->assertJsonCount(2, 'data');
    }

    public function test_can_filter_books_by_year(): void
    {
        Book::factory()->create(['published_year' => 2020]);
        Book::factory()->create(['published_year' => 2021]);
        Book::factory()->create(['published_year' => 2022]);

        $response = $this->getJson('/api/books?year=2021');

        $response->assertStatus(200);
        $response->assertJsonCount(1, 'data');
    }

    public function test_validates_book_creation(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/books', []);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['title', 'author', 'published_year', 'isbn', 'stock']);
    }

    public function test_validates_unique_isbn(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $existingBook = Book::factory()->create(['isbn' => '978-0-123456-78-9']);

        $bookData = [
            'title' => 'Another Book',
            'author' => 'Another Author',
            'published_year' => 2023,
            'isbn' => '978-0-123456-78-9', // Same ISBN
            'stock' => 3,
        ];

        $response = $this->postJson('/api/books', $bookData);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['isbn']);
    }
}
