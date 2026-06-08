<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            background-color: #212121;
            color: #fff;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .navbar,
        footer {
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
            margin-top: 8%;
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
                            <i class="fas fa-key"></i> Administrator Password Reset
                        </h3>

                        @if (\Session::has('message'))
                            <div class="alert alert-danger mb-0">
                                {{ \Session::get('message') }}
                            </div>
                        @endif

                        <div class="card-body">
                            <form method="POST" action="{{ route('password.otp.send') }}">
                                @csrf
                                <div class="form-group mb-3">
                                    <label for="email">Administrator Email</label>
                                    <input type="email" placeholder="Enter administrator email" id="email"
                                        class="form-control form-control-lg border border-dark" name="email"
                                        value="{{ old('email') }}" autofocus>
                                    @if ($errors->has('email'))
                                        <span class="text-danger">{{ $errors->first('email') }}</span>
                                    @endif
                                </div>

                                <button type="submit" class="btn btn-primary btn-block btn-lg">Send OTP</button>
                            </form>

                            <div class="text-center mt-3">
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
