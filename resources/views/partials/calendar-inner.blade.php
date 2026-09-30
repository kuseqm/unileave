{{-- Calendar month view - swapped in place by JS on prev/next (see the
     script at the bottom of landing.blade.php), so this fragment never
     includes the surrounding #calendarWidget wrapper or its id. --}}
<div class="d-flex justify-content-between align-items-center mb-3">
    <a href="{{ route('calendar.partial', $calendarPrev) }}" class="carousel-arrow calendar-nav-link" style="border-color: var(--line); color: var(--navy); background: var(--paper);" aria-label="Previous month"><i class="ti ti-chevron-left"></i></a>
    <div class="fw-semibold" style="font-size: 1.05rem;">{{ \Carbon\Carbon::createFromDate($calendarYear, $calendarMonth, 1)->format('F Y') }}</div>
    <a href="{{ route('calendar.partial', $calendarNext) }}" class="carousel-arrow calendar-nav-link" style="border-color: var(--line); color: var(--navy); background: var(--paper);" aria-label="Next month"><i class="ti ti-chevron-right"></i></a>
</div>
<div class="calendar-grid">
    @foreach(['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'] as $weekday)
        <div class="calendar-weekday">{{ $weekday }}</div>
    @endforeach
    @foreach($calendarWeeks as $week)
        @foreach($week as $day)
            <div class="calendar-day {{ ! $day['inMonth'] ? 'is-muted' : '' }} {{ $day['isToday'] ? 'is-today' : '' }}">
                <span class="calendar-day-number">{{ $day['date']->day }}</span>
                @foreach($day['events'] as $event)
                    <a href="{{ route('events.show', $event->slug) }}" class="calendar-event-link" title="{{ $event->title }}">{{ \Illuminate\Support\Str::limit($event->title, 16) }}</a>
                @endforeach
            </div>
        @endforeach
    @endforeach
</div>
