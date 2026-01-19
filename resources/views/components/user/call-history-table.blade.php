@props(['calls'])

<div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
        <thead class="table-light">
            <tr>
                <th scope="col" class="ps-4">Astrologer</th>
                <th scope="col">Date & Time</th>
                <th scope="col">Duration</th>
                <th scope="col">Cost</th>
                <th scope="col">Status</th>
                <th scope="col" class="text-end pe-4">Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse($calls as $call)
                <tr>
                    <td class="ps-4">
                        <div class="d-flex align-items-center">
                            <img class="rounded-circle object-fit-cover" style="width: 40px; height: 40px;"
                                src="https://ui-avatars.com/api/?name={{ urlencode($call['astrologer_name']) }}&color=7F9CF5&background=EBF4FF"
                                alt="">
                            <span class="ms-3 fw-medium text-dark">{{ $call['astrologer_name'] }}</span>
                        </div>
                    </td>
                    <td class="text-secondary small">
                        <div>{{ $call['date'] }}</div>
                        <div class="text-muted">{{ $call['time'] }}</div>
                    </td>
                    <td class="text-secondary">{{ $call['duration'] }}</td>
                    <td class="fw-bold text-dark">₹{{ number_format($call['cost'], 2) }}</td>
                    <td>
                        <x-ui.badge :color="$call['status'] === 'completed' ? 'success' : ($call['status'] === 'missed' ? 'danger' : ($call['status'] === 'busy' ? 'warning' : 'secondary'))"
                            :label="ucfirst($call['status'])" />
                    </td>
                    <td class="text-end pe-4">
                        <a href="#" class="btn btn-sm btn-outline-primary">Rate</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6">
                        <x-ui.empty-state title="No calls yet" description="Start a consultation to see history here." />
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>