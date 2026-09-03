@props([
    'searchPlaceholder' => 'ابحث...',
    'filters' => [], // e.g., ['diagnosis_category' => ['label' => 'التصنيف', 'options' => ['speech' => 'تخاطب', 'autism' => 'توحد']]]
    'sortOptions' => [], // e.g., ['name' => 'الاسم', 'created_at' => 'تاريخ الإضافة']
    'route' => '#'
])

<div class="bg-white rounded-3xl p-5 border border-slate-100 shadow-sm mb-6">
    <form action="{{ $route }}" method="GET" class="flex flex-col md:flex-row flex-wrap gap-4 text-sm items-end md:items-center">
        
        <!-- Search -->
        <div class="flex-grow w-full md:w-auto min-w-[250px] relative">
            <label class="block text-xs font-bold text-slate-500 mb-1.5">البحث المباشر</label>
            <div class="relative">
                <i class="fa-solid fa-magnifying-glass absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ $searchPlaceholder }}" class="w-full pl-3 pr-10 py-2.5 bg-slate-50 border border-slate-200 rounded-2xl outline-none focus:bg-white focus:border-slate-400 font-semibold transition">
            </div>
        </div>

        <!-- Custom Filters -->
        @foreach($filters as $fieldName => $filterData)
        <div class="w-full md:w-auto min-w-[160px]">
            <label class="block text-xs font-bold text-slate-500 mb-1.5">{{ $filterData['label'] ?? 'تصفية' }}</label>
            <select name="{{ $fieldName }}" class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-2xl outline-none focus:bg-white font-bold transition" onchange="this.form.submit()">
                <option value="all">الكل</option>
                @foreach($filterData['options'] as $val => $text)
                <option value="{{ $val }}" {{ request($fieldName) === (string)$val ? 'selected' : '' }}>{{ $text }}</option>
                @endforeach
            </select>
        </div>
        @endforeach

        <!-- Sorting -->
        @if(!empty($sortOptions))
        <div class="w-full md:w-auto flex gap-2 min-w-[220px]">
            <div class="flex-grow">
                <label class="block text-xs font-bold text-slate-500 mb-1.5">ترتيب حسب</label>
                <select name="sort_by" class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-2xl outline-none focus:bg-white font-bold transition" onchange="this.form.submit()">
                    <option value="">الترتيب الافتراضي</option>
                    @foreach($sortOptions as $val => $text)
                    <option value="{{ $val }}" {{ request('sort_by') === (string)$val ? 'selected' : '' }}>{{ $text }}</option>
                    @endforeach
                </select>
            </div>
            <div class="w-24">
                <label class="block text-xs font-bold text-slate-500 mb-1.5">الاتجاه</label>
                <select name="sort_dir" class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-2xl outline-none focus:bg-white font-bold transition" onchange="this.form.submit()">
                    <option value="desc" {{ request('sort_dir', 'desc') === 'desc' ? 'selected' : '' }}>تنازلي</option>
                    <option value="asc" {{ request('sort_dir') === 'asc' ? 'selected' : '' }}>تصاعدي</option>
                </select>
            </div>
        </div>
        @endif

        <!-- Actions -->
        <div class="w-full md:w-auto flex gap-2">
            <!-- Filler to push buttons to the bottom if labels are present -->
            <div class="hidden md:block w-full h-[22px]"></div>
            
            <button type="submit" class="flex-1 md:flex-none py-2.5 px-6 rounded-2xl text-white font-bold shadow-sm hover:opacity-95 active:scale-95 transition flex items-center justify-center gap-2" style="background-color: #0d9488;">
                <i class="fa-solid fa-filter"></i>
                <span>تطبيق</span>
            </button>
            
            @if(count(request()->except(['page'])) > 0)
            <a href="{{ $route }}" class="py-2.5 px-4 rounded-2xl bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold transition flex items-center justify-center" title="إلغاء الفلاتر">
                <i class="fa-solid fa-rotate-left"></i>
            </a>
            @endif
        </div>
    </form>
</div>


