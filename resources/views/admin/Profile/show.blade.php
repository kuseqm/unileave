@extends('layouts.master')

@section('title', 'My Profile')

@section('content')
<div class="container-fluid px-2">
    
    <x-breadcrumb :items="[
        ['name' => 'Dashboard', 'url' => route('dashboard')],
        ['name' => 'My Profile', 'url' => '#']
    ]" />
    
    <x-page-header title="MY PROFILE" description="Manage your personal and professional information">
    </x-page-header>
    
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show py-1 px-2 mb-2 rounded-sm" style="font-size: 0.7rem;" role="alert">
        <i class="ti ti-check-circle me-1"></i> {{ session('success') }}
        <button type="button" class="btn-close py-0" style="font-size: 0.6rem;" data-bs-dismiss="alert"></button>
    </div>
    @endif
    
    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show py-1 px-2 mb-2 rounded-sm" style="font-size: 0.7rem;" role="alert">
        <i class="ti ti-alert-circle me-1"></i> {{ session('error') }}
        <button type="button" class="btn-close py-0" style="font-size: 0.6rem;" data-bs-dismiss="alert"></button>
    </div>
    @endif
    
    <div class="row">
        <!-- Left Column - Profile Picture & Stats -->
        <div class="col-md-3">
            <!-- Profile Card -->
            <div class="card rounded-sm mb-2">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-user text-primary me-1" style="font-size: 0.8rem;"></i>
                        <span style="font-size: 0.7rem; font-weight: 600;">Profile Picture</span>
                    </div>
                </div>
                <div class="card-body text-center py-3">
                    <div class="user-avatar-large mx-auto mb-2" style="width: 100px; height: 100px; background: #EFF6FF; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto;">
                        @if(auth()->user()->avatar)
                            <img src="{{ Storage::url(auth()->user()->avatar) }}" style="width: 100px; height: 100px; border-radius: 50%; object-fit: cover;">
                        @else
                            <span style="font-size: 2rem; font-weight: 600; color: #3B82F6;">
                                {{ strtoupper(substr(auth()->user()->first_name, 0, 1)) }}
                                @if(auth()->user()->middle_name)
                                    {{ strtoupper(substr(auth()->user()->middle_name, 0, 1)) }}
                                @endif
                                {{ strtoupper(substr(auth()->user()->last_name, 0, 1)) }}
                            </span>
                        @endif
                    </div>
                    
                    <form action="{{ route('profile.avatar.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="file" name="avatar" id="avatar" class="d-none" accept="image/*">
                        <button type="button" class="btn btn-outline-primary btn-sm rounded-sm mt-2" onclick="document.getElementById('avatar').click()" style="font-size: 0.65rem;">
                            <i class="ti ti-camera me-1"></i> Change Photo
                        </button>
                    </form>
                    
                    <hr class="my-2">
                    
                    <!-- Full Name Display -->
                    <div class="text-start">
                        <div class="mb-2 text-center">
                            <span style="font-size: 1rem; font-weight: 700; color: #1E3A5F;">{{ auth()->user()->first_name }} {{ auth()->user()->middle_name ? auth()->user()->middle_name . ' ' : '' }}<span style="color: #3B82F6;">{{ strtoupper(auth()->user()->last_name) }}</span></span>
                        </div>
                        <div class="mb-1" style="font-size: 0.65rem;">
                            <i class="ti ti-id me-1"></i> <strong>Staff ID:</strong> {{ auth()->user()->staff_id }}
                        </div>
                        <div class="mb-1" style="font-size: 0.65rem;">
                            <i class="ti ti-building me-1"></i> <strong>Department:</strong> {{ auth()->user()->department->name ?? 'N/A' }}
                        </div>
                        <div class="mb-1" style="font-size: 0.65rem;">
                            <i class="ti ti-phone me-1"></i> <strong>Phone:</strong> 
                            @php
                                $primaryPhone = auth()->user()->phones()->where('is_primary', true)->first();
                                $phoneNumber = $primaryPhone ? $primaryPhone->phone_number : (auth()->user()->phone ?? null);
                            @endphp
                            @if($phoneNumber)
                                {{ $phoneNumber }}
                            @else
                                <span class="text-muted">Not provided</span>
                                <a href="#" onclick="document.querySelector('#basic-tab').click(); return false;" class="text-primary" style="font-size: 0.6rem;">Add</a>
                            @endif
                        </div>
                        <div style="font-size: 0.65rem;">
                            <i class="ti ti-mail me-1"></i> <strong>Email:</strong> 
                            @php
                                $primaryEmail = auth()->user()->emails()->where('is_primary', true)->first();
                                $emailAddress = $primaryEmail ? $primaryEmail->email : (auth()->user()->email ?? null);
                            @endphp
                            @if($emailAddress)
                                {{ $emailAddress }}
                            @else
                                <span class="text-muted">Not provided</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Statistics Card -->
            <div class="card rounded-sm">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-chart-bar text-primary me-1" style="font-size: 0.8rem;"></i>
                        <span style="font-size: 0.7rem; font-weight: 600;">Leave Statistics</span>
                    </div>
                </div>
                <div class="card-body py-2 px-2">
                    <div class="row g-1">
                        <div class="col-6 text-center">
                            <div class="text-muted" style="font-size: 0.6rem;">Total</div>
                            <div class="fw-bold" style="font-size: 1rem;">{{ $totalLeaveRequests ?? 0 }}</div>
                        </div>
                        <div class="col-6 text-center">
                            <div class="text-muted" style="font-size: 0.6rem;">Pending</div>
                            <div class="fw-bold text-warning" style="font-size: 1rem;">{{ $pendingLeaveRequests ?? 0 }}</div>
                        </div>
                        <div class="col-6 text-center">
                            <div class="text-muted" style="font-size: 0.6rem;">Approved</div>
                            <div class="fw-bold text-success" style="font-size: 1rem;">{{ $approvedLeaveRequests ?? 0 }}</div>
                        </div>
                        <div class="col-6 text-center">
                            <div class="text-muted" style="font-size: 0.6rem;">Rejected</div>
                            <div class="fw-bold text-danger" style="font-size: 1rem;">{{ $rejectedLeaveRequests ?? 0 }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Right Column - Tabs -->
        <div class="col-md-9">
            <div class="card rounded-sm">
                <div class="card-header py-1 px-2 bg-light">
                    <ul class="nav nav-tabs card-header-tabs" id="profileTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="basic-tab" data-bs-toggle="tab" data-bs-target="#basic" type="button" role="tab" style="font-size: 0.7rem; padding: 0.25rem 0.75rem;">
                                <i class="ti ti-user me-1"></i> Basic Info
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="contact-tab" data-bs-toggle="tab" data-bs-target="#contact" type="button" role="tab" style="font-size: 0.7rem; padding: 0.25rem 0.75rem;">
                                <i class="ti ti-phone me-1"></i> Contact
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="dependents-tab" data-bs-toggle="tab" data-bs-target="#dependents" type="button" role="tab" style="font-size: 0.7rem; padding: 0.25rem 0.75rem;">
                                <i class="ti ti-users me-1"></i> Dependents
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="addresses-tab" data-bs-toggle="tab" data-bs-target="#addresses" type="button" role="tab" style="font-size: 0.7rem; padding: 0.25rem 0.75rem;">
                                <i class="ti ti-home me-1"></i> Addresses
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="bank-tab" data-bs-toggle="tab" data-bs-target="#bank" type="button" role="tab" style="font-size: 0.7rem; padding: 0.25rem 0.75rem;">
                                <i class="ti ti-credit-card me-1"></i> Bank Details
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="emergency-tab" data-bs-toggle="tab" data-bs-target="#emergency" type="button" role="tab" style="font-size: 0.7rem; padding: 0.25rem 0.75rem;">
                                <i class="ti ti-phone-call me-1"></i> Emergency
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="professional-tab" data-bs-toggle="tab" data-bs-target="#professional" type="button" role="tab" style="font-size: 0.7rem; padding: 0.25rem 0.75rem;">
                                <i class="ti ti-briefcase me-1"></i> Professional
                            </button>
                        </li>
                    </ul>
                </div>
                <div class="card-body p-2">
                    <div class="tab-content">
                        
                        <!-- ============================================ -->
                        <!-- TAB 1: BASIC INFORMATION -->
                        <!-- ============================================ -->
                        <div class="tab-pane fade show active" id="basic" role="tabpanel">
                            <form method="POST" action="{{ route('profile.update') }}">
                                @csrf
                                @method('PUT')
                                
                                <div class="row g-2">
                                    <div class="col-md-2">
                                        <label class="form-label text-muted" style="font-size: 0.65rem;">Title</label>
                                        <select name="title" class="form-select form-select-sm rounded-sm" style="font-size: 0.75rem;">
                                            <option value="">Select</option>
                                            <option value="Mr" {{ old('title', auth()->user()->title) == 'Mr' ? 'selected' : '' }}>Mr</option>
                                            <option value="Mrs" {{ old('title', auth()->user()->title) == 'Mrs' ? 'selected' : '' }}>Mrs</option>
                                            <option value="Ms" {{ old('title', auth()->user()->title) == 'Ms' ? 'selected' : '' }}>Ms</option>
                                            <option value="Dr" {{ old('title', auth()->user()->title) == 'Dr' ? 'selected' : '' }}>Dr</option>
                                            <option value="Prof" {{ old('title', auth()->user()->title) == 'Prof' ? 'selected' : '' }}>Prof</option>
                                            <option value="Eng" {{ old('title', auth()->user()->title) == 'Eng' ? 'selected' : '' }}>Eng</option>
                                            <option value="Rev" {{ old('title', auth()->user()->title) == 'Rev' ? 'selected' : '' }}>Rev</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label text-muted" style="font-size: 0.65rem;">Gender</label>
                                        <select name="gender" class="form-select form-select-sm rounded-sm" style="font-size: 0.75rem;">
                                            <option value="">Select</option>
                                            <option value="male" {{ old('gender', auth()->user()->gender) == 'male' ? 'selected' : '' }}>Male</option>
                                            <option value="female" {{ old('gender', auth()->user()->gender) == 'female' ? 'selected' : '' }}>Female</option>
                                            <option value="other" {{ old('gender', auth()->user()->gender) == 'other' ? 'selected' : '' }}>Other</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label text-muted" style="font-size: 0.65rem;">Date of Birth</label>
                                        <input type="date" name="date_of_birth" class="form-control form-control-sm rounded-sm" 
                                            value="{{ old('date_of_birth', auth()->user()->date_of_birth ? auth()->user()->date_of_birth->format('Y-m-d') : '') }}" style="font-size: 0.75rem;">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label text-muted" style="font-size: 0.65rem;">National ID</label>
                                        <input type="text" name="national_id" class="form-control form-control-sm rounded-sm" 
                                            value="{{ old('national_id', auth()->user()->national_id) }}" style="font-size: 0.75rem;">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label text-muted" style="font-size: 0.65rem;">Staff ID (Read Only)</label>
                                        <input type="text" class="form-control form-control-sm rounded-sm" 
                                            value="{{ auth()->user()->staff_id }}" readonly style="font-size: 0.75rem; background:#f5f5f5;">
                                    </div>
                                </div>
                                
                                <div class="row g-2 mt-1">
                                    <div class="col-md-4">
                                        <label class="form-label text-muted" style="font-size: 0.65rem;">First Name (Read Only)</label>
                                        <input type="text" class="form-control form-control-sm rounded-sm" 
                                            value="{{ auth()->user()->first_name }}" readonly style="font-size: 0.75rem; background:#f5f5f5;">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label text-muted" style="font-size: 0.65rem;">Middle Name (Read Only)</label>
                                        <input type="text" class="form-control form-control-sm rounded-sm" 
                                            value="{{ auth()->user()->middle_name }}" readonly style="font-size: 0.75rem; background:#f5f5f5;">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label text-muted" style="font-size: 0.65rem;">Last Name (Read Only)</label>
                                        <input type="text" class="form-control form-control-sm rounded-sm" 
                                            value="{{ auth()->user()->last_name }}" readonly style="font-size: 0.75rem; background:#f5f5f5;">
                                    </div>
                                </div>
                                
                                <div class="row g-2 mt-1">
                                    <div class="col-md-6">
                                        <label class="form-label text-muted" style="font-size: 0.65rem;">Email Address</label>
                                        <input type="email" name="email" class="form-control form-control-sm rounded-sm" 
                                            value="{{ old('email', auth()->user()->email) }}" style="font-size: 0.75rem;">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label text-muted" style="font-size: 0.65rem;">Phone Number</label>
                                        <input type="text" name="phone" class="form-control form-control-sm rounded-sm" 
                                            value="{{ old('phone', auth()->user()->phone) }}" style="font-size: 0.75rem;">
                                    </div>
                                </div>
                                
                                <div class="row g-2 mt-1">
                                    <div class="col-md-6">
                                        <label class="form-label text-muted" style="font-size: 0.65rem;">Marital Status</label>
                                        <select name="marital_status" class="form-select form-select-sm rounded-sm" style="font-size: 0.75rem;">
                                            <option value="">Select</option>
                                            <option value="single" {{ old('marital_status', auth()->user()->marital_status) == 'single' ? 'selected' : '' }}>Single</option>
                                            <option value="married" {{ old('marital_status', auth()->user()->marital_status) == 'married' ? 'selected' : '' }}>Married</option>
                                            <option value="divorced" {{ old('marital_status', auth()->user()->marital_status) == 'divorced' ? 'selected' : '' }}>Divorced</option>
                                            <option value="widowed" {{ old('marital_status', auth()->user()->marital_status) == 'widowed' ? 'selected' : '' }}>Widowed</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label text-muted" style="font-size: 0.65rem;">Spouse Name</label>
                                        <input type="text" name="spouse_name" class="form-control form-control-sm rounded-sm" 
                                            value="{{ old('spouse_name', auth()->user()->spouse_name) }}" style="font-size: 0.75rem;" placeholder="If married">
                                    </div>
                                </div>
                                
                                <div class="mt-3">
                                    <button type="submit" class="btn btn-primary btn-sm rounded-sm" style="font-size: 0.7rem;">
                                        <i class="ti ti-save me-1"></i> Update Profile
                                    </button>
                                </div>
                            </form>
                            
                            <hr class="my-2">
                            
                            <!-- Change Password Form -->
                            <form method="POST" action="{{ route('profile.password.update') }}">
                                @csrf
                                @method('PUT')
                                <h6 style="font-size: 0.7rem;">Change Password</h6>
                                <div class="row g-2">
                                    <div class="col-md-4">
                                        <label class="form-label text-muted" style="font-size: 0.65rem;">Current Password</label>
                                        <input type="password" name="current_password" class="form-control form-control-sm rounded-sm" required style="font-size: 0.75rem;">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label text-muted" style="font-size: 0.65rem;">New Password</label>
                                        <input type="password" name="password" class="form-control form-control-sm rounded-sm" required style="font-size: 0.75rem;">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label text-muted" style="font-size: 0.65rem;">Confirm Password</label>
                                        <input type="password" name="password_confirmation" class="form-control form-control-sm rounded-sm" required style="font-size: 0.75rem;">
                                    </div>
                                </div>
                                <div class="mt-2">
                                    <button type="submit" class="btn btn-warning btn-sm rounded-sm" style="font-size: 0.7rem;">
                                        <i class="ti ti-key me-1"></i> Change Password
                                    </button>
                                </div>
                            </form>
                        </div>
                        
                        <!-- ============================================ -->
                        <!-- TAB 2: CONTACT (Phones & Emails) -->
                        <!-- ============================================ -->
                        <div class="tab-pane fade" id="contact" role="tabpanel">
                            <!-- Phone Numbers -->
                            <div class="card rounded-sm mb-2">
                                <div class="card-header py-1 px-2 bg-light d-flex justify-content-between align-items-center">
                                    <span style="font-size: 0.7rem; font-weight: 600;"><i class="ti ti-phone me-1"></i> Phone Numbers</span>
                                    <button type="button" class="btn btn-sm btn-outline-primary rounded-sm" data-bs-toggle="modal" data-bs-target="#addPhoneModal" style="font-size: 0.6rem; padding: 0.1rem 0.4rem;">
                                        <i class="ti ti-plus"></i> Add Phone
                                    </button>
                                </div>
                                <div class="card-body p-0">
                                    <table class="table table-sm mb-0" style="font-size: 0.7rem;">
                                        <thead class="bg-light">
                                            <tr>
                                                <th class="ps-2 py-1">Number</th>
                                                <th class="py-1">Type</th>
                                                <th class="py-1">Primary</th>
                                                <th class="pe-2 py-1">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse(auth()->user()->phones as $phone)
                                            <tr>
                                                <td class="ps-2 py-1">{{ $phone->phone_number }}</td>
                                                <td class="py-1">{{ ucfirst($phone->phone_type) }}</td>
                                                <td class="py-1">
                                                    @if($phone->is_primary)
                                                        <span class="badge bg-success-subtle text-success rounded-sm">Primary</span>
                                                    @endif
                                                </td>
                                                <td class="pe-2 py-1">
                                                    <button class="btn btn-sm btn-outline-danger rounded-sm" onclick="deletePhone({{ $phone->id }})" style="padding: 0.1rem 0.3rem;">
                                                        <i class="ti ti-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="4" class="text-center py-2 text-muted">No phone numbers added</td>
                                            </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            
                            <!-- Email Addresses -->
                            <div class="card rounded-sm">
                                <div class="card-header py-1 px-2 bg-light d-flex justify-content-between align-items-center">
                                    <span style="font-size: 0.7rem; font-weight: 600;"><i class="ti ti-mail me-1"></i> Email Addresses</span>
                                    <button type="button" class="btn btn-sm btn-outline-primary rounded-sm" data-bs-toggle="modal" data-bs-target="#addEmailModal" style="font-size: 0.6rem; padding: 0.1rem 0.4rem;">
                                        <i class="ti ti-plus"></i> Add Email
                                    </button>
                                </div>
                                <div class="card-body p-0">
                                    <table class="table table-sm mb-0" style="font-size: 0.7rem;">
                                        <thead class="bg-light">
                                            <tr>
                                                <th class="ps-2 py-1">Email</th>
                                                <th class="py-1">Type</th>
                                                <th class="py-1">Primary</th>
                                                <th class="pe-2 py-1">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse(auth()->user()->emails as $email)
                                            <tr>
                                                <td class="ps-2 py-1">{{ $email->email }}</td>
                                                <td class="py-1">{{ ucfirst($email->email_type) }}</td>
                                                <td class="py-1">
                                                    @if($email->is_primary)
                                                        <span class="badge bg-success-subtle text-success rounded-sm">Primary</span>
                                                    @endif
                                                </td>
                                                <td class="pe-2 py-1">
                                                    <button class="btn btn-sm btn-outline-danger rounded-sm" onclick="deleteEmail({{ $email->id }})" style="padding: 0.1rem 0.3rem;">
                                                        <i class="ti ti-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="4" class="text-center py-2 text-muted">No email addresses added</td>
                                            </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        
                        <!-- ============================================ -->
                        <!-- TAB 3: DEPENDENTS -->
                        <!-- ============================================ -->
                        <div class="tab-pane fade" id="dependents" role="tabpanel">
                            <div class="d-flex justify-content-end mb-2">
                                <button type="button" class="btn btn-primary btn-sm rounded-sm" data-bs-toggle="modal" data-bs-target="#addDependentModal" style="font-size: 0.65rem;">
                                    <i class="ti ti-plus me-1"></i> Add Dependent
                                </button>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr style="font-size: 0.65rem;">
                                            <th>Name</th>
                                            <th>Relationship</th>
                                            <th>Date of Birth</th>
                                            <th>Phone</th>
                                            <th>Emergency Contact</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse(auth()->user()->dependents ?? [] as $dependent)
                                        <tr style="font-size: 0.65rem;">
                                            <td>{{ $dependent->name }}</td>
                                            <td>{{ ucfirst($dependent->relationship) }}</td>
                                            <td>{{ $dependent->date_of_birth ? \Carbon\Carbon::parse($dependent->date_of_birth)->format('d M Y') : '-' }}</td>
                                            <td>{{ $dependent->phone ?? '-' }}</td>
                                            <td>
                                                @if($dependent->is_emergency_contact)
                                                    <span class="badge bg-success-subtle text-success rounded-sm" style="font-size: 0.55rem;">Yes</span>
                                                @else
                                                    <span class="badge bg-secondary-subtle text-secondary rounded-sm" style="font-size: 0.55rem;">No</span>
                                                @endif
                                            </td>
                                            <td>
                                                <button class="btn btn-sm btn-outline-danger rounded-sm" onclick="deleteDependent({{ $dependent->id }})" style="padding: 0.1rem 0.3rem; font-size: 0.6rem;">
                                                    <i class="ti ti-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="6" class="text-center text-muted" style="font-size: 0.65rem;">No dependents added yet</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        
                        <!-- ============================================ -->
                        <!-- TAB 4: ADDRESSES -->
                        <!-- ============================================ -->
                        <div class="tab-pane fade" id="addresses" role="tabpanel">
                            <div class="d-flex justify-content-end mb-2">
                                <button type="button" class="btn btn-primary btn-sm rounded-sm" data-bs-toggle="modal" data-bs-target="#addAddressModal" style="font-size: 0.65rem;">
                                    <i class="ti ti-plus me-1"></i> Add Address
                                </button>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr style="font-size: 0.65rem;">
                                            <th>Type</th>
                                            <th>Address</th>
                                            <th>City</th>
                                            <th>Country</th>
                                            <th>Primary</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse(auth()->user()->addresses ?? [] as $address)
                                        <tr style="font-size: 0.65rem;">
                                            <td>{{ ucfirst($address->address_type) }}</td>
                                            <td>{{ $address->address ?? '-' }}</td>
                                            <td>{{ $address->city ?? '-' }}</td>
                                            <td>{{ $address->country ?? 'Tanzania' }}</td>
                                            <td>
                                                @if($address->is_primary)
                                                    <span class="badge bg-success-subtle text-success rounded-sm" style="font-size: 0.55rem;">Yes</span>
                                                @endif
                                            </td>
                                            <td>
                                                <button class="btn btn-sm btn-outline-danger rounded-sm" onclick="deleteAddress({{ $address->id }})" style="padding: 0.1rem 0.3rem; font-size: 0.6rem;">
                                                    <i class="ti ti-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="6" class="text-center text-muted" style="font-size: 0.65rem;">No addresses added yet</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        
                        <!-- ============================================ -->
                        <!-- TAB 5: BANK DETAILS -->
                        <!-- ============================================ -->
                        <div class="tab-pane fade" id="bank" role="tabpanel">
                            @php $bank = auth()->user()->bankDetails; @endphp
                            <form method="POST" action="{{ route('profile.bank.update') }}">
                                @csrf
                                @method('PUT')
                                <div class="row g-2">
                                    <div class="col-md-6">
                                        <label class="form-label text-muted" style="font-size: 0.65rem;">Bank Name</label>
                                        <input type="text" name="bank_name" class="form-control form-control-sm rounded-sm" 
                                            value="{{ old('bank_name', $bank->bank_name ?? '') }}" style="font-size: 0.75rem;">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label text-muted" style="font-size: 0.65rem;">Account Name</label>
                                        <input type="text" name="account_name" class="form-control form-control-sm rounded-sm" 
                                            value="{{ old('account_name', $bank->account_name ?? '') }}" style="font-size: 0.75rem;">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label text-muted" style="font-size: 0.65rem;">Account Number</label>
                                        <input type="text" name="account_number" class="form-control form-control-sm rounded-sm" 
                                            value="{{ old('account_number', $bank->account_number ?? '') }}" style="font-size: 0.75rem;">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label text-muted" style="font-size: 0.65rem;">TIN Number</label>
                                        <input type="text" name="tin_number" class="form-control form-control-sm rounded-sm" 
                                            value="{{ old('tin_number', auth()->user()->tin_number ?? '') }}" style="font-size: 0.75rem;">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label text-muted" style="font-size: 0.65rem;">NSSF Number</label>
                                        <input type="text" name="nssf_number" class="form-control form-control-sm rounded-sm" 
                                            value="{{ old('nssf_number', auth()->user()->nssf_number ?? '') }}" style="font-size: 0.75rem;">
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <button type="submit" class="btn btn-primary btn-sm rounded-sm" style="font-size: 0.7rem;">
                                        <i class="ti ti-save me-1"></i> Save Bank Details
                                    </button>
                                </div>
                            </form>
                        </div>
                        
                        <!-- ============================================ -->
                        <!-- TAB 6: EMERGENCY CONTACTS -->
                        <!-- ============================================ -->
                        <div class="tab-pane fade" id="emergency" role="tabpanel">
                            <div class="d-flex justify-content-end mb-2">
                                <button type="button" class="btn btn-primary btn-sm rounded-sm" data-bs-toggle="modal" data-bs-target="#addEmergencyModal" style="font-size: 0.65rem;">
                                    <i class="ti ti-plus me-1"></i> Add Emergency Contact
                                </button>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr style="font-size: 0.65rem;">
                                            <th>Name</th>
                                            <th>Relationship</th>
                                            <th>Primary Phone</th>
                                            <th>Alternative Phone</th>
                                            <th>Primary</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse(auth()->user()->emergencyContacts ?? [] as $contact)
                                        <tr style="font-size: 0.65rem;">
                                            <td>{{ $contact->name }}</td>
                                            <td>{{ $contact->relationship }}</td>
                                            <td>{{ $contact->phone }}</td>
                                            <td>{{ $contact->alternative_phone ?? '-' }}</td>
                                            <td>
                                                @if($contact->is_primary)
                                                    <span class="badge bg-success-subtle text-success rounded-sm" style="font-size: 0.55rem;">Primary</span>
                                                @else
                                                    <span class="badge bg-secondary-subtle text-secondary rounded-sm" style="font-size: 0.55rem;">Secondary</span>
                                                @endif
                                            </td>
                                            <td>
                                                <button class="btn btn-sm btn-outline-danger rounded-sm" onclick="deleteEmergency({{ $contact->id }})" style="padding: 0.1rem 0.3rem; font-size: 0.6rem;">
                                                    <i class="ti ti-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="6" class="text-center text-muted" style="font-size: 0.65rem;">No emergency contacts added yet</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        
                        <!-- ============================================ -->
                        <!-- TAB 7: PROFESSIONAL (Qualifications, Skills, Languages) -->
                        <!-- ============================================ -->
                        <div class="tab-pane fade" id="professional" role="tabpanel">
                            <div class="row g-2">
                                
                                <!-- Employment Information -->
                                <div class="col-12">
                                    <div class="card rounded-sm mb-2">
                                        <div class="card-header py-1 px-2 bg-light">
                                            <span style="font-size: 0.7rem; font-weight: 600;">Employment Information</span>
                                        </div>
                                        <div class="card-body py-2 px-2">
                                            <div class="row g-1">
                                                <div class="col-md-4">
                                                    <label class="text-muted" style="font-size: 0.6rem;">Job Title</label>
                                                    <p class="mb-0" style="font-size: 0.75rem;">{{ auth()->user()->job_title ?? 'Not set' }}</p>
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="text-muted" style="font-size: 0.6rem;">Job Grade</label>
                                                    <p class="mb-0" style="font-size: 0.75rem;">{{ auth()->user()->job_grade ?? 'Not set' }}</p>
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="text-muted" style="font-size: 0.6rem;">Contract Type</label>
                                                    <p class="mb-0" style="font-size: 0.75rem;">{{ ucfirst(auth()->user()->contract_type ?? 'Not set') }}</p>
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="text-muted" style="font-size: 0.6rem;">Contract End Date</label>
                                                    <p class="mb-0" style="font-size: 0.75rem;">{{ auth()->user()->contract_end_date ? auth()->user()->contract_end_date->format('d M Y') : 'N/A' }}</p>
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="text-muted" style="font-size: 0.6rem;">Confirmation Date</label>
                                                    <p class="mb-0" style="font-size: 0.75rem;">{{ auth()->user()->confirmation_date ? auth()->user()->confirmation_date->format('d M Y') : 'N/A' }}</p>
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="text-muted" style="font-size: 0.6rem;">Reporting To</label>
                                                    <p class="mb-0" style="font-size: 0.75rem;">{{ auth()->user()->reportsTo->full_name ?? 'N/A' }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Qualifications -->
                                <div class="col-12">
                                    <div class="card rounded-sm mb-2">
                                        <div class="card-header py-1 px-2 bg-light d-flex justify-content-between align-items-center">
                                            <span style="font-size: 0.7rem; font-weight: 600;"><i class="ti ti-certificate me-1"></i> Qualifications</span>
                                            <button type="button" class="btn btn-sm btn-outline-primary rounded-sm" data-bs-toggle="modal" data-bs-target="#addQualificationModal" style="font-size: 0.6rem; padding: 0.1rem 0.4rem;">
                                                <i class="ti ti-plus"></i> Add
                                            </button>
                                        </div>
                                        <div class="card-body p-0">
                                            <div class="table-responsive">
                                                <table class="table table-sm mb-0" style="font-size: 0.7rem;">
                                                    <thead class="bg-light">
                                                        <tr>
                                                            <th class="ps-2 py-1">Qualification</th>
                                                            <th class="py-1">Institution</th>
                                                            <th class="py-1">Year</th>
                                                            <th class="py-1">Type</th>
                                                            <th class="pe-2 py-1">Actions</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @forelse(auth()->user()->qualifications ?? [] as $qual)
                                                        <tr>
                                                            <td class="ps-2 py-1">{{ $qual->qualification_name }}</td>
                                                            <td class="py-1">{{ $qual->institution ?? '-' }}</td>
                                                            <td class="py-1">{{ $qual->year_obtained ?? '-' }}</td>
                                                            <td class="py-1">{{ ucfirst($qual->qualification_type ?? 'N/A') }}</td>
                                                            <td class="pe-2 py-1">
                                                                <button class="btn btn-sm btn-outline-danger rounded-sm" onclick="deleteQualification({{ $qual->id }})" style="padding: 0.1rem 0.3rem;">
                                                                    <i class="ti ti-trash"></i>
                                                                </button>
                                                            </td>
                                                        </tr>
                                                        @empty
                                                        <tr>
                                                            <td colspan="5" class="text-center py-2 text-muted">No qualifications added</td>
                                                        </tr>
                                                        @endforelse
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Skills -->
                                <div class="col-12">
                                    <div class="card rounded-sm mb-2">
                                        <div class="card-header py-1 px-2 bg-light d-flex justify-content-between align-items-center">
                                            <span style="font-size: 0.7rem; font-weight: 600;"><i class="ti ti-code me-1"></i> Skills</span>
                                            <button type="button" class="btn btn-sm btn-outline-primary rounded-sm" data-bs-toggle="modal" data-bs-target="#addSkillModal" style="font-size: 0.6rem; padding: 0.1rem 0.4rem;">
                                                <i class="ti ti-plus"></i> Add
                                            </button>
                                        </div>
                                        <div class="card-body p-0">
                                            <div class="table-responsive">
                                                <table class="table table-sm mb-0" style="font-size: 0.7rem;">
                                                    <thead class="bg-light">
                                                        <tr>
                                                            <th class="ps-2 py-1">Skill</th>
                                                            <th class="py-1">Category</th>
                                                            <th class="py-1">Proficiency</th>
                                                            <th class="pe-2 py-1">Actions</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @forelse(auth()->user()->skills ?? [] as $skill)
                                                        <tr>
                                                            <td class="ps-2 py-1">{{ $skill->skill_name }}</td>
                                                            <td class="py-1">{{ ucfirst($skill->skill_category ?? 'Other') }}</td>
                                                            <td class="py-1">{{ ucfirst($skill->proficiency_level) }}</td>
                                                            <td class="pe-2 py-1">
                                                                <button class="btn btn-sm btn-outline-danger rounded-sm" onclick="deleteSkill({{ $skill->id }})" style="padding: 0.1rem 0.3rem;">
                                                                    <i class="ti ti-trash"></i>
                                                                </button>
                                                            </td>
                                                        </tr>
                                                        @empty
                                                        <tr>
                                                            <td colspan="4" class="text-center py-2 text-muted">No skills added</td>
                                                        </tr>
                                                        @endforelse
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Languages -->
                                <div class="col-12">
                                    <div class="card rounded-sm mb-2">
                                        <div class="card-header py-1 px-2 bg-light d-flex justify-content-between align-items-center">
                                            <span style="font-size: 0.7rem; font-weight: 600;"><i class="ti ti-language me-1"></i> Languages</span>
                                            <button type="button" class="btn btn-sm btn-outline-primary rounded-sm" data-bs-toggle="modal" data-bs-target="#addLanguageModal" style="font-size: 0.6rem; padding: 0.1rem 0.4rem;">
                                                <i class="ti ti-plus"></i> Add
                                            </button>
                                        </div>
                                        <div class="card-body p-0">
                                            <div class="table-responsive">
                                                <table class="table table-sm mb-0" style="font-size: 0.7rem;">
                                                    <thead class="bg-light">
                                                        <tr>
                                                            <th class="ps-2 py-1">Language</th>
                                                            <th class="py-1">Speak</th>
                                                            <th class="py-1">Read</th>
                                                            <th class="py-1">Write</th>
                                                            <th class="pe-2 py-1">Actions</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @forelse(auth()->user()->languages ?? [] as $lang)
                                                        <tr>
                                                            <td class="ps-2 py-1">{{ $lang->language_name }}</td>
                                                            <td class="py-1">{{ ucfirst($lang->proficiency_speak) }}</td>
                                                            <td class="py-1">{{ ucfirst($lang->proficiency_read) }}</td>
                                                            <td class="py-1">{{ ucfirst($lang->proficiency_write) }}</td>
                                                            <td class="pe-2 py-1">
                                                                <button class="btn btn-sm btn-outline-danger rounded-sm" onclick="deleteLanguage({{ $lang->id }})" style="padding: 0.1rem 0.3rem;">
                                                                    <i class="ti ti-trash"></i>
                                                                </button>
                                                            </td>
                                                        </tr>
                                                        @empty
                                                        <tr>
                                                            <td colspan="5" class="text-center py-2 text-muted">No languages added</td>
                                                        </tr>
                                                        @endforelse
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ============================================ -->
<!-- MODALS -->
<!-- ============================================ -->

<!-- Add Phone Modal -->
<div class="modal fade" id="addPhoneModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-sm">
            <div class="modal-header py-1 px-2">
                <h5 class="modal-title" style="font-size: 0.8rem;">Add Phone Number</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('profile.phones.store') }}">
                @csrf
                <div class="modal-body py-2 px-2">
                    <div class="mb-2">
                        <label class="form-label" style="font-size: 0.65rem;">Phone Number</label>
                        <input type="text" name="phone_number" class="form-control form-control-sm rounded-sm" required style="font-size: 0.7rem;">
                    </div>
                    <div class="mb-2">
                        <label class="form-label" style="font-size: 0.65rem;">Phone Type</label>
                        <select name="phone_type" class="form-select form-select-sm rounded-sm" style="font-size: 0.7rem;">
                            <option value="mobile">Mobile</option>
                            <option value="work">Work</option>
                            <option value="home">Home</option>
                            <option value="alternative">Alternative</option>
                            <option value="emergency">Emergency</option>
                        </select>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" name="is_primary" class="form-check-input" id="primaryPhone" value="1">
                        <label class="form-check-label" for="primaryPhone" style="font-size: 0.65rem;">Set as Primary Phone</label>
                    </div>
                </div>
                <div class="modal-footer py-1 px-2">
                    <button type="button" class="btn btn-secondary btn-sm rounded-sm" data-bs-dismiss="modal" style="font-size: 0.65rem;">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm rounded-sm" style="font-size: 0.65rem;">Add Phone</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add Email Modal -->
<div class="modal fade" id="addEmailModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-sm">
            <div class="modal-header py-1 px-2">
                <h5 class="modal-title" style="font-size: 0.8rem;">Add Email Address</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('profile.emails.store') }}">
                @csrf
                <div class="modal-body py-2 px-2">
                    <div class="mb-2">
                        <label class="form-label" style="font-size: 0.65rem;">Email Address</label>
                        <input type="email" name="email" class="form-control form-control-sm rounded-sm" required style="font-size: 0.7rem;">
                    </div>
                    <div class="mb-2">
                        <label class="form-label" style="font-size: 0.65rem;">Email Type</label>
                        <select name="email_type" class="form-select form-select-sm rounded-sm" style="font-size: 0.7rem;">
                            <option value="work">Work</option>
                            <option value="personal">Personal</option>
                            <option value="alternative">Alternative</option>
                        </select>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" name="is_primary" class="form-check-input" id="primaryEmail" value="1">
                        <label class="form-check-label" for="primaryEmail" style="font-size: 0.65rem;">Set as Primary Email</label>
                    </div>
                </div>
                <div class="modal-footer py-1 px-2">
                    <button type="button" class="btn btn-secondary btn-sm rounded-sm" data-bs-dismiss="modal" style="font-size: 0.65rem;">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm rounded-sm" style="font-size: 0.65rem;">Add Email</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add Dependent Modal -->
<div class="modal fade" id="addDependentModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-sm">
            <div class="modal-header py-1 px-2">
                <h5 class="modal-title" style="font-size: 0.8rem;">Add Dependent</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('profile.dependents.store') }}">
                @csrf
                <div class="modal-body py-2 px-2">
                    <div class="mb-2">
                        <label class="form-label" style="font-size: 0.65rem;">Full Name</label>
                        <input type="text" name="name" class="form-control form-control-sm rounded-sm" required style="font-size: 0.7rem;">
                    </div>
                    <div class="row g-2">
                        <div class="col-6">
                            <label class="form-label" style="font-size: 0.65rem;">Relationship</label>
                            <select name="relationship" class="form-select form-select-sm rounded-sm" style="font-size: 0.7rem;">
                                <option value="spouse">Spouse</option>
                                <option value="child">Child</option>
                                <option value="parent">Parent</option>
                                <option value="sibling">Sibling</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label" style="font-size: 0.65rem;">Date of Birth</label>
                            <input type="date" name="date_of_birth" class="form-control form-control-sm rounded-sm" style="font-size: 0.7rem;">
                        </div>
                    </div>
                    <div class="row g-2 mt-1">
                        <div class="col-6">
                            <label class="form-label" style="font-size: 0.65rem;">Phone Number</label>
                            <input type="text" name="phone" class="form-control form-control-sm rounded-sm" style="font-size: 0.7rem;">
                        </div>
                        <div class="col-6">
                            <label class="form-label" style="font-size: 0.65rem;">Email</label>
                            <input type="email" name="email" class="form-control form-control-sm rounded-sm" style="font-size: 0.7rem;">
                        </div>
                    </div>
                    <div class="form-check mt-2">
                        <input type="checkbox" name="is_emergency_contact" class="form-check-input" value="1">
                        <label class="form-check-label" style="font-size: 0.65rem;">Is Emergency Contact</label>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" name="is_beneficiary" class="form-check-input" value="1">
                        <label class="form-check-label" style="font-size: 0.65rem;">Is Beneficiary</label>
                    </div>
                </div>
                <div class="modal-footer py-1 px-2">
                    <button type="button" class="btn btn-secondary btn-sm rounded-sm" data-bs-dismiss="modal" style="font-size: 0.65rem;">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm rounded-sm" style="font-size: 0.65rem;">Add Dependent</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add Address Modal -->
<div class="modal fade" id="addAddressModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-sm">
            <div class="modal-header py-1 px-2">
                <h5 class="modal-title" style="font-size: 0.8rem;">Add Address</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('profile.addresses.store') }}">
                @csrf
                <div class="modal-body py-2 px-2">
                    <div class="mb-2">
                        <label class="form-label" style="font-size: 0.65rem;">Address Type</label>
                        <select name="address_type" class="form-select form-select-sm rounded-sm" style="font-size: 0.7rem;">
                            <option value="current">Current Address</option>
                            <option value="permanent">Permanent Address</option>
                            <option value="postal">Postal Address</option>
                        </select>
                    </div>
                    <div class="mb-2">
                        <label class="form-label" style="font-size: 0.65rem;">Street/Address</label>
                        <input type="text" name="address" class="form-control form-control-sm rounded-sm" style="font-size: 0.7rem;">
                    </div>
                    <div class="row g-2">
                        <div class="col-6">
                            <label class="form-label" style="font-size: 0.65rem;">City</label>
                            <input type="text" name="city" class="form-control form-control-sm rounded-sm" style="font-size: 0.7rem;">
                        </div>
                        <div class="col-6">
                            <label class="form-label" style="font-size: 0.65rem;">Country</label>
                            <input type="text" name="country" class="form-control form-control-sm rounded-sm" value="Tanzania" style="font-size: 0.7rem;">
                        </div>
                    </div>
                    <div class="form-check mt-2">
                        <input type="checkbox" name="is_primary" class="form-check-input" value="1">
                        <label class="form-check-label" style="font-size: 0.65rem;">Set as Primary Address</label>
                    </div>
                </div>
                <div class="modal-footer py-1 px-2">
                    <button type="button" class="btn btn-secondary btn-sm rounded-sm" data-bs-dismiss="modal" style="font-size: 0.65rem;">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm rounded-sm" style="font-size: 0.65rem;">Add Address</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add Emergency Contact Modal -->
<div class="modal fade" id="addEmergencyModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-sm">
            <div class="modal-header py-1 px-2">
                <h5 class="modal-title" style="font-size: 0.8rem;">Add Emergency Contact</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('profile.emergency.store') }}">
                @csrf
                <div class="modal-body py-2 px-2">
                    <div class="mb-2">
                        <label class="form-label" style="font-size: 0.65rem;">Full Name</label>
                        <input type="text" name="name" class="form-control form-control-sm rounded-sm" required style="font-size: 0.7rem;">
                    </div>
                    <div class="mb-2">
                        <label class="form-label" style="font-size: 0.65rem;">Relationship</label>
                        <input type="text" name="relationship" class="form-control form-control-sm rounded-sm" required style="font-size: 0.7rem;">
                    </div>
                    <div class="mb-2">
                        <label class="form-label" style="font-size: 0.65rem;">Primary Phone</label>
                        <input type="text" name="phone" class="form-control form-control-sm rounded-sm" required style="font-size: 0.7rem;">
                    </div>
                    <div class="mb-2">
                        <label class="form-label" style="font-size: 0.65rem;">Alternative Phone</label>
                        <input type="text" name="alternative_phone" class="form-control form-control-sm rounded-sm" style="font-size: 0.7rem;">
                    </div>
                    <div class="form-check mt-2">
                        <input type="checkbox" name="is_primary" class="form-check-input" value="1">
                        <label class="form-check-label" style="font-size: 0.65rem;">Primary Emergency Contact</label>
                    </div>
                </div>
                <div class="modal-footer py-1 px-2">
                    <button type="button" class="btn btn-secondary btn-sm rounded-sm" data-bs-dismiss="modal" style="font-size: 0.65rem;">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm rounded-sm" style="font-size: 0.65rem;">Add Contact</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add Qualification Modal -->
<div class="modal fade" id="addQualificationModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-sm">
            <div class="modal-header py-1 px-2">
                <h5 class="modal-title" style="font-size: 0.8rem;">Add Qualification</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('profile.qualifications.store') }}">
                @csrf
                <div class="modal-body py-2 px-2">
                    <div class="mb-2">
                        <label class="form-label" style="font-size: 0.65rem;">Qualification Name *</label>
                        <input type="text" name="qualification_name" class="form-control form-control-sm rounded-sm" required style="font-size: 0.7rem;">
                    </div>
                    <div class="mb-2">
                        <label class="form-label" style="font-size: 0.65rem;">Institution</label>
                        <input type="text" name="institution" class="form-control form-control-sm rounded-sm" style="font-size: 0.7rem;">
                    </div>
                    <div class="row g-2">
                        <div class="col-6">
                            <label class="form-label" style="font-size: 0.65rem;">Year Obtained</label>
                            <select name="year_obtained" class="form-select form-select-sm rounded-sm" style="font-size: 0.7rem;">
                                <option value="">Select Year</option>
                                @for($y = date('Y'); $y >= 1970; $y--)
                                    <option value="{{ $y }}">{{ $y }}</option>
                                @endfor
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label" style="font-size: 0.65rem;">Qualification Type</label>
                            <select name="qualification_type" class="form-select form-select-sm rounded-sm" style="font-size: 0.7rem;">
                                <option value="">Select Type</option>
                                <option value="phd">PhD</option>
                                <option value="masters">Masters</option>
                                <option value="bachelors">Bachelor's</option>
                                <option value="diploma">Diploma</option>
                                <option value="certificate">Certificate</option>
                                <option value="professional">Professional</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer py-1 px-2">
                    <button type="button" class="btn btn-secondary btn-sm rounded-sm" data-bs-dismiss="modal" style="font-size: 0.65rem;">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm rounded-sm" style="font-size: 0.65rem;">Add Qualification</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add Skill Modal -->
<div class="modal fade" id="addSkillModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-sm">
            <div class="modal-header py-1 px-2">
                <h5 class="modal-title" style="font-size: 0.8rem;">Add Skill</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('profile.skills.store') }}">
                @csrf
                <div class="modal-body py-2 px-2">
                    <div class="mb-2">
                        <label class="form-label" style="font-size: 0.65rem;">Skill Name *</label>
                        <input type="text" name="skill_name" class="form-control form-control-sm rounded-sm" required style="font-size: 0.7rem;">
                    </div>
                    <div class="row g-2">
                        <div class="col-6">
                            <label class="form-label" style="font-size: 0.65rem;">Category</label>
                            <select name="skill_category" class="form-select form-select-sm rounded-sm" style="font-size: 0.7rem;">
                                <option value="technical">Technical</option>
                                <option value="soft">Soft Skills</option>
                                <option value="artistic">Artistic</option>
                                <option value="sports">Sports</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label" style="font-size: 0.65rem;">Proficiency</label>
                            <select name="proficiency_level" class="form-select form-select-sm rounded-sm" style="font-size: 0.7rem;">
                                <option value="beginner">Beginner</option>
                                <option value="intermediate">Intermediate</option>
                                <option value="advanced">Advanced</option>
                                <option value="expert">Expert</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer py-1 px-2">
                    <button type="button" class="btn btn-secondary btn-sm rounded-sm" data-bs-dismiss="modal" style="font-size: 0.65rem;">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm rounded-sm" style="font-size: 0.65rem;">Add Skill</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add Language Modal -->
<div class="modal fade" id="addLanguageModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-sm">
            <div class="modal-header py-1 px-2">
                <h5 class="modal-title" style="font-size: 0.8rem;">Add Language</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('profile.languages.store') }}">
                @csrf
                <div class="modal-body py-2 px-2">
                    <div class="mb-2">
                        <label class="form-label" style="font-size: 0.65rem;">Language Name *</label>
                        <input type="text" name="language_name" class="form-control form-control-sm rounded-sm" required style="font-size: 0.7rem;">
                    </div>
                    <div class="row g-2">
                        <div class="col-4">
                            <label class="form-label" style="font-size: 0.65rem;">Speaking</label>
                            <select name="proficiency_speak" class="form-select form-select-sm rounded-sm" style="font-size: 0.7rem;">
                                <option value="none">None</option>
                                <option value="basic">Basic</option>
                                <option value="intermediate">Intermediate</option>
                                <option value="fluent">Fluent</option>
                                <option value="native">Native</option>
                            </select>
                        </div>
                        <div class="col-4">
                            <label class="form-label" style="font-size: 0.65rem;">Reading</label>
                            <select name="proficiency_read" class="form-select form-select-sm rounded-sm" style="font-size: 0.7rem;">
                                <option value="none">None</option>
                                <option value="basic">Basic</option>
                                <option value="intermediate">Intermediate</option>
                                <option value="fluent">Fluent</option>
                                <option value="native">Native</option>
                            </select>
                        </div>
                        <div class="col-4">
                            <label class="form-label" style="font-size: 0.65rem;">Writing</label>
                            <select name="proficiency_write" class="form-select form-select-sm rounded-sm" style="font-size: 0.7rem;">
                                <option value="none">None</option>
                                <option value="basic">Basic</option>
                                <option value="intermediate">Intermediate</option>
                                <option value="fluent">Fluent</option>
                                <option value="native">Native</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer py-1 px-2">
                    <button type="button" class="btn btn-secondary btn-sm rounded-sm" data-bs-dismiss="modal" style="font-size: 0.65rem;">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm rounded-sm" style="font-size: 0.65rem;">Add Language</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Avatar upload on file select
document.getElementById('avatar')?.addEventListener('change', function() {
    this.closest('form').submit();
});

// Delete functions
function deletePhone(id) {
    if(confirm('Are you sure you want to delete this phone number?')) {
        fetch(`/profile/phones/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json'
            }
        }).then(() => location.reload());
    }
}

function deleteEmail(id) {
    if(confirm('Are you sure you want to delete this email address?')) {
        fetch(`/profile/emails/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json'
            }
        }).then(() => location.reload());
    }
}

function deleteDependent(id) {
    if(confirm('Are you sure you want to delete this dependent?')) {
        fetch(`/profile/dependents/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json'
            }
        }).then(() => location.reload());
    }
}

function deleteAddress(id) {
    if(confirm('Are you sure you want to delete this address?')) {
        fetch(`/profile/addresses/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json'
            }
        }).then(() => location.reload());
    }
}

function deleteEmergency(id) {
    if(confirm('Are you sure you want to delete this emergency contact?')) {
        fetch(`/profile/emergency/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json'
            }
        }).then(() => location.reload());
    }
}

function deleteQualification(id) {
    if(confirm('Are you sure you want to delete this qualification?')) {
        fetch(`/profile/qualifications/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json'
            }
        }).then(() => location.reload());
    }
}

function deleteSkill(id) {
    if(confirm('Are you sure you want to delete this skill?')) {
        fetch(`/profile/skills/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json'
            }
        }).then(() => location.reload());
    }
}

function deleteLanguage(id) {
    if(confirm('Are you sure you want to delete this language?')) {
        fetch(`/profile/languages/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json'
            }
        }).then(() => location.reload());
    }
}
</script>
@endsection