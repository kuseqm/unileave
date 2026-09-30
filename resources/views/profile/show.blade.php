@extends('layouts.master')

@section('title', 'My Profile')

@section('content')
<style>
.card { margin-bottom: 0.5rem !important; }
.card-body { padding: 0.5rem !important; }
.row.g-2 { --bs-gutter-y: 0.5rem; }
.mb-2 { margin-bottom: 0.5rem !important; }
.mt-2 { margin-top: 0.5rem !important; }
.table-striped tbody tr:nth-of-type(odd) { background-color: rgba(0, 0, 0, 0.02); }
.table-striped tbody tr:hover { background-color: rgba(59, 130, 246, 0.05); }
.table-sm td, .table-sm th { padding: 0.3rem 0.5rem; font-size: 0.7rem; }
.progress { height: 4px; background-color: #E8E2D5; }
</style>

<div class="container-fluid px-2">
    <x-breadcrumb :items="[
        ['name' => 'Dashboard', 'url' => route('dashboard')],
        ['name' => 'My Profile', 'url' => '#']
    ]" />
    
    <x-page-header title="MY PROFILE" description="View and manage your personal and professional information"></x-page-header>
    
    <x-alert-success />
    
    <x-alert-danger />

    @if(session('warning'))
    <x-alert-warning>
    {{ session('warning') }}
        <button type="button" class="btn-close py-0" style="font-size: 0.6rem;" data-bs-dismiss="alert"></button>
