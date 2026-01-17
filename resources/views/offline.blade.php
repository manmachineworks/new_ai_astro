@extends('layouts.app')

@section('content')
    <div class="container py-5 text-center">
        <div class="display-1 text-muted mb-3">
            <i class="fas fa-wifi-slash"></i>
        </div>
        <h1 class="h3 mb-3">You are offline</h1>
        <p class="text-muted">
            It seems you have lost your internet connection.
            <br>Please check your connection and try again.
        </p>
        <a href="{{ url('/') }}" class="btn btn-primary mt-3">Try Again</a>
    </div>
@endsection