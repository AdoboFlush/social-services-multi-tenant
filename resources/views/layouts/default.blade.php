<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>{{ env('APP_NAME') }} | Information System</title>
    <link rel="shortcut icon" href="{{ asset('images/android-chrome-512x512.png') }}" />
    
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <link rel="stylesheet" href="{{ asset('adminLTE/plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('adminLTE/plugins/fontawesome-free/css/all.min.css')}}">
    <link rel="stylesheet" href="{{ asset('adminLTE/plugins/summernote/summernote-bs4.min.css')}}">
    <link rel="stylesheet" href="{{ asset('adminLTE/dist/css/adminlte.min.css')}}">
    <link rel="stylesheet" href="{{ asset('adminLTE/dist/css/override.css')}}">

</head>
<body>
<div id="app">
    <main>
        @yield('content')
    </main>
</div>

@yield('js-script')
</body>
</html>
