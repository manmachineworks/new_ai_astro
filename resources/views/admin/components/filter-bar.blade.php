@props([
    'action' => '',
    'filters' => []
])
<form action="{{ $action }}" method="GET" class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body p-3">
        <div class="row g-2 align-items-center">
            <!-- Search -->
            <div class="col-md-3">
                <div class="input-group">
                    <span class="input-group-text bg-light border-0"><i class="fas fa-search text-muted"></i></span>
                    <input type="text" name="search" class="form-control bg-light border-0" placeholder="Search..." value="{{ request('search') }}">
                </div>
            </div>

            <!-- Date Range (Optional) -->
            @if(in_array('date', $filters))
            <div class="col-md-2">
                 <input type="date" name="start_date" class="form-control bg-light border-0" value="{{ request('start_date') }}" placeholder="Start Date">
            </div>
            <div class="col-md-2">
                 <input type="date" name="end_date" class="form-control bg-light border-0" value="{{ request('end_date') }}" placeholder="End Date">
            </div>
            @endif

            <!-- Status (Optional) -->
            @if(in_array('status', $filters))
            <div class="col-md-2">
                <select name="status" class="form-select bg-light border-0">
                    <option value="">All Statuses</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                </select>
            </div>
            @endif
            
            <!-- Slots for extra filters -->
            {{ $slot }}

            <div class="col-auto ms-auto d-flex gap-2">
                <button type="submit" class="btn btn-primary rounded-pill px-4">Filter</button>
                <a href="{{ $action }}" class="btn btn-light rounded-pill px-3" data-bs-toggle="tooltip" title="Reset Filters"><i class="fas fa-undo"></i></a>
                
                @if(in_array('export', $filters))
                    <button type="submit" name="export" value="csv" class="btn btn-outline-success rounded-pill px-3" data-bs-toggle="tooltip" title="Export CSV">
                        <i class="fas fa-file-csv"></i>
                    </button>
                @endif
            </div>
        </div>
    </div>
</form>
