<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: radial-gradient(circle at top, #1f2937, #0f172a); }
        .login-card { max-width: 420px; }
    </style>
</head>
<body class="d-flex align-items-center min-vh-100">
    <div class="container d-flex justify-content-center">
        <div class="card shadow-lg login-card w-100">
            <div class="card-body p-4">
                <h4 class="mb-2">Admin Login</h4>
                <p class="text-muted">Use your admin email or phone to sign in.</p>

                @if($errors->any())
                    <div class="alert alert-danger">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.login.submit') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Email or Phone</label>
                        <input type="text" name="login" class="form-control" value="{{ old('login') }}" required autofocus>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" name="remember" id="remember">
                        <label class="form-check-label" for="remember">Remember me</label>
                    </div>
                    <button class="btn btn-primary w-100">Login</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
