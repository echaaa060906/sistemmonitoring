<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - SCM System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f4f6f9;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            overflow: hidden;
            width: 100%;
            max-width: 450px;
        }
        .login-header {
            background: #343a40;
            color: #fff;
            padding: 30px 20px;
            text-align: center;
        }
        .login-header h4 {
            margin: 0;
            font-weight: 700;
        }
        .login-body {
            padding: 30px;
        }
        .form-control {
            padding: 12px;
            border-radius: 8px;
        }
        .btn-primary {
            padding: 12px;
            border-radius: 8px;
            font-weight: 600;
            background-color: #007bff;
            border: none;
        }
        .btn-primary:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>

<div class="login-card">
    <div class="login-header">
        <h4><i class="bi bi-person-plus me-2"></i>Create Account</h4>
        <small class="text-light opacity-75">Join SCM Global Tracking</small>
    </div>
    <div class="login-body">
        @if ($errors->any())
            <div class="alert alert-danger py-2 small">
                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('register') }}">
            @csrf
            
            <div class="mb-3">
                <label class="form-label text-muted small fw-bold">Full Name</label>
                <div class="input-group">
                    <span class="input-group-text bg-light"><i class="bi bi-person"></i></span>
                    <input type="text" name="name" class="form-control" value="{{ old('name') }}" required autofocus placeholder="John Doe">
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label text-muted small fw-bold">Email Address</label>
                <div class="input-group">
                    <span class="input-group-text bg-light"><i class="bi bi-envelope"></i></span>
                    <input type="email" name="email" class="form-control" value="{{ old('email') }}" required placeholder="johndoe@example.com">
                </div>
            </div>
            
            <div class="mb-3">
                <label class="form-label text-muted small fw-bold">Password</label>
                <div class="input-group">
                    <span class="input-group-text bg-light"><i class="bi bi-lock"></i></span>
                    <input type="password" name="password" class="form-control" required placeholder="Min. 8 characters">
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label text-muted small fw-bold">Confirm Password</label>
                <div class="input-group">
                    <span class="input-group-text bg-light"><i class="bi bi-lock-fill"></i></span>
                    <input type="password" name="password_confirmation" class="form-control" required placeholder="Repeat password">
                </div>
            </div>

            <button type="submit" class="btn btn-primary w-100 mb-3">
                Register
            </button>

            <div class="text-center small">
                Already have an account? <a href="{{ route('login') }}" class="text-decoration-none">Sign in here</a>
            </div>
        </form>
    </div>
</div>

</body>
</html>
