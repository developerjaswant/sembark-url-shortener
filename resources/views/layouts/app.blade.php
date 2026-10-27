<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? config('app.name', 'URL Shortener') }}</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <!-- Toastr CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet">

    <!-- DataTables Bootstrap 5 CSS -->
    <link href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css" rel="stylesheet">

    <style>
        :root {
            --bs-primary: #dc2626;
            --bs-primary-rgb: 220, 38, 38;
            --bs-link-color: #dc2626;
            --bs-link-hover-color: #b91c1c;
        }

        body {
            background-color: #f8fafc;
            color: #1e293b;
        }

        /* Red Theme Primary Elements */
        .btn-primary {
            background-color: #dc2626 !important;
            border-color: #dc2626 !important;
            color: #ffffff !important;
        }
        .btn-primary:hover, .btn-primary:focus, .btn-primary:active {
            background-color: #b91c1c !important;
            border-color: #b91c1c !important;
            color: #ffffff !important;
        }

        .btn-outline-primary {
            color: #dc2626 !important;
            border-color: #dc2626 !important;
        }
        .btn-outline-primary:hover, .btn-outline-primary:focus, .btn-outline-primary:active {
            background-color: #dc2626 !important;
            border-color: #dc2626 !important;
            color: #ffffff !important;
        }

        .bg-primary {
            background-color: #dc2626 !important;
        }

        .text-primary {
            color: #dc2626 !important;
        }

        .border-primary {
            border-color: #dc2626 !important;
        }

        .badge.bg-primary {
            background-color: #dc2626 !important;
        }

        a {
            color: #dc2626;
            text-decoration: none;
        }
        a:hover {
            color: #b91c1c;
            text-decoration: underline;
        }

        /* Form Inputs Focus with Red Glow */
        .form-control:focus, .form-select:focus {
            border-color: #f87171 !important;
            box-shadow: 0 0 0 0.25rem rgba(220, 38, 38, 0.2) !important;
        }
        .form-check-input:checked {
            background-color: #dc2626 !important;
            border-color: #dc2626 !important;
        }

        /* DataTables and Pagination in Red */
        .page-item.active .page-link {
            background-color: #dc2626 !important;
            border-color: #dc2626 !important;
            color: #fff !important;
        }
        .page-link {
            color: #dc2626;
        }
        .page-link:hover {
            color: #b91c1c;
        }

        /* Card styles */
        .card {
            border-radius: 10px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }
        .card-header {
            background-color: #fff;
            border-bottom: 1px solid #e2e8f0;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <main class="py-4">
        <div class="container">
            @yield('content')
            {{ $slot ?? '' }}
        </div>
    </main>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <!-- Bootstrap 5 Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Toastr JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>

    <!-- jQuery Validation -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.20.0/jquery.validate.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.20.0/additional-methods.min.js"></script>

    <script>
        // Toastr configuration
        toastr.options = {
            "closeButton": true,
            "progressBar": true,
            "positionClass": "toast-top-right",
            "timeOut": "3500"
        };

        @if(session('success'))
            toastr.success(@json(session('success')));
        @endif

        @if(session('error'))
            toastr.error(@json(session('error')));
        @endif

        @if($errors->any())
            @foreach($errors->all() as $error)
                toastr.error(@json($error));
            @endforeach
        @endif


        // Default Bootstrap 5 styling for jQuery Validation
        if (typeof $.validator !== 'undefined') {
            $.validator.setDefaults({
                errorElement: 'div',
                errorClass: 'invalid-feedback',
                highlight: function (element) {
                    $(element).addClass('is-invalid');
                },
                unhighlight: function (element) {
                    $(element).removeClass('is-invalid');
                },
                errorPlacement: function (error, element) {
                    if (element.parent('.input-group').length) {
                        error.insertAfter(element.parent());
                    } else {
                        error.insertAfter(element);
                    }
                }
            });
        }
    </script>

    @stack('scripts')
</body>
</html>
