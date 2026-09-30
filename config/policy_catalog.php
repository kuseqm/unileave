<?php

/*
|--------------------------------------------------------------------------
| Policy setting catalog
|--------------------------------------------------------------------------
|
| The settings a policy rule is allowed to control. HR picks from this list
| when writing a rule (see PolicyRuleController) instead of typing a free-text
| key, so a rule can never name a setting the system doesn't know - a typo'd
| key used to be saved silently and simply never do anything.
|
| Each setting:
|   label        What HR sees.
|   description  One or two plain sentences on what it changes.
|   category     The policy category it belongs to (Policy::CATEGORIES).
|   group        Heading it's listed under in the rule editor.
|   type         numeric | boolean | select | time | string
|   unit         Shown next to the value ("days per year"). Optional.
|   min / max    Allowed range for numeric settings. `integer` => whole numbers.
|   options      value => label, for `select`.
|   default      What applies when no rule matches. null means "no limit".
|   conditions   Which condition fields a rule for this setting may use
|                (keys of `conditions` below).
|   enforcement  How much of the system actually reads it today:
|                  enforced  - checked by the system when it matters
|                  display   - shown to staff, but not checked when applying yet
|                  planned   - can be recorded now, not enforced yet
|                  reference - documentation value; nothing reads it
|   enforced_by  Where, for `enforced`/`display`.
|
| Keep `enforcement` honest: flip a setting to `enforced` in the same change
| that makes the code read it, never before - otherwise HR sets a rule and
| nothing happens, which is exactly the trap this catalog exists to remove.
|
| The default values here are the single place they live: callers ask
| policy_value('key') and get this default when no policy rule applies.
|
*/

