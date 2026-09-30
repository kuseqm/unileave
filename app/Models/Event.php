<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class Event extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'description',
        'body',
        'event_date',
        'start_time',
        'end_time',
        'location',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'event_date' => 'date',
        'is_active' => 'boolean',
    ];

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Active events for a given calendar month, keyed by day-of-month so
     * the calendar grid can look up a day's events in O(1).
     */
    public function scopeForMonth($query, int $year, int $month)
    {
        return $query->where('is_active', true)
            ->whereYear('event_date', $year)
            ->whereMonth('event_date', $month)
            ->orderBy('event_date');
    }

    public function scopeUpcoming($query)
    {
        return $query->where('is_active', true)
            ->where('event_date', '>=', now()->toDateString())
            ->orderBy('event_date');
    }

    /**
     * A Monday-start, 6-week month grid for the landing page calendar:
     * each cell carries its date, whether it belongs to the displayed
     * month, and the (already-loaded) events on that day.
     *
     * @return array<int, array<int, array{date: Carbon, inMonth: bool, isToday: bool, events: Collection}>>
     */
    public static function calendarGrid(int $year, int $month): array
    {
        $firstOfMonth = Carbon::createFromDate($year, $month, 1)->startOfDay();
        $gridStart = $firstOfMonth->copy()->startOfWeek(Carbon::MONDAY);
        $gridEnd = $firstOfMonth->copy()->endOfMonth()->endOfWeek(Carbon::MONDAY);

        $eventsByDay = static::forMonth($year, $month)->get()->groupBy(fn ($event) => $event->event_date->day);

        $weeks = [];
        $cursor = $gridStart->copy();

        while ($cursor->lte($gridEnd)) {
            $week = [];

            for ($i = 0; $i < 7; $i++) {
                $inMonth = $cursor->month === $month;

                $week[] = [
                    'date' => $cursor->copy(),
                    'inMonth' => $inMonth,
                    'isToday' => $cursor->isToday(),
                    'events' => $inMonth ? ($eventsByDay->get($cursor->day) ?? collect()) : collect(),
                ];

                $cursor->addDay();
            }

            $weeks[] = $week;
        }

        return $weeks;
    }

    /**
     * Everything the calendar partial needs for one month: the grid plus
     * clamped/normalized prev-month and next-month targets.
     *
     * @return array{weeks: array, year: int, month: int, prev: array{month: int, year: int}, next: array{month: int, year: int}}
     */
    public static function calendarNav(int $year, int $month): array
    {
        if ($month < 1) {
            $month = 12;
            $year--;
        } elseif ($month > 12) {
            $month = 1;
            $year++;
        }

        $prev = ['month' => $month - 1, 'year' => $year];
        if ($prev['month'] < 1) {
            $prev = ['month' => 12, 'year' => $year - 1];
        }

        $next = ['month' => $month + 1, 'year' => $year];
        if ($next['month'] > 12) {
            $next = ['month' => 1, 'year' => $year + 1];
        }

        return [
            'weeks' => static::calendarGrid($year, $month),
            'year' => $year,
            'month' => $month,
            'prev' => $prev,
            'next' => $next,
        ];
    }
}
