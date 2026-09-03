<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SessionSchedule;
use App\Models\Child;
use App\Models\Specialist;

class ScheduleSeeder extends Seeder
{
    public function run(): void
    {
        if (SessionSchedule::count() == 0) {
            $child1 = Child::first();
            $child2 = Child::skip(1)->first() ?? $child1;
            $child3 = Child::skip(2)->first() ?? $child1;

            $spec1 = Specialist::first();
            $spec2 = Specialist::skip(1)->first() ?? $spec1;

            if ($child1) {
                // موعد اليوم
                SessionSchedule::create([
                    'child_id' => $child1->id,
                    'specialist_id' => $spec1 ? $spec1->id : null,
                    'specialist_name' => $spec1 ? $spec1->name : 'د. أحمد يسري',
                    'session_title' => 'جلسة نطق وتخاطب فردي (مخارج الحروف)',
                    'session_date' => now()->toDateString(),
                    'start_time' => '10:00:00',
                    'end_time' => '10:45:00',
                    'room_name' => 'غرفة التخاطب 1',
                    'day_of_week' => 'الأحد',
                    'is_recurring' => true,
                    'status' => 'scheduled',
                    'attendance_status' => 'pending',
                    'notes' => 'تدريب على صوت حرف الراء واستخدام المرآة البصرية.'
                ]);

                // موعد بعد يومين
                SessionSchedule::create([
                    'child_id' => $child1->id,
                    'specialist_id' => $spec1 ? $spec1->id : null,
                    'specialist_name' => $spec1 ? $spec1->name : 'د. أحمد يسري',
                    'session_title' => 'جلسة تخاطب وتركيب جمل',
                    'session_date' => now()->addDays(2)->toDateString(),
                    'start_time' => '11:00:00',
                    'end_time' => '11:45:00',
                    'room_name' => 'غرفة التخاطب 1',
                    'day_of_week' => 'الثلاثاء',
                    'is_recurring' => true,
                    'status' => 'scheduled',
                    'attendance_status' => 'pending',
                    'notes' => 'متابعة الواجب المنزلي السابق والتعبير عن الصور.'
                ]);
            }

            if ($child2) {
                // موعد تكامل حسي
                SessionSchedule::create([
                    'child_id' => $child2->id,
                    'specialist_id' => $spec2 ? $spec2->id : null,
                    'specialist_name' => $spec2 ? $spec2->name : 'د. مروة كمال',
                    'session_title' => 'جلسة تكامل حسي وتفريغ طاقة',
                    'session_date' => now()->addDays(1)->toDateString(),
                    'start_time' => '12:00:00',
                    'end_time' => '12:45:00',
                    'room_name' => 'غرفة التكامل الحسي Sensory Room',
                    'day_of_week' => 'الإثنين',
                    'is_recurring' => true,
                    'status' => 'scheduled',
                    'attendance_status' => 'pending',
                    'notes' => 'تمارين الأرجوحة والكرات الحسية وتنظيم الحركة.'
                ]);
            }

            if ($child3) {
                // موعد تنمية مهارات
                SessionSchedule::create([
                    'child_id' => $child3->id,
                    'specialist_id' => $spec1 ? $spec1->id : null,
                    'specialist_name' => $spec1 ? $spec1->name : 'د. أحمد يسري',
                    'session_title' => 'جلسة تنمية مهارات إدراكية',
                    'session_date' => now()->addDays(3)->toDateString(),
                    'start_time' => '01:00:00',
                    'end_time' => '01:45:00',
                    'room_name' => 'غرفة تنمية المهارات وتعديل السلوك',
                    'day_of_week' => 'الأربعاء',
                    'is_recurring' => false,
                    'status' => 'scheduled',
                    'attendance_status' => 'pending',
                    'notes' => 'مطابقة الأشكال والألوان وزيادة مدى الانتباه.'
                ]);
            }
        }
    }
}