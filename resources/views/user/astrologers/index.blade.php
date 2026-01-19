@extends('layouts.user')

@section('header')
    <x-ui.page-header title="Browse Astrologers" description="Connect with our expert astrologers for guidance." />
@endsection

@section('content')

    {{-- Filters --}}
    <x-user.filters-bar />

    {{-- Astrologers Grid --}}
    <div class="row g-4">
        @forelse($astrologers as $astrologer)
            <div class="col-md-6 col-lg-4">
                <x-user.astrologer-card :astrologer="$astrologer" />
            </div>
        @empty
            <div class="col-12">
                <x-ui.empty-state title="No Astrologers Found" description="Try adjusting your filters or search criteria."
                    action='<a href="{{ route("user.astrologers.index") }}" class="btn btn-primary">Clear Filters</a>' />
            </div>
        @endforelse
    </div>

    {{-- Pagination (Placeholder) --}}
    <div class="mt-8">
        {{-- {{ $astrologers->links() }} --}}
    </div>

@endsection