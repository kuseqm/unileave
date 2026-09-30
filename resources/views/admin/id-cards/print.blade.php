<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>ID Card — {{ $idCard->staff->full_name }}</title>
<style>
/* =========================
   PRINT SETTINGS
   ========================= */
@media print {
    @page { size: 85.6mm 54mm; margin:0; }

    html, body {
        width:100%;
        height:100%;
        margin:0;
        padding:0;
        background:white;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }

    body * { visibility: hidden !important; }
    .print-area, .print-area * { visibility: visible !important; }
    button { display: none !important; }

    .id-card-wrapper { page-break-after: always; }
    .id-card-wrapper:last-child { page-break-after: auto; }

    .id-card {
        transform: scale(2.2);
        transform-origin: top left;
        border-radius:0;
    }
}

body { background: #eee; font-family: Arial, sans-serif; }

/* On screen (not printing) the card's fixed 570px print-scale width is
   wider than most preview containers - without this it just gets clipped
   by the iframe/card edge instead of showing the whole card. The JS below
   scales each .id-card down to fit its wrapper (screen only), and undoes
   that transform right before printing so it can never fight the real
   @media print scale(2.2)/@page rule above, which is tuned for the actual
   card printer - not touched here. Scoped to @media screen specifically -
   the same overflow:hidden applying during print would clip the
   scale(2.2) card to its un-scaled layout box, cutting it into pieces. */
@media screen {
    .id-card-wrapper {
        overflow: hidden;
    }
}

.value-strong {
    font-weight: 900;
    color: #000;
}

.back-strong {
    font-weight: 900;
    color: #000;
}
</style>
</head>
<body>

<div class="print-area">

    <!-- ================= FRONT SIDE ================= -->
    <div class="id-card-wrapper">
        <div class="card p-0 id-card"
             style="width:570px; height:424px; border-radius:0px;
             overflow:hidden; border:1px solid #ccc;
             font-family:Arial,sans-serif; position:relative;">

            <!-- HEADER -->
            <div style="display:flex; align-items:center; padding:8px 12px 4px;">
                <div style="flex:1; text-align:center;">
                    <h3 style="margin:0; color:#000; font-weight:900; font-size:22px;">
                        {{ $idCard->staff->university->name ?? 'St. John\'s University of Tanzania' }}
                    </h3>
                    <p style="margin:0; font-weight:bold; font-size:16px;">
                        Staff Identity Card
                    </p>
                </div>
                @if($idCard->staff->university?->has_logo)
                <img src="{{ $idCard->staff->university->logo_url }}" style="height:50px;">
                @endif
            </div>

            <!-- DOUBLE LINE -->
            <div>
                <div style="height:4px; background:#3c3ff3;"></div>
                <div style="height:4px; background:#2C7EFA;"></div>
            </div>

            <!-- BODY -->
            <div style="background:linear-gradient(to bottom, rgba(200,92,233,.8) 0%, rgba(233,248,255,.3) 30%, #fff 100%);
                        padding:16px 18px 85px; display:flex; position:relative;">

                <!-- PHOTO -->
                <div style="width:110px; height:135px; border:1px solid #ccc; border-radius:4px; overflow:hidden; background:#f5f5f5;">
                    @if($idCard->staff->avatar)
                    <img src="{{ asset('storage/'.$idCard->staff->avatar) }}"
                         style="width:100%; height:100%; object-fit:cover;">
                    @endif
                </div>

                <div style="flex:1; margin-left:20px; font-size:15px;">

                    <div style="display:flex; margin-bottom:6px;">
                        <div style="width:120px; font-weight:bold;">Staff ID:</div>
                        <div class="value-strong">
                            {{ $idCard->staff->staff_id }}
                        </div>
                    </div>

                    <div style="display:flex; margin-bottom:6px;">
                        <div style="width:120px; font-weight:bold;">Name:</div>
                        <div class="value-strong" style="font-size:16px;">
                            {{ $idCard->staff->title }} {{ strtoupper($idCard->staff->first_name) }}
                            {{ $idCard->staff->last_name }}
                        </div>
                    </div>

                    <div style="display:flex; margin-bottom:6px;">
                        <div style="width:120px; font-weight:bold;">Job Title:</div>
                        <div class="value-strong">
                            {{ $idCard->staff->job_title }}
                        </div>
                    </div>

                    <div style="display:flex; margin-bottom:6px;">
                        <div style="width:120px; font-weight:bold;">Valid Until:</div>
                        <div class="value-strong">
                            {{ $idCard->valid_until?->format('d M Y') }}
                        </div>
                    </div>

                </div>

                <!-- SIGNATURE -->
                <div style="position:absolute; bottom:0; left:15px; text-align:center;">
                    <strong>Staff Signature</strong><br>
                    @if($idCard->signature_path)
                        <img src="{{ asset('storage/'.$idCard->signature_path) }}" style="height:50px;">
                    @endif
                </div>

                <!-- QR -->
                <div style="position:absolute; bottom:10px; right:15px;">
                    <canvas id="qrcode"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- ================= BACK SIDE ================= -->
    <div class="id-card-wrapper">
        <div class="id-card"
             style="width:570px; height:330px;
             background: linear-gradient(135deg, #FCFCFC 0%, #F7F7FA 100%);
             border:1px solid #ccc; border-radius:0px; overflow:hidden;
             font-family: Arial,sans-serif; padding:2px; position:relative;">

            <div style="text-align:center;">
                <h2 style="margin:0; font-size:16pt; color:#000; font-weight:900;">
                    {{ $idCard->staff->university->name ?? 'St. John\'s University of Tanzania' }}
                </h2>
                <h4 style="font-size:12pt; color:#000; font-weight:900; font-style:italic;">
                    To Learn To Serve
                </h4>
            </div>

            <div>
                <div style="height:4px;background:#3c3ff3;"></div>
                <div style="height:4px;background:#2C7EFA;"></div>
            </div>

            <div style="background: linear-gradient(to bottom, rgba(200,92,233,0.8) 0%, rgba(248,249,250,0) 40%);
                        padding:10px;">

                <table style="width:100%; text-align:center; font-size:11pt;">
                    <tr>
                        <td class="back-strong">
                            <p>P.O. Box 47, Dodoma, Tanzania</p>
                            <p>Tel: +255 (0)26 239 0044</p>
                            <p>Email: admin@sjut.ac.tz</p>
                            <p>Website: www.sjut.ac.tz</p>
                            <p>
                                This card is the property of <strong>SJUT</strong> and must be returned to the
                                University administration upon request.
                            </p>
                            <p>
                                If found, please return it to the address provided above.
                            </p>
                        </td>
                    </tr>
                </table>

            </div>

        </div>
    </div>

</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/qrious/4.0.2/qrious.min.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function () {
    new QRious({
        element: document.getElementById('qrcode'),
        value: "StaffID: {{ $idCard->staff->staff_id }}",
        size: 70,
        level: 'H'
    });

    // Scale each fixed-size card down to fit its wrapper on screen (this
    // view's cards are sized in real print pixels, e.g. 570px wide, which
    // is wider than most preview containers - without this the right side
    // just gets clipped instead of shrinking to fit).
    function fitCards() {
        document.querySelectorAll('.id-card').forEach(function (card) {
            const wrapper = card.parentElement;
            const naturalWidth = card.offsetWidth / (parseFloat(card.dataset.scale) || 1);
            const naturalHeight = card.offsetHeight / (parseFloat(card.dataset.scale) || 1);
            if (!card.dataset.naturalWidth) {
                card.dataset.naturalWidth = naturalWidth;
                card.dataset.naturalHeight = naturalHeight;
            }
            const w = parseFloat(card.dataset.naturalWidth);
            const h = parseFloat(card.dataset.naturalHeight);
            const scale = Math.min(1, wrapper.clientWidth / w);
            card.dataset.scale = scale;
            card.style.transform = 'scale(' + scale + ')';
            card.style.transformOrigin = 'top left';
            wrapper.style.height = (h * scale) + 'px';
        });
    }

    fitCards();
    window.addEventListener('resize', fitCards);

    // Printing has its own scale(2.2) rule for the physical card size -
    // never let the on-screen fit-to-container scale leak into that.
    window.addEventListener('beforeprint', function () {
        document.querySelectorAll('.id-card').forEach(function (card) {
            card.style.transform = '';
            card.parentElement.style.height = '';
        });
    });
    window.addEventListener('afterprint', fitCards);

    @if(request()->boolean('autoprint'))
        window.print();
    @endif
});
</script>

</body>
</html>
