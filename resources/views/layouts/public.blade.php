<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>{{ env('APP_NAME') }} | Information System</title>
  <link rel="shortcut icon" href="{{ asset('images/juan-connect-favicon.png') }}" />

  <link href="{{ asset('css/intlTelInput.css') }}" rel="stylesheet">
  <link href="{{ asset('css/bootstrap-datepicker.css') }}" rel="stylesheet">
  <link href="{{ asset('css/bootstrap-datetimepicker.min.css') }}" rel="stylesheet">
  <link href="{{ asset('css/passtrength.css') }}" rel="stylesheet">
  <link href="{{ asset('css/toastr.css') }}" rel="stylesheet">
  <link href="{{ asset('css/dropify.min.css') }}" rel="stylesheet">

  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <link rel="stylesheet" href="{{ asset('adminLTE/plugins/select2/css/select2.min.css') }}">
  <link rel="stylesheet" href="{{ asset('adminLTE/plugins/fontawesome-free/css/all.min.css')}}">
  <link rel="stylesheet" href="{{ asset('adminLTE/plugins/daterangepicker/daterangepicker.css')}}">
  <link rel="stylesheet" href="{{ asset('adminLTE/plugins/icheck-bootstrap/icheck-bootstrap.min.css')}}">
  <link rel="stylesheet" href="{{ asset('adminLTE/plugins/bootstrap-colorpicker/css/bootstrap-colorpicker.min.css')}}">
  <link rel="stylesheet" href="{{ asset('adminLTE/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css')}}">
  <link rel="stylesheet" href="{{ asset('adminLTE/plugins/bootstrap4-duallistbox/bootstrap-duallistbox.min.css')}}">
  <link rel="stylesheet" href="{{ asset('adminLTE/plugins/bs-stepper/css/bs-stepper.min.css')}}">
  <link rel="stylesheet" href="{{ asset('adminLTE/plugins/dropzone/min/dropzone.min.css')}}">
  <link rel="stylesheet" href="{{ asset('adminLTE/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css')}}">
  <link rel="stylesheet" href="{{ asset('adminLTE/plugins/datatables-responsive/css/responsive.bootstrap4.min.css')}}">
  <link rel="stylesheet" href="{{ asset('adminLTE/plugins/datatables-buttons/css/buttons.bootstrap4.min.css')}}">
  <link rel="stylesheet" href="{{ asset('adminLTE/plugins/summernote/summernote-bs4.min.css')}}">
  <link rel="stylesheet" href="{{ asset('adminLTE/dist/css/adminlte.min.css')}}">
  <link rel="stylesheet" href="{{ asset('adminLTE/dist/css/override.css')}}">

  @yield('custom-css')

</head>

