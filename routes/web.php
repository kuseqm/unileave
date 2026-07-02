<?php

use App\Http\Controllers\MessageAccessController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LeaveRequestController;
use App\Http\Controllers\TravelPermissionController;
use App\Http\Controllers\PermissionRequestController;
use App\Http\Controllers\BlackoutController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ApprovalController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\AuditTrailController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\HrSettingsController;
use App\Http\Controllers\BackupController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\VcDashboardController;
use App\Http\Controllers\Admin\ImportController;
use App\Http\Controllers\Admin\UniversityController;
use App\Http\Controllers\Admin\CampusController;
use App\Http\Controllers\Admin\SchoolController;
use App\Http\Controllers\Admin\DepartmentController;
use App\Http\Controllers\Admin\LeaveTypeController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\TenantSettingsController;
use App\Http\Controllers\Admin\ActingDvcAssignmentController;
use App\Http\Controllers\Admin\LetterheadController;
use App\Http\Controllers\Admin\DatabaseSchemaController;
use App\Http\Controllers\DevController;
use App\Http\Controllers\VerifyController;
use App\Http\Controllers\LetterController;
use App\Http\Controllers\LeaveLetterController;
use App\Http\Controllers\Registry\ReferenceFormatController;
use App\Http\Controllers\Registry\LetterFileController;
use App\Http\Controllers\Registry\LetterSettingController;
use App\Http\Controllers\PortalController;

// Central Registry Controllers
use App\Http\Controllers\Registry\IncomingLetterController;
use App\Http\Controllers\Registry\OutgoingLetterController;
use App\Http\Controllers\Registry\PrintQueueController;
use App\Http\Controllers\Registry\OfficeController;

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// ============================================
// LANDING PAGE
// ============================================
Route::get('/', function () {
    return view('landing');
})->name('landing');

// ============================================
// PUBLIC DOCUMENT VERIFICATION ROUTES
// ============================================
Route::get('/verify', [VerifyController::class, 'index'])->name('verify.index');
Route::get('/verify/{code}', [VerifyController::class, 'show'])->name('verify.show');
Route::get('/verify/{code}/print', [VerifyController::class, 'print'])->name('verify.print');

// ============================================
// LETTER VERIFICATION ROUTES (Public)
// ============================================
Route::get('/verify-letter/{code}', [LetterController::class, 'verify'])->name('verify.letter');
Route::get('/letters/{id}/view', [LetterController::class, 'view'])->name('letters.view');
Route::get('/letters/{id}/print', [LetterController::class, 'print'])->name('letters.print');
Route::get('/letters/{id}/pdf', [LetterController::class, 'downloadPdf'])->name('letters.pdf');

// ============================================
// TEST LETTER ROUTE (Remove in production)
// ============================================
Route::get('/test-letter/{id}', function($id) {
    $leaveRequest = App\Models\LeaveRequest::findOrFail($id);
    $staff = $leaveRequest->staff;
    $university = App\Models\University::first();
    
    return view('components.letter.leave-approval', [
        'leaveRequest' => $leaveRequest,
        'staff' => $staff,
        'university' => $university,
        'letterNumber' => 'LTR/' . date('Y') . '/' . str_pad($leaveRequest->id, 4, '0', STR_PAD_LEFT),
        'secureCode' => 'SMU-LTR-' . strtoupper(substr(md5($leaveRequest->id . now()), 0, 8)),
        'signatory' => 'Prof. Sarah Johnson',
        'signatoryTitle' => 'Vice Chancellor',
        'cc' => [
            'Head of Department - ' . ($staff->department->name ?? 'N/A'),
            'Dean - ' . ($staff->department->school->name ?? 'Faculty'),
            'HR Department',
            'DVC Academic'
        ]
    ]);
})->name('test.letter');

// ============================================
// AUTHENTICATION ROUTES
// ============================================
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/register', function () {
    return redirect('/login')->with('info', 'Registration is currently disabled. Contact admin for account creation.');
})->name('register');

