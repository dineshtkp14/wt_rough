<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            background-color: #212121;
            color: #fff;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .navbar {
            background-color: #333;
        }

        .navbar-brand,
        .card-header,
        a {
            color: #ffcc00 !important;
        }

        .card {
            background-color: #333;
            border: 2px solid #ffcc00;
        }

        .card-header {
            background-color: #444;
        }

        .form-control {
            background-color: #444;
            color: white;
            border-color: #ffcc00;
        }

        .btn-primary {
            background-color: #ffcc00;
            border-color: #ffcc00;
            color: #212121;
            font-weight: 700;
        }

        .btn-primary:hover {
            background-color: #ffd54f;
            border-color: #ffd54f;
            color: #212121;
        }

        .reset-form {
            margin-top: 6%;
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand text-warning" href="{{ route('login') }}">WT</a>
        </div>
    </nav>

    <main class="reset-form">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <div class="card">
                        <h3 class="card-header text-center">
                            <i class="fas fa-mobile-alt"></i> Verify OTP
                        </h3>

                        @if (\Session::has('status'))
                            <div class="alert alert-success mb-0">
                                {{ \Session::get('status') }}
                            </div>
                        @endif

                        @if (\Session::has('message'))
                            <div class="alert alert-danger mb-0">
                                {{ \Session::get('message') }}
                            </div>
                        @endif

                        <div class="card-body">
                            <form method="POST" action="{{ route('password.otp.reset') }}">
                                @csrf
                                <div class="form-group mb-3">
                                    <label for="otp">OTP Code</label>
                                    <input type="text" inputmode="numeric" maxlength="6" placeholder="Enter 6 digit OTP"
                                        id="otp" class="form-control form-control-lg border border-dark" name="otp"
                                        value="{{ old('otp') }}" autofocus>
                                    @if ($errors->has('otp'))
                                        <span class="text-danger">{{ $errors->first('otp') }}</span>
                                    @endif
                                </div>

                                <div class="form-group mb-3">
                                    <label for="password">New Password</label>
                                    <input type="password" placeholder="New password" id="password"
                                        class="form-control form-control-lg border border-dark" name="password">
                                    @if ($errors->has('password'))
                                        <span class="text-danger">{{ $errors->first('password') }}</span>
                                    @endif
                                </div>

                                <div class="form-group mb-3">
                                    <label for="password_confirmation">Confirm Password</label>
                                    <input type="password" placeholder="Confirm password" id="password_confirmation"
                                        class="form-control form-control-lg border border-dark"
                                        name="password_confirmation">
                                </div>

                                <button type="submit" class="btn btn-primary btn-block btn-lg">Reset Password</button>
                            </form>

                            <div class="d-flex justify-content-between mt-3">
                                <a href="{{ route('password.request') }}">Resend OTP</a>
                                <a href="{{ route('login') }}">Back to Login</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
</body>

</html>
