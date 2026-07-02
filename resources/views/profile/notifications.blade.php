@extends('layouts.master')

@section('title', 'Notification Settings')

@section('content')
<div class="container-fluid px-2">
    
    <x-breadcrumb :items="[
        ['name' => 'Profile', 'url' => '#'],
        ['name' => 'Notifications', 'url' => '#']
    ]" />
    
    <x-page-header title="NOTIFICATION SETTINGS" description="Configure how you receive notifications">
    </x-page-header>
    
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show py-1 px-2 mb-2 rounded-sm" role="alert">
        <i class="ti ti-check-circle me-1"></i> {{ session('success') }}
        <button type="button" class="btn-close py-0" data-bs-dismiss="alert"></button>
    </div>
    @endif
    
    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show py-1 px-2 mb-2 rounded-sm" role="alert">
        <i class="ti ti-alert-circle me-1"></i> {{ session('error') }}
        <button type="button" class="btn-close py-0" data-bs-dismiss="alert"></button>
    </div>
    @endif
    
    <div class="row">
        <div class="col-md-6">
            <div class="card rounded-sm">
                <div class="card-header py-1 px-2 bg-light">
                    <i class="ti ti-bell me-1"></i>
                    <span style="font-weight: 600;">Notification Preferences</span>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('profile.notifications.update') }}">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input type="hidden" name="email_notifications" value="0">
                                <input type="checkbox" class="form-check-input" name="email_notifications" value="1" id="emailNotifications" 
                                    {{ ($emailNotifications && $emailNotifications->value == 'true') ? 'checked' : '' }}>
                                <label class="form-check-label" for="emailNotifications">
                                    Email Notifications
                                </label>
                            </div>
                            <small class="text-muted">Receive email notifications for leave approvals and updates</small>
                        </div>
                        
                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input type="hidden" name="browser_notifications" value="0">
                                <input type="checkbox" class="form-check-input" name="browser_notifications" value="1" id="browserNotifications"
                                    {{ ($browserNotifications && $browserNotifications->value == 'true') ? 'checked' : '' }}>
                                <label class="form-check-label" for="browserNotifications">
                                    Browser Notifications
                                </label>
                            </div>
                            <small class="text-muted">Receive browser popup notifications</small>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Reminder Days Before Leave</label>
                            <select name="reminder_days" class="form-select form-select-sm">
                                <option value="0" {{ ($reminderDays && $reminderDays->value == '0') ? 'selected' : '' }}>No reminders</option>
                                <option value="1" {{ ($reminderDays && $reminderDays->value == '1') ? 'selected' : '' }}>1 day before</option>
                                <option value="3" {{ ($reminderDays && $reminderDays->value == '3') ? 'selected' : '' }}>3 days before</option>
                                <option value="7" {{ ($reminderDays && $reminderDays->value == '7') ? 'selected' : '' }}>7 days before</option>
                                <option value="14" {{ ($reminderDays && $reminderDays->value == '14') ? 'selected' : '' }}>14 days before</option>
                            </select>
                            <small class="text-muted">Get reminded before your leave starts</small>
                        </div>
                        
                        <button type="submit" class="btn btn-primary btn-sm rounded-sm">
                            <i class="ti ti-device-floppy me-1"></i> Save Notification Settings
                        </button>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card rounded-sm">
                <div class="card-header py-1 px-2 bg-light">
                    <i class="ti ti-bell-ringing me-1"></i>
                    <span style="font-weight: 600;">Recent Notifications</span>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @php
                            // Get notifications from your custom notifications table
                            $recentNotifications = \App\Models\Notification::where('user_id', auth()->id())
                                ->orderBy('created_at', 'desc')
                                ->limit(5)
                                ->get();
                        @endphp
                        
                        @forelse($recentNotifications as $notification)
                        <div class="list-group-item py-2 px-2 {{ $notification->is_read ? '' : 'bg-light' }}">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <strong style="font-size: 0.7rem;">{{ $notification->title }}</strong>
                                    <p class="mb-0 text-muted" style="font-size: 0.65rem;">{{ Str::limit($notification->message, 100) }}</p>
                                </div>
                                <small class="text-muted" style="font-size: 0.6rem;">{{ $notification->created_at->diffForHumans() }}</small>
                            </div>
                        </div>
                        @empty
                        <div class="list-group-item py-3 text-center text-muted">
                            <i class="ti ti-bell-off fs-4"></i>
                            <p class="mb-0">No notifications yet</p>
                        </div>
                        @endforelse
                    </div>
                </div>
                @if(\App\Models\Notification::where('user_id', auth()->id())->count() > 5)
                <div class="card-footer py-1 px-2 text-center">
                    <a href="{{ route('notifications.index') }}" class="btn btn-sm btn-link" style="font-size: 0.7rem;">
                        View All Notifications
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection