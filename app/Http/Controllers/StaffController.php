<?php

namespace App\Http\Controllers;

use App\Mail\StaffAccountCreated;
use App\Models\Campus;
use App\Models\Department;
use App\Models\LeaveBalance;
use App\Models\LeaveRequest;
use App\Models\School;
use App\Models\University;
use App\Models\User;
use App\Traits\LogsActivity;
use App\Traits\PerPageTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class StaffController extends Controller
{
    use LogsActivity;
    use PerPageTrait;

    /**
     * Display list of staff members (Role-based visibility)
     */
    public function index(Request $request)
    {
        if ($request->has('per_page')) {
            session(['staff_per_page' => $request->per_page]);
        }

        $perPage = $request->get('per_page', session('staff_per_page', 15));
        $user = auth()->user();
        $role = $user->getRoleNames()->first();

        // Apply role-based visibility to staff listing
        $staffQuery = User::with('department', 'roles')
            ->visibleStaff()
            ->orderBy('created_at', 'desc');

        // The search box used to be client-side JS filtering the rendered table
        // rows only — with pagination that meant it could only ever "find" staff
        // already on the current page. Filtering the query itself instead makes
        // it search the whole (role-visible) staff list, not just one page.
        if ($search = $request->get('search')) {
            $staffQuery->where(function ($query) use ($search) {
                $query->where('first_name', 'like', "%{$search}%")
                    ->orWhere('middle_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('staff_id', 'like', "%{$search}%");
            });
        }

        $staffMembers = $staffQuery->paginate($perPage)->appends($request->query());

        // Statistics also need to respect role-based visibility
        $baseQuery = User::query();

        switch ($role) {
            case 'admin':
                // A plain admin's stats are scoped to their own institute;
                // only super-admin (cross-institute) sees the global totals.
                if (! $user->hasRole('super-admin')) {
                    $baseQuery->where('university_id', $user->university_id);
                }
                break;
            case 'hr-officer':
                $baseQuery->where('university_id', $user->university_id);
                break;
            case 'dean':
                $baseQuery->whereHas('department.school', function ($q) use ($user) {
                    $q->where('dean_staff_id', $user->id);
                });
                break;
            case 'hod':
            case 'unit_head':
                $baseQuery->where('department_id', $user->department_id);
                break;
            case 'dvca':
                $baseQuery->where('staff_category', 'academic');
                break;
            case 'dvc-pfa':
                $baseQuery->whereIn('staff_category', ['administrative', 'supporting']);
                break;
            default:
                $baseQuery->where('id', $user->id);
        }

        $totalStaff = (clone $baseQuery)->count();
        $academicStaff = (clone $baseQuery)->where('staff_category', 'academic')->count();
        $administrativeStaff = (clone $baseQuery)->where('staff_category', 'administrative')->count();
        $supportingStaff = (clone $baseQuery)->where('staff_category', 'supporting')->count();
        $newThisMonth = (clone $baseQuery)->whereMonth('created_at', now()->month)->count();

        $departments = $user->hasRole('super-admin')
            ? Department::all()
            : Department::where('campus_id', $user->campus_id)->get();

        // The live search (and its pagination/per-page controls) fetch this same
        // route via AJAX and swap in just the results partial + a fresh stats
        // line, instead of a full page reload — see resources/views/admin/staff/
        // index.blade.php. $totalStaff here is already the role-visible grand
        // total (unaffected by the search filter), which is what "grand_total"
        // in the response represents.
        if ($request->wantsJson()) {
            return response()->json([
                'html' => view('admin.staff._results', compact('staffMembers'))->render(),
                'total' => $staffMembers->total(),
                'grand_total' => $totalStaff,
            ]);
        }

        return view('admin.staff.index', compact(
            'staffMembers', 'departments', 'totalStaff',
            'academicStaff', 'administrativeStaff', 'supportingStaff', 'newThisMonth'
        ));
    }

    /**
     * Show form to create new staff
     */
    public function create(Request $request)
    {
        $user = auth()->user();
        // A plain 'admin' runs one institute - starting them at "pick a
        // university" is both pointless and lets them file a staff member
        // under an institute that isn't theirs. Only 'super-admin' (managing
        // more than one institute) gets the full University dropdown.
        $isSuperAdmin = $user->hasRole('super-admin');

        if ($isSuperAdmin) {
            $universities = University::where('status', 'active')->get();
            $campuses = collect();
        } else {
            $universities = collect();
            $campuses = Campus::where('university_id', $user->university_id)->where('status', 'active')->get();
        }

        $schools = collect();
        $departments = collect();
        $roles = ['staff', 'hod', 'hr-officer', 'admin'];
        $supervisorsQuery = User::where('status', 'active');
        if (! $isSuperAdmin) {
            $supervisorsQuery->where('university_id', $user->university_id);
        }
        $supervisors = $supervisorsQuery->get();

        return view('admin.staff.create', compact('universities', 'campuses', 'schools', 'departments', 'roles', 'supervisors', 'isSuperAdmin'));
    }

    /**
     * Store new staff member
     */
    public function store(Request $request)
    {
        $user = auth()->user();
        $isSuperAdmin = $user->hasRole('super-admin');

        $request->validate([
            // Basic Info
            'title' => 'nullable|string|in:Mr,Mrs,Ms,Dr,Prof,Eng,Rev',
            'gender' => 'nullable|string|in:male,female,other',
            'date_of_birth' => 'nullable|date',
            'national_id' => 'nullable|string|max:50',
            'first_name' => 'required|string|max:100',
            'middle_name' => 'nullable|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'required|email|unique:staff,email',
            'phone' => 'nullable|string|max:20',
            'marital_status' => 'nullable|string|in:single,married,divorced,widowed',
            'spouse_name' => 'nullable|string|max:255',

            // Employment Info
            'role' => 'required|string',
            'staff_category' => 'nullable|string|in:academic,administrative,supporting',
            'job_title' => 'nullable|string|max:255',
            'job_grade' => 'nullable|string|max:50',
            'joining_date' => 'nullable|date',
            'contract_type' => 'nullable|string|in:permanent,contract,temporary,probation',
            'contract_end_date' => 'nullable|date|after:joining_date',
            'tin_number' => 'nullable|string|max:50',
            'nssf_number' => 'nullable|string|max:50',
            'qualification' => 'nullable|string|max:255',
            'reporting_to' => 'nullable|exists:staff,id',

            // Location - a plain admin never submits university_id (it's not
            // in their form at all), so it's only required when they could
            // actually have chosen one.
            'university_id' => $isSuperAdmin ? 'required|exists:universities,id' : 'nullable',
            'campus_id' => 'required|exists:campuses,id',
            'school_id' => 'nullable|exists:schools,id',
            'department_id' => 'required|exists:departments,id',
        ]);

        // Never trust the client for this: a non-super-admin is always
        // pinned to their own institute regardless of what the form posted.
        $universityId = $isSuperAdmin ? $request->university_id : $user->university_id;

        $lastStaff = User::orderBy('id', 'desc')->first();
        $nextId = ($lastStaff ? $lastStaff->id + 1 : 1);
        $staffId = 'STF'.str_pad($nextId, 4, '0', STR_PAD_LEFT);
        // Fixed, memorable temp password (matches the bulk-import convention in
        // ProcessImportJob/ImportStaffIdHr) instead of a random one - a random
        // password is only ever shown once in a flash message, and if that gets
        // missed or misrelayed the staff member can never log in.
        $temporaryPassword = 'Temp@123';

        DB::beginTransaction();

        try {
            $staff = User::create([
                // Basic Info
                'staff_id' => $staffId,
                'title' => $request->title,
                'gender' => $request->gender,
                'date_of_birth' => $request->date_of_birth,
                'national_id' => $request->national_id,
                'first_name' => $request->first_name,
                'middle_name' => $request->middle_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'marital_status' => $request->marital_status,
                'spouse_name' => $request->spouse_name,
                'password' => Hash::make($temporaryPassword),
                'password_change_required' => true,
                'password_changed_at' => now(),
                'status' => 'active',

                // Employment Info
                'staff_category' => $request->staff_category ?: $this->getCategoryFromRole($request->role),
                'job_title' => $request->job_title,
                'job_grade' => $request->job_grade,
                'joining_date' => $request->joining_date ?? now(),
                'contract_type' => $request->contract_type,
                'contract_end_date' => $request->contract_end_date,
                'tin_number' => $request->tin_number,
                'nssf_number' => $request->nssf_number,
                'qualification' => $request->qualification,
                'reporting_to' => $request->reporting_to,

                // Location
                'university_id' => $universityId,
                'campus_id' => $request->campus_id,
                'school_id' => $request->school_id ?: null,
                'department_id' => $request->department_id,
            ]);

            $staff->assignRole($request->role);

            DB::commit();

            $fullName = $this->formatFullName($staff);

            $this->logStaffCreated($staff, 'manual');

            try {
                Mail::to($staff->email)->send(new StaffAccountCreated($staff, $temporaryPassword));
                $emailNote = ' An activation email was sent to '.$staff->email.'.';
            } catch (\Exception $e) {
                Log::error('Failed to send staff account creation email: '.$e->getMessage());
                $emailNote = ' Could not send the activation email - please relay the credentials below manually.';
            }

            return redirect()->route('staff.index')
                ->with('success', "Staff '{$fullName}' created. Temporary password: {$temporaryPassword}.{$emailNote}");

        } catch (\Exception $e) {
            DB::rollBack();

            return back()->with('error', 'Failed to create staff: '.$e->getMessage());
        }
    }

    /**
     * Display staff details with all relationships
     */
    public function show($id)
    {
        $staff = User::with([
            'university', 'campus', 'school', 'department', 'roles', 'reportsTo',
            'qualifications', 'skills', 'languages', 'certifications',
            'awards', 'workExperience', 'memberships', 'publications',
            'trainings', 'contracts', 'phones', 'emails',
            'addresses', 'bankDetails', 'emergencyContacts', 'dependents',
        ])->findOrFail($id);

        // Check if current user can view this staff member
        if (! auth()->user()->canViewStaff($staff)) {
            abort(403, 'You do not have permission to view this staff member.');
        }

        if (auth()->id() !== $staff->id) {
            $this->logActivity('view', 'Staff', $staff->id);
        }

        $staffRole = $staff->roles->first()->name ?? 'staff';

        $leaveBalances = LeaveBalance::with('leaveType')
            ->where('staff_id', $id)
            ->get();

        $totalLeaveRequests = LeaveRequest::where('staff_id', $id)->count();
        $approvedLeave = LeaveRequest::where('staff_id', $id)
            ->where('status', 'approved')
            ->count();
        $pendingLeave = LeaveRequest::where('staff_id', $id)
            ->where('status', 'pending')
            ->count();

        return view('admin.staff.show', compact(
            'staff',
            'staffRole',
            'leaveBalances',
            'totalLeaveRequests',
            'approvedLeave',
            'pendingLeave'
        ));
    }

    /**
     * Show form to edit staff
     */
    public function edit($id)
    {
        $staff = User::with('roles')->findOrFail($id);

        // Check if current user can edit this staff member
        if (! auth()->user()->canViewStaff($staff)) {
            abort(403, 'You do not have permission to edit this staff member.');
        }

        $isSuperAdmin = auth()->user()->hasRole('super-admin');
        $ownUniversityId = auth()->user()->university_id;

        $departmentsQuery = Department::where('status', 'active');
        $campusesQuery = Campus::where('status', 'active');
        $schoolsQuery = School::where('status', 'active');
        $universitiesQuery = University::where('status', 'active');
        $supervisorsQuery = User::where('status', 'active')->where('id', '!=', $id);

        if (! $isSuperAdmin) {
            $departmentsQuery->whereHas('campus', function ($q) use ($ownUniversityId) {
                $q->where('university_id', $ownUniversityId);
            });
            $campusesQuery->where('university_id', $ownUniversityId);
            $schoolsQuery->whereHas('campus', function ($q) use ($ownUniversityId) {
                $q->where('university_id', $ownUniversityId);
            });
            $universitiesQuery->where('id', $ownUniversityId);
            $supervisorsQuery->where('university_id', $ownUniversityId);
        }

        $departments = $departmentsQuery->get();
        $campuses = $campusesQuery->get();
        $schools = $schoolsQuery->get();
        $universities = $universitiesQuery->get();
        $roles = ['staff', 'hod', 'hr-officer', 'admin'];
        $staffRole = $staff->roles->first()->name ?? 'staff';
        $supervisors = $supervisorsQuery->get();

        return view('admin.staff.edit', compact('staff', 'departments', 'campuses', 'schools', 'universities', 'roles', 'staffRole', 'supervisors', 'isSuperAdmin'));
    }

    /**
     * Update staff member
     */
    public function update(Request $request, $id)
    {
        $staff = User::findOrFail($id);

        // Check if current user can edit this staff member
        if (! auth()->user()->canViewStaff($staff)) {
            abort(403, 'You do not have permission to edit this staff member.');
        }

        $oldValues = [
            'title' => $staff->title,
            'gender' => $staff->gender,
            'date_of_birth' => $staff->date_of_birth,
            'national_id' => $staff->national_id,
            'first_name' => $staff->first_name,
            'middle_name' => $staff->middle_name,
            'last_name' => $staff->last_name,
            'email' => $staff->email,
            'phone' => $staff->phone,
            'marital_status' => $staff->marital_status,
            'spouse_name' => $staff->spouse_name,
            'staff_category' => $staff->staff_category,
            'job_title' => $staff->job_title,
            'job_grade' => $staff->job_grade,
            'joining_date' => $staff->joining_date,
            'contract_type' => $staff->contract_type,
            'contract_end_date' => $staff->contract_end_date,
            'tin_number' => $staff->tin_number,
            'nssf_number' => $staff->nssf_number,
            'qualification' => $staff->qualification,
            'reporting_to' => $staff->reporting_to,
            'status' => $staff->status,
            'university_id' => $staff->university_id,
            'campus_id' => $staff->campus_id,
            'school_id' => $staff->school_id,
            'department_id' => $staff->department_id,
        ];

        $request->validate([
            'title' => 'nullable|string|in:Mr,Mrs,Ms,Dr,Prof,Eng,Rev',
            'gender' => 'nullable|string|in:male,female,other',
            'date_of_birth' => 'nullable|date',
            'national_id' => 'nullable|string|max:50',
            'first_name' => 'required|string|max:100',
            'middle_name' => 'nullable|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'required|email|unique:staff,email,'.$id,
            'phone' => 'nullable|string|max:20',
            'marital_status' => 'nullable|string|in:single,married,divorced,widowed',
            'spouse_name' => 'nullable|string|max:255',
            'role' => 'required|string',
            'staff_category' => 'nullable|string|in:academic,administrative,supporting',
            'job_title' => 'nullable|string|max:255',
            'job_grade' => 'nullable|string|max:50',
            'joining_date' => 'nullable|date',
            'contract_type' => 'nullable|string|in:permanent,contract,temporary,probation',
            'contract_end_date' => 'nullable|date|after:joining_date',
            'tin_number' => 'nullable|string|max:50',
            'nssf_number' => 'nullable|string|max:50',
            'qualification' => 'nullable|string|max:255',
            'reporting_to' => 'nullable|exists:staff,id',
            'university_id' => 'required|exists:universities,id',
            'campus_id' => 'required|exists:campuses,id',
            'school_id' => 'nullable|exists:schools,id',
            'department_id' => 'required|exists:departments,id',
            'status' => 'required|in:active,inactive,suspended',
        ]);

        if (! auth()->user()->hasRole('super-admin')) {
            $campusBelongsToOwnUniversity = Campus::where('id', $request->campus_id)
                ->where('university_id', auth()->user()->university_id)
                ->exists();
            abort_unless($campusBelongsToOwnUniversity, 403, 'You cannot assign staff to a campus outside your institute.');
        }

        DB::beginTransaction();

        try {
            $staff->update([
                'title' => $request->title,
                'gender' => $request->gender,
                'date_of_birth' => $request->date_of_birth,
                'national_id' => $request->national_id,
                'first_name' => $request->first_name,
                'middle_name' => $request->middle_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'marital_status' => $request->marital_status,
                'spouse_name' => $request->spouse_name,
                'staff_category' => $request->staff_category ?: $this->getCategoryFromRole($request->role),
                'job_title' => $request->job_title,
                'job_grade' => $request->job_grade,
                'joining_date' => $request->joining_date,
                'contract_type' => $request->contract_type,
                'contract_end_date' => $request->contract_end_date,
                'tin_number' => $request->tin_number,
                'nssf_number' => $request->nssf_number,
                'qualification' => $request->qualification,
                'reporting_to' => $request->reporting_to,
                // A non-super-admin's edit form only ever offers their own
                // institute's options, but the request body isn't trusted -
                // force it server-side so a tampered POST can't move this
                // staff member into a different institute.
                'university_id' => auth()->user()->hasRole('super-admin') ? $request->university_id : $staff->university_id,
                'campus_id' => $request->campus_id,
                'school_id' => $request->school_id ?: null,
                'department_id' => $request->department_id,
                'status' => $request->status,
            ]);

            $staff->syncRoles([$request->role]);

            DB::commit();

            $newValues = [
                'title' => $staff->title,
                'gender' => $staff->gender,
                'date_of_birth' => $staff->date_of_birth,
                'national_id' => $staff->national_id,
                'first_name' => $staff->first_name,
                'middle_name' => $staff->middle_name,
                'last_name' => $staff->last_name,
                'email' => $staff->email,
                'phone' => $staff->phone,
                'marital_status' => $staff->marital_status,
                'spouse_name' => $staff->spouse_name,
                'staff_category' => $staff->staff_category,
                'job_title' => $staff->job_title,
                'job_grade' => $staff->job_grade,
                'joining_date' => $staff->joining_date,
                'contract_type' => $staff->contract_type,
                'contract_end_date' => $staff->contract_end_date,
                'tin_number' => $staff->tin_number,
                'nssf_number' => $staff->nssf_number,
                'qualification' => $staff->qualification,
                'reporting_to' => $staff->reporting_to,
                'status' => $staff->status,
                'university_id' => $staff->university_id,
                'campus_id' => $staff->campus_id,
                'school_id' => $staff->school_id,
                'department_id' => $staff->department_id,
            ];

            $this->logStaffUpdated($staff, $oldValues, $newValues);

            $fullName = $this->formatFullName($staff);

            return redirect()->route('staff.index')
                ->with('success', "Staff '{$fullName}' updated successfully.");

        } catch (\Exception $e) {
            DB::rollBack();

            return back()->with('error', 'Failed to update staff: '.$e->getMessage());
        }
    }

    /**
     * Reset staff password
     */
    public function resetPassword($id)
    {
        $staff = User::findOrFail($id);

        // Check if current user can edit this staff member
        if (! auth()->user()->canViewStaff($staff)) {
            abort(403, 'You do not have permission to reset password for this staff member.');
        }

        // Fixed, memorable temp password - see the note in store() above.
        $temporaryPassword = 'Temp@123';

        $fullName = $this->formatFullName($staff);

        $staff->update([
            'password' => Hash::make($temporaryPassword),
            'password_change_required' => true,
            'password_changed_at' => now(),
        ]);

        return redirect()->route('staff.index')
            ->with('success', "Password reset for '{$fullName}'. Temporary password: {$temporaryPassword}");
    }

    /**
     * Delete staff member
     */
    public function destroy($id)
    {
        $staff = User::findOrFail($id);

        // Check if current user can delete this staff member (only admin)
        $user = auth()->user();
        $role = $user->getRoleNames()->first();

        if ($role != 'admin') {
            abort(403, 'Only administrators can delete staff members.');
        }

        if ($staff->id == auth()->id()) {
            return redirect()->route('staff.index')
                ->with('error', 'You cannot delete your own account.');
        }

        $fullName = $this->formatFullName($staff);

        if ($staff->avatar && Storage::disk('public')->exists($staff->avatar)) {
            Storage::disk('public')->delete($staff->avatar);
        }

        $this->logStaffDeleted($staff);
        $staff->delete();

        return redirect()->route('staff.index')
            ->with('success', "Staff '{$fullName}' deleted successfully.");
    }

    /**
     * AJAX: Get campuses by university
     */
    public function getCampusesByUniversity($universityId)
    {
        $campuses = Campus::where('university_id', $universityId)
            ->where('status', 'active')
            ->get(['id', 'name']);

        return response()->json($campuses);
    }

    /**
     * AJAX: Get schools by campus
     */
    public function getSchoolsByCampus($campusId)
    {
        $schools = School::where('campus_id', $campusId)
            ->where('status', 'active')
            ->get(['id', 'name']);

        return response()->json($schools);
    }

    /**
     * AJAX: Get departments by school
     */
    public function getDepartmentsBySchool($schoolId)
    {
        $departments = Department::where('school_id', $schoolId)
            ->where('status', 'active')
            ->get(['id', 'name']);

        return response()->json($departments);
    }

    /**
     * AJAX: Get departments by campus, optionally filtered by school.
     */
    public function getDepartmentsByCampus($campusId, Request $request)
    {
        $query = Department::where('campus_id', $campusId)
            ->where('status', 'active')
            ->orderBy('name');

        if ($request->filled('school_id')) {
            $query->where('school_id', $request->school_id);
        }

        return response()->json($query->get(['id', 'name']));
    }

    /**
     * Export staff list to CSV.
     */
    public function export()
    {
        $staff = User::with(['department.school.campus.university', 'roles'])
            ->visibleStaff()
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();

        $columns = ['Staff ID', 'Title', 'First Name', 'Middle Name', 'Last Name', 'Email', 'Phone', 'Gender', 'Category', 'Department', 'School', 'Campus', 'University', 'Status', 'Roles'];

        $callback = function () use ($staff, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($staff as $s) {
                fputcsv($file, [
                    $s->staff_id,
                    $s->title,
                    $s->first_name,
                    $s->middle_name,
                    $s->last_name,
                    $s->email,
                    $s->phone,
                    $s->gender,
                    $s->staff_category,
                    $s->department?->name,
                    $s->department?->school?->name,
                    $s->department?->school?->campus?->name,
                    $s->department?->school?->campus?->university?->name,
                    $s->status,
                    $s->roles->pluck('name')->implode(', '),
                ]);
            }

            fclose($file);
        };

        return response()->streamDownload($callback, 'staff_export_'.now()->format('Ymd_His').'.csv');
    }

    /**
     * Helper: Format full name with title
     */
    private function formatFullName($staff)
    {
        $name = '';
        if ($staff->title) {
            $name .= $staff->title.' ';
        }
        $name .= $staff->first_name;
        if ($staff->middle_name) {
            $name .= ' '.$staff->middle_name;
        }
        $name .= ' '.$staff->last_name;

        return $name;
    }

    /**
     * Helper: Get staff category from role
     */
    private function getCategoryFromRole($role)
    {
        if ($role == 'hod' || $role == 'staff') {
            return 'academic';
        } elseif ($role == 'hr-officer' || $role == 'admin') {
            return 'administrative';
        }

        return 'supporting';
    }
}
