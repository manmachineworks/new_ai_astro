@extends('layouts.app')

@section('title', 'Generate Your Kundli')

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow border-0 rounded-4 overflow-hidden">
                    <div class="card-header bg-primary text-white p-4">
                        <h4 class="fw-bold mb-1">Generate Kundli</h4>
                        <p class="small opacity-75 mb-0">Enter your birth details for a professional report.</p>
                    </div>
                    <div class="card-body p-4">
                        <form action="{{ route('user.kundli.get') }}" method="POST">
                            @csrf
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold">Day</label>
                                    <input type="number" name="day" class="form-control" placeholder="DD" min="1" max="31"
                                        required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold">Month</label>
                                    <input type="number" name="month" class="form-control" placeholder="MM" min="1" max="12"
                                        required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold">Year</label>
                                    <input type="number" name="year" class="form-control" placeholder="YYYY" min="1900"
                                        max="2100" required>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Hour (24h)</label>
                                    <input type="number" name="hour" class="form-control" placeholder="HH" min="0" max="23"
                                        required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Minute</label>
                                    <input type="number" name="min" class="form-control" placeholder="MM" min="0" max="59"
                                        required>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Latitude</label>
                                    <input type="number" step="any" name="lat" class="form-control"
                                        placeholder="e.g. 28.6139" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Longitude</label>
                                    <input type="number" step="any" name="lon" class="form-control"
                                        placeholder="e.g. 77.2090" required>
                                </div>

                                <div class="col-12">
                                    <label class="form-label small fw-bold">Timezone</label>
                                    <input type="number" step="0.5" name="tzone" class="form-control" value="5.5"
                                        placeholder="e.g. 5.5 for India" required>
                                </div>
                            </div>

                            <div class="mt-4 d-grid">
                                <button type="submit" class="btn btn-primary btn-lg rounded-pill shadow-sm">
                                    Generate Free Kundli
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection