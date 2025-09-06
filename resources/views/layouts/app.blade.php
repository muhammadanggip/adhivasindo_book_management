<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Book Management</title>

        <!-- Modern Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

        <!-- Bootstrap CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">

        <!-- Select2 CSS -->
        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
        <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />

        <!-- Modern Custom Styles -->
        <style>
        :root {
            --primary-color: #005aa0;
            --primary-dark: #003d73;
            --secondary-color: #64748b;
            --success-color: #10b981;
            --warning-color: #f59e0b;
            --danger-color: #ef4444;
            --light-bg: #f8fafc;
            --card-bg: #ffffff;
            --border-color: #e2e8f0;
            --text-primary: #1e293b;
            --text-secondary: #64748b;
            --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
            --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
            --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
            --border-radius: 12px;
            --border-radius-lg: 16px;
        }

        * {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }

        body {
            background-color: var(--light-bg);
            color: var(--text-primary);
            line-height: 1.6;
        }

        .navbar {
            background: var(--card-bg) !important;
            border-bottom: 1px solid var(--border-color);
            box-shadow: var(--shadow-sm);
            backdrop-filter: blur(10px);
        }

        .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
            color: var(--primary-color) !important;
        }

        .nav-link {
            font-weight: 500;
            color: var(--text-secondary) !important;
            transition: all 0.2s ease;
            border-radius: 8px;
            margin: 0 4px;
        }

        .nav-link:hover, .nav-link.active {
            color: var(--primary-color) !important;
            background-color: rgba(0, 90, 160, 0.1);
        }

        .card {
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-sm);
            transition: all 0.2s ease;
        }

        .card:hover {
            box-shadow: var(--shadow-md);
            transform: translateY(-1px);
        }

        .btn {
            border-radius: 8px;
            font-weight: 500;
            padding: 0.75rem 1.5rem;
            transition: all 0.2s ease;
            border: none;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
            box-shadow: var(--shadow-sm);
        }

        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: var(--shadow-md);
        }

        .btn-outline-primary {
            color: var(--primary-color);
            background: transparent;
        }

        .btn-outline-primary:hover {
            background: var(--primary-color);
            transform: translateY(-1px);
        }

        /* Bootstrap Primary Override */
        .bg-primary {
            background-color: var(--primary-color) !important;
        }

        .text-primary {
            color: var(--primary-color) !important;
        }

        .border-primary {
            border-color: var(--primary-color) !important;
        }

        .form-control {
            border: 2px solid var(--border-color);
            border-radius: 8px;
            padding: 0.75rem 1rem;
            transition: all 0.2s ease;
            font-weight: 400;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(0, 90, 160, 0.1);
        }

        .form-label {
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 0.5rem;
        }

        .dropdown-menu {
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-lg);
            padding: 0.5rem;
        }

        .dropdown-item {
            border-radius: 6px;
            padding: 0.5rem 0.75rem;
            font-weight: 500;
            transition: all 0.2s ease;
        }

        .dropdown-item:hover {
            background-color: rgba(0, 90, 160, 0.1);
            color: var(--primary-color);
        }

        .stat-card {
            background: linear-gradient(135deg, var(--card-bg), #f8fafc);
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius-lg);
            padding: 1.5rem;
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }

        .stat-number {
            font-size: 2.5rem;
            font-weight: 700;
            line-height: 1;
        }

        .stat-icon {
            width: 3rem;
            height: 3rem;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }

        .feature-card {
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius-lg);
            padding: 2rem;
            background: var(--card-bg);
            transition: all 0.3s ease;
        }

        .feature-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-lg);
            border-color: var(--primary-color);
        }

        .feature-icon {
            width: 4rem;
            height: 4rem;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            margin-bottom: 1rem;
        }

        .text-gradient {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .glass-effect {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        /* Modern Pagination */
        .pagination {
            --bs-pagination-color: var(--primary-color);
            --bs-pagination-bg: var(--card-bg);
            --bs-pagination-border-color: var(--border-color);
            --bs-pagination-hover-color: var(--primary-dark);
            --bs-pagination-hover-bg: rgba(0, 90, 160, 0.1);
            --bs-pagination-hover-border-color: var(--primary-color);
            --bs-pagination-focus-color: var(--primary-dark);
            --bs-pagination-focus-bg: rgba(0, 90, 160, 0.1);
            --bs-pagination-focus-box-shadow: 0 0 0 3px rgba(0, 90, 160, 0.1);
            --bs-pagination-active-color: #fff;
            --bs-pagination-active-bg: var(--primary-color);
            --bs-pagination-active-border-color: var(--primary-color);
            --bs-pagination-disabled-color: var(--text-secondary);
            --bs-pagination-disabled-bg: var(--card-bg);
            --bs-pagination-disabled-border-color: var(--border-color);
        }

        .page-link {
            border-radius: 8px;
            margin: 0 2px;
            border: 1px solid var(--border-color);
            font-weight: 500;
            transition: all 0.2s ease;
        }

        .page-link:hover {
            transform: translateY(-1px);
            box-shadow: var(--shadow-sm);
        }

        /* Modern Table Styling */
        .table {
            --bs-table-bg: transparent;
            --bs-table-striped-bg: rgba(0, 90, 160, 0.02);
            --bs-table-hover-bg: rgba(0, 90, 160, 0.05);
        }

        .table > :not(caption) > * > * {
            border-bottom-width: 1px;
            border-bottom-color: var(--border-color);
        }

        .table-hover > tbody > tr:hover > * {
            background-color: rgba(0, 90, 160, 0.05);
        }

        /* Form Select Styling */
        .form-select {
            border: 2px solid var(--border-color);
            border-radius: 8px;
            padding: 0.75rem 1rem;
            transition: all 0.2s ease;
            font-weight: 400;
        }

        .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(0, 90, 160, 0.1);
        }

        /* Badge Styling */
        .badge {
            font-weight: 500;
            border-radius: 6px;
        }

        /* Input Group Styling */
        .input-group-text {
            background-color: var(--light-bg);
            border: 2px solid var(--border-color);
            border-radius: 8px 0 0 8px;
            color: var(--text-secondary);
        }

        .input-group .form-control {
            border-left: 0;
            border-radius: 0 8px 8px 0;
        }

        .input-group .form-control:focus {
            border-left: 0;
        }

        /* Select2 Styling */
        .select2-container--bootstrap-5 .select2-selection {
            border: 2px solid var(--border-color);
            border-radius: 8px;
            min-height: 48px;
            padding: 0.75rem 1rem;
        }

        .select2-container--bootstrap-5 .select2-selection--single {
            height: 48px;
        }

        .select2-container--bootstrap-5 .select2-selection--single .select2-selection__rendered {
            line-height: auto;
            padding-left: 0;
            padding-right: 0;
        }

        .select2-container--bootstrap-5 .select2-selection--single .select2-selection__arrow {
            height: 46px;
            right: 8px;
        }

        .select2-container--bootstrap-5.select2-container--focus .select2-selection {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(0, 90, 160, 0.1);
        }

        .select2-dropdown {
            border: 1px solid var(--border-color);
            border-radius: 8px;
            box-shadow: var(--shadow-lg);
        }

        .select2-container--bootstrap-5 .select2-results__option--highlighted[aria-selected] {
            background-color: var(--primary-color);
        }

        .select2-search--dropdown .select2-search__field {
            border: 1px solid var(--border-color);
            border-radius: 6px;
            padding: 0.5rem 0.75rem;
        }

        .select2-search--dropdown .select2-search__field:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 2px rgba(0, 90, 160, 0.1);
        }
        </style>

    </head>
    <body class="bg-light">
        <div class="min-vh-100">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @hasSection('header')
                <header class="bg-white shadow-sm border-bottom">
                    <div class="container py-4">
                        @yield('header')
                    </div>
                </header>
            @endif

            <!-- Page Content -->
            <main class="py-4">
                <div class="container">
                    @yield('content')
                </div>
            </main>
        </div>

        <!-- jQuery -->
        <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

        <!-- Bootstrap JS -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

        <!-- Select2 JS -->
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

        <!-- Initialize Select2 -->
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize all select elements with Select2
            $('.form-select').select2({
                theme: 'bootstrap-5',
                width: '100%',
                placeholder: function() {
                    return $(this).find('option:first').text();
                },
                allowClear: true,
                language: {
                    noResults: function() {
                        return "No results found";
                    },
                    searching: function() {
                        return "Searching...";
                    }
                }
            });

            // Handle form validation styling for Select2
            $('.form-select').on('select2:select select2:unselect', function() {
                $(this).removeClass('is-invalid');
            });
        });
        </script>
    </body>
</html>
