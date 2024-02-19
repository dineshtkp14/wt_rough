@extends('layouts.master')
@include('layouts.breadcrumb')

@section('content')
<div class="main-content"> 
    @yield('breadcrumb')




    <div class="container-fluid">


            <div class="container mt-5 ">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header text-center ">
                            <h3>{{ __('Change Password') }}</h3>
                        </div>

                        <form action="{{ route('update-password') }}" method="POST">
                            @csrf
                            <div class="card-body">
                                @if (session('status'))
                                    <div class="alert alert-success" role="alert">
                                        {{ session('status') }}
                                    </div>
                                @elseif (session('error'))
                                    <div class="alert alert-danger" role="alert">
                                        {{ session('error') }}
                                    </div>
                                @endif

                                <div class="mb-3">
                                    <label for="oldPasswordInput" class="form-label">Old Password</label>
                                    <input name="old_password" type="password"
                                        class="form-control @error('old_password') is-invalid @enderror"
                                        id="oldPasswordInput" placeholder="Old Password">
                                    @error('old_password')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label for="newPasswordInput" class="form-label">New Password</label>
                                    <input name="new_password" type="password"
                                        class="form-control @error('new_password') is-invalid @enderror"
                                        id="newPasswordInput" placeholder="New Password">
                                    @error('new_password')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label for="confirmNewPasswordInput" class="form-label">Confirm New Password</label>
                                    <input name="new_password_confirmation" type="password" class="form-control"
                                        id="confirmNewPasswordInput" placeholder="Confirm New Password">
                                </div>

                            </div>

                            <div class="card-footer d-grid mx-auto">
                                <button class="btn btn-lg btn-success btn-block">Change Password</button>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>


    </div>
</div>
@stop
