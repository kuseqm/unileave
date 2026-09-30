<?php

namespace App\Http\Controllers\Registry;

use App\Http\Controllers\Controller;
use App\Models\PrintQueue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PrintQueueController extends Controller
{
    /**
     * Display the print queue.
     */
    public function index(Request $request)
    {
        $pendingQuery = PrintQueue::with('outgoingLetter')
            ->where('status', 'pending')
            ->orderBy('created_at', 'asc');

        $grandTotal = (clone $pendingQuery)->count();

        if ($search = $request->get('search')) {
            $pendingQuery->where(function ($q) use ($search) {
                $q->where('recipient_address', 'like', "%{$search}%")
                    ->orWhereHas('outgoingLetter', function ($sq) use ($search) {
                        $sq->where('ref_number', 'like', "%{$search}%");
                    });
            });
        }

        $queue = $pendingQuery->paginate($request->get('per_page', 20))->appends($request->query());

        $printed = PrintQueue::with('outgoingLetter')
            ->where('status', 'printed')
            ->orderBy('printed_at', 'desc')
            ->limit(20)
            ->get();

        $dispatched = PrintQueue::with('outgoingLetter')
            ->where('status', 'dispatched')
            ->orderBy('dispatched_at', 'desc')
            ->limit(20)
            ->get();

        $overdue = PrintQueue::where('status', 'pending')
            ->where('created_at', '<', now()->subHours(48))
            ->count();

        if ($request->wantsJson()) {
            return response()->json([
                'html' => view('registry.print-queue._results', compact('queue'))->render(),
                'total' => $queue->total(),
                'grand_total' => $grandTotal,
            ]);
        }

        return view('registry.print-queue.index', compact('queue', 'printed', 'dispatched', 'overdue'));
    }

    /**
     * Mark letter as printed.
     */
    public function markAsPrinted($id)
    {
        $printItem = PrintQueue::findOrFail($id);

        $printItem->update([
            'printed_by' => Auth::id(),
            'printed_at' => now(),
            'status' => 'printed',
        ]);

        return redirect()->route('registry.print-queue.index')
            ->with('success', 'Letter marked as printed.');
    }

    /**
     * Mark letter as dispatched.
     */
    public function markAsDispatched($id)
    {
        $printItem = PrintQueue::findOrFail($id);

        $printItem->update([
            'dispatched_by' => Auth::id(),
            'dispatched_at' => now(),
            'status' => 'dispatched',
        ]);

        // Update the outgoing letter
        if ($printItem->outgoingLetter) {
            $printItem->outgoingLetter->update([
                'dispatched_at' => now(),
            ]);
        }

        return redirect()->route('registry.print-queue.index')
            ->with('success', 'Letter marked as dispatched.');
    }

    /**
     * Batch print multiple letters.
     */
    public function batchPrint(Request $request)
    {
        $ids = $request->input('ids', []);

        if (empty($ids)) {
            return back()->with('error', 'No letters selected for printing.');
        }

        $printItems = PrintQueue::whereIn('id', $ids)->get();

        foreach ($printItems as $item) {
            $item->update([
                'printed_by' => Auth::id(),
                'printed_at' => now(),
                'status' => 'printed',
            ]);
        }

        return back()->with('success', count($ids).' letters marked as printed.');
    }
}