</x-alert-warning>
    @endif

    <div class="row">
        <!-- LEFT COLUMN - Profile Picture & Stats -->
        <div class="col-md-3">
            <div class="card rounded-sm">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-user text-primary me-1" style="font-size: 0.8rem;"></i>
                        <span style="font-size: 0.7rem; font-weight: 600;">Profile Picture</span>
                    </div>
                </div>
                <div class="card-body text-center py-2">
                    <div class="user-avatar-large mx-auto mb-2 position-relative" style="width: 100px; height: 100px;">
                        <div style="width: 100px; height: 100px; background: #EFF6FF; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto; border: 3px solid #3B82F6;">
                            @if(auth()->user()->avatar)
                                <img src="{{ Storage::url(auth()->user()->avatar) }}" style="width: 94px; height: 94px; border-radius: 50%; object-fit: cover;">
                            @else
                                <span style="font-size: 2rem; font-weight: 600; color: #3B82F6;">
                                    {{ strtoupper(substr(auth()->user()->first_name, 0, 1)) }}
                                    @if(auth()->user()->middle_name){{ strtoupper(substr(auth()->user()->middle_name, 0, 1)) }}@endif
                                    {{ strtoupper(substr(auth()->user()->last_name, 0, 1)) }}
                                </span>
                            @endif
                        </div>
                        <button type="button" class="btn btn-sm btn-primary rounded-circle position-absolute" style="bottom: 5px; right: 0; width: 28px; height: 28px; padding: 0;" data-bs-toggle="modal" data-bs-target="#avatarModal">
                            <i class="ti ti-camera" style="font-size: 0.7rem;"></i>
                        </button>
                    </div>
                    <hr class="my-1">
                    <div class="text-start">
                        <div class="mb-1 text-center">
                            <span style="font-size: 0.9rem; font-weight: 700; color: #1E3A5F;">
                                {{ auth()->user()->title ? auth()->user()->title . ' ' : '' }}{{ auth()->user()->first_name }} 
                                {{ auth()->user()->middle_name ? auth()->user()->middle_name . ' ' : '' }}
                                <span style="color: #3B82F6;">{{ strtoupper(auth()->user()->last_name) }}</span>
                            </span>
                        </div>
                        <div class="mb-1" style="font-size: 0.65rem;"><i class="ti ti-id me-1"></i> <strong>Staff ID:</strong> {{ auth()->user()->staff_id ?? 'N/A' }}</div>
                        <div class="mb-1" style="font-size: 0.65rem;"><i class="ti ti-school me-1"></i> <strong>School/Faculty:</strong> {{ auth()->user()->school->name ?? 'N/A' }}</div>
                        <div class="mb-1" style="font-size: 0.65rem;"><i class="ti ti-building me-1"></i> <strong>Department:</strong> {{ auth()->user()->department->name ?? 'N/A' }}</div>
                        <div class="mb-1" style="font-size: 0.65rem;"><i class="ti ti-phone me-1"></i> <strong>Phone:</strong> {{ auth()->user()->phone ?? 'Not provided' }}</div>
                        <div style="font-size: 0.65rem;"><i class="ti ti-mail me-1"></i> <strong>Email:</strong> {{ auth()->user()->email }}</div>
                    </div>
                </div>
            </div>
            
            <div class="card rounded-sm mt-1">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center"><i class="ti ti-chart-bar text-primary me-1" style="font-size: 0.8rem;"></i><span style="font-size: 0.7rem; font-weight: 600;">Leave Statistics</span></div>
                </div>
                <div class="card-body py-2 px-2">
                    <div class="row g-1 text-center">
                        <div class="col-6"><div class="text-muted" style="font-size: 0.55rem;">Total</div><div class="fw-bold text-primary" style="font-size: 1.1rem;">{{ $totalLeaveRequests ?? 0 }}</div></div>
                        <div class="col-6"><div class="text-muted" style="font-size: 0.55rem;">Pending</div><div class="fw-bold text-warning" style="font-size: 1.1rem;">{{ $pendingLeaveRequests ?? 0 }}</div></div>
                        <div class="col-6 mt-1"><div class="text-muted" style="font-size: 0.55rem;">Approved</div><div class="fw-bold text-success" style="font-size: 1.1rem;">{{ $approvedLeaveRequests ?? 0 }}</div></div>
                        <div class="col-6 mt-1"><div class="text-muted" style="font-size: 0.55rem;">Rejected</div><div class="fw-bold text-danger" style="font-size: 1.1rem;">{{ $rejectedLeaveRequests ?? 0 }}</div></div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- RIGHT COLUMN - TABS -->
        <div class="col-md-9">
            <div class="card rounded-sm">
                <div class="card-header py-1 px-2 bg-light">
                    <ul class="nav nav-tabs card-header-tabs" id="profileTabs" role="tablist">
                        <li class="nav-item"><button class="nav-link active" id="basic-tab" data-bs-toggle="tab" data-bs-target="#basic" type="button" style="font-size: 0.7rem;"><i class="ti ti-user me-1"></i> Basic Info</button></li>
                        <li class="nav-item"><button class="nav-link" id="contact-tab" data-bs-toggle="tab" data-bs-target="#contact" type="button" style="font-size: 0.7rem;"><i class="ti ti-phone me-1"></i> Contact & Emergency</button></li>
                        <li class="nav-item"><button class="nav-link" id="dependents-tab" data-bs-toggle="tab" data-bs-target="#dependents" type="button" style="font-size: 0.7rem;"><i class="ti ti-users me-1"></i> Dependents</button></li>
                        <li class="nav-item"><button class="nav-link" id="addresses-tab" data-bs-toggle="tab" data-bs-target="#addresses" type="button" style="font-size: 0.7rem;"><i class="ti ti-home me-1"></i> Addresses</button></li>
                        <li class="nav-item"><button class="nav-link" id="bank-tab" data-bs-toggle="tab" data-bs-target="#bank" type="button" style="font-size: 0.7rem;"><i class="ti ti-credit-card me-1"></i> Bank Details</button></li>
                        <li class="nav-item"><button class="nav-link" id="professional-tab" data-bs-toggle="tab" data-bs-target="#professional" type="button" style="font-size: 0.7rem;"><i class="ti ti-briefcase me-1"></i> Professional</button></li>
                    </ul>
                </div>
                <div class="card-body p-2">
                    <div class="tab-content">
                        
                        <!-- ==================== TAB 1: BASIC INFO ==================== -->
                        <div class="tab-pane fade show active" id="basic" role="tabpanel">
                            <div class="card rounded-sm">
                                <div class="card-header py-1 px-2 bg-light d-flex justify-content-between align-items-center">
                                    <span style="font-size: 0.7rem; font-weight: 600;"><i class="ti ti-user me-1"></i> Personal Information</span>
                                    <button type="button" class="btn btn-sm btn-outline-primary rounded-sm" data-bs-toggle="modal" data-bs-target="#editBasicInfoModal" style="font-size: 0.6rem;"><i class="ti ti-edit me-1"></i> Edit</button>
                                </div>
                                <div class="card-body p-0">
                                    <table class="table table-sm table-bordered mb-0" style="font-size: 0.7rem;">
                                        <tbody>
                                            <tr><td class="ps-2 py-1 bg-light" style="width: 15%;">Title</td><td class="ps-2 py-1">{{ auth()->user()->title ?? 'Not set' }}</td>
                                                <td class="ps-2 py-1 bg-light" style="width: 15%;">Gender</td><td class="ps-2 py-1">{{ ucfirst(auth()->user()->gender ?? 'Not set') }}</td></tr>
                                            <tr><td class="ps-2 py-1 bg-light">Date of Birth</td><td class="ps-2 py-1">{{ auth()->user()->date_of_birth ? auth()->user()->date_of_birth->format('d M Y') : 'Not set' }}</td>
                                                <td class="ps-2 py-1 bg-light">National ID</td><td class="ps-2 py-1">{{ auth()->user()->national_id ?? 'Not set' }}</td></tr>
                                            <tr><td class="ps-2 py-1 bg-light">First Name</td><td class="ps-2 py-1">{{ auth()->user()->first_name }}</td>
                                                <td class="ps-2 py-1 bg-light">Middle Name</td><td class="ps-2 py-1">{{ auth()->user()->middle_name ?? 'Not set' }}</td></tr>
                                            <tr><td class="ps-2 py-1 bg-light">Last Name</td><td class="ps-2 py-1" colspan="3">{{ auth()->user()->last_name }}</td></tr>
                                            <tr><td class="ps-2 py-1 bg-light">Marital Status</td><td class="ps-2 py-1">{{ ucfirst(auth()->user()->marital_status ?? 'Not set') }}</td>
                                                <td class="ps-2 py-1 bg-light">Spouse Name</td><td class="ps-2 py-1">{{ auth()->user()->spouse_name ?? 'Not set' }}</td></tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            
                            <div class="card rounded-sm mt-1">
                                <div class="card-header py-1 px-2 bg-light"><span style="font-size: 0.7rem; font-weight: 600;"><i class="ti ti-key me-1"></i> Security</span></div>
                                <div class="card-body p-2">
                                    <form method="POST" action="{{ route('profile.password.update') }}">
                                        @csrf @method('PUT')
                                        <div class="row g-2">
                                            <div class="col-md-4"><label class="form-label" style="font-size: 0.65rem;">Current Password</label><input type="password" name="current_password" class="form-control form-control-sm" required style="font-size: 0.75rem;"></div>
                                            <div class="col-md-4"><label class="form-label" style="font-size: 0.65rem;">New Password</label><input type="password" name="password" class="form-control form-control-sm" required style="font-size: 0.75rem;"></div>
                                            <div class="col-md-4"><label class="form-label" style="font-size: 0.65rem;">Confirm Password</label><input type="password" name="password_confirmation" class="form-control form-control-sm" required style="font-size: 0.75rem;"></div>
                                        </div>
                                        <div class="mt-2"><button type="submit" class="btn btn-warning btn-sm"><i class="ti ti-key me-1"></i> Change Password</button></div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        
                        <!-- ==================== TAB 2: CONTACT & EMERGENCY (MERGED) ==================== -->
                        <div class="tab-pane fade" id="contact" role="tabpanel">
                            <!-- Primary Contact -->
                            <div class="card rounded-sm">
                                <div class="card-header py-1 px-2 bg-light"><span style="font-size: 0.7rem; font-weight: 600;"><i class="ti ti-phone me-1"></i> Primary Contact</span></div>
                                <div class="card-body p-2">
                                    <div class="row g-2">
                                        <div class="col-md-6"><div class="text-muted" style="font-size: 0.6rem;">Email</div><div style="font-size: 0.75rem;">{{ auth()->user()->email }}</div></div>
                                        <div class="col-md-6"><div class="text-muted" style="font-size: 0.6rem;">Phone</div><div style="font-size: 0.75rem;">{{ auth()->user()->phone ?? 'Not provided' }}</div></div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Emergency Contacts -->
                            <div class="card rounded-sm mt-1">
                                <div class="card-header py-1 px-2 bg-light d-flex justify-content-between align-items-center">
                                    <span style="font-size: 0.7rem; font-weight: 600;"><i class="ti ti-phone-call me-1"></i> Emergency Contacts</span>
                                    <button type="button" class="btn btn-sm btn-outline-primary rounded-sm" data-bs-toggle="modal" data-bs-target="#addEmergencyModal" style="font-size: 0.6rem;"><i class="ti ti-plus"></i> Add</button>
                                </div>
                                <div class="card-body p-0">
                                    <table class="table table-sm table-striped mb-0" style="font-size: 0.65rem;">
                                        <thead class="bg-light"><tr><th class="ps-2 py-1">Name</th><th class="py-1">Relationship</th><th class="py-1">Phone</th><th class="pe-2 py-1" width="100">Actions</th></tr></thead>
                                        <tbody>
                                            @forelse(auth()->user()->emergencyContacts ?? [] as $contact)
                                            <tr>
                                                <td class="ps-2 py-1">{{ $contact->full_name }}</td>
                                                <td class="py-1">{{ $contact->relationship }}</td>
                                                <td class="py-1">{{ $contact->phone_primary }}</td>
                                                <td class="pe-2 py-1">
                                                    <button class="btn btn-sm btn-outline-info rounded-sm" onclick="viewEmergency({{ $contact->id }})"><i class="ti ti-eye"></i></button>
                                                    <button class="btn btn-sm btn-outline-danger rounded-sm" onclick="deleteEmergency({{ $contact->id }})"><i class="ti ti-trash"></i></button>
                                                </td>
                                            </tr>
                                            @empty
                                            <tr><td colspan="4" class="text-center py-2 text-muted">No emergency contacts added</td></tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        
                        <!-- ==================== TAB 3: DEPENDENTS ==================== -->
                        <div class="tab-pane fade" id="dependents" role="tabpanel">
                            <div class="card rounded-sm">
                                <div class="card-body p-0">
                                    <table class="table table-sm table-striped mb-0" style="font-size: 0.65rem;">
                                        <thead class="bg-light"><tr><th class="ps-2 py-1">#</th><th class="py-1">Name</th><th class="py-1">Relationship</th><th class="py-1">Phone</th><th class="py-1">Emergency</th><th class="pe-2 py-1" width="100">Actions</th></tr></thead>
                                        <tbody>
                                            @forelse(auth()->user()->dependents ?? [] as $index => $dependent)
                                            <tr>
                                                <td class="ps-2 py-1">{{ $index + 1 }}</td>
                                                <td class="py-1">{{ $dependent->full_name }}</td>
                                                <td class="py-1">{{ ucfirst($dependent->relationship) }}</td>
                                                <td class="py-1">{{ $dependent->phone ?? '-' }}</td>
                                                <td class="py-1">@if($dependent->is_emergency_contact)<span class="badge bg-success-subtle text-success">Yes</span>@else<span class="badge bg-secondary-subtle text-secondary">No</span>@endif</td>
                                                <td class="pe-2 py-1">
                                                    <button class="btn btn-sm btn-outline-info rounded-sm" onclick="viewDependent({{ $dependent->id }})"><i class="ti ti-eye"></i></button>
                                                    <button class="btn btn-sm btn-outline-danger rounded-sm" onclick="deleteDependent({{ $dependent->id }})"><i class="ti ti-trash"></i></button>
                                                </td>
                                            </tr>
                                            @empty
                                            <tr><td colspan="6" class="text-center py-2 text-muted">No dependents added</td></tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                                <div class="card-footer py-1 px-2 text-end">
                                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addDependentModal"><i class="ti ti-plus me-1"></i> Add Dependent</button>
                                </div>
                            </div>
                        </div>
                        
                        <!-- ==================== TAB 4: ADDRESSES ==================== -->
                        <div class="tab-pane fade" id="addresses" role="tabpanel">
                            <div class="card rounded-sm">
                                <div class="card-header py-1 px-2 bg-light d-flex justify-content-between align-items-center">
                                    <span style="font-size: 0.7rem; font-weight: 600;"><i class="ti ti-home me-1"></i> Addresses</span>
                                    <button type="button" class="btn btn-sm btn-outline-primary rounded-sm" data-bs-toggle="modal" data-bs-target="#addAddressModal" style="font-size: 0.6rem;"><i class="ti ti-plus"></i> Add</button>
                                </div>
                                <div class="card-body p-0">
                                    <table class="table table-sm table-striped mb-0" style="font-size: 0.65rem;">
                                        <thead class="bg-light"><tr><th class="ps-2 py-1">#</th><th class="py-1">Type</th><th class="py-1">Address</th><th class="py-1">City</th><th class="py-1">Primary</th><th class="pe-2 py-1" width="80">Actions</th></tr></thead>
                                        <tbody>
                                            @forelse(auth()->user()->addresses ?? [] as $index => $address)
                                            <tr>
                                                <td class="ps-2 py-1">{{ $index + 1 }}</td>
                                                <td class="py-1">{{ ucfirst($address->address_type) }}</td>
                                                <td class="py-1">{{ $address->street ?? '-' }}</td>
                                                <td class="py-1">{{ $address->city ?? '-' }}</td>
                                                <td class="py-1">@if($address->is_primary)<span class="badge bg-success-subtle text-success">Yes</span>@endif</td>
                                                <td class="pe-2 py-1">
                                                    <button class="btn btn-sm btn-outline-info rounded-sm" onclick="viewAddress({{ $address->id }})"><i class="ti ti-eye"></i></button>
                                                    <button class="btn btn-sm btn-outline-danger rounded-sm" onclick="deleteAddress({{ $address->id }})"><i class="ti ti-trash"></i></button>
                                                </td>
                                            </tr>
                                            @empty
                                            <tr><td colspan="6" class="text-center py-2 text-muted">No addresses added</td></tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        
                        <!-- ==================== TAB 5: BANK DETAILS ==================== -->
                        <div class="tab-pane fade" id="bank" role="tabpanel">
                            <!-- TIN & NSSF -->
                            <div class="card rounded-sm mb-1">
                                <div class="card-header py-1 px-2 bg-light"><span style="font-size: 0.7rem; font-weight: 600;"><i class="ti ti-file-text me-1"></i> Tax & Social Security</span></div>
                                <div class="card-body p-2">
                                    <div class="row g-2">
                                        <div class="col-md-6"><div class="text-muted" style="font-size: 0.6rem;">TIN Number</div><div class="fw-semibold">{{ auth()->user()->tin_number ?? 'Not provided' }}</div></div>
                                        <div class="col-md-6"><div class="text-muted" style="font-size: 0.6rem;">NSSF Number</div><div class="fw-semibold">{{ auth()->user()->nssf_number ?? 'Not provided' }}</div></div>
                                    </div>
                                    <div class="mt-2"><button type="button" class="btn btn-sm btn-outline-primary rounded-sm" data-bs-toggle="modal" data-bs-target="#editTinNssfModal"><i class="ti ti-edit me-1"></i> Update</button></div>
                                </div>
                            </div>
                            
                            <!-- Bank Accounts -->
                            <div class="card rounded-sm">
                                <div class="card-header py-1 px-2 bg-light d-flex justify-content-between align-items-center">
                                    <span style="font-size: 0.7rem; font-weight: 600;"><i class="ti ti-credit-card me-1"></i> Bank Accounts</span>
                                    <button type="button" class="btn btn-sm btn-outline-primary rounded-sm" data-bs-toggle="modal" data-bs-target="#addBankAccountModal" style="font-size: 0.6rem;"><i class="ti ti-plus"></i> Add Account</button>
                                </div>
                                <div class="card-body p-0">
                                    <table class="table table-sm table-striped mb-0" style="font-size: 0.65rem;">
                                        <thead class="bg-light"><tr><th class="ps-2 py-1">#</th><th class="py-1">Bank Name</th><th class="py-1">Account Name</th><th class="py-1">Account Number</th><th class="py-1">Primary</th><th class="pe-2 py-1" width="80">Actions</th></tr></thead>
                                        <tbody>
                                            @forelse(auth()->user()->bankDetails ?? [] as $index => $bank)
                                            <tr>
                                                <td class="ps-2 py-1">{{ $index + 1 }}</td>
                                                <td class="py-1">{{ $bank->bank_name ?? '-' }}</td>
                                                <td class="py-1">{{ $bank->account_name ?? '-' }}</td>
                                                <td class="py-1">{{ $bank->account_number ?? '-' }}</td>
                                                <td class="py-1">@if($bank->is_primary)<span class="badge bg-success-subtle text-success">Primary</span>@endif</td>
                                                <td class="pe-2 py-1">
                                                    <button class="btn btn-sm btn-outline-info rounded-sm" onclick="viewBankAccount({{ $bank->id }})"><i class="ti ti-eye"></i></button>
                                                    <button class="btn btn-sm btn-outline-danger rounded-sm" onclick="deleteBankAccount({{ $bank->id }})"><i class="ti ti-trash"></i></button>
                                                </td>
                                            </tr>
                                            @empty
                                            <tr><td colspan="6" class="text-center py-2 text-muted">No bank accounts added. <button type="button" class="btn btn-sm btn-link" data-bs-toggle="modal" data-bs-target="#addBankAccountModal">Add one</button></td></tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        
                        <!-- ==================== TAB 6: PROFESSIONAL ==================== -->
                        <div class="tab-pane fade" id="professional" role="tabpanel">
                            <!-- Employment Info -->
                            <div class="card rounded-sm">
                                <div class="card-header py-1 px-2 bg-light"><span style="font-size: 0.7rem; font-weight: 600;">Employment Information</span></div>
                                <div class="card-body p-2">
                                    <div class="row g-1">
                                        <div class="col-md-4"><div class="text-muted" style="font-size: 0.6rem;">Job Title</div><p class="mb-0" style="font-size: 0.75rem;">{{ auth()->user()->job_title ?? 'Not set' }}</p></div>
                                        <div class="col-md-4"><div class="text-muted" style="font-size: 0.6rem;">Job Grade</div><p class="mb-0" style="font-size: 0.75rem;">{{ auth()->user()->job_grade ?? 'Not set' }}</p></div>
                                        <div class="col-md-4"><div class="text-muted" style="font-size: 0.6rem;">Contract Type</div><p class="mb-0" style="font-size: 0.75rem;">{{ ucfirst(auth()->user()->contract_type ?? 'Not set') }}</p></div>
                                        <div class="col-md-4"><div class="text-muted" style="font-size: 0.6rem;">Contract End Date</div><p class="mb-0" style="font-size: 0.75rem;">{{ auth()->user()->contract_end_date ? auth()->user()->contract_end_date->format('d M Y') : 'N/A' }}</p></div>
                                        <div class="col-md-4"><div class="text-muted" style="font-size: 0.6rem;">Confirmation Date</div><p class="mb-0" style="font-size: 0.75rem;">{{ auth()->user()->confirmation_date ? auth()->user()->confirmation_date->format('d M Y') : 'N/A' }}</p></div>
                                        <div class="col-md-4"><div class="text-muted" style="font-size: 0.6rem;">Reporting To</div><p class="mb-0" style="font-size: 0.75rem;">{{ auth()->user()->reportsTo->full_name ?? 'N/A' }}</p></div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Qualifications -->
                            <div class="card rounded-sm mt-1">
                                <div class="card-header py-1 px-2 bg-light d-flex justify-content-between align-items-center"><span style="font-size: 0.7rem; font-weight: 600;"><i class="ti ti-certificate me-1"></i> Qualifications</span><button type="button" class="btn btn-sm btn-outline-primary rounded-sm" data-bs-toggle="modal" data-bs-target="#addQualificationModal" style="font-size: 0.6rem;"><i class="ti ti-plus"></i> Add</button></div>
                                <div class="card-body p-0">
                                    <table class="table table-sm table-striped mb-0" style="font-size: 0.65rem;">
                                        <thead class="bg-light"><tr><th class="ps-2 py-1">Qualification</th><th class="py-1">Institution</th><th class="py-1">Year</th><th class="pe-2 py-1" width="80">Actions</th></tr></thead>
                                        <tbody>
                                            @forelse(auth()->user()->qualifications ?? [] as $qual)
                                            <tr>
                                                <td class="ps-2 py-1">{{ $qual->qualification_name }}</td>
                                                <td class="py-1">{{ $qual->institution ?? '-' }}</td>
                                                <td class="py-1">{{ $qual->year_obtained ?? '-' }}</td>
                                                <td class="pe-2 py-1">
                                                    <button class="btn btn-sm btn-outline-info rounded-sm" onclick="viewQualification({{ $qual->id }})"><i class="ti ti-eye"></i></button>
                                                    <button class="btn btn-sm btn-outline-danger rounded-sm" onclick="deleteQualification({{ $qual->id }})"><i class="ti ti-trash"></i></button>
                                                </td>
                                            </tr>
                                            @empty
                                            <tr><td colspan="4" class="text-center py-2 text-muted">No qualifications added</td></tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            
                            <!-- Skills -->
                            <div class="card rounded-sm mt-1">
                                <div class="card-header py-1 px-2 bg-light d-flex justify-content-between align-items-center"><span style="font-size: 0.7rem; font-weight: 600;"><i class="ti ti-code me-1"></i> Skills</span><button type="button" class="btn btn-sm btn-outline-primary rounded-sm" data-bs-toggle="modal" data-bs-target="#addSkillModal" style="font-size: 0.6rem;"><i class="ti ti-plus"></i> Add</button></div>
                                <div class="card-body p-0">
                                    <table class="table table-sm table-striped mb-0" style="font-size: 0.65rem;">
                                        <thead class="bg-light"><tr><th class="ps-2 py-1">Skill</th><th class="py-1">Category</th><th class="py-1">Proficiency</th><th class="pe-2 py-1" width="80">Actions</th></tr></thead>
                                        <tbody>
                                            @forelse(auth()->user()->skills ?? [] as $skill)
                                            <tr>
                                                <td class="ps-2 py-1">{{ $skill->skill_name }}</td>
                                                <td class="py-1">{{ ucfirst($skill->skill_category ?? 'Other') }}</td>
                                                <td class="py-1">{{ ucfirst($skill->proficiency_level) }}</td>
                                                <td class="pe-2 py-1">
                                                    <button class="btn btn-sm btn-outline-info rounded-sm" onclick="viewSkill({{ $skill->id }})"><i class="ti ti-eye"></i></button>
                                                    <button class="btn btn-sm btn-outline-danger rounded-sm" onclick="deleteSkill({{ $skill->id }})"><i class="ti ti-trash"></i></button>
                                                </td>
                                            </tr>
                                            @empty
                                            <tr><td colspan="4" class="text-center py-2 text-muted">No skills added</td></tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            
                            <!-- Languages -->
                            <div class="card rounded-sm mt-1">
                                <div class="card-header py-1 px-2 bg-light d-flex justify-content-between align-items-center"><span style="font-size: 0.7rem; font-weight: 600;"><i class="ti ti-language me-1"></i> Languages</span><button type="button" class="btn btn-sm btn-outline-primary rounded-sm" data-bs-toggle="modal" data-bs-target="#addLanguageModal" style="font-size: 0.6rem;"><i class="ti ti-plus"></i> Add</button></div>
                                <div class="card-body p-0">
                                    <table class="table table-sm table-striped mb-0" style="font-size: 0.65rem;">
                                        <thead class="bg-light"><tr><th class="ps-2 py-1">Language</th><th class="py-1">Speak</th><th class="py-1">Read</th><th class="pe-2 py-1" width="80">Actions</th></tr></thead>
                                        <tbody>
                                            @forelse(auth()->user()->languages ?? [] as $lang)
                                            <tr>
                                                <td class="ps-2 py-1">{{ $lang->language_name }}</td>
                                                <td class="py-1">{{ ucfirst($lang->proficiency_speak) }}</td>
                                                <td class="py-1">{{ ucfirst($lang->proficiency_read) }}</td>
                                                <td class="pe-2 py-1">
                                                    <button class="btn btn-sm btn-outline-info rounded-sm" onclick="viewLanguage({{ $lang->id }})"><i class="ti ti-eye"></i></button>
                                                    <button class="btn btn-sm btn-outline-danger rounded-sm" onclick="deleteLanguage({{ $lang->id }})"><i class="ti ti-trash"></i></button>
                                                </td>
                                            </tr>
                                            @empty
                                            <tr><td colspan="4" class="text-center py-2 text-muted">No languages added</td></tr>
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

