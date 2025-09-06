@extends('layouts.app')

@section('header')
    <div class="d-flex align-items-center">
        <div class="me-3">
            <div class="feature-icon bg-success text-white">
                <i class="bi bi-list-check"></i>
            </div>
        </div>
        <div>
            <h1 class="h3 mb-0 text-gradient">Book Loans Management</h1>
            <p class="text-muted mb-0">Track and manage book borrowing activities</p>
        </div>
    </div>
@endsection

@section('content')
    <!-- Header Actions -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="d-flex align-items-center">
            <span class="badge bg-primary fs-6 me-2">{{ $loans->total() }} Total Loans</span>
            <span class="badge bg-warning fs-6 me-2">{{ $loans->where('returned_at', null)->count() }} Active</span>
            <span class="badge bg-success fs-6 me-2">{{ $loans->where('returned_at', '!=', null)->count() }} Returned</span>
        </div>
        <a href="{{ route('loans.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-2"></i>New Loan
        </a>
    </div>

    <!-- Filter Form -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('loans.index') }}" class="row g-3">
                <div class="col-md-4">
                    <label for="user_id" class="form-label">
                        <i class="bi bi-person me-1"></i>Filter by User
                    </label>
                    <select class="form-select" id="user_id" name="user_id">
                        <option value="">All Users</option>
                        @foreach(\App\Models\User::all() as $user)
                            <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}
                                    data-email="{{ $user->email }}">
                                {{ $user->name }} ({{ $user->email }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">&nbsp;</label>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-outline-primary">
                            <i class="bi bi-search me-1"></i>Filter
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Loans Table -->
    @if($loans->count() > 0)
        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="border-0 ps-4">
                                    <i class="bi bi-hash text-muted me-1"></i>ID
                                </th>
                                <th class="border-0">
                                    <i class="bi bi-person text-muted me-1"></i>User
                                </th>
                                <th class="border-0">
                                    <i class="bi bi-book text-muted me-1"></i>Book
                                </th>
                                <th class="border-0">
                                    <i class="bi bi-calendar-event text-muted me-1"></i>Loaned At
                                </th>
                                <th class="border-0">
                                    <i class="bi bi-calendar-check text-muted me-1"></i>Returned At
                                </th>
                                <th class="border-0">
                                    <i class="bi bi-calendar-event text-muted me-1"></i>Expected Return
                                </th>
                                <th class="border-0">
                                    <i class="bi bi-circle-fill text-muted me-1"></i>Status
                                </th>
                                <th class="border-0 pe-4">
                                    <i class="bi bi-gear text-muted me-1"></i>Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($loans as $loan)
                                <tr class="border-bottom">
                                    <td class="ps-4">
                                        <span class="badge bg-light text-dark">#{{ $loan->id }}</span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="stat-icon bg-primary text-white me-3" style="width: 2.5rem; height: 2.5rem; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 0.875rem;">
                                                {{ substr($loan->user_name, 0, 1) }}
                                            </div>
                                            <div>
                                                <div class="fw-medium">{{ $loan->user_name }}</div>
                                                <small class="text-muted">{{ $loan->user_email }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            <div class="fw-medium">{{ $loan->book_title }}</div>
                                            <small class="text-muted">
                                                <i class="bi bi-person me-1"></i>by {{ $loan->book_author }}
                                            </small>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="text-muted">
                                            <i class="bi bi-calendar-event me-1"></i>
                                            {{ \Carbon\Carbon::parse($loan->loaned_at)->format('M d, Y') }}
                                        </div>
                                        <small class="text-muted">
                                            {{ \Carbon\Carbon::parse($loan->loaned_at)->format('H:i') }}
                                        </small>
                                    </td>
                                    <td>
                                        @if($loan->returned_at)
                                            <div class="text-success">
                                                <i class="bi bi-calendar-check me-1"></i>
                                                {{ \Carbon\Carbon::parse($loan->returned_at)->format('M d, Y') }}
                                            </div>
                                            <small class="text-muted">
                                                {{ \Carbon\Carbon::parse($loan->returned_at)->format('H:i') }}
                                            </small>
                                        @else
                                            <span class="text-muted">
                                                <i class="bi bi-dash-circle me-1"></i>Not returned
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($loan->expected_return_at)
                                            <div class="text-info">
                                                <i class="bi bi-calendar-event me-1"></i>
                                                {{ \Carbon\Carbon::parse($loan->expected_return_at)->format('M d, Y') }}
                                            </div>
                                            @if(!$loan->returned_at && \Carbon\Carbon::parse($loan->expected_return_at)->isPast())
                                                <small class="text-danger">
                                                    <i class="bi bi-exclamation-triangle me-1"></i>Overdue
                                                </small>
                                            @endif
                                        @else
                                            <span class="text-muted">
                                                <i class="bi bi-dash-circle me-1"></i>No due date
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($loan->returned_at)
                                            <span class="badge bg-success">
                                                <i class="bi bi-check-circle me-1"></i>Returned
                                            </span>
                                        @elseif($loan->expected_return_at && \Carbon\Carbon::parse($loan->expected_return_at)->isPast())
                                            <span class="badge bg-danger">
                                                <i class="bi bi-exclamation-triangle me-1"></i>Overdue
                                            </span>
                                        @else
                                            <span class="badge bg-warning">
                                                <i class="bi bi-clock me-1"></i>Active
                                            </span>
                                        @endif
                                    </td>
                                    <td class="pe-4">
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('loans.show', $loan->id) }}" class="btn btn-outline-primary btn-sm" title="View Details">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="{{ route('loans.edit', $loan->id) }}" class="btn btn-outline-warning btn-sm" title="Edit Loan">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <button type="button" class="btn btn-outline-danger btn-sm"
                                                    data-bs-toggle="modal" data-bs-target="#deleteLoanModal{{ $loan->id }}" title="Delete Loan">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Modern Pagination -->
        <div class="d-flex justify-content-center mt-4">
            <nav aria-label="Loans pagination">
                {{ $loans->appends(request()->query())->links('pagination::bootstrap-4') }}
            </nav>
        </div>
    @else
        <div class="text-center py-5">
            <div class="feature-icon bg-light text-muted mx-auto mb-4" style="width: 6rem; height: 6rem; border-radius: 20px; display: flex; align-items: center; justify-content: center; font-size: 3rem;">
                <i class="bi bi-list-check"></i>
            </div>
            <h3 class="h4 mb-3">No loans found</h3>
            <p class="text-muted mb-4">No book loans match your criteria. Create a new loan to get started.</p>
            <a href="{{ route('loans.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle me-2"></i>Create New Loan
            </a>
        </div>
    @endif

    <!-- Delete Loan Modals -->
    @foreach($loans as $loan)
        <div class="modal fade" id="deleteLoanModal{{ $loan->id }}" tabindex="-1" aria-labelledby="deleteLoanModalLabel{{ $loan->id }}" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteLoanModalLabel{{ $loan->id }}">
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
    @endforeach
@endsection