// ============================================
// AUTHENTICATED ROUTES
// ============================================
Route::middleware(['auth'])->group(function () {

    // ============================================
    // SHARED DASHBOARD
    // ============================================
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/stats', [DashboardController::class, 'getStats'])->name('dashboard.stats');

    // ============================================
    // PORTAL DASHBOARD (Landing Page after Login)
    // ============================================
    Route::get('/portal-dashboard', [PortalController::class, 'index'])->name('portal.dashboard');
    Route::get('/portal', [PortalController::class, 'index']);

    // ============================================
    // STAFF MANAGEMENT
    // ============================================
    Route::prefix('staff')->name('staff.')->group(function () {
        Route::get('/', [StaffController::class, 'index'])->name('index');
        Route::get('/create', [StaffController::class, 'create'])->name('create');
        Route::post('/', [StaffController::class, 'store'])->name('store');
        Route::get('/{id}', [StaffController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [StaffController::class, 'edit'])->name('edit');
        Route::put('/{id}', [StaffController::class, 'update'])->name('update');
        Route::delete('/{id}', [StaffController::class, 'destroy'])->name('destroy');
        Route::get('/{id}/reset-password', [StaffController::class, 'resetPassword'])->name('reset-password');
        
        Route::get('/campuses/{universityId}', [StaffController::class, 'getCampusesByUniversity'])->name('campuses');
        Route::get('/schools/{campusId}', [StaffController::class, 'getSchoolsByCampus'])->name('schools');
        Route::get('/departments/{schoolId}', [StaffController::class, 'getDepartmentsBySchool'])->name('departments');
    });

    // ============================================
    // LEAVE REQUESTS
    // ============================================
    Route::prefix('leave-requests')->name('leave-requests.')->group(function () {
        Route::get('/approvals-history', [LeaveRequestController::class, 'approvalsHistory'])->name('approvals-history');
        Route::get('/my-requests', [LeaveRequestController::class, 'myRequests'])->name('my-requests');
        Route::get('/create', [LeaveRequestController::class, 'create'])->name('create');
        Route::get('/pending', [LeaveRequestController::class, 'pendingApprovals'])->name('pending');
        Route::get('/hold-position-for-me', [LeaveRequestController::class, 'holdPositionForMe'])->name('hold-position-for-me');
        Route::get('/i-held-position-for', [LeaveRequestController::class, 'iHeldPositionFor'])->name('i-held-position-for');
        Route::get('/status', [LeaveRequestController::class, 'myRequestStatus'])->name('status');
        Route::get('/acting/{token}', [LeaveRequestController::class, 'actingResponse'])->name('acting.response');
        Route::post('/acting/{id}/process', [LeaveRequestController::class, 'processActingResponse'])->name('acting.process');
        Route::post('/', [LeaveRequestController::class, 'store'])->name('store');
        Route::post('/{id}/approve', [LeaveRequestController::class, 'approve'])->name('approve');
        Route::post('/{id}/reject', [LeaveRequestController::class, 'reject'])->name('reject');
        Route::post('/{id}/cancel', [LeaveRequestController::class, 'cancel'])->name('cancel');
        
        Route::middleware(['role:hr-officer'])->group(function () {
            Route::get('/hr-logistics/{id}', [LeaveRequestController::class, 'hrLogistics'])->name('hr-logistics');
            Route::put('/hr-logistics/{id}/process', [LeaveRequestController::class, 'processHrLogistics'])->name('hr-logistics.process');
            Route::get('/hr-review/{id}', [LeaveRequestController::class, 'hrReview'])->name('hr-review');
            Route::put('/hr-review/{id}/process', [LeaveRequestController::class, 'processHrReview'])->name('hr-review.process');
        });
        
        Route::get('/dvc-review/{id}', [LeaveRequestController::class, 'dvcReview'])->name('dvc-review');
        Route::put('/dvc-review/{id}/process', [LeaveRequestController::class, 'processDvcReview'])->name('dvc-review.process');
        
        Route::get('/{id}', [LeaveRequestController::class, 'show'])->name('show');
        Route::get('/{id}/letter', [LeaveRequestController::class, 'viewLetter'])->name('letter');
        Route::get('/approval-details/{id}', [LeaveRequestController::class, 'approvalDetails'])->name('approval-details');
    });

    // ============================================
    // TRAVEL PERMISSIONS
    // ============================================
    Route::prefix('travel-permissions')->name('travel-permissions.')->group(function () {
        Route::get('/my-requests', [TravelPermissionController::class, 'myRequests'])->name('my-requests');
        Route::get('/create', [TravelPermissionController::class, 'create'])->name('create');
        Route::post('/', [TravelPermissionController::class, 'store'])->name('store');
        Route::get('/{id}', [TravelPermissionController::class, 'show'])->name('show');
        Route::get('/acting/{token}', [TravelPermissionController::class, 'actingResponse'])->name('acting.response');
        Route::post('/acting/{id}/process', [TravelPermissionController::class, 'processActingResponse'])->name('acting.process');
        Route::get('/pending', [TravelPermissionController::class, 'pendingApprovals'])->name('pending');
        Route::post('/{id}/approve', [TravelPermissionController::class, 'approve'])->name('approve');
        Route::post('/{id}/reject', [TravelPermissionController::class, 'reject'])->name('reject');
    });

    // ============================================
    // SHORT PERMISSIONS
    // ============================================
    Route::prefix('permissions')->name('permissions.')->group(function () {
        Route::get('/my-requests', [PermissionRequestController::class, 'myRequests'])->name('my-requests');
        Route::get('/create', [PermissionRequestController::class, 'create'])->name('create');
        Route::get('/pending', [PermissionRequestController::class, 'pendingApprovals'])->name('pending');
        Route::post('/', [PermissionRequestController::class, 'store'])->name('store');
        Route::get('/{id}', [PermissionRequestController::class, 'show'])->name('show');
        Route::post('/{id}/cancel', [PermissionRequestController::class, 'cancel'])->name('cancel');
        Route::post('/hod/{id}/approve', [PermissionRequestController::class, 'hodApprove'])->name('hod.approve');
        Route::post('/hod/{id}/reject', [PermissionRequestController::class, 'hodReject'])->name('hod.reject');
        Route::post('/hr/{id}/approve', [PermissionRequestController::class, 'hrApprove'])->name('hr.approve');
        Route::post('/hr/{id}/reject', [PermissionRequestController::class, 'hrReject'])->name('hr.reject');
    });

    // ============================================
    // BLACKOUT PERIODS
    // ============================================
    Route::prefix('blackout')->name('blackout.')->group(function () {
        Route::get('/create', [BlackoutController::class, 'create'])->name('create');
        Route::post('/', [BlackoutController::class, 'store'])->name('store');
        Route::get('/check-date', [BlackoutController::class, 'checkDate'])->name('check-date');
        Route::get('/', [BlackoutController::class, 'index'])->name('index');
        Route::get('/{id}', [BlackoutController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [BlackoutController::class, 'edit'])->name('edit');
        Route::put('/{id}', [BlackoutController::class, 'update'])->name('update');
        Route::delete('/{id}', [BlackoutController::class, 'destroy'])->name('destroy');
    });

    // ============================================
    // PROFILE ROUTES
    // ============================================
    Route::prefix('profile')->name('profile.')->group(function () {
        // Core Profile
        Route::get('/', [ProfileController::class, 'show'])->name('show');
        Route::put('/', [ProfileController::class, 'update'])->name('update');
        Route::put('/password', [ProfileController::class, 'updatePassword'])->name('password.update');
        Route::post('/avatar', [ProfileController::class, 'updateAvatar'])->name('avatar.update');
        
        // Phones
        Route::post('/phones', [ProfileController::class, 'storePhone'])->name('phones.store');
        Route::delete('/phones/{id}', [ProfileController::class, 'destroyPhone'])->name('phones.destroy');
        
        // Emails
        Route::post('/emails', [ProfileController::class, 'storeEmail'])->name('emails.store');
        Route::delete('/emails/{id}', [ProfileController::class, 'destroyEmail'])->name('emails.destroy');
        
        // Dependents
        Route::post('/dependents', [ProfileController::class, 'storeDependent'])->name('dependents.store');
        Route::delete('/dependents/{id}', [ProfileController::class, 'destroyDependent'])->name('dependents.destroy');
        
        // Addresses
        Route::post('/addresses', [ProfileController::class, 'storeAddress'])->name('addresses.store');
        Route::delete('/addresses/{id}', [ProfileController::class, 'destroyAddress'])->name('addresses.destroy');
        
        // Bank Details (Single account - update)
        Route::put('/bank', [ProfileController::class, 'updateBankDetails'])->name('bank.update');
        
        // Bank Accounts (Multiple accounts - new)
        Route::post('/bank-accounts', [ProfileController::class, 'storeBankAccount'])->name('bank-accounts.store');
        Route::delete('/bank-accounts/{id}', [ProfileController::class, 'destroyBankAccount'])->name('bank-accounts.destroy');
        Route::get('/bank-accounts/{id}/json', [ProfileController::class, 'getBankJson'])->name('bank-accounts.json');
        
        // Emergency Contacts
        Route::post('/emergency', [ProfileController::class, 'storeEmergencyContact'])->name('emergency.store');
        Route::delete('/emergency/{id}', [ProfileController::class, 'destroyEmergencyContact'])->name('emergency.destroy');
        
        // Qualifications
        Route::post('/qualifications', [ProfileController::class, 'storeQualification'])->name('qualifications.store');
        Route::delete('/qualifications/{id}', [ProfileController::class, 'destroyQualification'])->name('qualifications.destroy');
        
        // Skills
        Route::post('/skills', [ProfileController::class, 'storeSkill'])->name('skills.store');
        Route::delete('/skills/{id}', [ProfileController::class, 'destroySkill'])->name('skills.destroy');
        
        // Languages
        Route::post('/languages', [ProfileController::class, 'storeLanguage'])->name('languages.store');
        Route::delete('/languages/{id}', [ProfileController::class, 'destroyLanguage'])->name('languages.destroy');
        
        // Certifications
        Route::post('/certifications', [ProfileController::class, 'storeCertification'])->name('certifications.store');
        Route::delete('/certifications/{id}', [ProfileController::class, 'destroyCertification'])->name('certifications.destroy');
        
        // Awards
        Route::post('/awards', [ProfileController::class, 'storeAward'])->name('awards.store');
        Route::delete('/awards/{id}', [ProfileController::class, 'destroyAward'])->name('awards.destroy');
        
        // Work Experience
        Route::post('/work-experience', [ProfileController::class, 'storeWorkExperience'])->name('work-experience.store');
        Route::delete('/work-experience/{id}', [ProfileController::class, 'destroyWorkExperience'])->name('work-experience.destroy');
        
        // Settings
        Route::get('/settings', [ProfileController::class, 'settings'])->name('settings');
        Route::put('/settings', [ProfileController::class, 'updateSettings'])->name('settings.update');
        Route::get('/notifications', [ProfileController::class, 'notificationSettings'])->name('notifications');
        Route::put('/notifications', [ProfileController::class, 'updateNotificationSettings'])->name('notifications.update');

        // JSON routes for view modals
        Route::get('/dependents/{id}/json', [ProfileController::class, 'getDependentJson'])->name('dependents.json');
        Route::get('/addresses/{id}/json', [ProfileController::class, 'getAddressJson'])->name('addresses.json');
        Route::get('/emergency/{id}/json', [ProfileController::class, 'getEmergencyJson'])->name('emergency.json');
        Route::get('/qualifications/{id}/json', [ProfileController::class, 'getQualificationJson'])->name('qualifications.json');
        Route::get('/skills/{id}/json', [ProfileController::class, 'getSkillJson'])->name('skills.json');
        Route::get('/languages/{id}/json', [ProfileController::class, 'getLanguageJson'])->name('languages.json');
    });

    // ============================================
    // NOTIFICATIONS
    // ============================================
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/unread-count', [NotificationController::class, 'unreadCount'])->name('unread-count');
        Route::get('/list', [NotificationController::class, 'list'])->name('list');
        Route::get('/', [NotificationController::class, 'index'])->name('index');
        Route::post('/{id}/read', [NotificationController::class, 'markAsRead'])->name('mark-read');
        Route::post('/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('mark-all-read');
        Route::delete('/{id}', [NotificationController::class, 'destroy'])->name('destroy');
    });

    // ============================================
    // APPROVALS
    // ============================================
    Route::prefix('approvals')->name('approvals.')->group(function () {
        Route::get('/', [ApprovalController::class, 'index'])->name('index');
        Route::get('/leave-requests', [ApprovalController::class, 'leaveRequests'])->name('leave-requests');
        Route::get('/permission-requests', [ApprovalController::class, 'permissionRequests'])->name('permission-requests');
        Route::post('/bulk-approve', [ApprovalController::class, 'bulkApprove'])->name('bulk-approve');
        Route::get('/statistics', [ApprovalController::class, 'statistics'])->name('statistics');
    });

    Route::get('/approvals', function () {
        return view('admin.approvals');
    })->name('approvals');

    // ============================================
    // MESSAGING SYSTEM
    // ============================================
    Route::prefix('messages')->name('messages.')->middleware(['auth'])->group(function () {
        
        // ============================================
        // SPECIFIC ROUTES FIRST (No parameters)
        // ============================================
        
        // Main views
        Route::get('/inbox', [MessageController::class, 'inbox'])->name('inbox');
        Route::get('/sent', [MessageController::class, 'sent'])->name('sent');
        Route::get('/drafts', [MessageController::class, 'drafts'])->name('drafts');
        
        // Compose and send
        Route::get('/create', [MessageController::class, 'create'])->name('create');
        Route::post('/', [MessageController::class, 'store'])->name('store');
        
        // Admin/HR message list - MUST be BEFORE /{id} route
        Route::get('/admin-list', [MessageAccessController::class, 'messageList'])->name('admin-list');
        
        // VC approval routes - MUST be BEFORE /{id} route
        Route::get('/access-requests', [MessageAccessController::class, 'pendingRequests'])->name('access-requests');
        
        // API endpoints (for AJAX)
        Route::get('/unread-count', [MessageController::class, 'getUnreadCount'])->name('unread-count');
        Route::get('/users/search', [MessageController::class, 'searchUsers'])->name('users.search');
        
        // Get pending access requests count (for sidebar badge)
        Route::get('/access-requests/count', [MessageAccessController::class, 'pendingCount'])->name('access-requests.count');
        
        // Search
        Route::get('/search', [MessageController::class, 'search'])->name('search');
        
        // Bulk operations
        Route::post('/bulk-delete', [MessageController::class, 'bulkDelete'])->name('bulk-delete');
        
        // Legacy/alternative views
        Route::get('/user/{userId}', [MessageController::class, 'showForUser'])->name('user.show');
        Route::get('/leave-request/{leaveRequestId}', [MessageController::class, 'showForLeaveRequest'])->name('leave-request.show');
        
        // Redirect root /messages to inbox
        Route::get('/', [MessageController::class, 'inbox'])->name('index');
        
        // ============================================
        // ROUTES WITH {id} PARAMETER - MUST BE LAST
        // ============================================
        
        // Metadata view - MUST be BEFORE /{id}
        Route::get('/{id}/metadata', [MessageAccessController::class, 'metadata'])->name('metadata');
        
        // Read, reply, and manage
        Route::get('/{id}', [MessageController::class, 'show'])->name('show');
        Route::post('/{id}/reply', [MessageController::class, 'reply'])->name('reply');
        Route::delete('/{id}', [MessageController::class, 'destroy'])->name('destroy');
        
        // Actions
        Route::post('/{id}/archive', [MessageController::class, 'archive'])->name('archive');
        Route::post('/{id}/star', [MessageController::class, 'toggleStar'])->name('star');
        Route::post('/{id}/unread', [MessageController::class, 'markUnread'])->name('mark-unread');
        
        // Legacy mark as read (AJAX)
        Route::post('/{id}/read', [MessageController::class, 'markAsRead'])->name('mark-read');
        
        // Message Access & Management Routes with {id}
        // Secure message content view (for VC-approved access)
        Route::get('/{id}/secure-content', [MessageAccessController::class, 'secureContent'])->name('secure-content');
        Route::get('/{id}/audit-trail', [MessageAccessController::class, 'auditTrail'])->name('audit-trail');
        Route::post('/{id}/restore', [MessageAccessController::class, 'restoreMessage'])->name('restore');
        Route::post('/{id}/request-access', [MessageAccessController::class, 'requestAccess'])->name('request-access');
        Route::post('/{id}/view-with-code', [MessageAccessController::class, 'viewWithCode'])->name('view-with-code');
        
        // VC approval routes with {id}
        Route::post('/access-requests/{id}/approve', [MessageAccessController::class, 'approveRequest'])->name('approve-access');
        Route::post('/access-requests/{id}/reject', [MessageAccessController::class, 'rejectRequest'])->name('reject-access');
    });

    // ============================================
    // AUDIT TRAIL ROUTES
    // ============================================
    Route::prefix('audit-trail')->name('audit-trail.')->middleware(['auth'])->group(function () {
        Route::get('/', [AuditTrailController::class, 'index'])->name('index');
        Route::get('/export', [AuditTrailController::class, 'export'])->name('export');
        Route::get('/{id}', [AuditTrailController::class, 'show'])->name('show');
    });
    
    // ============================================
    // REPORTS
    // ============================================
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::get('/{slug}', [ReportController::class, 'show'])->name('show');
        Route::get('/{slug}/pdf', [ReportController::class, 'exportPdf'])->name('export.pdf');
        Route::get('/{slug}/excel', [ReportController::class, 'exportExcel'])->name('export.excel');
    });

    // ============================================
    // AUDIT LOGS
    // ============================================
    Route::prefix('audit-logs')->name('audit-logs.')->group(function () {
        Route::get('/', [AuditLogController::class, 'index'])->name('index');
        Route::get('/export', [AuditLogController::class, 'export'])->name('export');
        Route::get('/user/{userId}', [AuditLogController::class, 'user'])->name('user');
    });

    // ============================================
    // SETTINGS - GENERAL (Admin only)
    // ============================================
    Route::middleware(['role:admin'])->prefix('settings')->name('settings.')->group(function () {
        Route::get('/', [SettingsController::class, 'index'])->name('index');
        Route::get('/create', [SettingsController::class, 'create'])->name('create');
        Route::post('/', [SettingsController::class, 'store'])->name('store');
        Route::get('/{id}', [SettingsController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [SettingsController::class, 'edit'])->name('edit');
        Route::put('/{id}', [SettingsController::class, 'update'])->name('update');
        Route::delete('/{id}', [SettingsController::class, 'destroy'])->name('destroy');
        Route::put('/bulk-update', [SettingsController::class, 'bulkUpdate'])->name('bulk-update');
        Route::get('/department', [SettingsController::class, 'departmentSettings'])->name('department');
        Route::put('/department', [SettingsController::class, 'updateDepartmentSettings'])->name('department.update');
        Route::get('/approvals', [SettingsController::class, 'approvalSettings'])->name('approvals');
        Route::put('/approvals', [SettingsController::class, 'updateApprovalSettings'])->name('approvals.update');
        Route::get('/faculty', [SettingsController::class, 'facultySettings'])->name('faculty');
        Route::put('/faculty', [SettingsController::class, 'updateFacultySettings'])->name('faculty.update');
        Route::get('/deputy', [SettingsController::class, 'deputySettings'])->name('deputy');
        Route::put('/deputy', [SettingsController::class, 'updateDeputySettings'])->name('deputy.update');
        Route::get('/secretary', [SettingsController::class, 'secretarySettings'])->name('secretary');
        Route::put('/secretary', [SettingsController::class, 'updateSecretarySettings'])->name('secretary.update');
    });

    // ============================================
    // SETTINGS - HR (HR Officer and Admin only)
    // ============================================
    Route::middleware(['role:hr-officer|admin'])->prefix('settings')->name('settings.')->group(function () {
        Route::get('/hr', [HrSettingsController::class, 'index'])->name('hr');
        Route::put('/hr', [HrSettingsController::class, 'update'])->name('hr.update');
        Route::get('/policies', [HrSettingsController::class, 'policies'])->name('policies');
    });

    // ============================================
    // BACKUP
    // ============================================
    Route::prefix('backup')->name('backup.')->group(function () {
        Route::post('/create', [BackupController::class, 'create'])->name('create');
        Route::get('/download/{filename}', [BackupController::class, 'download'])->name('download');
        Route::delete('/delete/{filename}', [BackupController::class, 'delete'])->name('delete');
        Route::get('/restore/{filename}', [BackupController::class, 'restore'])->name('restore');
    });

    // ============================================
    // ADMIN API STATS
    // ============================================
    Route::prefix('admin-api')->name('admin-api.')->group(function () {
        Route::get('/dashboard/stats', [AdminDashboardController::class, 'getStats'])->name('dashboard.stats');
    });

    // ============================================
    // VICE CHANCELLOR DASHBOARD
    // ============================================
    Route::middleware(['role:vice-chancellor'])->prefix('vc')->name('vc.')->group(function () {
        Route::get('/dashboard', [VcDashboardController::class, 'index'])->name('dashboard');
        Route::get('/reports/monthly', [VcDashboardController::class, 'exportMonthlyReport'])->name('reports.monthly');
        Route::get('/reports/excel', [VcDashboardController::class, 'exportExcel'])->name('reports.excel');
        Route::get('/reports/pdf', [VcDashboardController::class, 'exportPdf'])->name('reports.pdf');
    });

    // ============================================
    // INTERNAL AUDITOR
    // ============================================
    Route::middleware(['role:internal-auditor'])->prefix('auditor')->name('auditor.')->group(function () {
        Route::get('/settings', [SettingsController::class, 'auditorSettings'])->name('settings');
        Route::put('/settings', [SettingsController::class, 'updateAuditorSettings'])->name('settings.update');
    });

    // ============================================
    // ADMIN ROUTES
    // ============================================
    Route::middleware(['role:admin'])->prefix('admin')->name('admin.')->group(function () {
        
        Route::prefix('import')->name('import.')->group(function () {
            Route::get('/staff', [ImportController::class, 'index'])->name('staff');
            Route::post('/staff/upload', [ImportController::class, 'upload'])->name('staff.upload');
            Route::get('/staff/template', [ImportController::class, 'downloadTemplate'])->name('staff.template');
            Route::get('/progress/{id}', [ImportController::class, 'progress'])->name('progress');
            Route::get('/download-error/{id}', [ImportController::class, 'downloadErrorReport'])->name('download-error');
            Route::get('/campuses/{universityId}', [ImportController::class, 'getCampuses'])->name('campuses');
            Route::get('/schools/{campusId}', [ImportController::class, 'getSchools'])->name('schools');
            Route::get('/departments/{campusId}', [ImportController::class, 'getDepartments'])->name('departments');
            Route::get('/departments-by-school/{schoolId}', [ImportController::class, 'getDepartmentsBySchool'])->name('departments-by-school');
            Route::get('/universities', function() {
                return response()->json(\App\Models\University::where('status', 'active')->get());
            })->name('universities');
            Route::get('/quarterly-report', [ImportController::class, 'quarterlyReport'])->name('quarterly-report');
            Route::get('/details/{id}', [ImportController::class, 'getDetails'])->name('details');
            Route::delete('/delete/{id}', [ImportController::class, 'deleteImport'])->name('delete');
        });
        
        Route::resource('universities', UniversityController::class)->except(['show']);
        Route::resource('campuses', CampusController::class)->except(['show']);
        Route::resource('schools', SchoolController::class)->except(['show']);
        Route::resource('departments', DepartmentController::class);
        Route::resource('leave-types', LeaveTypeController::class);
        
        // Role Management
        Route::prefix('roles')->name('roles.')->group(function () {
            Route::get('/', [RoleController::class, 'index'])->name('index');
            Route::get('/assign', [RoleController::class, 'assignForm'])->name('assign');
            Route::post('/assign', [RoleController::class, 'assignRole'])->name('assign.store');
            Route::delete('/remove/{userId}/{roleName}', [RoleController::class, 'removeRole'])->name('remove');
            Route::get('/create', [RoleController::class, 'createForm'])->name('create');
            Route::post('/create', [RoleController::class, 'storeRole'])->name('store');
            Route::get('/edit/{id}', [RoleController::class, 'editRole'])->name('edit');
            Route::put('/edit/{id}', [RoleController::class, 'updateRole'])->name('update');
            Route::delete('/delete/{id}', [RoleController::class, 'deleteRole'])->name('delete');
            Route::get('/permissions', [RoleController::class, 'permissions'])->name('permissions');
        });
        
        // LETTERHEAD SETTINGS
        Route::get('/settings/letterhead', [LetterheadController::class, 'index'])->name('settings.letterhead');
        Route::put('/settings/letterhead', [LetterheadController::class, 'update'])->name('settings.letterhead.update');
        Route::post('/settings/letterhead/preview', [LetterheadController::class, 'preview'])->name('settings.letterhead.preview');

        Route::resource('acting-dvc', ActingDvcAssignmentController::class);
        
        Route::get('/departments/by-school/{schoolId}', [DepartmentController::class, 'getBySchool'])->name('departments.by-school');
        Route::get('/departments/schools-by-campus/{campusId}', [DepartmentController::class, 'getSchoolsByCampus'])->name('departments.schools-by-campus');
        Route::get('/departments/all-json', [DepartmentController::class, 'getAllJson'])->name('departments.all-json');
        Route::post('/departments/bulk-update', [DepartmentController::class, 'bulkUpdate'])->name('departments.bulk-update');
        Route::patch('/departments/{id}/toggle-status', [DepartmentController::class, 'toggleStatus'])->name('departments.toggle-status');
        Route::get('/departments/export', [DepartmentController::class, 'export'])->name('departments.export');
        Route::get('/departments/export/pdf', [DepartmentController::class, 'exportPdf'])->name('departments.export-pdf');
        
        // ============================================
        // DATABASE SCHEMA VIEWER (Admin only)
        // ============================================
        Route::prefix('database-schema')->name('database-schema.')->group(function () {
            Route::get('/', [DatabaseSchemaController::class, 'index'])->name('index');
            Route::get('/table/{tableName}', [DatabaseSchemaController::class, 'getTableDetails'])->name('table.details');
            Route::get('/export/json', [DatabaseSchemaController::class, 'exportJson'])->name('export-json');
        });
    });

    // ============================================
    // SUPER ADMIN ROUTES (Tenant Management)
    // ============================================
    Route::middleware(['role:super-admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::resource('tenants', TenantSettingsController::class);
        Route::get('tenants/{id}/settings', [TenantSettingsController::class, 'settings'])->name('tenants.settings');
        Route::put('tenants/{id}/settings', [TenantSettingsController::class, 'updateSettings'])->name('tenants.settings.update');
    });

    // ============================================
    // LETTER SYSTEM (For ALL authenticated users)
    // ============================================
    Route::prefix('letters')->name('letters.')->group(function () {
        // Basic CRUD
        Route::get('/', [LetterController::class, 'index'])->name('index');
        Route::get('/create', [LetterController::class, 'create'])->name('create');
        Route::post('/', [LetterController::class, 'store'])->name('store');
        Route::get('/{id}', [LetterController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [LetterController::class, 'edit'])->name('edit');
        Route::put('/{id}', [LetterController::class, 'update'])->name('update');
        Route::delete('/{id}', [LetterController::class, 'destroy'])->name('destroy');
        
        // Actions
        Route::post('/{id}/submit', [LetterController::class, 'submit'])->name('submit');
        Route::get('/{id}/print', [LetterController::class, 'print'])->name('print');
        
        // Multi-Level Approvals (HOD → Dean → DVC → VC)
        Route::post('/{id}/hod-approve', [LetterController::class, 'hodApprove'])->name('hod.approve');
        Route::post('/{id}/dean-approve', [LetterController::class, 'deanApprove'])->name('dean.approve');
        Route::post('/{id}/dvc-approve', [LetterController::class, 'dvcApprove'])->name('dvc.approve');
        Route::post('/{id}/vc-sign', [LetterController::class, 'vcSign'])->name('vc.sign');
        
        // Reject route
        Route::post('/{id}/reject', [LetterController::class, 'reject'])->name('reject');
        
        // Approval Queues
        Route::get('/pending/approval', [LetterController::class, 'pendingApproval'])->name('pending.approval');
        Route::get('/pending/signature', [LetterController::class, 'pendingSignature'])->name('pending.signature');
    });

    // ============================================
    // LEAVE LETTERS ROUTES (For leave approval letters)
    // ============================================
    Route::prefix('leave-letters')->name('leave-letters.')->group(function () {
        Route::get('/', [LeaveLetterController::class, 'index'])->name('index');
        Route::get('/{id}', [LeaveLetterController::class, 'show'])->name('show');
        Route::get('/{id}/print', [LeaveLetterController::class, 'print'])->name('print');
        Route::get('/{id}/download-pdf', [LeaveLetterController::class, 'downloadPdf'])->name('download-pdf');
    });

    // ============================================
    // CENTRAL REGISTRY SYSTEM (Registry Officers & Admin only)
    // ============================================
    Route::middleware(['role:admin|registry-officer'])->prefix('registry')->name('registry.')->group(function () {
        
        // ============================================
        // REFERENCE FORMATS (Existing)
        // ============================================
        Route::resource('formats', ReferenceFormatController::class);
        Route::get('/reference-formats/entities', [ReferenceFormatController::class, 'getEntities'])->name('formats.entities');
        Route::post('/reference-formats/preview', [ReferenceFormatController::class, 'preview'])->name('formats.preview');
        
        // ============================================
        // LETTER FILES (Existing)
        // ============================================
        Route::resource('files', LetterFileController::class);
        Route::put('/files/{id}/close', [LetterFileController::class, 'close'])->name('files.close');
        Route::put('/files/{id}/reopen', [LetterFileController::class, 'reopen'])->name('files.reopen');
        Route::get('/files/next-file-number', [LetterFileController::class, 'getNextFileNumber'])->name('files.next-number');
        Route::get('/files/{id}/stats', [LetterFileController::class, 'getStats'])->name('files.stats');
        
        // ============================================
        // REGISTRY SETTINGS (Existing)
        // ============================================
        Route::get('/settings', [LetterSettingController::class, 'index'])->name('settings');
        Route::put('/settings', [LetterSettingController::class, 'update'])->name('settings.update');
        Route::post('/settings/reset', [LetterSettingController::class, 'reset'])->name('settings.reset');
        Route::post('/settings/preview-secure-code', [LetterSettingController::class, 'previewSecureCode'])->name('settings.preview-secure-code');
        Route::get('/settings/{universityId}', [LetterSettingController::class, 'getSettings'])->name('settings.get');
        Route::post('/settings/test-verification-url', [LetterSettingController::class, 'testVerificationUrl'])->name('settings.test-url');
        
        // ============================================
        // INCOMING LETTERS (NEW)
        // ============================================
        Route::resource('incoming', IncomingLetterController::class);
        Route::post('incoming/{id}/assign', [IncomingLetterController::class, 'assign'])->name('incoming.assign');
        Route::post('incoming/{id}/status', [IncomingLetterController::class, 'updateStatus'])->name('incoming.status');
        
        // ============================================
        // OUTGOING LETTERS (NEW)
        // ============================================
        Route::resource('outgoing', OutgoingLetterController::class);
        Route::post('outgoing/{id}/submit', [OutgoingLetterController::class, 'submitForApproval'])->name('outgoing.submit');
        Route::post('outgoing/{id}/approve', [OutgoingLetterController::class, 'approve'])->name('outgoing.approve');
        Route::post('outgoing/{id}/reject', [OutgoingLetterController::class, 'reject'])->name('outgoing.reject');
        Route::post('outgoing/{id}/dispatch', [OutgoingLetterController::class, 'dispatch'])->name('outgoing.dispatch');
        
        // ============================================
        // PRINT QUEUE (NEW)
        // ============================================
        Route::get('print-queue', [PrintQueueController::class, 'index'])->name('print-queue.index');
        Route::post('print-queue/{id}/print', [PrintQueueController::class, 'markAsPrinted'])->name('print-queue.print');
        Route::post('print-queue/{id}/dispatch', [PrintQueueController::class, 'markAsDispatched'])->name('print-queue.dispatch');
        Route::post('print-queue/batch-print', [PrintQueueController::class, 'batchPrint'])->name('print-queue.batch-print');
        
        // ============================================
        // OFFICES MANAGEMENT (NEW - Admin only)
        // ============================================
        Route::resource('offices', OfficeController::class)->except(['show']);
    });

    // ============================================
    // API Routes for Messaging (AJAX)
    // ============================================
    Route::get('/api/users/search', [App\Http\Controllers\MessageController::class, 'searchUsers'])->name('api.users.search');

});

// ============================================
// DEVELOPMENT ROUTES (Local only)
// ============================================
if (app()->environment('local')) {
    Route::get('/dev/project-state', [DevController::class, 'exportState'])->name('dev.project-state');
    Route::get('/dev/clear-cache', [DevController::class, 'clearCache'])->name('dev.clear-cache');
    Route::get('/dev/routes', [DevController::class, 'listRoutes'])->name('dev.routes');
}

// ============================================
// TEST ROUTES
// ============================================
Route::get('/test-hold', function() {
    return 'Test route works!';
});

Route::get('/test-approvals', [App\Http\Controllers\LeaveRequestController::class, 'approvalsHistory'])->name('test.approvals');

Route::get('/test-schema', function() {
    $columns = \Illuminate\Support\Facades\Schema::getColumnListing('leave_requests');
    return response()->json($columns);
});

Route::get('/test-permission-debug', function() {
    $user = App\Models\User::find(1);
    
    try {
        $permission = App\Models\PermissionRequest::create([
            'staff_id' => $user->id,
            'permission_type' => 'late_arrival',
            'date' => now()->toDateString(),
            'start_time' => '09:30:00',
            'end_time' => '10:30:00',
            'hours' => 1,
            'duration_hours' => 1,
            'reason' => 'Test permission',
            'status' => 'pending',
            'hod_status' => 'pending',
            'hr_status' => 'pending',
        ]);
        return "Success! Created permission ID: " . $permission->id;
    } catch (\Exception $e) {
        return "Error: " . $e->getMessage() . "\n\nFile: " . $e->getFile() . "\nLine: " . $e->getLine();
    }
});

// ============================================
// FALLBACK ROUTE (404)
// ============================================
Route::fallback(function () {
    return view('errors.404');
});

Route::get('/test-messages', function() {
    return 'Messages route test works!';
});