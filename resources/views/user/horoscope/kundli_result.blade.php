@extends('layouts.app')

@section('title', 'Your Kundli Report')

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-10 col-lg-8">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="fw-bold text-white m-0">Birth Chart / Kundli</h2>
                    <a href="{{ route('user.kundli.form') }}" class="btn btn-outline-light btn-sm rounded-pill px-3">New
                        Report</a>
                </div>

                <div class="card shadow border-0 rounded-4 mb-4">
                    <div class="card-body p-4 p-md-5">
                        <div class="row g-4 mb-5">
                            <div class="col-12">
                                <h5 class="fw-bold border-bottom pb-2 mb-3">Planetary Positions</h5>
                                <div class="table-responsive">
                                    <table class="table table-sm table-hover align-middle">
                                        <thead class="bg-light">
                                            <tr>
                                                <th>Planet</th>
                                                <th>Sign</th>
                                                <th>Degree</th>
                                                <th>Retrograde</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($data as $planet)
                                                @if(is_array($planet) && isset($planet['name']))
                                                    <tr>
                                                        <td class="fw-bold">{{ $planet['name'] }}</td>
                                                        <td>{{ $planet['sign'] }}</td>
                                                        <td>{{ $planet['fullDegree'] }}°</td>
                                                        <td>
                                                            @if($planet['isRetro'] == 'true')
                                                                <span class="badge bg-danger rounded-pill px-2">Yes</span>
                                                            @else
                                                                <span class="text-muted">No</span>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endif
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="bg-dark text-white p-4 rounded-4 text-center">
                            <h5 class="fw-bold mb-3">Need Professional Analysis?</h5>
                            <p class="small opacity-75 mb-4">Our expert astrologers can interpret your birth chart in detail
                                and provide remedies and predictions.</p>
                            <div class="d-flex justify-content-center gap-3">
                                <a href="{{ route('astrologers.index') }}" class="btn btn-primary rounded-pill px-4">Talk to
                                    Astrologer</a>
                                <a href="{{ route('user.ai_chat.index') }}"
                                    class="btn btn-warning rounded-pill px-4 fw-bold">Ask AI Bot</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection