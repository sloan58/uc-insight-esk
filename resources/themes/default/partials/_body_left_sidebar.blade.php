<!-- Left side column. contains the logo and sidebar -->
<aside class="main-sidebar">

    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">

        <!-- Sidebar user panel (optional) -->
        <div class="user-panel">
            @if (Auth::check())
                <div class="pull-left image">
                    <img src="{{ asset("/bower_components/admin-lte/dist/img/user2-160x160.jpg") }}" class="img-circle" alt="User Image" />
                </div>
                <div class="pull-left info">
                    <p>{{ Auth::user()->full_name }}</p>
                    <!-- Status -->
                    <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
                </div>
            @endif
        </div>

        <!-- search form (Optional) -->
        <form action="#" method="get" class="sidebar-form">
            <div class="input-group">
                <input type="text" name="q" class="form-control" placeholder="Search..."/>
              <span class="input-group-btn">
                <button type='submit' name='search' id='search-btn' class="btn btn-flat"><i class="fa fa-search"></i></button>
              </span>
            </div>
        </form>
        <!-- /.search form -->

        <!-- Sidebar Menu -->
        <ul class="sidebar-menu">
            <li class="header">Menu</li>
            @if(\Auth::user()->hasRole(['admins','sql-runner','sql-creator','sql-admin']))
            <li class="treeview {{ areActiveRoutes([
            'sql.index',
            'sql.store',
            'sql.history',
            'sql.show',
            'sql.favorites'
            ]) }}">
                <a href="#"><i class="fa fa-database"></i> <span>SQL Query Tool</span> <i class="fa fa-angle-left pull-right"></i></a>
                <ul class="treeview-menu">
                    @if(\Auth::user()->hasRole(['admins','sql-creator','sql-admin']))
                    <li><a href="{{ url('/sql') }}">New Query</a></li>
                    @endif
                    <li><a href="{{ url('/sql/history') }}">Query History</a></li>
                    <li><a href="{{ url('/sql/favorites') }}">Favorite Queries</a></li>
                </ul>
            </li>
            @endif
            @if(\Auth::user()->hasRole(['admins','cert-erasers']))
            <li class="treeview {{ areActiveRoutes([
            'itl.index',
            'ctl.index',
            'phone.show',
            'eraser.bulk.index',
            'eraser.bulk.show',
            'eraser.bulk.create',
            ]) }}">
                <a href="#"><i class="fa fa-eraser"></i> <span>Cert Eraser</span> <i class="fa fa-angle-left pull-right"></i></a>
                <ul class="treeview-menu">
                    <li><a href="{{ route('itl.index') }}">ITL</a></li>
                    <li><a href="{{ route('ctl.index') }}">CTL</a></li>
                    <li><a href="{{ route('eraser.bulk.index') }}">Bulk</a></li>
                </ul>
            </li>
            @endif
            @if(\Auth::user()->hasRole(['admins','autodialer']))
            <li class="treeview {{ areActiveRoutes([
            'autodialer.index',
            'autodialer.bulk.index'
            ]) }}">
                <a href="#"><i class="fa fa-phone"></i> <span>Auto Dialer</span> <i class="fa fa-angle-left pull-right"></i></a>
                <ul class="treeview-menu">
                    <li><a href="{{ url('/autodialer') }}">Single Call</a></li>
                    <li><a href="{{ url('/autodialer/bulk') }}">Bulk Calls</a></li>
                </ul>
            </li>
            <li class=" {{isActiveRoute('cdrs.index')}}">
                <a href="{!! route('cdrs.index') !!}"><i class="fa fa-random"></i> <span>CDR's</span>
                    <i class=""></i></a>
            </li>
            @endif
            <li><a href="{{ route('cluster.index') }}"><i class="fa fa-fax"></i>Clusters</a></li>
            @if(\Auth::user()->hasRole(['admins']))
            <li class="treeview {{ areActiveRoutes([
            'admin.audit.index',
            'admin.users.index',
            'admin.roles.index',
            'admin.permissions.index',
            'admin.routes.index'
            ]) }}">
                <a href="#"><i class='fa fa-cog'></i> <span>Admin</span> <i class="fa fa-angle-left pull-right"></i></a>
                <ul class="treeview-menu">
                    <li><a href="{{ route('admin.audit.index')     }}"><i class='fa fa-binoculars'> </i> Audit   </a></li>
                    <li class="treeview">
                        <a href="#"><i class='fa fa-user-secret'></i> <span>Security</span> <i class="fa fa-angle-left pull-right"></i></a>
                        <ul class="treeview-menu active menu-open">
                            <li><a href="{{ route('admin.users.index')       }}"><i class='fa fa-user'> </i> Users      </a></li>
                            <li><a href="{{ route('admin.roles.index')       }}"><i class='fa fa-users'></i> Roles      </a></li>
                            <li><a href="{{ route('admin.permissions.index') }}"><i class='fa fa-bolt'> </i> Permissions</a></li>
                            <li><a href="{{ route('admin.routes.index')      }}"><i class='fa fa-road'> </i> Routes     </a></li>
                        </ul>
                    </li>
                </ul>
            </li>
            @endif
        </ul><!-- /.sidebar-menu -->
    </section>
    <!-- /.sidebar -->
</aside>
