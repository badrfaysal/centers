<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TherapySession extends Model
{
    use HasFactory;

    protected $fillable = [
        'child_id',
        'specialist_name',
        'session_date',
        'session_time',
        'room_name',
        'session_type',
        'child_mood',
        'goals_evaluated',
        'clinical_notes',
        'home_exercise',
        'video_path',
        'video_title',
        'video_duration',
        'whatsapp_notified',
    ];

    protected $casts = [
        'session_date' => 'date',
        'goals_evaluated' => 'array',
        'whatsapp_notified' => 'boolean',
    ];

    public function child(): BelongsTo
    {
        return $this->belongsTo(Child::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(VideoComment::class, 'therapy_session_id')->oldest();
    }

    public function videoComments(): HasMany
    {
        return $this->hasMany(VideoComment::class, 'therapy_session_id')->oldest();
    }

    public function getVideoUrlAttribute(): ?string
    {
        if ($this->video_path) {
            return asset('storage/' . $this->video_path);
        }
        return null;
    }

    /**
     * Generate a WhatsApp wa.me URL with a beautifully formatted session summary message for the parent.
     */
    public function getWhatsappSessionSummaryUrlAttribute(): string
    {
        $child = $this->child;
        $parentName = $child ? $child->parent_name : 'ولي الأمر';
        $phone = $child ? preg_replace('/[^0-9]/', '', $child->phone) : '';
        $centerSettings = \App\Http\Controllers\SettingController::getSettings();
        $centerName = $centerSettings['center_name'] ?? 'مركز الأمل للتأهيل';

        // بناء رسالة ملخص الجلسة بشكل شيك ومنسق
        $msg = "✨ *ملخص جلسة اليوم* ✨\n";
        $msg .= "━━━━━━━━━━━━━━━━\n\n";

        $msg .= "السلام عليكم أستاذ/ة *{$parentName}* 👋\n";
        $msg .= "نسعد بمشاركتكم تقرير جلسة البطل/ة *{$child->name}* في {$centerName}.\n\n";

        $msg .= "📅 *التاريخ:* " . ($this->session_date ? $this->session_date->format('Y-m-d') : '-') . "\n";
        if ($this->session_time) {
            $msg .= "🕐 *التوقيت:* {$this->session_time}\n";
        }
        $msg .= "🩺 *الأخصائي:* {$this->specialist_name}\n";
        $msg .= "🏥 *نوع الجلسة:* {$this->session_type}\n";
        $msg .= "🏢 *القاعة:* {$this->room_name}\n\n";

        // حالة الطفل مع إيموجي مناسب
        $moodEmoji = match(true) {
            str_contains($this->child_mood ?? '', 'ممتاز') => '😊',
            str_contains($this->child_mood ?? '', 'متوسط') => '😐',
            str_contains($this->child_mood ?? '', 'مقاوم') => '😢',
            str_contains($this->child_mood ?? '', 'نشاط')  => '🏃',
            default => '📋',
        };
        $msg .= "{$moodEmoji} *حالة الطفل:* {$this->child_mood}\n\n";

        $msg .= "━━━━━━━━━━━━━━━━\n";
        $msg .= "📝 *ملاحظات الأخصائي:*\n";
        $msg .= $this->clinical_notes . "\n\n";

        // الأهداف المنجزة مع نسب الإنجاز
        if (!empty($this->goals_evaluated) && is_array($this->goals_evaluated)) {
            $msg .= "🎯 *الأهداف التي تم التدريب عليها:*\n";
            foreach ($this->goals_evaluated as $i => $goal) {
                $num = $i + 1;
                if (is_array($goal)) {
                    $text = $goal['text'] ?? '';
                    $pct = (int) ($goal['percentage'] ?? 0);
                    $filled = (int) round($pct / 10);
                    $empty = 10 - $filled;
                    $bar = str_repeat('▓', $filled) . str_repeat('░', $empty);
                    $emoji = $pct >= 100 ? '✅' : ($pct >= 75 ? '🟢' : ($pct >= 50 ? '🟡' : ($pct >= 25 ? '🟠' : '⚪')));
                    $msg .= "  {$num}. {$text}\n";
                    $msg .= "     {$emoji} {$bar} *{$pct}%*\n";
                } else {
                    $msg .= "  {$num}. {$goal}\n";
                }
            }
            $msg .= "\n";
        }

        // التمرين المنزلي
        if (!empty($this->home_exercise)) {
            $msg .= "━━━━━━━━━━━━━━━━\n";
            $msg .= "🏠 *التمرين المنزلي المطلوب:*\n";
            $msg .= $this->home_exercise . "\n\n";
        }

        // رابط بوابة ولي الأمر
        if ($child && $child->code) {
            $msg .= "━━━━━━━━━━━━━━━━\n";
            $msg .= "📱 *لمتابعة كل الجلسات والفيديوهات:*\n";
            $msg .= route('parent.portal', ['code' => $child->code]) . "\n\n";
        }

        $msg .= "نتمنى لبطلنا *{$child->name}* دوام التقدم والنجاح! 🌟\n";
        $msg .= "فريق عمل *{$centerName}* 💚";

        return "https://wa.me/2{$phone}?text=" . urlencode($msg);
    }
}