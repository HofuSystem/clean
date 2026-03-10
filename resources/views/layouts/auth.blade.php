<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <!-- CSRF Token -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Clean Station' }}</title>
      <!-- fevicon -->
    <link rel="icon" href="{{ asset('frontend') }}/images/fevicon.png" type="image/gif" />
    <link rel="stylesheet" href="{{ asset('website/client/style.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Toastr CSS -->
    <link rel="stylesheet" href="{{ asset('control/assets/vendor/libs/toastr/toastr.css') }}">
</head>

<body>
    @yield('content')

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="{{ asset('control/assets/vendor/libs/toastr/toastr.js') }}"></script>
    
    <style>
      .invalid-feedback{
        display: block !important; 
      }
      
      /* Custom toast styling for better appearance */
      .toast-top-right {
        top: 20px;
        right: 20px;
      }
      
      .toast-success {
        background-color: #28a745;
        color: white;
      }
      
      .toast-error {
        background-color: #dc3545;
        color: white;
      }
      
      .toast-warning {
        background-color: #ffc107;
        color: #212529;
      }
      
      .toast-info {
        background-color: #17a2b8;
        color: white;
      }
    </style>
    
    <script>
        // Wait for jQuery and toastr to be loaded
        $(document).ready(function() {
            // Configure toastr options
            if (typeof toastr !== 'undefined') {
                toastr.options = {
                    "closeButton": true,
                    "debug": false,
                    "newestOnTop": true,
                    "progressBar": true,
                    "positionClass": "toast-top-right",
                    "preventDuplicates": false,
                    "onclick": null,
                    "showDuration": "300",
                    "hideDuration": "1000",
                    "timeOut": "5000",
                    "extendedTimeOut": "1000",
                    "showEasing": "swing",
                    "hideEasing": "linear",
                    "showMethod": "fadeIn",
                    "hideMethod": "fadeOut"
                };

                // Display success messages
                @if(session('success'))
                    toastr.success("{{ session('success') }}", "Success");
                @endif

                // Display error messages
                @if(session('error'))
                    toastr.error("{{ session('error') }}", "Error");
                @endif

                // Display validation errors
                @if($errors->any())
                    @foreach($errors->all() as $error)
                        toastr.error("{{ $error }}", "Error");
                    @endforeach
                @endif

                // Display warning messages
                @if(session('warning'))
                    toastr.warning("{{ session('warning') }}", "Warning");
                @endif

                // Display info messages
                @if(session('info'))
                    toastr.info("{{ session('info') }}", "Info");
                @endif
            }
        });
    </script>
</body>

</html>