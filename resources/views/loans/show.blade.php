@extends('layouts.app')

@section('title', 'Loan Details')

@section('content')
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1 text-gradient">Loan Details</h1>
            <p class="text-muted mb-0">View and manage loan information</p>
        </div>
        <div>
            <a href="{{ route('loans.edit', $loan->id) }}" class="btn btn-warning">
                <i class="bi bi-pencil me-1"></i>Edit
            </a>
            <a href="{{ route('loans.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i>Back to Loans
            </a>
        </div>
    </div>

    <!-- Loan Information -->
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <h3 class="card-title">Loan #{{ $loan->id }}</h3>
                    <hr>

                    <div class="row">
                        <div class="col-sm-3">
                            <strong>User:</strong>
                        </div>
                        <div class="col-sm-9">
                            {{ $loan->user_name }}<br>
                            <small class="text-muted">{{ $loan->user_email }}</small>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-sm-3">
                            <strong>Book:</strong>
                        </div>
                        <div class="col-sm-9">
                            <strong>{{ $loan->book_title }}</strong><br>
                            <small class="text-muted">by {{ $loan->book_author }}</small>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-sm-3">
                            <strong>Loaned At:</strong>
                        </div>
                        <div class="col-sm-9">
                            {{ \Carbon\Carbon::parse($loan->loaned_at)->format('M d, Y H:i') }}
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-sm-3">
                            <strong>Expected Return:</strong>
                        </div>
                        <div class="col-sm-9">
                            @if($loan->expected_return_at)
                                {{ \Carbon\Carbon::parse($loan->expected_return_at)->format('M d, Y') }}
                                @if(\Carbon\Carbon::parse($loan->expected_return_at)->isPast() && !$loan->returned_at)
                                    <span class="badge bg-danger ms-2">Overdue</span>
                                @endif
                            @else
                                <span class="text-muted">No due date set</span>
                            @endif
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-sm-3">
                            <strong>Returned At:</strong>
                        </div>
                        <div class="col-sm-9">
                            @if($loan->returned_at)
                                {{ \Carbon\Carbon::parse($loan->returned_at)->format('M d, Y H:i') }}
                            @else
                                <span class="text-muted">Not returned yet</span>
                            @endif
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-sm-3">
                            <strong>Status:</strong>
                        </div>
                        <div class="col-sm-9">
                            @if($loan->returned_at)
                                <span class="badge bg-success fs-6">Returned</span>
                            @elseif($loan->expected_return_at && \Carbon\Carbon::parse($loan->expected_return_at)->isPast())
                                <span class="badge bg-danger fs-6">Overdue</span>
                            @else
                                <span class="badge bg-warning fs-6">Active Loan</span>
                            @endif
                        </div>
                    </div>

                    @if($loan->returned_at)
                        <div class="row mt-3">
                            <div class="col-sm-3">
                                <strong>Loan Duration:</strong>
                            </div>
                            <div class="col-sm-9">
                                {{ \Carbon\Carbon::parse($loan->loaned_at)->diffForHumans(\Carbon\Carbon::parse($loan->returned_at), true) }}
                            </div>
                        </div>
                    @else
                        <div class="row mt-3">
                            <div class="col-sm-3">
                                <strong>Days Since Loaned:</strong>
                            </div>
                            <div class="col-sm-9">
                                {{ \Carbon\Carbon::parse($loan->loaned_at)->diffForHumans() }}
                            </div>
                        </div>
                    @endif

                    <div class="row mt-3">
                        <div class="col-sm-3">
                            <strong>Created:</strong>
                        </div>
                        <div class="col-sm-9">
                            {{ \Carbon\Carbon::parse($loan->created_at)->format('M d, Y H:i') }}
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-sm-3">
                            <strong>Last Updated:</strong>
                        </div>
                        <div class="col-sm-9">
                            {{ \Carbon\Carbon::parse($loan->updated_at)->format('M d, Y H:i') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Quick Actions</h5>

                    <div class="d-grid gap-2">
                        <a href="{{ route('loans.edit', $loan->id) }}" class="btn btn-warning">
                            <i class="bi bi-pencil me-1"></i>Edit Loan
                        </a>

                        @if(!$loan->returned_at)
                            <form method="POST" action="{{ route('loans.update', $loan->id) }}">
                                @csrf
                                @method('PUT')
                                <button type="submit" class="btn btn-success w-100"
                                        onclick="return confirm('Are you sure you want to mark this book as returned?')">
                                    <i class="bi bi-check-circle me-1"></i>Mark as Returned
                                </button>
                            </form>
                        @endif

                        <button type="button" class="btn btn-danger w-100"
                                data-bs-toggle="modal" data-bs-target="#deleteLoanModal">
                            <i class="bi bi-trash me-1"></i>Delete Loan
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Loan Modal -->
    <div class="modal fade" id="deleteLoanModal" tabindex="-1" aria-labelledby="deleteLoanModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteLoanModalLabel">
                        <i class="bi bi-exclamation-triangle text-warning me-2"></i>Confirm Delete
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete this loan record?</p>
                    <div class="alert alert-warning">
                        <strong>User:</strong> {{ $loan->user_name }}<br>
                        <strong>Book:</strong> {{ $loan->book_title }} by {{ $loan->book_author }}<br>
                        <strong>Loaned:</strong> {{ \Carbon\Carbon::parse($loan->loaned_at)->format('M d, Y H:i') }}
                    </div>
                    <p class="text-danger mb-0">
                        <i class="bi bi-exclamation-circle me-1"></i>
                        This action cannot be undone and will permanently remove the loan record.
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-1"></i>Cancel
                    </button>
                    <form method="POST" action="{{ route('loans.destroy', $loan->id) }}" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="bi bi-trash me-1"></i>Delete Loan
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
