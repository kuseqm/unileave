@extends('layouts.master')



@section('title', 'Leave Policies')



@section('content')

<div class="container-fluid px-2">

    

    <x-breadcrumb :items="[

        ['name' => 'Settings', 'url' => '#'],

        ['name' => 'Leave Policies', 'url' => '#']

    ]" />

    

    <x-page-header title="LEAVE POLICIES" description="Configure leave policy rules">

    </x-page-header>

    

    <x-alert-success />

    <x-alert-danger />



    <!-- Policy Overview -->

    <div class="row g-2 mb-3">

        <x-stats-card value="{{ setting('max_annual_leave', '30', 'hr-officer') }}" label="Annual Leave" subtext="days/year" color="primary" icon="calendar" />

        <x-stats-card value="{{ setting('max_sick_leave', '14', 'hr-officer') }}" label="Sick Leave" subtext="days/year" color="danger" icon="medical" />

        <x-stats-card value="{{ setting('max_casual_leave', '10', 'hr-officer') }}" label="Casual Leave" subtext="days/year" color="info" icon="coffee" />

        <x-stats-card value="{{ setting('carryover_days', '5', 'hr-officer') }}" label="Carryover" subtext="max days" color="warning" icon="transfer" />

    </div>



    <x-search-card

        id="policiesSearch"

        placeholder="Search leave policies..."

    >

        <div class="text-muted" style="font-size: 0.65rem;">

            {{ $settings->count() }} policy rules

        </div>

    </x-search-card>

    

    <div class="row g-2">
        <!-- Policy form - main column -->
        <div class="col-lg-8">
    <form method="POST" action="{{ route('settings.hr.update') }}">

        @csrf

        @method('PUT')



        <!-- Leave Limits -->

        <div class="card rounded-sm mb-3 policy-card" data-search="leave limits annual sick casual">

            <div class="card-header py-1 px-2 bg-light">

                <i class="ti ti-calendar me-1"></i>

                <span style="font-weight: 600;">Leave Limits</span>

            </div>

            <div class="card-body">

                <div class="row">

                    <div class="col-md-4 mb-3">

                        <label class="form-label">Annual Leave (Days/Year)</label>

                        <input type="number" class="form-control form-control-sm" name="settings[max_annual_leave]" value="{{ setting('max_annual_leave', '30', 'hr-officer') }}">

                    </div>

                    <div class="col-md-4 mb-3">

                        <label class="form-label">Sick Leave (Days/Year)</label>

                        <input type="number" class="form-control form-control-sm" name="settings[max_sick_leave]" value="{{ setting('max_sick_leave', '14', 'hr-officer') }}">

                    </div>

                    <div class="col-md-4 mb-3">

                        <label class="form-label">Casual Leave (Days/Year)</label>

                        <input type="number" class="form-control form-control-sm" name="settings[max_casual_leave]" value="{{ setting('max_casual_leave', '10', 'hr-officer') }}">

                    </div>

                </div>

            </div>

        </div>

        

        <!-- Carryover Rules -->

        <div class="card rounded-sm mb-3 policy-card" data-search="carryover rules maximum carryover days expiry">

            <div class="card-header py-1 px-2 bg-light">

                <i class="ti ti-transfer me-1"></i>

                <span style="font-weight: 600;">Carryover Rules</span>

            </div>

            <div class="card-body">

                <div class="row">

                    <div class="col-md-6 mb-3">

                        <label class="form-label">Maximum Carryover Days</label>

                        <input type="number" class="form-control form-control-sm" name="settings[carryover_days]" value="{{ setting('carryover_days', '5', 'hr-officer') }}">

                    </div>

                    <div class="col-md-6 mb-3">

                        <label class="form-label">Carryover Expiry (Months)</label>

                        <input type="number" class="form-control form-control-sm" name="settings[carryover_expiry]" value="{{ setting('carryover_expiry', '3', 'hr-officer') }}">

                        <small class="text-muted">Months into new year before carryover expires</small>

                    </div>

                </div>

            </div>

        </div>

        

        <!-- Approval Escalation -->

        <div class="card rounded-sm mb-3 policy-card" data-search="approval escalation hod dean auto approve limit days advance">

            <div class="card-header py-1 px-2 bg-light">

                <i class="ti ti-clock me-1"></i>

                <span style="font-weight: 600;">Approval Escalation</span>

            </div>

            <div class="card-body">

                <div class="row">

                    <div class="col-md-6 mb-3">

                        <label class="form-label">HOD Auto-Approve Limit (Days)</label>

                        <input type="number" class="form-control form-control-sm" name="settings[hod_auto_approve_days]" value="{{ setting('hod_auto_approve_days', '3', 'hod') }}">

                    </div>

                    <div class="col-md-6 mb-3">

                        <label class="form-label">Dean Auto-Approve Limit (Days)</label>

                        <input type="number" class="form-control form-control-sm" name="settings[dean_auto_approve_days]" value="{{ setting('dean_auto_approve_days', '7', 'dean') }}">

                    </div>

                    <div class="col-md-6 mb-3">

                        <label class="form-label">Escalate After (Days)</label>

                        <input type="number" class="form-control form-control-sm" name="settings[escalate_after_days]" value="{{ setting('escalate_after_days', '5', 'hr-officer') }}">

                        <small class="text-muted">Days pending before escalation</small>

                    </div>

                    <div class="col-md-6 mb-3">

                        <label class="form-label">Advance Leave Limit (Days)</label>

                        <input type="number" class="form-control form-control-sm" name="settings[advance_leave_limit]" value="{{ setting('advance_leave_limit', '5', 'hr-officer') }}">

                    </div>

                </div>

            </div>

        </div>

        

        <!-- Blackout Rules -->

        <div class="card rounded-sm mb-3 policy-card" data-search="blackout rules block leave during examinations">

            <div class="card-header py-1 px-2 bg-light">

                <i class="ti ti-ban me-1"></i>

                <span style="font-weight: 600;">Blackout Rules</span>

            </div>

            <div class="card-body">

                <div class="row">

                    <div class="col-md-12 mb-3">

                        <div class="form-check form-switch">

                            <input type="hidden" name="settings[block_leave_during_exams]" value="false">

                            <input type="checkbox" class="form-check-input" name="settings[block_leave_during_exams]" value="true" id="exam_blackout">

                            <label class="form-check-label" for="exam_blackout">Block leave during examination period</label>

                        </div>

                    </div>

                </div>

            </div>

        </div>

        

        <button type="submit" class="btn btn-primary btn-sm rounded-sm">Save Policies</button>

        <a href="{{ route('dashboard') }}" class="btn btn-secondary btn-sm rounded-sm">Cancel</a>

    </form>
        </div>

        <!-- Information - narrow column -->
        <div class="col-lg-4">
            <div class="card rounded-sm">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-info-circle text-info me-1" style="font-size: 0.8rem;"></i>
                        <span style="font-size: 0.7rem; font-weight: 600;">Information</span>
                    </div>
                </div>
                <div class="card-body py-2 px-2">
                    <div class="mb-2">
                        <div class="fw-semibold" style="font-size: 0.7rem;"><i class="ti ti-flag me-1 text-primary"></i> About These Policies</div>
                        <ul class="mb-0 ps-3" style="font-size: 0.65rem; color: #555;">
                            <li>Leave limits set the maximum days staff can request per year, per leave type</li>
                            <li>Carryover Expiry controls how many months into the new year unused days remain valid</li>
                            <li>Auto-Approve limits let HOD/Dean bypass manual review for short requests</li>
                        </ul>
                    </div>

                    <div class="mb-2">
                        <div class="fw-semibold" style="font-size: 0.7rem;"><i class="ti ti-link me-1 text-primary"></i> Quick Links</div>
                        <ul class="mb-0 ps-3" style="font-size: 0.65rem; color: #555;">
                            <li><a href="{{ route('settings.hr') }}">HR Settings</a> - document and approval settings</li>
                        </ul>
                    </div>

                    <x-alert-light class="mb-0 py-1 px-2 rounded-sm">
                        <div class="d-flex align-items-start gap-1">
                            <i class="ti ti-tips text-warning mt-0" style="font-size: 0.7rem;"></i>
                            <div>
                                <strong class="d-block" style="font-size: 0.65rem;">Tips</strong>
                                <small class="text-muted" style="font-size: 0.6rem;">&bull; Blackout Rules block leave requests outright during exam periods</small><br>
                                <small class="text-muted" style="font-size: 0.6rem;">&bull; Escalation moves a stalled request up the approval chain automatically</small>
                            </div>
                        </div>
                    </x-alert-light>
                </div>
            </div>
        </div>
    </div>

</div>



<script>

const searchInput = document.getElementById('policiesSearch');

const resetFilters = document.getElementById('resetFilters');

const cards = document.querySelectorAll('.policy-card');



function filterCards() {

    const term = searchInput ? searchInput.value.toLowerCase() : '';

    cards.forEach(card => {

        const text = (card.dataset.search || '').toLowerCase();

        card.style.display = !term || text.includes(term) ? '' : 'none';

    });

}



if (searchInput) searchInput.addEventListener('keyup', filterCards);

if (resetFilters) {

    resetFilters.addEventListener('click', function () {

        if (searchInput) searchInput.value = '';

        filterCards();

    });

}

</script>

@endsection