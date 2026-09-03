<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ParentMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'child_id',
        'recipient_type',
        'status',
        'is_urgent',
        'parent_name',
        'subject',
        'message',
        'doctor_reply',
        'admin_notes',
        'replied_by',
        'replied_at',
    ];

    protected $casts = [
        'is_urgent'  => 'boolean',
        'replied_at' => 'datetime',
    ];

    public function child(): BelongsTo
    {
        return $this->belongsTo(Child::class);
    }
}