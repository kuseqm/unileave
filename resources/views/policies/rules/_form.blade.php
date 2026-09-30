@php
$editMode = isset($rule);
// `?key=` lets the settings screen open this form already on the setting HR clicked "Change" for.
$selectedKey = old('key', $rule->key ?? request('key', ''));
$initial = [
    'key' => $selectedKey,
    'value' => old('value', $rule->value ?? null),
    'conditionField' => old('condition_field', $rule->condition_field ?? ''),
    'operator' => old('operator', ($rule->operator ?? null) ?: 'eq'),
    'conditionValue' => old('condition_value', $rule->condition_value ?? ''),
];
@endphp

{{-- The setting a rule controls. Chosen from the policy catalog rather than
     typed, so a rule can only ever name something the system reads. --}}
<div class="row g-2">
    <div class="col-md-7 mb-3">
        <label class="form-label" style="font-size: 0.65rem;">Setting <span class="text-danger">*</span></label>
        @if($editMode)
            <div class="form-control form-control-sm rounded-sm bg-light" style="font-size: 0.75rem;">
                {{ $catalog->label($rule->key) }} <span class="text-muted">({{ $rule->key }})</span>
            </div>
            <small class="text-muted" style="font-size: 0.6rem;">The setting can't be changed. To control a different setting, add a new rule.</small>
        @else
            <select name="key" id="ruleKey" class="form-select form-select-sm rounded-sm @error('key') is-invalid @enderror" required>
                <option value="">Choose a setting...</option>
                @foreach($catalog->grouped($policy->category) as $group => $settings)
                    <optgroup label="{{ $group }}">
                        @foreach($settings as $key => $setting)
                            <option value="{{ $key }}" {{ $selectedKey === $key ? 'selected' : '' }}>
                                {{ $setting['label'] }}{{ isset($setting['unit']) ? ' ('.$setting['unit'].')' : '' }}
                            </option>
                        @endforeach
                    </optgroup>
                @endforeach
            </select>
        @endif
        @error('key')<div class="text-danger mt-1" style="font-size:0.6rem;">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-5 mb-3">
        <div id="settingInfo" class="border rounded-sm p-2 h-100" style="font-size: 0.65rem; background: #FAFAF7;">
            <span class="text-muted">Pick a setting to see what it does and whether the system enforces it yet.</span>
        </div>
    </div>
</div>

<div class="row g-2">
    <div class="col-md-4 mb-3">
        <label class="form-label" style="font-size: 0.65rem;">Value <span class="text-danger">*</span></label>
        <div id="valueControl"></div>
        <small id="valueHint" class="text-muted" style="font-size: 0.6rem;"></small>
        @error('value')<div class="text-danger mt-1" style="font-size:0.6rem;">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-8 mb-3">
        <label class="form-label" style="font-size: 0.65rem;">Applies to</label>
        <div class="row g-1">
            <div class="col-md-5">
                <select name="condition_field" id="conditionField" class="form-select form-select-sm rounded-sm @error('condition_field') is-invalid @enderror"></select>
            </div>
            <div class="col-md-3" id="operatorWrap" style="display:none;">
                <select name="operator" id="conditionOperator" class="form-select form-select-sm rounded-sm">
                    @foreach(App\Services\PolicyCatalog::OPERATORS as $op => $opLabel)
                        <option value="{{ $op }}" {{ $initial['operator'] === $op ? 'selected' : '' }}>{{ $opLabel }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4" id="conditionValueWrap" style="display:none;">
                <div id="conditionValueControl"></div>
            </div>
        </div>
        <input type="hidden" name="condition_value" id="conditionValue" value="{{ $initial['conditionValue'] }}">
        <small class="text-muted" style="font-size: 0.6rem;">
            Leave as "Everyone" for a rule that applies to all staff. A rule limited to a group takes priority over the "Everyone" rule for the same setting.
        </small>
        @error('condition_field')<div class="text-danger" style="font-size:0.6rem;">{{ $message }}</div>@enderror
        @error('operator')<div class="text-danger" style="font-size:0.6rem;">{{ $message }}</div>@enderror
        @error('condition_value')<div class="text-danger" style="font-size:0.6rem;">{{ $message }}</div>@enderror
    </div>
</div>

<div class="row g-2 align-items-end">
    <div class="col-md-3 mb-3">
        <label class="form-label" style="font-size: 0.65rem;">Priority <span class="text-muted">(optional)</span></label>
        <input type="number" name="priority" value="{{ old('priority', $rule->priority ?? '') }}" class="form-control form-control-sm rounded-sm @error('priority') is-invalid @enderror" min="0" max="1000" placeholder="Automatic">
        <small class="text-muted" style="font-size: 0.6rem;">Higher is checked first. Blank lets the system decide.</small>
        @error('priority')<div class="invalid-feedback" style="font-size:0.6rem;">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-3 mb-3">
        <div class="form-check">
            <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $rule->is_active ?? true) ? 'checked' : '' }}>
            <label class="form-check-label" for="is_active" style="font-size: 0.65rem;">Active</label>
        </div>
    </div>
