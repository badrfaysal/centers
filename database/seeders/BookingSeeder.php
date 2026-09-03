<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ConsultationBooking;

class BookingSeeder extends Seeder
{
    public function run(): void
    {
        if (ConsultationBooking::count() == 0) {
            ConsultationBooking::create([
                'booking_code' => 'BK-2001',
                'parent_name' => 'أ. عبد الرحمن طارق',
                'phone' => '01012345678',
                'child_name' => 'يوسف عبد الرحمن',
                'child_age' => '4 سنوات و 6 أشهر',
                'service' => 'جلسة تقييم نطق وتخاطب أولي',
                'notes' => 'يعاني الطفل من تأخر في نطق الجمل والكلمات وتلعثم عند التحدث مع الغرباء، نرغب في تقييم شامل.',
                'status' => 'pending'
            ]);

            ConsultationBooking::create([
                'booking_code' => 'BK-2002',
                'parent_name' => 'د. نورهان الشناوي',
                'phone' => '01098765432',
                'child_name' => 'حمزة أحمد',
                'child_age' => '5 سنوات',
                'service' => 'تقييم تكامل حسي وتفريغ حركي',
                'notes' => 'فرط حركة وتشتت انتباه وعدم استقرار في الحضانة، نريد تقييم الحواس والتفريغ الحركي.',
                'status' => 'confirmed',
                'scheduled_at' => now()->addDays(2)->setHour(11)->setMinute(30),
                'specialist_name' => 'د. مروة كمال',
                'room' => 'غرفة التكامل الحسي Sensory Room',
                'admin_notes' => 'تم التواصل هاتفياً مع الأم وتأكيد الحضور صباح الأربعاء.',
                'confirmed_by' => 'إدارة المركز العامة',
                'confirmed_at' => now()->subHours(2)
            ]);
        }
    }
}