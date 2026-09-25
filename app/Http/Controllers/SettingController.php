<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use App\Models\Specialist;

class SettingController extends Controller
{
    protected string $settingsPath;

    public function __construct()
    {
        $this->settingsPath = storage_path('app/settings.json');
    }

    /**
     * Get system settings with default fallbacks.
     */
    public static function getSettings(): array
    {
        $path = storage_path('app/settings.json');
        if (File::exists($path)) {
            $data = json_decode(File::get($path), true);
            if (is_array($data)) {
                return array_merge(self::defaultSettings(), $data);
            }
        }
        return self::defaultSettings();
    }

    /**
     * Default white-label settings.
     */
    public static function defaultSettings(): array
    {
        return [
            'center_name'         => 'مركز الأمل للتخاطب والتأهيل',
            'center_slogan'       => 'للتخاطب وتنمية المهارات والتأهيل الشامل',
            'primary_color'       => '#0d9488', // Teal
            'secondary_color'     => '#6366f1', // Indigo
            'accent_color'        => '#f59e0b', // Amber
            'phone'               => '01012345678',
            'whatsapp'            => '01012345678',
            'email'               => 'contact@al-amal.com',
            'address'             => 'القاهرة - مدينة نصر - شارع عباس العقاد',
            'currency'            => 'ج.م',
            'default_session_min' => '45',
            'logo_icon'           => 'fa-brain',
            'hero_image'          => 'https://images.unsplash.com/photo-1576765608535-5f04d1e3f289?auto=format&fit=crop&w=1200&q=80',
            'gallery_images'      => [
                'https://images.unsplash.com/photo-1588072432836-e10032774350?auto=format&fit=crop&w=800&q=80',
                'https://images.unsplash.com/photo-1596495578065-6e0763fa1178?auto=format&fit=crop&w=800&q=80',
                'https://images.unsplash.com/photo-1503454537195-1dcabb73ffb9?auto=format&fit=crop&w=800&q=80'
            ],
            'whatsapp_auto_send'  => true,
            'whatsapp_template'   => "مرحباً {ولي_الأمر} \nنود تذكيركم بموعد جلسة البطل ({الطفل}) غداً في {اسم_المركز} \n الموعد: {الموعد}\n الأخصائي: {الأخصائي}\n الغرفة: {الغرفة}\nنتمنى لكم دوام الصحة والعافية!",
        ];
    }

    public static function getDropdownList($key): array
    {
        $settings = self::getSettings();
        if (isset($settings['dropdown_lists'][$key]) && !empty($settings['dropdown_lists'][$key])) {
            return $settings['dropdown_lists'][$key];
        }
        
        $defaults = [
            'rooms' => [
                'غرفة التخاطب 1',
                'غرفة التخاطب 2',
                'غرفة التكامل الحسي',
                'غرفة التدريبات الحركية',
                'غرفة اختبارات الذكاء والمقاييس',
            ],
            'session_types' => [
                'جلسة تخاطب نطق وكلام',
                'جلسة تكامل حسي',
                'جلسة تنمية مهارات',
                'جلسة تعديل سلوك',
                'جلسة مقاييس واختبارات ذكاء',
            ],
            'specializations' => [
                'أخصائي تخاطب ونطق',
                'أخصائي تنمية مهارات',
                'أخصائي تكامل حسي',
                'أخصائي صعوبات تعلم',
                'أخصائي نفسي',
                'أخصائي علاج طبيعي',
            ],
            'services' => [
                'تخاطب ونطق',
                'تنمية مهارات',
                'تكامل حسي',
                'تعديل سلوك',
                'اختبارات ومقاييس ذكاء',
            ],
            'expense_categories' => [
                'إيجار المركز',
                'فواتير (كهرباء / إنترنت / مياه)',
                'رواتب موظفين وعمال',
                'أدوات ومستلزمات جلسات',
                'نظافة وصيانة',
                'دعاية وتسويق',
                'أخرى',
            ],
            'diagnoses' => [
                'تأخر نمو لغوي ونطق (لدغات / تلعثم)',
                'طيف توحد (ASD) وتواصل اجتماعي',
                'فرط حركة وتشتت انتباه (ADHD)',
                'ضعف سمعي / زراعة قوقعة إلكترونية',
                'متلازمة داون وتأهيل شامل',
                'صعوبات تعلم وعسر قراءة (Dyslexia)',
                'تعديل سلوك وعناد واضطرابات انفعالية',
                'تشخيص أو تقييم أولي آخر',
            ],
        ];
        
        return $defaults[$key] ?? [];
    }


    /**
     * Show settings edit screen.
     */
    public function index()
    {
        $settings = self::getSettings();
        $specialists = Specialist::where('status', 'active')->orderBy('name')->get();
        return view('settings.index', compact('settings', 'specialists'));
    }

