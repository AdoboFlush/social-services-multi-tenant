<!-- Sidebar Menu -->
<nav class="mt-2">
    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">

        <li class="nav-item">
            <a href="{{ url('admin/dashboard') }}" class="nav-link {{ Request::is('admin/dashboard') || Request::is('dashboard') ? 'active' : ''}}">
                <i class="nav-icon fas fa-tachometer-alt"></i>
                <p>
                    {{ _lang('Dashboard') }}
                </p>
            </a>
        </li>

        @can('voter_view')
        <li class="nav-item">
            <a href="#" class="nav-link {{ Request::is('voters/*') || Request::is('voters')  ? 'active' : ''}}">
                <i class="nav-icon fa fa-users"></i>
                <p>
                    {{ _lang('Voters') }}
                    <i class="right fas fa-angle-left"></i>
                </p>
            </a>
            <ul class="nav nav-treeview">

                <li class="nav-item">
                    <a href="{{ url('voters') }}" class="nav-link">
                        <i class="fa fa-user nav-icon"></i>
                        <p>{{ _lang('All Voters') }}</p>
                    </a>
                </li>

                @if(!in_array(Auth::user()->user_access, ["voter_viewing"]))

                <li class="nav-item">
                    <a href="{{ url('senior_citizen_voters') }}" class="nav-link {{ Request::is('senior_citizen_voters') ? 'active' : ''}}">
                        <i class="nav-icon fa fa-user"></i>
                        <p>
                            {{ _lang('Senior Citizens') }}
                        </p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ url('voters/archived') }}" class="nav-link">
                        <i class="fa fa-archive nav-icon"></i>
                        <p>{{ _lang('Archived/Deleted Voters') }}</p>
                    </a>
                </li>

                @endif

            </ul>
        </li>
        @endcan

        @can('social_service_view')
        <li class="nav-item">
            <a href="{{ url('social_services') }}" class="nav-link {{ Request::is('social_services/*') || Request::is('social_services')  ? 'active' : ''}}">
                <i class="nav-icon fa fa-list"></i>
                <p>
                    {{ _lang('Social Services') }}
                </p>
            </a>
        </li>
        @endcan

        @can('reports_view')
        <li class="nav-item">
            <a href="{{ url('reports/social_services/overview') }}" class="nav-link {{ Request::is('reports/*') ? 'active' : ''}}">
                <i class="nav-icon fa fa-table"></i>
                <p>
                    {{ _lang('Reports') }}
                </p>
            </a>
        </li>
        @endcan

        @can('admin_view')

        <li class="nav-item">
            <a href="#" class="nav-link {{ Request::is('admin/administration/*') ? 'active' : ''}} {{ Request::is('tags') ? 'active' : ''}} {{ Request::is('admin/staffs') ? 'active' : ''}}">
                <i class="nav-icon fa fa-universal-access"></i>
                <p>
                    {{ _lang('Administration') }}
                    <i class="right fas fa-angle-left"></i>
                </p>
            </a>
            <ul class="nav nav-treeview">
                @can('activity_logs_view')
                <li class="nav-item">
                    <a class="nav-link" href="{{ url('admin/administration/activity_log') }}">
                        <i class="fas fa-clipboard-list nav-icon"></i>
                        {{ _lang('Activity Logs') }}
                    </a>
                </li>
                @endcan

                @can('roles_and_permissions_view')
                <li class="nav-item">
                    <a class="nav-link" href="{{ url('admin/administration/roles_permissions') }}">
                        <i class="fas fa-user-shield nav-icon"></i>
                        {{ _lang('Roles and Permissions') }}
                    </a>
                </li>
                @endcan

                @can('welcome_message_view')
                <li class="nav-item">
                    <a class="nav-link" href="{{ url('admin/administration/welcome_message') }}">
                        <i class="fas fa-envelope-open-text nav-icon"></i>
                        {{ _lang('Welcome Message') }}
                    </a>
                </li>
                @endcan

                @can('tag_view')
                <li class="nav-item">
                    <a href="{{ url('tags') }}" class="nav-link">
                        <i class="fas fa-tags nav-icon"></i>
                        <p>
                            {{ _lang('Tagging') }}
                        </p>
                    </a>
                </li>
                @endcan

                @can('staffs_view')
                <li class="nav-item">
                    <a href="{{ url('admin/staffs') }}" class="nav-link">
                        <i class="far fa-user nav-icon"></i>
                        <p>{{ _lang('Staff List') }}</p>
                    </a>
                </li>
                @endcan
            </ul>
        </li>

    @endcan


    <li class="nav-header">SWITCH TENANT CONTEXT</li>

    @php
        // Get the actual main tenant (parent_id == 0) from tenant_details (the current connected tenant)
        $mainTenant = request()->attributes->get('tenant_details');
        
        // Get the currently selected context
        $currentContext = session('current_tenant_context');
    @endphp

    @if($mainTenant)
    <li class="nav-item">
        <a href="{{ route('admin.dashboard', [], false) }}?current_tenant_context_id={{ $mainTenant['tenant_id'] }}" class="nav-link switch-tenant {{ ($currentContext && $currentContext['tenant_id'] == $mainTenant['tenant_id']) ? 'active bg-gray' : '' }}" data-tenant="">
            <i class="nav-icon fa fa-home"></i>
            <p> Main 
                @if($currentContext && $currentContext['tenant_id'] == $mainTenant['tenant_id'])
                <span class="right badge badge-danger">Current</span>
                @endif
            </p>
        </a>
    </li>
    @endif

    @foreach(request()->attributes->get('tenant_children') as $tenant)
    <li class="nav-item">
        <a href="{{ route('admin.dashboard', [], false) }}?current_tenant_context_id={{ $tenant['tenant_id'] }}" class="nav-link switch-tenant {{ ($currentContext && $currentContext['tenant_id'] == $tenant['tenant_id']) ? 'active bg-gray' : '' }}">
            <i class="nav-icon fas fa-building"></i>
            <p>
                {{ _lang($tenant['name']) }}
                @if($currentContext && $currentContext['tenant_id'] == $tenant['tenant_id'])
                <span class="right badge badge-danger">Current</span>
                @endif
            </p>
        </a>
    </li>
    @endforeach

    </ul>
</nav>
