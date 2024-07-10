<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-purple elevation-4">
    <!-- Brand Logo -->
    <a href="{{route('home')}}" class="brand-link">
        <img src="{{ asset('public/images/poslg.png') }}" alt="AdminLTE Logo" class="brand-image img-circle elevation-3"
             style="opacity: .8">
        <span class="brand-text font-weight-light">{{ config('app.name') }}</span>
    </a>
	<!-- Log on to codeastro.com for more projects -->

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user (optional) -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                <img src="{{ auth()->user()->getAvatar() }}" class="img-circle elevation-2" alt="User Image">
            </div>
            <div class="info">
            <a href="#" class="d-block">{{ explode(' ', auth()->user()->getFullname())[0] }}</a>
            </div>
        </div>

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <li class="nav-item has-treeview">
                    <a href="{{route('home')}}" class="nav-link {{ activeSegment('') }}">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>Dashboard</p>
                    </a>
                </li>
                <li class="nav-item has-treeview">
                    <a href="{{ route('users.index') }}" class="nav-link {{ activeSegment('users') }}">
                        <i class="nav-icon fas fa-users"></i>
                        <p>Users</p>
                    </a>
                </li>
                <li class="nav-item has-treeview">
                    <a href="{{ route('bulk-users.upload') }}" class="nav-link {{ activeSegment('bulk-users') }}">
                        <i class="nav-icon fas fa-user-plus"></i>
                        <p>Add Bulk User</p>
                    </a>
                </li>
                <li class="nav-item has-treeview">
                    <a href="{{ route('professions.index') }}" class="nav-link {{ activeSegment('professions') }}">
                    <i class="nav-icon fas fa-user-md"></i>
                        <p>Professions</p>
                    </a>
                </li>
                <li class="nav-item has-treeview">
                    <a href="{{ route('trips.index') }}" class="nav-link {{ activeSegment('trips') }}">
                        <i class="nav-icon fas fa-suitcase"></i>
                        <p>Trips</p>
                    </a>
                </li>
                <li class="nav-item has-treeview">
                    <a href="{{ route('points.index') }}" class="nav-link {{ activeSegment('points') }}">
                        <i class="nav-icon fas fa-star"></i>
                        <p>Points</p>
                    </a>
                </li>
                <li class="nav-item has-treeview">
                    <a href="{{ route('chats.index') }}" class="nav-link {{ activeSegment('chats') }}">
                        <i class="nav-icon fas fa-comment"></i>
                        <p>Chats</p>
                    </a>
                </li>
                <li class="nav-item has-treeview">
                    <a href="{{ route('friends.index') }}" class="nav-link {{ activeSegment('friends') }}">
                        <i class="nav-icon fas fa-heart"></i>
                        <p>Friends</p>
                    </a>
                </li>
                <li class="nav-item has-treeview">
                    <a href="{{ route('notifications.index') }}" class="nav-link {{ activeSegment('notifications') }}">
                        <i class="nav-icon fas fa-bell"></i>
                        <p>Notifications</p>
                    </a>
                </li>
                <li class="nav-item has-treeview">
                    <a href="{{ route('verifications.index') }}" class="nav-link {{ activeSegment('verifications') }}">
                    <i class="nav-icon fas fa-check-circle"></i>
                        <p>Verifications</p>
                    </a>
                </li>

                <li class="nav-item has-treeview">
                    <a href="{{ route('transactions.index') }}" class="nav-link {{ activeSegment('Transactions') }}">
                    <i class="nav-icon fas fa-exchange-alt"></i>
                        <p>Transactions</p>
                    </a>
                </li>
               
                <li class="nav-item has-treeview">
                    <a href="{{ route('news.edit') }}" class="nav-link {{ activeSegment('news') }}">
                        <i class="nav-icon fas fa-gear"></i>
                        <p>Settings</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="#" class="nav-link" onclick="document.getElementById('logout-form').submit()">
                        <i class="nav-icon fas fa-power-off"></i>
                        <p>Logout</p>
                        <form action="{{route('logout')}}" method="POST" id="logout-form">
                            @csrf
                        </form>
                    </a>
                </li>
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div><!-- Log on to codeastro.com for more projects -->
    <!-- /.sidebar -->
</aside>
<?php
function activeSegment($segmentName) {
    $currentUri = $_SERVER['REQUEST_URI'];
    if (strpos($currentUri, $segmentName) !== false) {
        return 'active';
    }
    return '';
}
?>
