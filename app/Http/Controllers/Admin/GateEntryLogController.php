<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GateEntryLog;
use App\Models\ReportLog;
use App\Models\User;
use App\Traits\PerPageTrait;
use Illuminate\Http\Request;

class GateEntryLogController extends Controller
{
    use PerPageTrait;

    /**
     * Display a paginated list of gate entry/exit logs.
     */
    public function index(Request $request)
    {
        $query = GateEntryLog::with(['staff', 'recorder'])
            ->orderBy('recorded_at', 'desc');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('staff', function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('middle_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('staff_id', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('gate_name')) {
            $query->where('gate_name', 'like', "%{$request->gate_name}%");
        }

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        if ($request->filled('from')) {
            $query->whereDate('recorded_at', '>=', $request->from);
        }

        if ($request->filled('to')) {
            $query->whereDate('recorded_at', '<=', $request->to);
        }

        $perPage = $this->getPerPage($request, 20);
        $grandTotal = (clone $query)->count();
        $logs = $query->paginate($perPage)->appends($request->query());

        if ($request->wantsJson()) {
            return response()->json([
                'html' => view('admin.gate-entry-logs._results', compact('logs'))->render(),
                'total' => $logs->total(),
                'grand_total' => $grandTotal,
            ]);
        }

        return view('admin.gate-entry-logs.index', compact('logs'));
    }

    /**
     * Show the manual entry/exit recording form.
     */
    public function create()
    {
        return view('admin.gate-entry-logs.create');
    }

    /**
     * Store a new gate entry/exit log.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'staff_id' => 'required|string|max:255',
            'action' => 'required|in:entry,exit',
            'gate_name' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'recorded_at' => 'nullable|date',
            'verification_method' => 'nullable|in:manual,qr_code,id_card',
            'verification_reference' => 'nullable|string|max:255',
            'user_id' => 'nullable|exists:users,id',
        ]);

        $userId = $validated['user_id'] ?? null;

        if (! $userId) {
            $staff = User::where('staff_id', $validated['staff_id'])
                ->orWhere('email', $validated['staff_id'])
                ->first();

            if (! $staff) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Staff not found for the provided ID or email.');
            }

            $userId = $staff->id;
        }

        GateEntryLog::create([
            'user_id' => $userId,
            'recorded_by' => auth()->id(),
            'gate_name' => $validated['gate_name'] ?? null,
            'action' => $validated['action'],
            'verification_method' => $validated['verification_method'] ?? 'manual',
            'verification_reference' => $validated['verification_reference'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'recorded_at' => $validated['recorded_at'] ?? now(),
        ]);

        return redirect()->route('admin.gate-entry-logs.index')
            ->with('success', 'Gate log recorded successfully.');
    }

    /**
     * Display a single gate entry/exit log.
     */
    public function show(GateEntryLog $gateEntryLog)
    {
        return view('admin.gate-entry-logs.show', compact('gateEntryLog'));
    }

    /**
     * Show the QR / staff scan page.
     */
    public function scan()
    {
        return view('admin.gate-entry-logs.scan');
    }

    /**
     * Verify a QR code, staff ID or email and return staff details.
     */
    public function verify(Request $request)
    {
        $validated = $request->validate([
            'reference' => 'required|string|max:255',
        ]);

        $reference = $validated['reference'];

        // First, try to match a report verification code
        $reportLog = ReportLog::with('user')
            ->where('verification_code', $reference)
            ->first();

        if ($reportLog && $reportLog->user) {
            $staff = $reportLog->user;
            $context = 'document';
        } else {
            // Fall back to staff ID or email
            $staff = User::where('staff_id', $reference)
                ->orWhere('email', $reference)
                ->first();

            $context = 'staff';
        }

        if (! $staff) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'No staff or document found for the provided reference.');
        }

        return view('admin.gate-entry-logs.scan', compact('staff', 'reference', 'context'));
    }
}
