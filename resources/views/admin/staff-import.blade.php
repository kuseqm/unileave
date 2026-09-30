@extends('layouts.master')

@section('title', 'Import Staff')

@section('content')
<div class="container-fluid">
    
    <!-- Breadcrumb -->
    <div class="mb-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.staff') }}" class="text-decoration-none">Staff</a></li>
                <li class="breadcrumb-item active" aria-current="page">Import Staff</li>
            </ol>
        </nav>
    </div>
    
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
        <div>
            <h1 class="h3 mb-1">Import Staff</h1>
            <p class="text-muted">Bulk import staff members via Excel/CSV template</p>
        </div>
        <div>
            <a href="{{ route('admin.staff') }}" class="btn btn-outline-secondary">
                <i class="ti ti-arrow-left me-1"></i> Back to Staff List
            </a>
        </div>
    </div>
    
    <x-alert-success />
    
    <x-alert-danger />
    
    <div class="row g-4">
        
        <!-- ============================================ -->
        <!-- LEFT COLUMN: IMPORT FORM -->
        <!-- ============================================ -->
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-0 pt-4">
                    <h5 class="card-title mb-0">
                        <i class="ti ti-file-import me-2"></i> Import Staff Members
                    </h5>
                    <p class="text-muted small mt-1">Upload an Excel file with staff data</p>
                </div>
                <div class="card-body">
                    
                    <!-- Step 1: Hierarchical Selection -->
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Step 1: Select Location</label>
                        
                        <div class="mb-3">
                            <label class="form-label">University <span class="text-danger">*</span></label>
                            <select id="university_id" class="form-select" required>
                                <option value="">-- Select University --</option>
                                @foreach($universities ?? [] as $uni)
                                <option value="{{ $uni->id }}">{{ $uni->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Campus <span class="text-danger">*</span></label>
                            <select id="campus_id" class="form-select" disabled>
                                <option value="">-- Select University First --</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Department <span class="text-danger">*</span></label>
                            <select id="department_id" class="form-select" disabled>
                                <option value="">-- Select Campus First --</option>
                            </select>
                        </div>
                    </div>
                    
                    <!-- Step 2: Download Template -->
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Step 2: Download Template</label>
                        <button type="button" id="downloadTemplateBtn" class="btn btn-outline-primary w-100 py-2" disabled>
                            <i class="ti ti-download me-1"></i> Download Excel Template
                        </button>
                        <small class="text-muted d-block mt-1">Template will be pre-configured for your selected location</small>
                    </div>
                    
                    <hr>
                    
                    <!-- Step 3: Upload File -->
                    <div>
                        <label class="form-label fw-semibold">Step 3: Upload File</label>
                        <form action="{{ route('admin.staff-import.upload') }}" method="POST" enctype="multipart/form-data" id="importForm">
                            @csrf
                            <input type="hidden" name="university_id" id="upload_university_id">
                            <input type="hidden" name="campus_id" id="upload_campus_id">
                            <input type="hidden" name="department_id" id="upload_department_id">
                            
                            <div class="mb-3">
                                <input type="file" name="file" class="form-control @error('file') is-invalid @enderror" accept=".xlsx,.csv" required>
                                @error('file')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="d-flex justify-content-between mt-1">
                                    <small class="text-muted">Max 10MB, .xlsx or .csv only</small>
                                    <small class="text-muted">Recommended: up to 5,000 rows</small>
                                </div>
                            </div>
                            
                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input type="checkbox" name="test_mode" value="1" class="form-check-input" id="testMode">
                                        <label class="form-check-label" for="testMode">
                                            <i class="ti ti-test-pipe me-1"></i> Test Mode (Dry Run)
                                        </label>
                                        <div class="small text-muted">Validate only, don't save data</div>
                                    </div>
                                </div>
                            </div>
                            
                            <button type="submit" class="btn btn-primary w-100 py-2" id="uploadBtn" disabled>
                                <i class="ti ti-upload me-1"></i> Upload & Import
                            </button>
                        </form>
                    </div>
                    
                </div>
            </div>
        </div>
        
        <!-- ============================================ -->
        <!-- RIGHT COLUMN: INSTRUCTIONS & HELP -->
        <!-- ============================================ -->
        <div class="col-lg-5">
            
            <!-- Quick Tips Card -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">
                        <i class="ti ti-info-circle me-2"></i> Import Guidelines
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <h6><i class="ti ti-file me-1 text-primary"></i> Required Columns</h6>
                        <ul class="small text-muted mb-0">
                            <li><strong>Staff ID</strong> - Unique identifier (e.g., STF2024001)</li>
                            <li><strong>First Name</strong> - Staff's first name</li>
                            <li><strong>Last Name</strong> - Staff's last name</li>
                            <li><strong>Email</strong> - Valid email address</li>
                            <li><strong>Department</strong> - Must match existing department</li>
                            <li><strong>Campus</strong> - Must match existing campus</li>
                            <li><strong>Staff Category</strong> - academic/administrative/supporting</li>
                            <li><strong>Joining Date</strong> - Format: YYYY-MM-DD</li>
                            <li><strong>Phone</strong> - Optional phone number</li>
                        </ul>
                    </div>
                    
                    <div class="mb-3">
                        <h6><i class="ti ti-alert-triangle me-1 text-warning"></i> Important Notes</h6>
                        <ul class="small text-muted mb-0">
                            <li>Do not change column headers</li>
                            <li>Use "Test Mode" to validate first</li>
                            <li>Maximum file size: 10MB</li>
                            <li>Supports .xlsx and .csv formats</li>
                            <li>Duplicate Staff IDs or Emails will be skipped</li>
                        </ul>
                    </div>
                    
                    <x-alert-light class="mb-0">

                        <div class="d-flex align-items-start gap-2">
                            <i class="ti ti-help text-primary mt-1"></i>
                            <div>
                                <strong class="d-block">Need Help?</strong>
                                <small class="text-muted">Contact the system administrator for assistance</small>
                            </div>
                        
</x-alert-light>
                    </div>
                </div>
            </div>
            
            <!-- Progress Card (Hidden initially) -->
            <div id="importProgress" class="card border-0 shadow-sm" style="display: none;">
                <div class="card-header bg-info text-white">
                    <h5 class="card-title mb-0">
                        <i class="ti ti-loader me-2"></i> Import Progress
                    </h5>
                </div>
                <div class="card-body">
                    <div class="progress mb-3" style="height: 25px;">
                        <div id="progressBar" class="progress-bar progress-bar-striped progress-bar-animated" style="width: 0%">0%</div>
                    </div>
                    <div id="progressStatus" class="text-center">
                        <div class="spinner-border spinner-border-sm me-1 text-primary" role="status"></div>
                        <span>Initializing import...</span>
                    </div>
                </div>
            </div>
            
            <!-- Recent Imports Card -->
            <div class="card border-0 shadow-sm mt-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="ti ti-history me-2"></i> Recent Imports
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @forelse($history ?? [] as $import)
                        <div class="list-group-item">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="small text-muted">{{ $import->created_at->format('d M Y H:i') }}</div>
                                    <div class="fw-semibold">{{ \Str::limit($import->original_filename, 30) }}</div>
                                    <div class="small">
                                        <span class="text-success">{{ $import->successful_rows }}</span> / 
                                        <span class="text-danger">{{ $import->failed_rows }}</span> rows
                                    </div>
                                </div>
                                <div>
                                    @if($import->status == 'completed')
                                        <span class="badge bg-success">Completed</span>
                                    @elseif($import->status == 'processing')
                                        <span class="badge bg-info">Processing</span>
                                    @elseif($import->status == 'failed')
                                        <span class="badge bg-danger">Failed</span>
                                    @elseif($import->status == 'pending')
                                        <span class="badge bg-secondary">Pending</span>
                                    @endif
                                </div>
                            </div>
                            @if($import->status == 'completed' && $import->failed_rows > 0 && $import->error_report_path)
                            <div class="mt-2">
                                <a href="{{ route('admin.import.download-error', $import->id) }}" class="btn btn-sm btn-outline-danger">
                                    <i class="ti ti-file-text me-1"></i> Download Error Report
                                </a>
                            </div>
                            @endif
                        </div>
                        @empty
                        <div class="list-group-item text-center text-muted py-4">
                            No recent imports
                        </div>
                        @endforelse
                    </div>
                </div>
                @if(isset($history) && $history->count() > 0)
                <div class="card-footer bg-transparent text-center">
                    <a href="{{ route('admin.import.index') }}" class="small">View all imports →</a>
                </div>
                @endif
            </div>
            
        </div>
    </div>
</div>

<script>
let progressInterval = null;
let currentImportId = null;

// Get universities data
let universities = @json($universities ?? []);

// University change -> Load campuses
document.getElementById('university_id').addEventListener('change', function() {
    const universityId = this.value;
    const campusSelect = document.getElementById('campus_id');
    const departmentSelect = document.getElementById('department_id');
    
    if (universityId) {
        fetch(`/admin/import/campuses/${universityId}`)
            .then(response => response.json())
            .then(data => {
                campusSelect.innerHTML = '<option value="">-- Select Campus --</option>';
                data.forEach(campus => {
                    campusSelect.innerHTML += `<option value="${campus.id}">${campus.name}</option>`;
                });
                campusSelect.disabled = false;
                departmentSelect.innerHTML = '<option value="">-- Select Campus First --</option>';
                departmentSelect.disabled = true;
            });
    } else {
        campusSelect.disabled = true;
        departmentSelect.disabled = true;
    }
});

// Campus change -> Load departments
document.getElementById('campus_id').addEventListener('change', function() {
    const campusId = this.value;
    const departmentSelect = document.getElementById('department_id');
    
    if (campusId) {
        fetch(`/admin/import/departments/${campusId}`)
            .then(response => response.json())
            .then(data => {
                departmentSelect.innerHTML = '<option value="">-- Select Department --</option>';
                data.forEach(dept => {
                    departmentSelect.innerHTML += `<option value="${dept.id}">${dept.name}</option>`;
                });
                departmentSelect.disabled = false;
            });
    } else {
        departmentSelect.innerHTML = '<option value="">-- Select Campus First --</option>';
        departmentSelect.disabled = true;
    }
});

// Department change -> Enable download and upload
document.getElementById('department_id').addEventListener('change', function() {
    const universityId = document.getElementById('university_id').value;
    const campusId = document.getElementById('campus_id').value;
    const departmentId = this.value;
    
    const downloadBtn = document.getElementById('downloadTemplateBtn');
    const uploadBtn = document.getElementById('uploadBtn');
    
    if (universityId && campusId && departmentId) {
        downloadBtn.disabled = false;
        uploadBtn.disabled = false;
        
        document.getElementById('upload_university_id').value = universityId;
        document.getElementById('upload_campus_id').value = campusId;
        document.getElementById('upload_department_id').value = departmentId;
    } else {
        downloadBtn.disabled = true;
        uploadBtn.disabled = true;
    }
});

// Download template
document.getElementById('downloadTemplateBtn').addEventListener('click', function() {
    const universityId = document.getElementById('university_id').value;
    const campusId = document.getElementById('campus_id').value;
    const departmentId = document.getElementById('department_id').value;
    
    if (!universityId || !campusId || !departmentId) {
        alert('Please select University, Campus, and Department first');
        return;
    }
    
    window.location.href = `{{ route('admin.staff-import.template') }}?university_id=${universityId}&campus_id=${campusId}&department_id=${departmentId}`;
});

// Function to check progress
function checkProgress(importId) {
    fetch(`/admin/import/progress/${importId}`)
        .then(response => response.json())
        .then(data => {
            const progressBar = document.getElementById('progressBar');
            const progressStatus = document.getElementById('progressStatus');
            
            progressBar.style.width = data.percentage + '%';
            progressBar.innerHTML = data.percentage + '%';
            
            progressStatus.innerHTML = `
                <strong>Status:</strong> ${data.status}<br>
                <strong>Processed:</strong> ${data.successful_rows + data.failed_rows} of ${data.total_rows} rows<br>
                <strong>Successful:</strong> <span class="text-success">${data.successful_rows}</span><br>
                <strong>Failed:</strong> <span class="text-danger">${data.failed_rows}</span>
            `;
            
            if (data.status === 'completed') {
                if (progressInterval) clearInterval(progressInterval);
                progressBar.classList.remove('progress-bar-animated');
                progressStatus.innerHTML += '<br><span class="text-success"><i class="ti ti-check-circle me-1"></i> Import completed successfully!</span>';
                setTimeout(() => location.reload(), 3000);
            } else if (data.status === 'failed') {
                if (progressInterval) clearInterval(progressInterval);
                progressBar.classList.remove('progress-bar-animated');
                progressStatus.innerHTML += '<br><span class="text-danger"><i class="ti ti-alert-circle me-1"></i> Import failed!</span>';
            }
        });
}

// Form submission with progress tracking
document.getElementById('importForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const uploadBtn = document.getElementById('uploadBtn');
    
    uploadBtn.disabled = true;
    uploadBtn.innerHTML = '<div class="spinner-border spinner-border-sm me-1" role="status"></div> Uploading...';
    
    // Show progress card
    document.getElementById('importProgress').style.display = 'block';
    
    fetch('{{ route("admin.staff-import.upload") }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.import_id) {
            currentImportId = data.import_id;
            // Start polling for progress every 2 seconds
            progressInterval = setInterval(() => checkProgress(currentImportId), 2000);
            checkProgress(currentImportId);
            uploadBtn.innerHTML = '<i class="ti ti-check me-1"></i> Import Started';
        } else {
            throw new Error(data.message || 'Upload failed');
        }
    })
    .catch(error => {
        alert('Error: ' + error.message);
        uploadBtn.disabled = false;
        uploadBtn.innerHTML = '<i class="ti ti-upload me-1"></i> Upload & Import';
        document.getElementById('importProgress').style.display = 'none';
    });
});
</script>
@endsection