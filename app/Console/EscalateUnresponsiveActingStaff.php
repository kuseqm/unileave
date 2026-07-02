<?php

namespace App\Console\Commands;

use App\Models\LeaveRequest;
use App\Models\User;
use App\Models\Notification;
use Illuminate\Console\Command;

class EscalateUnresponsiveActingStaff extends Command
{
    protected $signature = 'leave:escalate-unresponsive';
    protected $description = 'Escalate leave requests where acting staff has not responded within 48 hours';

    public function handle()
    {
        $unresponsiveRequests = LeaveRequest::where('acting_status', 'pending')
            ->where('created_at', '<', now()->subHours(48))
            ->get();

        foreach ($unresponsiveRequests as $request) {
            // Notify HOD
            $hod = $request->staff->department->hod ?? null;
            if ($hod) {
                Notification::create([
                    'user_id' => $hod->id,
                    'type' => 'escalated_unresponsive',
                    'title' => '⚠️ Escalated: Acting Staff Unresponsive',
                    'message' => "The acting staff ({$request->actingStaff->first_name} {$request->actingStaff->last_name}) has not responded to {$request->staff->first_name} {$request->staff->last_name}'s leave request for over 48 hours.",
                    'action_url' => route('leave-requests.pending'),
                    'action_text' => 'Review Request',
                    'is_read' => false,
                    'priority' => 'urgent',
                ]);
                $this->info("Escalated request #{$request->id} to HOD");
            }
            
            // Notify HR
            $hrOfficer = User::role('hr-officer')->first();
            if ($hrOfficer) {
                Notification::create([
                    'user_id' => $hrOfficer->id,
                    'type' => 'escalated_unresponsive',
                    'title' => '⚠️ Escalated: Acting Staff Unresponsive',
                    'message' => "Leave request #{$request->id}: Acting staff unresponsive for 48+ hours. Staff: {$request->staff->first_name} {$request->staff->last_name}",
                    'action_url' => route('admin.staff.edit', $request->staff->id),
                    'action_text' => 'View Staff',
                    'is_read' => false,
                    'priority' => 'high',
                ]);
                $this->info("Escalated request #{$request->id} to HR");
            }
        }

        $this->info("Processed " . $unresponsiveRequests->count() . " unresponsive requests.");
        return Command::SUCCESS;
    }
}