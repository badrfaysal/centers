@extends('layouts.app')
@section('title', 'مكتبة الملفات والفيديوهات')
@section('content')
<div class="space-y-6" x-data="{ uploadModal: false }">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white p-6 rounded-3xl border border-slate-100 shadow-sm">
        <div>
            <h1 class="font-black text-xl text-slate-800 flex items-center gap-3">
                <i class="fa-solid fa-photo-film text-purple-600"></i>
                مكتبة الملفات والفيديوهات
            </h1>
            <p class="text-xs text-slate-400 font-semibold mt-1">مساحة لرفع الفيديوهات التعليمية، التمارين، والملفات الخاصة بكل طفل.</p>
        </div>
        
        <button @click="uploadModal = true" class="px-5 py-2.5 bg-purple-600 text-white rounded-2xl text-xs font-bold shadow-md hover:bg-purple-700 transition flex items-center gap-2">
            <i class="fa-solid fa-cloud-arrow-up"></i>
            <span>رفع ملف جديد</span>
        </button>
    </div>

    @if(session('success'))
    <div class="p-4 bg-emerald-50 text-emerald-700 border border-emerald-200 rounded-2xl text-xs font-bold">
        <i class="fa-solid fa-check-circle mr-1"></i> {{ session('success') }}
    </div>
    @endif
    @if($errors->any())
    <div class="p-4 bg-rose-50 text-rose-700 border border-rose-200 rounded-2xl text-xs font-bold">
        <ul>
            @foreach($errors->all() as $error)
                <li>- {{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <!-- Media Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
        @forelse($media as $item)
        <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden flex flex-col h-full" x-data="{ commentOpen: false }">
            
            <!-- Media Preview -->
            <div class="h-48 bg-slate-900 relative flex items-center justify-center overflow-hidden">
                @if($item->file_type === 'video')
                    <video src="{{ asset('storage/' . $item->file_path) }}" controls class="w-full h-full object-cover"></video>
                @elseif($item->file_type === 'image')
                    <img src="{{ asset('storage/' . $item->file_path) }}" class="w-full h-full object-cover">
                @else
                    <i class="fa-solid fa-file-pdf text-6xl text-slate-700"></i>
                @endif
                
                <div class="absolute top-3 left-3 bg-white/90 px-2 py-1 rounded-lg text-[10px] font-black text-slate-800 shadow-sm backdrop-blur-md">
                    {{ $item->file_size }}
                </div>
                
                @if(Auth::id() === $item->uploaded_by || Auth::user()->role === 'admin')
                <form action="{{ route('media.destroy', $item->id) }}" method="POST" class="absolute top-3 right-3" onsubmit="return confirm('هل أنت متأكد من الحذف؟');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="w-8 h-8 bg-rose-500 text-white rounded-full flex items-center justify-center hover:bg-rose-600 transition shadow-sm">
                        <i class="fa-solid fa-trash text-xs"></i>
                    </button>
                </form>
                @endif
            </div>

            <!-- Details -->
            <div class="p-5 flex-1 flex flex-col">
                <h3 class="font-black text-sm text-slate-800 line-clamp-1">{{ $item->title }}</h3>
                <div class="flex items-center gap-2 mt-2 text-xs text-slate-500 font-medium">
                    <i class="fa-solid fa-child text-teal-600"></i>
                    <span>الطفل: <strong>{{ $item->child->name ?? 'غير محدد' }}</strong></span>
                </div>
                <div class="flex items-center gap-2 mt-1 text-[10px] text-slate-400 font-medium">
                    <i class="fa-solid fa-user-pen"></i>
                    <span>بواسطة: {{ $item->uploader->name ?? '' }}</span>
                    <span class="mx-1">•</span>
                    <span>{{ $item->created_at->diffForHumans() }}</span>
                </div>

                @if($item->description)
                <p class="text-xs text-slate-600 font-medium mt-3 bg-slate-50 p-3 rounded-2xl line-clamp-2">
                    {{ $item->description }}
                </p>
                @endif

                <div class="mt-auto pt-4 flex gap-2">
                    <button @click="commentOpen = !commentOpen" class="flex-1 px-4 py-2 bg-slate-100 text-slate-700 rounded-xl text-xs font-bold hover:bg-slate-200 transition flex items-center justify-center gap-2">
                        <i class="fa-regular fa-comment-dots"></i>
                        <span>التعليقات ({{ $item->comments->count() }})</span>
                    </button>
                    @if($item->file_type === 'document')
                    <a href="{{ asset('storage/' . $item->file_path) }}" target="_blank" class="px-4 py-2 bg-indigo-50 text-indigo-700 rounded-xl text-xs font-bold hover:bg-indigo-100 transition flex items-center justify-center gap-2">
                        <i class="fa-solid fa-download"></i>
                    </a>
                    @endif
                </div>
            </div>

            <!-- Comments Section (Slide Down) -->
            <div x-show="commentOpen" x-collapse class="border-t border-slate-100 bg-slate-50">
                <div class="p-5 space-y-4">
                    <!-- Comments List -->
                    <div class="space-y-3 max-h-48 overflow-y-auto custom-scrollbar pr-1">
                        @forelse($item->comments as $comment)
                        <div class="bg-white p-3 rounded-2xl shadow-sm border border-slate-100">
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-[10px] font-black {{ $comment->user->role === 'parent' ? 'text-purple-600' : 'text-blue-600' }}">
                                    {{ $comment->user->name }}
                                </span>
                                <span class="text-[9px] text-slate-400">{{ $comment->created_at->diffForHumans() }}</span>
                            </div>
                            <p class="text-xs text-slate-700 font-medium leading-relaxed">{{ $comment->comment }}</p>
                        </div>
                        @empty
                        <p class="text-[11px] text-center text-slate-400 font-semibold py-2">لا توجد تعليقات بعد.</p>
                        @endforelse
                    </div>

                    <!-- Add Comment -->
                    <form action="{{ route('media.comment', $item->id) }}" method="POST" class="flex gap-2 relative">
                        @csrf
                        <input type="text" name="comment" required placeholder="اكتب تعليقاً..." class="w-full pl-10 pr-3 py-2 bg-white border border-slate-200 rounded-xl text-xs outline-none focus:border-purple-300">
                        <button type="submit" class="absolute left-1 top-1 bottom-1 w-8 bg-purple-100 text-purple-700 rounded-lg hover:bg-purple-200 transition flex items-center justify-center">
                            <i class="fa-solid fa-paper-plane text-xs"></i>
                        </button>
                    </form>
                </div>
            </div>

        </div>
        @empty
        <div class="col-span-full py-16 text-center bg-white rounded-3xl border border-dashed border-slate-200">
            <i class="fa-solid fa-photo-film text-4xl text-slate-300 mb-3"></i>
            <h3 class="font-bold text-slate-500">لا توجد ملفات أو فيديوهات مرفوعة حتى الآن</h3>
        </div>
        @endforelse
    </div>

    <!-- Upload Modal -->
    <div x-show="uploadModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6" style="display: none;">
        <div x-show="uploadModal" class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm" @click="uploadModal = false"></div>
        
        <div x-show="uploadModal" class="relative w-full max-w-lg bg-white rounded-3xl shadow-2xl overflow-hidden flex flex-col max-h-full">
            <div class="p-5 sm:p-6 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                <h3 class="font-black text-sm text-slate-800 flex items-center gap-2">
                    <i class="fa-solid fa-cloud-arrow-up text-purple-600"></i> رفع ملف / فيديو جديد
                </h3>
                <button @click="uploadModal = false" class="w-8 h-8 bg-white border border-slate-200 rounded-full flex items-center justify-center text-slate-400 hover:text-rose-500 hover:bg-rose-50 transition">
                    <i class="fa-solid fa-times"></i>
                </button>
            </div>
            
            <div class="p-5 sm:p-6 overflow-y-auto">
                <form action="{{ route('media.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-2">اختر الطفل المخصص له الملف</label>
                        <select name="child_id" required class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold outline-none focus:border-purple-400">
                            <option value="">-- اختر الطفل --</option>
                            <option value="all" class="font-bold text-purple-600">-- كل الأطفال (عام) --</option>
                            @foreach($children as $child)
                                <option value="{{ $child->id }}">{{ $child->name }} ({{ $child->code }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-2">عنوان الملف / الفيديو</label>
                        <input type="text" name="title" required placeholder="مثال: التدريب على نطق حرف السين" class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold outline-none focus:border-purple-400">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-2">الملف المرفق (فيديو، صورة، مستند)</label>
                        <div class="border-2 border-dashed border-slate-300 rounded-2xl p-6 text-center hover:bg-slate-50 transition relative">
                            <input type="file" name="file" required class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                            <i class="fa-solid fa-upload text-3xl text-slate-300 mb-2"></i>
                            <p class="text-xs font-bold text-slate-600">اضغط أو اسحب الملف هنا</p>
                            <p class="text-[10px] text-slate-400 mt-1">الحد الأقصى 20 ميجابايت (MP4, JPG, PDF)</p>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-2">وصف تفصيلي (اختياري)</label>
                        <textarea name="description" rows="3" placeholder="اكتب تعليمات أو ملاحظات لولي الأمر..." class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold outline-none focus:border-purple-400"></textarea>
                    </div>

                    <div class="pt-4 border-t border-slate-100 flex justify-end gap-3">
                        <button type="button" @click="uploadModal = false" class="px-5 py-2.5 bg-slate-100 text-slate-700 rounded-xl text-xs font-bold hover:bg-slate-200 transition">
                            إلغاء
                        </button>
                        <button type="submit" class="px-5 py-2.5 bg-purple-600 text-white rounded-xl text-xs font-bold shadow-md hover:bg-purple-700 transition">
                            رفع وحفظ
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
