@extends('layouts.app')

@section('title', 'إدارة مستخدمي النظام')

@section('content')
<div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
        <h1 class="text-2xl font-black text-slate-800 flex items-center gap-2">
            <i class="fa-solid fa-users-gear text-teal-600"></i> إدارة مستخدمي النظام
        </h1>
        <p class="text-slate-500 text-sm mt-1 font-medium">إضافة وحذف المستخدمين وتحديد صلاحياتهم في النظام</p>
    </div>
    <button onclick="document.getElementById('addUserModal').classList.remove('hidden')" class="px-5 py-2.5 bg-teal-600 text-white rounded-xl font-bold hover:bg-teal-700 transition flex items-center gap-2 shadow-lg shadow-teal-600/20">
        <i class="fa-solid fa-plus"></i> إضافة مستخدم جديد
    </button>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-right">
            <thead class="bg-slate-50 text-slate-600 font-bold border-b border-slate-200">
                <tr>
                    <th class="px-6 py-4">الاسم</th>
                    <th class="px-6 py-4">اسم المستخدم</th>
                    <th class="px-6 py-4">رقم الجوال</th>
                    <th class="px-6 py-4">الصلاحية</th>
                    <th class="px-6 py-4 text-center">الإجراءات</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-slate-700 font-medium">
                @forelse($users as $user)
                    <tr class="hover:bg-slate-50/50 transition">
                        <td class="px-6 py-4 font-bold">{{ $user->name }}</td>
                        <td class="px-6 py-4">{{ $user->username }}</td>
                        <td class="px-6 py-4">{{ $user->phone ?? '—' }}</td>
                        <td class="px-6 py-4">
                            @if($user->role === 'admin')
                                <span class="px-2.5 py-1 rounded-lg text-xs font-bold bg-purple-100 text-purple-700">مدير عام</span>
                            @elseif($user->role === 'user')
                                <span class="px-2.5 py-1 rounded-lg text-xs font-bold bg-blue-100 text-blue-700">مستخدم عادي</span>
                            @elseif($user->role === 'viewer')
                                <span class="px-2.5 py-1 rounded-lg text-xs font-bold bg-slate-100 text-slate-700">مشاهد فقط</span>
                            @else
                                <span class="px-2.5 py-1 rounded-lg text-xs font-bold bg-gray-100 text-gray-700">{{ $user->role }}</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center">
                            <button onclick="openEditModal({{ $user->toJson() }})" class="w-8 h-8 rounded-lg bg-slate-100 text-slate-500 hover:bg-teal-50 hover:text-teal-600 transition" title="تعديل">
                                <i class="fa-solid fa-pen"></i>
                            </button>
                            @if($user->id !== auth()->id())
                            <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="inline-block" onsubmit="return confirm('هل أنت متأكد من حذف هذا المستخدم؟');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-8 h-8 rounded-lg bg-slate-100 text-slate-500 hover:bg-rose-50 hover:text-rose-600 transition" title="حذف">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-slate-500">لا يوجد مستخدمين مسجلين بعد</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Add User Modal -->
