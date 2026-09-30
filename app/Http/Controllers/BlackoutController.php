<?php

namespace App\Http\Controllers;

use App\Models\BlackoutPeriod;
use Illuminate\Http\Request;

class BlackoutController extends Controller
{
    public function index(Request $request)
    {
        $query = BlackoutPeriod::orderBy('start_date', 'desc');

        $grandTotal = (clone $query)->count();

        if ($search = $request->get('search')) {
            $query->where('name', 'like', "%{$search}%");
        }

        if ($request->filled('restriction_type') && $request->restriction_type !== 'all') {
            $query->where('restriction_type', $request->restriction_type);
        }

        $blackoutPeriods = $query->paginate($request->get('per_page', 10))->appends($request->query());

        if ($request->wantsJson()) {
            return response()->json([
                'html' => view('shared.blackout._results', compact('blackoutPeriods'))->render(),
                'total' => $blackoutPeriods->total(),
                'grand_total' => $grandTotal,
            ]);
        }

        return view('shared.blackout.index', compact('blackoutPeriods'));
    }

    public function create()
    {
        if (! auth()->user()->hasAnyRole(['admin', 'hr-officer', 'dean'])) {
            return redirect()->route('dashboard')->with('error', 'Access denied');
        }

        return view('shared.blackout.create');
    }

    public function store(Request $request)
    {
        if (! auth()->user()->hasAnyRole(['admin', 'hr-officer', 'dean'])) {
            return redirect()->route('dashboard')->with('error', 'Access denied');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'restriction_type' => 'required|in:hard,soft',
            'override_allowed' => 'boolean',
        ]);

        BlackoutPeriod::create([
            'name' => $request->name,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'restriction_type' => $request->restriction_type,
            'override_allowed' => $request->has('override_allowed'),
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('blackout.index')->with('success', 'Blackout period created successfully.');
    }

    // ✅ ADD THIS METHOD
    public function show($id)
    {
        $blackout = BlackoutPeriod::findOrFail($id);

        return view('shared.blackout.show', compact('blackout'));
    }

    public function edit($id)
    {
        if (! auth()->user()->hasAnyRole(['admin', 'hr-officer', 'dean'])) {
            return redirect()->route('dashboard')->with('error', 'Access denied');
        }

        $blackout = BlackoutPeriod::findOrFail($id);

        return view('shared.blackout.edit', compact('blackout'));
    }

    public function update(Request $request, $id)
    {
        if (! auth()->user()->hasAnyRole(['admin', 'hr-officer', 'dean'])) {
            return redirect()->route('dashboard')->with('error', 'Access denied');
        }

        $blackout = BlackoutPeriod::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'restriction_type' => 'required|in:hard,soft',
            'override_allowed' => 'boolean',
        ]);

        $blackout->update([
            'name' => $request->name,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'restriction_type' => $request->restriction_type,
            'override_allowed' => $request->has('override_allowed'),
        ]);

        return redirect()->route('blackout.index')->with('success', 'Blackout period updated successfully.');
    }

    public function destroy($id)
    {
        if (! auth()->user()->hasAnyRole(['admin', 'hr-officer'])) {
            return redirect()->route('dashboard')->with('error', 'Access denied');
        }

        $blackout = BlackoutPeriod::findOrFail($id);
        $blackout->delete();

        return redirect()->route('blackout.index')->with('success', 'Blackout period deleted successfully.');
    }

    public function checkDate(Request $request)
    {
        $startDate = $request->start_date;
        $endDate = $request->end_date;

        $blackout = BlackoutPeriod::where('start_date', '<=', $endDate)
            ->where('end_date', '>=', $startDate)
            ->first();

        if ($blackout) {
            return response()->json([
                'available' => false,
                'message' => 'This period falls within a blackout period: '.$blackout->name,
            ]);
        }

        return response()->json(['available' => true]);
    }
}
