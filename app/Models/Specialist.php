<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Specialist extends Model
{
    use HasFactory, \App\Traits\Filterable;


    protected $fillable = [
        'national_id',
        'user_id',
        'code',
        'name',
        'specialization',
        'job_title',
        'phone',
        'email',
        'license_number',
        'qualification',
        'experience_years',
        'default_room',
        'work_days',
        'salary_type',
        'session_rate',
        'photo_path',
        'bio',
        'status',
    ];

    protected $casts = [
        'work_days'        => 'array',
        'experience_years' => 'integer',
        'session_rate'     => 'float',
    ];

    public function children(): HasMany
    {
        return $this->hasMany(Child::class, 'main_specialist', 'name');
    }

    public function therapySessions(): HasMany
    {
        return $this->hasMany(TherapySession::class, 'specialist_name', 'name');
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function getAvatarUrlAttribute(): string
    {
        if ($this->photo_path) {
            return asset('storage/' . $this->photo_path);
        }

        $seed = urlencode($this->name);
        return "https://api.dicebear.com/7.x/avataaars/svg?seed={$seed}";
    }

    public static function generateNextCode(): string
    {
        $last = self::latest('id')->first();
        $next = $last ? ($last->id + 101) : 101;
        return 'SP-' . $next;
    }
}