@extends('layouts.admin')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-primary text-white">Recommendation Weights</div>
                    <div class="card-body">
                        <form action="{{ route('admin.recommendations.update') }}" method="POST">
                            @csrf
                            @foreach($settings as $key => $value)
                                <div class="mb-3">
                                    <label class="form-label text-capitalize">{{ str_replace('_', ' ', $key) }}</label>
                                    <input type="number" name="{{ $key }}" class="form-control" value="{{ $value }}">
                                </div>
                            @endforeach
                            <button type="submit" class="btn btn-primary">Save Weights</button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-info text-white">Preview Suggestions</div>
                    <div class="card-body">
                        <form action="{{ route('admin.recommendations.preview') }}" method="GET" class="mb-4">
                            <div class="input-group">
                                <input type="number" name="user_id" class="form-control" placeholder="User ID"
                                    value="{{ request('user_id') }}">
                                <button class="btn btn-info text-white">Preview</button>
                            </div>
                        </form>

                        @if(isset($previewData))
                            <h5>Preview for User ID: {{ request('user_id') }}</h5>
                            <ul class="list-group">
                                @foreach($previewData as $astro)
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <div>
                                            <strong>{{ $astro->display_name ?? $astro->user->name }}</strong>
                                            <br>
                                            <small class="text-muted">Score:
                                                {{ number_format($astro->recommendation_score, 1) }}</small>
                                            <br>
                                            @foreach($astro->recommendation_reasons ?? [] as $reason)
                                                <span class="badge bg-secondary">{{ $reason }}</span>
                                            @endforeach
                                        </div>
                                        <span class="badge bg-primary rounded-pill">Rank #{{ $loop->iteration }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection