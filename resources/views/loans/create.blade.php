@extends('layouts.app')

@section('header')
    <div class="d-flex align-items-center">
        <div class="me-3">
            <div class="feature-icon bg-success text-white">
                <i class="bi bi-plus-circle"></i>
            </div>
        </div>
        <div>
            <h1 class="h3 mb-0 text-gradient">Create New Loan</h1>
            <p class="text-muted mb-0">Create a new book loan for a user</p>
        </div>
    </div>
@endsection

@section('content')
    <!-- Form Card -->
    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('loans.store') }}">
                @csrf

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-4">
                            <label for="user_id" class="form-label">
                                <i class="bi bi-person me-1"></i>User <span class="text-danger">*</span>
                            </label>
                            <select class="form-select @error('user_id') is-invalid @enderror"
                                    id="user_id" name="user_id" required>
                                <option value="">Select a user...</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}
                                            data-email="{{ $user->email }}">
                                        {{ $user->name }} ({{ $user->email }})
                                    </option>
                                @endforeach
                            </select>
                            @error('user_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-4">
                            <label for="book_id" class="form-label">
                                <i class="bi bi-book me-1"></i>Book <span class="text-danger">*</span>
                            </label>
                            <select class="form-select @error('book_id') is-invalid @enderror"
                                    id="book_id" name="book_id" required>
                                <option value="">Select a book...</option>
                                @foreach($books as $book)
                                    <option value="{{ $book->id }}"
                                            {{ old('book_id', request('book_id')) == $book->id ? 'selected' : '' }}
                                            data-stock="{{ $book->available_stock }}"
                                            data-total-stock="{{ $book->stock }}"
                                            data-author="{{ $book->author }}"
                                            data-isbn="{{ $book->isbn }}"
                                            data-year="{{ $book->published_year }}">
                                        {{ $book->title }} by {{ $book->author }} (Available: {{ $book->available_stock }}/{{ $book->stock }})
                                    </option>
                                @endforeach
                            </select>
                            @error('book_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Return Date -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-4">
                            <label for="expected_return_at" class="form-label">
                                <i class="bi bi-calendar-check me-1"></i>Expected Return Date
                            </label>
                            <input type="date"
                                   class="form-control @error('expected_return_at') is-invalid @enderror"
                                   id="expected_return_at"
                                   name="expected_return_at"
                                   value="{{ old('expected_return_at') }}"
                                   min="{{ date('Y-m-d') }}">
                            @error('expected_return_at')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                <i class="bi bi-info-circle me-1"></i>
                                Optional: Set expected return date. Leave empty if no specific return date.
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Book Information Display -->
                <div id="book-info" class="card mt-4" style="display: none;">
                    <div class="card-body">
                        <h6 class="card-title">
                            <i class="bi bi-info-circle me-2"></i>Selected Book Information
                        </h6>
                        <div id="book-details"></div>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-3 mt-4">
                    <a href="{{ route('loans.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i>Cancel
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-book me-1"></i>Create Loan
                    </button>
                </div>
            </form>
        </div>
    </div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Wait for Select2 to initialize
    setTimeout(function() {
        const bookSelect = $('#book_id');
        const bookInfo = document.getElementById('book-info');
        const bookDetails = document.getElementById('book-details');

        // Get all books data
        const books = @json($books);

        bookSelect.on('select2:select', function(e) {
            const selectedBookId = e.params.data.id;
            const selectedOption = $(this).find('option[value="' + selectedBookId + '"]');

            if (selectedBookId) {
                const availableStock = selectedOption.data('stock');
                const totalStock = selectedOption.data('total-stock');
                const author = selectedOption.data('author');
                const isbn = selectedOption.data('isbn');
                const year = selectedOption.data('year');
                const title = e.params.data.text.split(' by ')[0];

                bookDetails.innerHTML = `
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="d-flex align-items-center mb-2">
                                <i class="bi bi-book text-primary me-2"></i>
                                <strong>Title:</strong> ${title}
                            </div>
                            <div class="d-flex align-items-center mb-2">
                                <i class="bi bi-person text-primary me-2"></i>
                                <strong>Author:</strong> ${author}
                            </div>
                            <div class="d-flex align-items-center">
                                <i class="bi bi-upc text-primary me-2"></i>
                                <strong>ISBN:</strong> ${isbn}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-center mb-2">
                                <i class="bi bi-calendar text-primary me-2"></i>
                                <strong>Published Year:</strong> ${year}
                            </div>
                            <div class="d-flex align-items-center mb-2">
                                <i class="bi bi-box text-primary me-2"></i>
                                <strong>Available Stock:</strong>
                                <span class="badge ${availableStock > 0 ? 'bg-success' : 'bg-danger'} ms-2">${availableStock}</span>
                            </div>
                            <div class="d-flex align-items-center">
                                <i class="bi bi-stack text-primary me-2"></i>
                                <strong>Total Stock:</strong>
                                <span class="badge bg-info ms-2">${totalStock}</span>
                            </div>
                        </div>
                    </div>
                `;
                bookInfo.style.display = 'block';
            }
        });

        bookSelect.on('select2:clear', function() {
            bookInfo.style.display = 'none';
        });

        // Trigger change event if a book is pre-selected
        if (bookSelect.val()) {
            bookSelect.trigger('select2:select');
        }
    }, 100);
});
</script>
@endsection
