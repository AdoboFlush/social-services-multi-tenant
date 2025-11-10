@extends('layouts.app')

@section('content')

<div class="sb-page-header pb-10 sb-page-header-dark bg-gradient-primary-to-secondary">
    <div class="container-fluid">
        <div class="sb-page-header-content py-5">
            <h1 class="sb-page-header-title">
                <div class="sb-page-header-icon"><i data-feather="alert-octagon"></i></div>
                <span>{{ _lang('Available Countries') }}</span>
            </h1>
        </div>
    </div>
</div>

<div class="container-fluid mt-n10">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title"><span class="panel-title">{{ _lang('Available Countries') }}</span>
                    </h4>
                    <table class="table table-striped" id="countryTable">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Code</th>
                                <th class="text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($countries as $country)
                            <tr>
                                <td>{{ $country->name }}</td>
                                <td>{{ $country->code }}</td>
                                <td class="text-center">
                                    <form class="ajax-submit" action="{{ route('risk.management.update.country',$country->id) }}" method="post" enctype="multipart/form-data">
                                        {{ csrf_field() }}
                                        <input onclick="updateCountry(this)" type="checkbox" name="status" @if($country->status == "active") checked @endif value={{ $country->status }}>                                                                
                                    </form>
                                </td>          
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js-script')
    <script type="text/javascript">
        $(function() {
            $('#countryTable').DataTable({
                "order": []
            });
        });

        function updateCountry(country) {
            $(country).parent().submit();
        }
    </script>
@endsection