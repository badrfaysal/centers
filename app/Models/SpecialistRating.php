<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SpecialistRating extends Model
{
    use HasFactory;

    protected $fillable = [
        'child_id',
        'specialist_name',
        'rating',
        'feedback',
        'parent_name',
    ];

    public function child(): BelongsTo
    {
        return $this->belongsTo(Child::class);
    }
}