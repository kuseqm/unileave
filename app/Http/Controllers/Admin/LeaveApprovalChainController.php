<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LeaveApprovalStage;
use Illuminate\Support\Facades\DB;

/**
 * Lets an admin reorder the four review stages (HOD, Dean, HR, DVC) in the
 * leave-approval chain without touching code. HR Logistics is deliberately
 * excluded - it's the terminal finalization stage (signature capture,
 * letter generation, balance deduction) with its own dedicated page, and a
 * lot of the controller's self-collision skip logic assumes it stays last.
 * Adding an entirely new stage type isn't supported here either - each of
 * these four keeps its own already-working "who exactly approves" logic
 * (the applicant's own HOD, their school's Dean, DVCA vs DVC-PFA); only the
 * sequence is configurable.
 *
 * Reordering works as plain up/down swaps of adjacent sequence_order values
 * - a full server round-trip per click, no JavaScript required.
 */
class LeaveApprovalChainController extends Controller
{
    private const REORDERABLE_KEYS = ['hod', 'dean', 'hr', 'dvc'];

    public function index()
    {
        $stages = LeaveApprovalStage::query()->orderBy('sequence_order')->get();

        return view('admin.leave-approval-chain.index', compact('stages'));
    }

    public function moveUp(string $stageKey)
    {
        return $this->swap($stageKey, -1);
    }

    public function moveDown(string $stageKey)
    {
        return $this->swap($stageKey, 1);
    }

    private function swap(string $stageKey, int $direction)
    {
        if (! in_array($stageKey, self::REORDERABLE_KEYS, true)) {
            return redirect()->route('admin.leave-approval-chain.index')
                ->with('error', 'HR Logistics always stays last and cannot be reordered.');
        }

        $stages = LeaveApprovalStage::whereIn('stage_key', self::REORDERABLE_KEYS)
            ->orderBy('sequence_order')
            ->get()
            ->values();

        $index = $stages->search(fn ($stage) => $stage->stage_key === $stageKey);
        $swapWithIndex = $index + $direction;

        if ($index === false || $swapWithIndex < 0 || $swapWithIndex >= $stages->count()) {
            // Already at the top/bottom of the reorderable list - nothing to do.
            return redirect()->route('admin.leave-approval-chain.index');
        }

        $current = $stages[$index];
        $swapWith = $stages[$swapWithIndex];

        DB::transaction(function () use ($current, $swapWith) {
            $currentOrder = $current->sequence_order;
            $current->update(['sequence_order' => $swapWith->sequence_order]);
            $swapWith->update(['sequence_order' => $currentOrder]);
        });

        LeaveApprovalStage::forgetChainCache();

        return redirect()->route('admin.leave-approval-chain.index')
            ->with('success', 'Approval chain order updated. This applies to leave requests submitted or advanced from now on - requests already mid-chain keep moving through whatever stages come next in the new order.');
    }
}
