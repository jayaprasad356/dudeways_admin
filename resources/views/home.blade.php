@extends('layouts.admin')

@section('content-header', 'Dashboard')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-4 col-6">
                <!-- small box -->
                <div class="small-box bg-red">
                    <div class="inner">
                        <h3>{{$users_count}}</h3>
                        <p>Total Users</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <a href="{{ route('users.index') }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <!-- ./col -->

            <div class="col-lg-4 col-6">
                <!-- small box -->
                <div class="small-box bg-green">
                    <div class="inner">
                        <h3>{{$trips_count}}</h3>
                        <p>Total Trips</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-suitcase"></i>
                    </div>
                    <a href="{{ route('trips.index') }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>

             <div class="col-lg-4 col-6">
                <!-- small box -->
                <div class="small-box bg-blue">
                    <div class="inner">
                        <h3>{{ $pending_trips_count }}</h3>
                        <p>Pending Trips</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-suitcase"></i>
                    </div>
                    <a href="{{ route('trips.index', ['trip_status' => '0']) }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>

            <div class="col-lg-4 col-6">
                <!-- small box -->
                <div class="small-box bg-green">
                    <div class="inner">
                        <h3>{{$points_count}}</h3>
                        <p>Total Points</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-star"></i>
                    </div>
                    <a href="{{ route('points.index') }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <!-- ./col -->

            <div class="col-lg-4 col-6">
                <!-- small box -->
                <div class="small-box bg-yellow">
                    <div class="inner">
                        <h3>{{ $pending_profile_count }}</h3>
                        <p>Pending Profiles</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-id-badge"></i>
                    </div>
                    <a href="{{ route('users.index') }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <!-- ./col -->

            <div class="col-lg-4 col-6">
                <!-- small box -->
                <div class="small-box bg-orange">
                    <div class="inner">
                        <h3>{{ $pending_cover_image_count }}</h3>
                        <p>Pending Cover Images</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-image"></i>
                    </div>
                    <a href="{{ route('users.index') }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>

            <div class="col-lg-4 col-6">
                <!-- small box -->
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3>{{ $pending_verification }}</h3>
                        <p>Pending Verification</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-user-clock"></i> <!-- Example of a different icon -->
                    </div>
                    <a href="{{ route('verifications.index') }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>

            <!-- ./col -->
        </div>
        <!-- /.row -->
    </div>
@endsection
