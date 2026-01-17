@extends('layouts.astrologer')

@section('title', 'Dashboard')
@section('page-title', 'Overview')

@section('content')
    <div class="container-fluid">
        <!-- Customize Toggle -->
        <div class="d-flex justify-content-end mb-4">
            <button class="btn btn-outline-primary btn-sm rounded-pill px-3" id="toggleCustomize">
                <i class="fas fa-pencil-alt me-2"></i>Customize Layout
            </button>
        </div>

        <!-- Widget Grid -->
        <div class="row g-4" id="widget-grid">
            <!-- Summary Widgets (Default Top Row) -->
            <div class="col-12 col-md-6 col-lg-3 widget-item" data-id="calls_summary">
                <x-astrologer.widgets.summary-card title="Today's Calls" value="12" icon="fas fa-phone-alt" color="info"
                    trend="+2" />
            </div>
            <div class="col-12 col-md-6 col-lg-3 widget-item" data-id="chats_summary">
                <x-astrologer.widgets.summary-card title="Today's Chats" value="28" icon="fas fa-comments" color="success"
                    trend="+5" />
            </div>
            <div class="col-12 col-md-6 col-lg-3 widget-item" data-id="earnings_summary">
                <x-astrologer.widgets.summary-card title="Today's Earnings" value="₹ 1,240" icon="fas fa-rupee-sign"
                    color="warning" trend="+15%" />
            </div>
            <div class="col-12 col-md-6 col-lg-3 widget-item" data-id="rating_summary">
                <x-astrologer.widgets.summary-card title="Avg. Rating" value="4.8" icon="fas fa-star" color="primary"
                    subtext="from 5 reviews" />
            </div>

            <!-- Main Content Widgets (Default Middle) -->
            <div class="col-12 col-lg-8 widget-item" data-id="active_chats">
                <div class="card card-premium h-100">
                    <div class="card-header bg-transparent border-0 py-3 d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 fw-bold">Active Chats</h6>
                        <span class="badge bg-success-subtle text-success rounded-pill">3 Active</span>
                    </div>
                    <div class="card-body p-0">
                        <div class="text-center py-5 text-muted">
                            <i class="fas fa-comments fa-2x mb-3 text-light"></i>
                            <p class="mb-0">No stats available yet</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-lg-4 widget-item" data-id="availability">
                <div class="card card-premium h-100">
                    <div class="card-header bg-transparent border-0 py-3">
                        <h6 class="mb-0 fw-bold">Quick Availability</h6>
                    </div>
                    <div class="card-body">
                        <!-- Placeholder for Availability Switcher -->
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="small fw-semibold"><i class="fas fa-phone me-2 text-info"></i>Calls</span>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" checked>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="small fw-semibold"><i class="fas fa-comment me-2 text-success"></i>Chats</span>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" checked>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="small fw-semibold"><i class="fas fa-robot me-2 text-primary"></i>AI Chat</span>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
    <script>
        const grid = document.getElementById('widget-grid');
        let sortable = null;

        document.getElementById('toggleCustomize').addEventListener('click', function () {
            this.classList.toggle('active');
            const isCustomizing = this.classList.contains('active');

            if (isCustomizing) {
                this.innerHTML = '<i class="fas fa-save me-2"></i>Save Layout';
                this.classList.replace('btn-outline-primary', 'btn-primary');
                // Enable Sortable
                sortable = new Sortable(grid, {
                    animation: 150,
                    ghostClass: 'bg-light',
                    handle: '.card-premium', // Drag by card
                    onEnd: function (evt) {
                        // Logic to save order
                        console.log('Moved item');
                    },
                });
                // Add visual cue
                grid.classList.add('border', 'border-dashed', 'p-2', 'rounded');
            } else {
                this.innerHTML = '<i class="fas fa-pencil-alt me-2"></i>Customize Layout';
                this.classList.replace('btn-primary', 'btn-outline-primary');
                // Disable/Destroy Sortable
                if (sortable) sortable.destroy();

                // Save to DB (mock)
                saveLayout();

                grid.classList.remove('border', 'border-dashed', 'p-2', 'rounded');
            }
        });

        function saveLayout() {
        const widgetIds = Array.from(grid.children).map(el => el.getAttribute('data-id'));
        
        fetch('{{ route("astrologer.dashboard.layout") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ widgets: widgetIds })
        })
        .then(response => response.json())
        .then(data => {
            if(data.status === 'success') {
                // Optional: Show toast
                console.log('Layout saved!');
            }
        })
        .catch(error => console.error('Error saving layout:', error));
    }
    </script>
@endpush