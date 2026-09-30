<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StaffPublication extends Model
{
    protected $table = 'staff_publications';

    protected $fillable = [
        'staff_id', 'publication_title', 'publication_type', 'journal_name',
        'publisher', 'publication_year', 'doi', 'url', 'is_peer_reviewed',
        'authors', 'citations_count', 'file_path',
    ];

    protected $casts = [
        'is_peer_reviewed' => 'boolean',
        'authors' => 'array',
    ];

    public function staff()
    {
        return $this->belongsTo(User::class, 'staff_id');
    }

    public function getAuthorListAttribute()
    {
        if (is_array($this->authors)) {
            return implode(', ', $this->authors);
        }

        return $this->authors;
    }
}
