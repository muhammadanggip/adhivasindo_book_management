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
            'id' => $this->resource->id ?? null,
            'user_id' => $this->resource->user_id ?? null,
            'book_id' => $this->resource->book_id ?? null,
            'user_name' => $this->resource->user_name ?? null,
            'user_email' => $this->resource->user_email ?? null,
            'book_title' => $this->resource->book_title ?? null,
            'book_author' => $this->resource->book_author ?? null,
            'loaned_at' => $this->resource->loaned_at ?? null,
            'expected_return_at' => $this->resource->expected_return_at ?? null,
            'returned_at' => $this->resource->returned_at ?? null,
            'is_returned' => !is_null($this->resource->returned_at ?? null),
            'is_overdue' => ($this->resource->expected_return_at ?? null) &&
                           \Carbon\Carbon::parse($this->resource->expected_return_at)->isPast() &&
                           is_null($this->resource->returned_at ?? null),
            'created_at' => $this->resource->created_at ?? null,
            'updated_at' => $this->resource->updated_at ?? null,
        ];
    }
}