<!-- ============================================ -->
<!-- MODALS SECTION -->
<!-- ============================================ -->

<!-- Edit Basic Info Modal -->
<div class="modal fade" id="editBasicInfoModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered"><div class="modal-content rounded-sm">
        <div class="modal-header py-1 px-2 bg-light"><h5 class="modal-title" style="font-size: 0.8rem;"><i class="ti ti-edit me-1"></i> Edit Basic Information</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
        <form method="POST" action="{{ route('profile.update') }}">@csrf @method('PUT')
            <div class="modal-body py-2 px-2">
                <div class="row g-2"><div class="col-6"><label class="form-label" style="font-size: 0.65rem;">Title</label><select name="title" class="form-select form-select-sm"><option value="">Select</option><option value="Mr" {{ auth()->user()->title == 'Mr' ? 'selected' : '' }}>Mr</option><option value="Mrs" {{ auth()->user()->title == 'Mrs' ? 'selected' : '' }}>Mrs</option><option value="Ms" {{ auth()->user()->title == 'Ms' ? 'selected' : '' }}>Ms</option><option value="Dr" {{ auth()->user()->title == 'Dr' ? 'selected' : '' }}>Dr</option><option value="Prof" {{ auth()->user()->title == 'Prof' ? 'selected' : '' }}>Prof</option><option value="Eng" {{ auth()->user()->title == 'Eng' ? 'selected' : '' }}>Eng</option><option value="Rev" {{ auth()->user()->title == 'Rev' ? 'selected' : '' }}>Rev</option></select></div>
                <div class="col-6"><label class="form-label" style="font-size: 0.65rem;">Gender</label><select name="gender" class="form-select form-select-sm"><option value="">Select</option><option value="male" {{ auth()->user()->gender == 'male' ? 'selected' : '' }}>Male</option><option value="female" {{ auth()->user()->gender == 'female' ? 'selected' : '' }}>Female</option><option value="other" {{ auth()->user()->gender == 'other' ? 'selected' : '' }}>Other</option></select></div></div>
                <div class="row g-2 mt-1"><div class="col-6"><label class="form-label" style="font-size: 0.65rem;">Date of Birth</label><input type="date" name="date_of_birth" class="form-control form-control-sm" value="{{ auth()->user()->date_of_birth ? auth()->user()->date_of_birth->format('Y-m-d') : '' }}"></div>
                <div class="col-6"><label class="form-label" style="font-size: 0.65rem;">National ID</label><input type="text" name="national_id" class="form-control form-control-sm" value="{{ auth()->user()->national_id ?? '' }}"></div></div>
                <div class="row g-2 mt-1"><div class="col-6"><label class="form-label" style="font-size: 0.65rem;">Marital Status</label><select name="marital_status" class="form-select form-select-sm"><option value="">Select</option><option value="single" {{ auth()->user()->marital_status == 'single' ? 'selected' : '' }}>Single</option><option value="married" {{ auth()->user()->marital_status == 'married' ? 'selected' : '' }}>Married</option><option value="divorced" {{ auth()->user()->marital_status == 'divorced' ? 'selected' : '' }}>Divorced</option><option value="widowed" {{ auth()->user()->marital_status == 'widowed' ? 'selected' : '' }}>Widowed</option></select></div>
                <div class="col-6"><label class="form-label" style="font-size: 0.65rem;">Spouse Name</label><input type="text" name="spouse_name" class="form-control form-control-sm" value="{{ auth()->user()->spouse_name ?? '' }}"></div></div>
                <div class="row g-2 mt-1"><div class="col-12"><label class="form-label" style="font-size: 0.65rem;">Phone Number</label><input type="text" name="phone" class="form-control form-control-sm" value="{{ auth()->user()->phone ?? '' }}"></div></div>
                <x-alert-info>
    First Name, Middle Name, and Last Name cannot be changed. Contact HR for corrections.
