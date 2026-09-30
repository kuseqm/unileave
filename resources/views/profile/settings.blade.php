@extends('layouts.master')

@section('title', 'Profile Settings')

@section('content')
<div class="container-fluid px-2">
    
    <x-breadcrumb :items="[
        ['name' => 'Profile', 'url' => '#'],
        ['name' => 'Settings', 'url' => '#']
    ]" />
    
    <x-page-header title="PROFILE SETTINGS" description="Customize your account preferences">
    </x-page-header>
    
    <x-alert-success />
    
    <div class="row">
        <div class="col-md-6">
            <div class="card rounded-sm">
                <div class="card-header py-1 px-2 bg-light">
                    <i class="ti ti-user me-1"></i>
                    <span style="font-weight: 600;">Account Information</span>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('profile.settings.update') }}">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-3">
                            <label class="form-label">Name</label>
                            <input type="text" class="form-control form-control-sm" value="{{ $user->full_name }}" disabled>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control form-control-sm" value="{{ $user->email }}" disabled>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Staff ID</label>
                            <input type="text" class="form-control form-control-sm" value="{{ $user->staff_id ?? 'N/A' }}" disabled>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Preferred Language</label>
                            <select name="language" class="form-select form-select-sm">
                                <option value="en" {{ $language && $language->value == 'en' ? 'selected' : '' }}>English</option>
                                <option value="sw" {{ $language && $language->value == 'sw' ? 'selected' : '' }}>Swahili</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Timezone</label>
                            <select name="timezone" class="form-select form-select-sm">
                                <option value="Africa/Dar_es_Salaam" {{ $timezone && $timezone->value == 'Africa/Dar_es_Salaam' ? 'selected' : '' }}>Dar es Salaam (EAT)</option>
                                <option value="Africa/Nairobi" {{ $timezone && $timezone->value == 'Africa/Nairobi' ? 'selected' : '' }}>Nairobi (EAT)</option>
                                <option value="UTC" {{ $timezone && $timezone->value == 'UTC' ? 'selected' : '' }}>UTC</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Clock Format</label>
                            <select name="clock_format" class="form-select form-select-sm">
                                <option value="24" {{ ! $clockFormat || $clockFormat->value == '24' ? 'selected' : '' }}>24-hour (14:30:00)</option>
                                <option value="12" {{ $clockFormat && $clockFormat->value == '12' ? 'selected' : '' }}>12-hour (2:30:00 PM)</option>
                            </select>
                            <div class="form-text" style="font-size: 0.7rem;">Controls the live clock shown in the top bar.</div>
                        </div>

                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" role="switch" name="show_hebrew_date" value="1" id="show_hebrew_date" {{ $showHebrewDate && $showHebrewDate->value == 'true' ? 'checked' : '' }}>
                            <label class="form-check-label" for="show_hebrew_date">Also show the Hebrew calendar date</label>
                            <div class="form-text" style="font-size: 0.7rem;">Shown alongside the Gregorian date in the top bar, below the clock.</div>
                        </div>

                        <button type="submit" class="btn btn-primary btn-sm rounded-sm">Save Settings</button>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card rounded-sm">
                <div class="card-header py-1 px-2 bg-light">
                    <i class="ti ti-shield me-1"></i>
                    <span style="font-weight: 600;">Password</span>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('profile.password.update') }}">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-3">
                            <label class="form-label">Current Password</label>
                            <input type="password" name="current_password" class="form-control form-control-sm" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">New Password</label>
                            <input type="password" name="password" class="form-control form-control-sm" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Confirm Password</label>
                            <input type="password" name="password_confirmation" class="form-control form-control-sm" required>
                        </div>
                        
                        <button type="submit" class="btn btn-warning btn-sm rounded-sm">Change Password</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection