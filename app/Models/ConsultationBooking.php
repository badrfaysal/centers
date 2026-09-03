<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ConsultationBooking extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_code',
        'parent_name',
        'phone',
        'parent_password',
        'child_name',
        'child_age',
        'service',
        'notes',
        'status',
        'scheduled_at',
        'specialist_name',
        'room',
        'session_price',
        'payment_status',
        'admin_notes',
        'confirmed_by',
        'confirmed_at',
        'child_id',
    ];

    protected $casts = [
        'scheduled_at'  => 'datetime',
        'confirmed_at'  => 'datetime',
        'session_price' => 'float',
    ];

    public function child(): BelongsTo
    {
        return $this->belongsTo(Child::class);
    }

    public static function generateNextCode(): string
    {
        $last = self::latest('id')->first();
        $next = $last ? ($last->id + 2001) : 2001;
        return 'BK-' . $next;
    }

    public function getWhatsappConfirmationUrlAttribute(): string
    {
        $centerSettings = \App\Http\Controllers\SettingController::getSettings();
        $centerName = $centerSettings['center_name'] ?? 'مركز الأمل للتأهيل';
        $timeStr = $this->scheduled_at ? $this->scheduled_at->format('Y-m-d h:i A') : 'الموعد المحدد';
        $currency = $centerSettings['currency'] ?? 'ج.م';
        
        $msg = "مرحباً أستاذ/ة {$this->parent_name} \n"
             . "يسرنا إبلاغكم بتأكيد موعد جلسة التقييم الأولي للبطل ({$this->child_name}) في {$centerName}.\n\n"
             . " الموعد: {$timeStr}\n"
             . " الأخصائي المعالج: " . ($this->specialist_name ?? 'الاستشاري المتابع') . "\n"
             . " القاعة: " . ($this->room ?? 'غرفة التقييم') . "\n"
             . " رسوم الجلسة: {$this->session_price} {$currency}\n"
             . " العنوان: " . ($centerSettings['address'] ?? 'مقر المركز') . "\n\n"
             . "يمكنكم متابعة تفاصيل حجزكم عبر بوابتكم الإلكترونية: " . route('parent.bookings.track', ['phone' => $this->phone]) . "\n\n"
             . "نتطلع لرؤيتكم ونتمنى للبطل دوام الصحة والتوفيق!";

        return "https://wa.me/2{$this->phone}?text=" . urlencode($msg);
    }
}