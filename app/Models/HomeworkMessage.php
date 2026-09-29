<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HomeworkMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'homework_id',
        'sender_type',
        'message_type',
        'content',
        'read_at',
    ];

    protected $casts = [
        'read_at' => 'datetime',
    ];

    public function homework()
    {
        return $this->belongsTo(Homework::class);
    }
}