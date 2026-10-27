@extends('layouts.app')

@section('content')
<div class="row justify-content-center mt-5">
    <div class="col-md-6 col-lg-5">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3 text-center border-bottom">
                <h4 class="fw-bold mb-1">Accept Invitation</h4>
                <p class="text-muted small mb-0">
                    Join <strong>{{ $invitation->company->name }}</strong> as <strong>{{ $invitation->role }}</strong>
                </p>
            </div>
            <div class="card-body p-4">
                <form id="acceptInvitationForm" method="POST" action="{{ route('invitations.accept', $invitation->token) }}">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label text-muted small fw-semibold">Email Address</label>
                        <input type="text" class="form-control bg-light" value="{{ $invitation->email }}" readonly>
                    </div>

                    <div class="mb-3">
                        <label for="name" class="form-label fw-semibold">Your Full Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="name" class="form-control" placeholder="John Doe" required>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label fw-semibold">Choose Password <span class="text-danger">*</span></label>
                        <input type="password" name="password" id="password" class="form-control" placeholder="Minimum 8 characters" required>
                    </div>

                    <div class="mb-4">
                        <label for="password_confirmation" class="form-label fw-semibold">Confirm Password <span class="text-danger">*</span></label>
                        <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" placeholder="Repeat password" required>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold">
                        Activate Account & Continue
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('#acceptInvitationForm').validate({
        rules: {
            name: {
                required: true,
                minlength: 2
            },
            password: {
                required: true,
                minlength: 8
            },
            password_confirmation: {
                required: true,
                equalTo: '#password'
            }
        },
        messages: {
            name: {
                required: "Please enter your name",
                minlength: "Name must be at least 2 characters"
            },
            password: {
                required: "Please choose a password",
                minlength: "Password must be at least 8 characters long"
            },
            password_confirmation: {
                required: "Please confirm your password",
                equalTo: "Passwords do not match"
            }
        }
    });
});
</script>
@endpush