</x-alert-info>
            </div>
            <div class="modal-footer py-1 px-2"><button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button><button type="submit" class="btn btn-primary btn-sm"><i class="ti ti-save me-1"></i> Save Changes</button></div>
        </form>
    </div></div>
</div>

<!-- Avatar Modal -->
<div class="modal fade" id="avatarModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered"><div class="modal-content rounded-sm">
        <div class="modal-header py-1 px-2"><h5 class="modal-title" style="font-size: 0.8rem;">Update Profile Picture</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
        <form method="POST" action="{{ route('profile.avatar.update') }}" enctype="multipart/form-data">@csrf
            <div class="modal-body py-2 px-2 text-center">
                <img id="avatarPreview" src="{{ auth()->user()->avatar ? Storage::url(auth()->user()->avatar) : 'https://via.placeholder.com/150' }}" style="width: 100px; height: 100px; border-radius: 50%; object-fit: cover; border: 2px solid #3B82F6;">
                <input type="file" name="avatar" id="avatarInput" class="form-control form-control-sm mt-2" accept="image/*">
                <small class="text-muted">Max size: 2MB. Allowed: JPG, PNG, GIF</small>
            </div>
            <div class="modal-footer py-1 px-2"><button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button><button type="submit" class="btn btn-primary btn-sm">Upload</button></div>
        </form>
    </div></div>
</div>

<!-- Add Dependent Modal -->
<div class="modal fade" id="addDependentModal" tabindex="-1"><div class="modal-dialog modal-dialog-centered"><div class="modal-content rounded-sm">
    <div class="modal-header py-1 px-2 bg-light"><h5 class="modal-title" style="font-size: 0.8rem;">Add Dependent</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
    <form method="POST" action="{{ route('profile.dependents.store') }}">@csrf
        <div class="modal-body py-2 px-2">
            <div class="mb-2"><label class="form-label" style="font-size: 0.65rem;">Full Name *</label><input type="text" name="full_name" class="form-control form-control-sm" required></div>
            <div class="row g-2"><div class="col-6"><label>Relationship</label><select name="relationship" class="form-select form-select-sm"><option value="spouse">Spouse</option><option value="child">Child</option><option value="parent">Parent</option><option value="sibling">Sibling</option><option value="other">Other</option></select></div><div class="col-6"><label>Date of Birth</label><input type="date" name="date_of_birth" class="form-control form-control-sm"></div></div>
            <div class="row g-2 mt-1"><div class="col-6"><label>Phone</label><input type="text" name="phone" class="form-control form-control-sm"></div><div class="col-6"><label>Email</label><input type="email" name="email" class="form-control form-control-sm"></div></div>
            <div class="form-check mt-2"><input type="checkbox" name="is_emergency_contact" class="form-check-input" value="1"><label class="form-check-label">Emergency Contact</label></div>
            <div class="form-check"><input type="checkbox" name="is_beneficiary" class="form-check-input" value="1"><label class="form-check-label">Is Beneficiary</label></div>
        </div>
        <div class="modal-footer py-1 px-2"><button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button><button type="submit" class="btn btn-primary btn-sm">Add Dependent</button></div>
    </form>