</div>

{{-- Why the rule is being changed. Mandatory once the policy is in force: it
     takes effect at once, and this is what the change history records. --}}
<div class="mb-3">
    @if($policy->isActive())
        <div class="alert alert-warning py-1 px-2 mb-2" style="font-size: 0.65rem;">
            This policy is <strong>in force</strong>. A change here applies to staff immediately and is recorded in the change history with your name and the reason below.
        </div>
    @endif
    <label class="form-label" style="font-size: 0.65rem;">
        Reason for this change
        @if($policy->isActive())<span class="text-danger">*</span>@else<span class="text-muted">(optional while the policy is a draft)</span>@endif
    </label>
    <textarea name="reason" rows="2" maxlength="1000" class="form-control form-control-sm rounded-sm @error('reason') is-invalid @enderror" placeholder="e.g. Senate resolution 12/2026 raised the academic entitlement" @if($policy->isActive()) required @endif>{{ old('reason') }}</textarea>
    @error('reason')<div class="invalid-feedback" style="font-size:0.6rem;">{{ $message }}</div>@enderror
</div>

<script>
(function () {
    const CATALOG = @json($editorCatalog);
    const INITIAL = @json($initial);
    const EDIT_MODE = @json($editMode);

    const keySelect = document.getElementById('ruleKey');
    const info = document.getElementById('settingInfo');
    const valueControl = document.getElementById('valueControl');
    const valueHint = document.getElementById('valueHint');
    const fieldSelect = document.getElementById('conditionField');
    const operatorWrap = document.getElementById('operatorWrap');
    const operatorSelect = document.getElementById('conditionOperator');
    const valueWrap = document.getElementById('conditionValueWrap');
    const conditionValueControl = document.getElementById('conditionValueControl');
    const hiddenConditionValue = document.getElementById('conditionValue');

    const BADGE = { enforced: 'bg-success', display: 'bg-info text-dark', planned: 'bg-warning text-dark', reference: 'bg-secondary' };
    let currentValue = INITIAL.value;
    let currentField = INITIAL.conditionField;

    const el = (tag, attrs = {}, text) => {
        const node = document.createElement(tag);
        Object.entries(attrs).forEach(([k, v]) => node.setAttribute(k, v));
        if (text !== undefined) node.textContent = text;
        return node;
    };

    const currentKey = () => EDIT_MODE ? INITIAL.key : (keySelect ? keySelect.value : '');
    const currentDef = () => CATALOG[currentKey()] || null;

    function renderInfo(def) {
        info.replaceChildren();
        if (!def) {
            info.appendChild(el('span', { class: 'text-muted' }, 'Pick a setting to see what it does and whether the system enforces it yet.'));
            return;
        }
        const head = el('div', { class: 'mb-1' });
        head.appendChild(el('strong', {}, def.label + ' '));
        head.appendChild(el('span', { class: 'badge ' + (BADGE[def.enforcement] || 'bg-secondary') }, def.enforcementLabel));
        info.appendChild(head);
        if (def.description) info.appendChild(el('div', {}, def.description));
        if (def.enforcedBy) info.appendChild(el('div', { class: 'text-muted mt-1' }, 'Used by: ' + def.enforcedBy));
        if (def.enforcement === 'planned') info.appendChild(el('div', { class: 'text-muted mt-1' }, 'You can record it now; it takes effect once the system is updated to check it.'));
        if (def.enforcement === 'reference') info.appendChild(el('div', { class: 'text-muted mt-1' }, 'A written value for reference - nothing in the system reads it.'));
    }

    function renderValue(def) {
        valueControl.replaceChildren();
        valueHint.textContent = '';
        if (!def) {
            valueControl.appendChild(el('input', { type: 'text', class: 'form-control form-control-sm rounded-sm', disabled: 'disabled', placeholder: 'Choose a setting first' }));
            return;
        }
        let control;
        if (def.type === 'boolean') {
            control = el('select', { name: 'value', class: 'form-select form-select-sm rounded-sm', required: 'required' });
            [['true', 'Yes'], ['false', 'No']].forEach(([v, l]) => control.appendChild(el('option', { value: v }, l)));
            const truthy = ['true', '1', 1, true].includes(currentValue);
            control.value = currentValue === null || currentValue === undefined || currentValue === '' ? 'true' : (truthy ? 'true' : 'false');
        } else if (def.type === 'select') {
            control = el('select', { name: 'value', class: 'form-select form-select-sm rounded-sm', required: 'required' });
            Object.entries(def.options).forEach(([v, l]) => control.appendChild(el('option', { value: v }, l)));
            if (currentValue) control.value = currentValue;
        } else if (def.type === 'numeric') {
            control = el('input', { type: 'number', name: 'value', class: 'form-control form-control-sm rounded-sm', required: 'required', step: def.integer ? '1' : 'any' });
            if (def.min !== null) control.setAttribute('min', def.min);
            if (def.max !== null) control.setAttribute('max', def.max);
            control.value = currentValue ?? '';
        } else if (def.type === 'time') {
            control = el('input', { type: 'time', name: 'value', class: 'form-control form-control-sm rounded-sm', required: 'required' });
            control.value = currentValue ?? '';
        } else {
            control = el('input', { type: 'text', name: 'value', class: 'form-control form-control-sm rounded-sm', required: 'required', maxlength: '255' });
            control.value = currentValue ?? '';
        }
        control.addEventListener('input', () => { currentValue = control.value; });
        control.addEventListener('change', () => { currentValue = control.value; });
        valueControl.appendChild(control);

        const hint = [];
        if (def.unit) hint.push(def.unit);
        if (def.type === 'numeric' && def.min !== null && def.max !== null) hint.push('between ' + def.min + ' and ' + def.max);
        if (def.default !== null && def.default !== undefined && def.type !== 'boolean') {
            const shown = def.type === 'select' ? (def.options[def.default] || def.default) : def.default;
            hint.push('used when no rule applies: ' + shown);
        } else if (def.default === null && def.type === 'numeric') {
            hint.push('no limit when no rule applies');
        }
        valueHint.textContent = hint.join(' - ');
    }

    function renderConditionFields(def) {
        fieldSelect.replaceChildren();
        fieldSelect.appendChild(el('option', { value: '' }, 'Everyone'));
        const fields = def ? def.conditions : {};
        Object.entries(fields).forEach(([field, meta]) => fieldSelect.appendChild(el('option', { value: field }, meta.label + '...')));
        if (currentField && fields[currentField]) fieldSelect.value = currentField; else currentField = '';
        renderConditionValue(def);
    }

    function renderConditionValue(def) {
        conditionValueControl.replaceChildren();
        const field = fieldSelect.value;
        const meta = def && field ? def.conditions[field] : null;
        operatorWrap.style.display = meta ? '' : 'none';
        valueWrap.style.display = meta ? '' : 'none';
        if (!meta) { hiddenConditionValue.value = ''; return; }

        const selected = (hiddenConditionValue.value || '').split(',').map(s => s.trim()).filter(Boolean);
        const multiple = operatorSelect.value === 'in';

        if (!multiple) {
            const select = el('select', { class: 'form-select form-select-sm rounded-sm' });
            Object.entries(meta.options).forEach(([v, l]) => select.appendChild(el('option', { value: v }, l)));
            select.value = selected[0] && meta.options[selected[0]] ? selected[0] : Object.keys(meta.options)[0];
            hiddenConditionValue.value = select.value;
            select.addEventListener('change', () => { hiddenConditionValue.value = select.value; });
            conditionValueControl.appendChild(select);
        } else {
            const box = el('div', { class: 'border rounded-sm p-1', style: 'max-height: 9rem; overflow:auto; font-size: 0.7rem;' });
            const sync = () => { hiddenConditionValue.value = [...box.querySelectorAll('input:checked')].map(i => i.value).join(','); };
            Object.entries(meta.options).forEach(([v, l]) => {
                const label = el('label', { class: 'd-block' });
                const cb = el('input', { type: 'checkbox', value: v, class: 'form-check-input me-1' });
                cb.checked = selected.includes(v);
                cb.addEventListener('change', sync);
                label.appendChild(cb);
                label.appendChild(document.createTextNode(l));
                box.appendChild(label);
            });
            conditionValueControl.appendChild(box);
            sync();
        }
    }

    function refresh() {
        const def = currentDef();
        renderInfo(def);
        renderValue(def);
        renderConditionFields(def);
    }

    if (keySelect) keySelect.addEventListener('change', () => { currentValue = null; currentField = ''; hiddenConditionValue.value = ''; refresh(); });
    fieldSelect.addEventListener('change', () => { currentField = fieldSelect.value; hiddenConditionValue.value = ''; renderConditionValue(currentDef()); });
    operatorSelect.addEventListener('change', () => renderConditionValue(currentDef()));

    refresh();
})();
</script>
