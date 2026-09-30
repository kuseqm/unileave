{{-- Live preview of how this announcement will look in the landing-page
     Notice Board carousel. Colors are hardcoded to match landing.blade.php's
     navy/gold SJUT palette directly, since the admin panel runs its own
     (unrelated) Tabler theme. --}}
<div class="card border-0 rounded-sm shadow-sm">
    <div class="card-body p-2">
        <div class="text-muted text-uppercase mb-2" style="font-size: 0.65rem; letter-spacing: 0.05em;">
            <i class="ti ti-eye me-1"></i> Live preview
        </div>
        <div id="announcement-preview-wrap" style="background: #1B2F6B; border-radius: 8px; padding: 1.5rem clamp(0.75rem, 4vw, 1.5rem);">
            <div id="announcement-preview-card" style="background: #FFFFFF; border-radius: 12px; padding: 1.25rem; border-top: 4px solid #E7EAF6; max-width: 300px;">
                <span id="announcement-preview-cat" style="font-size: 0.62rem; font-weight: 700; letter-spacing: 0.05em; text-transform: uppercase; color: #96771A;">General</span>
                <h5 id="announcement-preview-title" style="font-size: 1rem; font-weight: 700; margin: 0.5rem 0 0.25rem; color: #1B1926;">Announcement title</h5>
                <p id="announcement-preview-body" style="font-size: 0.82rem; color: #5C5A6E; margin-bottom: 0.5rem;">Announcement body text will appear here as you type.</p>
                <span style="font-size: 0.68rem; color: #5C5A6E;">{{ now()->format('d M Y') }}</span>
            </div>
        </div>
    </div>
</div>

<script>
    (function () {
        var titleField = document.getElementById('announcement-title');
        var bodyField = document.getElementById('announcement-body');
        var categoryField = document.getElementById('announcement-category');
        var pinnedField = document.getElementById('is_pinned');

        var previewTitle = document.getElementById('announcement-preview-title');
        var previewBody = document.getElementById('announcement-preview-body');
        var previewCat = document.getElementById('announcement-preview-cat');
        var previewCard = document.getElementById('announcement-preview-card');

        if (!titleField || !previewTitle) {
            return;
        }

        function refresh() {
            previewTitle.textContent = titleField.value.trim() || 'Announcement title';
            previewBody.textContent = bodyField.value.trim() || 'Announcement body text will appear here as you type.';

            var categoryLabel = categoryField.options[categoryField.selectedIndex]
                ? categoryField.options[categoryField.selectedIndex].text
                : 'General';
            var pinned = pinnedField && pinnedField.checked;

            previewCat.textContent = (pinned ? '☆ ' : '') + categoryLabel;
            previewCard.style.borderTopColor = pinned ? '#C9A227' : '#E7EAF6';
        }

        [titleField, bodyField, categoryField].forEach(function (el) {
            el.addEventListener('input', refresh);
            el.addEventListener('change', refresh);
        });
        if (pinnedField) {
            pinnedField.addEventListener('change', refresh);
        }

        refresh();
    })();
</script>
