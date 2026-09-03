<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChildMedia extends Model
{
    protected $guarded = [];

    public function child() {
        return $this->belongsTo(Child::class);
    }

    public function uploader() {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function comments() {
        return $this->hasMany(MediaComment::class)->latest();
    }
}
