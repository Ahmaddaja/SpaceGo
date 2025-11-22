<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'Laravel') }}</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <link rel="stylesheet" href="{{ asset('adminlte/plugins/fontawesome-free/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('adminlte/dist/css/adminlte.min.css') }}">
    @stack('styles')
</head>
<body class="hold-transition sidebar-mini">
    <div class="wrapper">
        @include('layouts.navbar')
        @include('layouts.sidebar')
        <div class="content-wrapper">
            @hasSection('title-content')
            <section class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1>@yield('title-content')</h1>
                        </div>
                    </div>
                </div>
            </section>
            @endif
            <section class="content">
              @yield('content')  
            </section>
        </div>
        @include('layouts.footer')
    </div>
    @stack('modals')
    <script src="{{ asset('adminlte/plugins/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('adminlte/dist/js/adminlte.min.js') }}"></script>
    @stack('scripts')
    
</body>
</html>