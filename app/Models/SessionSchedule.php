<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class SessionSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'child_id',
        'specialist_id',
        'specialist_name',
        'session_title',
        'session_date',
        'start_time',
        'end_time',
        'room_name',
        'day_of_week',
        'is_recurring',
        'status',
        'attendance_status',
        'notes',
    ];

    protected $casts = [
        'session_date' => 'date',
        'is_recurring' => 'boolean',
    ];

    public function child(): BelongsTo
    {
        return $this->belongsTo(Child::class);
    }

    public function specialist(): BelongsTo
    {
        return $this->belongsTo(Specialist::class);
    }

    public function getFormattedTimeRangeAttribute(): string
    {
        $start = $this->start_time ? Carbon::parse($this->start_time)->format('h:i A') : '';
        $end = $this->end_time ? Carbon::parse($this->end_time)->format('h:i A') : '';
        return $end ? "{$start} - {$end}" : $start;
    }

    public function getDayNameArabicAttribute(): string
    {
        if ($this->day_of_week) {
            return $this->day_of_week;
        }
        $days = [
            'Saturday'  => 'السبت',
            'Sunday'    => 'الأحد',
            'Monday'    => 'الإثنين',
            'Tuesday'   => 'الثلاثاء',
            'Wednesday' => 'الأربعاء',
            'Thursday'  => 'الخميس',
            'Friday'    => 'الجمعة',
        ];
        $en = Carbon::parse($this->session_date)->format('l');
        return $days[$en] ?? $en;
    }

    public function getCalendarColorAttribute(): string
    {
        if ($this->attendance_status === 'attended') {
            return '#10b981'; // Green for attended
        } elseif ($this->attendance_status === 'absent') {
            return '#f43f5e'; // Red for absent
        }
        return '#0ea5e9'; // Blue for scheduled/pending
    }

    public function getWhatsappReminderUrlAttribute(): string
    {
        $child = $this->child;
        $parentName = $child ? $child->parent_name : 'ولي الأمر';
        $phone = $child ? $child->phone : '';
        $centerSettings = \App\Http\Controllers\SettingController::getSettings();
        $centerName = $centerSettings['center_name'] ?? 'مركز الأمل للتأهيل';

        $msg = "مرحباً أستاذ/ة {$parentName} 👋\n"
             . "نود تذكيركم بموعد جلسة البطل ({$child->name}) في {$centerName}.\n\n"
             . "📅 الموعد: {$this->day_name_arabic} " . $this->session_date->format('Y-m-d') . " في تمام الساعة {$this->formatted_time_range}\n"
             . "🩺 الأخصائي المعالج: {$this->specialist_name}\n"
             . "🏢 القاعة: {$this->room_name}\n"
             . "📌 نوع الجلسة: {$this->session_title}\n\n"
             . "يمكنكم متابعة جدول الجلسات والفيديوهات عبر بوابة ولي الأمر: " . route('parent.portal', ['code' => $child->code]) . "\n\n"
             . "نتمنى للبطل دوام التقدم والشفاء!";

        return "https://wa.me/2{$phone}?text=" . urlencode($msg);
    }
}