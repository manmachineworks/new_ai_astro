@extends('layouts.user')

@section('header')
    <x-ui.page-header title="Settings" description="Manage your preferences and account security." />
@endsection

@section('content')
    <div class="mx-auto" style="max-width: 800px;">
        <div class="d-flex flex-column gap-4">
            {{-- Notifications Settings --}}
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h5 class="card-title fw-bold text-dark mb-4">Notifications</h5>
                    <div class="d-flex flex-column gap-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h6 class="mb-1 fw-bold text-dark">Push Notifications</h6>
                                <p class="mb-0 text-muted small">Receive alerts on your device for calls and chats.</p>
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input fs-4" type="checkbox" role="switch" id="pushNotifications"
                                    checked>
                            </div>
                        </div>

                        <hr class="my-1">

                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h6 class="mb-1 fw-bold text-dark">Email Updates</h6>
                                <p class="mb-0 text-muted small">Receive weekly horoscopes and offers.</p>
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input fs-4" type="checkbox" role="switch" id="emailUpdates"
                                    checked>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Language --}}
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h5 class="card-title fw-bold text-dark mb-4">Language & Region</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <label class="form-label small fw-medium text-muted">App Language</label>
                            <select class="form-select">
                                <option value="en" selected>English</option>
                                <option value="hi">Hindi</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Danger Zone --}}
            <div class="card border-danger bg-danger bg-opacity-10 shadow-sm">
                <div class="card-body p-4">
                    <h5 class="card-title fw-bold text-danger mb-2">Danger Zone</h5>
                    <p class="text-danger small mb-4 opacity-75">
                        Once you delete your account, there is no going back. All your data and wallet balance will be
                        permanently removed. Please be certain.
                    </p>
                    <button class="btn btn-danger px-4 shadow-sm">
                        Delete Account
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection