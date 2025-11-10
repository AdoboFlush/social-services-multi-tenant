@extends('backend.id_system.layout')

@section('card-header')
<h3 class="card-title">Templates List</h3>
@can('id_template_add')
<span class="float-right"><a href="{{url()->current()."/create"}}" class="btn btn-primary btn-sm" data-title="{{ _lang('Add New') }}">Add New</a></span>
@endcan
@endsection
@section('tab-content')

<div class="row">

    @if(count($templates) < 0)

        <div class="col-md-12">
            <div class="alert alert-warning text-center">
                No templates found. Create a new template by clicking the "Add New" button
            </div>
        </div>

    @else

        @php
            $template_count = 0;
        @endphp
        @foreach($templates as $template)
        @php
            $template_count++;
        @endphp
        <div class="col-md-3">
            <div class="card">
                <div class="card-header">
                        <i class="nav-icon fa fa-id-card"></i> {{ $template->name }} Template
                </div>
                <div class="card-body">
                    @php
                        $template_data = json_decode($template->properties_json, true);    
                        $bg_front = $template_data['front']['bg'];
                        $bg_back = $template_data['back']['bg'];
                    @endphp

                    <div id="template_{{ $template->id }}" class="carousel slide" data-ride="carousel">
                        <div class="carousel-inner">
                            <div class="carousel-item active">
                                <img id="id_front_background" src="{{ !empty($bg_front) ? asset('uploads/templates/'.$bg_front) : asset('images/id_canvass.png') }}" alt="img" style="width:100%;">
                            </div>
                            <div class="carousel-item">
                                <img id="id_back_background" src="{{ !empty($bg_back) ? asset('uploads/templates/'.$bg_back) : asset('images/id_canvass.png') }}" alt="img" style="width:100%;">
                            </div>
                        </div>
                        <a class="carousel-control-prev" href="#template_{{ $template->id }}" role="button" data-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            <span class="sr-only">Previous</span>
                        </a>
                        <a class="carousel-control-next" href="#template_{{ $template->id }}" role="button" data-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            <span class="sr-only">Next</span>
                        </a>
                    </div>

                </div>

                <div class="card-footer">
                    @can('id_template_edit')
                    <a class="btn btn-warning btn-block" href="{{url('id_system/templates/edit/'. $template->id)}}" ><i class="fa fa-edit"></i> Edit Template</a> 
                    {{-- <button class="btn btn-danger btn-block" type="button"><i class="fa fa-trash"></i> Delete Template</button> --}}
                    @endcan
                </div>
            </div>
        </div>

        @endforeach

        @if($template_count <= 0)
            <div class="col-md-12">
                <div class="alert alert-warning text-center">
                    No templates found. Create a new template by clicking the "Add New" button
                </div>
            </div>
        @endif

    @endif

</div>

@endsection