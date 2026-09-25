<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    protected $fillable = [
        'user_id', 'action', 'model_type', 'model_id', 
        'old_values', 'new_values', 'ip_address', 'user_agent'
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function model()
    {
        return $this->morphTo();
    }

    public function getModelNameArabic()
    {
        if (!$this->model_type) return '';
        $name = class_basename($this->model_type);
        return match($name) {
            'User' => 'مستخدم',
            'Child' => 'طفل',
            'Specialist' => 'أخصائي',
            'SessionSchedule' => 'جلسة/موعد',
            'Waitlist' => 'طلب انتظار',
            'Expense' => 'مصروف',
            'Invoice' => 'فاتورة',
            default => 'سجل'
        };
    }

    public function getHumanActionSummary()
    {
        $modelName = $this->getModelNameArabic();
        $userName = $this->user ? $this->user->name : 'النظام';
        
        if ($this->action === 'login') return "قام {$userName} بتسجيل الدخول للنظام";
        if ($this->action === 'logout') return "قام {$userName} بتسجيل الخروج من النظام";
        
        $entityName = '';
        if ($this->new_values && isset($this->new_values['name'])) {
            $entityName = $this->new_values['name'];
        } elseif ($this->old_values && isset($this->old_values['name'])) {
            $entityName = $this->old_values['name'];
        }

        $entityDisplay = $entityName ? " ($entityName)" : "";

        if ($this->action === 'created') return "قام {$userName} بإضافة {$modelName} جديد{$entityDisplay}";
        if ($this->action === 'deleted') return "قام {$userName} بحذف {$modelName}{$entityDisplay}";
        if ($this->action === 'updated') return "قام {$userName} بتعديل بيانات {$modelName}{$entityDisplay}";
        
        return "إجراء غير معروف";
    }

    public function getHumanChanges()
    {
        if ($this->action !== 'updated' || !$this->old_values || !$this->new_values) return [];
        
        $changes = [];
        $dictionary = [
            'name' => 'الاسم',
            'national_id' => 'رقم الهوية',
            'phone' => 'رقم الهاتف',
            'attendance_status' => 'حالة الحضور',
            'session_date' => 'تاريخ الجلسة',
            'session_time' => 'وقت الجلسة',
            'status' => 'الحالة',
            'amount' => 'المبلغ',
            'notes' => 'الملاحظات',
            'role' => 'الصلاحية',
            'email' => 'البريد الإلكتروني'
        ];

        $valueTranslations = [
            'pending' => 'معلق / قيد الانتظار',
            'attended' => 'حاضر',
            'absent' => 'غائب',
            'admin' => 'مدير',
            'specialist' => 'أخصائي',
            'parent' => 'ولي أمر',
        ];

        foreach ($this->new_values as $key => $newValue) {
            if (array_key_exists($key, $this->old_values)) {
                $oldValue = $this->old_values[$key];
                
                // تجاهل أعمدة النظام
                if (in_array($key, ['updated_at', 'created_at', 'id'])) continue;
                
                if ($oldValue != $newValue) {
                    $humanKey = $dictionary[$key] ?? $key;
                    
                    // تحويل القيم التي قد تكون مصفوفات إلى نصوص لتجنب خطأ Illegal offset type
                    $oldValScalar = is_array($oldValue) ? json_encode($oldValue, JSON_UNESCAPED_UNICODE) : (is_scalar($oldValue) ? $oldValue : (string)$oldValue);
                    $newValScalar = is_array($newValue) ? json_encode($newValue, JSON_UNESCAPED_UNICODE) : (is_scalar($newValue) ? $newValue : (string)$newValue);

                    $humanOld = (is_scalar($oldValue) && isset($valueTranslations[$oldValue])) ? $valueTranslations[$oldValue] : $oldValScalar;
                    $humanNew = (is_scalar($newValue) && isset($valueTranslations[$newValue])) ? $valueTranslations[$newValue] : $newValScalar;
                    
                    if ($humanOld === null || $humanOld === '') $humanOld = '(فارغ)';
                    if ($humanNew === null || $humanNew === '') $humanNew = '(فارغ)';
                    
                    $changes[] = "تم تغيير <b>{$humanKey}</b> من <u>{$humanOld}</u> إلى <u class='text-emerald-700 font-bold'>{$humanNew}</u>";
                }
            }
        }
        return $changes;
    }
}
