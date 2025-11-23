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

@php
$use_theme = request()->cookie("use_theme");
if($use_theme == "dark") {
  $body_class = "dark-mode";
  $navbar_text_class = "navbar-dark";
  $navbar_class = "navbar-dark";
  $sidebar_class = "sidebar-dark-olive";
  $theme_icon = "fa-sun";
} else {
  $body_class = "";
  $navbar_text_class = "navbar-light";
  $navbar_class = "navbar-dark";
  $sidebar_class = "sidebar-dark-olive";
  $theme_icon = "fa-moon";
}
@endphp

<body class="hold-transition sidebar-mini {{$body_class}}">

<div id="main_modal" class="modal animated bounceInDown" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header bg-olive">
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
  <div class="lds-ring"><div></div><div></div><div></div><div></div></div>
</div>

<div class="wrapper">

  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand {{$navbar_class}} {{$navbar_text_class}}">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
	  </li>
    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
      <li class="nav-item">
        <a class="nav-link" href="{{ url('/switch-theme') }}" role="button">
          <i class="fa {{$theme_icon}}"></i>
        </a>
      </li>
      <!-- Notifications Dropdown Menu -->
      @php
        //$notifCount = count(Auth::user()->unreadNotifications);
		    $notifCount = 0;
      @endphp
      <li class="nav-item dropdown">
        <a class="nav-link" data-toggle="dropdown" href="#">
          <i class="far fa-bell"></i>
          @if($notifCount > 0)
            <span class="badge badge-danger navbar-badge">{{ $notifCount }}</span>
          @endif
        </a>
		
        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
          <span class="dropdown-header">{{ $notifCount }} Notifications</span>
		  {{--
          @php
          $notifCounter = 0;
          $showNotifLimit = 5;
          @endphp
          @foreach(Auth::user()->unreadNotifications as $notification)
            @php
              $title = $notification->data['title'];
              $message = $notification->data['message'];
              $notifCounter++;
            @endphp
            <div class="dropdown-divider"></div>
            <a href="#" class="dropdown-item">
              <i class="fas fa-envelope mr-2"></i> {{ strlen($message) > 36 ? substr($message, 0, 36).' ...' : $message }}
              <span class="float-right text-muted text-sm">{{ \Carbon\Carbon::createFromTimeStamp(strtotime($notification->created_at))->diffForHumans() }}</span>
            </a>
            @if($notifCounter == $showNotifLimit)
              @break;
            @endif
          @endforeach
          <div class="dropdown-divider"></div>
          @if($notifCount > 0)
          <a href="{{url('notifications')}}" class="dropdown-item dropdown-footer">See All Notifications</a>
          @endif
		  --}}
        </div> 
		
      </li>
      <li class="nav-item">
        <a class="nav-link" data-widget="fullscreen" href="#" role="button">
          <i class="fas fa-expand-arrows-alt"></i>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" data-widget="control-sidebar" data-slide="true" href="#" role="button">
          <i class="fa fa-cog"></i>
        </a>
      </li>
    </ul>
  </nav>
  <!-- /.navbar -->

  <!-- Main Sidebar Container -->
  <aside class="main-sidebar {{$sidebar_class}} elevation-4">

	<!-- Brand Logo -->
	<a href="#" class="brand-link">
    <span class="brand-text font-weight-dark">
      <img src="{{ asset('images/juan-connect-logo.png') }}" alt="JC Logo" style="height:auto; width: 170px;">
    </span>
	</a>
	
	<!-- Sidebar -->
	<div class="sidebar">	
	<!-- SidebarSearch Form -->
	<div class="form-inline mt-2">
		
	</div>
		@php
			$user_type = "user";
      if (Auth::user()->user_type != 'user') {
        $user_type = "admin";
        $tenant = request()->attributes->get('tenant_details');
        if($tenant && isset($tenant['role'])) {
            if($tenant['role'] == \App\User::T_USER_ROLE_LANDLORD) {
                $user_type = "landlord";
            }
        }
      }

      $profile_picture = !empty(Auth::user()->profile_picture) ? asset('uploads/profile/'.Auth::user()->profile_picture) : asset('images/juan-connect-favicon.png');
		@endphp
		<!--Include Menu-->
		@include('layouts.menus.'.$user_type)
  </aside>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    @yield('content-header')
    <section class="content">
      @yield('content')
    </section>
  </div>
  <!-- /.content-wrapper -->

  <!-- Control Sidebar -->
  <aside class="control-sidebar control-sidebar-dark">
    <!-- Control sidebar content goes here -->
    <div class="p-3">
      <div class="text-center">
        <img class="profile-user-img img-fluid img-circle"
             src="{{ $profile_picture }}"
             alt="User profile picture">
      </div>
      <div class="text-center mt-2">
        <h5>{{ Auth::user()->first_name.' '.Auth::user()->last_name }}</h5>
        <p>{{ !empty(Auth::user()->user_access) ? ucwords(Auth::user()->user_access) : ucwords($user_type) }}</p>
      </div>
      <div class="mt-2">
        <ul class="list-group list-group-unbordered text-center mb-3">
          <li class="list-group-item p-1">
            <b>Email</b> <br><span>{{ Auth::user()->email }}</span>
          </li>
          <li class="list-group-item p-1">
            <b>Phone</b> <br><span>{{ Auth::user()->phone }}</span>
          </li>
          <li class="list-group-item p-1">
            <b>Gender</b> <br><span>Male</span>
          </li>
        </ul>

        <a href="{{url('profile/edit')}}" class="btn btn-primary btn-block"><b>Update Profile</b></a>
        <a href="#" data-toggle="modal" data-target="#change_password_modal" class="btn btn-primary btn-block"><b>Change Password</b></a>
        <a href="{{ url('logout') }}" class="btn btn-danger btn-block"><b>Logout</b></a>

      </div>
    </div>
  </aside>
  <!-- /.control-sidebar -->

  <!-- Main Footer -->
  <footer class="main-footer">
    <!-- To the right -->
    <div class="float-right d-none d-sm-inline">
      Version 1.0
    </div>
    <!-- Default to the left -->
    <strong>Copyright &copy; {{ date('Y') }} <a href="#">{{ env('APP_NAME') }}</a>.</strong> All rights reserved.
  </footer>
