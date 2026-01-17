@props(['filters' => [], 'action' => null])

<div class="card mb-4">
    <div class="card-body py-3">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div class="d-flex gap-2 align-items-center flex-wrap">
                {{ $slot }}

                @foreach($filters as $key => $filter)
                    @if(is_array($filter) && isset($filter['options']))
                        <div class="dropdown">
                            <button class="btn btn-outline-secondary dropdown-toggle btn-sm" type="button" data-bs-toggle="dropdown">
                                {{ $filter['label'] }}: {{ request($key) ? ucfirst(request($key)) : 'All' }}
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery([$key => null]) }}">All</a></li>
                                @foreach($filter['options'] as $optionKey => $optionLabel)
                                    <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery([$key => $optionKey]) }}">{{ $optionLabel }}</a></li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                @endforeach
            </div>
            
            <div class="search-box">
                <form action="{{ $action ?? url()->current() }}" method="GET" class="d-flex gap-2">
                    @foreach(request()->except(['search', 'page']) as $key => $value)
                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                    @endforeach
                    <input type="text" name="search" class="form-control form-control-sm" placeholder="Search..." value="{{ request('search') }}">
                    <button type="submit" class="btn btn-sm btn-light border"><i class="fas fa-search"></i></button>
                </form>
            </div>
        </div>
    </div>
</div>