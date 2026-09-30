<?php

namespace App\Traits;

use App\Models\LeaveLetter;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

trait LetterTrait
{
    /**
     * Generate a unique secure code for letter verification
     */
    public function generateSecureCode($leaveRequestId)
    {
        return 'SMU-LTR-'.Str::upper(Str::random(8));
    }

    /**
     * Generate letter number, matching the institution's own reference
     * format (see e.g. "SJUT/SVAC/013/04/VOL IV/157" on real correspondence)
     * rather than a generic sequence.
     *
     * NOTE: "HR" here is a placeholder office code for leave letters - unlike
     * DVCA's "SVAC" code, the real code HR uses (if any) hasn't been
     * confirmed yet. Update once confirmed.
     */
    public function generateLetterNumber()
    {
        $year = date('Y');
        $lastLetter = LeaveLetter::whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();

        if ($lastLetter && $lastLetter->letter_number) {
            $lastNumber = intval(substr($lastLetter->letter_number, -4));
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }

        return "SJUT/HR/{$year}/{$newNumber}";
    }

    /**
     * Save letter to database
     */
    public function saveLetter($leaveRequest, $content, $type = 'approval')
    {
        $letter = LeaveLetter::create([
            'leave_request_id' => $leaveRequest->id,
            'letter_number' => $this->generateLetterNumber(),
            'secure_code' => $this->generateSecureCode($leaveRequest->id),
            'title' => $type === 'approval' ? 'Leave Approval Letter' : 'Leave '.ucfirst($type).' Letter',
            'content' => $content,
            'type' => $type,
            'generated_by' => auth()->user()->full_name ?? 'System',
            'generated_by_role' => auth()->user()->getRoleNames()->first(),
            'stage' => 'generated',
        ]);

        return $letter;
    }

    /**
     * Generate QR code for letter verification
     */
    public function generateQrCode($secureCode)
    {
        $verificationUrl = route('verify.letter', $secureCode);

        return QrCode::size(80)->generate($verificationUrl);
    }
}
