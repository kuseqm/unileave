@php
    $clockFormat = setting('clock_format', '24', null, auth()->id());
    $showHebrewDate = (bool) setting('show_hebrew_date', false, null, auth()->id());
@endphp

<div class="live-clock" id="liveClock" data-format="{{ $clockFormat }}">
    <div class="live-clock-time" id="liveClockTime">--:--:--</div>
    <div class="live-clock-date">
        <span class="live-clock-full" id="liveClockDateFull"></span>
        <span class="live-clock-short" id="liveClockDateShort"></span>
    </div>
    @if($showHebrewDate)
        <div class="live-clock-hebrew">
            <span class="live-clock-full" id="liveClockHebrewFull"></span>
            <span class="live-clock-short" id="liveClockHebrewShort"></span>
        </div>
    @endif
</div>

<style>
    .live-clock { text-align: right; line-height: 1.25; }
    .live-clock-time { font-size: 0.85rem; font-weight: 700; color: #1B2F6B; font-variant-numeric: tabular-nums; }
    .live-clock-date { font-size: 0.65rem; color: #64748B; }
    .live-clock-hebrew { font-size: 0.6rem; color: #96771A; }

    /* Full text ("12th September 2026") on normal screens, numbers-only
       ("12/09/26") below 768px - a narrow screen can't comfortably fit
       the full sentence next to the notification icons. */
    .live-clock-short { display: none; }
    @media (max-width: 768px) {
        .live-clock-full { display: none; }
        .live-clock-short { display: inline; }
    }
</style>

<script>
    (function () {
        var el = document.getElementById('liveClock');
        if (! el) return;

        var format = el.dataset.format;
        var timeEl = document.getElementById('liveClockTime');
        var dateFullEl = document.getElementById('liveClockDateFull');
        var dateShortEl = document.getElementById('liveClockDateShort');
        var hebrewFullEl = document.getElementById('liveClockHebrewFull');
        var hebrewShortEl = document.getElementById('liveClockHebrewShort');

        // Dodoma, Tanzania (SJUT's home campus) - used to work out the real
        // sunset time each day, since the Hebrew calendar day begins at
        // sunset, not at midnight. Fixed to the campus location rather
        // than the visitor's own location, so it reads the same for
        // everyone and never has to ask for GPS permission.
        var LAT = -6.1630;
        var LON = 35.7516;
        var TZ_OFFSET_HOURS = 3; // Africa/Dar_es_Salaam (EAT), no DST

        function ordinal(day) {
            var lastDigit = day % 10;
            var lastTwoDigits = day % 100;
            if (lastDigit === 1 && lastTwoDigits !== 11) return day + 'st';
            if (lastDigit === 2 && lastTwoDigits !== 12) return day + 'nd';
            if (lastDigit === 3 && lastTwoDigits !== 13) return day + 'rd';
            return day + 'th';
        }

        function pad(n) {
            return n < 10 ? '0' + n : String(n);
        }

        function dayOfYear(date) {
            var start = new Date(date.getFullYear(), 0, 1);
            return Math.floor((date - start) / 86400000) + 1;
        }

        /**
         * Sunset time (decimal local hours, e.g. 18.5 = 18:30) for a given
         * date and location, using the standard "Sunrise/Sunset Algorithm"
         * (Almanac for Computers, 1990, US Naval Observatory) - a public
         * astronomical formula, accurate to within a minute or two.
         */
        function sunsetHour(date, latDeg, lonDeg, tzOffsetHours) {
            var rad = Math.PI / 180;
            var N = dayOfYear(date);
            var lngHour = lonDeg / 15;
            var t = N + ((18 - lngHour) / 24);

            var M = (0.9856 * t) - 3.289;
            var L = M + (1.916 * Math.sin(M * rad)) + (0.020 * Math.sin(2 * M * rad)) + 282.634;
            L = (L + 360) % 360;

            var RA = Math.atan(0.91764 * Math.tan(L * rad)) / rad;
            RA = (RA + 360) % 360;
            var Lquadrant = Math.floor(L / 90) * 90;
            var RAquadrant = Math.floor(RA / 90) * 90;
            RA = RA + (Lquadrant - RAquadrant);
            RA = RA / 15;

            var sinDec = 0.39782 * Math.sin(L * rad);
            var cosDec = Math.cos(Math.asin(sinDec));

            var cosH = (Math.cos(90.833 * rad) - (sinDec * Math.sin(latDeg * rad))) / (cosDec * Math.cos(latDeg * rad));
            if (cosH > 1 || cosH < -1) {
                // Sun never sets/rises that day at this latitude - not a
                // real case for Dodoma, but fall back to 18:00 rather than
                // producing NaN.
                return 18;
            }

            var H = Math.acos(cosH) / rad;
            H = H / 15;

            var T = H + RA - (0.06571 * t) - 6.622;
            var UT = (T - lngHour + 24) % 24;

            return (UT + tzOffsetHours + 24) % 24;
        }

        // Browsers don't actually support {month: 'numeric'} for the Hebrew
        // calendar (leap years insert "Adar I"/"Adar II", so ICU always
        // returns the name) - map the name to its number ourselves for the
        // numbers-only short form. Uses the religious/biblical count
        // (Nisan = 1, per Exodus 12:2), not the civil count some
        // calendars use (where Tishri = 1, matching the year-number
        // rollover at Rosh Hashanah) - both are legitimate conventions,
        // this is simply the one that matches what most people expect.
        var HEBREW_MONTH_NUMBERS = {
            'nisan': 1, 'iyar': 2, 'sivan': 3, 'tamuz': 4, 'tammuz': 4, 'av': 5,
            'elul': 6, 'tishri': 7, 'heshvan': 8, 'cheshvan': 8, 'kislev': 9,
            'tevet': 10, 'shevat': 11, 'adar i': 12, 'adar': 12, 'adar ii': 13,
        };

        function hebrewCalendarDateFor(date) {
            var parts = new Intl.DateTimeFormat('en-u-ca-hebrew', {day: 'numeric', month: 'long', year: 'numeric'}).formatToParts(date);
            var day, month, year;
            parts.forEach(function (p) {
                if (p.type === 'day') day = p.value;
                if (p.type === 'month') month = p.value;
                if (p.type === 'year') year = p.value;
            });

            var monthNum = HEBREW_MONTH_NUMBERS[(month || '').toLowerCase()];

            return {
                full: day + ' ' + month + ' ' + year,
                short: day + '/' + (monthNum || '?') + '/' + year,
            };
        }

        function tick() {
            var now = new Date();

            if (format === '12') {
                var period = now.getHours() >= 12 ? 'PM' : 'AM';
                var hours12 = now.getHours() % 12;
                if (hours12 === 0) hours12 = 12;
                timeEl.textContent = hours12 + ':' + pad(now.getMinutes()) + ':' + pad(now.getSeconds()) + ' ' + period;
            } else {
                timeEl.textContent = pad(now.getHours()) + ':' + pad(now.getMinutes()) + ':' + pad(now.getSeconds());
            }

            var monthName = now.toLocaleDateString('en-US', {month: 'long'});
            dateFullEl.textContent = ordinal(now.getDate()) + ' ' + monthName + ' ' + now.getFullYear();
            dateShortEl.textContent = pad(now.getDate()) + '/' + pad(now.getMonth() + 1) + '/' + String(now.getFullYear()).slice(-2);

            if (hebrewFullEl) {
                try {
                    var currentHour = now.getHours() + (now.getMinutes() / 60) + (now.getSeconds() / 3600);
                    var todaySunset = sunsetHour(now, LAT, LON, TZ_OFFSET_HOURS);

                    // The Hebrew day already began at tonight's sunset -
                    // show the calendar date for tomorrow instead of today.
                    var hebrewSourceDate = now;
                    if (currentHour >= todaySunset) {
                        hebrewSourceDate = new Date(now);
                        hebrewSourceDate.setDate(hebrewSourceDate.getDate() + 1);
                    }

                    var hebrew = hebrewCalendarDateFor(hebrewSourceDate);
                    hebrewFullEl.textContent = hebrew.full;
                    hebrewShortEl.textContent = hebrew.short;
                } catch (e) {
                    hebrewFullEl.textContent = '';
                    hebrewShortEl.textContent = '';
                }
            }
        }

        tick();
        setInterval(tick, 1000);
    })();
</script>
