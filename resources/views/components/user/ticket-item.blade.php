@props(['ticket'])

<a href="{{ route('user.support.show', $ticket['id']) }}"
    class="list-group-item list-group-item-action border-0 border-bottom p-4">
    <div class="d-flex align-items-center">
        <div class="flex-shrink-0">
            <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center fw-bold"
                style="width: 50px; height: 50px; font-size: 0.8rem;">
                #{{ $ticket['id'] }}
            </div>
        </div>
        <div class="flex-grow-1 ms-4 min-width-0">
            <div class="row align-items-center">
                <div class="col-md-7">
                    <h6 class="mb-1 fw-bold text-dark text-truncate">{{ $ticket['subject'] ?? 'Ticket Subject' }}</h6>
                    <p class="mb-0 text-muted small">
                        <span class="badge bg-light text-dark border me-2">{{ $ticket['category'] ?? 'General' }}</span>
                        <span
                            class="d-none d-sm-inline opacity-75">{{ $ticket['last_message'] ?? 'No messages yet.' }}</span>
                    </p>
                </div>
                <div class="col-md-5 d-none d-md-block text-end">
                    <div class="text-muted small mb-1">Last updated</div>
                    <div class="text-dark small fw-medium">{{ $notification['time'] ?? 'Just now' }}</div>
                    <!-- Standardizing on relative time if possible, or $ticket['updated_at'] -->
                    <div class="text-muted small">{{ $ticket['updated_at'] ?? 'Never' }}</div>
                </div>
            </div>
        </div>
        <div class="ms-3 d-flex align-items-center">
            <x-ui.badge :color="$ticket['status'] === 'Open' ? 'success' : 'secondary'" :label="$ticket['status']" />
            <i class="bi bi-chevron-right ms-3 text-muted"></i>
        </div>
    </div>
</a>