    /**
     * Update settings and persist to JSON storage.
     */
    public function update(Request $request)
    {
        $currentSettings = self::getSettings();

        $validated = $request->validate([
            'center_name'         => 'required|string|max:100',
            'center_slogan'       => 'nullable|string|max:150',
            'primary_color'       => 'required|string|regex:/^#[a-fA-F0-9]{6}$/',
            'secondary_color'     => 'required|string|regex:/^#[a-fA-F0-9]{6}$/',
            'accent_color'        => 'nullable|string|regex:/^#[a-fA-F0-9]{6}$/',
            'phone'               => 'nullable|string|max:30',
            'whatsapp'            => 'nullable|string|max:30',
            'email'               => 'nullable|email|max:100',
            'address'             => 'nullable|string|max:255',
            'currency'            => 'required|string|max:15',
            'default_session_min' => 'required|numeric',
            'logo_icon'           => 'nullable|string',
            'hero_image_url'      => 'nullable|string|max:500',
            'hero_image'          => 'nullable|image|mimes:jpeg,png,jpg,webp|max:10240',
            'gallery_photos.*'    => 'nullable|image|mimes:jpeg,png,jpg,webp|max:10240',
            'whatsapp_auto_send'  => 'nullable|boolean',
            'whatsapp_template'   => 'nullable|string',
            'dropdown_rooms'           => 'nullable|string',
            'dropdown_session_types'   => 'nullable|string',
            'dropdown_specializations' => 'nullable|string',
            'dropdown_services'        => 'nullable|string',
            'dropdown_expense_categories'=> 'nullable|string',
            'dropdown_diagnoses'       => 'nullable|string',
        ]);

        $validated['whatsapp_auto_send'] = $request->has('whatsapp_auto_send');

        $dropdown_lists = $currentSettings['dropdown_lists'] ?? [];
        if ($request->has('dropdown_rooms')) {
            $dropdown_lists['rooms'] = array_filter(array_map('trim', explode("\n", $request->dropdown_rooms)));
        }
        if ($request->has('dropdown_session_types')) {
            $dropdown_lists['session_types'] = array_filter(array_map('trim', explode("\n", $request->dropdown_session_types)));
        }
        if ($request->has('dropdown_specializations')) {
            $dropdown_lists['specializations'] = array_filter(array_map('trim', explode("\n", $request->dropdown_specializations)));
        }
        if ($request->has('dropdown_services')) {
            $dropdown_lists['services'] = array_filter(array_map('trim', explode("\n", $request->dropdown_services)));
        }
        if ($request->has('dropdown_expense_categories')) {
            $dropdown_lists['expense_categories'] = array_filter(array_map('trim', explode("\n", $request->dropdown_expense_categories)));
        }
        if ($request->has('dropdown_diagnoses')) {
            $dropdown_lists['diagnoses'] = array_filter(array_map('trim', explode("\n", $request->dropdown_diagnoses)));
        }
        $validated['dropdown_lists'] = $dropdown_lists;
        unset($validated['dropdown_rooms'], $validated['dropdown_session_types'], $validated['dropdown_specializations'], $validated['dropdown_services'], $validated['dropdown_expense_categories'], $validated['dropdown_diagnoses']);


        // رفع صورة الواجهة الرئيسية للمركز (Hero Image) باستخدام Storage public disk
        if ($request->hasFile('hero_image')) {
            $path = $request->file('hero_image')->store('center_assets', 'public');
            $validated['hero_image'] = asset('storage/' . $path);
        } elseif ($request->filled('hero_image_url')) {
            $validated['hero_image'] = $request->hero_image_url;
        } else {
            $validated['hero_image'] = $currentSettings['hero_image'] ?? self::defaultSettings()['hero_image'];
        }

        // رفع صور معرض المركز الإضافية
        $gallery = $currentSettings['gallery_images'] ?? [];
        if ($request->hasFile('gallery_photos')) {
            foreach ($request->file('gallery_photos') as $photoFile) {
                $gPath = $photoFile->store('center_assets', 'public');
                $gallery[] = asset('storage/' . $gPath);
            }
        }
        $validated['gallery_images'] = $gallery;

        unset($validated['hero_image_url'], $validated['gallery_photos']);

        File::put($this->settingsPath, json_encode($validated, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        Cache::forget('app_center_settings');

        return redirect()->route('settings.index')->with('success', 'تم حفظ وتطبيق إعدادات وصور المركز بنجاح وستظهر فوراً في الموقع الإلكتروني والسايد بار!');
    }

    /**
     * تحديث أسعار جلسات الأخصائيين دفعة واحدة.
     */
    public function updateSessionPrices(Request $request)
    {
        $request->validate([
            'prices'   => 'required|array',
            'prices.*' => 'required|numeric|min:0',
        ]);

        foreach ($request->prices as $specialistId => $price) {
            Specialist::where('id', $specialistId)->update(['session_rate' => $price]);
        }

        return redirect()->route('settings.index')->with('success', 'تم تحديث أسعار جلسات الأخصائيين بنجاح!');
    }
}