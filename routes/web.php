<?php

use App\Http\Controllers\AuthController;

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\ChildController;
use App\Http\Controllers\DoctorSessionController;
use App\Http\Controllers\ParentPortalController;
use App\Http\Controllers\AdminParentNoteController;
use App\Http\Controllers\SpecialistController;
use App\Http\Controllers\WebsiteController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\FinanceController;
use App\Http\Controllers\DebtController;
use App\Http\Controllers\ExpenseController;

// 1. الموقع الإلكتروني العام للمركز (Public Website & Landing Page)
Route::get('/site', [WebsiteController::class, 'index'])->name('website');
Route::post('/site/book', [WebsiteController::class, 'bookConsultation'])->name('website.book');

// 2. // Auth Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

Route::middleware('auth')->group(function () {
    // 2. لوحة التحكم الرئيسية لإدارة المركز
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // شاشة الانتظار للمركز
    Route::get('/center-screen', function () {
        return view('center-screen');
    })->name('center.screen');
    
    Route::get('/api/center-screen/notifications', function () {
        $notification = \Illuminate\Support\Facades\Cache::get('center_screen_notification');
        return response()->json($notification);
    })->name('api.center.screen');

    // 3. جدول وكالندر الجلسات العام وتوزيع الغرف (Master Timetable & Calendar)
    Route::get('/calendar', [ScheduleController::class, 'index'])->name('calendar.index');
    Route::get('/doctor-portal/timetable', [ScheduleController::class, 'specialistTimetable'])->name('doctor.timetable');
    Route::post('/doctor-portal/timetable/apologize-day', [ScheduleController::class, 'apologizeDay'])->name('doctor.timetable.apologize');
    Route::post('/schedules', [ScheduleController::class, 'store'])->name('schedules.store');
    Route::put('/schedules/{schedule}', [ScheduleController::class, 'update'])->name('schedules.update');
    Route::post('/schedules/{schedule}/attendance', [ScheduleController::class, 'updateAttendance'])->name('schedules.attendance');
    Route::delete('/schedules/{schedule}', [ScheduleController::class, 'destroy'])->name('schedules.destroy');

    // 4. إدارة وتأكيد طلبات الحجز والمواعيد (Bookings & Appointments Hub)
    Route::get('/bookings', [BookingController::class, 'index'])->name('bookings.index');
    Route::post('/bookings/{booking}/approve', [BookingController::class, 'approve'])->name('bookings.approve');
    Route::post('/bookings/{booking}/status', [BookingController::class, 'updateStatus'])->name('bookings.status');
    Route::delete('/bookings/{booking}', [BookingController::class, 'destroy'])->name('bookings.destroy');

    // 5. إدارة وتعديل وملفات الأطفال (360° Child Profile & Official Print)
    Route::get('/children', [ChildController::class, 'index'])->name('children.index');
    Route::get('/children/create', [ChildController::class, 'create'])->name('children.create');
    Route::post('/children', [ChildController::class, 'store'])->name('children.store');
    Route::get('/children/{child}', [ChildController::class, 'show'])->name('children.show');
    Route::get('/children/{child}/edit', [ChildController::class, 'edit'])->name('children.edit');
    Route::get('/children/{child}/print', [ChildController::class, 'print'])->name('children.print');
    Route::put('/children/{child}', [ChildController::class, 'update'])->name('children.update');

    // 6. إدارة وتسجيل الأخصائيين وفريق العمل (Specialists Management)
    Route::resource('specialists', SpecialistController::class);

    // 7. بوابة الأخصائي (تسجيل جلسات، الرد على تعليقات الفيديوهات، والرد على رسائل أولياء الأمور)
    Route::get('/doctor-portal', [DoctorSessionController::class, 'index'])->name('doctor.portal');
    Route::get('/doctor-portal/log', [DoctorSessionController::class, 'create'])->name('doctor.sessions.create');
    Route::post('/doctor-portal/log', [DoctorSessionController::class, 'store'])->name('doctor.sessions.store');
    Route::post('/doctor-portal/reply-comment', [DoctorSessionController::class, 'replyComment'])->name('doctor.comment.reply');
    Route::post('/doctor-portal/reply-message', [DoctorSessionController::class, 'replyMessage'])->name('doctor.message.reply');

    // 8. شاشة وملاحظات وشكاوى أولياء الأمور لإدارة المركز
    Route::get('/admin/parent-notes', [AdminParentNoteController::class, 'index'])->name('admin.parent-notes.index');
    Route::post('/admin/parent-notes/{message}/reply', [AdminParentNoteController::class, 'reply'])->name('admin.parent-notes.reply');
    Route::post('/admin/parent-notes/{message}/urgent', [AdminParentNoteController::class, 'toggleUrgent'])->name('admin.parent-notes.urgent');
    Route::delete('/admin/parent-notes/{message}', [AdminParentNoteController::class, 'destroy'])->name('admin.parent-notes.destroy');

    // 9. بوابة ولي الأمر التفاعلية ومتابعة الحجوزات والتقارير
    Route::get('/parent/my-bookings', [ParentPortalController::class, 'trackBookings'])->name('parent.bookings.track');
    Route::post('/parent/my-bookings', [ParentPortalController::class, 'trackBookings'])->name('parent.bookings.lookup');
    Route::get('/parent-portal/{code?}', [ParentPortalController::class, 'index'])->name('parent.portal');
    Route::post('/parent-portal/comment', [ParentPortalController::class, 'storeComment'])->name('parent.comment.store');
    Route::post('/parent-portal/message', [ParentPortalController::class, 'storeMessage'])->name('parent.message.store');
    Route::post('/parent-portal/rating', [ParentPortalController::class, 'storeRating'])->name('parent.rating.store');
    Route::post('/parent-portal/apologize/{sessionSchedule}', [ParentPortalController::class, 'apologizeSession'])->name('parent.session.apologize');

    // 10. إعدادات وهويّة المركز
    Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
    Route::post('/settings', [SettingController::class, 'update'])->name('settings.update');
    Route::post('/settings/session-prices', [SettingController::class, 'updateSessionPrices'])->name('settings.session-prices');

    // 11. نظام المالية والفواتير
    Route::get('/finances', [FinanceController::class, 'index'])->name('finances.index');
    Route::get('/finances/create', [FinanceController::class, 'create'])->name('finances.create');
    Route::post('/finances', [FinanceController::class, 'store'])->name('finances.store');
    Route::get('/finances/{invoice}', [FinanceController::class, 'show'])->name('finances.show');
    Route::get('/api/specialist-price/{specialist}', [FinanceController::class, 'getSpecialistPrice'])->name('api.specialist.price');
    Route::get('/api/urgent-notifications', [\App\Http\Controllers\DashboardController::class, 'getUrgentNotifications'])->name('api.urgent-notifications');
    Route::get('/api/new-bookings', [\App\Http\Controllers\BookingController::class, 'checkNewBookings'])->name('api.new-bookings');

    // 12. إدارة ديون أولياء الأمور
    Route::get('/debts', [DebtController::class, 'index'])->name('debts.index');
    Route::post('/debts/{invoice}/pay', [DebtController::class, 'payDebt'])->name('debts.pay');

    // 13. إدارة المصروفات
    Route::get('/expenses', [ExpenseController::class, 'index'])->name('expenses.index');
    Route::post('/expenses', [ExpenseController::class, 'store'])->name('expenses.store');
    Route::delete('/expenses/{expense}', [ExpenseController::class, 'destroy'])->name('expenses.destroy');

    // 14. التقارير والإحصائيات
    Route::get('/reports', [\App\Http\Controllers\ReportController::class, 'index'])->name('reports.index');

    // 15. صرف المرتبات
    Route::get('/payroll', [\App\Http\Controllers\PayrollController::class, 'index'])->name('payroll.index');
    Route::post('/payroll/pay', [\App\Http\Controllers\PayrollController::class, 'payAll'])->name('payroll.payAll');
    
    // الموظفين العاديين
    Route::post('/employees', [\App\Http\Controllers\EmployeeController::class, 'store'])->name('employees.store');
    Route::delete('/employees/{employee}', [\App\Http\Controllers\EmployeeController::class, 'destroy'])->name('employees.destroy');

    // 16. قائمة الانتظار
    Route::get('/waitlists', [\App\Http\Controllers\WaitlistController::class, 'index'])->name('waitlists.index');
    Route::post('/waitlists', [\App\Http\Controllers\WaitlistController::class, 'store'])->name('waitlists.store');
    Route::patch('/waitlists/{waitlist}/status', [\App\Http\Controllers\WaitlistController::class, 'updateStatus'])->name('waitlists.updateStatus');

    // مكتبة الملفات والفيديوهات
    Route::get('/media', [\App\Http\Controllers\MediaController::class, 'index'])->name('media.index');
    Route::post('/media', [\App\Http\Controllers\MediaController::class, 'store'])->name('media.store');
    Route::post('/media/{id}/comment', [\App\Http\Controllers\MediaController::class, 'comment'])->name('media.comment');
    Route::delete('/media/{id}', [\App\Http\Controllers\MediaController::class, 'destroy'])->name('media.destroy');
    // تسجيل الحضور بالـ QR الجديد (شاشة المركز ومسح ولي الأمر)
    Route::get('/attendance/screen', [\App\Http\Controllers\AttendanceController::class, 'centerScreen'])->name('attendance.screen');
    
    // (الراوت القديم لمسح الاستقبال - سنبقيه للاحتياط)
    Route::get('/attendance/scanner', [\App\Http\Controllers\AttendanceController::class, 'scanner'])->name('attendance.scanner');
    Route::get('/attendance/scan/{code}', [\App\Http\Controllers\AttendanceController::class, 'scan'])->name('attendance.scan');

    // الواجب المنزلي
    Route::post('/sessions/{session}/homework-complete', [\App\Http\Controllers\DoctorSessionController::class, 'completeHomework'])->name('homework.complete');
});

// مسار تسجيل الحضور للآباء (مفتوح للآباء بدون تسجيل دخول إجباري إذا كان لديهم كود الطفل)
Route::get('/attendance/check-in', [\App\Http\Controllers\AttendanceController::class, 'parentCheckIn'])->name('attendance.parent_checkin');

Route::get('/api/tts', function (\Illuminate\Http\Request $request) {
    $text = $request->get('text');
    $url = "https://translate.google.com/translate_tts?ie=UTF-8&tl=ar&client=tw-ob&q=" . urlencode($text);
    
    $opts = [
        "http" => [
            "method" => "GET",
            "header" => "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36\r\n"
        ]
    ];
    $context = stream_context_create($opts);
    $audio = @file_get_contents($url, false, $context);
    
    if ($audio) {
        return response($audio)->header('Content-Type', 'audio/mpeg');
    }
    return response('Error', 500);
})->name('api.tts');
