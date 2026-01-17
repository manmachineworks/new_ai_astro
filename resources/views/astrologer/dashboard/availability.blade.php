@extends('layouts.astrologer')

@section('title', 'Availability')
@section('page-title', 'Availability Schedule')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <form action="{{ route('astrologer.availability.update') }}" method="POST">
            @csrf
            
            <div class="card card-premium mb-4">
                <div class="card-header bg-white border-0 py-3">
                    <h6 class="mb-0 fw-bold"><i class="fas fa-calendar-alt me-2 text-primary"></i>Weekly Schedule</h6>
                    <div class="small text-muted mt-1">Set your standard availability hours. All times are in UTC.</div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light text-uppercase small text-muted">
                                <tr>
                                    <th class="ps-4 border-0" style="width: 150px;">Day</th>
                                    <th class="border-0 text-center" style="width: 100px;">Active</th>
                                    <th class="border-0">Start Time</th>
                                    <th class="border-0">End Time</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach(['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'] as $day)
                                    @php
                                        $rule = $rules[$day] ?? null;
                                        $isActive = $rule ? $rule->is_active : false;
                                        $start = $rule ? $rule->start_time_utc : '09:00';
                                        $end = $rule ? $rule->end_time_utc : '17:00';
                                    @endphp
                                    <tr class="{{ $isActive ? '' : 'bg-light opacity-75' }}">
                                        <td class="ps-4 fw-bold text-dark">{{ $day }}</td>
                                        <td class="text-center">
                                            <div class="form-check form-switch d-inline-block">
                                                <input class="form-check-input" type="checkbox" name="schedule[{{ $day }}][active]" 
                                                       {{ $isActive ? 'checked' : '' }} onchange="this.closest('tr').classList.toggle('bg-light'); this.closest('tr').classList.toggle('opacity-75');">
                                            </div>
                                        </td>
                                        <td>
                                            <input type="time" name="schedule[{{ $day }}][start]" class="form-control" value="{{ $start }}">
                                        </td>
                                        <td>
                                            <input type="time" name="schedule[{{ $day }}][end]" class="form-control" value="{{ $end }}">
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-end">
                <button type="submit" class="btn btn-primary px-5 rounded-pill shadow-sm">
                    Save Schedule
                </button>
            </div>
        </form>
    </div>
</div>
@endsection