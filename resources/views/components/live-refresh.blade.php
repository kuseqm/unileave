@props(['url', 'interval' => 15000])

{{-- Polls a lightweight signature endpoint and silently reloads the page
     the moment the underlying data actually changes - e.g. an acting staff
     member accepts, or a request moves to the next approval stage - so the
     viewer never has to hit reload themselves to see current state. --}}
<script>
(function() {
    let lastSignature = null;

    async function checkForUpdates() {
        try {
            const res = await fetch('{{ $url }}', { headers: { 'Accept': 'application/json' } });
            if (!res.ok) return;
            const data = await res.json();

            if (lastSignature === null) {
                lastSignature = data.signature;
                return;
            }

            if (data.signature !== lastSignature) {
                window.location.reload();
            }
        } catch (e) {
            // Network hiccup - just try again next tick.
        }
    }

    setInterval(checkForUpdates, {{ $interval }});
})();
</script>
