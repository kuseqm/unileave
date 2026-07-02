<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Department;
use App\Models\University;
use App\Models\Campus;
use App\Models\School;
use App\Traits\LogsActivity;
use App\Models\LeaveBalance;
use App\Models\LeaveRequest;
use App\Traits\PerPageTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

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
        $staffMembers = User::with('department', 'roles')
            ->visibleStaff()
            ->orderBy('created_at', 'desc')
            ->paginate($perPage)
            ->appends($request->query());
        
        // Statistics also need to respect role-based visibility
        $baseQuery = User::query();
        
        switch ($role) {
            case 'admin':
                // Admin sees all stats
                break;
            case 'hr-officer':
                $baseQuery->where('university_id', $user->university_id);
                break;
            case 'dean':
                $baseQuery->whereHas('department.school', function($q) use ($user) {
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
        
        $departments = Department::all();
        
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
        $universities = University::where('status', 'active')->get();
        $campuses = collect();
        $schools = collect();
        $departments = collect();
        $roles = ['staff', 'hod', 'hr-officer', 'admin'];
        $supervisors = User::where('status', 'active')->get();
        
        return view('admin.staff.create', compact('universities', 'campuses', 'schools', 'departments', 'roles', 'supervisors'));
    }

    /**
     * Store new staff member
     */
    public function store(Request $request)
    {
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
            
            // Location
            'university_id' => 'required|exists:universities,id',
            'campus_id' => 'required|exists:campuses,id',
            'school_id' => 'required|exists:schools,id',
            'department_id' => 'required|exists:departments,id',
        ]);

        $lastStaff = User::orderBy('id', 'desc')->first();
        $nextId = ($lastStaff ? $lastStaff->id + 1 : 1);
        $staffId = 'STF' . str_pad($nextId, 4, '0', STR_PAD_LEFT);

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
                'password' => Hash::make('Temp@123'),
                'password_change_required' => true,
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
                'university_id' => $request->university_id,
                'campus_id' => $request->campus_id,
                'school_id' => $request->school_id,
                'department_id' => $request->department_id,
            ]);

            $staff->assignRole($request->role);

            DB::commit();

            $fullName = $this->formatFullName($staff);

            $this->logStaffCreated($staff, 'manual');

            return redirect()->route('staff.index')
                ->with('success', "Staff '{$fullName}' created. Temporary password: Temp@123");
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to create staff: ' . $e->getMessage());
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
            'addresses', 'bankDetails', 'emergencyContacts', 'dependents'
        ])->findOrFail($id);
        
        // Check if current user can view this staff member
        if (!auth()->user()->canViewStaff($staff)) {
            abort(403, 'You do not have permission to view this staff member.');
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
        if (!auth()->user()->canViewStaff($staff)) {
            abort(403, 'You do not have permission to edit this staff member.');
        }
        
        $departments = Department::where('status', 'active')->get();
        $campuses = Campus::where('status', 'active')->get();
        $schools = School::where('status', 'active')->get();
        $universities = University::where('status', 'active')->get();
        $roles = ['staff', 'hod', 'hr-officer', 'admin'];
        $staffRole = $staff->roles->first()->name ?? 'staff';
        $supervisors = User::where('status', 'active')->where('id', '!=', $id)->get();
        
        return view('admin.staff.edit', compact('staff', 'departments', 'campuses', 'schools', 'universities', 'roles', 'staffRole', 'supervisors'));
    }

    /**
     * Update staff member
     */
    public function update(Request $request, $id)
    {
        $staff = User::findOrFail($id);
        
        // Check if current user can edit this staff member
        if (!auth()->user()->canViewStaff($staff)) {
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
            'email' => 'required|email|unique:staff,email,' . $id,
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
            'school_id' => 'required|exists:schools,id',
            'department_id' => 'required|exists:departments,id',
            'status' => 'required|in:active,inactive,suspended',
        ]);

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
                'university_id' => $request->university_id,
                'campus_id' => $request->campus_id,
                'school_id' => $request->school_id,
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
            return back()->with('error', 'Failed to update staff: ' . $e->getMessage());
        }
    }

    /**
     * Reset staff password
     */
    public function resetPassword($id)
    {
        $staff = User::findOrFail($id);
        
        // Check if current user can edit this staff member
        if (!auth()->user()->canViewStaff($staff)) {
            abort(403, 'You do not have permission to reset password for this staff member.');
        }
        
        $temporaryPassword = 'Temp@123';
        
        $fullName = $this->formatFullName($staff);
        
        $staff->update([
            'password' => Hash::make($temporaryPassword),
            'password_change_required' => true,
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
        
        if ($staff->avatar && \Illuminate\Support\Facades\Storage::disk('public')->exists($staff->avatar)) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($staff->avatar);
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
     * Helper: Format full name with title
     */
    private function formatFullName($staff)
    {
        $name = '';
        if ($staff->title) {
            $name .= $staff->title . ' ';
        }
        $name .= $staff->first_name;
        if ($staff->middle_name) {
            $name .= ' ' . $staff->middle_name;
        }
        $name .= ' ' . $staff->last_name;
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