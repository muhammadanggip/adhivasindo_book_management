@extends('layouts.app')

@section('header')
    <div class="d-flex align-items-center">
        <div class="me-3">
            <div class="feature-icon bg-primary text-white">
                <i class="bi bi-book"></i>
            </div>
        </div>
        <div>
            <h1 class="h3 mb-0 text-gradient">Books Management</h1>
            <p class="text-muted mb-0">Manage your book collection and inventory</p>
        </div>
    </div>
@endsection

@section('content')
    <!-- Header Actions -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="d-flex align-items-center">
            <span class="badge bg-primary fs-6 me-2">{{ $books->total() }} Books</span>
            <span class="badge bg-success fs-6">{{ $books->where('available_stock', '>', 0)->count() }} Available</span>
        </div>
        <a href="{{ route('books.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-2"></i>Add New Book
        </a>
    </div>

    <!-- Search and Filter Form -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('books.index') }}" class="row g-3">
                <div class="col-md-4">
                    <label for="search" class="form-label">
                        <i class="bi bi-search me-1"></i>Search by Title
                    </label>
                    <input type="text" class="form-control" id="search" name="search"
                           value="{{ request('search') }}" placeholder="Enter book title...">
                </div>
                <div class="col-md-3">
                    <label for="author" class="form-label">
                        <i class="bi bi-person me-1"></i>Filter by Author
                    </label>
                    <input type="text" class="form-control" id="author" name="author"
                           value="{{ request('author') }}" placeholder="Enter author name...">
                </div>
                <div class="col-md-3">
                    <label for="year" class="form-label">
                        <i class="bi bi-calendar me-1"></i>Filter by Year
                    </label>
                    <input type="number" class="form-control" id="year" name="year"
                           value="{{ request('year') }}" placeholder="Enter year..."
                           min="1000" max="{{ date('Y') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">&nbsp;</label>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-outline-primary">
                            <i class="bi bi-search me-1"></i>Search
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Books Grid -->
    @if($books->count() > 0)
        <div class="row">
            @foreach($books as $book)
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card h-100 feature-card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <h5 class="card-title mb-0">{{ $book->title }}</h5>
                                <div class="text-end">
                                    <span class="badge {{ $book->available_stock > 0 ? 'bg-success' : 'bg-danger' }} fs-6">
                                        {{ $book->available_stock }} available
                                    </span>
                                    <div class="small text-muted mt-1">
                                        Total: {{ $book->stock }} {{ $book->stock == 1 ? 'copy' : 'copies' }}
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="bi bi-person text-muted me-2"></i>
                                    <span class="text-muted">{{ $book->author }}</span>
                                </div>
                                <div class="d-flex align-items-center mb-2">
                                    <i class="bi bi-calendar text-muted me-2"></i>
                                    <span class="text-muted">{{ $book->published_year }}</span>
                                </div>
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-upc text-muted me-2"></i>
                                    <span class="text-muted small">{{ $book->isbn }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer bg-transparent border-0 p-3">
                            <div class="d-grid gap-2">
                                <div class="btn-group" role="group">
                                    <a href="{{ route('books.show', $book) }}" class="btn btn-outline-primary btn-sm">
                                        <i class="bi bi-eye me-1"></i>View
                                    </a>
                                    <a href="{{ route('books.edit', $book) }}" class="btn btn-outline-warning btn-sm">
                                        <i class="bi bi-pencil me-1"></i>Edit
                                    </a>
                                    <button type="button" class="btn btn-outline-danger btn-sm"
                                            data-bs-toggle="modal" data-bs-target="#deleteBookModal{{ $book->id }}">
                                        <i class="bi bi-trash me-1"></i>Delete
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Modern Pagination -->
        <div class="d-flex justify-content-center mt-5">
            <nav aria-label="Books pagination">
                {{ $books->appends(request()->query())->links('pagination::bootstrap-4') }}
            </nav>
        </div>
    @else
        <div class="text-center py-5">
            <div class="feature-icon bg-light text-muted mx-auto mb-4" style="width: 6rem; height: 6rem; border-radius: 20px; display: flex; align-items: center; justify-content: center; font-size: 3rem;">
                <i class="bi bi-book"></i>
            </div>
            <h3 class="h4 mb-3">No books found</h3>
            <p class="text-muted mb-4">Try adjusting your search criteria or add a new book to get started.</p>
            <a href="{{ route('books.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle me-2"></i>Add New Book
            </a>
        </div>
    @endif

    <!-- Delete Book Modals -->
    @foreach($books as $book)
        <div class="modal fade" id="deleteBookModal{{ $book->id }}" tabindex="-1" aria-labelledby="deleteBookModalLabel{{ $book->id }}" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteBookModalLabel{{ $book->id }}">
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
    @endforeach
@endsection
