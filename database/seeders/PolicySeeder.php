<?php

namespace Database\Seeders;

use App\Models\Policy;
use App\Models\PolicyRule;
use App\Models\User;
use Illuminate\Database\Seeder;

class PolicySeeder extends Seeder
{
    public function run(): void
    {
        $user = User::query()->first();
        $creatorId = $user?->id ?? 1;

        $policies = [
            [
                'code' => 'HR-LEAVE-001',
                'title' => 'Human Resources Leave Policy',
                'category' => 'hr_policy',
                'scope' => 'university-wide',
                'status' => Policy::STATUS_ACTIVE,
                'description' => 'Defines leave entitlements for all university staff.',
                'content' => 'This policy governs annual, sick, compassionate and special leave for academic and administrative staff.',
                'version' => 1,
                'approved_at' => now(),
                'created_by' => $creatorId,
                'sections' => [
                    ['number' => '1.0', 'title' => 'Purpose', 'content' => 'This policy defines leave entitlements and application procedures for all staff.'],
                    ['number' => '2.0', 'title' => 'Annual Leave', 'content' => 'Academic staff are entitled to 45 days; administrative staff 28 days unless otherwise specified.'],
                    ['number' => '3.0', 'title' => 'Sick Leave', 'content' => 'All staff are entitled to up to 12 days of sick leave per calendar year.'],
                    ['number' => '4.0', 'title' => 'Compassionate Leave', 'content' => 'Up to 3 days for all staff; 5 days for academic staff in special cases.'],
                ],
            ],
            [
                'code' => 'STAFF-HANDBOOK-2025',
                'title' => 'Staff Handbook',
                'category' => 'handbook',
                'scope' => 'university-wide',
                'status' => Policy::STATUS_ACTIVE,
                'description' => 'General staff conduct, dress code and working hours.',
                'content' => 'The Staff Handbook outlines the values, expectations and day-to-day conduct required of all university employees.',
                'version' => 1,
                'approved_at' => now(),
                'created_by' => $creatorId,
                'sections' => [
                    ['number' => '1.0', 'title' => 'General Conduct', 'content' => 'Staff are expected to maintain professionalism, integrity and respect in all dealings.'],
                    ['number' => '2.0', 'title' => 'Dress Code', 'content' => 'Formal attire is required during working hours and official functions.'],
                    ['number' => '3.0', 'title' => 'Working Hours', 'content' => 'Official hours are 08:00 to 17:00 with a 30-minute grace period.'],
                    ['number' => '4.0', 'title' => 'Disciplinary Process', 'content' => 'Breach of handbook rules may lead to verbal warning, written warning or disciplinary hearing.'],
                ],
            ],
            [
                'code' => 'EXAM-REG-2025',
                'title' => 'Examination Regulations',
                'category' => 'examination_regulation',
                'scope' => 'university-wide',
                'status' => Policy::STATUS_ACTIVE,
                'description' => 'Rules governing coursework, examinations and supplementary eligibility.',
                'content' => 'These regulations set the pass marks, supplementary eligibility, and coursework weighting for all university programmes.',
                'version' => 1,
                'approved_at' => now(),
                'created_by' => $creatorId,
                'sections' => [
                    ['number' => '1.0', 'title' => 'Scope', 'content' => 'These regulations apply to all undergraduate and postgraduate examinations.'],
                    ['number' => '2.0', 'title' => 'Coursework and Final Examination Weighting', 'content' => 'Coursework contributes 40% and the final examination 60% to the final grade.'],
                    ['number' => '3.0', 'title' => 'Pass Mark', 'content' => 'The minimum pass mark for any course is 50%.'],
                    ['number' => '4.0', 'title' => 'Supplementary Examinations', 'content' => 'A student who scores between 40% and 49% may be eligible for a supplementary exam.'],
                    ['number' => '5.0', 'title' => 'Academic Misconduct', 'content' => 'Cheating, plagiarism and impersonation constitute academic misconduct and may lead to disqualification.'],
                ],
            ],
        ];

        $policyModels = [];

        foreach ($policies as $data) {
            $code = $data['code'];
            $sections = $data['sections'] ?? null;
            unset($data['sections']);

            $policy = Policy::firstOrCreate(
                ['code' => $code],
                $data
            );

            if ($sections !== null) {
                $policy->update(['sections' => $sections]);
            }

            $policyModels[$code] = $policy;
        }

        $this->seedLeaveRules($policyModels['HR-LEAVE-001'], $creatorId);
        $this->seedHandbookRules($policyModels['STAFF-HANDBOOK-2025'], $creatorId);
        $this->seedExamRules($policyModels['EXAM-REG-2025'], $creatorId);
    }

