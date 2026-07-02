<?php

namespace App\Traits;

use App\Models\LeaveLetter;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

trait LetterTrait
{
    /**
     * Generate a unique secure code for letter verification
     */
    public function generateSecureCode($leaveRequestId)
    {
        return 'SMU-LTR-' . strtoupper(substr(md5($leaveRequestId . uniqid() . date('YmdHis')), 0, 8));
    }
    
    /**
     * Generate letter number
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
        
        return "LTR/{$year}/{$newNumber}";
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
            'title' => $type === 'approval' ? 'Leave Approval Letter' : 'Leave ' . ucfirst($type) . ' Letter',
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