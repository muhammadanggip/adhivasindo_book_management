<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Queue;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class BookLoanTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_can_loan_book(): void
    {
        Queue::fake();

        $user = User::factory()->create();
        $book = Book::factory()->create(['stock' => 5]);

        Sanctum::actingAs($user);

        $loanData = [
            'user_id' => $user->id,
            'book_id' => $book->id,
        ];

        $response = $this->postJson('/api/loans', $loanData);

        $response->assertStatus(201)
                ->assertJsonStructure([
                    'message',
                    'loan' => [
                        'user_id',
                        'book_id',
                        'loaned_at',
                    ]
                ]);

        $this->assertDatabaseHas('book_loans', [
            'user_id' => $user->id,
            'book_id' => $book->id,
            'returned_at' => null,
        ]);

        Queue::assertPushed(\App\Jobs\SendBookLoanNotification::class);
    }

    public function test_cannot_loan_unavailable_book(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->create(['stock' => 0]);

        Sanctum::actingAs($user);

        $loanData = [
            'user_id' => $user->id,
            'book_id' => $book->id,
        ];

        $response = $this->postJson('/api/loans', $loanData);

        $response->assertStatus(400)
                ->assertJson([
                    'message' => 'Book is not available for loan'
                ]);
    }

    public function test_cannot_loan_same_book_twice(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->create(['stock' => 5]);

        Sanctum::actingAs($user);

        // First loan
        $loanData = [
            'user_id' => $user->id,
            'book_id' => $book->id,
        ];

        $this->postJson('/api/loans', $loanData);

        // Second loan attempt
        $response = $this->postJson('/api/loans', $loanData);

        $response->assertStatus(400)
                ->assertJson([
                    'message' => 'User already has this book borrowed'
                ]);
    }

    public function test_can_get_user_loans(): void
    {
        $user = User::factory()->create();
        $book1 = Book::factory()->create();
        $book2 = Book::factory()->create();

        Sanctum::actingAs($user);

        // Create loans
        $user->books()->attach($book1->id, ['loaned_at' => now()]);
        $user->books()->attach($book2->id, ['loaned_at' => now()]);

        $response = $this->getJson("/api/loans/user/{$user->id}");

        $response->assertStatus(200)
                ->assertJsonCount(2);
    }

    public function test_can_return_book(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();

        Sanctum::actingAs($user);

        // Create loan
        $loan = $user->books()->attach($book->id, ['loaned_at' => now()]);
        $loanId = \DB::table('book_loans')
            ->where('user_id', $user->id)
            ->where('book_id', $book->id)
            ->first()->id;

        $response = $this->putJson("/api/loans/{$loanId}", [
            'returned_at' => now(),
        ]);

        $response->assertStatus(200)
                ->assertJson([
                    'message' => 'Loan updated successfully'
                ]);

        $this->assertDatabaseHas('book_loans', [
            'id' => $loanId,
            'returned_at' => now()->format('Y-m-d H:i:s'),
        ]);
    }

    public function test_can_list_all_loans(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $book1 = Book::factory()->create();
        $book2 = Book::factory()->create();

        Sanctum::actingAs($user1);

        // Create loans
        $user1->books()->attach($book1->id, ['loaned_at' => now()]);
        $user2->books()->attach($book2->id, ['loaned_at' => now()]);

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
                            'returned_at',
                            'is_returned',
                        ]
                    ]
                ]);
    }

    public function test_validates_loan_creation(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/loans', []);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['user_id', 'book_id']);
    }

    public function test_validates_existing_user_and_book(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $loanData = [
            'user_id' => 999, // Non-existent user
            'book_id' => 999, // Non-existent book
        ];

        $response = $this->postJson('/api/loans', $loanData);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['user_id', 'book_id']);
    }
}
