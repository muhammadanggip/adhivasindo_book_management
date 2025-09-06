@extends('layouts.app')

@section('header')
    <div class="d-flex align-items-center">
        <div class="me-3">
            <div class="feature-icon bg-info text-white">
                <i class="bi bi-book"></i>
            </div>
        </div>
        <div>
            <h1 class="h3 mb-0 text-gradient">Book Details</h1>
            <p class="text-muted mb-0">{{ $book->title }} by {{ $book->author }}</p>
        </div>
    </div>
@endsection

@section('content')
    <div class="row">
        <!-- Book Information -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-4">
                        <h3 class="h4 mb-0">{{ $book->title }}</h3>
                        <span class="badge {{ $book->isAvailable() ? 'bg-success' : 'bg-danger' }} fs-6">
                            <i class="bi bi-{{ $book->isAvailable() ? 'check-circle' : 'x-circle' }} me-1"></i>
                            {{ $book->isAvailable() ? 'Available' : 'Not Available' }}
                        </span>
                    </div>

                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="d-flex align-items-center mb-3">
                                <div class="stat-icon bg-primary text-white me-3" style="width: 2.5rem; height: 2.5rem; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 0.875rem;">
                                    <i class="bi bi-person"></i>
                                </div>
                                <div>
                                    <div class="text-muted small">Author</div>
                                    <div class="fw-medium">{{ $book->author }}</div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="d-flex align-items-center mb-3">
                                <div class="stat-icon bg-warning text-white me-3" style="width: 2.5rem; height: 2.5rem; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 0.875rem;">
                                    <i class="bi bi-calendar"></i>
                                </div>
                                <div>
                                    <div class="text-muted small">Published Year</div>
                                    <div class="fw-medium">{{ $book->published_year }}</div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="d-flex align-items-center mb-3">
                                <div class="stat-icon bg-info text-white me-3" style="width: 2.5rem; height: 2.5rem; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 0.875rem;">
                                    <i class="bi bi-upc"></i>
                                </div>
                                <div>
                                    <div class="text-muted small">ISBN</div>
                                    <div class="fw-medium font-monospace">{{ $book->isbn }}</div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="d-flex align-items-center mb-3">
                                <div class="stat-icon bg-success text-white me-3" style="width: 2.5rem; height: 2.5rem; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 0.875rem;">
                                    <i class="bi bi-box"></i>
                                </div>
                                <div>
                                    <div class="text-muted small">Total Stock</div>
                                    <div class="fw-medium">{{ $book->stock }} {{ $book->stock == 1 ? 'copy' : 'copies' }}</div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="d-flex align-items-center mb-3">
                                <div class="stat-icon {{ $book->available_stock > 0 ? 'bg-success' : 'bg-danger' }} text-white me-3" style="width: 2.5rem; height: 2.5rem; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 0.875rem;">
                                    <i class="bi bi-check-circle"></i>
                                </div>
                                <div>
                                    <div class="text-muted small">Available Stock</div>
                                    <div class="fw-medium">{{ $book->available_stock }} {{ $book->available_stock == 1 ? 'copy' : 'copies' }}</div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="d-flex align-items-center mb-3">
                                <div class="stat-icon bg-secondary text-white me-3" style="width: 2.5rem; height: 2.5rem; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 0.875rem;">
                                    <i class="bi bi-clock"></i>
                                </div>
                                <div>
                                    <div class="text-muted small">Last Updated</div>
                                    <div class="fw-medium">{{ $book->updated_at->format('M d, Y H:i') }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-4">
                        <i class="bi bi-lightning me-2"></i>Quick Actions
                    </h5>

                    <div class="d-grid gap-3">
                        <a href="{{ route('books.edit', $book) }}" class="btn btn-warning">
                            <i class="bi bi-pencil me-2"></i>Edit Book
                        </a>

                        @if($book->isAvailable())
                            <a href="{{ route('loans.create') }}?book_id={{ $book->id }}" class="btn btn-success">
                                <i class="bi bi-book me-2"></i>Loan This Book
                            </a>
                        @endif

                        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteBookModal">
                            <i class="bi bi-trash me-2"></i>Delete Book
                        </button>

                        <a href="{{ route('books.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-2"></i>Back to Books
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Book Modal -->
    <div class="modal fade" id="deleteBookModal" tabindex="-1" aria-labelledby="deleteBookModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteBookModalLabel">
                        <i class="bi bi-exclamation-triangle text-warning me-2"></i>Confirm Delete
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete this book?</p>
                    <div class="alert alert-warning">
                        <strong>{{ $book->title }}</strong><br>
                        <small>by {{ $book->author }}</small>
                    </div>
                    <p class="text-danger mb-0">
                        <i class="bi bi-exclamation-circle me-1"></i>
                        This action cannot be undone and will permanently remove the book from your library.
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-1"></i>Cancel
                    </button>
                    <form method="POST" action="{{ route('books.destroy', $book) }}" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="bi bi-trash me-1"></i>Delete Book
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
