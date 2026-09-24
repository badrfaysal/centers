@extends("layouts.app")

@section("title", "سجل أنشطة النظام")

@section("content")
<div class="space-y-6" x-data="{ showModal: false, selectedLog: null }">

    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-2xl bg-teal-50 text-teal-600 flex items-center justify-center text-xl shadow-xs">
                <i class="fa-solid fa-list-check"></i>
            </div>
            <div>
                <h2 class="text-xl font-black text-slate-800">سجل أنشطة النظام (Audit Logs)</h2>
                <p class="text-xs text-slate-500 font-semibold mt-0.5">مراقبة وتسجيل كافة الإجراءات والتعديلات وحركات الدخول</p>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white p-4 rounded-3xl border border-slate-100 shadow-sm">
        <form action="{{ route('logs.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-[10px] font-bold text-slate-500 mb-1">المستخدم</label>
                <select name="user_id" class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold outline-none">
                    <option value="">الكل</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ request("user_id") == $user->id ? "selected" : "" }}>{{ $user->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-[10px] font-bold text-slate-500 mb-1">نوع الإجراء</label>
                <select name="action" class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold outline-none">
                    <option value="">الكل</option>
                    <option value="login" {{ request("action") == "login" ? "selected" : "" }}>تسجيل دخول</option>
                    <option value="logout" {{ request("action") == "logout" ? "selected" : "" }}>تسجيل خروج</option>
                    <option value="created" {{ request("action") == "created" ? "selected" : "" }}>إضافة (إنشاء)</option>
                    <option value="updated" {{ request("action") == "updated" ? "selected" : "" }}>تعديل</option>
                    <option value="deleted" {{ request("action") == "deleted" ? "selected" : "" }}>حذف</option>
                </select>
            </div>
            <div>
                <label class="block text-[10px] font-bold text-slate-500 mb-1">تاريخ الحركة</label>
                <input type="date" name="date" value="{{ request("date") }}" class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold outline-none">
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 p-2.5 bg-teal-600 hover:bg-teal-700 text-white rounded-xl text-xs font-bold transition flex items-center justify-center gap-1.5">
                    <i class="fa-solid fa-magnifying-glass"></i> تصفية
                </button>
                <a href="{{ route('logs.index') }}" class="p-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl text-xs font-bold transition flex items-center justify-center">
                    إلغاء
                </a>
            </div>
        </form>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-right text-xs">
                <thead class="bg-slate-50 text-slate-500 font-bold border-b border-slate-100">
                    <tr>
                        <th class="p-4">الوقت والتاريخ</th>
                        <th class="p-4">المستخدم</th>
                        <th class="p-4">الإجراء</th>
                        <th class="p-4 w-1/3">ملخص الحركة</th>
                        <th class="p-4">IP Address</th>
                        <th class="p-4">التفاصيل</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($logs as $log)
                    <tr class="hover:bg-slate-50/50 transition border-b border-slate-50">
                        <td class="p-4 font-mono text-slate-500" dir="ltr">{{ $log->created_at->format("Y-m-d H:i") }}</td>
                        <td class="p-4">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center shrink-0">
                                    <i class="fa-solid fa-user text-slate-400"></i>
                                </div>
                                <span class="font-bold text-slate-700">{{ $log->user ? $log->user->name : "النظام" }}</span>
                            </div>
                        </td>
                        <td class="p-4">
                            @if($log->action === "login")
                                <span class="bg-blue-50 text-blue-700 px-2.5 py-1 rounded-md font-bold text-[10px]">دخول</span>
                            @elseif($log->action === "logout")
                                <span class="bg-slate-100 text-slate-700 px-2.5 py-1 rounded-md font-bold text-[10px]">خروج</span>
                            @elseif($log->action === "created")
                                <span class="bg-emerald-50 text-emerald-700 px-2.5 py-1 rounded-md font-bold text-[10px]">إضافة</span>
                            @elseif($log->action === "updated")
                                <span class="bg-amber-50 text-amber-700 px-2.5 py-1 rounded-md font-bold text-[10px]">تعديل</span>
                            @elseif($log->action === "deleted")
                                <span class="bg-rose-50 text-rose-700 px-2.5 py-1 rounded-md font-bold text-[10px]">حذف</span>
                            @else
                                <span class="bg-slate-50 text-slate-700 px-2.5 py-1 rounded-md font-bold text-[10px]">{{ $log->action }}</span>
                            @endif
                        </td>
                        <td class="p-4 font-bold text-slate-700 leading-relaxed">
                            {{ $log->getHumanActionSummary() }}
                            @if($log->action === 'updated' && count($log->getHumanChanges()) > 0)
                                <div class="mt-2 space-y-1 text-[11px] text-slate-500 font-normal">
                                    @foreach($log->getHumanChanges() as $change)
                                        <div class="bg-slate-50 px-2 py-1 rounded border border-slate-100">{!! $change !!}</div>
                                    @endforeach
                                </div>
                            @endif
                        </td>
                        <td class="p-4 font-mono text-slate-400 text-[10px]">{{ $log->ip_address }}</td>
                        <td class="p-4">
                            @if($log->old_values || $log->new_values)
                                <button type="button" @click="selectedLog = {{ json_encode([
                                    'action' => $log->action,
                                    'model' => class_basename($log->model_type),
                                    'summary' => $log->getHumanActionSummary(),
                                    'humanChanges' => $log->getHumanChanges(),
                                    'old' => $log->old_values,
                                    'new' => $log->new_values
                                ]) }}; showModal = true" class="text-teal-600 hover:text-teal-800 font-bold bg-teal-50 px-3 py-1.5 rounded-lg flex items-center gap-1 text-[11px]">
                                    <i class="fa-regular fa-eye"></i> التفاصيل
                                </button>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="p-8 text-center text-slate-400 font-bold">لا توجد سجلات مطابقة للبحث</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-slate-100">
            {{ $logs->links() }}
        </div>
    </div>

    <!-- Modal for Before/After Details -->
    <div x-show="showModal" x-cloak class="fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-sm flex items-center justify-center p-4">
        <div @click.away="showModal = false" class="bg-white rounded-3xl w-full max-w-2xl max-h-[90vh] flex flex-col shadow-2xl">
            <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                <h3 class="font-black text-slate-800 flex items-center gap-2">
                    <i class="fa-solid fa-code-compare text-teal-600"></i>
                    تفاصيل الحركة (<span x-text="selectedLog?.model"></span>)
                </h3>
                <button @click="showModal = false" class="text-slate-400 hover:text-slate-700 w-8 h-8 flex items-center justify-center rounded-xl bg-slate-50 hover:bg-slate-100 transition">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
            
            <div class="p-5 overflow-y-auto flex-1 text-xs">
                
                <div x-show="selectedLog?.action === 'created'" class="space-y-3">
                    <h4 class="font-bold text-emerald-700 bg-emerald-50 px-3 py-2 rounded-lg">البيانات الجديدة المضافة:</h4>
                    <pre class="bg-slate-50 p-4 rounded-xl border border-slate-200 text-left font-mono" dir="ltr" x-text="JSON.stringify(selectedLog?.new, null, 2)"></pre>
                </div>

                <div x-show="selectedLog?.action === 'deleted'" class="space-y-3">
                    <h4 class="font-bold text-rose-700 bg-rose-50 px-3 py-2 rounded-lg">البيانات المحذوفة:</h4>
                    <pre class="bg-slate-50 p-4 rounded-xl border border-slate-200 text-left font-mono" dir="ltr" x-text="JSON.stringify(selectedLog?.old, null, 2)"></pre>
                </div>

                <div x-show="selectedLog?.action === 'updated'" class="space-y-4">
                    
                    <div x-show="selectedLog?.humanChanges?.length > 0" class="bg-emerald-50 border border-emerald-100 rounded-2xl p-4">
                        <h4 class="font-black text-emerald-800 mb-3 text-sm flex items-center gap-2">
                            <i class="fa-solid fa-list-check"></i> ملخص التعديلات:
                        </h4>
                        <ul class="space-y-2">
                            <template x-for="change in selectedLog?.humanChanges">
                                <li class="text-[12px] text-emerald-700 bg-white px-3 py-2 rounded-lg shadow-sm border border-emerald-100" x-html="change"></li>
                            </template>
                        </ul>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-3">
                            <h4 class="font-bold text-slate-700 bg-slate-100 px-3 py-2 rounded-lg flex items-center justify-between">
                                البيانات الفنية القديمة
                                <i class="fa-solid fa-code"></i>
                            </h4>
                            <pre class="bg-slate-50 p-4 rounded-xl border border-slate-200 text-left font-mono text-[10px] overflow-x-auto text-slate-500" dir="ltr" x-text="JSON.stringify(selectedLog?.old, null, 2)"></pre>
                        </div>
                        <div class="space-y-3">
                            <h4 class="font-bold text-slate-700 bg-slate-100 px-3 py-2 rounded-lg flex items-center justify-between">
                                البيانات الفنية الجديدة
                                <i class="fa-solid fa-code"></i>
                            </h4>
                            <pre class="bg-slate-50 p-4 rounded-xl border border-slate-200 text-left font-mono text-[10px] overflow-x-auto text-slate-500" dir="ltr" x-text="JSON.stringify(selectedLog?.new, null, 2)"></pre>
                        </div>
                    </div>
                </div>

            </div>
            
            <div class="p-4 border-t border-slate-100 text-left">
                <button @click="showModal = false" class="px-5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl text-xs transition">إغلاق</button>
            </div>
        </div>
    </div>
</div>
@endsection

