@extends('layouts.app')

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center align-items-center" style="min-height: 60vh;">
            <div class="col-md-5">
                <div class="glass-card">
                    <div class="text-center mb-4">
                        <i class="fa-solid fa-mobile-screen fa-3x text-gold mb-3"></i>
                        <h3>Login / Signup</h3>
                        <p class="text-muted">Enter your phone number or email to continue</p>
                    </div>

                    @if(session('error'))
                        <div class="alert alert-danger mb-3">{{ session('error') }}</div>
                    @endif

                    @if(session('success'))
                        <div class="alert alert-success mb-3">{{ session('success') }}</div>
                    @endif
                    
                    @if($errors->any())
                        <div class="alert alert-danger mb-3">{{ $errors->first() }}</div>
                    @endif

                    <form method="POST" action="{{ route('auth.phone.verify') }}">
                        @csrf

                        @if(!session('waiting_for_otp'))
                            <div class="mb-4">
                                <label class="form-label text-muted small">Phone Number</label>
                                <input type="text" name="phone" class="form-control form-control-cosmic form-control-lg"
                                    placeholder="e.g. 9876543210" required autofocus>
                            </div>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-cosmic btn-lg">Send OTP</button>
                            </div>
                        @else
                            <!-- Hidden phone input to maintain state -->
                            <input type="hidden" name="phone" value="{{ session('phone') }}">

                            <div class="mb-4 text-center">
                                <p class="text-white">OTP sent to {{ session('phone') }}</p>
                                <label class="form-label text-muted small">Enter OTP</label>
                                <input type="text" name="otp"
                                    class="form-control form-control-cosmic form-control-lg text-center" placeholder="XXXX"
                                    maxlength="4" required autofocus>
                            </div>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-cosmic btn-lg">Verify & Login</button>
                            </div>
                            <div class="text-center mt-3">
                                <a href="{{ route('auth.phone.show') }}" class="small text-gold">Change Number</a>
                            </div>
                        @endif
                    </form>

                    <div class="text-center my-4 text-muted small">or</div>

                    <form method="POST" action="{{ route('auth.email.login') }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label text-muted small">Email</label>
                            <input type="email" name="email" class="form-control form-control-cosmic form-control-lg"
                                placeholder="you@example.com" value="{{ old('email') }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-muted small">Password</label>
                            <input type="password" name="password" class="form-control form-control-cosmic form-control-lg"
                                placeholder="Enter your password" required>
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" name="remember" id="remember">
                            <label class="form-check-label small text-muted" for="remember">
                                Remember me
                            </label>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-outline-light btn-lg">Login with Email</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
