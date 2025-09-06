<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookLoanResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'book_id' => $this->book_id,
            'user_name' => $this->user_name ?? null,
            'user_email' => $this->user_email ?? null,
            'book_title' => $this->book_title ?? null,
            'book_author' => $this->book_author ?? null,
            'loaned_at' => $this->loaned_at,
            'expected_return_at' => $this->expected_return_at,
            'returned_at' => $this->returned_at,
            'is_returned' => !is_null($this->returned_at),
            'is_overdue' => $this->expected_return_at && 
                           \Carbon\Carbon::parse($this->expected_return_at)->isPast() && 
                           is_null($this->returned_at),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
