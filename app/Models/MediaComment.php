<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MediaComment extends Model
{
    protected $guarded = [];

    public function media() {
        return $this->belongsTo(ChildMedia::class, 'child_media_id');
    }

    public function user() {
        return $this->belongsTo(User::class);
    }
}
