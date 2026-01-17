@extends('admin.layouts.app')

@section('title', 'Astrologer Performance')

@section('content')
    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold m-0">Astrologer Performance</h2>
            <div>
                <a href="{{ route('admin.reports.dashboard') }}" class="btn btn-light rounded-pill px-4 ms-2">Back to
                    Dashboard</a>
            </div>
        </div>

        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-0">
                <table class="table align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">Astrologer</th>
                            <th>Calls</th>
                            <th>Chats</th>
                            <th class="pe-4 text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($profiles as $profile)
                            <tr>
                                <td class="ps-4">
                                    <div class="fw-bold">{{ $profile->user->name }}</div>
                                    <div class="small text-muted">ID: {{ $profile->id }}</div>
                                </td>
                                <td>{{ $profile->calls_count }}</td>
                                <td>{{ $profile->chats_count }}</td>
                                <td class="pe-4 text-end">
                                    <a href="{{ route('admin.astrologers.show', $profile->id) }}"
                                        class="btn btn-sm btn-light rounded-pill">View Profile</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection