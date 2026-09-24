<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChildTest extends Model
{
    use HasFactory;

    protected $fillable = [
        'child_id',
        'test_name',
        'test_date',
        'score',
        'notes',
        'file_path'
    ];

    protected $casts = [
        'test_date' => 'date',
    ];

    public function child()
    {
        return $this->belongsTo(Child::class);
    }
}
