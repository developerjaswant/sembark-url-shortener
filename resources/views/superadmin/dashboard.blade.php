@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-12 mb-4">
            <div class="d-flex justify-content-between align-items-center bg-white p-4 rounded shadow-sm">
                <div>
                    <h3 class="fw-bold mb-1">Super Admin Dashboard</h3>
                    <p class="text-muted mb-0">Welcome back, <strong>{{ auth()->user()->name }}</strong></p>
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
                    <strong>Client Invitation Link:</strong>
                    <a href="{{ session('invitation_url') }}" target="_blank"
                        class="alert-link text-break">{{ session('invitation_url') }}</a>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
    @endif

    <div class="card shadow-sm mb-4">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">Clients / Companies</h5>
            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#inviteClientModal">
                <i class="bi bi-plus-lg me-1"></i>Invite Client
            </button>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="clientsTable" class="table table-hover align-middle mb-0 w-100">
                    <thead class="table-light">
                        <tr>
                            <th>Client Name</th>
                            <th>Email</th>
                            <th>Users</th>
                            <th>Total Generated URLs</th>
                            <th>Total URL Clicks</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($companies as $company)
                            <tr>
                                <td class="fw-semibold">{{ $company->name }}</td>
                                <td>{{ $company->email }}</td>
                                <td>{{ $company->users_count }}</td>
                                <td>{{ $company->short_urls_count }}</td>
                                <td>{{ optional($company->shortUrls)->sum('clicks') ?? '0' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-header bg-white py-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
            <h5 class="card-title mb-0">Generated Short URLs (All Companies)</h5>
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
                <a id="exportCsvBtn" href="{{ route('superadmin.export') }}" class="btn btn-outline-success btn-sm">
                    <i class="bi bi-download me-1"></i>Export CSV
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="main_datatable" class="table table-hover align-middle mb-0 w-100">
                    <thead class="table-light">
                        <tr>
                            <th>Short URL</th>
                            <th>Long URL</th>
                            <th>Company</th>
                            <th>Created By</th>
                            <th>Clicks</th>
                            <th>Created On</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>


    <div class="modal fade" id="inviteClientModal" tabindex="-1" aria-labelledby="inviteClientModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="inviteClientModalLabel">Invite New Client</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="inviteClientForm" method="POST" action="{{ route('superadmin.clients.add') }}">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="company_name" class="form-label fw-semibold">Company Name <span
                                    class="text-danger">*</span></label>
                            <input type="text" name="company_name" id="company_name" class="form-control"
                                placeholder="Acme Inc." required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label fw-semibold">Client Admin Email <span
                                    class="text-danger">*</span></label>
                            <input type="email" name="email" id="email" class="form-control" placeholder="admin@acme.com"
                                required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary btn-sm">Create & Generate Invite</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


@endsection

@push('scripts')
    <script>
        $('#inviteClientForm').validate();

        $(document).ready(function () {
            $('#clientsTable').DataTable({
                pageLength: 10
            });

            var main_datatable = $('#main_datatable').DataTable({
                processing: true,
                serverSide: true,
                pageLength: 10,
                ajax: {
                    url: "{{ route('superadmin.dashboard') }}",
                    data: function (d) {
                        d.interval = $('#intervalFilter').val();
                    }
                },
                columns: [
                    { data: 'short_url', name: 'short_code' },
                    { data: 'original_url', name: 'original_url' },
                    { data: 'company', name: 'company', orderable: false, searchable: false },
                    { data: 'user', name: 'user', orderable: false, searchable: false },
                    { data: 'clicks', name: 'clicks', orderable: false, searchable: false },
                    { data: 'created_at', name: 'created_at' }
                ],

            });

            $('#intervalFilter').on('change', function () {
                var interval = $(this).val();
                main_datatable.ajax.reload();
                var exportUrl = "{{ route('superadmin.export') }}";
                if (interval) {
                    exportUrl += "?interval=" + encodeURIComponent(interval);
                }
                $('#exportCsvBtn').attr('href', exportUrl);
            });


        });
    </script>
@endpush