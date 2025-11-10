(function($) {
	$(document).on('click','.btn-remove-2',function(){
		var link = $(this).attr('href');
		//Sweet Alert for delete action
		swal({
		  title: "Are you sure?",
		  text: "Once deleted, you will not be able to recover this record !",
		  icon: "warning",
		  buttons: true,
		gerMode: true,
		})
		.then((willDelete) => {
		  if (willDelete) {
			window.location.href = link;
		  } else {
			return false;
		  }
		});
		return false;
	});


	$(document).on('click','.btn-remove',function(){
        var ajax_url = $(this).data('ajax');
        var text = "Once deleted, you will not be able to recover this record !";
        if(ajax_url){
            $.ajax({
                type:"GET",
                url: ajax_url,
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                async: false,
                beforeSend: function(){
                    $("#preloader").css("display","block");
                },success: function(data){
                    if(!data.status){
                        text = text + " " + data.message;
                    }
                    $("#preloader").css("display","none");
                }
            });
        }
        //Sweet Alert for delete action
        swal({
		  title: "Are you sure?",
		  text: text,
		  icon: "warning",
		  buttons: true,
		  dangerMode: true,
		})
		.then((willDelete) => {
		  if (willDelete) {
			$(this).closest('form').submit();
		  } else {
			return false;
		  }
		});
		return false;
	});

	$(".select2").select2();
	$(".datepicker").datepicker();
	$(".telephone").intlTelInput({
		nationalMode: false,
		//separateDialCode: true,
	});

	$(".monthpicker").datepicker( {
		format: "mm/yyyy",
		viewMode: "months",
		minViewMode: "months"
	});

    try {
        $('.dropify').dropify();
        $('.datetimepicker').datetimepicker({
            format:'YYYY-MM-DD HH:mm:00'
        });

        $('.timepicker').datetimepicker({
            format:'HH:mm:00'
        });

        //Mask Plugin
        // $('.year').mask('0000-0000');

        // //Credit Card Mask
        // $('.cc').mask('0000-0000-0000-0000', {placeholder: "____-____-____-____"});
        // $('.cvv').mask('000', {placeholder: "___"});
        // $('.exp-date').mask('00 / 0000', {placeholder: "MM / YYYY"});

    } catch (Ex) {

    }

	//Form validation
	validate();

    /*Summernote editor*/

	if ($("#summernote,.summernote").length) {
		$('#summernote,.summernote').summernote({
			height: 200,
			popover: {
			  image: [],
			  link: [],
			  air: []
		    },
			dialogsInBody: true
		});
	}

if ($(".summernote-simple").length) {
    $('.summernote-simple').summernote({
        height: 200,
        toolbar: [
            // [groupName, [list of button]]
            ['style', ['bold', 'italic', 'underline', 'clear']],
            ['font', ['strikethrough', 'superscript', 'subscript']],
            ['fontsize', ['fontsize']],
            ['color', ['color']],
            ['para', ['ul', 'ol', 'paragraph', 'link']],
        ],
        popover: {
            image: [],
            link: [],
            air: []
        }
    });
}

if ($(".summernote-admin").length) {
    $('.summernote-admin').summernote({
        height: 200,
        toolbar: [
            // [groupName, [list of button]]
            ['style', ['bold', 'italic', 'underline', 'clear']],
            ['font', ['strikethrough', 'superscript', 'subscript']],
            ['fontsize', ['fontsize']],
            ['color', ['color']],
            ['para', ['ul', 'ol', 'paragraph', 'link']],
			['view', ['codeview']]
        ],
        popover: {
            image: [],
            link: [],
            air: []
        }
    });
}

$('.amount-auto-format')
	.focusout(function() {
		var currentVal = $(this).val();
		if(currentVal.length > 1){
			$(this).val(makeDigit(currentVal));
		}
	})
	.focusin(function() {
		var newValue = $(this).val().replace(/,/g, "");
		$(this).val(newValue);
	});

$(".float-field").keypress(function(event) {
	   if ((event.which != 46 || $(this).val().indexOf('.') != -1) &&
			(event.which < 48 || event.which > 57)) { event.preventDefault();
		}
	});

	$(".int-field").keypress(function(event) {
		if ((event.which < 48 || event.which > 57)) { event.preventDefault();
		}
	});

	$("input").on('input',function(e){
		if($(this).attr('name') === 'amount' || $(this).hasClass('absolute')){
            if ((event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 47 || event.which > 57)) {
                event.preventDefault();
            }
            if($(this).val() < 0){
                $(this).val(Math.abs($(this).val()));
            }
		}
	});

	$(document).on('click','#modal-fullscreen',function(){
		$("#main_modal >.modal-dialog").toggleClass("fullscreen-modal");
	});

	$("input:required, select:required, textarea:required").closest(".form-group").find('.control-label').append("<span class='required'> *</span>");

	//Print Command

	$(document).on('click','.print',function(){
		$("#preloader").css("display","block");
		var div = "#"+$(this).data("print");
		$(div).print({
			timeout: 1000,
		});
	});

	//Appsvan File Upload Field

	$(".appsvan-file").after("<input type='text' class='form-control filename' readOnly>"
	+"<button type='button' class='btn btn-info appsvan-upload-btn'>Browse</button>");


	$(".appsvan-file").each(function(){
		if($(this).data("value")){
			$(this).parent().find(".filename").val($(this).data("value"));
		}
		if($(this).attr("required")){
			$(this).parent().find(".filename").prop("required",true);
		}
	});

	$(document).on("click",".appsvan-upload-btn",function(){
		$(this).parent().find("input[type=file]").click();
	});

	$(document).on('change','.appsvan-file',function(){
		readFileURL(this);
	});

	function readFileURL(input) {

		if (input.files && input.files[0]) {

			var reader = new FileReader();

			reader.onload = function (e) {};



			$(input).parent().find(".filename").val(input.files[0].name);

			reader.readAsDataURL(input.files[0]);

		}

	}



	//Ajax Modal Function

	$(document).on("click",".ajax-modal",function(){

		 var link = $(this).data("href");

		 if ( typeof link == 'undefined' ) {
		 	link = $(this).attr("href");
		 }

		 var title = $(this).data("title");

		 var fullscreen = $(this).data("fullscreen");

		 $.ajax({
			 url: link,
			 beforeSend: function(){
				$("#preloader").css("display","block");
			 },success: function(data){
				$("#preloader").css("display","none");
				$('#main_modal .modal-title').html(title);
				$('#main_modal .modal-body').html(data);
				$("#main_modal .alert-success").css("display","none");
				$("#main_modal .alert-danger").css("display","none");
				$('#main_modal').modal('show');

				if(fullscreen ==true){
					$("#main_modal >.modal-dialog").addClass("fullscreen-modal");
				}else{
					$("#main_modal >.modal-dialog").removeClass("fullscreen-modal");
				}

				//init Essention jQuery Library

				$("select.select2").select2({
					dropdownParent: $('#main_modal')
				});

				//$('.year').mask('0000-0000');
				$(".ajax-submit").validate();
				$('.datepicker').datepicker({
					format: 'yyyy-mm-dd',
				}).on('changeDate', function(e){
					$(this).datepicker('hide');
				});

                $('.datepicker').datepicker().on('hide', function(e) {
					$("#main_modal").css("overflow-y","auto");
				});

				$(".telephone").intlTelInput({
					nationalMode: false,
					//separateDialCode: true,
				});



				$(".float-field").keypress(function(event) {

				   if ((event.which != 46 || $(this).val().indexOf('.') != -1) &&

						(event.which < 48 || event.which > 57)) { event.preventDefault();

					}

				});



				$(".int-field").keypress(function(event) {

					if ((event.which < 48 || event.which > 57)) { event.preventDefault();

					}

				});



				//Credit Card Mask

				// $('.cc').mask('0000-0000-0000-0000', {placeholder: "____-____-____-____"});

				// $('.cvv').mask('000', {placeholder: "___"});



				$(".dropify").dropify();

				$(".ajax-modal input:required, .ajax-modal select:required, .ajax-modal textarea:required").closest(".form-group").find('.control-label').append("<span class='required'> *</span>");

			 },

			  error: function (request, status, error) {

				console.log(request.responseText);

			  }

		 });



		 return false;

	 });



	 $("#main_modal").on('show.bs.modal', function () {
         $('#main_modal').css("overflow-y","hidden");
     });

	 $("#main_modal").on('shown.bs.modal', function () {
		setTimeout(function(){
		  $('#main_modal').css("overflow-y","auto");
		}, 1000);
     });


	 //Ajax Modal Submit

	 $(document).on("submit",".ajax-submit",function(){

		 var link = $(this).attr("action");
		 var btn = $(document.activeElement).val();
		 var formData = new FormData(this);

		 formData.append("btn", btn);

		 $.ajax({

			 method:  "POST",

			 url: link,

			 data:  formData,

			 mimeType:"multipart/form-data",

			 contentType: false,

			 cache: false,

			 processData:false,

			 beforeSend: function(){

				$("#preloader").css("display","block");

			 },success: function(data){
                $('#main_modal').scrollTop(0);
				$("#preloader").css("display","none");

				var json = JSON.parse(data);

				if(json['result'] == "success"){
					$("#verification_code").val("");
					if (json["load-verification-modal-email"] != null && json["load-verification-modal-email"] == true) {

						$("#change_email_modal").modal("hide");
						$('#back-btn').attr('data-target','#change_email_modal');

						if (json["data"]["email"] != null) {
							$("#new_email").val(json["data"]["email"]);
						}

						$("#verification_modal .alert-danger").hide();
						$("p#message").text(json['message']);
						$("#verification_modal").modal("show");
					} else if(json["load-verification-modal-password"] != null && json["load-verification-modal-password"] == true) {
						$("#change_password_modal").modal("hide");
						$('#back-btn').attr('data-target','#change_password_modal');

						if (json["data"]["password"] != null) {
							$("#password").val(json["data"]["password"]);
						}

						$("#verification_modal .alert-danger").hide();
						$("p#message").text(json['message']);
						$("#verification_modal").modal("show");
					}

					if (json["two_step_verification"] != null && json["two_step_verification"] == true) {
						$("h4#success-title .alert-danger").hide();
						$("p#message").text(json['message']);
					}

					if (json["verified"] != null && json["verified"] == true) {
						$("#change_password_modal").modal("hide");
						$("#verification_modal").modal("hide");
						$("h4#success-title").text(json['title']);
						$("p#success-message").text(json['message']);
						$("#success_modal").modal("show");
						setTimeout(function(){ window.location = "/logout"; }, 10000);
					}

					$("#main_modal .alert-success").html(json['message']);

					$("#main_modal .alert-success").css("display","block");

					$("#main_modal .alert-danger").css("display","none");


					if(json['action'] == "update"){

						$('#row_'+json['data']['id']).find('td').each (function() {

						   if(typeof $(this).attr("class") != "undefined"){

							   $(this).html(json['data'][$(this).attr("class")]);

						   }

						});

                        if (typeof gblLoadTable === "function") {
                            gblLoadTable(JSON.parse(window.sessionStorage.getItem("current_page")))
                        }

						if (typeof updateModal === "function") {
                            updateModal(json)
                        }

					}else if(json['action'] == "store"){

						$('.ajax-submit')[0].reset();

						//store = true;
						var table = $("table");
						if (json['choose']) {
							table = $('table#' + json['choose']);
						}

						var new_row = table.find('tr:eq(1)').clone();
						$(new_row).attr("id", "row_"+json['data']['id']);


						$(new_row).find('td').each (function() {
						   if($(this).attr("class") == "dataTables_empty"){
							   window.location.reload();
						   }
						   if(typeof $(this).attr("class") != "undefined"){
						   	   var content = json['data'][$(this).attr("class").split(' ')[0]];
						   	   if (content == null) {
						   	   	content = '';
						   	   }
							   $(this).html(content);
						   }

						});



						var url  = window.location.href;

						$(new_row).find('form').attr("action",url+"/"+json['data']['id']);

						$(new_row).find('.dropdown-edit').attr("data-href",url+"/"+json['data']['id']+"/edit");

						$(new_row).find('.dropdown-view').attr("data-href",url+"/"+json['data']['id']);



						table.prepend(new_row);



						//window.setTimeout(function(){window.location.reload()}, 2000);

					}

				}else{

					if(Array.isArray(json['message'])){

						jQuery.each( json['message'], function( i, val ) {

						   $("#main_modal .alert-danger").html("<p>"+val+"</p>");

						});

						$("#main_modal .alert-success").css("display","none");

						$("#main_modal .alert-danger").css("display","block");

					}else{

						$("#main_modal .alert-danger").html("<p>" + json['message'] + "</p>");

						$("#main_modal .alert-secondary").css("display","none");

						$("#main_modal .alert-danger").css("display","block");

					}

					if (json['two_step_verification'] != null && json['two_step_verification'] == true) {
						$('#verification_modal .alert-success').hide();
						$('#verification_modal .alert-danger').html(json['message']).show();
					} else if (json["load_change_email_modal"] != null && json["load_change_email_modal"] == true) {
						$('#change_email_modal .alert-danger').html(json['message']).show();
					} else if (json["load_change_password_modal"] != null && json["load_change_password_modal"] == true) {
						$('#change_password_modal .alert-danger').html(json['message']).show();
					}

				}

			 },

			 error: function (request, status, error) {

				console.log(request.responseText);

			 }

		 });



		 return false;

	 });



	 //Ajax submit with validate

	 $(".appsvan-submit-validate").validate({

		 submitHandler: function(form) {

			 var elem = $(form);

			 $(elem).find("button[type=submit]").prop("disabled",true);

			 var link = $(form).attr("action");

			 $.ajax({

				 method: "POST",

				 url: link,

				 data:  new FormData(form),

				 mimeType:"multipart/form-data",

				 contentType: false,

				 cache: false,

				 processData:false,

				 beforeSend: function(){

				   button_val = $(elem).find("button[type=submit]").text();

				   $(elem).find("button[type=submit]").html('<i class="fas fa-circle-notch fa-spin" aria-hidden="true"></i>');



				 },success: function(data){

					$(elem).find("button[type=submit]").html(button_val);

					$(elem).find("button[type=submit]").attr("disabled",false);

					var json = JSON.parse(data);

					if(json['result'] == "success"){

						Command: toastr["success"](json['message']);

					} else{

						jQuery.each( json['message'], function( i, val ) {

						   Command: toastr["error"](val);

						});

					}

				 }

			 });



			return false;

		},invalidHandler: function(form, validator) {},

		  errorPlacement: function(error, element) {}

	 });



	 //Ajax submit without validate

	 $(document).on("submit",".appsvan-submit",function(){

		 var elem = $(this);

		 $(elem).find("button[type=submit]").prop("disabled",true);

		 var link = $(this).attr("action");

		 $.ajax({

			 method: "POST",

			 url: link,

			 data:  new FormData(this),

			 mimeType:"multipart/form-data",

			 contentType: false,

			 cache: false,

			 processData:false,

			 beforeSend: function(){

			   button_val = $(elem).find("button[type=submit]").text();

			   $(elem).find("button[type=submit]").html('<i class="fas fa-circle-notch fa-spin" aria-hidden="true"></i>');



			 },success: function(data){

				$(elem).find("button[type=submit]").html(button_val);

				$(elem).find("button[type=submit]").attr("disabled",false);

				var json = JSON.parse(data);

				if(json['result'] == "success"){

					Command: toastr["success"](json['message']);

				}else{
					if(typeof json['message'] == "object"){
						jQuery.each( json['message'], function( i, val ) {
							Command: toastr["error"](val);
						});
					} else {
						Command: toastr[json['result']](json['message']);
					}



				}

			 }

		 });



		 return false;

	 });





	$("#main_modal").on("hidden.bs.modal", function () {});


	/*$('.resend_verification_code').on('click', function(){
		alert('onclick is working.');
	});*/

	$('.integer-only').on('keydown onblur', function(event) {
		$(this).val($(this).val().replace(/[^\d].+/, ""));
        if ((event.which < 48 || event.which > 57) && (event.which < 96 || event.which > 105) && event.which != 8 && event.which != 9) {
            event.preventDefault();
        }
	 });

    $('.float-only').on('keydown', function(event) {
        if ((event.which !=190 || $(this).val().indexOf('.') != -1) && (event.which < 47 || event.which > 57) && (event.which < 96 || event.which > 105) && event.which != 8 && event.which != 9) {
            event.preventDefault();
        }
    });

	$("#user_account").change(function(e) {
		e.preventDefault();
		$.ajax({
			type:"POST",
			url: location.origin + "/user/search",
			headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
			data:{
				user_account : this.value
			},
			beforeSend: function(){
				$("#searchAccountSpinner").toggleClass("d-none");
			},success: function(data){
				$('#account')
					.empty()
					.append('<option value="" selected="selected">Select Account</option>')
				;

				$("#searchAccountSpinner").addClass("d-none");
				var json = JSON.parse(JSON.stringify(data));

				if (json['success']) {
					$("#user_account_details").text(json['message']).removeClass("text-danger");
					$.each(json.data, function(i, d) {
						if (d.account_status === 'Closed'){
							$('#account').append('<option value="' + d.id + '" disabled>' + d.currency + '-' + d.opening_balance_in_money_format + ' [Closed]</option>');
						} else {
							$('#account').append('<option value="' + d.id + '">' + d.currency + '-' + d.opening_balance_in_money_format + '</option>');
						}
					});
				} else {
					$("#user_account_details").addClass("text-danger").text(json['message']);
				}
			}
		});
	});

	$('#switch-theme').on("click", function(){
		var theme = $(this).data('theme');

		theme = (theme == 'light') ? 'dark' : 'light';

		 $("#preloader").css("display","block");
		 setCookie('use_theme', theme, 365);
		 location.reload();
	});

    $(".btn-show-hide-password").on("click",function(){
        if($(this).parents('.input-group').find('input').attr("type") == "text"){
            $(this).parents('.input-group').find('input').attr('type', 'password');
            $(this).find("svg.feather.feather-eye").replaceWith(feather.icons['eye-off'].toSvg());
        }else if($(this).parents('.input-group').find('input').attr("type") == "password"){
            $(this).parents('.input-group').find('input').attr('type', 'text');
            $(this).find("svg.feather.feather-eye-off").replaceWith(feather.icons['eye'].toSvg());
        }
    });

    $(".account_number_link").on("click", function(){
        var params = {
            log: "User Account Detail",
            subject_type: "App\\User",
            subject_id: $(this).data("user_id"),
            description: "Viewed"
        };
        logActivity(params, $(this).data("url"));
    });

})(jQuery);

