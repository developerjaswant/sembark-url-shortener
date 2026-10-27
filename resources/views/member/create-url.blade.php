@extends('layouts.app')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-7 col-lg-6">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="card-title fw-bold mb-0">Generate Short URL</h5>
                    <a href="{{ route('member.dashboard') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-arrow-left me-1"></i>Back
                    </a>
                </div>
                <div class="card-body p-4">
                    <form id="createShortUrlForm" method="POST" action="{{ route('member.short_urls.store') }}">
                        @csrf

                        <div class="mb-4">
                            <label for="original_url" class="form-label fw-semibold">Long Destination URL <span
                                    class="text-danger">*</span></label>
                            <input type="url" name="original_url" id="original_url" class="form-control"
                                placeholder="https://example.com/very/long/url" value="{{ old('original_url') }}" required>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('member.dashboard') }}" class="btn btn-secondary btn-sm">Cancel</a>
                            <button type="submit" class="btn btn-primary btn-sm">Generate Short URL</button>
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
            $('#createShortUrlForm').validate();
        });
    </script>
@endpush
