@extends('layouts.user')

@section('header')
    <x-ui.page-header title="Call History" description="View details of your past consultations." :breadcrumbs="[['label' => 'Dashboard', 'url' => route('user.dashboard')], ['label' => 'Call History']]" />
@endsection

@section('content')
    <div class="card border-0 shadow-sm overflow-hidden">
        <div class="card-body p-0">
            <x-user.call-history-table :calls="$calls" />
        </div>

        <div class="card-footer bg-white border-top">
            {{-- {{ $calls->links() }} --}}
        </div>
    </div>
@endsection