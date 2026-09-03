<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Child extends Model
{
    use HasFactory, \App\Traits\Filterable;


    protected $fillable = [
        'national_id',
        'user_id',
        'code',
        'name',
        'birth_date',
        'mental_age',
        'gender',
        'parent_name',
        'parent_relation',
        'phone',
        'emergency_phone',
        'address',
        'initial_diagnosis',
        'diagnosis_category',
        'iq_tests_history',
        'main_specialist',
        'neurologist_name',
        'current_medications',
        'medical_notes',
        'assistive_devices',
        'package_type',
        'avatar',
        'photo_path',
        'status',
    ];

    protected $casts = [
        'birth_date' => 'date',
    ];

    /**
     * Relationship with child therapy sessions.
     */
    public function sessionSchedules(): HasMany
    {
        return $this->hasMany(SessionSchedule::class)->orderBy('session_date')->orderBy('start_time');
    }

    public function therapySessions(): HasMany
    {
        return $this->hasMany(TherapySession::class)->latest('session_date');
    }

    /**
     * Relationship with parent messages.
     */
    public function parentMessages(): HasMany
    {
        return $this->hasMany(ParentMessage::class)->latest();
    }

    /**
     * Relationship with invoices.
     */
    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class)->latest();
    }

    /**
     * إجمالي الدين المتبقي على ولي أمر هذا الطفل.
     */
    public function getTotalDebtAttribute(): float
    {
        return $this->invoices()->where('remaining_amount', '>', 0)->sum('remaining_amount');
    }

    /**
     * Relationship with specialist ratings.
     */
    public function specialistRatings(): HasMany
    {
        return $this->hasMany(SpecialistRating::class)->latest();
    }

    /**
     * Get diagnoses as an independent array list.
     */
    public function getDiagnosesListAttribute(): array
    {
        if (empty($this->initial_diagnosis)) {
            return [];
        }

        $decoded = json_decode($this->initial_diagnosis, true);
        if (is_array($decoded)) {
            return array_values(array_filter($decoded));
        }

        return array_values(array_filter(array_map('trim', explode("\n", $this->initial_diagnosis))));
    }

    /**
     * Auto-calculate child chronological age (e.g. 5 سنوات و 3 أشهر)
     */
    public function getAgeTextAttribute(): string
    {
        if (!$this->birth_date) {
            return 'غير محدد';
        }

        $now = Carbon::now();
        $years = (int) $this->birth_date->diffInYears($now);
        $months = ((int) $this->birth_date->diffInMonths($now)) % 12;

        if ($years == 0) {
            return "{$months} أشهر";
        }

        if ($months == 0) {
            return "{$years} سنوات";
        }

        return "{$years} سنوات و {$months} أشهر";
    }

    /**
     * Get avatar or uploaded child photo.
     */
    public function getAvatarUrlAttribute(): string
    {
        if ($this->photo_path) {
            return asset('storage/' . $this->photo_path);
        }

        if ($this->avatar) {
            return $this->avatar;
        }

        $seed = urlencode($this->name);
        return "https://api.dicebear.com/7.x/bottts/svg?seed={$seed}";
    }

    /**
     * Generate next available child code (e.g. CH-1001, CH-1002).
     */
    public static function generateNextCode(): string
    {
        $lastChild = self::latest('id')->first();
        $nextNumber = $lastChild ? ($lastChild->id + 1001) : 1001;
        return 'CH-' . $nextNumber;
    }
}