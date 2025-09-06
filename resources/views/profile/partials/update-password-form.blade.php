<div class="mb-4">
    <h5 class="card-title mb-2">
        <i class="bi bi-shield-lock me-2"></i>Update Password
    </h5>
    <p class="text-muted mb-4">
        Ensure your account is using a long, random password to stay secure.
    </p>
</div>

<form method="post" action="{{ route('password.update') }}">
    @csrf
    @method('put')

    <div class="row">
        <div class="col-md-4">
            <div class="mb-4">
                <label for="update_password_current_password" class="form-label">
                    <i class="bi bi-key me-1"></i>Current Password <span class="text-danger">*</span>
                </label>
                <input type="password"
                       class="form-control @error('current_password', 'updatePassword') is-invalid @enderror"
                       id="update_password_current_password"
                       name="current_password"
                       autocomplete="current-password">
                @error('current_password', 'updatePassword')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
        <div class="col-md-4">
            <div class="mb-4">
                <label for="update_password_password" class="form-label">
                    <i class="bi bi-lock me-1"></i>New Password <span class="text-danger">*</span>
                </label>
                <input type="password"
                       class="form-control @error('password', 'updatePassword') is-invalid @enderror"
                       id="update_password_password"
                       name="password"
                       autocomplete="new-password">
                @error('password', 'updatePassword')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
        <div class="col-md-4">
            <div class="mb-4">
                <label for="update_password_password_confirmation" class="form-label">
                    <i class="bi bi-lock-fill me-1"></i>Confirm Password <span class="text-danger">*</span>
                </label>
                <input type="password"
                       class="form-control @error('password_confirmation', 'updatePassword') is-invalid @enderror"
                       id="update_password_password_confirmation"
                       name="password_confirmation"
                       autocomplete="new-password">
                @error('password_confirmation', 'updatePassword')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-center">
        <div>
            @if (session('status') === 'password-updated')
                <div class="alert alert-success mb-0">
                    <i class="bi bi-check-circle me-2"></i>Password updated successfully!
                </div>
            @endif
        </div>
        <button type="submit" class="btn btn-primary">
            <i class="bi bi-save me-1"></i>Update Password
        </button>
    </div>
</form>
