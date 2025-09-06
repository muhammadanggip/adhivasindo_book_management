@extends('layouts.app')

@section('header')
    <div class="d-flex align-items-center">
        <div class="me-3">
            <div class="feature-icon bg-primary text-white">
                <i class="bi bi-speedometer2"></i>
            </div>
        </div>
        <div>
            <h1 class="h3 mb-0 text-gradient">Dashboard</h1>
            <p class="text-muted mb-0">Welcome back! Here's what's happening with your library.</p>
        </div>
    </div>
@endsection

@section('content')
    <!-- Welcome Section -->
    <div class="row mb-5">
        <div class="col-12">
            <div class="card glass-effect">
                <div class="card-body text-center py-5">
                    <div class="feature-icon bg-primary text-white mx-auto mb-3">
                        <i class="bi bi-book"></i>
                    </div>
                    <h2 class="h4 mb-3">Welcome to Book Management System</h2>
                    <p class="text-muted mb-0">{{ __("You're logged in!") }} Let's manage your library efficiently.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-5">
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="stat-number text-primary">{{ \App\Models\Book::count() }}</div>
                        <p class="text-muted mb-0 fw-medium">Total Books</p>
                    </div>
                    <div class="stat-icon bg-primary text-white">
                        <i class="bi bi-book"></i>
                    </div>
                </div>
            </div>
        </div>

                                <div class="col-lg-3 col-md-6 mb-4">
                            <div class="stat-card">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="stat-number text-success">{{ \App\Models\Book::where('stock', '>', 0)->get()->filter(fn($book) => $book->available_stock > 0)->count() }}</div>
                                        <p class="text-muted mb-0 fw-medium">Available Books</p>
                                    </div>
                                    <div class="stat-icon bg-success text-white">
                                        <i class="bi bi-check-circle"></i>
                                    </div>
                                </div>
                            </div>
                        </div>

        <div class="col-lg-3 col-md-6 mb-4">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="stat-number text-warning">{{ \DB::table('book_loans')->whereNull('returned_at')->count() }}</div>
                        <p class="text-muted mb-0 fw-medium">Active Loans</p>
                    </div>
                    <div class="stat-icon bg-warning text-white">
                        <i class="bi bi-clock"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-4">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="stat-number text-info">{{ \App\Models\User::count() }}</div>
                        <p class="text-muted mb-0 fw-medium">Total Users</p>
                    </div>
                    <div class="stat-icon bg-info text-white">
                        <i class="bi bi-people"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Feature Cards -->
    <div class="row">
        <div class="col-lg-6 mb-4">
            <div class="feature-card h-100">
                <div class="feature-icon bg-primary text-white">
                    <i class="bi bi-book"></i>
                </div>
                <h4 class="h5 mb-3">Book Management</h4>
                <p class="text-muted mb-4">Manage your book collection, add new books, and update existing ones with our intuitive interface.</p>
                <div class="d-grid gap-2">
                    <a href="{{ route('books.index') }}" class="btn btn-primary">
                        <i class="bi bi-book me-2"></i>View All Books
                    </a>
                    <a href="{{ route('books.create') }}" class="btn btn-outline-primary">
                        <i class="bi bi-plus-circle me-2"></i>Add New Book
                    </a>
                </div>
            </div>
        </div>

        <div class="col-lg-6 mb-4">
            <div class="feature-card h-100">
                <div class="feature-icon bg-success text-white">
                    <i class="bi bi-list-check"></i>
                </div>
                <h4 class="h5 mb-3">Loan Management</h4>
                <p class="text-muted mb-4">Track book loans, manage returns, and monitor borrowing history with detailed analytics.</p>
                <div class="d-grid gap-2">
                    <a href="{{ route('loans.index') }}" class="btn btn-success">
                        <i class="bi bi-list-check me-2"></i>View All Loans
                    </a>
                    <a href="{{ route('loans.create') }}" class="btn btn-outline-success">
                        <i class="bi bi-plus-circle me-2"></i>Create New Loan
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
