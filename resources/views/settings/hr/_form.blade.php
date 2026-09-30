    <!-- Stats -->
    <div class="row g-2 mb-3">
        <x-stats-card value="{{ setting('max_file_size', '5', 'hr-officer') }}" label="Max File Size" subtext="MB" color="primary" icon="file-text" />
        <x-stats-card value="{{ setting('hr_approval_timeout', '48', 'hr-officer') }}" label="Approval Timeout" subtext="hours" color="warning" icon="clock" />
        <x-stats-card value="{{ setting('require_medical_certificate', false, 'hr-officer') ? 'Yes' : 'No' }}" label="Medical Cert" subtext="required" color="info" icon="stethoscope" />
        <x-stats-card value="{{ setting('auto_approve_logistics', false, 'hr-officer') ? 'On' : 'Off' }}" label="Auto Approve" subtext="logistics" color="success" icon="toggle-left" />
    </div>

    <x-search-card
        id="hrSettingsSearch"
        placeholder="Search HR settings..."
    >
        <div class="text-muted" style="font-size: 0.65rem;">
            {{ count($settings) }} setting groups
        </div>
    </x-search-card>

    <div class="row g-2">
        <!-- Settings form - main column -->
        <div class="col-lg-8">
            <form method="POST" action="{{ route('settings.hr.update') }}">
                @csrf
                @method('PUT')

                <!-- Document Requirements -->
                <div class="card rounded-sm mb-3 hr-setting-card" data-search="document requirements medical certificate file size allowed file types">
                    <div class="card-header py-1 px-2 bg-light">
                        <i class="ti ti-file-text me-1"></i>
                        <span style="font-weight: 600;">Document Requirements</span>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Require Medical Certificate for Sick Leave > 3 days</label>
                                <div class="form-check form-switch">
                                    <input type="hidden" name="settings[require_medical_certificate]" value="false">
                                    <input type="checkbox" class="form-check-input" name="settings[require_medical_certificate]" value="true" id="medical_cert">
                                    <label class="form-check-label" for="medical_cert">Yes, require document</label>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Maximum File Size (MB)</label>
                                <input type="number" class="form-control form-control-sm" name="settings[max_file_size]" value="{{ setting('max_file_size', '5', 'hr-officer') }}">
                            </div>
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Allowed File Types</label>
                                <input type="text" class="form-control form-control-sm" name="settings[allowed_file_types]" value="{{ setting('allowed_file_types', 'pdf,jpg,jpeg,png', 'hr-officer') }}">
                                <small class="text-muted">Comma-separated list: pdf,jpg,jpeg,png,doc,docx</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- HR Approval Settings -->
                <div class="card rounded-sm mb-3 hr-setting-card" data-search="approval settings auto approve hr approval timeout">
                    <div class="card-header py-1 px-2 bg-light">
                        <i class="ti ti-checklist me-1"></i>
                        <span style="font-weight: 600;">Approval Settings</span>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Auto-Approve HR Logistics</label>
                                <div class="form-check form-switch">
                                    <input type="hidden" name="settings[auto_approve_logistics]" value="false">
                                    <input type="checkbox" class="form-check-input" name="settings[auto_approve_logistics]" value="true" id="auto_approve">
                                    <label class="form-check-label" for="auto_approve">Auto-approve after DVC approval</label>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">HR Approval Timeout (Hours)</label>
                                <input type="number" class="form-control form-control-sm" name="settings[hr_approval_timeout]" value="{{ setting('hr_approval_timeout', '48', 'hr-officer') }}">
                            </div>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary btn-sm rounded-sm">Save HR Settings</button>
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
                        <div class="fw-semibold" style="font-size: 0.7rem;"><i class="ti ti-flag me-1 text-primary"></i> About These Settings</div>
                        <ul class="mb-0 ps-3" style="font-size: 0.65rem; color: #555;">
                            <li>These control document upload rules and HR approval behavior institution-wide</li>
                            <li>Auto-Approve only fires after DVC has already approved the request</li>
                            <li>The Approval Timeout flags requests waiting past the set number of hours</li>
                        </ul>
                    </div>

                    <div class="mb-2">
                        <div class="fw-semibold" style="font-size: 0.7rem;"><i class="ti ti-link me-1 text-primary"></i> Quick Links</div>
                        <ul class="mb-0 ps-3" style="font-size: 0.65rem; color: #555;">
                            @can('view_policies')
                            <li><a href="{{ route('policies.settings') }}">Policy Settings</a> - the leave rules in force at your university, and where each comes from</li>
                            @endcan
                        </ul>
                    </div>

                    <x-alert-light class="mb-0 py-1 px-2 rounded-sm">
                        <div class="d-flex align-items-start gap-1">
                            <i class="ti ti-tips text-warning mt-0" style="font-size: 0.7rem;"></i>
                            <div>
                                <strong class="d-block" style="font-size: 0.65rem;">Tips</strong>
                                <small class="text-muted" style="font-size: 0.6rem;">&bull; Use the search box above to jump to a setting group by keyword</small><br>
                                <small class="text-muted" style="font-size: 0.6rem;">&bull; Changes apply immediately after saving</small>
                            </div>
                        </div>
                    </x-alert-light>
                </div>
            </div>
        </div>
    </div>

<script>
(function () {
    const searchInput = document.getElementById('hrSettingsSearch');
    const cards = document.querySelectorAll('.hr-setting-card');

    function filterCards() {
        const term = searchInput ? searchInput.value.toLowerCase() : '';
        cards.forEach(card => {
            const text = (card.dataset.search || '').toLowerCase();
            card.style.display = !term || text.includes(term) ? '' : 'none';
        });
    }

    if (searchInput) searchInput.addEventListener('keyup', filterCards);
})();
</script>