return [

    /*
    | What a rule can be conditioned on. `options` is value => label, or the
    | string 'leave_types' to be read live from the leave_types table.
    */
    'conditions' => [
        'staff_category' => [
            'label' => 'Staff category',
            'options' => [
                'academic' => 'Academic',
                'administrative' => 'Administrative',
                'supporting' => 'Supporting',
            ],
        ],
        'contract_type' => [
            'label' => 'Contract type',
            'options' => [
                'permanent' => 'Permanent',
                'contract' => 'Contract',
            ],
        ],
        'leave_type' => [
            'label' => 'Leave type',
            'options' => 'leave_types',
        ],
    ],

    'keys' => [

        // ---------------------------------------------------------------
        // HR / leave
        // ---------------------------------------------------------------
        'max_annual_leave' => [
            'label' => 'Annual leave entitlement',
            'description' => 'How many days of annual leave a staff member may take in a calendar year. Requests that would take them over this are refused.',
            'category' => 'hr_policy',
            'group' => 'Leave entitlements',
            'type' => 'numeric',
            'unit' => 'days per year',
            'integer' => true,
            'min' => 0,
            'max' => 365,
            'default' => 28,
            'conditions' => ['staff_category', 'contract_type'],
            'enforcement' => 'enforced',
            'enforced_by' => 'Leave application form (checked when a request is submitted), and the staff dashboard balance',
        ],
        'max_sick_leave' => [
            'label' => 'Sick leave entitlement',
            'description' => 'Days of sick leave a staff member may take in a calendar year. Requests that would take them over this are refused.',
            'category' => 'hr_policy',
            'group' => 'Leave entitlements',
            'type' => 'numeric',
            'unit' => 'days per year',
            'integer' => true,
            'min' => 0,
            'max' => 365,
            'default' => 12,
            'conditions' => ['staff_category', 'contract_type'],
            'enforcement' => 'enforced',
            'enforced_by' => 'Leave application form (checked when a request is submitted), and the staff dashboard balance',
        ],
        'max_compassionate_leave' => [
            'label' => 'Compassionate leave entitlement',
            'description' => 'Days of compassionate leave a staff member may take in a calendar year. Requests that would take them over this are refused.',
            'category' => 'hr_policy',
            'group' => 'Leave entitlements',
            'type' => 'numeric',
            'unit' => 'days per year',
            'integer' => true,
            'min' => 0,
            'max' => 365,
            'default' => 3,
            'conditions' => ['staff_category', 'contract_type'],
            'enforcement' => 'enforced',
            'enforced_by' => 'Leave application form (checked when a request is submitted), and the staff dashboard balance',
        ],
        'max_days_per_year' => [
            'label' => 'Yearly limit for a leave type',
            'description' => 'Days a staff member may take in a calendar year for one type of leave - use the leave-type condition to say which. Annual, sick and compassionate leave have their own settings above and ignore this one. With no rule, the allowance set on the leave type itself applies.',
            'category' => 'hr_policy',
            'group' => 'Leave entitlements',
            'type' => 'numeric',
            'unit' => 'days per year',
            'integer' => true,
            'min' => 0,
            'max' => 730,
            'default' => null,
            'default_label' => "Each leave type's own allowance",
            'conditions' => ['leave_type', 'staff_category', 'contract_type'],
            'enforcement' => 'enforced',
            'enforced_by' => 'Leave application form (checked when a request is submitted)',
        ],
        'max_consecutive_leave_days' => [
            'label' => 'Longest single request',
            'description' => 'The most days one leave request may cover. Use the leave-type condition to limit only some types - with no rule there is no limit, so a request is bounded only by the yearly entitlement.',
            'category' => 'hr_policy',
            'group' => 'Requests',
            'type' => 'numeric',
            'unit' => 'days per request',
            'integer' => true,
            'min' => 1,
            'max' => 730,
            'default' => null,
            'conditions' => ['leave_type', 'staff_category', 'contract_type'],
            'enforcement' => 'enforced',
            'enforced_by' => 'Leave application form (checked when a request is submitted)',
        ],
        'min_notice_days' => [
            'label' => 'Minimum notice before leave starts',
            'description' => 'How many days ahead a request must be made, so the department has time to arrange cover. Use the leave-type condition to exempt urgent types such as sick or compassionate leave (set 0 for those).',
            'category' => 'hr_policy',
            'group' => 'Requests',
            'type' => 'numeric',
            'unit' => 'days before start',
            'integer' => true,
            'min' => 0,
            'max' => 90,
            'default' => 0,
            'conditions' => ['leave_type', 'staff_category', 'contract_type'],
            'enforcement' => 'enforced',
            'enforced_by' => 'Leave application form (checked when a request is submitted)',
        ],
        'return_date_rule' => [
            'label' => 'Expected return date',
            'description' => 'Whether the system works out the return date itself (the day after leave ends) or the staff member enters it.',
            'category' => 'hr_policy',
            'group' => 'Requests',
            'type' => 'select',
            'options' => [
                'computed' => 'System sets it (day after leave ends)',
                'employee' => 'Staff member enters it',
            ],
            'default' => 'computed',
            'conditions' => ['leave_type'],
            'enforcement' => 'enforced',
            'enforced_by' => 'Leave application form (checked when a request is submitted)',
        ],
        'max_return_gap_days' => [
            'label' => 'Latest return, after leave ends',
            'description' => 'When staff enter their own return date, how many days after their leave ends it may be (to allow for a weekend or holiday). 0 means the very next day.',
            'category' => 'hr_policy',
            'group' => 'Requests',
            'type' => 'numeric',
            'unit' => 'days after leave ends',
            'integer' => true,
            'min' => 0,
            'max' => 30,
            'default' => 3,
            'conditions' => ['leave_type'],
            'enforcement' => 'enforced',
            'enforced_by' => 'Leave application form (checked when a request is submitted)',
        ],
        'allow_half_day' => [
            'label' => 'Half-day leave',
            'description' => 'Whether staff may take half a day (the morning or the afternoon of a single day) and HR may grant part of a day when reviewing a request. Where it is not allowed, leave is counted in whole days only. Use the leave-type condition to allow it for some types only.',
            'category' => 'hr_policy',
            'group' => 'Requests',
            'type' => 'boolean',
            'default' => false,
            'conditions' => ['leave_type', 'staff_category', 'contract_type'],
            'enforcement' => 'enforced',
            'enforced_by' => 'Leave application form (checked when a request is submitted) and HR review (day adjustment)',
        ],

        // ---------------------------------------------------------------
        // Staff handbook - reference values, nothing reads them yet
        // ---------------------------------------------------------------
        'dress_code' => [
            'label' => 'Dress code',
            'category' => 'handbook',
            'group' => 'Working conditions',
            'type' => 'string',
            'default' => null,
            'conditions' => [],
            'enforcement' => 'reference',
        ],
        'working_day_start' => [
            'label' => 'Working day starts',
            'category' => 'handbook',
            'group' => 'Working conditions',
            'type' => 'time',
            'default' => null,
            'conditions' => [],
            'enforcement' => 'reference',
        ],
        'working_day_end' => [
            'label' => 'Working day ends',
            'category' => 'handbook',
            'group' => 'Working conditions',
            'type' => 'time',
            'default' => null,
            'conditions' => [],
            'enforcement' => 'reference',
        ],
        'attendance_grace_minutes' => [
            'label' => 'Attendance grace period',
            'category' => 'handbook',
            'group' => 'Working conditions',
            'type' => 'numeric',
            'unit' => 'minutes',
            'integer' => true,
            'min' => 0,
            'max' => 240,
            'default' => null,
            'conditions' => [],
            'enforcement' => 'reference',
        ],
        'remote_work_allowed' => [
            'label' => 'Remote work allowed',
            'category' => 'handbook',
            'group' => 'Working conditions',
            'type' => 'boolean',
            'default' => null,
            'conditions' => [],
            'enforcement' => 'reference',
        ],

        // ---------------------------------------------------------------
        // Examination regulations - reference values, nothing reads them yet
        // ---------------------------------------------------------------
        'coursework_weight' => [
            'label' => 'Coursework weight',
            'category' => 'examination_regulation',
            'group' => 'Assessment',
            'type' => 'numeric',
            'unit' => '% of final mark',
            'integer' => true,
            'min' => 0,
            'max' => 100,
            'default' => null,
            'conditions' => [],
            'enforcement' => 'reference',
        ],
        'final_exam_weight' => [
            'label' => 'Final examination weight',
            'category' => 'examination_regulation',
            'group' => 'Assessment',
            'type' => 'numeric',
            'unit' => '% of final mark',
            'integer' => true,
            'min' => 0,
            'max' => 100,
            'default' => null,
            'conditions' => [],
            'enforcement' => 'reference',
        ],
        'pass_mark' => [
            'label' => 'Pass mark',
            'category' => 'examination_regulation',
            'group' => 'Assessment',
            'type' => 'numeric',
            'unit' => 'marks',
            'integer' => true,
            'min' => 0,
            'max' => 100,
            'default' => null,
            'conditions' => [],
            'enforcement' => 'reference',
        ],
        'supplementary_eligible' => [
            'label' => 'Supplementary examinations allowed',
            'category' => 'examination_regulation',
            'group' => 'Assessment',
            'type' => 'boolean',
            'default' => null,
            'conditions' => [],
            'enforcement' => 'reference',
        ],
        'supplementary_pass_mark' => [
            'label' => 'Supplementary pass mark',
            'category' => 'examination_regulation',
            'group' => 'Assessment',
            'type' => 'numeric',
            'unit' => 'marks',
            'integer' => true,
            'min' => 0,
            'max' => 100,
            'default' => null,
            'conditions' => [],
            'enforcement' => 'reference',
        ],
    ],
];
