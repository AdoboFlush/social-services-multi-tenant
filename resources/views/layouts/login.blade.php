<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>{{ env('APP_NAME') }}</title>
  <link rel="shortcut icon" href="{{ asset('images/juan-connect-favicon.png') }}" />
    <link rel="stylesheet" href="{{ asset('adminLTE/plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('adminLTE/plugins/fontawesome-free/css/all.min.css') }}">
    <link href="{{ asset('css/intlTelInput.css') }}" rel="stylesheet">
    <link href="{{ asset('css/bootstrap-datepicker.css') }}" rel="stylesheet">
    <link href="{{ asset('css/passtrength.css') }}" rel="stylesheet">
    <link href="{{ asset('css/toastr.css') }}" rel="stylesheet">

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('adminLTE/plugins/fontawesome-free/css/all.min.css') }}">
    <!-- icheck bootstrap -->
    <link rel="stylesheet" href="{{ asset('adminLTE/plugins/icheck-bootstrap/icheck-bootstrap.min.css') }}">
    <!-- Theme style -->
    <link rel="stylesheet" href="{{ asset('adminLTE/dist/css/adminlte.min.css') }}">

   
</head>

<body class="hold-transition login-page">
    
    @yield('content')

    <!-- jQuery -->
    <script src="{{ asset('adminLTE/plugins/jquery/jquery.min.js') }}"></script>
    <!-- Bootstrap 4 -->
    <script src="{{ asset('adminLTE/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <!-- AdminLTE App -->
    <script src="{{ asset('adminLTE/dist/js/adminlte.min.js') }}"></script>
    <script src="{{ asset('adminLTE/plugins/select2/js/select2.full.min.js') }}"></script>

    <script src="{{ asset('js/intlTelInput-jquery.min.js') }}"></script>
    <script src="{{ asset('js/jquery.validate.min.js') }}"></script>
    <script src="{{ asset('js/select2.min.js') }}"></script>
    <script src="{{ asset('js/bootstrap-datepicker.js') }}"></script>
    <script src="{{ asset('js/dobpicker.js') }}"></script>
    <script src="{{ asset('js/toastr.js') }}"></script>
    <script src="{{ asset('js/assorted.js') }}"></script>

    <script type="text/javascript" src="{{ asset('js/jquery.passtrength.js') }}"></script>

    <script type="text/javascript">

        $(".phone").intlTelInput({
            nationalMode: false,
            //separateDialCode: true,
        });  

        $(".validate").validate({
            submitHandler: function(form) {
                form.submit();
            },invalidHandler: function(form, validator) {},
            errorPlacement: function(error, element) {}
        });    

        $('.select2').select2();

        $('.datepicker').datepicker();

        $.dobPicker({
            yearSelector:'#year',
            minimumAge: 18,
            yearDefault: '{{ _lang("Year") }}'
        });

        $('.datepicker-birthday').datepicker({
            endDate: '-18y'
        });

    </script>
    @yield('js-script')

    @yield('js-script')

    <script type="text/javascript">
    //Show Success Message
    @if(Session::has('success'))
    toastr.success("{{ session('success') }}")
    @endif

    //Show Single Error Message
    @if(Session::has('error'))
    toastr.error("{{ session('error') }}")
    @endif
    </script>

</body>

</html>