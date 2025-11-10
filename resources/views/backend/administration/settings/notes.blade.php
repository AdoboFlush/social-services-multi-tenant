@extends("backend.administration.settings._layout")

@section('form')
    <div id="maintenance" class="tab-pane active">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title panel-title">{{ _lang('Notes') }}</h4>
                <form method="post" class="appsvan-submit params-panel" autocomplete="off"
                    action="{{ url('admin/administration/settings/notes') }}" enctype="multipart/form-data">
                    {{ csrf_field() }}
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">{{ _lang('Route') }}</label>
                                <select class="form-control select2" id="notes_selector" name="note_id" required>
                                    @foreach ($notes as $note)
                                        <option value="{{ $note->id }}">{{ $note->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="note-wrapper">
                        @foreach ($notes as $note)
                            <div class="d-none" id="{{ $note->id }}">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group content_wrapper">
                                            <label>{{ _lang('Set notes for English') }}</label>
                                            <textarea class="form-control summernote" id="note_content"
                                                name="content[{{ $note->id }}]">{{ $note->content }}</textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group jp_content_wrapper">
                                            <label>{{ _lang('Set notes for Japanese') }}</label>
                                            <textarea class="form-control summernote" id="note_jp_content"
                                                name="jp_content[{{ $note->id }}]">{{ $note->jp_content }}</textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="row">
                        <div class="form-group">
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-primary"
                                    id="btn-save-note">{{ _lang('Save Settings') }}</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('js-script')
    <script type="text/javascript">
        $(document).ready(function() {
            $('.note-wrapper > div:first-child').addClass("d-block");

            let previous_note_id = $('#notes_selector').find(":selected").val();

            $("#notes_selector").on('focus', function() {
                previous_note_id = this.value;
            }).change(function() {
                $(`.note-wrapper #${previous_note_id}`).removeClass("d-block");

                let note_id = $('#notes_selector').find(":selected").val();
                $(`.note-wrapper #${note_id}`).addClass("d-block");

                previous_note_id = this.value;
            });
        });
    </script>
@endsection
