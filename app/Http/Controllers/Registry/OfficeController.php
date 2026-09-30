<?php

namespace App\Http\Controllers\Registry;

use App\Http\Controllers\Controller;
use App\Models\Office;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OfficeController extends Controller
{
    /**
     * Display a listing of offices.
     */
    public function index(Request $request)
    {
        $query = Office::with(['department', 'campus', 'headStaff'])
            ->orderBy('sort_order');

        $grandTotal = (clone $query)->count();

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('short_name', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('is_active', $request->status === 'active');
        }

        $offices = $query->paginate($request->get('per_page', 20))->appends($request->query());

        if ($request->wantsJson()) {
            return response()->json([
                'html' => view('registry.offices._results', compact('offices'))->render(),
                'total' => $offices->total(),
                'grand_total' => $grandTotal,
            ]);
        }

        return view('registry.offices.index', compact('offices'));
    }

    /**
     * Show the form for creating a new office.
     */
    public function create()
    {
        $staff = User::where('status', 'active')->orderBy('first_name')->get();

        return view('registry.offices.create', compact('staff'));
    }

    /**
     * Store a newly created office.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'short_name' => 'nullable|string|max:30',
            'department_id' => 'nullable|exists:departments,id',
            'campus_id' => 'nullable|exists:campuses,id',
            'school_id' => 'nullable|exists:schools,id',
            'head_staff_id' => 'nullable|exists:staff,id',
            'email_alias' => 'nullable|email|max:100',
            'receives_letters' => 'boolean',
            'sort_order' => 'integer',
        ]);

        Office::create([
            'name' => $request->name,
            'short_name' => $request->short_name,
            'department_id' => $request->department_id,
            'campus_id' => $request->campus_id,
            'school_id' => $request->school_id,
            'head_staff_id' => $request->head_staff_id,
            'email_alias' => $request->email_alias,
            'is_active' => true,
            'receives_letters' => $request->has('receives_letters'),
            'sort_order' => $request->sort_order ?? 0,
            'created_by' => Auth::id(),
        ]);

        return redirect()->route('registry.offices.index')
            ->with('success', 'Office created successfully.');
    }

    /**
     * Show the form for editing the specified office.
     */
    public function edit($id)
    {
        $office = Office::findOrFail($id);
        $staff = User::where('status', 'active')->orderBy('first_name')->get();

        return view('registry.offices.edit', compact('office', 'staff'));
    }

    /**
     * Update the specified office.
     */
    public function update(Request $request, $id)
    {
        $office = Office::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:100',
            'short_name' => 'nullable|string|max:30',
            'department_id' => 'nullable|exists:departments,id',
            'campus_id' => 'nullable|exists:campuses,id',
            'school_id' => 'nullable|exists:schools,id',
            'head_staff_id' => 'nullable|exists:staff,id',
            'email_alias' => 'nullable|email|max:100',
            'receives_letters' => 'boolean',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ]);

        $office->update([
            'name' => $request->name,
            'short_name' => $request->short_name,
            'department_id' => $request->department_id,
            'campus_id' => $request->campus_id,
            'school_id' => $request->school_id,
            'head_staff_id' => $request->head_staff_id,
            'email_alias' => $request->email_alias,
            'is_active' => $request->has('is_active'),
            'receives_letters' => $request->has('receives_letters'),
            'sort_order' => $request->sort_order ?? 0,
        ]);

        return redirect()->route('registry.offices.index')
            ->with('success', 'Office updated successfully.');
    }

    /**
     * Remove the specified office.
     */
    public function destroy($id)
    {
        $office = Office::findOrFail($id);

        // Check if office has incoming letters
        if ($office->incomingLetters()->count() > 0) {
            return back()->with('error', 'Cannot delete office with existing incoming letters.');
        }

        $office->delete();

        return redirect()->route('registry.offices.index')
            ->with('success', 'Office deleted successfully.');
    }
}