    private function seedLeaveRules(Policy $policy, int $creatorId): void
    {
        $rules = [
            ['key' => 'max_annual_leave', 'value' => '45', 'type' => 'numeric', 'priority' => 20, 'condition_field' => 'staff_category', 'condition_value' => 'academic'],
            ['key' => 'max_annual_leave', 'value' => '35', 'type' => 'numeric', 'priority' => 15, 'condition_field' => 'contract_type', 'condition_value' => 'permanent'],
            ['key' => 'max_annual_leave', 'value' => '28', 'type' => 'numeric', 'priority' => 0],
            ['key' => 'max_sick_leave', 'value' => '12', 'type' => 'numeric', 'priority' => 0],
            ['key' => 'max_compassionate_leave', 'value' => '5', 'type' => 'numeric', 'priority' => 10, 'condition_field' => 'staff_category', 'condition_value' => 'academic'],
            ['key' => 'max_compassionate_leave', 'value' => '3', 'type' => 'numeric', 'priority' => 0],
            // Apply three days ahead, except for the leave that can't be planned.
            ['key' => 'min_notice_days', 'value' => '3', 'type' => 'numeric', 'priority' => 0],
            ['key' => 'min_notice_days', 'value' => '0', 'type' => 'numeric', 'priority' => 10, 'condition_field' => 'leave_type', 'operator' => 'in', 'condition_value' => 'LC2,LC3'],
            // Half a day off is allowed for the everyday types only.
            ['key' => 'allow_half_day', 'value' => 'true', 'type' => 'boolean', 'priority' => 10, 'condition_field' => 'leave_type', 'operator' => 'in', 'condition_value' => 'LC1,LC2,LC3'],
        ];

        $this->createRules($policy, $rules, $creatorId);
    }

    private function seedHandbookRules(Policy $policy, int $creatorId): void
    {
        $rules = [
            ['key' => 'dress_code', 'value' => 'formal', 'type' => 'string', 'priority' => 0],
            ['key' => 'working_day_start', 'value' => '08:00', 'type' => 'string', 'priority' => 0],
            ['key' => 'working_day_end', 'value' => '17:00', 'type' => 'string', 'priority' => 0],
            ['key' => 'attendance_grace_minutes', 'value' => '30', 'type' => 'numeric', 'priority' => 0],
            ['key' => 'remote_work_allowed', 'value' => 'false', 'type' => 'boolean', 'priority' => 0],
        ];

        $this->createRules($policy, $rules, $creatorId);
    }

    private function seedExamRules(Policy $policy, int $creatorId): void
    {
        $rules = [
            ['key' => 'coursework_weight', 'value' => '40', 'type' => 'numeric', 'priority' => 0],
            ['key' => 'final_exam_weight', 'value' => '60', 'type' => 'numeric', 'priority' => 0],
            ['key' => 'pass_mark', 'value' => '50', 'type' => 'numeric', 'priority' => 0],
            ['key' => 'supplementary_eligible', 'value' => 'true', 'type' => 'boolean', 'priority' => 0],
            ['key' => 'supplementary_pass_mark', 'value' => '50', 'type' => 'numeric', 'priority' => 0],
        ];

        $this->createRules($policy, $rules, $creatorId);
    }

    private function createRules(Policy $policy, array $rules, int $creatorId): void
    {
        foreach ($rules as $rule) {
            $rule['policy_id'] = $policy->id;
            $rule['created_by'] = $creatorId;
            $rule['is_active'] = true;

            PolicyRule::firstOrCreate(
                [
                    'policy_id' => $policy->id,
                    'key' => $rule['key'],
                    'condition_field' => $rule['condition_field'] ?? null,
                    'condition_value' => $rule['condition_value'] ?? null,
                ],
                $rule
            );
        }
    }
}
