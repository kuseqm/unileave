

<?php $__env->startSection('title', 'Import Staff'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid px-2">
    
    <!-- Breadcrumb -->
    <div class="row mb-2">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0" style="font-size: 0.75rem;">
                    <li class="breadcrumb-item"><a href="<?php echo e(route('dashboard')); ?>" class="text-muted">Home</a></li>
                    <li class="breadcrumb-item"><a href="<?php echo e(route('staff.index')); ?>" class="text-muted">Staff</a></li>
                    <li class="breadcrumb-item active">Import Staff</li>
                </ol>
            </nav>
        </div>
    </div>
    
    <!-- Page Header Card -->
    <div class="card rounded-sm mb-2">
        <div class="card-body py-2 px-3">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <h1 class="h5 mb-0" style="color: #3B82F6; font-weight: 700;">IMPORT STAFF</h1>
                    <p class="text-muted mb-0" style="font-size: 0.7rem;">Bulk import staff members via Excel/CSV template</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="<?php echo e(route('staff.index')); ?>" class="btn btn-sm btn-outline-secondary rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">
                        <i class="ti ti-arrow-left me-1"></i> Back
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <?php if(session('success')): ?>
    <div class="alert alert-success alert-dismissible fade show py-1 px-2 mb-2 rounded-sm" style="font-size: 0.7rem;" role="alert">
        <i class="ti ti-check-circle me-1"></i> <?php echo e(session('success')); ?>

        <button type="button" class="btn-close py-0" style="font-size: 0.6rem;" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>
    
    <?php if(session('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show py-1 px-2 mb-2 rounded-sm" style="font-size: 0.7rem;" role="alert">
        <i class="ti ti-alert-circle me-1"></i> <?php echo e(session('error')); ?>

        <button type="button" class="btn-close py-0" style="font-size: 0.6rem;" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>
    
    <!-- Stats Cards Row -->
    <?php if(auth()->check() && auth()->user()->hasAnyRole('admin|hr-officer')): ?>
    <div class="row g-2 mb-3">
        <div class="col-6 col-md-3">
            <div class="card rounded-sm text-center">
                <div class="card-body py-2 px-2">
                    <div class="text-primary fw-bold" style="font-size: 1.4rem;"><?php echo e($stats['total_imports']); ?></div>
                    <div class="text-muted" style="font-size: 0.7rem;">Total Imports</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card rounded-sm text-center">
                <div class="card-body py-2 px-2">
                    <div class="text-success fw-bold" style="font-size: 1.4rem;"><?php echo e($stats['completed_imports'] ?? 0); ?></div>
                    <div class="text-muted" style="font-size: 0.7rem;">Completed</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card rounded-sm text-center">
                <div class="card-body py-2 px-2">
                    <div class="text-warning fw-bold" style="font-size: 1.4rem;"><?php echo e($stats['processing_imports'] ?? 0); ?></div>
                    <div class="text-muted" style="font-size: 0.7rem;">Processing</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card rounded-sm text-center">
                <div class="card-body py-2 px-2">
                    <div class="text-danger fw-bold" style="font-size: 1.4rem;"><?php echo e($stats['failed_imports']); ?></div>
                    <div class="text-muted" style="font-size: 0.7rem;">Failed</div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
    
    <!-- Search Card -->
    <div class="card rounded-sm mb-2">
        <div class="card-header py-1 px-2 bg-light">
            <div class="d-flex align-items-center">
                <i class="ti ti-search text-muted me-1" style="font-size: 0.8rem;"></i>
                <span style="font-size: 0.7rem; font-weight: 600;">Search Import History</span>
            </div>
        </div>
        <div class="card-body py-2 px-2">
            <div class="row g-1 align-items-end">
                <div class="col-md-3">
                    <label class="form-label" style="font-size: 0.65rem;">Date From</label>
                    <input type="date" id="search_date_from" class="form-control form-control-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.3rem;">
                </div>
                <div class="col-md-3">
                    <label class="form-label" style="font-size: 0.65rem;">Date To</label>
                    <input type="date" id="search_date_to" class="form-control form-control-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.3rem;">
                </div>
                <div class="col-md-3">
                    <label class="form-label" style="font-size: 0.65rem;">Status</label>
                    <select id="search_status" class="form-select form-select-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.3rem;">
                        <option value="">All</option>
                        <option value="completed">Completed</option>
                        <option value="processing">Processing</option>
                        <option value="failed">Failed</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <button id="searchBtn" class="btn btn-primary btn-sm w-100 rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.3rem;">
                        <i class="ti ti-search me-1"></i> Search
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Two Column Layout -->
    <div class="row g-2">
        
        <!-- LEFT CARD: Import Form -->
        <div class="col-lg-5">
            <div class="card rounded-sm">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-file-import text-primary me-1" style="font-size: 0.8rem;"></i>
                        <span style="font-size: 0.7rem; font-weight: 600;">Import Staff</span>
                    </div>
                </div>
                <div class="card-body py-2 px-2">
                    
                    <!-- Step 1: Select Hierarchy -->
                    <div class="mb-2">
                        <label class="form-label" style="font-size: 0.65rem; font-weight: 600;">Step 1: Select Location</label>
                        
                        <div class="mb-1">
                            <label class="form-label" style="font-size: 0.65rem;">University</label>
                            <select id="university_id" name="university_id" class="form-select form-select-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.3rem;" required>
                                <option value="">Select University</option>
                                <?php $__currentLoopData = $universities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $uni): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($uni->id); ?>" <?php echo e(session('import_university_id') == $uni->id ? 'selected' : ''); ?>><?php echo e($uni->name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        
                        <div class="mb-1">
                            <label class="form-label" style="font-size: 0.65rem;">Campus</label>
                            <select id="campus_id" name="campus_id" class="form-select form-select-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.3rem;" disabled>
                                <option value="">Select University First</option>
                            </select>
                        </div>
                        
                        <div class="mb-1">
                            <label class="form-label" style="font-size: 0.65rem;">School/Faculty</label>
                            <select id="school_id" name="school_id" class="form-select form-select-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.3rem;" disabled>
                                <option value="">Select Campus First</option>
                            </select>
                        </div>
                        
                        <div class="mb-1">
                            <label class="form-label" style="font-size: 0.65rem;">Department</label>
                            <select id="department_id" name="department_id" class="form-select form-select-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.3rem;" disabled>
                                <option value="">Select School First</option>
                            </select>
                        </div>
                    </div>
                    
                    <!-- Step 2: Download Template -->
                    <div class="mb-2">
                        <label class="form-label" style="font-size: 0.65rem; font-weight: 600;">Step 2: Download Template</label>
                        <button type="button" id="downloadTemplateBtn" class="btn btn-outline-primary btn-sm w-100 rounded-sm" style="font-size: 0.7rem; padding: 0.25rem 0.3rem;" disabled>
                            <i class="ti ti-download me-1"></i> Download Template
                        </button>
                        <small class="text-muted d-block mt-0" style="font-size: 0.6rem;">Fill your staff data in this template</small>
                    </div>
                    
                    <!-- Step 3: Upload File -->
                    <div>
                        <label class="form-label" style="font-size: 0.65rem; font-weight: 600;">Step 3: Upload File</label>
                        <form id="uploadForm" action="<?php echo e(route('admin.import.staff.upload')); ?>" method="POST" enctype="multipart/form-data">
                            <?php echo csrf_field(); ?>
                            <input type="hidden" name="university_id" id="upload_university_id">
                            <input type="hidden" name="campus_id" id="upload_campus_id">
                            <input type="hidden" name="school_id" id="upload_school_id">
                            <input type="hidden" name="department_id" id="upload_department_id">
                            
                            <div class="mb-1">
                                <input type="file" name="file" class="form-control form-control-sm rounded-sm" style="font-size: 0.7rem; padding: 0.2rem 0.3rem;" accept=".xlsx,.csv" required>
                                <small class="text-muted" style="font-size: 0.6rem;">Max 10MB, .xlsx or .csv only</small>
                            </div>
                            
                            <div class="form-check mb-1">
                                <input type="checkbox" name="test_mode" value="1" class="form-check-input" id="test_mode" style="margin-top: 0.15rem;">
                                <label class="form-check-label" for="test_mode" style="font-size: 0.65rem;">
                                    Test Mode (Dry Run) - Validate only, don't save data
                                </label>
                            </div>
                            
                            <button type="submit" class="btn btn-primary btn-sm w-100 rounded-sm" id="uploadBtn" style="font-size: 0.7rem; padding: 0.25rem 0.3rem;" disabled>
                                <i class="ti ti-upload me-1"></i> Upload & Import
                            </button>
                        </form>
                    </div>
                    
                </div>
            </div>
        </div>
        
        <!-- RIGHT CARD: Instructions & Guide (RESTORED) -->
        <div class="col-lg-7">
            <div class="card rounded-sm">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-info-circle text-info me-1" style="font-size: 0.8rem;"></i>
                        <span style="font-size: 0.7rem; font-weight: 600;">How to Import Staff</span>
                    </div>
                </div>
                <div class="card-body py-2 px-2">
                    
                    <!-- Step by Step Guide -->
                    <div class="mb-2">
                        <div class="fw-semibold mb-1" style="font-size: 0.75rem; color: #3B82F6;">
                            <i class="ti ti-steps me-1"></i> Step-by-Step Guide
                        </div>
                        <ol class="mb-0 ps-3" style="font-size: 0.7rem; line-height: 1.6;">
                            <li>Select <strong>University</strong> → <strong>Campus</strong> → <strong>School/Faculty</strong> → <strong>Department</strong></li>
                            <li>Click <strong>"Download Template"</strong> to get the Excel file</li>
                            <li>Open the downloaded Excel file</li>
                            <li>Fill in your staff data starting from <strong>Row 3</strong> (Row 2 is an example - delete it or overwrite)</li>
                            <li>Save the file as <strong>.xlsx</strong> or <strong>.csv</strong></li>
                            <li>Click <strong>"Browse"</strong> and select your filled file</li>
                            <li>(Optional) Check <strong>"Test Mode"</strong> to validate without saving</li>
                            <li>Click <strong>"Upload & Import"</strong> to start the import</li>
                        </ol>
                    </div>
                    
                    <!-- Column Descriptions Table -->
                    <div class="mb-2">
                        <div class="fw-semibold mb-1" style="font-size: 0.75rem; color: #3B82F6;">
                            <i class="ti ti-table me-1"></i> Column Descriptions
                        </div>
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered mb-0" style="font-size: 0.65rem;">
                                <thead>
                                    <tr class="bg-light">
                                        <th class="py-1" width="30">#</th>
                                        <th class="py-1">Column Name</th>
                                        <th class="py-1">Required</th>
                                        <th class="py-1">Description / Format</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr><td class="py-1">1</td><td class="py-1 fw-semibold">Staff ID</td><td class="py-1">Optional</td><td class="py-1">Leave empty to auto-generate (Format: STF0001)</td></tr>
                                    <tr><td class="py-1">2</td><td class="py-1 fw-semibold">Title</td><td class="py-1">Optional</td><td class="py-1">Values: <code>Mr, Mrs, Ms, Dr, Prof, Eng, Rev</code></td></tr>
                                    <tr><td class="py-1">3</td><td class="py-1 fw-semibold">First Name</td><td class="py-1"><span class="text-danger">Required</span></td><td class="py-1">Staff first name</td></tr>
                                    <tr><td class="py-1">4</td><td class="py-1 fw-semibold">Middle Name</td><td class="py-1">Optional</td><td class="py-1">Staff middle name (if any)</td></tr>
                                    <tr><td class="py-1">5</td><td class="py-1 fw-semibold">Last Name</td><td class="py-1"><span class="text-danger">Required</span></td><td class="py-1">Staff last name/surname</td></tr>
                                    <tr><td class="py-1">6</td><td class="py-1 fw-semibold">Email</td><td class="py-1"><span class="text-danger">Required</span></td><td class="py-1">Must be unique email address</td></tr>
                                    <tr><td class="py-1">7</td><td class="py-1 fw-semibold">Staff Category</td><td class="py-1"><span class="text-danger">Required</span></td><td class="py-1">Values: <code>academic</code>, <code>administrative</code>, <code>supporting</code></td></tr>
                                    <tr><td class="py-1">8</td><td class="py-1 fw-semibold">Joining Date</td><td class="py-1"><span class="text-danger">Required</span></td><td class="py-1">Format: <code>YYYY-MM-DD</code> (e.g., 2024-01-15)</td></tr>
                                    <tr><td class="py-1">9</td><td class="py-1 fw-semibold">Phone</td><td class="py-1">Optional</td><td class="py-1">Include country code (e.g., +255712345678)</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <!-- Important Notes -->
                    <div class="mb-2">
                        <div class="fw-semibold mb-1" style="font-size: 0.75rem; color: #3B82F6;">
                            <i class="ti ti-alert-circle me-1"></i> Important Notes
                        </div>
                        <ul class="mb-0 ps-3" style="font-size: 0.7rem; line-height: 1.6;">
                            <li><i class="ti ti-check text-success me-1"></i> Maximum file size: <strong>10MB</strong></li>
                            <li><i class="ti ti-check text-success me-1"></i> Supported formats: <strong>.xlsx, .csv</strong></li>
                            <li><i class="ti ti-check text-success me-1"></i> Delete the <strong>example row (Row 2)</strong> before uploading</li>
                            <li><i class="ti ti-check text-success me-1"></i> Use <strong>Test Mode</strong> first to validate your data</li>
                            <li><i class="ti ti-check text-success me-1"></i> Failed rows will be reported in an error file</li>
                            <li><i class="ti ti-check text-success me-1"></i> Temporary password for new staff: <code>Temp@123</code></li>
                        </ul>
                    </div>
                    
                    <!-- Quick Tips -->
                    <div class="alert alert-info py-1 px-2 mb-0 rounded-sm" style="font-size: 0.65rem;">
                        <i class="ti ti-lightbulb me-1"></i>
                        <strong>Pro Tip:</strong> Always use <strong>Test Mode</strong> on your first attempt to catch any errors before actual import!
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- BOTTOM SECTION: Import History -->
    <div class="row g-2 mt-2">
        <div class="col-12">
            <div class="card rounded-sm">
                <div class="card-header py-1 px-2 bg-light d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-history text-info me-1" style="font-size: 0.8rem;"></i>
                        <span style="font-size: 0.7rem; font-weight: 600;">Import History (Downloads & Uploads)</span>
                    </div>
                    <?php if(auth()->check() && auth()->user()->hasRole('admin')): ?>
                    <div class="dropdown">
                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle rounded-sm" type="button" data-bs-toggle="dropdown" style="font-size: 0.65rem; padding: 0.15rem 0.4rem;">
                            <i class="ti ti-chart-pie me-1"></i> Quarterly Report
                        </button>
                        <div class="dropdown-menu dropdown-menu-end rounded-sm">
                            <form action="<?php echo e(route('admin.import.quarterly-report')); ?>" method="GET" class="px-2 py-1">
                                <select name="year" class="form-select form-select-sm mb-1 rounded-sm" style="font-size: 0.7rem;">
                                    <option value="2025">2025</option>
                                    <option value="2026" selected>2026</option>
                                    <option value="2027">2027</option>
                                </select>
                                <select name="quarter" class="form-select form-select-sm mb-1 rounded-sm" style="font-size: 0.7rem;">
                                    <option value="1">Q1 (Jan-Mar)</option>
                                    <option value="2">Q2 (Apr-Jun)</option>
                                    <option value="3">Q3 (Jul-Sep)</option>
                                    <option value="4">Q4 (Oct-Dec)</option>
                                </select>
                                <button type="submit" class="btn btn-primary btn-sm w-100 rounded-sm" style="font-size: 0.7rem;">Generate</button>
                            </form>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm mb-0" style="font-size: 0.75rem;">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-2 py-1" width="40">SN</th>
                                    <th class="py-1" width="90">Status</th>
                                    <th class="py-1" width="90">Operation</th>
                                    <th class="py-1">Filename</th>
                                    <th class="py-1" width="100">Date</th>
                                    <th class="pe-2 py-1" width="120">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $history; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td class="ps-2 py-1"><?php echo e($loop->iteration); ?></td>
                                    <td class="py-1">
                                        <?php if($item->status == 'completed'): ?>
                                            <span class="badge bg-success rounded-sm" style="font-size: 0.6rem;">Completed</span>
                                        <?php elseif($item->status == 'processing'): ?>
                                            <span class="badge bg-info rounded-sm" style="font-size: 0.6rem;">Processing</span>
                                        <?php elseif($item->status == 'failed'): ?>
                                            <span class="badge bg-danger rounded-sm" style="font-size: 0.6rem;">Failed</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary rounded-sm" style="font-size: 0.6rem;"><?php echo e(ucfirst($item->status)); ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="py-1">
                                        <?php if($item->operation_type == 'download'): ?>
                                            <span class="badge bg-info-subtle text-info rounded-sm" style="font-size: 0.6rem;"><i class="ti ti-download me-1"></i> Download</span>
                                        <?php else: ?>
                                            <span class="badge bg-primary-subtle text-primary rounded-sm" style="font-size: 0.6rem;"><i class="ti ti-upload me-1"></i> Upload</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="py-1" style="max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                        <span title="<?php echo e($item->original_filename); ?>"><?php echo e(Str::limit($item->original_filename, 35)); ?></span>
                                    </td>
                                    <td class="py-1"><?php echo e($item->created_at->format('d M Y')); ?><br><span class="text-muted" style="font-size: 0.6rem;"><?php echo e($item->created_at->format('H:i')); ?></span></td>
                                    <td class="pe-2 py-1">
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-sm btn-outline-info rounded-sm" onclick="viewDetails(<?php echo e($item->id); ?>)" title="View Details" style="padding: 0.15rem 0.3rem;">
                                                <i class="ti ti-eye" style="font-size: 0.7rem;"></i>
                                            </button>
                                            <?php if($item->operation_type == 'upload' && $item->status == 'completed' && $item->failed_rows > 0 && $item->error_report_path): ?>
                                            <a href="<?php echo e(route('admin.import.download-error', $item->id)); ?>" class="btn btn-sm btn-outline-danger rounded-sm" title="Download Errors" style="padding: 0.15rem 0.3rem;">
                                                <i class="ti ti-file-text" style="font-size: 0.7rem;"></i>
                                            </a>
                                            <?php endif; ?>
                                            <?php if(auth()->check() && auth()->user()->hasRole('admin')): ?>
                                            <button type="button" class="btn btn-sm btn-outline-danger rounded-sm" onclick="deleteImport(<?php echo e($item->id); ?>)" title="Delete" style="padding: 0.15rem 0.3rem;">
                                                <i class="ti ti-trash" style="font-size: 0.7rem;"></i>
                                            </button>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="6" class="text-center py-2 text-muted" style="font-size: 0.7rem;">No import history found</td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer py-1 px-2 d-flex justify-content-between align-items-center">
                    <div class="text-muted" style="font-size: 0.65rem;">
                        <i class="ti ti-database me-1"></i> Total Records: <?php echo e($history->total()); ?>

                    </div>
                    <div class="text-muted" style="font-size: 0.65rem;">
                        <i class="ti ti-calendar me-1"></i> Last Import: <?php echo e($lastImportDate ?? 'N/A'); ?>

                    </div>
                    <div><?php echo e($history->links('pagination::bootstrap-4')); ?></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- View Details Modal -->
<div class="modal fade" id="viewDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content rounded-sm">
            <div class="modal-header py-1 px-2">
                <h5 class="modal-title" style="font-size: 0.8rem;"><i class="ti ti-file-info me-1"></i> Import Details</h5>
                <button type="button" class="btn-close py-0" style="font-size: 0.6rem;" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0" id="detailsContent">
                <div class="text-center py-3">
                    <div class="spinner-border text-primary" style="width: 1rem; height: 1rem;"></div>
                    <p class="mt-1" style="font-size: 0.7rem;">Loading...</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Campus loading
document.getElementById('university_id').addEventListener('change', function() {
    var universityId = this.value;
    var campusSelect = document.getElementById('campus_id');
    var schoolSelect = document.getElementById('school_id');
    var departmentSelect = document.getElementById('department_id');
    
    if (universityId) {
        fetch(`/admin/import/campuses/${universityId}`)
            .then(response => response.json())
            .then(data => {
                campusSelect.innerHTML = '<option value="">Select Campus</option>';
                data.forEach(campus => {
                    campusSelect.innerHTML += `<option value="${campus.id}">${campus.name}</option>`;
                });
                campusSelect.disabled = false;
                schoolSelect.innerHTML = '<option value="">Select Campus First</option>';
                schoolSelect.disabled = true;
                departmentSelect.innerHTML = '<option value="">Select School First</option>';
                departmentSelect.disabled = true;
            });
    }
});

// School loading
document.getElementById('campus_id').addEventListener('change', function() {
    var campusId = this.value;
    var schoolSelect = document.getElementById('school_id');
    var departmentSelect = document.getElementById('department_id');
    
    if (campusId) {
        fetch(`/admin/import/schools/${campusId}`)
            .then(response => response.json())
            .then(data => {
                schoolSelect.innerHTML = '<option value="">Select School/Faculty</option>';
                data.forEach(school => {
                    schoolSelect.innerHTML += `<option value="${school.id}">${school.name}</option>`;
                });
                schoolSelect.disabled = false;
                departmentSelect.innerHTML = '<option value="">Select School First</option>';
                departmentSelect.disabled = true;
            });
    }
});

// Department loading
document.getElementById('school_id').addEventListener('change', function() {
    var schoolId = this.value;
    var departmentSelect = document.getElementById('department_id');
    
    if (schoolId) {
        fetch(`/admin/import/departments-by-school/${schoolId}`)
            .then(response => response.json())
            .then(data => {
                departmentSelect.innerHTML = '<option value="">Select Department</option>';
                data.forEach(dept => {
                    departmentSelect.innerHTML += `<option value="${dept.id}">${dept.name}</option>`;
                });
                departmentSelect.disabled = false;
            });
    }
});

// Download template - includes school_id
document.getElementById('downloadTemplateBtn').addEventListener('click', function() {
    var universityId = document.getElementById('university_id').value;
    var campusId = document.getElementById('campus_id').value;
    var schoolId = document.getElementById('school_id').value;
    var departmentId = document.getElementById('department_id').value;
    
    if (!universityId || !campusId || !schoolId || !departmentId) {
        alert('Please select University, Campus, School, and Department first');
        return;
    }
    
    window.location.href = `<?php echo e(route('admin.import.staff.template')); ?>?university_id=${universityId}&campus_id=${campusId}&school_id=${schoolId}&department_id=${departmentId}`;
});

// Enable upload after selection
document.getElementById('department_id').addEventListener('change', function() {
    var universityId = document.getElementById('university_id').value;
    var campusId = document.getElementById('campus_id').value;
    var schoolId = document.getElementById('school_id').value;
    var departmentId = this.value;
    
    if (universityId && campusId && schoolId && departmentId) {
        document.getElementById('upload_university_id').value = universityId;
        document.getElementById('upload_campus_id').value = campusId;
        document.getElementById('upload_school_id').value = schoolId;
        document.getElementById('upload_department_id').value = departmentId;
        document.getElementById('uploadBtn').disabled = false;
        document.getElementById('downloadTemplateBtn').disabled = false;
    } else {
        document.getElementById('uploadBtn').disabled = true;
        document.getElementById('downloadTemplateBtn').disabled = true;
    }
});

// Search functionality
document.getElementById('searchBtn').addEventListener('click', function() {
    var url = new URL(window.location.href);
    if (document.getElementById('search_date_from').value) url.searchParams.set('date_from', document.getElementById('search_date_from').value);
    if (document.getElementById('search_date_to').value) url.searchParams.set('date_to', document.getElementById('search_date_to').value);
    if (document.getElementById('search_status').value) url.searchParams.set('status', document.getElementById('search_status').value);
    window.location.href = url;
});

// View Details Function
function viewDetails(id) {
    fetch(`/admin/import/details/${id}`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('detailsContent').innerHTML = `
                <div class="p-2">
                    <div class="row mb-1">
                        <div class="col-md-6">
                            <label class="text-muted" style="font-size: 0.65rem;">Sequence Number</label>
                            <p class="fw-semibold mb-0" style="font-size: 0.75rem;">${data.sequence_number}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted" style="font-size: 0.65rem;">Status</label>
                            <p class="mb-0"><span class="badge bg-${data.status === 'completed' ? 'success' : (data.status === 'failed' ? 'danger' : 'warning')} rounded-sm" style="font-size: 0.65rem;">${data.status.toUpperCase()}</span></p>
                        </div>
                    </div>
                    <div class="row mb-1">
                        <div class="col-md-6">
                            <label class="text-muted" style="font-size: 0.65rem;">Operation</label>
                            <p class="mb-0" style="font-size: 0.75rem;">${data.operation_type === 'download' ? '📥 Download' : '📤 Upload'}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted" style="font-size: 0.65rem;">Filename</label>
                            <p class="mb-0" style="font-size: 0.75rem;">${data.original_filename}</p>
                        </div>
                    </div>
                    <div class="row mb-1">
                        <div class="col-md-4">
                            <label class="text-muted" style="font-size: 0.65rem;">Total Rows</label>
                            <p class="fw-semibold mb-0">${data.total_rows}</p>
                        </div>
                        <div class="col-md-4">
                            <label class="text-muted" style="font-size: 0.65rem;">Successful</label>
                            <p class="fw-semibold text-success mb-0">${data.successful_rows}</p>
                        </div>
                        <div class="col-md-4">
                            <label class="text-muted" style="font-size: 0.65rem;">Failed</label>
                            <p class="fw-semibold text-danger mb-0">${data.failed_rows}</p>
                        </div>
                    </div>
                    <div class="row mb-1">
                        <div class="col-md-6">
                            <label class="text-muted" style="font-size: 0.65rem;">Uploaded By</label>
                            <p class="mb-0">${data.user_name}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted" style="font-size: 0.65rem;">Date</label>
                            <p class="mb-0">${data.created_at}</p>
                        </div>
                    </div>
                    ${data.errors && data.errors.length > 0 ? `<div class="mt-2"><label class="text-muted">Errors</label><div class="alert alert-danger p-1 mt-1 rounded-sm" style="max-height: 150px; overflow-y: auto; font-size: 0.65rem;"><ul class="mb-0 ps-3">${data.errors.map(e => `<li>${e}</li>`).join('')}</ul></div></div>` : ''}
                </div>
            `;
            new bootstrap.Modal(document.getElementById('viewDetailsModal')).show();
        });
}

// Delete Import Function
function deleteImport(id) {
    if (confirm('Are you sure you want to delete this import record? This action cannot be undone.')) {
        fetch(`/admin/import/delete/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Failed to delete record: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            alert('Error deleting record: ' + error);
        });
    }
}
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\wamp64\www\unileave\resources\views/admin/imports/index.blade.php ENDPATH**/ ?>