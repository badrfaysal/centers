<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_number',
        'child_id',
        'specialist_id',
        'child_name',
        'specialist_name',
        'parent_name',
        'session_price',
        'sessions_count',
        'total_amount',
        'discount_amount',
        'discount_percentage',
        'net_amount',
        'paid_amount',
        'remaining_amount',
        'payment_method',
        'payment_status',
        'notes',
        'invoice_date',
    ];

    protected $casts = [
        'session_price'       => 'float',
        'total_amount'        => 'float',
        'discount_amount'     => 'float',
        'discount_percentage' => 'float',
        'net_amount'          => 'float',
        'paid_amount'         => 'float',
        'remaining_amount'    => 'float',
        'sessions_count'      => 'integer',
        'invoice_date'        => 'date',
    ];

    /**
     * العلاقة مع الطفل.
     */
    public function child(): BelongsTo
    {
        return $this->belongsTo(Child::class);
    }

    /**
     * العلاقة مع الأخصائي.
     */
    public function specialist(): BelongsTo
    {
        return $this->belongsTo(Specialist::class);
    }

    /**
     * Scope: الفواتير التي عليها ديون (باقي > 0).
     */
    public function scopeDebts($query)
    {
        return $query->where('remaining_amount', '>', 0);
    }

    /**
     * Scope: الفواتير المدفوعة بالكامل.
     */
    public function scopePaid($query)
    {
        return $query->where('payment_status', 'paid');
    }

    /**
     * تحديد حالة الدفع تلقائياً بناءً على المبالغ.
     */
    public function calculatePaymentStatus(): string
    {
        if ($this->paid_amount >= $this->net_amount) {
            return 'paid';
        }
        if ($this->paid_amount > 0) {
            return 'partial';
        }
        return 'unpaid';
    }

    /**
     * الحصول على اسم طريقة الدفع بالعربي.
     */
    public function getPaymentMethodLabelAttribute(): string
    {
        return match ($this->payment_method) {
            'cash'     => 'نقدي',
            'transfer' => 'تحويل بنكي',
            'visa'     => 'فيزا / بطاقة',
            default    => $this->payment_method,
        };
    }

    /**
     * الحصول على حالة الدفع بالعربي.
     */
    public function getPaymentStatusLabelAttribute(): string
    {
        return match ($this->payment_status) {
            'paid'    => 'مدفوع بالكامل',
            'partial' => 'مدفوع جزئياً',
            'unpaid'  => 'غير مدفوع',
            default   => $this->payment_status,
        };
    }

    /**
     * توليد رقم الفاتورة التالي تلقائياً.
     */
    public static function generateNextInvoiceNumber(): string
    {
        $last = self::latest('id')->first();
        $next = $last ? ($last->id + 1) : 1;
        return 'INV-' . str_pad($next, 4, '0', STR_PAD_LEFT);
    }
}
