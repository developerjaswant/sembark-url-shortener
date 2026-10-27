@extends('layouts.app')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-7 col-lg-6">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="card-title fw-bold mb-0">Invite New Client</h5>
                    <a href="{{ route('superadmin.dashboard') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-arrow-left me-1"></i>Back
                    </a>
                </div>
                <div class="card-body p-4">
                    <form id="inviteClientForm" method="POST" action="{{ route('superadmin.clients.add') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="company_name" class="form-label fw-semibold">Company Name <span
                                    class="text-danger">*</span></label>
                            <input type="text" name="company_name" id="company_name" class="form-control"
                                placeholder="Acme Inc." value="{{ old('company_name') }}" required>
                        </div>

                        <div class="mb-3">
                            <label for="user_name" class="form-label fw-semibold">Client Admin Name <span
                                    class="text-danger">*</span></label>
                            <input type="text" name="user_name" id="user_name" class="form-control" placeholder="John Doe"
                                value="{{ old('user_name') }}" required>
                        </div>

                        <div class="mb-4">
                            <label for="email" class="form-label fw-semibold">Client Admin Email <span
                                    class="text-danger">*</span></label>
                            <input type="email" name="email" id="email" class="form-control" placeholder="[EMAIL_ADDRESS]"
                                value="{{ old('email') }}" required>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('superadmin.dashboard') }}" class="btn btn-secondary btn-sm">Cancel</a>
                            <button type="submit" class="btn btn-primary btn-sm">Create & Generate Invite</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function () {
            $('#inviteClientForm').validate();
        });
    </script>
@endpush