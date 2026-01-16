@extends('layouts.app')

@section('title', 'Processing Payment')

@section('content')
    <div class="container py-5 text-center">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow-sm p-5">
                    <div class="mb-4">
                        <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                    <h3 class="mb-3">Payment Processing...</h3>
                    <p class="text-muted">Please wait while we confirm your transaction with the bank.</p>
                    <p class="small text-secondary">Do not close this window.</p>

                    <div class="mt-4">
                        <a href="{{ route('dashboard') }}" class="btn btn-outline-primary">Go to Dashboard</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection