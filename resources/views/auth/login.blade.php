@extends('layouts.app')

@section('content')
    <div class="row justify-content-center align-items-center" style="min-height: 82vh;">
        <div class="col-12 col-sm-10 col-md-7 col-lg-5 col-xl-4">

            <div class="text-center mb-4">
                <div class="d-inline-flex align-items-center justify-content-center bg-primary text-white rounded-circle mb-3 shadow-sm"
                    style="width: 56px; height: 56px;">
                    <i class="bi bi-link-45deg fs-2"></i>
                </div>
                <h4 class="fw-bold text-dark mb-1">Sembark URL Shortener</h4>
                <p class="text-muted small mb-0">Sign in to manage and track your links</p>
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    @if (session('status'))
                        <div class="alert alert-success alert-dismissible fade show py-2 px-3 small mb-3" role="alert">
                            <i class="bi bi-check-circle me-1"></i> {{ session('status') }}
                            <button type="button" class="btn-close py-2" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show py-2 px-3 small mb-3" role="alert">
                            <i class="bi bi-exclamation-triangle me-1"></i>
                            @if ($errors->count() == 1)
                                {{ $errors->first() }}
                            @else
                                <ul class="mb-0 ps-3">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            @endif
                            <button type="button" class="btn-close py-2" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('login') }}" novalidate>
                        @csrf

                        <div class="mb-3">
                            <label for="email" class="form-label small fw-semibold text-secondary">Email Address</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted border-end-0">
                                    <i class="bi bi-envelope"></i>
                                </span>
                                <input type="email" id="email" name="email"
                                    class="form-control border-start-0 ps-0 @error('email') is-invalid @enderror"
                                    value="{{ old('email') }}" placeholder="name@example.com" required autofocus
                                    autocomplete="username">
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label for="password"
                                    class="form-label small fw-semibold text-secondary mb-0">Password</label>
                            </div>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted border-end-0">
                                    <i class="bi bi-lock"></i>
                                </span>
                                <input type="password" id="password" name="password"
                                    class="form-control border-start-0 ps-0 @error('password') is-invalid @enderror"
                                    placeholder="••••••••" required autocomplete="current-password">
                            </div>
                        </div>

                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" name="remember" id="remember_me">
                            <label class="form-check-label small text-muted" for="remember_me">
                                Keep me logged in
                            </label>
                        </div>


                        <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold shadow-sm">
                            <i class="bi bi-box-arrow-in-right me-1"></i> Sign In
                        </button>
                    </form>

                </div>

                <!-- Demo Credentials Helper -->
                <div class="card-footer bg-light border-top-0 p-3 rounded-bottom text-center">
                    <p class="small text-muted mb-1 fw-semibold">
                        <i class="bi bi-info-circle me-1"></i> Credentials:
                    </p>
                    <div class="d-flex flex-wrap justify-content-center gap-2 small text-secondary">
                        <span class="badge bg-white text-dark border">superadmin@sembark.com</span>
                    </div>
                    <div class="text-muted small mt-1">Default Password: <code>12345678</code></div>
                </div>
            </div>

        </div>
    </div>
@endsection