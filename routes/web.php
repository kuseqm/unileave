<?php

use App\Http\Controllers\AcademicCatalogController;
use App\Http\Controllers\Admin\AcademicYearController;
use App\Http\Controllers\Admin\ActingDvcAssignmentController;
use App\Http\Controllers\Admin\AnnouncementController;
use App\Http\Controllers\Admin\ApprovalDelegationController;
use App\Http\Controllers\Admin\AuthorizedSignatureController;
use App\Http\Controllers\Admin\CampusController;
use App\Http\Controllers\Admin\DatabaseSchemaController;
use App\Http\Controllers\Admin\DepartmentController;
use App\Http\Controllers\Admin\DepartmentLeaveController;
use App\Http\Controllers\Admin\EventController as AdminEventController;
use App\Http\Controllers\Admin\GateEntryLogController;
use App\Http\Controllers\Admin\IdCardController;
use App\Http\Controllers\Admin\ImportController;
use App\Http\Controllers\Admin\LeaveApprovalChainController;
use App\Http\Controllers\Admin\LeaveDocumentController;
use App\Http\Controllers\Admin\LeaveTypeController;
use App\Http\Controllers\Admin\LetterheadController;
use App\Http\Controllers\Admin\RequestCleanupController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\SchoolController;
use App\Http\Controllers\Admin\TeamMemberController;
use App\Http\Controllers\Admin\TenantSettingsController;
use App\Http\Controllers\Admin\UniversityController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\ApplicationsOverviewController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\AuditTrailController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BackupController;
use App\Http\Controllers\BlackoutController;
use App\Http\Controllers\CertificateNumberUploadController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DevController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\GraduationLetterController;
use App\Http\Controllers\HrSettingsController;
use App\Http\Controllers\LeaveLetterController;
use App\Http\Controllers\LeaveRequestController;
use App\Http\Controllers\LegalController;
use App\Http\Controllers\LetterController;
use App\Http\Controllers\MessageAccessController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\PermissionRequestController;
use App\Http\Controllers\PolicyController;
use App\Http\Controllers\PolicyRuleController;
use App\Http\Controllers\PolicySettingsController;
use App\Http\Controllers\PortalController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Registry\IncomingLetterController;
use App\Http\Controllers\Registry\LetterFileController;
use App\Http\Controllers\Registry\LetterSettingController;
use App\Http\Controllers\Registry\OfficeController;
use App\Http\Controllers\Registry\OutgoingLetterController;
use App\Http\Controllers\Registry\PrintQueueController;
use App\Http\Controllers\Registry\ReferenceFormatController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SettingsController;
// Central Registry Controllers
use App\Http\Controllers\StaffController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\StudentImportController;
use App\Http\Controllers\StudentStatisticsController;
use App\Http\Controllers\TravelLetterController;
use App\Http\Controllers\TravelPermissionController;
use App\Http\Controllers\VcDashboardController;
use App\Http\Controllers\VerificationLetterController;
use App\Http\Controllers\VerificationLogController;
use App\Http\Controllers\VerifyController;
use App\Http\Middleware\EnsurePasswordIsChanged;
use App\Models\Announcement;
use App\Models\Event;
use App\Models\LeaveRequest;
use App\Models\PageVisit;
use App\Models\TeamMember;
use App\Models\University;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// ============================================
// LANDING PAGE
// ============================================
Route::get('/', function () {
    if (! session()->has('counted_visit')) {
        PageVisit::firstOrCreate(['session_id' => session()->getId()]);
        session()->put('counted_visit', true);
    }

    $visitorCount = PageVisit::count();

    $announcements = Announcement::visible()->limit(4)->get();
    $teamMembers = TeamMember::visible()->get();

    $calendar = Event::calendarNav(
        (int) request('year', now()->year),
        (int) request('month', now()->month)
    );
    $calendarYear = $calendar['year'];
    $calendarMonth = $calendar['month'];
    $calendarWeeks = $calendar['weeks'];
    $calendarPrev = $calendar['prev'];
    $calendarNext = $calendar['next'];

    return view('landing', compact('announcements', 'teamMembers', 'calendarYear', 'calendarMonth', 'calendarWeeks', 'calendarPrev', 'calendarNext', 'visitorCount'));
})->name('landing');

// AJAX-only fragment used by the landing page calendar's prev/next
// buttons, so switching months doesn't reload the whole page.
Route::get('/calendar-partial', function () {
    $calendar = Event::calendarNav(
        (int) request('year', now()->year),
        (int) request('month', now()->month)
    );
    $calendarYear = $calendar['year'];
    $calendarMonth = $calendar['month'];
    $calendarWeeks = $calendar['weeks'];
    $calendarPrev = $calendar['prev'];
    $calendarNext = $calendar['next'];

    return view('partials.calendar-inner', compact('calendarYear', 'calendarMonth', 'calendarWeeks', 'calendarPrev', 'calendarNext'));
})->name('calendar.partial');

// ============================================
// PUBLIC DOCUMENT VERIFICATION ROUTES
// ============================================
Route::get('/verify', [VerifyController::class, 'index'])->name('verify.index');
Route::get('/verify/{code}', [VerifyController::class, 'show'])->name('verify.show');
Route::get('/verify/{code}/print', [VerifyController::class, 'print'])->name('verify.print');

// ============================================
// ID CARD PRINT ROUTE (Public — for shared print stations)
// ============================================
Route::get('/id-cards/{id}/print', [IdCardController::class, 'print'])->name('id-cards.print');

// ============================================
// LETTER VERIFICATION ROUTES (Public)
// ============================================
Route::get('/verify-letter/{code}', [LetterController::class, 'verify'])->name('verify.letter');

// ============================================
// PUBLIC EVENT DETAIL PAGE (linked from the landing page calendar)
// ============================================
Route::get('/events/{event:slug}', [EventController::class, 'show'])->name('events.show');

// ============================================
// TEST LETTER ROUTE (Remove in production)
// ============================================
Route::get('/test-letter/{id}', function ($id) {
    $leaveRequest = LeaveRequest::findOrFail($id);
    $staff = $leaveRequest->staff;
    $university = University::first();

    return view('components.letter.leave-approval', [
        'leaveRequest' => $leaveRequest,
        'staff' => $staff,
        'university' => $university,
        'letterNumber' => 'LTR/'.date('Y').'/'.str_pad($leaveRequest->id, 4, '0', STR_PAD_LEFT),
        'secureCode' => 'SMU-LTR-'.Str::upper(Str::random(8)),
        'signatory' => 'Prof. Sarah Johnson',
        'signatoryTitle' => 'Vice Chancellor',
        'cc' => [
            'Head of Department - '.($staff->department->name ?? 'N/A'),
            'Dean - '.($staff->department->school->name ?? 'Faculty'),
            'HR Department',
            'DVC Academic',
        ],
    ]);
})->name('test.letter');

// ============================================
// AUTHENTICATION ROUTES
// ============================================
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/terms', [LegalController::class, 'terms'])->name('legal.terms');
Route::get('/privacy', [LegalController::class, 'privacy'])->name('legal.privacy');

