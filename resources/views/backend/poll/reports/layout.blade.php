@extends('backend.poll.layout')

@section('card-header')
    <h3 class="card-title">Reports</h3>
@endsection

@section('tab-content')

<style>
    .highlight-field {
        background-color: yellow;
    }
</style>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <ul class="nav nav-tabs">
                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('poll.reports.candidates_per_brgy') ? 'active' : '' }}" href="{{ route('poll.reports.candidates_per_brgy', [], false) }}"><i class="fa fa-chart-bar mr-2"></i>{{ _lang('Candidates Per Barangay') }}</a></li>
                    
                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('poll.reports.candidates_per_cluster') ? 'active' : '' }}" href="{{ route('poll.reports.candidates_per_cluster', [], false) }}"><i class="fa fa-chart-bar mr-2"></i>{{ _lang('Candidates Per Cluster') }}</a></li>
                    
                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('poll.reports.candidate_comparison_report') ? 'active' : '' }}" href="{{ route('poll.reports.candidate_comparison_report', [], false) }}"><i class="fa fa-balance-scale mr-2"></i>{{ _lang('Candidate Comparison') }}</a></li>
                    
                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('poll.reports.candidate_ranking') ? 'active' : '' }}" href="{{ route('poll.reports.candidate_ranking', [], false) }}"><i class="fa fa-list-ol mr-2"></i>{{ _lang('Candidate Ranking') }}</a></li>
                </ul>
                <div class="card-body">
                    <div class="tab-content">
                        @yield('sub-tab-content')
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection