@extends('layouts.master')

@section('title', 'My Profile')

@section('content')
<div class="container-fluid px-2">
    
    <!-- Breadcrumb -->
    <x-breadcrumb :items="[
        ['name' => 'Profile', 'url' => '#']
    ]" />
    
    <!-- Page Header -->
    <x-page-header title="MY PROFILE" description="View and manage your account information">
        <!-- No action buttons needed -->
    </x-page-header>
    
    <!-- Statistics Cards - User Stats -->
    <div class="row g-2 mb-3">
        <x-stats-card value="{{ $totalLeaveRequests ?? 0 }}" label="Total Leave Requests" subtext="all time" />
        <x-stats-card value="{{ $pendingLeaveRequests ?? 0 }}" label="Pending" subtext="awaiting approval" color="warning" />
        <x-stats-card value="{{ $approvedLeaveRequests ?? 0 }}" label="Approved" subtext="granted" color="success" />
        <x-stats-card value="{{ $rejectedLeaveRequests ?? 0 }}" label="Rejected" subtext="denied" color="danger" />
    </div>
    
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
    
    <div class="row g-2">
        
        <!-- LEFT COLUMN: Profile Picture & User Info -->
        <div class="col-lg-4">
            <!-- Profile Picture Card -->
            <div class="card rounded-sm">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-camera text-primary me-1" style="font-size: 0.8rem;"></i>
                        <span style="font-size: 0.7rem; font-weight: 600;">Profile Picture</span>
                    </div>
                </div>
                <div class="card-body py-2 px-2 text-center">
                    <div class="position-relative d-inline-block">
                        <div class="rounded-circle mx-auto mb-2 d-flex align-items-center justify-content-center" style="width: 100px; height: 100px; background: #EFF6FF; border: 3px solid #3B82F6;">
                            @if(auth()->user()->avatar)
                                <img src="{{ Storage::url(auth()->user()->avatar) }}" alt="Avatar" style="width: 94px; height: 94px; border-radius: 50%; object-fit: cover;">
                            @else
                                <span class="fw-bold" style="font-size: 2rem; color: #3B82F6;">
                                    {{ strtoupper(substr(auth()->user()->first_name, 0, 1)) }}
                                    @if(auth()->user()->middle_name)
                                        {{ strtoupper(substr(auth()->user()->middle_name, 0, 1)) }}
                                    @endif
                                    {{ strtoupper(substr(auth()->user()->last_name, 0, 1)) }}
                                </span>
                            @endif
                        </div>
                        <button type="button" class="btn btn-sm btn-primary rounded-sm position-absolute" style="bottom: 10px; right: -10px; font-size: 0.6rem; padding: 0.2rem 0.4rem;" data-bs-toggle="modal" data-bs-target="#avatarModal">
                            <i class="ti ti-edit"></i>
                        </button>
                    </div>
                    <div class="mt-2">
                        <h5 class="mb-0" style="font-size: 0.9rem;">
                            {{ auth()->user()->first_name }} 
                            @if(auth()->user()->middle_name)
                                {{ auth()->user()->middle_name }} 
                            @endif
                            {{ auth()->user()->last_name }}
                        </h5>
                        <p class="text-muted small mb-0">{{ auth()->user()->staff_id ?? 'No ID' }}</p>
                        @foreach(auth()->user()->getRoleNames() as $role)
                            <span class="badge bg-primary-subtle text-primary rounded-sm mt-1" style="font-size: 0.6rem;">{{ ucfirst($role) }}</span>
                        @endforeach
                    </div>
                </div>
            </div>
            
            <!-- User Statistics Card -->
            <div class="card rounded-sm mt-2">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-chart-bar text-success me-1" style="font-size: 0.8rem;"></i>
                        <span style="font-size: 0.7rem; font-weight: 600;">Account Statistics</span>
                    </div>
                </div>
                <div class="card-body py-2 px-2">
                    <div class="mb-2">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted" style="font-size: 0.65rem;">Member Since</span>
                            <span class="fw-semibold" style="font-size: 0.7rem;">{{ auth()->user()->created_at->format('d M Y') }}</span>
                        </div>
                        <div class="progress mt-1" style="height: 2px;">
                            <div class="progress-bar bg-primary" style="width: 100%"></div>
                        </div>
                    </div>
                    <div class="mb-2">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted" style="font-size: 0.65rem;">Last Login</span>
                            <span class="fw-semibold" style="font-size: 0.7rem;">{{ auth()->user()->last_login_at ? \Carbon\Carbon::parse(auth()->user()->last_login_at)->format('d M Y H:i') : 'First login' }}</span>
                        </div>
                        <div class="progress mt-1" style="height: 2px;">
                            <div class="progress-bar bg-info" style="width: 75%"></div>
                        </div>
                    </div>
                    <div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted" style="font-size: 0.65rem;">Account Status</span>
                            <span class="badge bg-success-subtle text-success rounded-sm" style="font-size: 0.6rem;">{{ ucfirst(auth()->user()->status ?? 'Active') }}</span>
                        </div>
                        <div class="progress mt-1" style="height: 2px;">
                            <div class="progress-bar bg-success" style="width: 100%"></div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Department Info Card -->
            <div class="card rounded-sm mt-2">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-building text-info me-1" style="font-size: 0.8rem;"></i>
                        <span style="font-size: 0.7rem; font-weight: 600;">Organization</span>
                    </div>
                </div>
                <div class="card-body py-2 px-2">
                    <div class="mb-2">
                        <div class="text-muted" style="font-size: 0.65rem;">University</div>
                        <div class="fw-semibold" style="font-size: 0.75rem;">{{ auth()->user()->university->name ?? 'Not Assigned' }}</div>
                    </div>
                    <div class="mb-2">
                        <div class="text-muted" style="font-size: 0.65rem;">Campus</div>
                        <div class="fw-semibold" style="font-size: 0.75rem;">{{ auth()->user()->campus->name ?? 'Not Assigned' }}</div>
                    </div>
                    <div>
                        <div class="text-muted" style="font-size: 0.65rem;">Department</div>
                        <div class="fw-semibold" style="font-size: 0.75rem;">{{ auth()->user()->department->name ?? 'Not Assigned' }}</div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- RIGHT COLUMN: Forms -->
        <div class="col-lg-8">
            <!-- Personal Information Card -->
            <div class="card rounded-sm">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-user text-primary me-1" style="font-size: 0.8rem;"></i>
                        <span style="font-size: 0.7rem; font-weight: 600;">Personal Information</span>
                    </div>
                </div>
                <div class="card-body py-2 px-2">
                    <form action="{{ route('admin.profile.update') }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row g-1">
                            <div class="col-md-4 mb-1">
                                <label class="form-label" style="font-size: 0.65rem;">First Name <span class="text-danger">*</span></label>
                                <input type="text" 
                                       name="first_name" 
                                       class="form-control form-control-sm rounded-sm @error('first_name') is-invalid @enderror" 
                                       value="{{ old('first_name', auth()->user()->first_name) }}"
                                       style="font-size: 0.7rem; padding: 0.2rem 0.3rem;"
                                       required>
                                @error('first_name')
                                    <div class="invalid-feedback" style="font-size: 0.65rem;">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-1">
                                <label class="form-label" style="font-size: 0.65rem;">Middle Name</label>
                                <input type="text" 
                                       name="middle_name" 
                                       class="form-control form-control-sm rounded-sm @error('middle_name') is-invalid @enderror" 
                                       value="{{ old('middle_name', auth()->user()->middle_name) }}"
                                       style="font-size: 0.7rem; padding: 0.2rem 0.3rem;">
                                @error('middle_name')
                                    <div class="invalid-feedback" style="font-size: 0.65rem;">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-1">
                                <label class="form-label" style="font-size: 0.65rem;">Last Name <span class="text-danger">*</span></label>
                                <input type="text" 
                                       name="last_name" 
                                       class="form-control form-control-sm rounded-sm @error('last_name') is-invalid @enderror" 
                                       value="{{ old('last_name', auth()->user()->last_name) }}"
                                       style="font-size: 0.7rem; padding: 0.2rem 0.3rem;"
                                       required>
                                @error('last_name')
                                    <div class="invalid-feedback" style="font-size: 0.65rem;">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="mb-1">
                            <label class="form-label" style="font-size: 0.65rem;">Email Address <span class="text-danger">*</span></label>
                            <input type="email" 
                                   name="email" 
                                   class="form-control form-control-sm rounded-sm @error('email') is-invalid @enderror" 
                                   value="{{ old('email', auth()->user()->email) }}"
                                   style="font-size: 0.7rem; padding: 0.2rem 0.3rem;"
                                   required>
                            @error('email')
                                <div class="invalid-feedback" style="font-size: 0.65rem;">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="row g-1">
                            <div class="col-md-6 mb-1">
                                <label class="form-label" style="font-size: 0.65rem;">Phone Number</label>
                                <input type="text" 
                                       name="phone" 
                                       class="form-control form-control-sm rounded-sm @error('phone') is-invalid @enderror" 
                                       value="{{ old('phone', auth()->user()->phone) }}"
                                       style="font-size: 0.7rem; padding: 0.2rem 0.3rem;">
                                @error('phone')
                                    <div class="invalid-feedback" style="font-size: 0.65rem;">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-1">
                                <label class="form-label" style="font-size: 0.65rem;">Staff ID</label>
                                <input type="text" 
                                       class="form-control form-control-sm rounded-sm" 
                                       value="{{ auth()->user()->staff_id ?? 'N/A' }}"
                                       style="font-size: 0.7rem; padding: 0.2rem 0.3rem; background: #f5f5f5;"
                                       readonly>
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-end mt-2">
                            <button type="submit" class="btn btn-primary btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.25rem 0.5rem;">
                                <i class="ti ti-device-floppy me-1"></i> Update Profile
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Change Password Card -->
            <div class="card rounded-sm mt-2">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-lock text-warning me-1" style="font-size: 0.8rem;"></i>
                        <span style="font-size: 0.7rem; font-weight: 600;">Change Password</span>
                    </div>
                </div>
                <div class="card-body py-2 px-2">
                    <form action="{{ route('admin.profile.password.update') }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-1">
                            <label class="form-label" style="font-size: 0.65rem;">Current Password <span class="text-danger">*</span></label>
                            <input type="password" 
                                   name="current_password" 
                                   class="form-control form-control-sm rounded-sm @error('current_password') is-invalid @enderror"
                                   style="font-size: 0.7rem; padding: 0.2rem 0.3rem;"
                                   required>
                            @error('current_password')
                                <div class="invalid-feedback" style="font-size: 0.65rem;">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="row g-1">
                            <div class="col-md-6 mb-1">
                                <label class="form-label" style="font-size: 0.65rem;">New Password <span class="text-danger">*</span></label>
                                <input type="password" 
                                       name="password" 
                                       class="form-control form-control-sm rounded-sm @error('password') is-invalid @enderror"
                                       style="font-size: 0.7rem; padding: 0.2rem 0.3rem;"
                                       required>
                                @error('password')
                                    <div class="invalid-feedback" style="font-size: 0.65rem;">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-1">
                                <label class="form-label" style="font-size: 0.65rem;">Confirm Password <span class="text-danger">*</span></label>
                                <input type="password" 
                                       name="password_confirmation" 
                                       class="form-control form-control-sm rounded-sm"
                                       style="font-size: 0.7rem; padding: 0.2rem 0.3rem;"
                                       required>
                            </div>
                        </div>
                        
                        <div class="alert alert-info py-1 px-2 mb-1 rounded-sm" style="font-size: 0.6rem;">
                            <i class="ti ti-info-circle me-1"></i> Password must be at least 8 characters long.
                        </div>
                        
                        <div class="d-flex justify-content-end mt-1">
                            <button type="submit" class="btn btn-warning btn-sm rounded-sm" style="font-size: 0.7rem; padding: 0.25rem 0.5rem;">
                                <i class="ti ti-key me-1"></i> Change Password
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Recent Activity Card -->
            <div class="card rounded-sm mt-2">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-history text-info me-1" style="font-size: 0.8rem;"></i>
                        <span style="font-size: 0.7rem; font-weight: 600;">Recent Activity</span>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @forelse($recentActivities ?? [] as $activity)
                        <div class="list-group-item d-flex align-items-center gap-2 py-1 px-2" style="border-left: none; border-right: none;">
                            <div class="rounded-circle bg-{{ $activity->type == 'submitted' ? 'primary' : ($activity->type == 'approved' ? 'success' : 'danger') }}-subtle p-1" style="width: 24px; height: 24px; display: flex; align-items: center; justify-content: center;">
                                <i class="ti ti-{{ $activity->type == 'submitted' ? 'file' : ($activity->type == 'approved' ? 'check' : 'x') }}" style="font-size: 0.7rem;"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="fw-semibold" style="font-size: 0.7rem;">{{ $activity->description ?? 'Leave Request ' . ucfirst($activity->type) }}</div>
                                <div class="text-muted" style="font-size: 0.6rem;">{{ $activity->created_at->diffForHumans() }}</div>
                            </div>
                            @if($activity->status)
                            <span class="badge bg-{{ $activity->status == 'approved' ? 'success' : ($activity->status == 'pending' ? 'warning' : 'danger') }}-subtle text-{{ $activity->status == 'approved' ? 'success' : ($activity->status == 'pending' ? 'warning' : 'danger') }} rounded-sm" style="font-size: 0.55rem;">{{ ucfirst($activity->status) }}</span>
                            @endif
                        </div>
                        @empty
                        <div class="list-group-item text-center py-2 text-muted" style="font-size: 0.7rem;">
                            No recent activity
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Avatar Upload Modal -->
<div class="modal fade" id="avatarModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-sm">
            <div class="modal-header py-1 px-2">
                <h5 class="modal-title" style="font-size: 0.8rem;">Update Profile Picture</h5>
                <button type="button" class="btn-close py-0" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.profile.avatar.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body py-2 px-2">
                    <div class="text-center mb-2">
                        <div class="rounded-circle mx-auto mb-2 d-flex align-items-center justify-content-center" style="width: 120px; height: 120px; background: #EFF6FF; border: 2px solid #3B82F6;" id="avatarPreviewContainer">
                            @if(auth()->user()->avatar)
                                <img src="{{ Storage::url(auth()->user()->avatar) }}" id="avatarPreview" style="width: 116px; height: 116px; border-radius: 50%; object-fit: cover;">
                            @else
                                <span class="fw-bold" id="avatarPreviewText" style="font-size: 2.5rem; color: #3B82F6;">
                                    {{ strtoupper(substr(auth()->user()->first_name, 0, 1)) }}
                                    @if(auth()->user()->middle_name)
                                        {{ strtoupper(substr(auth()->user()->middle_name, 0, 1)) }}
                                    @endif
                                    {{ strtoupper(substr(auth()->user()->last_name, 0, 1)) }}
                                </span>
                            @endif
                        </div>
                        <input type="file" name="avatar" id="avatarInput" class="form-control form-control-sm rounded-sm" accept="image/*" style="font-size: 0.7rem;">
                        <small class="text-muted" style="font-size: 0.6rem;">Max size: 2MB. Allowed: JPG, PNG, GIF</small>
                    </div>
                </div>
                <div class="modal-footer py-1 px-2">
                    <button type="button" class="btn btn-secondary btn-sm rounded-sm" data-bs-dismiss="modal" style="font-size: 0.7rem;">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm rounded-sm" style="font-size: 0.7rem;">Upload</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Avatar preview
document.getElementById('avatarInput')?.addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const previewContainer = document.getElementById('avatarPreviewContainer');
            const previewText = document.getElementById('avatarPreviewText');
            const previewImg = document.getElementById('avatarPreview');
            
            if (previewImg) {
                previewImg.src = e.target.result;
                previewImg.style.display = 'block';
            } else {
                const img = document.createElement('img');
                img.id = 'avatarPreview';
                img.src = e.target.result;
                img.style.width = '116px';
                img.style.height = '116px';
                img.style.borderRadius = '50%';
                img.style.objectFit = 'cover';
                if (previewText) previewText.style.display = 'none';
                previewContainer.appendChild(img);
            }
        };
        reader.readAsDataURL(file);
    }
});
</script>
@endsection