<div id="addUserModal" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm hidden items-center justify-center z-50 flex">
    <div class="bg-white w-full max-w-lg rounded-3xl p-6 shadow-2xl relative">
        <button onclick="document.getElementById('addUserModal').classList.add('hidden')" class="absolute top-6 left-6 text-slate-400 hover:text-rose-500 transition">
            <i class="fa-solid fa-xmark text-xl"></i>
        </button>
        
        <h2 class="text-xl font-black text-slate-800 mb-6 flex items-center gap-2">
            <i class="fa-solid fa-user-plus text-teal-600"></i> إضافة مستخدم جديد
        </h2>

        <form action="{{ route('users.store') }}" method="POST" class="space-y-4 text-sm font-medium">
            @csrf
            <div>
                <label class="block font-bold text-slate-700 mb-1.5">الاسم الكامل <span class="text-rose-500">*</span></label>
                <input type="text" name="name" required class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 outline-none">
            </div>
            
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block font-bold text-slate-700 mb-1.5">اسم المستخدم (للدخول) <span class="text-rose-500">*</span></label>
                    <input type="text" name="username" required dir="ltr" class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 outline-none">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1.5">كلمة المرور <span class="text-rose-500">*</span></label>
                    <input type="password" name="password" required dir="ltr" class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 outline-none">
                </div>
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1.5">رقم الجوال</label>
                <input type="text" name="phone" dir="ltr" class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 outline-none">
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1.5">الصلاحية <span class="text-rose-500">*</span></label>
                <select name="role" required class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 outline-none">
                    <option value="admin">مدير عام (وصول كامل)</option>
                    <option value="user" selected>مستخدم عادي (لا يرى التقارير أو السجل)</option>
                    <option value="viewer">مشاهد فقط (يرى التقارير فقط)</option>
                </select>
            </div>

            <button type="submit" class="w-full py-3.5 mt-2 bg-teal-600 text-white rounded-xl font-black hover:bg-teal-700 transition shadow-lg shadow-teal-600/30">
                حفظ المستخدم
            </button>
        </form>
    </div>
</div>

<!-- Edit User Modal -->
<div id="editUserModal" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm hidden items-center justify-center z-50 flex">
    <div class="bg-white w-full max-w-lg rounded-3xl p-6 shadow-2xl relative">
        <button onclick="document.getElementById('editUserModal').classList.add('hidden')" class="absolute top-6 left-6 text-slate-400 hover:text-rose-500 transition">
            <i class="fa-solid fa-xmark text-xl"></i>
        </button>
        
        <h2 class="text-xl font-black text-slate-800 mb-6 flex items-center gap-2">
            <i class="fa-solid fa-pen text-teal-600"></i> تعديل مستخدم
        </h2>

        <form id="editUserForm" method="POST" class="space-y-4 text-sm font-medium">
            @csrf
            @method('PUT')
            
            <div>
                <label class="block font-bold text-slate-700 mb-1.5">الاسم الكامل <span class="text-rose-500">*</span></label>
                <input type="text" name="name" id="edit_name" required class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 outline-none">
            </div>
            
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block font-bold text-slate-700 mb-1.5">اسم المستخدم <span class="text-rose-500">*</span></label>
                    <input type="text" name="username" id="edit_username" required dir="ltr" class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 outline-none">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1.5">تغيير كلمة المرور</label>
                    <input type="password" name="password" dir="ltr" placeholder="أتركه فارغاً إذا لم ترد التغيير" class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 outline-none">
                </div>
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1.5">رقم الجوال</label>
                <input type="text" name="phone" id="edit_phone" dir="ltr" class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 outline-none">
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1.5">الصلاحية <span class="text-rose-500">*</span></label>
                <select name="role" id="edit_role" required class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 outline-none">
                    <option value="admin">مدير عام (وصول كامل)</option>
                    <option value="user">مستخدم عادي (لا يرى التقارير أو السجل)</option>
                    <option value="viewer">مشاهد فقط (يرى التقارير فقط)</option>
                </select>
            </div>

            <button type="submit" class="w-full py-3.5 mt-2 bg-teal-600 text-white rounded-xl font-black hover:bg-teal-700 transition shadow-lg shadow-teal-600/30">
                حفظ التعديلات
            </button>
        </form>
    </div>
</div>

<script>
    function openEditModal(user) {
        document.getElementById('editUserForm').action = `/users/${user.id}`;
        document.getElementById('edit_name').value = user.name;
        document.getElementById('edit_username').value = user.username;
        document.getElementById('edit_phone').value = user.phone || '';
        document.getElementById('edit_role').value = user.role;
        document.getElementById('editUserModal').classList.remove('hidden');
    }
</script>
@endsection
