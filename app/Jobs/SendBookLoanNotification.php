<?php

namespace App\Jobs;

use App\Models\Book;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class SendBookLoanNotification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public User $user,
        public Book $book
    ) {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Log the notification (since we're using log driver for testing)
        Log::info('Book Loan Notification', [
            'user_id' => $this->user->id,
            'user_name' => $this->user->name,
            'user_email' => $this->user->email,
            'book_id' => $this->book->id,
            'book_title' => $this->book->title,
            'book_author' => $this->book->author,
            'loaned_at' => now(),
            'message' => "User {$this->user->name} has successfully borrowed the book '{$this->book->title}' by {$this->book->author}."
        ]);
    }
}
