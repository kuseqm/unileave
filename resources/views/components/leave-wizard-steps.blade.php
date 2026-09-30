@props(['currentStep' => 1, 'steps' => null])

@php
    // Each step keeps its own identity color throughout - it doesn't just
    // borrow "blue" for whichever step is current. Reached steps show the
    // color solid; a step not yet reached stays a pale outline until it is.
    // A caller can pass its own `steps` (same shape) for a different
    // stepped flow - the Leave wizard's own 3 steps stay the default so
    // every existing caller keeps working unchanged.
    $steps ??= [
        1 => ['label' => 'Fill Details', 'icon' => 'ti-forms', 'color' => '#5B84C4', 'pulse' => '91,132,196'],
        2 => ['label' => 'Review', 'icon' => 'ti-eye-check', 'color' => '#B88F42', 'pulse' => '184,143,66'],
        3 => ['label' => 'Sign & Submit', 'icon' => 'ti-signature', 'color' => '#4C9C79', 'pulse' => '76,156,121'],
    ];
@endphp

<div class="leave-wizard-ribbon mb-3">
    <div class="leave-wizard-ribbon__track">
        @foreach($steps as $number => $step)
            <div class="leave-wizard-ribbon__segment" style="background: {{ $number <= $currentStep ? $step['color'] : '#E7E4D9' }};"></div>
        @endforeach
    </div>
    <div class="leave-wizard-ribbon__row">
        @foreach($steps as $number => $step)
            @php
                $state = $number < $currentStep ? 'completed' : ($number === $currentStep ? 'current' : 'upcoming');
            @endphp
            <div class="leave-wizard-ribbon__step leave-wizard-ribbon__step--{{ $loop->first ? 'start' : ($loop->last ? 'end' : 'middle') }}">
                <div class="leave-wizard-ribbon__dot {{ $state === 'current' ? 'leave-wizard-ribbon__dot--pulse' : '' }}"
                     style="{{ $state === 'upcoming' ? 'background:#fff;border-color:#E7E4D9;' : 'background:'.$step['color'].';border-color:#fff;' }} --pulse-color: {{ $step['pulse'] }};">
                    @if($state === 'completed')
                        <i class="ti ti-check"></i>
                    @else
                        <i class="ti {{ $step['icon'] }}" style="color: {{ $state === 'upcoming' ? '#B4AE9C' : '#fff' }};"></i>
                    @endif
                </div>
                <span class="leave-wizard-ribbon__label" style="color: {{ $state === 'upcoming' ? '#94A3B8' : '#1E293B' }}; font-weight: {{ $state === 'upcoming' ? '600' : '700' }};">
                    {{ $step['label'] }}
                </span>
            </div>
        @endforeach
    </div>
</div>

<style>
.leave-wizard-ribbon { width: 100%; }
.leave-wizard-ribbon__track { display: flex; gap: 2px; height: 5px; }
.leave-wizard-ribbon__segment { flex: 1; border-radius: 3px; transition: background 0.3s ease; }
.leave-wizard-ribbon__row { display: flex; margin-top: -14px; }
.leave-wizard-ribbon__step { display: flex; flex-direction: column; align-items: center; flex: 1; min-width: 0; }
.leave-wizard-ribbon__step--start { align-items: flex-start; }
.leave-wizard-ribbon__step--end { align-items: flex-end; }
.leave-wizard-ribbon__dot {
    width: 28px; height: 28px; border-radius: 50%; border: 3px solid;
    display: flex; align-items: center; justify-content: center; flex-shrink: 0;
}
.leave-wizard-ribbon__dot i { font-size: 0.7rem; }
.leave-wizard-ribbon__dot--pulse { animation: leaveWizardPulse 2s infinite; }
.leave-wizard-ribbon__label {
    margin-top: 5px; font-size: 0.62rem; text-align: center; max-width: 100%;
    white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
}
.leave-wizard-ribbon__step--start .leave-wizard-ribbon__label { text-align: left; }
.leave-wizard-ribbon__step--end .leave-wizard-ribbon__label { text-align: right; }
@keyframes leaveWizardPulse {
    0% { box-shadow: 0 0 0 0 rgba(var(--pulse-color), 0.4); }
    70% { box-shadow: 0 0 0 6px rgba(var(--pulse-color), 0); }
    100% { box-shadow: 0 0 0 0 rgba(var(--pulse-color), 0); }
}
@media (max-width: 420px) {
    .leave-wizard-ribbon__dot { width: 22px; height: 22px; border-width: 2px; }
    .leave-wizard-ribbon__dot i { font-size: 0.58rem; }
    .leave-wizard-ribbon__label { font-size: 0.52rem; margin-top: 3px; }
    .leave-wizard-ribbon__row { margin-top: -11px; }
}
</style>