function logViewUser(obj)
{
	var params = {
		log: "User Account Detail",
		subject_type: "App\\User",
		subject_id: $(obj).data("user_id"),
		description: "Viewed"
	};
	logActivity(params);
}

function validate(){

	//Validation Form
	$(".validate").validate({
		submitHandler: function(form) {
			form.submit();
		},invalidHandler: function(form, validator) {},
		  errorPlacement: function(error, element) {}
	});

}

function validateFields(form) {
	var required = true
	function attachValidation(obj) {
		$(obj).on('blur', function(){
			if($(this).val() != '') {
				$(this).removeClass('err')
				$(this).siblings('.err-message').hide()
                var reqlength = $('.group-form').length;
                var value = $('.group-form').filter(function () {
                    return this.value != '';
                });
                if($(obj).hasClass('group-form') && reqlength == value.length){
                    $(obj).parent().siblings('.group-error-container').children('.err-message').hide()
                }
			}
		});

		if ($(obj).prop('required') && $(obj).val() == '') {
            if($(obj).hasClass('group-form')){
                $(obj).parent().siblings('.group-error-container').children('.err-message').show()
            }
			$(obj).addClass('err');
			$(obj).siblings('.err-message').show();
			required = false;
		} else if ($(obj).attr('type') == 'radio' && !$(obj).is(':checked') && $(obj).prop('required')) {
			$(obj).addClass('err');
			$(obj).siblings('.err-message').show();
			required = false;
		} else if ($(obj).attr('type') == 'file' && $(obj).prop('required') && $(obj).attr('accept') == ".csv") {
			var fileExtension = ["csv"];
			if ($.inArray($(obj).val().split('.').pop().toLowerCase(), fileExtension) == -1) {
				$(obj).addClass('err');
				$(obj).siblings('.err-message').show();
				required = false;
			}
		} else if ($(obj).attr('type') == 'email' && $(obj).prop('required') && ($(obj).val() != '')) {

			var testEmail = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test($(obj).val());

			if (!testEmail) {
				$(obj).siblings('.err-message').html('The email address must be valid.').show();
				$(obj).addClass('err');
				$(obj).siblings('.err-message').show();
				required = false;
			}
		} else {
			$(obj).removeClass('err');
			$(obj).siblings('.err-message').hide();
		}
	}

	$(form).find('input[type="text"]').each(function(){
		attachValidation($(this))
	});

	$(form).find('input[type="number"]').each(function(){
		attachValidation($(this))
	});

	$(form).find('textarea').each(function(){
		attachValidation($(this))
	});

	$(form).find('select').each(function(){
		attachValidation($(this))
	});

	$(form).find('input[type="email"]').each(function(){
		attachValidation($(this))
	});

	$(form).find('input[type="password"]').each(function(){
		attachValidation($(this))
	});

	$(form).find('input[type="tel"]').each(function(){
		attachValidation($(this))
	});

	$(form).find('input[type="file"]').each(function(){
		attachValidation($(this))
	});

	if(!$(form).find('input[type="radio"]').is(':checked')) {
		$(form).find('input[type="radio"]').each(function(){
			attachValidation($(this))
		});
	}

	return required;
}