</div></div></div>

<!-- Add Address Modal -->
<div class="modal fade" id="addAddressModal" tabindex="-1"><div class="modal-dialog modal-dialog-centered"><div class="modal-content rounded-sm">
    <div class="modal-header py-1 px-2 bg-light"><h5 class="modal-title" style="font-size: 0.8rem;">Add Address</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
    <form method="POST" action="{{ route('profile.addresses.store') }}">@csrf
        <div class="modal-body py-2 px-2">
            <div class="mb-2"><label>Address Type</label><select name="address_type" class="form-select form-select-sm"><option value="current">Current Address</option><option value="permanent">Permanent Address</option><option value="postal">Postal Address</option></select></div>
            <div class="mb-2"><label>Street Address</label><textarea name="address" rows="2" class="form-control form-control-sm"></textarea></div>
            <div class="row g-2"><div class="col-6"><label>City</label><input type="text" name="city" class="form-control form-control-sm"></div><div class="col-6"><label>Country</label><input type="text" name="country" class="form-control form-control-sm" value="Tanzania"></div></div>
            <div class="form-check mt-2"><input type="checkbox" name="is_primary" class="form-check-input" value="1"><label class="form-check-label">Set as Primary Address</label></div>
        </div>
        <div class="modal-footer py-1 px-2"><button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button><button type="submit" class="btn btn-primary btn-sm">Add Address</button></div>
    </form>
</div></div></div>

<!-- Add Emergency Contact Modal -->
<div class="modal fade" id="addEmergencyModal" tabindex="-1"><div class="modal-dialog modal-dialog-centered"><div class="modal-content rounded-sm">
    <div class="modal-header py-1 px-2 bg-light"><h5 class="modal-title" style="font-size: 0.8rem;">Add Emergency Contact</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
    <form method="POST" action="{{ route('profile.emergency.store') }}">@csrf
        <div class="modal-body py-2 px-2">
            <div class="mb-2"><label>Full Name *</label><input type="text" name="full_name" class="form-control form-control-sm" required></div>
            <div class="mb-2"><label>Relationship</label><input type="text" name="relationship" class="form-control form-control-sm"></div>
            <div class="row g-2"><div class="col-6"><label>Primary Phone *</label><input type="text" name="phone_primary" class="form-control form-control-sm" required></div><div class="col-6"><label>Secondary Phone</label><input type="text" name="phone_secondary" class="form-control form-control-sm"></div></div>
            <div class="mb-2"><label>Email</label><input type="email" name="email" class="form-control form-control-sm"></div>
            <div class="mb-2"><label>Address</label><textarea name="address" rows="2" class="form-control form-control-sm"></textarea></div>
        </div>
        <div class="modal-footer py-1 px-2"><button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button><button type="submit" class="btn btn-primary btn-sm">Add Contact</button></div>
    </form>
</div></div></div>

<!-- Add Qualification Modal -->
<div class="modal fade" id="addQualificationModal" tabindex="-1"><div class="modal-dialog modal-dialog-centered"><div class="modal-content rounded-sm">
    <div class="modal-header py-1 px-2 bg-light"><h5 class="modal-title" style="font-size: 0.8rem;">Add Qualification</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
    <form method="POST" action="{{ route('profile.qualifications.store') }}">@csrf
        <div class="modal-body py-2 px-2">
            <div class="mb-2"><label>Qualification Name *</label><input type="text" name="qualification_name" class="form-control form-control-sm" required></div>
            <div class="mb-2"><label>Institution</label><input type="text" name="institution" class="form-control form-control-sm"></div>
            <div class="row g-2"><div class="col-6"><label>Year Obtained</label><select name="year_obtained" class="form-select form-select-sm"><option value="">Select</option>@for($y=date('Y'); $y>=1970; $y--)<option value="{{ $y }}">{{ $y }}</option>@endfor</select></div>
            <div class="col-6"><label>Qualification Type</label><select name="qualification_type" class="form-select form-select-sm"><option value="">Select</option><option value="phd">PhD</option><option value="masters">Masters</option><option value="bachelors">Bachelor's</option><option value="diploma">Diploma</option><option value="certificate">Certificate</option></select></div></div>
            <div class="form-check mt-2"><input type="checkbox" name="is_highest" class="form-check-input" value="1"><label class="form-check-label">Mark as Highest Qualification</label></div>
        </div>
        <div class="modal-footer py-1 px-2"><button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button><button type="submit" class="btn btn-primary btn-sm">Add Qualification</button></div>
    </form>
</div></div></div>

<!-- Add Skill Modal -->
<div class="modal fade" id="addSkillModal" tabindex="-1"><div class="modal-dialog modal-dialog-centered"><div class="modal-content rounded-sm">
    <div class="modal-header py-1 px-2 bg-light"><h5 class="modal-title" style="font-size: 0.8rem;">Add Skill</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
    <form method="POST" action="{{ route('profile.skills.store') }}">@csrf
        <div class="modal-body py-2 px-2">
            <div class="mb-2"><label>Skill Name *</label><input type="text" name="skill_name" class="form-control form-control-sm" required></div>
            <div class="row g-2"><div class="col-6"><label>Category</label><select name="skill_category" class="form-select form-select-sm"><option value="">Select</option><option value="technical">Technical</option><option value="soft">Soft Skills</option><option value="artistic">Artistic</option><option value="sports">Sports</option><option value="other">Other</option></select></div>
            <div class="col-6"><label>Proficiency</label><select name="proficiency_level" class="form-select form-select-sm"><option value="beginner">Beginner</option><option value="intermediate">Intermediate</option><option value="advanced">Advanced</option><option value="expert">Expert</option></select></div></div>
            <div class="mb-2"><label>Description</label><textarea name="description" rows="2" class="form-control form-control-sm"></textarea></div>
        </div>
        <div class="modal-footer py-1 px-2"><button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button><button type="submit" class="btn btn-primary btn-sm">Add Skill</button></div>
    </form>
</div></div></div>

<!-- Add Language Modal -->
<div class="modal fade" id="addLanguageModal" tabindex="-1"><div class="modal-dialog modal-dialog-centered"><div class="modal-content rounded-sm">
    <div class="modal-header py-1 px-2 bg-light"><h5 class="modal-title" style="font-size: 0.8rem;">Add Language</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
    <form method="POST" action="{{ route('profile.languages.store') }}">@csrf
        <div class="modal-body py-2 px-2">
            <div class="mb-2"><label>Language Name *</label><input type="text" name="language_name" class="form-control form-control-sm" required></div>
            <div class="row g-2"><div class="col-4"><label>Speaking</label><select name="proficiency_speak" class="form-select form-select-sm"><option value="none">None</option><option value="basic">Basic</option><option value="intermediate">Intermediate</option><option value="fluent">Fluent</option><option value="native">Native</option></select></div>
            <div class="col-4"><label>Reading</label><select name="proficiency_read" class="form-select form-select-sm"><option value="none">None</option><option value="basic">Basic</option><option value="intermediate">Intermediate</option><option value="fluent">Fluent</option><option value="native">Native</option></select></div>
            <div class="col-4"><label>Writing</label><select name="proficiency_write" class="form-select form-select-sm"><option value="none">None</option><option value="basic">Basic</option><option value="intermediate">Intermediate</option><option value="fluent">Fluent</option><option value="native">Native</option></select></div></div>
        </div>
        <div class="modal-footer py-1 px-2"><button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button><button type="submit" class="btn btn-primary btn-sm">Add Language</button></div>
    </form>
