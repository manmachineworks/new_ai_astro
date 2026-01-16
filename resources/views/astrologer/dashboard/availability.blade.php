@extends('layouts.app')

@section('title', 'Availability Manager')

@section('content')
    <div class="container py-4">
        <div class="row">
            <div class="col-md-3">
                @include('astrologer.dashboard.nav')
            </div>
            <div class="col-md-9">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Weekly Availability (UTC)</h5>
                        <button type="button" class="btn btn-outline-primary btn-sm" onclick="copyToAll()">Copy Mon to
                            All</button>
                    </div>
                    <div class="card-body">
                        @if(session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif

                        <form action="{{ route('astrologer.availability.update') }}" method="POST">
                            @csrf
                            <div class="table-responsive">
                                <table class="table align-middle">
                                    <thead>
                                        <tr>
                                            <th>Active</th>
                                            <th>Day</th>
                                            <th>Start Time (UTC)</th>
                                            <th>End Time (UTC)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php $days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday']; @endphp
                                        @foreach($days as $idx => $day)
                                            @php $rule = $rules[$idx] ?? null; @endphp
                                            <tr>
                                                <td>
                                                    <div class="form-check form-switch">
                                                        <input class="form-check-input" type="checkbox"
                                                            name="schedule[{{ $idx }}][active]" value="1" {{ $rule && $rule->is_active ? 'checked' : '' }}>
                                                    </div>
                                                </td>
                                                <td class="fw-medium">{{ $day }}</td>
                                                <td>
                                                    <input type="time" name="schedule[{{ $idx }}][start]"
                                                        class="form-control form-control-sm time-start"
                                                        value="{{ $rule ? \Carbon\Carbon::parse($rule->start_time_utc)->format('H:i') : '09:00' }}">
                                                </td>
                                                <td>
                                                    <input type="time" name="schedule[{{ $idx }}][end]"
                                                        class="form-control form-control-sm time-end"
                                                        value="{{ $rule ? \Carbon\Carbon::parse($rule->end_time_utc)->format('H:i') : '17:00' }}">
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="d-flex justify-content-end p-2">
                                <button type="submit" class="btn btn-primary">Update Schedule</button>
                            </div>
                        </form>
                    </div>
                    <div class="card-footer bg-light text-muted small">
                        <i class="fas fa-info-circle me-1"></i> Ensure you set times in UTC. Your profile will convert these
                        to the user's local timezone automatically.
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function copyToAll() {
            const mondayStart = document.querySelector('input[name="schedule[1][start]"]').value;
            const mondayEnd = document.querySelector('input[name="schedule[1][end]"]').value;
            const mondayActive = document.querySelector('input[name="schedule[1][active]"]').checked;

            document.querySelectorAll('tbody tr').forEach((row, idx) => {
                if (idx === 1) return; // Skip Monday
                row.querySelector('.time-start').value = mondayStart;
                row.querySelector('.time-end').value = mondayEnd;
                row.querySelector('.form-check-input').checked = mondayActive;
            });
        }
    </script>
@endsection