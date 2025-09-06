<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ApiBookLoanResourceTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a user and authenticate
        $user = User::factory()->create();
        Sanctum::actingAs($user);
    }

    public function test_loans_index_returns_resource_collection(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->create(['stock' => 5]);

        // Create a loan
        \DB::table('book_loans')->insert([
            'user_id' => $user->id,
            'book_id' => $book->id,
            'loaned_at' => now(),
            'expected_return_at' => now()->addDays(7),
            'created_at' => now(),
            'updated_at' => now()
        ]);

        $response = $this->getJson('/api/loans');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'data' => [
                         '*' => [
                             'id',
                             'user_id',
                             'book_id',
                             'user_name',
                             'user_email',
                             'book_title',
                             'book_author',
                             'loaned_at',
                             'expected_return_at',
                             'returned_at',
                             'is_returned',
                             'is_overdue',
                             'created_at',
                             'updated_at'
                         ]
                     ],
                     'links',
                     'meta'
                 ]);
    }

    public function test_loans_store_returns_resource(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->create(['stock' => 5]);

        $loanData = [
            'user_id' => $user->id,
            'book_id' => $book->id,
            'expected_return_at' => now()->addDays(7)->toISOString()
        ];

        $response = $this->postJson('/api/loans', $loanData);

        $response->assertStatus(201)
                 ->assertJsonStructure([
                     'id',
                     'user_id',
                     'book_id',
                     'user_name',
                     'user_email',
                     'book_title',
                     'book_author',
                     'loaned_at',
                     'expected_return_at',
                     'returned_at',
                     'is_returned',
                     'is_overdue',
                     'created_at',
                     'updated_at'
                 ])
                 ->assertJson([
                     'user_id' => $user->id,
                     'book_id' => $book->id,
                     'user_name' => $user->name,
                     'user_email' => $user->email,
                     'book_title' => $book->title,
                     'book_author' => $book->author,
                     'is_returned' => false
                 ]);
    }

    public function test_loans_show_returns_single_resource(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->create(['stock' => 5]);

        $loanId = \DB::table('book_loans')->insertGetId([
            'user_id' => $user->id,
            'book_id' => $book->id,
            'loaned_at' => now(),
            'expected_return_at' => now()->addDays(7),
            'created_at' => now(),
            'updated_at' => now()
        ]);

        $response = $this->getJson("/api/loans/{$loanId}");

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'id',
                     'user_id',
                     'book_id',
                     'user_name',
                     'user_email',
                     'book_title',
                     'book_author',
                     'loaned_at',
                     'expected_return_at',
                     'returned_at',
                     'is_returned',
                     'is_overdue',
                     'created_at',
                     'updated_at'
                 ])
                 ->assertJson([
                     'id' => $loanId,
                     'user_id' => $user->id,
                     'book_id' => $book->id,
                     'user_name' => $user->name,
                     'user_email' => $user->email,
                     'book_title' => $book->title,
                     'book_author' => $book->author,
                     'is_returned' => false
                 ]);
    }

    public function test_loans_return_updates_resource(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->create(['stock' => 5]);

        $loanId = \DB::table('book_loans')->insertGetId([
            'user_id' => $user->id,
            'book_id' => $book->id,
            'loaned_at' => now(),
            'expected_return_at' => now()->addDays(7),
            'created_at' => now(),
            'updated_at' => now()
        ]);

        $response = $this->putJson("/api/loans/{$loanId}/return");

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'id',
                     'user_id',
                     'book_id',
                     'user_name',
                     'user_email',
                     'book_title',
                     'book_author',
                     'loaned_at',
                     'expected_return_at',
                     'returned_at',
                     'is_returned',
                     'is_overdue',
                     'created_at',
                     'updated_at'
                 ])
                 ->assertJson([
                     'id' => $loanId,
                     'is_returned' => true
                 ]);

        // Verify returned_at is not null
        $data = $response->json();
        $this->assertNotNull($data['returned_at']);
    }

    public function test_loans_resource_shows_overdue_status(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->create(['stock' => 5]);

        $loanId = \DB::table('book_loans')->insertGetId([
            'user_id' => $user->id,
            'book_id' => $book->id,
            'loaned_at' => now()->subDays(10),
            'expected_return_at' => now()->subDays(3), // Overdue
            'created_at' => now()->subDays(10),
            'updated_at' => now()->subDays(10)
        ]);

        $response = $this->getJson("/api/loans/{$loanId}");

        $response->assertStatus(200)
                 ->assertJson([
                     'id' => $loanId,
                     'is_returned' => false,
                     'is_overdue' => true
                 ]);
    }

    public function test_user_loans_returns_resource_collection(): void
    {
        $user = User::factory()->create();
        $book1 = Book::factory()->create(['stock' => 5]);
        $book2 = Book::factory()->create(['stock' => 3]);

        // Create loans for the user
        \DB::table('book_loans')->insert([
            [
                'user_id' => $user->id,
                'book_id' => $book1->id,
                'loaned_at' => now(),
                'expected_return_at' => now()->addDays(7),
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'user_id' => $user->id,
                'book_id' => $book2->id,
                'loaned_at' => now()->subDays(1),
                'expected_return_at' => now()->addDays(6),
                'returned_at' => now(),
                'created_at' => now()->subDays(1),
                'updated_at' => now()
            ]
        ]);

        $response = $this->getJson("/api/loans/user/{$user->id}");

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'data' => [
                         '*' => [
                             'id',
                             'user_id',
                             'book_id',
                             'user_name',
                             'user_email',
                             'book_title',
                             'book_author',
                             'loaned_at',
                             'expected_return_at',
                             'returned_at',
                             'is_returned',
                             'is_overdue',
                             'created_at',
                             'updated_at'
                         ]
                     ]
                 ]);

        $data = $response->json('data');
        $this->assertCount(2, $data);

        // Check that both loans belong to the same user
        foreach ($data as $loan) {
            $this->assertEquals($user->id, $loan['user_id']);
            $this->assertEquals($user->name, $loan['user_name']);
            $this->assertEquals($user->email, $loan['user_email']);
        }
    }
}
