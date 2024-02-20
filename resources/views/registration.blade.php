<!DOCTYPE html>
<html lang="en">

<head>
    <title>KPS-Kadayat Pharmacy Store</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
 <!-- Include Bootstrap CSS first -->
 <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
  
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.1/jquery.min.js"></script>

    
</head>

<body>
    
    {{-- @if (Auth::user())
        <script>
            window.location = "/dashboard";
        </script>
    @endif --}}


    @if (Auth::check())
    @if (Auth::user()->email === 'dineshtkp14@gmail.com')
        {{-- User is authenticated and has the correct email, allow access to dashboard --}}
        {{-- Your dashboard content goes here --}}
    @else
        {{-- User is authenticated but does not have the correct email, redirect to login --}}
        <script>
            window.location = "/login";
        </script>
    @endif
@else
    {{-- User is not authenticated, redirect to login --}}
    <script>
        window.location = "/login";
    </script>
@endif






    <div class="container-fluid m-0 p-0">


        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <div class="container-fluid">
                <a class="navbar-brand text-warning" href="">WT</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                    data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                    aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                        <li class="nav-item">
                            <a class="nav-link active text-warning" aria-current="page" href="">WholesaLe Tikapur
                                </a>
                        </li>



                    </ul>
                    <div class="d-flex">
                        <a class="nav-link active" aria-current="page" href="#"><span class="pe-2 text-white">
                            </span>

                                    <a class="nav-link btn btn-outline-light btn-primary regnavbtn" href="{{ route('signout') }}">Logout</a>

                    </div>
                </div>
            </div>
        </nav>





        <main class="signup-form  " style="margin-top:6%;>
    <div class="cotainer">
            <div class="row justify-content-center m-0 p-0">
                <div class="col-md-5">
                    <div class="card">
                        <h3 class="card-header text-center">Register User</h3>
                        <div class="card-body">
                            <form action="{{ route('postsignup') }}" method="POST">
                                @csrf
                                <div class="form-group mb-3">
                                    <input type="text" placeholder="Name" id="name" class="form-control"
                                        name="name" autofocus>
                                    @if ($errors->has('name'))
                                        <span class="text-danger">{{ $errors->first('name') }}</span>
                                    @endif
                                </div>
                                <input type="hidden" value="{{ session('user_email') }}" name="user_email">
                                <div class="form-group mb-3">
                                    <input type="text" placeholder="address" id="address" class="form-control"
                                        name="address" autofocus>
                                    @if ($errors->has('address'))
                                        <span class="text-danger">{{ $errors->first('address') }}</span>
                                    @endif
                                </div>

                                <div class="form-group mb-3">
                                    <input type="text" placeholder="Phoneno" id="phoneno" class="form-control"
                                        name="phoneno" autofocus>
                                    @if ($errors->has('phoneno'))
                                        <span class="text-danger">{{ $errors->first('phoneno') }}</span>
                                    @endif
                                </div>
                                <div class="form-group mb-3">
                                    <input type="text" placeholder="Email" id="email_address" class="form-control"
                                        name="email" autofocus>
                                    @if ($errors->has('email'))
                                        <span class="text-danger">{{ $errors->first('email') }}</span>
                                    @endif
                                </div>
                                <div class="form-group mb-3">
                                    <input type="password" placeholder="Password" id="password" class="form-control"
                                        name="password">
                                    @if ($errors->has('password'))
                                        <span class="text-danger">{{ $errors->first('password') }}</span>
                                    @endif
                                </div>
                                <div class="form-group mb-3">
                                    <div class="checkbox">
                                        <label><input type="checkbox" name="remember"> Remember Me</label>
                                    </div>
                                </div>
                                <div class="d-grid mx-auto">
                                    <button type="submit" class="btn btn-dark btn-block">Sign up</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
    </div>
    </main>




   

    </div>
</body>

</html>
