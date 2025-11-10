<option value="" data-message="">{{ _lang('Select Canned Action') }}</option>
@if(isset($templates) )
    @foreach($templates as $template)
        <option value="{{ $template->id }}" data-message="{{ $template->message }}">{{ $template->name }}</option>
    @endforeach
@endif