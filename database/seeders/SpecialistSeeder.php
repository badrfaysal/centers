<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Specialist;

class SpecialistSeeder extends Seeder
{
    public function run(): void
    {
        if (Specialist::count() == 0) {
            Specialist::create([
                'code' => 'SP-101',
                'name' => 'د. أحمد يسري',
                'specialization' => 'تخاطب ونطق واضطرابات كلام',
                'job_title' => 'أخصائي تخاطب ونطق أول',
                'phone' => '01011122233',
                'email' => 'ahmed.yousry@center.com',
                'license_number' => 'MED-LIC-8821',
                'qualification' => 'ماجستير أمراض التخاطب والتأهيل اللغوي - جامعة عين شمس',
                'experience_years' => 8,
                'default_room' => 'غرفة التخاطب 1',
                'work_days' => ['السبت', 'الإثنين', 'الأربعاء'],
                'salary_type' => 'per_session',
                'session_rate' => 150.00,
                'bio' => 'خبرة أكثر من 8 سنوات في علاج التلعثم واللدغات والتأهيل السمعي بعد زراعة القوقعة.',
                'status' => 'active'
            ]);

            Specialist::create([
                'code' => 'SP-102',
                'name' => 'د. مروة كمال',
                'specialization' => 'تكامل حسي وتعديل سلوك',
                'job_title' => 'استشاري تكامل حسي وتأهيل حركي',
                'phone' => '01022233344',
                'email' => 'marwa.kamal@center.com',
                'license_number' => 'MED-LIC-6542',
                'qualification' => 'دبلوم التكامل الحسي المعتمد وتعديل السلوك',
                'experience_years' => 6,
                'default_room' => 'غرفة التكامل الحسي Sensory Room',
                'work_days' => ['الأحد', 'الثلاثاء', 'الخميس'],
                'salary_type' => 'percentage',
                'session_rate' => 60.00,
                'bio' => 'متخصصة في التفريغ الحسي، فرط الحركة وتشتت الانتباه، وبرامج التكامل الحسي للأطفال.',
                'status' => 'active'
            ]);

            Specialist::create([
                'code' => 'SP-103',
                'name' => 'د. سارة إبراهيم',
                'specialization' => 'تأهيل تخاطب وضعف سمعي',
                'job_title' => 'أخصائي تخاطب وتأهيل سمعي',
                'phone' => '01033344455',
                'email' => 'sara.ibrahim@center.com',
                'license_number' => 'MED-LIC-9411',
                'qualification' => 'ليسانس علوم إعاقة وتأهيل لغوي',
                'experience_years' => 5,
                'default_room' => 'غرفة التخاطب 2',
                'work_days' => ['السبت', 'الأحد', 'الأربعاء'],
                'salary_type' => 'per_session',
                'session_rate' => 130.00,
                'bio' => 'خبرة واسعة في برامج التدريب السمعي اللفظي والتأهيل بعد زراعة القوقعة.',
                'status' => 'active'
            ]);

            Specialist::create([
                'code' => 'SP-104',
                'name' => 'أ. حسام فؤاد',
                'specialization' => 'صعوبات تعلم وتنمية مهارات',
                'job_title' => 'أخصائي تنمية مهارات ومقاييس ذكاء',
                'phone' => '01044455566',
                'email' => 'hossam.fouad@center.com',
                'license_number' => 'MED-LIC-3209',
                'qualification' => 'دبلوم تربية خاصة وتطبيق مقاييس الذكاء (ستانفورد بينيه)',
                'experience_years' => 7,
                'default_room' => 'غرفة تنمية المهارات وتعديل السلوك',
                'work_days' => ['الإثنين', 'الثلاثاء', 'الخميس'],
                'salary_type' => 'monthly',
                'session_rate' => 7000.00,
                'bio' => 'خبير تطبيق اختبارات ومقاييس الذكاء وتنمية المهارات الإدراكية للأطفال ذوي صعوبات التعلم.',
                'status' => 'active'
            ]);
        }
    }
}