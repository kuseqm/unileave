<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ApprovalDelegation;
use App\Models\User;
use App\Traits\PerPageTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ApprovalDelegationController extends Controller
{
    use PerPageTrait;

    public function index(Request $request)
    {
        $auth = auth()->user();

        $query = ApprovalDelegation::with(['delegator', 'delegate'])
            ->orderBy('created_at', 'desc');

        if (! $auth->hasRole('super-admin')) {
            $query->whereHas('delegator', function ($q) use ($auth) {
                $q->where('university_id', $auth->university_id);
            });
        }

        $delegations = $query->paginate($this->getPerPage($request, 20))->appends($request->query());

        return view('admin.approval-delegations.index', compact('delegations'));
    }

    public function create()
    {
        $auth = auth()->user();
        $query = User::role(['hod', 'dean', 'dvca', 'dvc-pfa', 'hr-officer', 'hr-manager'])
            ->where('status', 'active')
            ->orderBy('first_name');

        if (! $auth->hasRole('super-admin')) {
            $query->where('university_id', $auth->university_id);
        }

        $delegators = $query->get();

        $staffQuery = User::where('status', 'active')->orderBy('first_name');
        if (! $auth->hasRole('super-admin')) {
            $staffQuery->where('university_id', $auth->university_id);
        }
        $staff = $staffQuery->get();

        return view('admin.approval-delegations.create', compact('delegators', 'staff'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'delegator_staff_id' => 'required|exists:staff,id',
            'delegate_staff_id' => 'required|exists:staff,id|different:delegator_staff_id',
            'level' => 'required|in:hod,dean,dvc,hr,hr_logistics',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after:start_date',
            'reason' => 'nullable|string|max:500',
        ]);

        DB::beginTransaction();

        try {
            ApprovalDelegation::create([
                'delegator_staff_id' => $request->delegator_staff_id,
                'delegate_staff_id' => $request->delegate_staff_id,
                'level' => $request->level,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'reason' => $request->reason,
                'status' => 'active',
            ]);

            DB::commit();

            return redirect()->route('admin.approval-delegations.index')
                ->with('success', 'Approval delegation created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->with('error', 'Failed to create delegation: '.$e->getMessage())
                ->withInput();
        }
    }

    public function destroy($id)
    {
        $delegation = ApprovalDelegation::findOrFail($id);

        $delegation->update([
            'status' => 'revoked',
            'revoked_at' => now(),
            'revoked_by' => auth()->id(),
        ]);

        return redirect()->back()->with('success', 'Delegation revoked successfully.');
    }
}
