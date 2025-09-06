@extends('layouts.app')

@section('header')
    <div class="d-flex align-items-center">
        <div class="me-3">
            <div class="feature-icon bg-warning text-white">
                <i class="bi bi-pencil"></i>
            </div>
        </div>
        <div>
            <h1 class="h3 mb-0 text-gradient">Edit Loan</h1>
            <p class="text-muted mb-0">Update loan information for {{ $loan->user_name }}</p>
        </div>
    </div>
@endsection

@section('content')
    <!-- Loan Information Display -->
    <div class="card mb-4">
        <div class="card-body">
            <h6 class="card-title mb-4">
                <i class="bi bi-info-circle me-2"></i>Loan Information
            </h6>
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-primary text-white me-3" style="width: 2.5rem; height: 2.5rem; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 0.875rem;">
                            <i class="bi bi-person"></i>
                        </div>
                        <div>
                            <div class="text-muted small">User</div>
                            <div class="fw-medium">{{ $loan->user_name }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-info text-white me-3" style="width: 2.5rem; height: 2.5rem; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 0.875rem;">
                            <i class="bi bi-book"></i>
                        </div>
                        <div>
                            <div class="text-muted small">Book</div>
                            <div class="fw-medium">{{ $loan->book_title }}</div>
                            <div class="text-muted small">by {{ $loan->book_author }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-warning text-white me-3" style="width: 2.5rem; height: 2.5rem; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 0.875rem;">
                            <i class="bi bi-calendar-event"></i>
                        </div>
                        <div>
                            <div class="text-muted small">Loaned At</div>
                            <div class="fw-medium">{{ \Carbon\Carbon::parse($loan->loaned_at)->format('M d, Y H:i') }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-info text-white me-3" style="width: 2.5rem; height: 2.5rem; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 0.875rem;">
                            <i class="bi bi-calendar-check"></i>
                        </div>
                        <div>
                            <div class="text-muted small">Expected Return</div>
                            <div class="fw-medium">
                                @if($loan->expected_return_at)
                                    {{ \Carbon\Carbon::parse($loan->expected_return_at)->format('M d, Y') }}
                                    @if(\Carbon\Carbon::parse($loan->expected_return_at)->isPast() && !$loan->returned_at)
                                        <span class="badge bg-danger ms-2">Overdue</span>
                                    @endif
                                @else
                                    <span class="text-muted">No due date</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon {{ $loan->returned_at ? 'bg-success' : 'bg-warning' }} text-white me-3" style="width: 2.5rem; height: 2.5rem; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 0.875rem;">
                            <i class="bi bi-{{ $loan->returned_at ? 'check-circle' : 'clock' }}"></i>
                        </div>
                        <div>
                            <div class="text-muted small">Current Status</div>
                            <div class="fw-medium">
                                @if($loan->returned_at)
                                    <span class="badge bg-success">Returned</span>
                                @else
                                    <span class="badge bg-warning">Active</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Card -->
    <div class="card">
        <div class="card-body">
            @if($loan->returned_at)
                <div class="alert alert-success">
                    <i class="bi bi-check-circle me-2"></i>
                    <strong>This book has already been returned</strong><br>
                    <small>Returned on: {{ \Carbon\Carbon::parse($loan->returned_at)->format('M d, Y H:i') }}</small>
                </div>
                <div class="d-flex justify-content-end gap-3">
                    <a href="{{ route('loans.show', $loan->id) }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i>Back to Details
                    </a>
                </div>
            @else
                <div class="alert alert-warning">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    <strong>This book is currently borrowed</strong><br>
                    <small>Click the button below to mark it as returned</small>
                </div>

                <form method="POST" action="{{ route('loans.update', $loan->id) }}" class="d-inline">
                    @csrf
                    @method('PUT')
                    <div class="d-flex justify-content-end gap-3">
                        <a href="{{ route('loans.show', $loan->id) }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-1"></i>Cancel
                        </a>
                        <button type="submit" class="btn btn-success"
                                onclick="return confirm('Are you sure you want to mark this book as returned?')">
                            <i class="bi bi-check-circle me-1"></i>Mark as Returned
                        </button>
                    </div>
                </form>
            @endif
        </div>
    </div>
@endsection
