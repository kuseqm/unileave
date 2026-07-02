<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\LeaveRequest;
use Illuminate\Console\Command;

class CheckExceededLeave extends Command
{
    protected $signature = 'leave:check-exceeded';
    protected $description = 'Check for staff who have exceeded their approved leave period';

    public function handle()
    {
        // Get staff who have approved leave that ended in the past
        $exceededStaff = User::whereHas('leaveRequests', function($q) {
            $q->where('status', 'approved')
              ->where('end_date', '<', now());
        })->get();

        foreach ($exceededStaff as $staff) {
            $lastLeave = $staff->leaveRequests()
                ->where('status', 'approved')
                ->where('end_date', '<', now())
                ->latest()
                ->first();

            if ($lastLeave) {
                $daysExceeded = now()->diffInDays($lastLeave->end_date);
                
                // Notify staff
                \App\Models\Notification::create([
                    'user_id' => $staff->id,
                    'type' => 'leave_exceeded',
                    'title' => 'Leave Period Exceeded',
                    'message' => "You have exceeded your approved leave by {$daysExceeded} days. Please report back to work.",
                    'action_url' => route('leave-requests.show', $lastLeave->id),
                    'action_text' => 'View Request',
                    'is_read' => false,
                ]);

                // Notify HR Officer
                $hrOfficer = User::role('hr-officer')->first();
                if ($hrOfficer) {
                    \App\Models\Notification::create([
                        'user_id' => $hrOfficer->id,
                        'type' => 'staff_leave_exceeded',
                        'title' => 'Staff Leave Exceeded',
                        'message' => "{$staff->first_name} {$staff->last_name} has exceeded their leave by {$daysExceeded} days.",
                        'action_url' => route('admin.staff.edit', $staff->id),
                        'action_text' => 'View Staff',
                        'is_read' => false,
                    ]);
                }

                $this->info("Notified {$staff->first_name} about {$daysExceeded} days exceeded");
            }
        }

        $this->info('Leave exceedance check completed');
        return Command::SUCCESS;
    }
}