Route::middleware(['throttle:6,1'])->group(function () {
    Route::get('/forgot-password', [PasswordResetController::class, 'showForgotForm'])->name('password.request');
    Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLink'])->name('password.email');
    Route::get('/reset-password/{token}', [PasswordResetController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [PasswordResetController::class, 'resetPassword'])->name('password.update');
});

Route::get('/register', function () {
    return redirect('/login')->with('info', 'Registration is currently disabled. Contact admin for account creation.');
})->name('register');

// ============================================
// AUTHENTICATED ROUTES
// ============================================
Route::middleware(['auth', EnsurePasswordIsChanged::class])->group(function () {

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
        Route::get('/', [StaffController::class, 'index'])
            ->middleware('permission:view_staff')
            ->name('index');
        Route::get('/create', [StaffController::class, 'create'])
            ->middleware('permission:create_staff')
            ->name('create');
        Route::post('/', [StaffController::class, 'store'])
            ->middleware('permission:create_staff')
            ->name('store');
        Route::get('/export', [StaffController::class, 'export'])
            ->middleware('permission:export_staff')
            ->name('export');
        Route::get('/{id}', [StaffController::class, 'show'])
            ->middleware('permission:view_staff')
            ->name('show');
        Route::get('/{id}/edit', [StaffController::class, 'edit'])
            ->middleware('permission:edit_staff')
            ->name('edit');
        Route::put('/{id}', [StaffController::class, 'update'])
            ->middleware('permission:edit_staff')
            ->name('update');
        Route::delete('/{id}', [StaffController::class, 'destroy'])
            ->middleware('permission:delete_staff')
            ->name('destroy');
        Route::get('/{id}/reset-password', [StaffController::class, 'resetPassword'])
            ->middleware('permission:reset_staff_password')
            ->name('reset-password');

        Route::get('/campuses/{universityId}', [StaffController::class, 'getCampusesByUniversity'])
            ->middleware('permission:create_staff|edit_staff')
            ->name('campuses');
        Route::get('/schools/{campusId}', [StaffController::class, 'getSchoolsByCampus'])
            ->middleware('permission:create_staff|edit_staff')
            ->name('schools');
        Route::get('/departments/{schoolId}', [StaffController::class, 'getDepartmentsBySchool'])
            ->middleware('permission:create_staff|edit_staff')
            ->name('departments');
        Route::get('/departments-by-campus/{campusId}', [StaffController::class, 'getDepartmentsByCampus'])
            ->middleware('permission:create_staff|edit_staff')
            ->name('departments.by-campus');
    });

    // ============================================
    // LEAVE REQUESTS
    // ============================================
    Route::prefix('leave-requests')->name('leave-requests.')->group(function () {
        Route::get('/approvals-history', [LeaveRequestController::class, 'approvalsHistory'])
            ->middleware('permission:view_all_leave|view_school_leave|view_department_leave|view_pending_approvals')
            ->name('approvals-history');
        Route::get('/my-requests', [LeaveRequestController::class, 'myRequests'])
            ->middleware('permission:view_own_leave|view_all_leave')
            ->name('my-requests');
        Route::get('/create', [LeaveRequestController::class, 'create'])
            ->middleware('permission:apply_leave|submit_leave_on_behalf')
            ->name('create');
        Route::middleware('permission:apply_leave|submit_leave_on_behalf')->group(function () {
            Route::post('/draft', [LeaveRequestController::class, 'saveDraft'])->name('draft.save');
            Route::post('/draft/discard', [LeaveRequestController::class, 'discardDraft'])->name('draft.discard');
            Route::get('/draft/preview', [LeaveRequestController::class, 'previewDraftLetter'])->name('draft.preview');
            Route::post('/draft/documents', [LeaveRequestController::class, 'uploadDraftDocument'])->name('draft.documents.upload');
            Route::delete('/draft/documents/{document}', [LeaveRequestController::class, 'deleteDraftDocument'])->name('draft.documents.delete');
        });
        Route::get('/on-behalf', [LeaveRequestController::class, 'onBehalfForm'])
            ->middleware('permission:submit_leave_on_behalf')
            ->name('on-behalf');
        Route::get('/pending', [LeaveRequestController::class, 'pendingApprovals'])
            ->middleware('permission:view_pending_approvals|view_all_leave')
            ->name('pending');
        Route::get('/pending/heartbeat', [LeaveRequestController::class, 'pendingApprovalsHeartbeat'])
            ->middleware('permission:view_pending_approvals|view_all_leave')
            ->name('pending.heartbeat');
        Route::get('/hold-position-for-me', [LeaveRequestController::class, 'holdPositionForMe'])
            ->middleware('permission:request_acting_staff|view_acting_assignments|view_acting_requests')
            ->name('hold-position-for-me');
        Route::get('/i-held-position-for', [LeaveRequestController::class, 'iHeldPositionFor'])
            ->middleware('permission:view_acting_assignments|view_acting_requests|request_acting_staff')
            ->name('i-held-position-for');
        Route::get('/holder/{id}', [LeaveRequestController::class, 'showHolder'])
            ->middleware('permission:view_acting_assignments|view_acting_requests|request_acting_staff')
            ->name('holder');
        Route::middleware(['role:hod|dean|unit_head|hr-officer|hr-manager|dvca|dvc-pfa|admin'])->group(function () {
            Route::get('/unresponsive-acting', [LeaveRequestController::class, 'unresponsiveActing'])->name('unresponsive-acting');
            Route::post('/{id}/reassign-acting', [LeaveRequestController::class, 'reassignActingStaff'])->name('reassign-acting');
            Route::post('/{id}/override-acting', [LeaveRequestController::class, 'overrideActingApproval'])->name('override-acting');
            Route::post('/{id}/dismiss-unresponsive', [LeaveRequestController::class, 'dismissUnresponsive'])->name('dismiss-unresponsive');
            Route::post('/{id}/restore-unresponsive', [LeaveRequestController::class, 'restoreUnresponsive'])->name('restore-unresponsive');
        });
        Route::get('/status', [LeaveRequestController::class, 'myRequestStatus'])
            ->middleware('permission:view_own_leave|view_all_leave')
            ->name('status');
        Route::get('/status/heartbeat', [LeaveRequestController::class, 'myRequestStatusHeartbeat'])
            ->middleware('permission:view_own_leave|view_all_leave')
            ->name('status.heartbeat');
        Route::get('/acting/{token}', [LeaveRequestController::class, 'actingResponse'])->name('acting.response');
        Route::post('/acting/{id}/process', [LeaveRequestController::class, 'processActingResponse'])
            ->middleware('permission:accept_acting_request|request_acting_staff|view_acting_requests')
            ->name('acting.process');
        Route::post('/', [LeaveRequestController::class, 'store'])
            ->middleware('permission:apply_leave|submit_leave_on_behalf')
            ->name('store');
        Route::post('/{id}/approve', [LeaveRequestController::class, 'approve'])
            ->middleware('permission:approve_hod_level|approve_dean_level|approve_hr_level|approve_dvc_level|approve_hr_logistics')
            ->name('approve');
        Route::post('/{id}/reject', [LeaveRequestController::class, 'reject'])
            ->middleware('permission:reject_leave')
            ->name('reject');
        Route::post('/{id}/cancel', [LeaveRequestController::class, 'cancel'])
            ->middleware('permission:cancel_own_leave|reject_leave')
            ->name('cancel');

        Route::middleware(['role:hr-officer|hr-manager|admin', 'permission:approve_hr_level|approve_hr_logistics'])->group(function () {
            Route::get('/hr-logistics/{id}', [LeaveRequestController::class, 'hrLogistics'])->name('hr-logistics');
            Route::put('/hr-logistics/{id}/process', [LeaveRequestController::class, 'processHrLogistics'])->name('hr-logistics.process');
            Route::get('/hr-review/{id}', [LeaveRequestController::class, 'hrReview'])->name('hr-review');
            Route::put('/hr-review/{id}/process', [LeaveRequestController::class, 'processHrReview'])->name('hr-review.process');
        });

        Route::get('/dvc-review/{id}', [LeaveRequestController::class, 'dvcReview'])
            ->middleware('permission:approve_dvc_level|reject_leave|view_all_leave')
            ->name('dvc-review');
        Route::put('/dvc-review/{id}/process', [LeaveRequestController::class, 'processDvcReview'])
            ->middleware('permission:approve_dvc_level|reject_leave|view_all_leave')
            ->name('dvc-review.process');

        Route::get('/documents/{documentId}/download', [LeaveRequestController::class, 'downloadDocument'])->name('documents.download')->middleware('signed');

        Route::get('/{id}/heartbeat', [LeaveRequestController::class, 'showHeartbeat'])
            ->middleware('permission:view_own_leave|view_department_leave|view_school_leave|view_all_leave')
            ->name('show.heartbeat');
        Route::get('/{id}', [LeaveRequestController::class, 'show'])
            ->middleware('permission:view_own_leave|view_department_leave|view_school_leave|view_all_leave')
            ->name('show');
        Route::get('/{id}/letter', [LeaveRequestController::class, 'viewLetter'])
            ->middleware('permission:view_own_leave|view_department_leave|view_school_leave|view_all_leave')
            ->name('letter');
        Route::get('/approval-details/{id}', [LeaveRequestController::class, 'approvalDetails'])
            ->middleware('permission:view_own_leave|view_department_leave|view_school_leave|view_all_leave')
            ->name('approval-details');
    });

    // ============================================
    // TRAVEL PERMISSIONS
    // ============================================
    Route::prefix('travel-permissions')->name('travel-permissions.')->group(function () {
        Route::get('/my-requests', [TravelPermissionController::class, 'myRequests'])->middleware('permission:view_own_leave|view_all_leave')->name('my-requests');
        Route::get('/my-requests/heartbeat', [TravelPermissionController::class, 'myRequestsHeartbeat'])->middleware('permission:view_own_leave|view_all_leave')->name('my-requests.heartbeat');
        Route::get('/create', [TravelPermissionController::class, 'create'])->middleware('permission:apply_leave')->name('create');
        Route::middleware('permission:apply_leave')->group(function () {
            Route::post('/draft', [TravelPermissionController::class, 'saveDraft'])->name('draft.save');
            Route::post('/draft/discard', [TravelPermissionController::class, 'discardDraft'])->name('draft.discard');
            Route::get('/draft/preview', [TravelPermissionController::class, 'previewDraftLetter'])->name('draft.preview');
        });
        Route::get('/pending', [TravelPermissionController::class, 'pendingApprovals'])->middleware('permission:view_pending_approvals|view_all_leave')->name('pending');
        Route::get('/pending/heartbeat', [TravelPermissionController::class, 'pendingApprovalsHeartbeat'])->middleware('permission:view_pending_approvals|view_all_leave')->name('pending.heartbeat');
        Route::get('/approvals-history', [TravelPermissionController::class, 'approvalsHistory'])->middleware('permission:view_all_leave|view_school_leave|view_department_leave|view_own_leave')->name('approvals-history');
        Route::post('/', [TravelPermissionController::class, 'store'])->middleware('permission:apply_leave')->name('store');
        Route::get('/{id}/documents/{type}/download', [TravelPermissionController::class, 'downloadDocument'])->name('documents.download')->middleware('signed');
        Route::get('/acting/{token}', [TravelPermissionController::class, 'actingResponse'])->name('acting.response');
        Route::post('/acting/{id}/process', [TravelPermissionController::class, 'processActingResponse'])->name('acting.process');
        Route::post('/{id}/approve', [TravelPermissionController::class, 'approve'])->middleware('permission:approve_hod_level|approve_dean_level|approve_hr_level|approve_dvc_level')->name('approve');
        Route::post('/{id}/reject', [TravelPermissionController::class, 'reject'])->middleware('permission:reject_leave')->name('reject');
        Route::post('/{id}/cancel', [TravelPermissionController::class, 'cancel'])->middleware('permission:cancel_own_leave|reject_leave')->name('cancel');
        Route::get('/{id}/heartbeat', [TravelPermissionController::class, 'showHeartbeat'])->middleware('permission:view_all_leave|view_own_leave|view_department_leave|view_school_leave')->name('show.heartbeat');
        Route::get('/{id}', [TravelPermissionController::class, 'show'])->middleware('permission:view_all_leave|view_own_leave|view_department_leave|view_school_leave')->name('show');
    });

    // ============================================
    // SHORT PERMISSIONS
    // ============================================
    Route::prefix('permissions')->name('permissions.')->group(function () {
        Route::get('/my-requests', [PermissionRequestController::class, 'myRequests'])->middleware('permission:view_own_leave|view_all_leave')->name('my-requests');
        Route::get('/my-requests/heartbeat', [PermissionRequestController::class, 'myRequestsHeartbeat'])->middleware('permission:view_own_leave|view_all_leave')->name('my-requests.heartbeat');
        Route::get('/create', [PermissionRequestController::class, 'create'])->middleware('permission:apply_leave')->name('create');
        Route::get('/pending', [PermissionRequestController::class, 'pendingApprovals'])->middleware('permission:view_pending_approvals|view_all_leave')->name('pending');
        Route::get('/pending/heartbeat', [PermissionRequestController::class, 'pendingApprovalsHeartbeat'])->middleware('permission:view_pending_approvals|view_all_leave')->name('pending.heartbeat');
        Route::get('/approvals-history', [PermissionRequestController::class, 'approvalsHistory'])->middleware('permission:view_all_leave|view_school_leave|view_department_leave|view_own_leave')->name('approvals-history');
        Route::post('/', [PermissionRequestController::class, 'store'])->middleware('permission:apply_leave')->name('store');
        Route::get('/{id}/heartbeat', [PermissionRequestController::class, 'showHeartbeat'])->middleware('permission:view_all_leave|view_own_leave|view_department_leave|view_school_leave')->name('show.heartbeat');
        Route::get('/{id}', [PermissionRequestController::class, 'show'])->middleware('permission:view_all_leave|view_own_leave|view_department_leave|view_school_leave')->name('show');
        Route::post('/{id}/cancel', [PermissionRequestController::class, 'cancel'])->middleware('permission:cancel_own_leave|reject_leave')->name('cancel');
        Route::post('/hod/{id}/approve', [PermissionRequestController::class, 'hodApprove'])->middleware('permission:approve_hod_level')->name('hod.approve');
        Route::post('/hod/{id}/reject', [PermissionRequestController::class, 'hodReject'])->middleware('permission:reject_leave')->name('hod.reject');
        Route::post('/hr/{id}/approve', [PermissionRequestController::class, 'hrApprove'])->middleware('permission:approve_hr_level')->name('hr.approve');
        Route::post('/hr/{id}/reject', [PermissionRequestController::class, 'hrReject'])->middleware('permission:reject_leave')->name('hr.reject');
    });

    // ============================================
    // BLACKOUT PERIODS
    // ============================================
    Route::prefix('blackout')->name('blackout.')->group(function () {
        Route::get('/create', [BlackoutController::class, 'create'])
            ->middleware('permission:manage_blackout_periods')
            ->name('create');
        Route::post('/', [BlackoutController::class, 'store'])
            ->middleware('permission:manage_blackout_periods')
            ->name('store');
        Route::get('/check-date', [BlackoutController::class, 'checkDate'])
            ->middleware('permission:manage_blackout_periods')
            ->name('check-date');
        Route::get('/', [BlackoutController::class, 'index'])
            ->middleware('permission:manage_blackout_periods')
            ->name('index');
        Route::get('/{id}', [BlackoutController::class, 'show'])
            ->middleware('permission:manage_blackout_periods')
            ->name('show');
        Route::get('/{id}/edit', [BlackoutController::class, 'edit'])
            ->middleware('permission:manage_blackout_periods')
            ->name('edit');
        Route::put('/{id}', [BlackoutController::class, 'update'])
            ->middleware('permission:manage_blackout_periods')
            ->name('update');
        Route::delete('/{id}', [BlackoutController::class, 'destroy'])
            ->middleware('permission:manage_blackout_periods')
            ->name('destroy');
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
        Route::delete('/bulk', [NotificationController::class, 'bulkDestroy'])->name('bulk-destroy');
        Route::delete('/{id}', [NotificationController::class, 'destroy'])->name('destroy');
    });

    // ============================================
    // APPROVALS
    // ============================================
    // Dashboard's "Pending" stat is leave-focused (see
    // DashboardController::pendingApprovalsCount), so this sends everyone to
    // the real, role-scoped leave pending-approvals page - not a stub.
    Route::get('/approvals', function () {
        return redirect()->route('leave-requests.pending');
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

        // Attachment download - MUST be BEFORE /{id}
        Route::get('/attachments/{attachmentId}/download', [MessageController::class, 'downloadAttachment'])->name('attachments.download')->middleware('signed');

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
        Route::get('/', [AuditTrailController::class, 'index'])
            ->middleware('permission:view_audit_trail')
            ->name('index');
        Route::get('/export', [AuditTrailController::class, 'export'])
            ->middleware('permission:export_audit_data')
            ->name('export');
        Route::get('/{id}', [AuditTrailController::class, 'show'])
            ->middleware('permission:view_audit_trail')
            ->name('show');
    });

    // ============================================
    // DOCUMENT VERIFICATION LOG ROUTES
    // ============================================
    Route::prefix('verification-logs')->name('verification-logs.')->middleware(['auth', 'permission:view_audit_trail'])->group(function () {
        Route::get('/', [VerificationLogController::class, 'index'])->name('index');
        Route::get('/{id}', [VerificationLogController::class, 'show'])->name('show');
        Route::post('/{id}/archive', [VerificationLogController::class, 'archive'])->name('archive');
        Route::post('/{id}/unarchive', [VerificationLogController::class, 'unarchive'])->name('unarchive');
    });

    // ============================================
    // REPORTS
    // ============================================
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [ReportController::class, 'index'])
            ->middleware('permission:view_reports')
            ->name('index');
        Route::get('/custom', [ReportController::class, 'custom'])
            ->middleware('permission:view_reports')
            ->name('custom');
        Route::get('/custom/export/pdf', [ReportController::class, 'customExportPdf'])
            ->middleware('permission:export_pdf')
            ->name('custom.export.pdf');
        Route::get('/custom/export/excel', [ReportController::class, 'customExportExcel'])
            ->middleware('permission:export_excel')
            ->name('custom.export.excel');
        Route::get('/{slug}', [ReportController::class, 'show'])
            ->middleware('permission:view_reports')
            ->name('show');
        Route::get('/{slug}/pdf', [ReportController::class, 'exportPdf'])
            ->middleware('permission:export_pdf')
            ->name('export.pdf');
        Route::get('/{slug}/excel', [ReportController::class, 'exportExcel'])
            ->middleware('permission:export_excel')
            ->name('export.excel');
    });

    // ============================================
    // AUDIT LOGS
    // ============================================
    Route::prefix('audit-logs')->name('audit-logs.')->group(function () {
        Route::get('/', [AuditLogController::class, 'index'])
            ->middleware('permission:view_audit_logs')
            ->name('index');
        Route::get('/export', [AuditLogController::class, 'export'])
            ->middleware('permission:export_audit_data')
            ->name('export');
        Route::get('/user/{userId}', [AuditLogController::class, 'user'])
            ->middleware('permission:view_audit_logs')
            ->name('user');
    });

    // ============================================
    // SETTINGS - GENERAL
    // ============================================
    Route::middleware(['permission:view_settings|edit_settings'])->prefix('settings')->name('settings.')->group(function () {
        Route::get('/', [SettingsController::class, 'index'])->name('index');
        Route::get('/create', [SettingsController::class, 'create'])->name('create');
        Route::post('/', [SettingsController::class, 'store'])->name('store');
        Route::get('/hr', [HrSettingsController::class, 'index'])->middleware('permission:view_settings')->name('hr');
        Route::put('/hr', [HrSettingsController::class, 'update'])->middleware('permission:edit_settings')->name('hr.update');
        // The old "Leave Policies" page showed made-up numbers that nothing read.
        // The real settings, with where each value comes from, are on the
        // University Policies settings screen.
        Route::redirect('/policies', '/policies/settings')->middleware('permission:view_settings')->name('policies');

        // Every fixed-segment route below MUST stay above the `/{id}`
        // routes further down - Laravel matches route definitions in
        // registration order, so `/settings/bulk-update` (etc.) used to be
        // swallowed by `/settings/{id}` first (id="bulk-update"), which
        // called SettingsController::update()'s findOrFail() and 404'd.
        // This silently broke "Save All Settings" and every one of these
        // pages - moving them above `/{id}` is the actual fix, not a
        // stylistic reorder.
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

        Route::get('/{id}', [SettingsController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [SettingsController::class, 'edit'])->name('edit');
        Route::put('/{id}', [SettingsController::class, 'update'])->name('update');
        Route::delete('/{id}', [SettingsController::class, 'destroy'])->name('destroy');
    });

    // ============================================
    // UNIVERSITY POLICIES
    // ============================================
    Route::middleware(['permission:view_policies'])->prefix('policies')->name('policies.')->group(function () {

        // Policy rules management. `policy.manageable` limits every action
        // that changes a policy to its owner (see EnsurePolicyIsManageable);
        // scopeBindings() makes `{rule}` resolve only among this policy's own
        // rules, so a rule id from another policy can't be edited through it.
        Route::middleware(['permission:edit_policies', 'policy.manageable'])->prefix('{policy}/rules')->name('rules.')->scopeBindings()->group(function () {
            Route::get('/', [PolicyRuleController::class, 'index'])->name('index');
            Route::get('/create', [PolicyRuleController::class, 'create'])->name('create');
            Route::post('/', [PolicyRuleController::class, 'store'])->name('store');
            Route::get('/{rule}/edit', [PolicyRuleController::class, 'edit'])->name('edit');
            Route::put('/{rule}', [PolicyRuleController::class, 'update'])->name('update');
            Route::delete('/{rule}', [PolicyRuleController::class, 'destroy'])->name('destroy');
        });

        // Must come before '/{policy}', or "settings" would be read as a policy.
        Route::get('/settings', [PolicySettingsController::class, 'index'])->name('settings');

        Route::get('/', [PolicyController::class, 'index'])->name('index');
        Route::get('/create', [PolicyController::class, 'create'])->middleware('permission:create_policies')->name('create');
        Route::post('/', [PolicyController::class, 'store'])->middleware('permission:create_policies')->name('store');
        Route::get('/{policy}', [PolicyController::class, 'show'])->name('show');
        Route::get('/{policy}/edit', [PolicyController::class, 'edit'])->middleware(['permission:edit_policies', 'policy.manageable'])->name('edit');
        Route::put('/{policy}', [PolicyController::class, 'update'])->middleware(['permission:edit_policies', 'policy.manageable'])->name('update');
        Route::delete('/{policy}', [PolicyController::class, 'destroy'])->middleware(['permission:delete_policies', 'policy.manageable'])->name('destroy');

        // Copies a platform default into the user's own university as an editable draft.
        Route::post('/{policy}/customize', [PolicyController::class, 'customize'])->middleware('permission:create_policies')->name('customize');

        Route::get('/{policy}/approve', [PolicyController::class, 'approve'])->middleware(['permission:approve_policies', 'policy.manageable'])->name('approve');
        Route::post('/{policy}/approve', [PolicyController::class, 'storeApproval'])->middleware(['permission:approve_policies', 'policy.manageable'])->name('approve.store');
        Route::post('/{policy}/publish', [PolicyController::class, 'publish'])->middleware(['permission:publish_policies', 'policy.manageable'])->name('publish');
        Route::post('/{policy}/submit', [PolicyController::class, 'submitForApproval'])->middleware(['permission:edit_policies', 'policy.manageable'])->name('submit');
        Route::post('/{policy}/archive', [PolicyController::class, 'archive'])->middleware(['permission:archive_policies', 'policy.manageable'])->name('archive');
        Route::post('/{policy}/revoke', [PolicyController::class, 'revoke'])->middleware(['permission:archive_policies', 'policy.manageable'])->name('revoke');
        Route::get('/{policy}/versions', [PolicyController::class, 'versions'])->name('versions');
        Route::get('/{policy}/changes', [PolicyRuleController::class, 'history'])->name('changes');
    });

    // Public policy reader (no view_policies permission needed; for discipline/exam references)
    Route::get('/policy/{code}', [PolicyController::class, 'publicShow'])->name('policies.public');

    // ============================================
    // BACKUP
    // ============================================
    Route::prefix('backup')->name('backup.')->group(function () {
        Route::post('/create', [BackupController::class, 'create'])
            ->middleware('permission:create_backup')
            ->name('create');
        Route::get('/download/{filename}', [BackupController::class, 'download'])
            ->middleware('permission:create_backup')
            ->name('download');
        Route::delete('/delete/{filename}', [BackupController::class, 'delete'])
            ->middleware('permission:delete_backup')
            ->name('delete');
        Route::get('/restore/{filename}', [BackupController::class, 'restore'])
            ->middleware('permission:restore_backup')
            ->name('restore');
    });

    // ============================================
    // ADMIN API STATS
    // ============================================
    Route::middleware(['permission:admin_access'])->prefix('admin-api')->name('admin-api.')->group(function () {
        Route::get('/dashboard/stats', [AdminDashboardController::class, 'getStats'])->name('dashboard.stats');
    });

    // ============================================
    // VICE CHANCELLOR DASHBOARD
    // ============================================
    Route::prefix('vc')->name('vc.')->group(function () {
        Route::get('/dashboard', [VcDashboardController::class, 'index'])
            ->middleware('permission:view_institution_dashboard')
            ->name('dashboard');
        Route::get('/reports/monthly', [VcDashboardController::class, 'exportMonthlyReport'])
            ->middleware('permission:view_reports')
            ->name('reports.monthly');
        Route::get('/reports/excel', [VcDashboardController::class, 'exportExcel'])
            ->middleware('permission:export_excel')
            ->name('reports.excel');
        Route::get('/reports/pdf', [VcDashboardController::class, 'exportPdf'])
            ->middleware('permission:export_pdf')
            ->name('reports.pdf');
    });

    // ============================================
    // INTERNAL AUDITOR
    // ============================================
    Route::middleware(['permission:view_audit_logs'])->prefix('auditor')->name('auditor.')->group(function () {
        Route::get('/settings', [SettingsController::class, 'auditorSettings'])->name('settings');
        Route::put('/settings', [SettingsController::class, 'updateAuditorSettings'])->name('settings.update');
    });

    // ============================================
    // APPLICATIONS OVERVIEW (read-only, cross-module oversight)
    // ============================================
    Route::middleware(['permission:view_all_leave|view_pending_approvals'])->prefix('applications-overview')->name('applications-overview.')->group(function () {
        Route::get('/', [ApplicationsOverviewController::class, 'index'])->name('index');
        Route::post('/remind/{module}/{id}', [ApplicationsOverviewController::class, 'remind'])->name('remind');
    });

    // ============================================
    // ADMIN ROUTES
    // ============================================
    Route::middleware(['permission:admin_access'])->prefix('admin')->name('admin.')->group(function () {

        Route::middleware(['permission:import_staff'])->prefix('import')->name('import.')->group(function () {
            Route::get('/staff', [ImportController::class, 'index'])->name('staff');
            Route::post('/staff/upload', [ImportController::class, 'upload'])->name('staff.upload');
            Route::get('/staff/template', [ImportController::class, 'downloadTemplate'])->name('staff.template');
            Route::get('/progress/{id}', [ImportController::class, 'progress'])->name('progress');
            Route::get('/download-error/{id}', [ImportController::class, 'downloadErrorReport'])->name('download-error');
            Route::get('/campuses/{universityId}', [ImportController::class, 'getCampuses'])->name('campuses');
            Route::get('/schools/{campusId}', [ImportController::class, 'getSchools'])->name('schools');
            Route::get('/departments/{campusId}', [ImportController::class, 'getDepartments'])->name('departments');
            Route::get('/departments-by-school/{schoolId}', [ImportController::class, 'getDepartmentsBySchool'])->name('departments-by-school');
            Route::get('/universities', function () {
                return response()->json(University::where('status', 'active')->get());
            })->name('universities');
            Route::get('/quarterly-report', [ImportController::class, 'quarterlyReport'])->name('quarterly-report');
            Route::get('/details/{id}', [ImportController::class, 'getDetails'])->name('details');
            Route::delete('/delete/{id}', [ImportController::class, 'deleteImport'])->name('delete');
        });

        Route::middleware(['permission:manage_universities'])->group(function () {
            Route::resource('universities', UniversityController::class)->except(['show']);
        });

        Route::middleware(['permission:manage_campuses'])->group(function () {
            Route::resource('campuses', CampusController::class)->except(['show']);
        });

        Route::middleware(['permission:manage_schools'])->group(function () {
            Route::resource('schools', SchoolController::class)->except(['show']);
        });

        Route::middleware(['permission:manage_departments'])->group(function () {
            Route::resource('departments', DepartmentController::class);
        });

        Route::middleware(['permission:manage_leave_types'])->group(function () {
            Route::resource('leave-types', LeaveTypeController::class);
        });

        // Bulk cleanup of Leave/Permission/Travel requests (e.g. wiping demo
        // or test data) - permanent delete, admin-only.
        Route::prefix('cleanup')->name('cleanup.')->group(function () {
            Route::get('/', [RequestCleanupController::class, 'index'])->name('index');
            Route::post('/', [RequestCleanupController::class, 'destroy'])->name('destroy');
        });

        // Role Management
        Route::prefix('roles')->name('roles.')->group(function () {
            Route::get('/', [RoleController::class, 'index'])
                ->middleware('permission:view_roles')
                ->name('index');
            Route::get('/assign', [RoleController::class, 'assignForm'])
                ->middleware('permission:assign_roles')
                ->name('assign');
            Route::post('/assign', [RoleController::class, 'assignRole'])
                ->middleware('permission:assign_roles')
                ->name('assign.store');
            Route::delete('/remove/{userId}/{roleName}', [RoleController::class, 'removeRole'])
                ->middleware('permission:assign_roles')
                ->name('remove');
            Route::get('/create', [RoleController::class, 'createForm'])
                ->middleware('permission:manage_roles')
                ->name('create');
            Route::post('/create', [RoleController::class, 'storeRole'])
                ->middleware('permission:manage_roles')
                ->name('store');
            Route::get('/edit/{id}', [RoleController::class, 'editRole'])
                ->middleware('permission:manage_roles')
                ->name('edit');
            Route::put('/edit/{id}', [RoleController::class, 'updateRole'])
                ->middleware('permission:manage_roles')
                ->name('update');
            Route::delete('/delete/{id}', [RoleController::class, 'deleteRole'])
                ->middleware('permission:manage_roles')
                ->name('delete');
        });

        // LETTERHEAD SETTINGS
        Route::get('/settings/letterhead', [LetterheadController::class, 'index'])->name('settings.letterhead');
        Route::put('/settings/letterhead', [LetterheadController::class, 'update'])->name('settings.letterhead.update');
        Route::post('/settings/letterhead/preview', [LetterheadController::class, 'preview'])->name('settings.letterhead.preview');

        Route::middleware(['permission:manage_acting_dvc'])->group(function () {
            Route::resource('acting-dvc', ActingDvcAssignmentController::class);
        });

        Route::middleware(['permission:manage_departments'])->group(function () {
            Route::get('/departments/by-school/{schoolId}', [DepartmentController::class, 'getBySchool'])->name('departments.by-school');
            Route::get('/departments/schools-by-campus/{campusId}', [DepartmentController::class, 'getSchoolsByCampus'])->name('departments.schools-by-campus');
            Route::get('/departments/all-json', [DepartmentController::class, 'getAllJson'])->name('departments.all-json');
            Route::post('/departments/bulk-update', [DepartmentController::class, 'bulkUpdate'])->name('departments.bulk-update');
            Route::patch('/departments/{id}/toggle-status', [DepartmentController::class, 'toggleStatus'])->name('departments.toggle-status');
            Route::get('/departments/export', [DepartmentController::class, 'export'])->name('departments.export');
            Route::get('/departments/export/pdf', [DepartmentController::class, 'exportPdf'])->name('departments.export-pdf');
        });

        // ============================================
        // GATE ENTRY LOGS
        // ============================================
        Route::prefix('gate-entry-logs')->name('gate-entry-logs.')->group(function () {
            Route::get('/', [GateEntryLogController::class, 'index'])
                ->middleware('permission:view_gate_entry_logs')
                ->name('index');
            Route::get('/create', [GateEntryLogController::class, 'create'])
                ->middleware('permission:record_entry_exit')
                ->name('create');
            Route::post('/', [GateEntryLogController::class, 'store'])
                ->middleware('permission:record_entry_exit')
                ->name('store');
            Route::get('/scan', [GateEntryLogController::class, 'scan'])
                ->middleware('permission:record_entry_exit|verify_qr_codes')
                ->name('scan');
            Route::post('/verify', [GateEntryLogController::class, 'verify'])
                ->middleware('permission:record_entry_exit|verify_qr_codes')
                ->name('verify');
            Route::get('/{gateEntryLog}', [GateEntryLogController::class, 'show'])
                ->middleware('permission:view_gate_entry_logs')
                ->name('show');
        });

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
    // ID CARD & AUTHORIZED SIGNATURE ROUTES
    // Gated per-action by permission (not role:admin) so hr-officer/hr-manager
    // can be granted only the slice they need — see RoleAndPermissionSeeder.
    // ============================================
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::resource('id-cards', IdCardController::class)
            ->except(['edit', 'update'])
            ->middleware('permission:view_id_cards')
            ->middlewareFor(['create', 'store'], 'permission:create_id_cards')
            ->middlewareFor('destroy', 'permission:delete_id_cards');

        Route::resource('authorized-signatures', AuthorizedSignatureController::class)
            ->except(['show'])
            ->middleware('permission:view_authorized_signatures')
            ->middlewareFor(['create', 'store'], 'permission:create_authorized_signatures')
            ->middlewareFor(['edit', 'update'], 'permission:edit_authorized_signatures')
            ->middlewareFor('destroy', 'permission:delete_authorized_signatures');

        // Acting DVC approval (available to users with approve_acting_dvc outside admin_access)
        Route::middleware(['permission:approve_acting_dvc'])
            ->get('acting-dvc/{id}/approve', [ActingDvcAssignmentController::class, 'approve'])
            ->name('acting-dvc.approve');

        // Department leave tracking and department-level approvals
        Route::prefix('department-leave')->name('department-leave.')->group(function () {
            Route::get('/', [DepartmentLeaveController::class, 'index'])
                ->middleware('permission:track_department_leave|view_department_leave')
                ->name('index');
            Route::post('/{id}/approve', [DepartmentLeaveController::class, 'approve'])
                ->middleware('permission:approve_department_leave')
                ->name('approve');
            Route::post('/{id}/reject', [DepartmentLeaveController::class, 'reject'])
                ->middleware('permission:approve_department_leave|reject_leave')
                ->name('reject');
        });

        // Leave document preparation
        Route::prefix('leave-documents')->name('leave-documents.')->group(function () {
            Route::get('/', [LeaveDocumentController::class, 'index'])
                ->middleware('permission:prepare_leave_documents')
                ->name('index');
            Route::get('/{id}/prepare', [LeaveDocumentController::class, 'prepare'])
                ->middleware('permission:prepare_leave_documents')
                ->name('prepare');
            Route::post('/{id}/store', [LeaveDocumentController::class, 'store'])
                ->middleware('permission:prepare_leave_documents')
                ->name('store');
            Route::get('/{letter}/show', [LeaveDocumentController::class, 'show'])
                ->middleware('permission:prepare_leave_documents|view_own_leave|view_all_leave')
                ->name('show');
        });

        // Approval delegation management
        Route::resource('approval-delegations', ApprovalDelegationController::class)
            ->only(['index', 'create', 'store', 'destroy'])
            ->middleware('permission:delegate_approval');

        // Leave approval chain reordering
        Route::middleware(['permission:manage_leave_approval_chain'])
            ->prefix('leave-approval-chain')->name('leave-approval-chain.')->group(function () {
                Route::get('/', [LeaveApprovalChainController::class, 'index'])->name('index');
                Route::put('/{stageKey}/move-up', [LeaveApprovalChainController::class, 'moveUp'])->name('move-up');
                Route::put('/{stageKey}/move-down', [LeaveApprovalChainController::class, 'moveDown'])->name('move-down');
            });

        // Landing page announcements
        Route::resource('announcements', AnnouncementController::class)
            ->except(['show'])
            ->middleware('permission:manage_announcements');
        Route::put('announcements/{id}/move-up', [AnnouncementController::class, 'moveUp'])
            ->middleware('permission:manage_announcements')
            ->name('announcements.move-up');
        Route::put('announcements/{id}/move-down', [AnnouncementController::class, 'moveDown'])
            ->middleware('permission:manage_announcements')
            ->name('announcements.move-down');

        // Landing page calendar events
        Route::resource('events', AdminEventController::class)
            ->except(['show'])
            ->middleware('permission:manage_events');

        // Landing page team members
        Route::resource('team-members', TeamMemberController::class)
            ->except(['show'])
            ->middleware('permission:manage_team_members');
        Route::put('team-members/{id}/move-up', [TeamMemberController::class, 'moveUp'])
            ->middleware('permission:manage_team_members')
            ->name('team-members.move-up');
        Route::put('team-members/{id}/move-down', [TeamMemberController::class, 'moveDown'])
            ->middleware('permission:manage_team_members')
            ->name('team-members.move-down');

        // Academic year / leave year
        Route::resource('academic-years', AcademicYearController::class)
            ->except(['show'])
            ->middleware('permission:manage_academic_years');
        Route::put('academic-years/{id}/activate', [AcademicYearController::class, 'activate'])
            ->middleware('permission:manage_academic_years')
            ->name('academic-years.activate');
        Route::put('academic-years/{id}/set-semester', [AcademicYearController::class, 'setSemester'])
            ->middleware('permission:manage_academic_years')
            ->name('academic-years.set-semester');
    });

    // ============================================
    // SUPER ADMIN ROUTES (Tenant Management)
    // ============================================
    Route::middleware(['permission:manage_tenants'])->prefix('admin')->name('admin.')->group(function () {
        Route::resource('tenants', TenantSettingsController::class);
        Route::get('tenants/{id}/settings', [TenantSettingsController::class, 'settings'])->name('tenants.settings');
        Route::put('tenants/{id}/settings', [TenantSettingsController::class, 'updateSettings'])->name('tenants.settings.update');
    });

    // ============================================
    // LETTER SYSTEM (For ALL authenticated users)
    // ============================================
    Route::prefix('letters')->name('letters.')->group(function () {
        // Basic CRUD
        Route::get('/', [LetterController::class, 'index'])->middleware('permission:view_own_leave|view_all_leave|view_department_leave|view_school_leave')->name('index');
        Route::get('/create', [LetterController::class, 'create'])->middleware('permission:apply_leave|view_all_leave|view_department_leave|view_school_leave')->name('create');
        Route::post('/', [LetterController::class, 'store'])->middleware('permission:apply_leave|view_all_leave|view_department_leave|view_school_leave')->name('store');
        Route::get('/{id}', [LetterController::class, 'show'])->middleware('permission:view_own_leave|view_all_leave|view_department_leave|view_school_leave')->name('show');
        Route::get('/{id}/edit', [LetterController::class, 'edit'])->middleware('permission:view_own_leave|view_all_leave|view_department_leave|view_school_leave')->name('edit');
        Route::put('/{id}', [LetterController::class, 'update'])->middleware('permission:view_own_leave|view_all_leave|view_department_leave|view_school_leave')->name('update');
        Route::delete('/{id}', [LetterController::class, 'destroy'])->middleware('permission:view_own_leave|view_all_leave|view_department_leave|view_school_leave')->name('destroy');

        // Actions
        Route::post('/{id}/submit', [LetterController::class, 'submit'])->middleware('permission:apply_leave|view_all_leave|view_department_leave|view_school_leave')->name('submit');
        Route::get('/{id}/print', [LetterController::class, 'print'])->middleware('permission:view_own_leave|view_all_leave|view_department_leave|view_school_leave')->name('print');
        Route::get('/{id}/view', [LetterController::class, 'view'])->middleware('permission:view_own_leave|view_all_leave|view_department_leave|view_school_leave')->name('view');
        Route::get('/{id}/pdf', [LetterController::class, 'downloadPdf'])->middleware('permission:view_own_leave|view_all_leave|view_department_leave|view_school_leave')->name('pdf');

        // Multi-Level Approvals (HOD → Dean → DVC → VC)
        Route::post('/{id}/hod-approve', [LetterController::class, 'hodApprove'])->middleware('permission:approve_hod_level')->name('hod.approve');
        Route::post('/{id}/dean-approve', [LetterController::class, 'deanApprove'])->middleware('permission:approve_dean_level')->name('dean.approve');
        Route::post('/{id}/dvc-approve', [LetterController::class, 'dvcApprove'])->middleware('permission:approve_dvc_level')->name('dvc.approve');
        Route::post('/{id}/vc-sign', [LetterController::class, 'vcSign'])->middleware('permission:sign_vc_letters')->name('vc.sign');

        // Reject route
        Route::post('/{id}/reject', [LetterController::class, 'reject'])->middleware('permission:reject_leave')->name('reject');

        // Approval Queues
        Route::get('/pending/approval', [LetterController::class, 'pendingApproval'])->middleware('permission:view_pending_approvals|view_all_leave')->name('pending.approval');
        Route::get('/pending/signature', [LetterController::class, 'pendingSignature'])->middleware('permission:view_pending_approvals|view_all_leave')->name('pending.signature');
    });

    // ============================================
    // LEAVE LETTERS ROUTES (For leave approval letters)
    // ============================================
    Route::middleware(['permission:view_own_leave|view_all_leave|view_department_leave|view_school_leave'])->prefix('leave-letters')->name('leave-letters.')->group(function () {
        Route::get('/', [LeaveLetterController::class, 'index'])->name('index');
        Route::get('/{id}', [LeaveLetterController::class, 'show'])->name('show');
        Route::get('/{id}/preview', [LeaveLetterController::class, 'preview'])->name('preview');
        Route::get('/{id}/print', [LeaveLetterController::class, 'print'])->name('print');
        Route::get('/{id}/download-pdf', [LeaveLetterController::class, 'downloadPdf'])->name('download-pdf');
    });

    Route::middleware(['permission:view_own_leave|view_all_leave|view_department_leave|view_school_leave'])->prefix('travel-letters')->name('travel-letters.')->group(function () {
        Route::get('/{id}', [TravelLetterController::class, 'show'])->name('show');
        Route::get('/{id}/preview', [TravelLetterController::class, 'preview'])->name('preview');
        Route::get('/{id}/print', [TravelLetterController::class, 'print'])->name('print');
        Route::get('/{id}/download-pdf', [TravelLetterController::class, 'downloadPdf'])->name('download-pdf');
    });

    // ============================================
    // GRADUATION VERIFICATION (first GMSA slice built inside sms_demo -
    // see gmsa's own CLAUDE.md "SUPERSEDED" notice). Dedicated permissions
    // (view_students / generate_graduation_letters), assignable to any
    // role via Permission Management - super-admin/admin get both
    // automatically (they get every permission), registrar gets
    // view_students only (read-only, matching that role's own purpose).
    // Also gated by `addon:academic_records` - an opt-in module per
    // institution (see EnsureAddonEnabled), so having the permission role
    // is not enough on its own if the institution hasn't been granted the
    // add-on.
    // ============================================
    Route::middleware(['permission:view_students', 'addon:academic_records'])->prefix('students')->name('students.')->group(function () {
        Route::get('/', [StudentController::class, 'index'])->name('index');

        // Graduation Roster bulk import - a separate permission from
        // view_students/generate_graduation_letters since it writes
        // records for the whole institution, not just a single student's
        // letters. Registered before the `/{student}` route below, or
        // "students/import" would be swallowed by that wildcard.
        Route::middleware(['permission:import_students'])->prefix('import')->name('import.')->group(function () {
            Route::get('/', [StudentImportController::class, 'index'])->name('index');
            Route::get('/programmes/{school}', [StudentImportController::class, 'programmesBySchool'])->name('programmes-by-school');
            Route::get('/template', [StudentImportController::class, 'downloadTemplate'])->name('template');
            Route::get('/trace', [StudentImportController::class, 'trace'])->name('trace');
            Route::get('/updates', [StudentImportController::class, 'recentUpdates'])->name('updates');
            Route::get('/updates/{studentGraduation}', [StudentImportController::class, 'showUpdate'])->name('updates.show');
            Route::post('/updates/{studentGraduation}/accept', [StudentImportController::class, 'acceptUpdate'])->name('updates.accept');
            Route::post('/updates/{studentGraduation}/reject', [StudentImportController::class, 'rejectUpdate'])->name('updates.reject');
            Route::post('/', [StudentImportController::class, 'upload'])->name('upload');
            Route::post('/stage', [StudentImportController::class, 'stage'])->name('stage');
            Route::get('/{studentRosterImport}', [StudentImportController::class, 'show'])->name('show');
            Route::post('/{studentRosterImport}/process', [StudentImportController::class, 'processChunk'])->name('process');
            Route::post('/{studentRosterImport}/close', [StudentImportController::class, 'close'])->name('close');
            Route::post('/{studentRosterImport}/reopen', [StudentImportController::class, 'reopen'])->name('reopen');
            Route::post('/{studentRosterImport}/reset', [StudentImportController::class, 'reset'])->name('reset');
            Route::post('/{studentRosterImport}/programme', [StudentImportController::class, 'reassignProgramme'])->name('reassign-programme');
            Route::post('/{studentRosterImport}/year', [StudentImportController::class, 'reassignYear'])->name('reassign-year');
            Route::get('/{studentRosterImport}/students', [StudentImportController::class, 'batchStudents'])->name('batch-students');
        });

        // Academic Catalog (Faculty/School, Programme, Graduation Ceremony)
        // management - same `import_students` permission as the roster
        // import itself, since it's the same person maintaining both.
        // Registered before `/{student}` below for the same reason as
        // `import` above - a literal prefix must come before the wildcard.
        Route::middleware(['permission:import_students'])->prefix('catalog')->name('catalog.')->group(function () {
            Route::get('/', [AcademicCatalogController::class, 'index'])->name('index');
            Route::post('/schools', [AcademicCatalogController::class, 'storeSchool'])->name('schools.store');
            Route::post('/programs', [AcademicCatalogController::class, 'storeProgram'])->name('programs.store');
            Route::put('/programs/{program}', [AcademicCatalogController::class, 'updateProgram'])->name('programs.update');
            Route::put('/programs/{program}/duration', [AcademicCatalogController::class, 'updateProgramDuration'])->name('programs.duration.update');
            Route::post('/graduation-ceremonies', [AcademicCatalogController::class, 'storeGraduationCeremony'])->name('graduation-ceremonies.store');
            Route::post('/graduation-ceremonies/{graduationCeremony}/book-cover', [AcademicCatalogController::class, 'updateBookCover'])->name('graduation-ceremonies.book-cover.update');
        });

        // Anyone who can view graduates can see a ceremony's book cover.
        Route::get('/book-covers/{graduationCeremony}', [AcademicCatalogController::class, 'showBookCover'])->name('book-cover');

        Route::get('/statistics', [StudentStatisticsController::class, 'index'])->name('statistics');
        Route::get('/statistics/programs', [StudentStatisticsController::class, 'programGender'])->name('statistics.programs');
        Route::get('/statistics/program-charts', [StudentStatisticsController::class, 'programCharts'])->name('statistics.program-charts');
        Route::get('/filtered', [StudentController::class, 'filtered'])->name('filtered');

        Route::middleware(['permission:import_students'])->prefix('certificates')->name('certificates.')->group(function () {
            Route::get('/', [CertificateNumberUploadController::class, 'index'])->name('index');
            Route::post('/', [CertificateNumberUploadController::class, 'store'])->name('store');
            Route::get('/stats', [CertificateNumberUploadController::class, 'stats'])->name('stats');
            Route::get('/template', [CertificateNumberUploadController::class, 'downloadTemplate'])->name('template');
            Route::get('/{certificateNumberUpload}', [CertificateNumberUploadController::class, 'show'])->name('show');
            Route::post('/{certificateNumberUpload}/close', [CertificateNumberUploadController::class, 'close'])->name('close');
            Route::post('/{certificateNumberUpload}/reopen', [CertificateNumberUploadController::class, 'reopen'])->name('reopen');
        });

        Route::get('/{student}', [StudentController::class, 'show'])->name('show');
        Route::get('/{student}/edit', [StudentController::class, 'edit'])->middleware('permission:import_students')->name('edit');
        Route::put('/{student}', [StudentController::class, 'update'])->middleware('permission:import_students')->name('update');
        Route::get('/{student}/compose-letter', [GraduationLetterController::class, 'create'])->middleware('permission:generate_graduation_letters')->name('compose-letter');
    });

    Route::middleware(['addon:academic_records'])->prefix('graduation-letters')->name('graduation-letters.')->group(function () {
        Route::post('/{student}/generate', [GraduationLetterController::class, 'generate'])->middleware('permission:generate_graduation_letters')->name('generate');
        Route::post('/{student}/store', [GraduationLetterController::class, 'store'])->middleware('permission:generate_graduation_letters')->name('store');
        Route::get('/{id}', [GraduationLetterController::class, 'show'])->middleware('permission:view_students')->name('show');
        Route::get('/{id}/preview', [GraduationLetterController::class, 'preview'])->middleware('permission:view_students')->name('preview');
        Route::get('/{id}/print', [GraduationLetterController::class, 'print'])->middleware('permission:view_students')->name('print');
        Route::get('/{id}/download-pdf', [GraduationLetterController::class, 'downloadPdf'])->middleware('permission:view_students')->name('download-pdf');
    });

    // Certificate / transcript verification letters sent to outside parties.
    // Viewing needs view_students; only the Examinations Officer prepares them
    // (prepare_verification_letters), and only an administrator manages who may
    // sign (manage_letter_signatories). Each is printed and signed by hand.
    Route::middleware(['addon:academic_records'])->prefix('verification-letters')->name('verification-letters.')->group(function () {
        Route::get('/', [VerificationLetterController::class, 'index'])->middleware('permission:view_students')->name('index');

        Route::middleware('permission:prepare_verification_letters')->group(function () {
            Route::get('/create', [VerificationLetterController::class, 'create'])->name('create');
            Route::get('/graduates', [VerificationLetterController::class, 'graduates'])->name('graduates');
            Route::post('/preview', [VerificationLetterController::class, 'preview'])->name('preview');
            Route::post('/', [VerificationLetterController::class, 'store'])->name('store');
        });

        Route::middleware('permission:manage_letter_signatories')->group(function () {
            Route::get('/signatories', [VerificationLetterController::class, 'signatories'])->name('signatories');
            Route::post('/signatories', [VerificationLetterController::class, 'storeSignatory'])->name('signatories.store');
            Route::post('/signatories/{signatory}/toggle', [VerificationLetterController::class, 'toggleSignatory'])->name('signatories.toggle');
        });

        Route::middleware('permission:view_students')->group(function () {
            Route::get('/{id}', [VerificationLetterController::class, 'show'])->name('show');
            Route::get('/{id}/letter', [VerificationLetterController::class, 'letter'])->name('letter');
            Route::get('/{id}/print', [VerificationLetterController::class, 'print'])->name('print');
            Route::get('/{id}/download-pdf', [VerificationLetterController::class, 'downloadPdf'])->name('download-pdf');
        });
    });

    // ============================================
    // CENTRAL REGISTRY SYSTEM (Registry Officers & Admin only)
    // ============================================
    Route::middleware(['permission:manage_registry'])->prefix('registry')->name('registry.')->group(function () {

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
        Route::get('incoming/{id}/download', [IncomingLetterController::class, 'downloadPdf'])->name('incoming.download');

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
    Route::get('/api/users/search', [MessageController::class, 'searchUsers'])->name('api.users.search');

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
Route::get('/test-hold', function () {
    return 'Test route works!';
});

Route::get('/test-approvals', [LeaveRequestController::class, 'approvalsHistory'])->name('test.approvals');

Route::get('/test-schema', function () {
    $columns = Schema::getColumnListing('leave_requests');

    return response()->json($columns);
});

// ============================================
// FALLBACK ROUTE (404)
// ============================================
Route::fallback(function () {
    return response()->view('errors.404', [], 404);
});

Route::get('/test-messages', function () {
    return 'Messages route test works!';
});
