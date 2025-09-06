<div class="mb-4">
    <h5 class="card-title mb-2">
        <i class="bi bi-person-circle me-2"></i>Profile Information
    </h5>
    <p class="text-muted mb-4">
        Update your account's profile information and email address.
    </p>
</div>

<form id="send-verification" method="post" action="{{ route('verification.send') }}">
    @csrf
</form>

<form method="post" action="{{ route('profile.update') }}">
    @csrf
    @method('patch')

    <div class="row">
        <div class="col-md-6">
            <div class="mb-4">
                <label for="name" class="form-label">
                    <i class="bi bi-person me-1"></i>Name <span class="text-danger">*</span>
                </label>
                <input type="text"
                       class="form-control @error('name') is-invalid @enderror"
                       id="name"
                       name="name"
                       value="{{ old('name', $user->name) }}"
                       required
                       autofocus
                       autocomplete="name">
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
        <div class="col-md-6">
            <div class="mb-4">
                <label for="email" class="form-label">
                    <i class="bi bi-envelope me-1"></i>Email Address <span class="text-danger">*</span>
                </label>
                <input type="email"
                       class="form-control @error('email') is-invalid @enderror"
                       id="email"
                       name="email"
                       value="{{ old('email', $user->email) }}"
                       required
                       autocomplete="username">
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror

                @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                    <div class="alert alert-warning mt-2">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        <strong>Your email address is unverified.</strong>
                        <button form="send-verification" class="btn btn-link p-0 ms-2">
                            Click here to re-send the verification email.
                        </button>
                    </div>

                    @if (session('status') === 'verification-link-sent')
                        <div class="alert alert-success mt-2">
                            <i class="bi bi-check-circle me-2"></i>
                            A new verification link has been sent to your email address.
                        </div>
                    @endif
                @endif
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-center">
        <div>
            @if (session('status') === 'profile-updated')
                <div class="alert alert-success mb-0">
                    <i class="bi bi-check-circle me-2"></i>Profile updated successfully!
                </div>
            @endif
        </div>
        <button type="submit" class="btn btn-primary">
            <i class="bi bi-save me-1"></i>Save Changes
        </button>
    </div>
</form>