function makeDigit(number) {
	number = parseFloat(number).toFixed(2);

    var parts = number.toString().split(".");
    parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    return parts.join(".");
}

function addZeroes(num) {
    var value = Number(num);
    var res = num.split(".");
    if(res.length == 1 || res[1].length < 3) {
        value = value.toFixed(2);
    }
    return value;
}

function setCookie(cname, cvalue, exdays) {
	var d = new Date();
  	d.setTime(d.getTime() + (exdays*24*60*60*1000));
  	var expires = "expires="+ d.toUTCString();
  	document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
}

function logActivity(data, redirectTo) {
    $.ajax({
        url: "/admin/administration/activity_log",
        async: false,
        method: 'POST',
        data: data,
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        beforeSend: function(){
            $("#preloader").css("display","block");
        },success: function(data){
            $("#preloader").css("display","none");
            if(redirectTo){
                window.location.href = redirectTo;
            }
            return true;
        },
        error: function () {
            $("#preloader").css("display","none");
            return false;
        }
    });
}

function getUrlVars()
{
    var vars = [], hash;
    var hashes = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');
    for(var i = 0; i < hashes.length; i++)
    {
        hash = hashes[i].split('=');
        vars.push(hash[0]);
        vars[hash[0]] = hash[1];
    }
    return vars;
}