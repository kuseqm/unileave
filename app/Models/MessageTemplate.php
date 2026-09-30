<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MessageTemplate extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'subject_template',
        'body_template',
        'category',
        'placeholders',
        'is_active',
    ];

    protected $casts = [
        'placeholders' => 'array',
        'is_active' => 'boolean',
    ];

    public function renderSubject(array $data): string
    {
        $subject = $this->subject_template;
        foreach ($data as $key => $value) {
            $subject = str_replace('{'.$key.'}', $value, $subject);
        }

        return $subject;
    }

    public function renderBody(array $data): string
    {
        $body = $this->body_template;
        foreach ($data as $key => $value) {
            $body = str_replace('{'.$key.'}', $value, $body);
        }

        return $body;
    }
}
