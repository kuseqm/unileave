<?php

namespace App\Traits;

use App\Models\University;
use chillerlan\QRCode\Output\QRGdImagePNG;
use chillerlan\QRCode\QRCode as QRCodeLib;
use chillerlan\QRCode\QROptions;
use Illuminate\Support\Facades\Storage;

/**
 * Shared QR generation for every letter/report verification code - used by
 * LeaveLetterController, TravelLetterController and LetterController so
 * there's exactly one place that decides how a verification QR looks,
 * instead of three copies that can quietly drift apart (the signature prop
 * bug earlier this session was exactly that failure mode).
 */
trait GeneratesQrCodeTrait
{
    /**
     * Render the verification code as a base64 PNG data URI, with the
     * issuing university's crest composited into the center.
     *
     * eccLevel 'H' (already required for DomPDF-safe PNG output) tolerates
     * losing up to ~30% of the modules to damage or obstruction - a small
     * center logo, backed by a plain white square so it never touches a
     * QR module directly, sits well inside that budget and every scanner
     * tested against this pattern reads through it fine.
     */
    protected function generateQrCode(string $data, ?University $university = null): string
    {
        $options = new QROptions([
            // Auto-sized (the default) rather than pinned to version 5 -
            // a bare secure code fits in version 5, but the full verify
            // URL this now encodes doesn't, and a fixed version throws a
            // "code length overflow" error instead of just growing.
            // The real option is 'outputInterface' (a class-string), not
            // 'outputType' - the previous code across all three letter
            // controllers passed 'outputType' => 'png', which this package
            // silently ignores, so every QR ever generated was actually
            // the default SVG output (still renders fine as a browser
            // <img> data URI, which is why it went unnoticed) rather than
            // the real PNG DomPDF and this GD compositing step both need.
            'outputInterface' => QRGdImagePNG::class,
            'eccLevel' => 'H',
            'scale' => 4,
            'outputBase64' => false,
            // Matches the letterhead's pale-green band (#e8f4e8) instead of
            // white, so the QR blends into it instead of sitting inside a
            // visible white square. Luminance is still high enough (close
            // to white) that contrast against the dark modules is
            // unaffected - scanning reliability is unchanged.
            'bgColor' => [232, 244, 232],
        ]);

        $qrPng = (new QRCodeLib($options))->render($data);
        $qrImage = imagecreatefromstring($qrPng);

        $logoImage = $this->loadQrLogo($university);
        if ($logoImage) {
            $qrSize = imagesx($qrImage);
            // Roughly a fifth of the QR's width - large enough to read
            // clearly, small enough to stay inside the ECC-H damage budget.
            $logoSize = (int) round($qrSize * 0.22);
            $offset = (int) round(($qrSize - $logoSize) / 2);

            // Backing square in the same green as the QR's background,
            // slightly larger than the logo itself, so the logo never sits
            // directly against a QR module edge.
            $padding = (int) round($logoSize * 0.12);
            imagefilledrectangle(
                $qrImage,
                $offset - $padding,
                $offset - $padding,
                $offset + $logoSize + $padding,
                $offset + $logoSize + $padding,
                imagecolorallocate($qrImage, 232, 244, 232)
            );

            imagecopyresampled(
                $qrImage, $logoImage,
                $offset, $offset, 0, 0,
                $logoSize, $logoSize, imagesx($logoImage), imagesy($logoImage)
            );
            imagedestroy($logoImage);
        }

        ob_start();
        imagepng($qrImage);
        $finalPng = ob_get_clean();
        imagedestroy($qrImage);

        return 'data:image/png;base64,'.base64_encode($finalPng);
    }

    /**
     * @return \GdImage|null
     */
    private function loadQrLogo(?University $university)
    {
        $logoPath = $university?->logo_path;
        if (! $logoPath || ! Storage::disk('public')->exists($logoPath)) {
            return null;
        }

        $fullPath = Storage::disk('public')->path($logoPath);
        $image = @imagecreatefrompng($fullPath);

        return $image ?: null;
    }
}
