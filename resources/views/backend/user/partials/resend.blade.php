<a class="text-primary text-underline" id="btn-resend" href="javascript:void(0)">Resend Verification Link</a>

<div id="confirmation-modal" class="modal animated bounceInDown" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Resend Email Verification</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="overflow-y:auto;">
                <p class="mb-3">Are you sure you want to resend an email verification to {{ $user->first_name }} {{ $user->last_name }}?</p>
                <div class="text-right">
                    <button type="button" class="btn btn-primary px-4" id="confirm">{{ _lang('Yes') }}</button>
                    <button type="button" class="btn btn-secondary px-3" data-dismiss="modal">{{ _lang('Back') }}</button>
                </div>
            </div>
        </div>
    </div>
</div>
@section('js-script')
    @parent
    <script type="text/javascript">
        $("#btn-resend").on("click",function(){
            $("#confirmation-modal").modal("show");
        });
        $("#confirmation-modal #confirm").on("click",function(){
            var data = {
                '_token' : $('input[name="_token"]').val(),
                'user_id' : "{{ $user->id }}"
                };
            $.ajax({
                url: "{{ route('users.resend_verification_email') }}",
                method: 'POST',
                data: data,
                beforeSend: function(){
                    $("#preloader").css("display","block");
                },success: function(data){
                    $("#preloader").css("display","none");
                    if(data.status){
                        $("#preloader").css("display","none");
                        toastr.success(data.message)
                    }else{
                        toastr.error(data.message)
                    }
                    $("#confirmation-modal").modal("hide");
                },
                error: function (request, status, error) {
                    $("#preloader").css("display","none");
                    toastr.error(error)
                }
            });
        });
    </script>
@endsection
