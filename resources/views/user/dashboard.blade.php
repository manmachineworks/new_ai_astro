<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>User Dashboard | {{ config('app.name') }}</title>
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="card shadow-sm">
                    <div class="card-body d-flex flex-column gap-3">
                        <h1 class="h4 mb-0">Welcome back!</h1>
                        <p class="mb-0 text-muted">You are logged in as a user.</p>
                        <form method="POST" action="{{ route('auth.logout') }}">
                            @csrf
                            <button class="btn btn-outline-danger" type="submit">Logout</button>
                        </form>
                        <a class="btn btn-outline-secondary" href="{{ route('auth.phone.show') }}">Back to login</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
