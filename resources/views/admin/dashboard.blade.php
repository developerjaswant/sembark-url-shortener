@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-12 mb-4">
            <div class="d-flex justify-content-between align-items-center bg-white p-4 rounded shadow-sm">
                <div>
                    <h3 class="fw-bold mb-1">Client Admin Dashboard</h3>
                    <p class="text-muted mb-0">
                        Welcome back, <strong>{{ auth()->user()->name }}</strong>
                        @if(auth()->user()->company)
                            — <span class="badge bg-secondary">{{ auth()->user()->company->name }}</span>
                        @endif
                    </p>
                </div>
                <div>
                    <form method="POST" action="{{ route('logout') }}" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-outline-danger btn-sm">
                            <i class="bi bi-box-arrow-right me-1"></i>Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @if(session('invitation_url'))
        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                <div>
                    <i class="bi bi-check-circle me-1"></i>
                    <strong>Member Invitation Link:</strong>
                    <a href="{{ session('invitation_url') }}" target="_blank"
                        class="alert-link text-break">{{ session('invitation_url') }}</a>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
    @endif

    <div class="card shadow-sm mb-4">
        <div class="card-header bg-white py-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
            <h5 class="card-title mb-0">Generated Short URLs</h5>
            <div class="d-flex align-items-center gap-2">
                <div>
                    <select id="intervalFilter" class="form-select form-select-sm">
                        <option value="">All Time</option>
                        <option value="today">Today</option>
                        <option value="last_week">Last Week</option>
                        <option value="this_month">This Month</option>
                        <option value="last_month">Last Month</option>
                    </select>
                </div>
                <a id="exportCsvBtn" href="{{ route('admin.export') }}" class="btn btn-outline-success btn-sm">
                    <i class="bi bi-download me-1"></i>Export CSV
                </a>
                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#generateModal">
                    <i class="bi bi-plus-lg me-1"></i>Generate Short URL
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="main_datatable" class="table table-hover align-middle mb-0 w-100">
                    <thead class="table-light">
                        <tr>
                            <th>Short URL</th>
                            <th>Long URL</th>
                            <th>Clicks</th>
                            <th>Created By</th>
                            <th>Created On</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">Team Members</h5>
            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#inviteMemberModal">
                <i class="bi bi-person-plus me-1"></i>Invite Member
            </button>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="teamMembersTable" class="table table-hover align-middle mb-0 w-100">
                    <thead class="table-light">
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Total Generated URLs</th>
                            <th>Total URL Clicks</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($members as $member)
                            <tr>
                                <td class="fw-semibold">{{ $member->name }}</td>
                                <td>{{ $member->email }}</td>
                                <td>
                                    <span class="badge {{ $member->role === 'Admin' ? 'bg-primary' : 'bg-info' }}">
                                        {{ $member->role }}
                                    </span>
                                </td>
                                <td>{{ $member->short_urls_count ?? 0 }}</td>
                                <td>{{ optional($member->shortUrls)->sum('clicks') ?? '0' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="generateModal" tabindex="-1" aria-labelledby="generateModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="generateModalLabel">Generate Short URL</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="generateShortUrlForm" method="POST" action="{{ route('admin.short_urls.add') }}">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="original_url" class="form-label fw-semibold">Long URL <span
                                    class="text-danger">*</span></label>
                            <input type="url" name="original_url" id="original_url" class="form-control"
                                placeholder="https://example.com/very/long/url" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary btn-sm">Generate</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="inviteMemberModal" tabindex="-1" aria-labelledby="inviteMemberModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="inviteMemberModalLabel">Invite Team Member</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="inviteMemberForm" method="POST" action="{{ route('admin.members.add') }}">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="member_email" class="form-label fw-semibold">Member Email <span
                                    class="text-danger">*</span></label>
                            <input type="email" name="email" id="member_email" class="form-control"
                                placeholder="colleague@example.com" required>
                        </div>
                        <div class="mb-3">
                            <label for="member_role" class="form-label fw-semibold">Role <span
                                    class="text-danger">*</span></label>
                            <select name="role" id="member_role" class="form-select" required>
                                <option value="Member" selected>Member</option>
                                <option value="Admin">Admin</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary btn-sm">Send Invite</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function () {
            var urlsTable = $('#main_datatable').DataTable({
                processing: true,
                serverSide: true,
                pageLength: 10,
                order: [[4, 'desc']],
                ajax: {
                    url: "{{ route('admin.dashboard') }}",
                    data: function (d) {
                        d.interval = $('#intervalFilter').val();
                    }
                },
                columns: [
                    { data: 'short_url', name: 'short_code' },
                    { data: 'original_url', name: 'original_url' },
                    { data: 'clicks', name: 'clicks' },
                    { data: 'user', name: 'user', orderable: false, searchable: false },
                    { data: 'created_at', name: 'created_at' }
                ],
            });

            $('#intervalFilter').on('change', function () {
                var interval = $(this).val();
                urlsTable.ajax.reload();
                var exportUrl = "{{ route('admin.export') }}";
                if (interval) {
                    exportUrl += "?interval=" + encodeURIComponent(interval);
                }
                $('#exportCsvBtn').attr('href', exportUrl);
            });

            $('#teamMembersTable').DataTable({
                pageLength: 10,
            });

            $('#generateShortUrlForm').validate();
            $('#inviteMemberForm').validate();
        });
    </script>
@endpush