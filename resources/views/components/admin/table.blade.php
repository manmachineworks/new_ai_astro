@props([
    'columns' => [],
    'rows' => []
])
<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table align-middle mb-0 table-hover">
                <thead class="bg-light">
                    <tr>
                        @isset($header_extra)
                            {{ $header_extra }}
                        @endisset
                        @foreach($columns as $column)
                            <th class="{{ $loop->first ? 'ps-4' : '' }} {{ $loop->last ? 'text-end pe-4' : '' }} text-uppercase fs-7 text-secondary" style="font-weight: 600;">
                                {{ $column }}
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    {{ $slot }}
                </tbody>
            </table>
        </div>
    </div>
    @if(method_exists($rows, 'hasPages') && $rows->hasPages())
        <div class="card-footer bg-white border-0 py-3">
            {{ $rows->appends(request()->query())->links() }}
        </div>
    @endif
</div>
