    <div class="row g-2">
        <div class="col-lg-8">
            <div class="card rounded-sm">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-settings text-primary me-1" style="font-size: 0.8rem;"></i>
                        <span style="font-size: 0.7rem; font-weight: 600;">Registry Configuration</span>
                    </div>
                </div>
                <div class="card-body py-2 px-2">
                    <form action="{{ route('registry.settings.update') }}" method="POST" id="registrySettingsForm">
                        @csrf
                        @method('PUT')

                        <!-- University Selection -->
                        <div class="mb-2">
                            <label class="form-label" style="font-size: 0.65rem;">University <span class="text-danger">*</span></label>
                            <select name="university_id" id="university_id" class="form-select form-select-sm rounded-sm" style="font-size: 0.7rem;" required>
                                <option value="">Select University</option>
                                @foreach($universities as $uni)
                                <option value="{{ $uni->id }}" {{ $selectedUniversity ?? ($settings[$uni->id]->university_id ?? '') == $uni->id ? 'selected' : '' }}>
                                    {{ $uni->name }} ({{ $uni->code ?? 'No Code' }})
                                </option>
                                @endforeach
                            </select>
                            <small class="text-muted" style="font-size: 0.55rem;">Settings are university-specific</small>
                        </div>

                        <!-- Secure Code Settings -->
                        <div class="border-top pt-2 mt-2">
                            <div class="fw-semibold mb-1" style="font-size: 0.7rem;">
                                <i class="ti ti-shield-lock text-info me-1"></i> Secure Code Settings
                            </div>

                            <div class="row g-1">
                                <div class="col-md-6 mb-1">
                                    <label class="form-label" style="font-size: 0.65rem;">Code Prefix</label>
                                    <input type="text" name="secure_code_prefix" id="secure_code_prefix" class="form-control form-control-sm rounded-sm" value="{{ old('secure_code_prefix', $settings[$selectedUniversity ?? 0]->secure_code_prefix ?? '') }}" style="font-size: 0.7rem;" placeholder="e.g., SMU-, SJUT-">
                                    <small class="text-muted" style="font-size: 0.55rem;">Optional prefix for verification codes</small>
                                </div>
                                <div class="col-md-6 mb-1">
                                    <label class="form-label" style="font-size: 0.65rem;">Code Length</label>
                                    <input type="number" name="secure_code_length" id="secure_code_length" class="form-control form-control-sm rounded-sm" value="{{ old('secure_code_length', $settings[$selectedUniversity ?? 0]->secure_code_length ?? 12) }}" style="font-size: 0.7rem;" min="8" max="20" required>
                                    <small class="text-muted" style="font-size: 0.55rem;">Between 8 and 20 characters</small>
                                </div>
                            </div>

                            <div class="mb-1">
                                <x-alert-secondary>
    <strong>Preview:</strong> <span id="code_preview" class="font-monospace">---</span>
                                    <button type="button" id="refreshPreview" class="btn btn-sm btn-outline-secondary ms-2" style="font-size: 0.55rem; padding: 0.1rem 0.3rem;">
                                        <i class="ti ti-refresh"></i> Refresh
                                    </button>