</div></div></div>

<!-- View Dependent Modal -->
<div class="modal fade" id="viewDependentModal" tabindex="-1"><div class="modal-dialog modal-dialog-centered"><div class="modal-content rounded-sm">
    <div class="modal-header py-1 px-2 bg-light"><h5 class="modal-title" style="font-size: 0.8rem;">Dependent Details</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
    <div class="modal-body py-2 px-2" id="dependentDetailsContent"></div>
    <div class="modal-footer py-1 px-2"><button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button></div>
</div></div></div>

<!-- View Address Modal -->
<div class="modal fade" id="viewAddressModal" tabindex="-1"><div class="modal-dialog modal-dialog-centered"><div class="modal-content rounded-sm">
    <div class="modal-header py-1 px-2 bg-light"><h5 class="modal-title" style="font-size: 0.8rem;">Address Details</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
    <div class="modal-body py-2 px-2" id="addressDetailsContent"></div>
    <div class="modal-footer py-1 px-2"><button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button></div>
</div></div></div>

<!-- View Emergency Modal -->
<div class="modal fade" id="viewEmergencyModal" tabindex="-1"><div class="modal-dialog modal-dialog-centered"><div class="modal-content rounded-sm">
    <div class="modal-header py-1 px-2 bg-light"><h5 class="modal-title" style="font-size: 0.8rem;">Emergency Contact Details</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
    <div class="modal-body py-2 px-2" id="emergencyDetailsContent"></div>
    <div class="modal-footer py-1 px-2"><button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button></div>
</div></div></div>

<!-- View Qualification Modal -->
<div class="modal fade" id="viewQualificationModal" tabindex="-1"><div class="modal-dialog modal-dialog-centered"><div class="modal-content rounded-sm">
    <div class="modal-header py-1 px-2 bg-light"><h5 class="modal-title" style="font-size: 0.8rem;">Qualification Details</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
    <div class="modal-body py-2 px-2" id="qualificationDetailsContent"></div>
    <div class="modal-footer py-1 px-2"><button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button></div>
</div></div></div>

<!-- View Skill Modal -->
<div class="modal fade" id="viewSkillModal" tabindex="-1"><div class="modal-dialog modal-dialog-centered"><div class="modal-content rounded-sm">
    <div class="modal-header py-1 px-2 bg-light"><h5 class="modal-title" style="font-size: 0.8rem;">Skill Details</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
    <div class="modal-body py-2 px-2" id="skillDetailsContent"></div>
    <div class="modal-footer py-1 px-2"><button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button></div>
</div></div></div>

<!-- View Language Modal -->
<div class="modal fade" id="viewLanguageModal" tabindex="-1"><div class="modal-dialog modal-dialog-centered"><div class="modal-content rounded-sm">
    <div class="modal-header py-1 px-2 bg-light"><h5 class="modal-title" style="font-size: 0.8rem;">Language Details</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
    <div class="modal-body py-2 px-2" id="languageDetailsContent"></div>
    <div class="modal-footer py-1 px-2"><button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button></div>
</div></div></div>

<!-- View Bank Account Modal -->
<div class="modal fade" id="viewBankAccountModal" tabindex="-1"><div class="modal-dialog modal-dialog-centered"><div class="modal-content rounded-sm">
    <div class="modal-header py-1 px-2 bg-light"><h5 class="modal-title" style="font-size: 0.8rem;">Bank Account Details</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
    <div class="modal-body py-2 px-2" id="bankAccountDetailsContent"></div>
    <div class="modal-footer py-1 px-2"><button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button></div>
</div></div></div>

<!-- Add Bank Account Modal -->
<div class="modal fade" id="addBankAccountModal" tabindex="-1"><div class="modal-dialog modal-dialog-centered"><div class="modal-content rounded-sm">
    <div class="modal-header py-1 px-2 bg-light"><h5 class="modal-title" style="font-size: 0.8rem;"><i class="ti ti-credit-card me-1"></i> Add Bank Account</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
    <form method="POST" action="{{ route('profile.bank-accounts.store') }}">@csrf
        <div class="modal-body py-2 px-2">
            <div class="mb-2"><label class="form-label" style="font-size: 0.65rem;">Bank Name *</label><input type="text" name="bank_name" class="form-control form-control-sm" required></div>
            <div class="mb-2"><label class="form-label" style="font-size: 0.65rem;">Account Name *</label><input type="text" name="account_name" class="form-control form-control-sm" required></div>
            <div class="mb-2"><label class="form-label" style="font-size: 0.65rem;">Account Number *</label><input type="text" name="account_number" class="form-control form-control-sm" required></div>
            <div class="form-check"><input type="checkbox" name="is_primary" class="form-check-input" value="1"><label class="form-check-label">Set as Primary Account</label></div>
        </div>
        <div class="modal-footer py-1 px-2"><button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button><button type="submit" class="btn btn-primary btn-sm">Add Account</button></div>
    </form>
</div></div></div>

<!-- Edit TIN/NSSF Modal -->
<div class="modal fade" id="editTinNssfModal" tabindex="-1"><div class="modal-dialog modal-dialog-centered"><div class="modal-content rounded-sm">
    <div class="modal-header py-1 px-2 bg-light"><h5 class="modal-title" style="font-size: 0.8rem;">Update TIN & NSSF</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
    <form method="POST" action="{{ route('profile.update') }}">@csrf @method('PUT')
        <div class="modal-body py-2 px-2">
            <div class="mb-2"><label class="form-label" style="font-size: 0.65rem;">TIN Number</label><input type="text" name="tin_number" class="form-control form-control-sm" value="{{ auth()->user()->tin_number ?? '' }}"></div>
            <div class="mb-2"><label class="form-label" style="font-size: 0.65rem;">NSSF Number</label><input type="text" name="nssf_number" class="form-control form-control-sm" value="{{ auth()->user()->nssf_number ?? '' }}"></div>
            <x-alert-info>
    TIN and NSSF numbers are used for payroll and tax purposes.
</x-alert-info>
        </div>
        <div class="modal-footer py-1 px-2"><button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button><button type="submit" class="btn btn-primary btn-sm">Save Changes</button></div>
    </form>
</div></div></div>

<script>
// Avatar preview
document.getElementById('avatarInput')?.addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) { document.getElementById('avatarPreview').src = e.target.result; };
        reader.readAsDataURL(file);
    }
});