</div>
<!-- ./wrapper -->

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
{{-- <script src="{{asset('adminLTE/plugins/datatables-responsive/js/dataTables.responsive.min.js')}}"></script>
<script src="{{asset('adminLTE/plugins/datatables-responsive/js/responsive.bootstrap4.min.js')}}"></script> --}}
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

  @if(Session::has('messages'))
    $("#main_modal").find(".modal-body").html("");
    $("#main_modal").modal("show");
    $("#main_modal").find(".modal-title").html("Message");
    @if(Session::has('success'))
      $("#main_modal").find(".alert-success").html("{{ session('success') }}").show();
    @endif
    $("#main_modal").find(".modal-body").append("<ul>");
    @foreach(session('messages') as $message)
      $("#main_modal").find(".modal-body").append("<li>" + "{{$message}}" + "</li>");
    @endforeach
    $("#main_modal").find(".modal-body").append("</ul>");
  @endif

  @php $i = 0; @endphp

  @foreach ($errors->all() as $error)
          @if($i == 0)
          toastr.error("{{ $error }}")
          @endif

    var name= "{{ isset($errors->keys()[$i]) ? $errors->keys()[$i] : "" }}";
    $("input[name='"+name+"']").addClass('error');
    $("select[name='"+name+"'] + span").addClass('error');
      if($("input[name='"+name+"'], select[name='"+name+"']").parent().hasClass('input-group')){
          $("input[name='"+name+"'], select[name='"+name+"']").parent().parent().append("<span class='v-error'>{{$error}}</span>");
      } else {
          $("input[name='"+name+"'], select[name='"+name+"']").parent().append("<span class='v-error'>{{$error}}</span>");
      }
    @php $i++; @endphp

  @endforeach

  // Re-initialize select2 when main_modal is closed
  $('#main_modal').on('hidden.bs.modal', function () {
    $(document).find('.select2').each(function() {
      // Destroy if already initialized
      if ($(this).hasClass('select2-hidden-accessible')) {
        $(this).select2('destroy');
      }
      // Re-initialize
      $(this).select2({
        width: '100%'
      });
    });
  });

  function exportCSV(ajax_url, filename = "report.csv") {
      $.ajax({
          type: "get",
          url: ajax_url,
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          async: true,
          beforeSend: function () {
              $("#preloader").css("display", "block");
          },
          success: function (data) {
              $("#preloader").css("display", "none");
              let BOM = new Uint8Array([0xEF, 0xBB, 0xBF]);
              let blob = new Blob([BOM, data], {
                  encoding: 'UTF-8',
                  type: 'application/vnd.ms-excel; charset=UTF-8'
              });
              let url = window.URL || window.webkitURL;
              link = url.createObjectURL(blob);
              let anchor = $("<a />");
              anchor.attr("download", filename);
              anchor.attr("href", link);
              anchor[0].click();
          },
          error: function (request, status, error) {
              $("#preloader").css("display", "none");
          }
      });
  }

 </script>

</body>
</html>
