<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PrintQueue extends Model
{
    use HasFactory;

    protected $table = 'print_queue';

    protected $fillable = [
        'outgoing_letter_id',
        'recipient_address',
        'printed_by',
        'printed_at',
        'dispatched_by',
        'dispatched_at',
        'status',
    ];

    protected $casts = [
        'printed_at' => 'datetime',
        'dispatched_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // ========== RELATIONSHIPS ==========

    /**
     * Outgoing letter that needs printing
     */
    public function outgoingLetter()
    {
        return $this->belongsTo(OutgoingLetter::class, 'outgoing_letter_id');
    }

    /**
     * Staff member who printed the letter
     */
    public function printedBy()
    {
        return $this->belongsTo(User::class, 'printed_by');
    }

    /**
     * Staff member who dispatched the letter
     */
    public function dispatchedBy()
    {
        return $this->belongsTo(User::class, 'dispatched_by');
    }

    // ========== SCOPES ==========

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopePrinted($query)
    {
        return $query->where('status', 'printed');
    }

    public function scopeDispatched($query)
    {
        return $query->where('status', 'dispatched');
    }

    // ========== HELPERS ==========

    /**
     * Mark as printed
     */
    public function markAsPrinted($userId)
    {
        $this->update([
            'printed_by' => $userId,
            'printed_at' => now(),
            'status' => 'printed',
        ]);
    }

    /**
     * Mark as dispatched
     */
    public function markAsDispatched($userId)
    {
        $this->update([
            'dispatched_by' => $userId,
            'dispatched_at' => now(),
            'status' => 'dispatched',
        ]);

        // Also update the outgoing letter status
        if ($this->outgoingLetter) {
            $this->outgoingLetter->update([
                'status' => 'dispatched',
                'dispatched_at' => now(),
            ]);
        }
    }
}