// View functions
function viewDependent(id) {
    fetch(`/profile/dependents/${id}/json`).then(r=>r.json()).then(data=>{
        document.getElementById('dependentDetailsContent').innerHTML = `<table class="table table-sm table-bordered">
            <tr><td class="bg-light" style="width:40%">Full Name:</td><td><strong>${data.full_name}</strong></td></tr>
            <tr><td class="bg-light">Relationship:</td><td>${data.relationship}</td></tr>
            <tr><td class="bg-light">Date of Birth:</td><td>${data.date_of_birth || '-'}</td></tr>
            <tr><td class="bg-light">Phone:</td><td>${data.phone || '-'}</td></tr>
            <tr><td class="bg-light">Email:</td><td>${data.email || '-'}</td></tr>
            <tr><td class="bg-light">Emergency Contact:</td><td>${data.is_emergency_contact ? 'Yes' : 'No'}</td></tr>
            <tr><td class="bg-light">Beneficiary:</td><td>${data.is_beneficiary ? 'Yes' : 'No'}</td></tr>
            ${data.beneficiary_percentage ? `<tr><td class="bg-light">Beneficiary %:</td><td>${data.beneficiary_percentage}%</td></tr>` : ''}
            ${data.notes ? `<tr><td class="bg-light">Notes:</td><td>${data.notes}</td></tr>` : ''}
        </table>`;
        new bootstrap.Modal(document.getElementById('viewDependentModal')).show();
    });
}

function viewAddress(id) {
    fetch(`/profile/addresses/${id}/json`).then(r=>r.json()).then(data=>{
        document.getElementById('addressDetailsContent').innerHTML = `<table class="table table-sm table-bordered">
            <tr><td class="bg-light" style="width:40%">Address Type:</td><td>${data.address_type || '-'}</td></tr>
            <tr><td class="bg-light">Street:</td><td>${data.street || '-'}</td></tr>
            <tr><td class="bg-light">City:</td><td>${data.city || '-'}</td></tr>
            <tr><td class="bg-light">State:</td><td>${data.state || '-'}</td></tr>
            <tr><td class="bg-light">Postal Code:</td><td>${data.postal_code || '-'}</td></tr>
            <tr><td class="bg-light">Country:</td><td>${data.country || 'Tanzania'}</td></tr>
            <tr><td class="bg-light">Primary:</td><td>${data.is_primary ? 'Yes' : 'No'}</td></tr>
        </table>`;
        new bootstrap.Modal(document.getElementById('viewAddressModal')).show();
    });
}

function viewEmergency(id) {
    fetch(`/profile/emergency/${id}/json`).then(r=>r.json()).then(data=>{
        document.getElementById('emergencyDetailsContent').innerHTML = `<table class="table table-sm table-bordered">
            <tr><td class="bg-light" style="width:40%">Full Name:</td><td><strong>${data.full_name}</strong></td></tr>
            <tr><td class="bg-light">Relationship:</td><td>${data.relationship}</td></tr>
            <tr><td class="bg-light">Primary Phone:</td><td>${data.phone_primary}</td></tr>
            <tr><td class="bg-light">Secondary Phone:</td><td>${data.phone_secondary || '-'}</td></tr>
            <tr><td class="bg-light">Email:</td><td>${data.email || '-'}</td></tr>
            <tr><td class="bg-light">Address:</td><td>${data.address || '-'}</td></tr>
            <tr><td class="bg-light">Priority:</td><td>${data.priority || 5} (1=highest)</td></tr>
        </table>`;
        new bootstrap.Modal(document.getElementById('viewEmergencyModal')).show();
    });
}

function viewQualification(id) {
    fetch(`/profile/qualifications/${id}/json`).then(r=>r.json()).then(data=>{
        document.getElementById('qualificationDetailsContent').innerHTML = `<table class="table table-sm table-bordered">
            <tr><td class="bg-light" style="width:40%">Qualification:</td><td><strong>${data.qualification_name}</strong></td></tr>
            <tr><td class="bg-light">Institution:</td><td>${data.institution || '-'}</td></tr>
            <tr><td class="bg-light">Year:</td><td>${data.year_obtained || '-'}</td></tr>
            <tr><td class="bg-light">Type:</td><td>${data.qualification_type || '-'}</td></tr>
            <tr><td class="bg-light">Highest:</td><td>${data.is_highest ? 'Yes' : 'No'}</td></tr>
        </table>`;
        new bootstrap.Modal(document.getElementById('viewQualificationModal')).show();
    });
}

function viewSkill(id) {
    fetch(`/profile/skills/${id}/json`).then(r=>r.json()).then(data=>{
        document.getElementById('skillDetailsContent').innerHTML = `<table class="table table-sm table-bordered">
            <tr><td class="bg-light" style="width:40%">Skill:</td><td><strong>${data.skill_name}</strong></td></tr>
            <tr><td class="bg-light">Category:</td><td>${data.skill_category || '-'}</td></tr>
            <tr><td class="bg-light">Proficiency:</td><td>${data.proficiency_level}</td></tr>
            <tr><td class="bg-light">Description:</td><td>${data.description || '-'}</td></tr>
        <tr>`;
        new bootstrap.Modal(document.getElementById('viewSkillModal')).show();
    });
}

function viewLanguage(id) {
    fetch(`/profile/languages/${id}/json`).then(r=>r.json()).then(data=>{
        document.getElementById('languageDetailsContent').innerHTML = `<table class="table table-sm table-bordered">
            <tr><td class="bg-light" style="width:40%">Language:</td><td><strong>${data.language_name}</strong></td></tr>
            <tr><td class="bg-light">Speaking:</td><td>${data.proficiency_speak}</td></tr>
            <tr><td class="bg-light">Reading:</td><td>${data.proficiency_read}</td></tr>
            <tr><td class="bg-light">Writing:</td><td>${data.proficiency_write}</td></tr>
        </table>`;
        new bootstrap.Modal(document.getElementById('viewLanguageModal')).show();
    });
}

function viewBankAccount(id) {
    fetch(`/profile/bank/${id}/json`).then(r=>r.json()).then(data=>{
        document.getElementById('bankAccountDetailsContent').innerHTML = `<table class="table table-sm table-bordered">
            <tr><td class="bg-light" style="width:40%">Bank Name:</td><td><strong>${data.bank_name}</strong></td></tr>
            <tr><td class="bg-light">Account Name:</td><td>${data.account_name}</td></tr>
            <td><td class="bg-light">Account Number:</td><td>${data.account_number}</td></tr>
            <tr><td class="bg-light">Primary:</td><td>${data.is_primary ? 'Yes' : 'No'}</td></tr>
        </table>`;
        new bootstrap.Modal(document.getElementById('viewBankAccountModal')).show();
    });
}

// Delete functions
function deleteProfileItem(endpoint, message) {
    confirmAction(message, function () {
        fetch(endpoint, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content } }).then(() => location.reload());
    });
}
function deleteDependent(id) { deleteProfileItem(`/profile/dependents/${id}`, 'Delete this dependent?'); }
function deleteAddress(id) { deleteProfileItem(`/profile/addresses/${id}`, 'Delete this address?'); }
function deleteEmergency(id) { deleteProfileItem(`/profile/emergency/${id}`, 'Delete this emergency contact?'); }
function deleteQualification(id) { deleteProfileItem(`/profile/qualifications/${id}`, 'Delete this qualification?'); }
function deleteSkill(id) { deleteProfileItem(`/profile/skills/${id}`, 'Delete this skill?'); }
function deleteLanguage(id) { deleteProfileItem(`/profile/languages/${id}`, 'Delete this language?'); }
function deleteBankAccount(id) { deleteProfileItem(`/profile/bank/${id}`, 'Delete this bank account?'); }
</script>
@endsection