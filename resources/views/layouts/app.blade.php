<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@stack('title')</title>

    <!-- Custom fonts for this template -->
    <link href="{{ asset('sbadmin2/vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,300,400,600,700,800,900" rel="stylesheet">

    <!-- SB Admin CSS -->
    <link href="{{ asset('sbadmin2/css/sb-admin-2.min.css') }}" rel="stylesheet">

    <!-- DataTables CSS (Bootstrap4 integration) -->
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css" rel="stylesheet">
    <!-- Buttons (Bootstrap4) CSS -->
    <link href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap4.min.css" rel="stylesheet">

    <style>
        /* layout tweaks */
        #wrapper {
            height: 100vh;
            overflow: hidden;
            display: flex;
        }

        .sidebar {
            height: 100vh !important;
            position: relative !important;
            /* Ubah dari sticky ke relative */
            top: 0;
            overflow-y: auto !important;
            overflow-x: visible !important;
            /* PENTING: visible */
            flex-shrink: 0;
            /* Prevent sidebar dari shrinking */
            z-index: 1;
            /* Base z-index untuk sidebar */
        }

        /* Normal sidebar state */
        .sidebar:not(.toggled) {
            width: 14rem !important;
        }

        /* Toggled sidebar state */
        .sidebar.toggled {
            width: 6.5rem !important;
        }

        /* KUNCI: Collapse menu keluar dari sidebar */
        .sidebar.toggled .collapse {
            position: fixed !important;
            /* Fixed, bukan absolute */
            left: calc(6.5rem + 0.5rem) !important;
            /* Tambah jarak 0.5rem dari sidebar */
            z-index: 9999 !important;
            /* Z-index tinggi agar di atas semua */
            min-width: 15rem;
            margin: 0;
        }

        .sidebar.toggled .collapse .collapse-inner {
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15) !important;
            border: 1px solid #e3e6f0;
        }

        /* Hide text when toggled */
        .sidebar.toggled .nav-item .nav-link span {
            display: none;
        }

        .sidebar.toggled .sidebar-brand-text {
            display: none;
        }

        /* Prevent collapse background dari transparent */
        .sidebar.toggled .collapse .bg-white {
            background-color: #fff !important;
        }

        #content-wrapper {
            height: 100vh;
            overflow-y: auto;
            overflow-x: hidden;
            flex: 1;
        }

        /* Prevent body horizontal scroll */
        body {
            overflow-x: hidden;
        }
    </style>

</head>

<body id="page-top">
    <div id="wrapper">
        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
            @include('layouts.sidebar')
        </ul>

        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                @include('layouts.navbar')

                @if (session('success'))
                    <div class="container-fluid">
                        <div class="alert alert-success">{{ session('success') }}</div>
                    </div>
                @endif
                @if (session('error'))
                    <div class="container-fluid">
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    </div>
                @endif

                @yield('content')
            </div>

            @include('layouts.footer')
        </div>
    </div>

    <a class="scroll-to-top rounded" href="#page-top"><i class="fas fa-angle-up"></i></a>

    <!-- Logout Modal -->
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5>Ready to Leave?</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">Select "Logout" below if you are ready to end your current session.</div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                    <form method="POST" action="{{ route('web.logout') }}">@csrf<button class="btn btn-primary"
                            type="submit">Logout</button></form>
                </div>
            </div>
        </div>
    </div>

    <!-- SCRIPTS: jQuery (local from SB Admin) -> Bootstrap -> SB Admin -> plugins -->
    <script src="{{ asset('sbadmin2/vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('sbadmin2/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>

    <script src="{{ asset('sbadmin2/vendor/jquery-easing/jquery.easing.min.js') }}"></script>
    <script src="{{ asset('sbadmin2/js/sb-admin-2.min.js') }}"></script>

    <!-- DataTables (v1.13.x) + Bootstrap4 integration -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>

    <!-- Buttons extension (use Buttons 2.x with bootstrap4 integration) -->
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap4.min.js"></script>

    <!-- pdfMake (for buttons.html5 pdf) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>

    <!-- Buttons HTML5 (pdf/excel) and print -->
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function() {
            // Handle collapse menu positioning when sidebar is toggled
            $('.sidebar').on('show.bs.collapse', '.collapse', function() {
                if ($('.sidebar').hasClass('toggled')) {
                    var $collapse = $(this);
                    var $navItem = $collapse.closest('.nav-item');
                    var navItemOffset = $navItem.offset();

                    // Set position relative to viewport dengan jarak
                    $collapse.css({
                        'top': navItemOffset.top + 'px',
                        'left': 'calc(6.5rem + 0.5rem)' // Tambah jarak 0.5rem
                    });
                }
            });

            // Reset positioning when collapse hidden
            $('.sidebar').on('hidden.bs.collapse', '.collapse', function() {
                $(this).css({
                    'top': '',
                    'left': ''
                });
            });

            // Reset positioning when sidebar is untoggled
            $('#sidebarToggle, .sidebar-brand').on('click', function() {
                setTimeout(function() {
                    if (!$('.sidebar').hasClass('toggled')) {
                        $('.sidebar .collapse').css({
                            'top': '',
                            'left': '',
                            'position': ''
                        });
                    }
                }, 50);
            });

            // Close collapse when clicking outside
            $(document).on('click', function(e) {
                if ($('.sidebar').hasClass('toggled')) {
                    if (!$(e.target).closest('.sidebar').length) {
                        $('.sidebar .collapse').collapse('hide');
                    }
                }
            });

            // Prevent collapse dari closing ketika click inside
            $('.sidebar .collapse').on('click', function(e) {
                e.stopPropagation();
            });
        });
    </script>


    @stack('scripts')

</body>

</html>
