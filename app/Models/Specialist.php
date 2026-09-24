<?php

namespace App\Models;

use App\Traits\Filterable;
use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Specialist extends Model
{
    use HasFactory, Filterable, LogsActivity;


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

    public function getExperienceYearsAttribute($value)
    {
        if (!$this->created_at) {
            return $value;
        }
        $diff = now()->year - $this->created_at->year;
        return $value + max(0, $diff);
    }

    public function setExperienceYearsAttribute($value)
    {
        if ($this->exists && $this->created_at) {
            $diff = now()->year - $this->created_at->year;
            $this->attributes['experience_years'] = max(0, $value - $diff);
        } else {
            $this->attributes['experience_years'] = $value;
        }
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