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

        @can('id_system_view')
        <li class="nav-item">
            <a href="{{ url('id_system/members') }}" class="nav-link {{ Request::is('id_system/*') ? 'active' : ''}}">
                <i class="nav-icon fa fa-id-card"></i>
                <p>
                    {{ _lang('ID System') }}
                    <!-- <span class="right badge badge-warning">BETA</span> -->
                </p>
            </a>
        </li>
        @endcan

        <!-- @can('admin_view')
        <li class="nav-item">
            <a href="{{ url('member_codes') }}" class="nav-link {{ Request::is('member_codes') ? 'active' : ''}}">
                <i class="nav-icon fa fa-code"></i>
                <p>
                    {{ _lang('Member Code') }}
                </p>
            </a>
        </li>
        @endcan -->

        @can('events_view')
        <li class="nav-item">
            <a href="{{ url('events') }}" class="nav-link {{ Request::is('events/*') || Request::is('events') ? 'active' : ''}}">
                <i class="nav-icon fa fa-calendar"></i>
                <p>
                    {{ _lang('Events') }}
                </p>
            </a>
        </li>
        @endcan

        @can('assistance_events_view')
        <li class="nav-item">
            <a href="{{ url('voter_assistance/events') }}" class="nav-link {{ Request::is('voter_assistance/events/*') || Request::is('voter_assistance/events') ? 'active' : ''}}">
                <i class="nav-icon fa fa-coins"></i>
                <p>
                    {{ _lang('Assistance Events') }}
                </p>
            </a>
        </li>
        @endcan

        @can('assistance_queue_view')
        <li class="nav-item">
            <a href="{{ route('assistance-queue.index', [], false) }}" class="nav-link {{ Request::is('assistance-queue') ? 'active' : ''}}">
                <i class="nav-icon fas fa-list-ol"></i>
                <p>
                    {{ _lang('Queueing System') }}
                </p>
            </a>
        </li>
        @endcan

        @can('poll_management_view')
        <li class="nav-item">
            <a href="{{ route('poll.elections.overview', [], false) }}" class="nav-link {{ Request::is('poll/*') ? 'active' : ''}}">
                <i class="nav-icon fa fa-poll"></i>
                <p>
                    {{ _lang('Poll Management') }}
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

        {{-- <li class="nav-item">
            <a href="#" class="nav-link {{ Request::is('admin/users/*') ? 'active' : ''}}">
        <i class="nav-icon fa fa-users"></i>
        <p>
            {{ _lang('User Management') }}
            <i class="right fas fa-angle-left"></i>
        </p>
        </a>
        <ul class="nav nav-treeview">

            @can('users_create')
            <li class="nav-item">
                <a class="nav-link" href="{{ url('admin/users/create') }}">
                    <i class="far fa-circle nav-icon"></i>
                    {{ _lang('Add New') }}
                </a>
            </li>
            @endcan

            <li class="nav-item">
                <a class="nav-link" href="{{ url('admin/users') }}">
                    <i class="far fa-circle nav-icon"></i>
                    {{ _lang('Users List') }}
                </a>
            </li>

        </ul>
        </li> --}}

        @can('messages_view')

        {{-- <li class="nav-item">
            <a href="#" class="nav-link {{ Request::is('message/*') ? 'active' : ''}}">
        <i class="nav-icon fa fa-envelope"></i>
        <p>
            {{ _lang('Message') }}
            <i class="right fas fa-angle-left"></i>
        </p>
        </a>
        <ul class="nav nav-treeview">
            <li class="nav-item">
                <a href="{{ url('message/compose') }}" class="nav-link">
                    <i class="far fa-circle nav-icon"></i>
                    <p>{{ _lang('Compose') }}</p>
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ url('message/inbox') }}" class="nav-link">
                    <i class="far fa-circle nav-icon"></i>
                    <p> {{ _lang('Inbox') }}</p>
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ url('message/outbox') }}" class="nav-link">
                    <i class="far fa-circle nav-icon"></i>
                    <p> {{ _lang('Outbox') }}</p>
                </a>
            </li>
        </ul>
        </li> --}}

        @endcan

        @can('tickets_view')

        {{-- <li class="nav-item">
                <a href="#" class="nav-link {{ Request::is('admin/ticket/*') ? 'active' : ''}}">
        <i class="nav-icon fa fa-envelope"></i>
        <p>
            {{ _lang('Tickets') }}
            <i class="right fas fa-angle-left"></i>
        </p>
        </a>
        <ul class="nav nav-treeview">
            <li class="nav-item">
                <a href="{{ url('admin/ticket/create') }}" class="nav-link">
                    <i class="far fa-circle nav-icon"></i>
                    <p>{{ _lang('Create a Ticket') }}</p>
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ url('admin/ticket/all') }}" class="nav-link">
                    <i class="far fa-circle nav-icon"></i>
                    <p> {{ _lang('All Tickets') }}</p>
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ url('admin/ticket/canned-messages') }}" class="nav-link">
                    <i class="far fa-circle nav-icon"></i>
                    <p> {{ _lang('Canned Messages') }}</p>
                </a>
            </li>
        </ul>
        </li> --}}

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
                @can('database_backup_view')
                {{-- <li class="nav-item">
                        <a class="nav-link" href="{{ url('admin/administration/database/backup') }}">
                <i class="far fa-circle nav-icon"></i>
                {{ _lang('Database Backup') }}
                </a>
        </li> --}}
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
        @endif
    </ul>
    </li>

    @endcan

    <li class="nav-item">
        <a href="{{ url('logout') }}" class="nav-link">
            <i class="nav-icon fas fa-sign-out-alt"></i>
            <p>
                {{ _lang('Logout') }}
            </p>
        </a>
    </li>

    </ul>
</nav>
<!-- /.sidebar-menu -->
</div>
<!-- /.sidebar -->