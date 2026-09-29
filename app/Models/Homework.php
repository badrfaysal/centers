<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Homework extends Model
{
    use HasFactory;
    
    protected $table = 'homeworks';

    protected $fillable = [
        'child_id',
        'specialist_id',
        'therapy_session_id',
        'title',
        'description',
        'status',
    ];

    public function child()
    {
        return $this->belongsTo(Child::class);
    }

    public function specialist()
    {
        return $this->belongsTo(User::class, 'specialist_id');
    }

    public function therapySession()
    {
        return $this->belongsTo(TherapySession::class);
    }

    public function messages()
    {
        return $this->hasMany(HomeworkMessage::class);
    }
}