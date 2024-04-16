<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WT</title>
    <!-- Include Bootstrap CSS first -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.1/jquery.min.js"></script>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <style>
        body {
            background-color: #212121; /* Dark grey background */
            color: #fff; /* White text */
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            overflow-x: hidden; /* Hide horizontal scrollbar */
        }

        .navbar {
            background-color: #333; /* Dark navbar */
        }

        .navbar-brand {
            color: #ffcc00 !important; /* Yellow color for brand */
        }

        .navbar-toggler-icon {
            background-color: #ffcc00; /* Yellow color for navbar toggler */
        }

        .card {
            background-color: #333; /* Dark card background */
            border: 2px solid #ffcc00; /* Yellow border */
        }

        .form-control {
            background-color: #444; /* Dark form background */
            color: white; /* White text */
            border-color: #ffcc00; /* Yellow border */
        }

        .btn-primary {
            background-color: #ffcc00; /* Yellow button background */
            border-color: #ffcc00; /* Yellow button border */
        }

        .btn-primary:hover {
            background-color: #ffd54f; /* Lighter yellow on hover */
            border-color: #ffd54f; /* Lighter yellow border on hover */
        }

        footer {
            background-color: #333; /* Dark footer background */
            color: #aaa; /* Light grey text */
            font-size: 0.9rem; /* Smaller font size */
        }

        .footerlogo {
            max-width: 50px; /* Limit logo size */
        }

        .footericons img {
            max-width: 30px; /* Limit icon size */
            filter: grayscale(100%); /* Convert icons to grayscale by default */
            transition: filter 0.3s; /* Smooth transition on hover */
        }

        .footericons img:hover {
            filter: grayscale(0%); /* Restore color on hover */
        }

        .login-form {
            margin-top: 5%; /* Adjust top margin for login form */
        }

        .card-header {
            background-color: #444; /* Darker background for card header */
            color: #ffcc00; /* Yellow text for card header */
        }

        .card-header::after {
            content: ''; /* Add a pseudo-element after the card header */
            display: block;
            width: 100%;
            height: 2px; /* Thin yellow line underneath the header */
            background-color: #ffcc00; /* Yellow color */
            margin-top: 10px; /* Add some space between header and line */
        }

        .regnavbtn {
            background-color: #007bff; /* Blue color for registration button */
            border-color: #007bff; /* Blue border */
            color: #fff; /* White text */
        }

        .regnavbtn:hover {
            background-color: #0056b3; /* Darker blue on hover */
            border-color: #0056b3; /* Darker blue border on hover */
        }
    </style>
</head>

<body>

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand text-warning" href="#">WT</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link active text-warning" aria-current="page" href="#">Zumanzi</a>
                    </li>
                </ul>
                <div class="d-flex">
                    <a class="nav-link active" aria-current="page" href="#">
                        <button class="btn btn-success regnavbtn">Play Now</button>
                    </a>
                    <div class="trial-version nav-link">
                        This Game is for trial purposes only. Please purchase a license for genuine use.
                     </div>
                </div>
            </div>
        </div>
    </nav>

    <main class="login-form">
        <div class="container">
            <div class="row justify-content-center">
                <!-- Left column for game image -->
                <div class="col-md-3 mb-3">
                    <img src="https://picsum.photos/200/300" alt="Game Image Left" class="img-fluid">
                </div>
                
                <!-- Login form column -->
                <div class="col-md-6">
                    <div class="card">
                        <h3 class="card-header text-center"> <i class="fas fa-ghost"></i> <i class="fas fa-ghost"></i>  <i class="fas fa-ghost"></i> WELCOME PLAYER <i class="fas fa-ghost"></i> <i class="fas fa-ghost"></i>  <i class="fas fa-ghost"></i> </h3>
                      
                        @if (\Session::has('message'))
                            <div class="bg-danger text-white alert alert-info">
                                {{ \Session::get('message') }}
                            </div>
                        @endif
                        @if (\Session::has('signupmessage'))
                            <div class="alert alert-info">
                                {{ \Session::get('signupmessage') }}
                            </div>
                        @endif
                        <div class="card-body">
                            <form method="POST" action="{{ route('postlogin') }}">
                                @csrf
                                <div class="form-group mb-3">
                                    <input type="text" placeholder="Email" id="email"
                                        class="form-control form-control-lg border border-dark" name="email"
                                        autofocus>
                                    @if ($errors->has('email'))
                                        <span class="text-danger">{{ $errors->first('email') }}</span>
                                    @endif
                                </div>
                                <div class="form-group mb-3">
                                    <input type="password" placeholder="Password" id="password"
                                        class="form-control form-control-lg border border-dark" name="password">
                                    @if ($errors->has('password'))
                                        <span class="text-danger">{{ $errors->first('password') }}</span>
                                    @endif
                                </div>

                                <div class="d-grid mx-auto">
                                    <button type="submit" class="btn btn-primary btn-block">
                                        <h2>Log In</h2>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                
                <!-- Right column for game image -->
                <div class="col-md-3 mb-3">
                    <img src="https://picsum.photos/200/300" alt="Game Image Right" class="img-fluid">
                </div>
            </div>
        </div>
    </main>

   
    

    <div class="container-fluid">
        <footer class="fixed-bottom p-4 bg-dark text-white">
            <div class="d-lg-flex justify-content-between">
                <div>
                    
                    <span><img src="{{ asset('assets/img/logop.png') }}" alt="" class="footerlogo"></span>
                </div>
                <div class="copyright">
                    <p>Developed and Designed by <a href="www.facebook.com/tkp.dinesh" target="_blank"
                            class="text-info">Dinesh Bajgain</a></p>
                </div>
                <div>
                    <ul class="d-flex gap-3 list-unstyled">
                        <li><a href="https://www.facebook.com/tkp.dinesh" target="blank"><img class="footericons"
                                    src="{{ asset('assets/img/facebook.png') }}" alt=""></a></li>
                        <li><a href="https://www.instagram.com/dinesh_bajgain/" target="blank"><img
                                    class="footericons" src="{{ asset('assets/img/instagram.png') }}"
                                    alt=""></a></li>
                        <li><a href="https://www.linkedin.com/in/dinesh-bajgain-017841121/" target="blank"><img
                                    class="footericons" src="{{ asset('assets/img/linkedin.png') }}"
                                    alt=""></a></li>
                    </ul>
                </div>
            </div>
        </footer>
    </div>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>

</body>

</html>