</x-alert-secondary>
                            </div>

                            <div class="form-check mb-1">
                                <input type="hidden" name="auto_generate_secure_code" value="0">
                                <input type="checkbox" class="form-check-input" name="auto_generate_secure_code" value="1" id="autoGenerate" {{ old('auto_generate_secure_code', $settings[$selectedUniversity ?? 0]->auto_generate_secure_code ?? true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="autoGenerate" style="font-size: 0.65rem;">Auto-generate secure code for new letters</label>
                            </div>
                        </div>

                        <!-- Letterhead & Signatory Settings -->
                        <div class="border-top pt-2 mt-2">
                            <div class="fw-semibold mb-1" style="font-size: 0.7rem;">
                                <i class="ti ti-file-text text-primary me-1"></i> Letterhead & Signatory
                            </div>

                            <div class="row g-1">
                                <div class="col-md-6 mb-1">
                                    <label class="form-label" style="font-size: 0.65rem;">Default Letterhead</label>
                                    <select name="default_letterhead_id" class="form-select form-select-sm rounded-sm" style="font-size: 0.7rem;" disabled>
                                        <option value="">System Default</option>
                                    </select>
                                    <small class="text-muted" style="font-size: 0.55rem;">Letterhead branding is managed on the Letter Branding tab.</small>
                                </div>
                                <div class="col-md-6 mb-1">
                                    <label class="form-label" style="font-size: 0.65rem;">Default Signatory</label>
                                    <input type="text" name="default_signatory" class="form-control form-control-sm rounded-sm" value="{{ old('default_signatory', $settings[$selectedUniversity ?? 0]->default_signatory ?? '') }}" style="font-size: 0.7rem;" placeholder="e.g., Prof. Sarah Johnson">
                                </div>
                            </div>

                            <div class="mb-1">
                                <label class="form-label" style="font-size: 0.65rem;">Default Signatory Title</label>
                                <input type="text" name="default_signatory_title" class="form-control form-control-sm rounded-sm" value="{{ old('default_signatory_title', $settings[$selectedUniversity ?? 0]->default_signatory_title ?? '') }}" style="font-size: 0.7rem;" placeholder="e.g., Vice Chancellor">
                            </div>
                        </div>

                        <!-- Verification & Audit Settings -->
                        <div class="border-top pt-2 mt-2">
                            <div class="fw-semibold mb-1" style="font-size: 0.7rem;">
                                <i class="ti ti-checklist text-success me-1"></i> Verification & Audit
                            </div>

                            <div class="row g-1">
                                <div class="col-md-6 mb-1">
                                    <label class="form-label" style="font-size: 0.65rem;">Verification URL</label>
                                    <input type="url" name="verification_url" class="form-control form-control-sm rounded-sm" value="{{ old('verification_url', $settings[$selectedUniversity ?? 0]->verification_url ?? '') }}" style="font-size: 0.7rem;" placeholder="https://yourdomain.com/verify">
                                    <small class="text-muted" style="font-size: 0.55rem;">Public URL for letter verification</small>
                                </div>
                                <div class="col-md-6 mb-1">
                                    <label class="form-label" style="font-size: 0.65rem;">Retention Period (Days)</label>
                                    <input type="number" name="retention_days" class="form-control form-control-sm rounded-sm" value="{{ old('retention_days', $settings[$selectedUniversity ?? 0]->retention_days ?? 730) }}" style="font-size: 0.7rem;" min="0">
                                    <small class="text-muted" style="font-size: 0.55rem;">0 = keep forever</small>
                                </div>
                            </div>

                            <div class="row g-2 mb-1">
                                <div class="col-md-6">
                                    <div class="form-check form-switch">
                                        <input type="hidden" name="enable_verification" value="0">
                                        <input type="checkbox" class="form-check-input" name="enable_verification" value="1" id="enableVerification" {{ old('enable_verification', $settings[$selectedUniversity ?? 0]->enable_verification ?? true) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="enableVerification" style="font-size: 0.65rem;">Enable Public Verification</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check form-switch">
                                        <input type="hidden" name="enable_audit_trail" value="0">
                                        <input type="checkbox" class="form-check-input" name="enable_audit_trail" value="1" id="enableAudit" {{ old('enable_audit_trail', $settings[$selectedUniversity ?? 0]->enable_audit_trail ?? true) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="enableAudit" style="font-size: 0.65rem;">Enable Audit Trail</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- System Settings -->
                        <div class="border-top pt-2 mt-2">
                            <div class="fw-semibold mb-1" style="font-size: 0.7rem;">
                                <i class="ti ti-server text-warning me-1"></i> System Settings
                            </div>

                            <div class="row g-1">
                                <div class="col-md-6 mb-1">
                                    <label class="form-label" style="font-size: 0.65rem;">Date Format</label>
                                    <select name="date_format" class="form-select form-select-sm rounded-sm" style="font-size: 0.7rem;">
                                        <option value="d M Y" {{ old('date_format', $settings[$selectedUniversity ?? 0]->date_format ?? 'd M Y') == 'd M Y' ? 'selected' : '' }}>15 Jan 2026</option>
                                        <option value="Y-m-d" {{ old('date_format', $settings[$selectedUniversity ?? 0]->date_format ?? '') == 'Y-m-d' ? 'selected' : '' }}>2026-01-15</option>
                                        <option value="d/m/Y" {{ old('date_format', $settings[$selectedUniversity ?? 0]->date_format ?? '') == 'd/m/Y' ? 'selected' : '' }}>15/01/2026</option>
                                        <option value="m/d/Y" {{ old('date_format', $settings[$selectedUniversity ?? 0]->date_format ?? '') == 'm/d/Y' ? 'selected' : '' }}>01/15/2026</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-1">
                                    <label class="form-label" style="font-size: 0.65rem;">Timezone</label>
                                    <select name="timezone" class="form-select form-select-sm rounded-sm" style="font-size: 0.7rem;">
                                        <option value="Africa/Dar_es_Salaam" {{ old('timezone', $settings[$selectedUniversity ?? 0]->timezone ?? 'Africa/Dar_es_Salaam') == 'Africa/Dar_es_Salaam' ? 'selected' : '' }}>Dar es Salaam (EAT)</option>
                                        <option value="Africa/Nairobi" {{ old('timezone', $settings[$selectedUniversity ?? 0]->timezone ?? '') == 'Africa/Nairobi' ? 'selected' : '' }}>Nairobi (EAT)</option>
                                        <option value="UTC" {{ old('timezone', $settings[$selectedUniversity ?? 0]->timezone ?? '') == 'UTC' ? 'selected' : '' }}>UTC</option>
                                    </select>
                                </div>
                            </div>

                            <div class="row g-1">
                                <div class="col-md-6 mb-1">
                                    <label class="form-label" style="font-size: 0.65rem;">Notification Email</label>
                                    <input type="email" name="notification_email" class="form-control form-control-sm rounded-sm" value="{{ old('notification_email', $settings[$selectedUniversity ?? 0]->notification_email ?? '') }}" style="font-size: 0.7rem;" placeholder="admin@university.ac.tz">
                                </div>
                                <div class="col-md-6 mb-1">
                                    <label class="form-label" style="font-size: 0.65rem;">Max Letters Per File</label>
                                    <input type="number" name="max_letters_per_file" class="form-control form-control-sm rounded-sm" value="{{ old('max_letters_per_file', $settings[$selectedUniversity ?? 0]->max_letters_per_file ?? 100) }}" style="font-size: 0.7rem;" min="1">
                                </div>
                            </div>

                            <div class="mb-1">
                                <label class="form-label" style="font-size: 0.65rem;">Backup Frequency</label>
                                <select name="backup_frequency" class="form-select form-select-sm rounded-sm" style="font-size: 0.7rem;">
                                    <option value="daily" {{ old('backup_frequency', $settings[$selectedUniversity ?? 0]->backup_frequency ?? '') == 'daily' ? 'selected' : '' }}>Daily</option>
                                    <option value="weekly" {{ old('backup_frequency', $settings[$selectedUniversity ?? 0]->backup_frequency ?? '') == 'weekly' ? 'selected' : '' }}>Weekly</option>
                                    <option value="monthly" {{ old('backup_frequency', $settings[$selectedUniversity ?? 0]->backup_frequency ?? '') == 'monthly' ? 'selected' : '' }}>Monthly</option>
                                    <option value="never" {{ old('backup_frequency', $settings[$selectedUniversity ?? 0]->backup_frequency ?? 'never') == 'never' ? 'selected' : '' }}>Never</option>
                                </select>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-1 mt-2">
                            <button type="submit" class="btn btn-primary btn-sm rounded-sm" style="font-size: 0.7rem;">
                                <i class="ti ti-device-floppy me-1"></i> Save Settings
                            </button>
                            <button type="button" id="resetBtn" class="btn btn-danger btn-sm rounded-sm" style="font-size: 0.7rem;" data-bs-toggle="modal" data-bs-target="#resetModal">
                                <i class="ti ti-refresh me-1"></i> Reset to Defaults
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Info Panel -->
        <div class="col-lg-4">
            <div class="card rounded-sm">
                <div class="card-header py-1 px-2 bg-light">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-info-circle text-info me-1" style="font-size: 0.8rem;"></i>
                        <span style="font-size: 0.7rem; font-weight: 600;">Settings Guide</span>
                    </div>
                </div>
                <div class="card-body py-2 px-2">
                    <x-alert-light class="mb-2 py-1 px-2 rounded-sm">

                        <div class="d-flex align-items-start gap-1">
                            <i class="ti ti-shield-lock text-primary mt-0" style="font-size: 0.7rem;"></i>
                            <div>
                                <strong class="d-block" style="font-size: 0.65rem;">Secure Code</strong>
                                <small class="text-muted" style="font-size: 0.6rem;">Unique verification codes for each letter. Format: PREFIX + RANDOM</small>
                            </div>

</x-alert-light>
                    </div>

                    <x-alert-light class="mb-2 py-1 px-2 rounded-sm">

                        <div class="d-flex align-items-start gap-1">
                            <i class="ti ti-file-text text-success mt-0" style="font-size: 0.7rem;"></i>
                            <div>
                                <strong class="d-block" style="font-size: 0.65rem;">Letterhead & Signatory</strong>
                                <small class="text-muted" style="font-size: 0.6rem;">Default letterhead template and signatory for all letters</small>
                            </div>

</x-alert-light>
                    </div>

                    <x-alert-light class="mb-2 py-1 px-2 rounded-sm">

                        <div class="d-flex align-items-start gap-1">
                            <i class="ti ti-checklist text-warning mt-0" style="font-size: 0.7rem;"></i>
                            <div>
                                <strong class="d-block" style="font-size: 0.65rem;">Verification URL</strong>
                                <small class="text-muted" style="font-size: 0.6rem;">Public URL where recipients can verify letter authenticity</small>
                            </div>

</x-alert-light>
                    </div>

                    <x-alert-light class="mb-0 py-1 px-2 rounded-sm">

                        <div class="d-flex align-items-start gap-1">
                            <i class="ti ti-database text-danger mt-0" style="font-size: 0.7rem;"></i>
                            <div>
                                <strong class="d-block" style="font-size: 0.65rem;">Retention Policy</strong>
                                <small class="text-muted" style="font-size: 0.6rem;">How long to keep letters before archiving (0 = forever)</small>
                            </div>

</x-alert-light>
                    </div>
                </div>
            </div>
        </div>
    </div>

<!-- Reset Confirmation Modal -->
<div class="modal fade" id="resetModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-sm">
            <div class="modal-header py-1 px-2 bg-danger-subtle">
                <h5 class="modal-title" style="font-size: 0.8rem;">
                    <i class="ti ti-alert-triangle text-danger me-1"></i> Reset Settings
                </h5>
                <button type="button" class="btn-close py-0" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body py-2 px-2">
                <p style="font-size: 0.75rem;">Are you sure you want to reset registry settings to system defaults?</p>
                <p class="text-danger mb-0" style="font-size: 0.7rem;">This action cannot be undone.</p>
            </div>
            <div class="modal-footer py-1 px-2">
                <button type="button" class="btn btn-secondary btn-sm rounded-sm" data-bs-dismiss="modal" style="font-size: 0.7rem;">Cancel</button>
                <form action="{{ route('registry.settings.reset') }}" method="POST" class="d-inline">
                    @csrf
                    <input type="hidden" name="university_id" id="reset_university_id">
                    <button type="submit" class="btn btn-danger btn-sm rounded-sm" style="font-size: 0.7rem;">
                        <i class="ti ti-refresh me-1"></i> Reset
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
(function () {
    // Secure code preview
    const prefixInput = document.getElementById('secure_code_prefix');
    const lengthInput = document.getElementById('secure_code_length');
    const previewSpan = document.getElementById('code_preview');
    const refreshBtn = document.getElementById('refreshPreview');

    function generatePreview() {
        const prefix = prefixInput?.value || '';
        const length = parseInt(lengthInput?.value) || 12;
        const random = Math.random().toString(36).substring(2, 10).toUpperCase();
        let code = prefix + random;
        if (code.length > length) {
            code = code.substring(0, length);
        }
        if (previewSpan) previewSpan.textContent = code || '---';
    }

    if (prefixInput) prefixInput.addEventListener('keyup', generatePreview);
    if (lengthInput) lengthInput.addEventListener('change', generatePreview);
    if (refreshBtn) refreshBtn.addEventListener('click', generatePreview);
    generatePreview();

    // Update reset modal with selected university
    const universitySelect = document.getElementById('university_id');
    const resetUniversityInput = document.getElementById('reset_university_id');

    if (universitySelect && resetUniversityInput) {
        universitySelect.addEventListener('change', function() {
            resetUniversityInput.value = this.value;
        });

        if (universitySelect.value) {
            resetUniversityInput.value = universitySelect.value;
        }
    }
})();
</script>
