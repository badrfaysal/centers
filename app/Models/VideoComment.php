<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VideoComment extends Model
{
    use HasFactory;

    protected $fillable = [
        'therapy_session_id',
        'child_id',
        'sender_type',
        'sender_name',
        'comment',
    ];

    public function session(): BelongsTo
    {
        return $this->belongsTo(TherapySession::class, 'therapy_session_id');
    }

    public function child(): BelongsTo
    {
        return $this->belongsTo(Child::class);
    }
}