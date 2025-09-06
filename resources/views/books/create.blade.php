@extends('layouts.app')

@section('header')
    <div class="d-flex align-items-center">
        <div class="me-3">
            <div class="feature-icon bg-primary text-white">
                <i class="bi bi-plus-circle"></i>
            </div>
        </div>
        <div>
            <h1 class="h3 mb-0 text-gradient">Add New Book</h1>
            <p class="text-muted mb-0">Create a new book entry in your library</p>
        </div>
    </div>
@endsection

@section('content')
    <!-- Form Card -->
    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('books.store') }}">
                @csrf

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-4">
                            <label for="title" class="form-label">
                                <i class="bi bi-book me-1"></i>Title <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror"
                                   id="title" name="title" value="{{ old('title') }}"
                                   placeholder="Enter book title..." required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-4">
                            <label for="author" class="form-label">
                                <i class="bi bi-person me-1"></i>Author <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control @error('author') is-invalid @enderror"
                                   id="author" name="author" value="{{ old('author') }}"
                                   placeholder="Enter author name..." required>
                            @error('author')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-4">
                            <label for="published_year" class="form-label">
                                <i class="bi bi-calendar me-1"></i>Published Year <span class="text-danger">*</span>
                            </label>
                            <input type="number" class="form-control @error('published_year') is-invalid @enderror"
                                   id="published_year" name="published_year" value="{{ old('published_year') }}"
                                   min="1000" max="{{ date('Y') }}" placeholder="e.g. 2023" required>
                            @error('published_year')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="mb-4">
                            <label for="isbn" class="form-label">
                                <i class="bi bi-upc me-1"></i>ISBN <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control @error('isbn') is-invalid @enderror"
                                   id="isbn" name="isbn" value="{{ old('isbn') }}"
                                   placeholder="Enter ISBN number..." required>
                            @error('isbn')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="mb-4">
                            <label for="stock" class="form-label">
                                <i class="bi bi-box me-1"></i>Stock <span class="text-danger">*</span>
                            </label>
                            <input type="number" class="form-control @error('stock') is-invalid @enderror"
                                   id="stock" name="stock" value="{{ old('stock') }}"
                                   min="0" placeholder="Enter stock quantity..." required>
                            @error('stock')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-3 mt-4">
                    <a href="{{ route('books.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i>Cancel
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-1"></i>Save Book
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
