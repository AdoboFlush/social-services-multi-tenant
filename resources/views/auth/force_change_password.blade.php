@extends('layouts.login')

@php
    $logo = 'images/logo-dark.png';
    if (isset($_COOKIE['use_theme']) && $_COOKIE['use_theme'] == 'dark') {
        $logo = 'images/logo-light.png';    
    }    
@endphp


@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="login-cols">
                <img class="logo mb-0 p-0 ml-5 mb-3" src="{{ asset($logo) }}" width="100">

                @if($errors->any())
                <div class="alert alert-danger force-password">
                    <ul class="p-0">
                     @foreach ($errors->all() as $error)
                         <li>{{$error}}</li>
                     @endforeach
                    </ul>
                </div>
                @endif
                <p class="mb-4">{{ _lang('To keep your account secured, please update your password.') }}</p>
				<form method="POST" autocomplete="off" class="form-signin" action="{{ url('/require-change-password') }}" id="force_change_password_form">
                    @csrf
                    <input type="hidden" name="two_fa_type" value="force_change_password">
                    <div class="form-group row">
                        <div class="col-md-12">

                            <div class="input-group show-hide-password">
                                <input type="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password" placeholder="{{ _lang('New Password') }}" value="{{ old('password') }}" required>
                                <div class="input-group-append">
                                    <div class="input-group-text text-muted cursor-pointer btn-show-hide-password">
                                        <i data-feather="eye-off"></i>
                                    </div>
                                </div>
                            </div>

                            @if ($errors->has('password'))
                                <span class="invalid-feedback">
                                    <strong>{{ $errors->first('password') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <ul class="list-requirements pl-3 text-white-on-dark">
                              <li id="length">
                                <i data-feather="circle"></i> 
                                <i data-feather="check-circle"></i> 
                                {{ _lang("Password must have at least 8 characters") }}
                              </li>
                              <li id="number">
                                <i data-feather="circle"></i> 
                                <i data-feather="check-circle"></i>  
                               {{ _lang("Password must have at least one number") }}
                              </li>
                              <li id="case">
                                <i data-feather="circle"></i>
                                <i data-feather="check-circle"></i> 
                                {{ _lang("Password must have an upper and lower case characters") }}
                              </li>
                              <li id="special">
                                <i data-feather="circle"></i>
                                <i data-feather="check-circle"></i> 
                                {{ _lang("Password must have at least one special character") }}
                              </li>
                            </ul>
                        </div>
                    </div>

                    <div class="form-group row">
					    <div class="col-md-12">

                            <div class="input-group show-hide-password">
                                <input type="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password_confirmation" placeholder="{{ _lang('Confirm Password') }}" value="{{ old('password_confirmation') }}" required>

                                <div class="input-group-append">
                                    <div class="input-group-text text-muted cursor-pointer btn-show-hide-password">
                                        <i data-feather="eye-off"></i>
                                    </div>
                                </div>
                            </div>

                            @if ($errors->has('password'))
                                <span class="invalid-feedback">
                                    <strong>{{ $errors->first('password') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="form-group row mb-0">
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-primary btn-login btn-block">
                                {{ _lang('Update Password') }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<div id="language-switcher">
    <a class="btn sb-btn-icon sb-btn-transparent-dark dropdown-toggle" id="navbarDropdownLanguages" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <i data-feather="globe"></i>
    </a>
    <div class="dropdown-menu dropdown-menu-right border-0 shadow animated--fade-in-up" aria-labelledby="navbarDropdownLanguages">
        <a class="dropdown-item language-option" href="{{ url('language/english') }}" data-lang="english">{{ _lang('English') }}</a>
        <a class="dropdown-item language-option" href="{{ url('language/japanese') }}" data-lang="japanese">{{ _lang('Japanese') }}</a>
    </div>
</div>

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
@endsection

@section('js-script')
<script>
$(document).ready(function(){
    $('.btn-login').on('click', function(e){
        e.preventDefault();
        if (returnValidate()) {
            $("#preloader").css("display","block");
            $('#force_change_password_form').submit();
        }
    });

   @if(Session::has('generate_two_factor'))
   loadGenerateCode()
   validatePassword($('input[name="password"]'))
   @endif

   $('input[name="password"]').passtrength({
      minChars: 8,
      passwordToggle: false,
      tooltip: true,
      textWeak:"{{ _lang('Weak') }}",
      textMedium:"{{ _lang('Medium') }}",
      textStrong:"{{ _lang('Strong') }}",
      textVeryStrong:"{{ _lang('Very Strong') }}",

    });

   $('input[name="password"]').on('keyup', function(){
        validatePassword($(this))
   })
});

function loadGenerateCode() {
    var formData = new FormData(document.querySelector('form'))
        $.ajax({
            method: "POST",
            url:"{{ url('user/generate_code') }}",
            data:  formData,
            mimeType:"multipart/form-data",
            contentType: false,
            cache: false,
            processData:false,
            beforeSend: function(){
                $("#preloader").css("display","block");
            },success: function(data){
                $("#preloader").css("display","none");
                $("#confirm_modal").modal('hide');
                var link  = $(this).attr('action');
                var title = "{{ _lang('Two Factor Authentication') }}";
                $('#main_modal .modal-title').html(title);
                $('#main_modal .modal-body').html(data);
                $('#main_modal').modal('show');
            },
            error: function (request, status, error) {
                console.log(request.responseText);
            }
        });
}

function validatePassword ($input) {
    checkLengh($input.val(), 8) ? $('#length').addClass('text-success') : $('#length').removeClass('text-success');
    checkIfTextCase($input.val()) ? $('#case').addClass('text-success') : $('#case').removeClass('text-success');
    checkIfDigit($input.val()) ? $('#number').addClass('text-success') : $('#number').removeClass('text-success');
    checkIfSpecialChar($input.val()) ? $('#special').addClass('text-success') : $('#special').removeClass('text-success');
}

function returnValidate() {
    var input = $('input[name="password"]');
    if (checkLengh(input.val(), 8) 
        && checkIfTextCase(input.val())
        && checkIfDigit(input.val())
        && checkIfSpecialChar(input.val())) {
            return true;
        }

    return false;
}

function checkLengh(text, length) {
    return (text.length >= length)
}

function checkIfTextCase(text) {
    if (/[A-Z]/.test(text) && /[a-z]/.test(text)) {
        return true
    }
    return false
}

function checkIfDigit(text) {
    return /[0-9]/.test(text);
}

function checkIfSpecialChar(text) {
    return /[~`!#$%\^@&*+=_\.\(\)\-\[\]\\';,/{}|\\":<>\?]/g.test(text);
}

</script>
@endsection