<body class="hold-transition layout-top-nav">

  <div id="main_modal" class="modal animated bounceInDown" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title"></h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="alert alert-danger" style="display:none; margin: 15px;"></div>
        <div class="alert alert-success" style="display:none; margin: 15px;"></div>
        <div class="modal-body" style="overflow-y:auto;"></div>

      </div>
    </div>
  </div>

  <div id="change_password_modal" class="modal fade" role="dialog">
    <form method="post" class="ajax-submit" autocomplete="off" action="{{url('profile/update_password')}}" enctype="multipart/form-data">
      {{ csrf_field() }}
      <div class="modal-dialog modal-dialog-sm">
        <!-- Modal content-->
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">{{ _lang('Change Password') }}</h5>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
          </div>
          <div class="modal-body">
            <div class="col-md-12">

              <div class="alert alert-danger" role="alert" style="display: none;"></div>

              <div class="form-group">
                <label class="control-label">{{ _lang('Old Password') }}</label>
                <div class="input-group show-hide-password">
                  <input type="password" class="form-control" name="oldpassword" required>
                  <div class="input-group-append">
                    <div class="input-group-text cursor-pointer btn-show-hide-password">
                      <i class="fa fa-key"></i>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-12">
              <div class="form-group">
                <label class="control-label">{{ _lang('New Password') }}</label>
                <div class="input-group show-hide-password">
                  <input type="password" class="form-control" name="password" required>
                  <div class="input-group-append">
                    <div class="input-group-text cursor-pointer btn-show-hide-password">
                      <i class="fa fa-key"></i>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-12">
              <div class="form-group">
                <label class="control-label">{{ _lang('Confirm Password') }}</label>
                <div class="input-group show-hide-password">
                  <input type="password" class="form-control" id="password-confirm" name="password_confirmation" required>
                  <div class="input-group-append">
                    <div class="input-group-text cursor-pointer btn-show-hide-password">
                      <i class="fa fa-key"></i>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="col-md-12">
              <div class="form-group text-right">
                <button type="submit" class="btn btn-primary">{{ _lang('Continue') }}</button>
                <button type="reset" class="btn btn-danger" data-dismiss="modal">{{ _lang('Close') }}</button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </form>
  </div>

  <div id="success_modal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-dialog-sm">
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <h4 id="success-title"></h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">
          <div class="col-md-12">
            <div class="form-group">
              <p id="success-message"></p>
            </div>
          </div>
          <div class="col-md-12">
            <div class="form-group text-right">
              <button type="reset" class="btn btn-primary" data-dismiss="modal">{{ _lang('OK') }}</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div id="preloader">
    <div class="lds-ring">
      <div></div>
      <div></div>
      <div></div>
      <div></div>
    </div>
  </div>

  <div class="wrapper">
    <nav class="main-header navbar navbar-expand navbar-dark">
      <div class="container">
        <a href="/profile" class="navbar-brand">
          <span class="brand-text font-weight-dark">
            <img src="{{ asset('images/juan-connect-logo.png') }}" alt="JC Logo" style="height:auto; width: 170px;">
          </span>
        </a>
        <ul class="navbar-nav ml-auto">
          <li class="nav-item dropdown">
            <a class="nav-link" data-toggle="dropdown" href="#">
              <span class="navbar-toggler-icon"></span>
            </a>
            <div class="dropdown-menu dropdown-menu-sm dropdown-menu-right">
              <a href="{{route('guest.profile')}}" class="dropdown-item">
                <i class="fa fa-user mr-2"></i> Profile
              </a>
              <div class="dropdown-divider"></div>
              <a href="/logout" class="dropdown-item">
                <i class="fas fa-sign-out-alt mr-2"></i>Logout
              </a>
            </div>
          </li>
        </ul>
      </div>
    </nav>

    <div class="content-wrapper">
      <section class="content">
        @yield('content')
      </section>
    </div>

  </div>

  @yield('content-modal')

  <!-- REQUIRED SCRIPTS -->

  <script src="{{asset('adminLTE/plugins/jquery/jquery.min.js')}}"></script>
  <script src="{{asset('adminLTE/plugins/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
  <script src="{{asset('adminLTE/plugins/select2/js/select2.full.min.js')}}"></script>

  <script src="{{asset('adminLTE/plugins/bootstrap4-duallistbox/jquery.bootstrap-duallistbox.min.js')}}"></script>
  <script src="{{asset('adminLTE/plugins/moment/moment.min.js')}}"></script>
  <script src="{{asset('adminLTE/plugins/inputmask/jquery.inputmask.min.js')}}"></script>
  <script src="{{asset('adminLTE/plugins/daterangepicker/daterangepicker.js')}}"></script>
  <script src="{{asset('adminLTE/plugins/bootstrap-colorpicker/js/bootstrap-colorpicker.min.js')}}"></script>
  <script src="{{asset('adminLTE/plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js')}}"></script>
  <script src="{{asset('adminLTE/plugins/bootstrap-switch/js/bootstrap-switch.min.js')}}"></script>
  <script src="{{asset('adminLTE/plugins/bs-stepper/js/bs-stepper.min.js')}}"></script>
  <script src="{{asset('adminLTE/plugins/dropzone/min/dropzone.min.js')}}"></script>
  <script src="{{asset('adminLTE/plugins/summernote/summernote-bs4.min.js')}}"></script>
  <script src="{{asset('adminLTE/dist/js/adminlte.min.js')}}"></script>

  <script src="{{asset('adminLTE/plugins/datatables/jquery.dataTables.min.js')}}"></script>
  <script src="{{asset('adminLTE/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js')}}"></script>
  <script src="{{asset('adminLTE/plugins/datatables-buttons/js/dataTables.buttons.min.js')}}"></script>
  <script src="{{asset('adminLTE/plugins/datatables-buttons/js/buttons.bootstrap4.min.js')}}"></script>
  <script src="{{asset('adminLTE/plugins/jszip/jszip.min.js')}}"></script>
  <script src="{{asset('adminLTE/plugins/pdfmake/pdfmake.min.js')}}"></script>
  <script src="{{asset('adminLTE/plugins/pdfmake/vfs_fonts.js')}}"></script>
  <script src="{{asset('adminLTE/plugins/datatables-buttons/js/buttons.html5.min.js')}}"></script>
  <script src="{{asset('adminLTE/plugins/datatables-buttons/js/buttons.print.min.js')}}"></script>
  <script src="{{asset('adminLTE/plugins/datatables-buttons/js/buttons.colVis.min.js')}}"></script>

  <script src="{{ asset('js/intlTelInput-jquery.min.js') }}"></script>
  <script src="{{ asset('js/jquery.validate.min.js') }}"></script>
  <script src="{{ asset('js/bootstrap-datepicker.js') }}"></script>
  <script src="{{ asset('js/bootstrap-datetimepicker.min.js') }}"></script>

  <script src="{{ asset('js/dobpicker.js') }}"></script>
  <script src="{{ asset('js/toastr.js') }}"></script>
  <script src="{{ asset('js/assorted.js') }}"></script>
  <script src="{{ asset('js/dropify.min.js') }}"></script>
  <script src="{{ asset('js/app.js') }}"></script>
  <script src="{{ asset('js/sweetalert.min.js') }}"></script>

  <script src="{{asset('js/print.min.js')}}"></script>
  <script src="{{asset('js/html2canvas.js')}}"></script>
  <script src="https://unpkg.com/html5-qrcode"></script>


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