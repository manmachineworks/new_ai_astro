@extends('layouts.user')

@section('header')
    <x-ui.page-header title="My Appointments" description="Manage your scheduled sessions." />
@endsection

@section('content')
    <x-ui.tabs :tabs="['upcoming' => 'Upcoming', 'past' => 'Past']" active="upcoming">
        {{-- Upcoming Tab --}}
        <div x-show="activeTab === 'upcoming'">
            <div class="row g-4">
                @forelse($upcomingAppointments as $appointment)
                    <div class="col-12">
                        <x-user.appointment-card :appointment="$appointment" />
                    </div>
                @empty
                    <div class="col-12">
                        <div class="card border-0 shadow-sm p-5 text-center">
                            <x-ui.empty-state title="No upcoming appointments"
                                description="Book a session with our expert astrologers."
                                action='<a href="{{ route("user.astrologers.index") }}" class="btn btn-primary px-4 mt-3 shadow-sm">Book Now</a>' />
                        </div>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Past Tab --}}
        <div x-show="activeTab === 'past'" style="display: none;">
            <div class="row g-4">
                @forelse($pastAppointments as $appointment)
                    <div class="col-12">
                        <x-user.appointment-card :appointment="$appointment" />
                    </div>
                @empty
                    <div class="col-12">
                        <div class="card border-0 shadow-sm p-5 text-center">
                            <x-ui.empty-state title="No past appointments"
                                description="Your completed sessions will appear here." />
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </x-ui.tabs>
@endsection