<?php

namespace App\Http\Controllers\Registry;

use App\Http\Controllers\Controller;
use App\Models\PrintQueue;
use App\Models\OutgoingLetter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class PrintQueueController extends Controller
{
    /**
     * Display the print queue.
     */
    public function index()
    {
        $queue = PrintQueue::with('outgoingLetter')
            ->where('status', 'pending')
            ->orderBy('created_at', 'asc')
            ->paginate(20);
        
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
        
        return view('registry.print-queue.index', compact('queue', 'printed', 'dispatched'));
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
        
        return back()->with('success', count($ids) . ' letters marked as printed.');
    }
}