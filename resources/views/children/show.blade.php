@extends('layouts.app')

@section('title', 'Ù…Ù„Ù Ø§Ù„Ø·ÙÙ„: ' . $child->name)

@section('content')
<div class="space-y-8" x-data="{ 
    activeTab: 'iep',
    goalModalOpen: false,
    videoModalOpen: false,
    activeVideoSrc: null,
    activeVideoTitle: '',
    whatsappModalOpen: {{ session('whatsapp_url') ? 'true' : 'false' }},
    isYoutube(url) {
        if(!url) return false;
        return url.includes('youtube.com') || url.includes('youtu.be');
    },
    getYoutubeId(url) {
        let match = url.match(/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/i);
        return match ? match[1] : '';
    },
    openVideo(src, title) {
        this.activeVideoSrc = src;
        this.activeVideoTitle = title;
        this.videoModalOpen = true;
    }
}">

    <!-- Ø±Ø³Ø§Ù„Ø© Ø§Ù„Ù†Ø¬Ø§Ø­ Ø¹Ù†Ø¯ ØªØ³Ø¬ÙŠÙ„ Ø¬Ù„Ø³Ø© -->
    @if(session('success'))
    <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 p-4 rounded-2xl text-xs font-bold flex items-center gap-3">
        <i class="fa-solid fa-circle-check text-emerald-600 text-lg"></i>
        <span class="flex-1">{{ session('success') }}</span>
        @if(session('whatsapp_url'))
        <a href="{{ session('whatsapp_url') }}" target="_blank" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold flex items-center gap-2 transition shadow-md animate-pulse">
            <i class="fa-brands fa-whatsapp text-base"></i>
            <span>Ø¥Ø±Ø³Ø§Ù„ Ø§Ù„Ù…Ù„Ø®Øµ Ù„Ù„Ø£Ù‡Ù„ Ø¹Ø¨Ø± ÙˆØ§ØªØ³Ø§Ø¨</span>
        </a>
        @endif
    </div>
    @endif

    <!-- Ù…ÙˆØ¯Ø§Ù„ ÙˆØ§ØªØ³Ø§Ø¨: Ø¥Ø±Ø³Ø§Ù„ Ù…Ù„Ø®Øµ Ø§Ù„Ø¬Ù„Ø³Ø© Ù„ÙˆÙ„ÙŠ Ø§Ù„Ø£Ù…Ø± -->
    @if(session('whatsapp_url'))
    <div x-show="whatsappModalOpen" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-[9999] flex items-center justify-center p-4"
         style="background: rgba(0,0,0,0.5); backdrop-filter: blur(4px);">
        
        <div x-show="whatsappModalOpen"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-90"
             x-transition:enter-end="opacity-100 scale-100"
             @click.away="whatsappModalOpen = false"
             class="bg-white rounded-3xl shadow-2xl w-full max-w-md overflow-hidden">
            
            <!-- Header -->
            <div class="bg-gradient-to-l from-emerald-500 to-emerald-600 p-6 text-center text-white">
                <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-3">
                    <i class="fa-brands fa-whatsapp text-4xl"></i>
                </div>
                <h3 class="text-lg font-black">ØªÙ… ØªØ³Ø¬ÙŠÙ„ Ø§Ù„Ø¬Ù„Ø³Ø© Ø¨Ù†Ø¬Ø§Ø­! âœ¨</h3>
                <p class="text-emerald-100 text-sm mt-1">Ù‡Ù„ ØªØ±ÙŠØ¯ Ø¥Ø±Ø³Ø§Ù„ Ù…Ù„Ø®Øµ Ø§Ù„Ø¬Ù„Ø³Ø© Ù„ÙˆÙ„ÙŠ Ø§Ù„Ø£Ù…Ø± Ø§Ù„Ø¢Ù†ØŸ</p>
            </div>

            <!-- Content -->
            <div class="p-6 space-y-4">
                <div class="bg-emerald-50 border border-emerald-200 rounded-2xl p-4 text-right">
                    <p class="text-xs text-emerald-700 font-bold mb-2 flex items-center gap-1.5">
                        <i class="fa-solid fa-circle-info"></i>
                        Ø³ÙŠØªÙ… ÙØªØ­ ÙˆØ§ØªØ³Ø§Ø¨ Ø¨Ø±Ø³Ø§Ù„Ø© Ø¬Ø§Ù‡Ø²Ø© ØªØ­ØªÙˆÙŠ Ø¹Ù„Ù‰:
                    </p>
                    <ul class="text-xs text-emerald-800 font-medium space-y-1.5 pr-4">
                        <li class="flex items-center gap-1.5">ðŸ“… ØªØ§Ø±ÙŠØ® ÙˆÙˆÙ‚Øª Ø§Ù„Ø¬Ù„Ø³Ø©</li>
                        <li class="flex items-center gap-1.5">ðŸ©º Ø§Ø³Ù… Ø§Ù„Ø£Ø®ØµØ§Ø¦ÙŠ ÙˆÙ†ÙˆØ¹ Ø§Ù„Ø¬Ù„Ø³Ø©</li>
                        <li class="flex items-center gap-1.5">ðŸ“ Ù…Ù„Ø§Ø­Ø¸Ø§Øª ÙˆØªÙ‚Ø±ÙŠØ± Ø§Ù„Ø£Ø®ØµØ§Ø¦ÙŠ</li>
                        <li class="flex items-center gap-1.5">ðŸŽ¯ Ø§Ù„Ø£Ù‡Ø¯Ø§Ù Ø§Ù„ØªÙŠ ØªÙ… Ø§Ù„ØªØ¯Ø±ÙŠØ¨ Ø¹Ù„ÙŠÙ‡Ø§</li>
                        <li class="flex items-center gap-1.5">ðŸ  Ø§Ù„ØªÙ…Ø±ÙŠÙ† Ø§Ù„Ù…Ù†Ø²Ù„ÙŠ Ø§Ù„Ù…Ø·Ù„ÙˆØ¨</li>
                        <li class="flex items-center gap-1.5">ðŸ“± Ø±Ø§Ø¨Ø· Ø¨ÙˆØ§Ø¨Ø© ÙˆÙ„ÙŠ Ø§Ù„Ø£Ù…Ø±</li>
                    </ul>
                </div>

                <!-- Action Buttons -->
                <div class="flex gap-3">
                    <a href="{{ session('whatsapp_url') }}" target="_blank" @click="whatsappModalOpen = false"
                       class="flex-1 py-3.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-2xl text-sm font-black flex items-center justify-center gap-2 transition shadow-lg shadow-emerald-200">
                        <i class="fa-brands fa-whatsapp text-xl"></i>
                        <span>Ø¥Ø±Ø³Ø§Ù„ Ø¹Ø¨Ø± ÙˆØ§ØªØ³Ø§Ø¨</span>
                    </a>
                    <button @click="whatsappModalOpen = false" type="button"
                            class="px-5 py-3.5 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-2xl text-sm font-bold transition">
                        Ù„Ø§Ø­Ù‚Ø§Ù‹
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- ==================== 1. Ø¨Ø·Ø§Ù‚Ø© Ø§Ù„Ù‡ÙˆÙŠØ© ÙˆØ§Ù„Ø¨Ø±ÙˆÙØ§ÙŠÙ„ Ø§Ù„Ø±Ø¦ÙŠØ³ÙŠØ© ==================== -->
    <div class="bg-white rounded-3xl p-6 md:p-8 border border-slate-100 shadow-sm space-y-6">
        
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 pb-6 border-b border-slate-100">
            
            <!-- ØµÙˆØ±Ø© Ø§Ù„Ø·ÙÙ„ ÙˆØ§Ù„Ø§Ø³Ù… ÙˆØ§Ù„Ø¨ÙŠØ§Ù†Ø§Øª Ø§Ù„Ø³Ø±ÙŠØ¹Ø© -->
            <div class="flex flex-col sm:flex-row items-center sm:items-start gap-5 text-center sm:text-right">
                <div class="relative">
                    <img src="{{ $child->avatar_url }}" alt="{{ $child->name }}" class="w-20 h-20 rounded-2xl object-cover ring-2 ring-slate-200 shadow-sm">
                    <span class="absolute -bottom-1 -right-1 w-4 h-4 rounded-full border-2 border-white {{ $child->status === 'active' ? 'bg-emerald-500' : 'bg-slate-400' }}"></span>
                </div>

                <div class="space-y-1.5">
                    <div class="flex flex-wrap items-center justify-center sm:justify-start gap-2.5">
                        <h2 class="text-xl font-black text-slate-900">{{ $child->name }}</h2>
                        <span class="font-mono text-[11px] px-2 py-0.5 rounded-lg font-bold bg-slate-100 text-slate-600 border border-slate-200">{{ $child->code }}</span>
                        @if($child->status === 'active')
                            <span class="px-2 py-0.5 rounded-lg text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                Ù†Ø´Ø· Ø¨Ø§Ù„Ø®Ø·Ø©
                            </span>
                        @elseif($child->status === 'on_hold')
                            <span class="px-2 py-0.5 rounded-lg text-[11px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                Ù…Ø¹Ù„Ù‚
                            </span>
                        @else
                            <span class="px-2 py-0.5 rounded-lg text-[11px] font-bold bg-slate-50 text-slate-500 border border-slate-200">
                                Ø®Ø§Ø±Ø¬ Ø§Ù„Ø®Ø·Ø©
                            </span>
                        @endif
                    </div>

                    <!-- Ø³Ø·ÙˆØ± Ø§Ù„ØªØ´Ø®ÙŠØµØ§Øª -->
                    <div class="flex flex-wrap items-center justify-center sm:justify-start gap-1.5 pt-0.5">
                        @foreach($child->diagnoses_list as $diag)
                        <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-slate-800 text-white">
                            {{ $diag }}
                        </span>
                        @endforeach
                    </div>

                    <p class="text-[11px] text-slate-500 font-medium">
                        ÙˆÙ„ÙŠ Ø§Ù„Ø£Ù…Ø±: <strong class="text-slate-700">{{ $child->parent_name }}</strong> ({{ $child->parent_relation }}) â€¢ Ù‡Ø§ØªÙ: <span class="font-mono font-bold text-slate-600">{{ $child->phone }}</span>
                    </p>
                </div>
            </div>

            <!-- Ø£Ø²Ø±Ø§Ø± Ø§Ù„Ø¥Ø¬Ø±Ø§Ø¡Ø§Øª -->
            <div class="flex flex-wrap items-center justify-center gap-2">
                @if(Auth::check() && Auth::user()->role === 'specialist')
                <a href="{{ route('doctor.sessions.create', ['child_id' => $child->id]) }}" class="px-4 py-2 rounded-xl text-white font-bold text-xs transition flex items-center gap-1.5 hover:opacity-90" style="background-color: #0d9488;">
                    <i class="fa-solid fa-notes-medical"></i>
                    <span>ØªØ³Ø¬ÙŠÙ„ Ø¬Ù„Ø³Ø©</span>
                </a>
                @endif

                <a href="{{ route('children.edit', $child) }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-bold transition flex items-center gap-1.5">
                    <i class="fa-solid fa-pen-to-square"></i>
                    <span>ØªØ¹Ø¯ÙŠÙ„</span>
                </a>

                <a href="https://wa.me/2{{ $child->phone }}" target="_blank" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-bold transition flex items-center gap-1.5">
                    <i class="fa-brands fa-whatsapp text-emerald-600"></i>
                    <span>ÙˆØ§ØªØ³Ø§Ø¨</span>
                </a>

                <a href="{{ route('children.print', $child) }}" target="_blank" class="px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white rounded-xl text-xs font-bold transition flex items-center gap-1.5">
                    <i class="fa-solid fa-print"></i>
                    <span>Ø·Ø¨Ø§Ø¹Ø© A4</span>
                </a>

                <a href="{{ route('children.index') }}" class="px-4 py-2 bg-slate-50 hover:bg-slate-100 text-slate-500 rounded-xl text-xs font-bold transition border border-slate-200">
                    Ø§Ù„Ø¹ÙˆØ¯Ø©
                </a>
            </div>

        </div>

        <!-- Ø£Ø´Ø±Ø·Ø© Ø§Ù„Ù…Ù‚Ø§Ø±Ù†Ø© ÙˆØ§Ù„Ù…Ø¤Ø´Ø±Ø§Øª Ø§Ù„Ø£Ø±Ø¨Ø¹Ø© Ø§Ù„Ø³Ø±ÙŠØ¹Ø© -->
        @php
            $childSchedules = $child->sessionSchedules()->get();
            $attendedCount = $childSchedules->where('attendance_status', 'attended')->count();
            $excusedCount = $childSchedules->filter(function($s) {
                return $s->status === 'cancelled' || str_contains($s->notes ?? '', 'Ø§Ø¹ØªØ°Ø§Ø±');
            })->count();
            $absentCount = $childSchedules->where('attendance_status', 'absent')->count() - $childSchedules->filter(function($s) {
                return $s->attendance_status === 'absent' && ($s->status === 'cancelled' || str_contains($s->notes ?? '', 'Ø§Ø¹ØªØ°Ø§Ø±'));
            })->count();
            $absentCount = max(0, $absentCount);
            
            $totalStats = $attendedCount + $absentCount + $excusedCount;
            $attPct = $totalStats > 0 ? round(($attendedCount / $totalStats) * 100) : 0;
            $absPct = $totalStats > 0 ? round(($absentCount / $totalStats) * 100) : 0;
            $excPct = $totalStats > 0 ? round(($excusedCount / $totalStats) * 100) : 0;

            $registeredSince = $child->created_at ? clone $child->created_at : now();
            $registeredSinceStr = $registeredSince->diffForHumans(['parts' => 2, 'join' => ' Ùˆ ', 'syntax' => \Carbon\CarbonInterface::DIFF_ABSOLUTE]);
            $uniqueSpecialists = $child->sessionSchedules()->whereNotNull('specialist_id')->distinct('specialist_id')->count('specialist_id');
        @endphp
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4 text-xs font-medium">

            <!-- 1. Ù…Ù‚Ø§Ø±Ù†Ø© Ø§Ù„Ø¹Ù…Ø± Ø§Ù„Ø²Ù…Ù†ÙŠ ÙˆØ§Ù„Ø¹Ù‚Ù„ÙŠ -->
            <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-100 space-y-2">
                <span class="text-[10px] font-bold text-slate-400 block">Ø§Ù„Ø¹Ù…Ø± Ø§Ù„Ø²Ù…Ù†ÙŠ / Ø§Ù„Ø¹Ù‚Ù„ÙŠ:</span>
                <div class="flex items-center gap-2">
                    <div class="flex-1 p-2 rounded-lg bg-white border border-slate-200 text-center">
                        <span class="text-[9px] text-slate-400 font-bold block">Ø§Ù„Ø²Ù…Ù†ÙŠ</span>
                        <span class="font-black text-slate-900 text-sm block">{{ $child->age_text }}</span>
                    </div>
                    <div class="flex-1 p-2 rounded-lg border text-center {{ $child->mental_age ? 'bg-amber-50 border-amber-200' : 'bg-slate-50 border-slate-200' }}">
                        <span class="text-[9px] font-bold block {{ $child->mental_age ? 'text-amber-600' : 'text-slate-400' }}">Ø§Ù„Ø¹Ù‚Ù„ÙŠ</span>
                        <span class="font-black text-sm block {{ $child->mental_age ? 'text-amber-800' : 'text-slate-400' }}">{{ $child->mental_age ?? 'â€”' }}</span>
                    </div>
                </div>
            </div>

            <!-- 2. Ø§Ù„Ø£Ø®ØµØ§Ø¦ÙŠ Ø§Ù„Ù…ØªØ§Ø¨Ø¹ -->
            <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-100 space-y-1">
                <span class="text-[10px] font-bold text-slate-400 block">Ø§Ù„Ø£Ø®ØµØ§Ø¦ÙŠ Ø§Ù„Ù…Ø¹Ø§Ù„Ø¬:</span>
                <p class="font-black text-slate-800 text-xs truncate">{{ $child->main_specialist ?? 'ØºÙŠØ± Ù…Ø­Ø¯Ø¯' }}</p>
                <p class="text-[10px] font-bold text-slate-400">Ù…ØªØ§Ø¨Ø¹ Ø§Ù„Ø­Ø§Ù„Ø© Ø¨Ø§Ù„Ù…Ø±ÙƒØ²</p>
            </div>

            <!-- 3. Ø¯ÙƒØªÙˆØ± Ø§Ù„Ù…Ø® ÙˆØ§Ù„Ø£Ø¹ØµØ§Ø¨ -->
            <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-100 space-y-1">
                <span class="text-[10px] font-bold text-slate-400 block">Ø¯ÙƒØªÙˆØ± Ø§Ù„Ù…Ø® ÙˆØ§Ù„Ø£Ø¹ØµØ§Ø¨:</span>
                <p class="font-black text-blue-800 text-xs truncate">{{ $child->neurologist_name ?? 'Ù„Ø§ ÙŠÙˆØ¬Ø¯' }}</p>
                <p class="text-[10px] text-slate-400 truncate">{{ $child->current_medications ? 'ÙŠØªÙ†Ø§ÙˆÙ„ Ø£Ø¯ÙˆÙŠØ© Ù…Ø³Ø¬Ù„Ø©' : 'Ø¨Ø¯ÙˆÙ† Ø£Ø¯ÙˆÙŠØ©' }}</p>
            </div>

            <!-- 4. Ø¥Ø­ØµØ§Ø¦ÙŠØ§Øª Ø§Ù„Ø­Ø¶ÙˆØ± -->
            <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-100 space-y-1 relative group">
                <span class="text-xs font-bold text-slate-500 block">Ø§Ù„Ø­Ø¶ÙˆØ± (Ø¥Ø¬Ù…Ø§Ù„ÙŠ: {{ $totalStats }}):</span>
                <div class="flex items-center justify-between font-black text-base px-1 pt-0.5 mt-1">
                    <span class="text-emerald-600 flex flex-col items-center gap-1 leading-none" title="Ø­Ø¶Ø±">{{ $attendedCount }}<span class="text-[10px] text-emerald-500 font-bold">Ø­Ø¶Ø± ({{ $attPct }}%)</span></span>
                    <span class="text-rose-500 flex flex-col items-center gap-1 leading-none" title="ØºØ§Ø¨">{{ $absentCount }}<span class="text-[10px] text-rose-400 font-bold">ØºØ§Ø¨ ({{ $absPct }}%)</span></span>
                    <span class="text-amber-500 flex flex-col items-center gap-1 leading-none" title="Ø§Ø¹ØªØ°Ø±">{{ $excusedCount }}<span class="text-[10px] text-amber-400 font-bold">Ø§Ø¹ØªØ°Ø± ({{ $excPct }}%)</span></span>
                </div>
            </div>

            <!-- 5. Ø±ØµÙŠØ¯ Ø§Ù„Ø¬Ù„Ø³Ø§Øª -->
            <div class="p-3.5 rounded-2xl border space-y-1" style="background-color: #0d948808; border-color: #0d948830;">
                <span class="text-[10px] font-bold text-slate-400 block">Ø±ØµÙŠØ¯ Ø¨Ø§Ù‚Ø© Ø§Ù„Ø¬Ù„Ø³Ø§Øª:</span>
                <p class="font-black text-slate-800 text-sm" style="color: #0d9488;">
                    Ù…ØªØ¨Ù‚ÙŠ {{ $packageInfo['remaining_sessions'] }} Ù…Ù† {{ $packageInfo['total_sessions'] }}
                </p>
                <p class="text-[10px] text-emerald-700 font-bold">Ø¬Ù„Ø³Ø§Øª Ù…Ù†ØªØ¸Ù…Ø©</p>
            </div>

            <!-- 6. Ø¥Ø­ØµØ§Ø¦ÙŠØ§Øª Ø¹Ø§Ù…Ø© -->
            <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-100 space-y-1">
                <span class="text-[10px] font-bold text-slate-400 block">Ø¥Ø­ØµØ§Ø¦ÙŠØ§Øª Ø§Ù„Ø·ÙÙ„:</span>
                <p class="font-black text-indigo-700 text-xs">Ù…Ø³Ø¬Ù„ Ù…Ù†Ø° {{ $registeredSinceStr }}</p>
                <p class="text-[10px] text-indigo-500 font-bold mt-1">ØªØ¯Ø±Ø¨ Ù…Ø¹ {{ $uniqueSpecialists }} Ø£Ø®ØµØ§Ø¦ÙŠÙŠÙ†</p>
            </div>

        </div>

    </div>

    <!-- ==================== 2. Ø´Ø±ÙŠØ· Ø§Ù„ØªØ¨ÙˆÙŠØ¨Ø§Øª ==================== -->
    <div class="flex flex-wrap items-center gap-1 bg-slate-100 p-1 rounded-xl text-xs font-bold">
        <button type="button" @click="activeTab = 'iep'" :class="activeTab === 'iep' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-500 hover:text-slate-700'" class="px-4 py-2 rounded-lg transition flex items-center gap-1.5">
            <i class="fa-solid fa-bullseye"></i>
            <span>Ø§Ù„Ø®Ø·Ø© Ø§Ù„Ø¹Ù„Ø§Ø¬ÙŠØ©</span>
        </button>

        <button type="button" @click="activeTab = 'medical'" :class="activeTab === 'medical' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-500 hover:text-slate-700'" class="px-4 py-2 rounded-lg transition flex items-center gap-1.5">
            <i class="fa-solid fa-brain"></i>
            <span>Ø§Ù„Ù…Ù„Ù Ø§Ù„Ø·Ø¨ÙŠ</span>
        </button>

        <button type="button" @click="activeTab = 'sessions'" :class="activeTab === 'sessions' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-500 hover:text-slate-700'" class="px-4 py-2 rounded-lg transition flex items-center gap-1.5">
            <i class="fa-solid fa-calendar-check"></i>
            <span>ØªÙ‚Ø§Ø±ÙŠØ± Ø§Ù„Ø¬Ù„Ø³Ø§Øª</span>
            <span class="px-1.5 py-0.5 rounded text-[10px] bg-slate-200 text-slate-600">{{ count($recentSessions) }}</span>
        </button>

        <button type="button" @click="activeTab = 'parent_notes'" :class="activeTab === 'parent_notes' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-500 hover:text-slate-700'" class="px-4 py-2 rounded-lg transition flex items-center gap-1.5">
            <i class="fa-solid fa-comments"></i>
            <span>Ù…Ù„Ø§Ø­Ø¸Ø§Øª ÙˆÙ„ÙŠ Ø§Ù„Ø£Ù…Ø±</span>
        </button>
    </div>

    <!-- ==================== ØªØ¨ÙˆÙŠØ¨ 1: Ø§Ù„Ø®Ø·Ø© Ø§Ù„Ø¹Ù„Ø§Ø¬ÙŠØ© Ø§Ù„ÙØ±Ø¯ÙŠØ© ==================== -->
    <div x-show="activeTab === 'iep'" class="space-y-6">
        
        <div class="flex items-center justify-between">
            <div>
                <h3 class="font-black text-lg text-slate-800">Ø§Ù„Ø£Ù‡Ø¯Ø§Ù Ø§Ù„Ø¹Ù„Ø§Ø¬ÙŠØ© Ø§Ù„Ù…Ø­Ø¯Ø¯Ø© Ù„Ù„Ø·ÙÙ„</h3>
                <p class="text-xs text-slate-400 font-semibold mt-0.5">Ù…ØªØ§Ø¨Ø¹Ø© Ù†Ø³Ø¨ Ø§Ù„ØªÙ‚Ø¯Ù… ÙˆÙ…Ø±Ø§Ø­Ù„ Ø§Ù„Ø¥Ù†Ø¬Ø§Ø² Ù„ÙƒÙ„ Ù‡Ø¯Ù ØªØ£Ù‡ÙŠÙ„ÙŠ</p>
            </div>

            <button type="button" @click="alert('Ø³ÙŠØªÙ… Ø¥Ø¶Ø§ÙØ© Ù†Ø§ÙØ°Ø© ØªØ¹Ø±ÙŠÙ Ø§Ù„Ø£Ù‡Ø¯Ø§Ù ÙÙŠ Ø§Ù„ØªØ­Ø¯ÙŠØ« Ø§Ù„Ù‚Ø§Ø¯Ù…')" class="px-4 py-2 rounded-2xl text-white font-bold text-xs shadow-md transition flex items-center gap-2" style="background-color: #0d9488;">
                <i class="fa-solid fa-plus"></i>
                <span>Ø¥Ø¶Ø§ÙØ© Ù‡Ø¯Ù Ø¹Ù„Ø§Ø¬ÙŠ Ø¬Ø¯ÙŠØ¯</span>
            </button>
        </div>

        <!-- ÙƒØ±ÙˆØª Ø§Ù„Ø£Ù‡Ø¯Ø§Ù Ø§Ù„Ø¹Ù„Ø§Ø¬ÙŠØ© -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach($iepGoals as $goal)
            <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm space-y-4 hover:border-slate-300 transition">
                
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <span class="px-2.5 py-0.5 rounded-lg text-[10px] font-extrabold bg-slate-100 text-slate-700">
                            {{ $goal['category'] }}
                        </span>
                        <h4 class="font-extrabold text-sm text-slate-800 mt-2 leading-snug">{{ $goal['title'] }}</h4>
                    </div>

                    @if($goal['status'] === 'achieved')
                        <span class="px-2.5 py-1 rounded-xl text-xs font-black bg-emerald-100 text-emerald-800 shrink-0">
                            <i class="fa-solid fa-check ml-1"></i> Ù…ÙƒØªÙ…Ù„
                        </span>
                    @else
                        <span class="px-2.5 py-1 rounded-xl text-xs font-black bg-blue-50 text-blue-700 shrink-0">
                            Ù‚ÙŠØ¯ Ø§Ù„ØªØ¯Ø±ÙŠØ¨
                        </span>
                    @endif
                </div>

                <!-- Ø´Ø±ÙŠØ· Ø§Ù„ØªÙ‚Ø¯Ù… Ø§Ù„Ø°ÙƒÙŠ ÙˆØ§Ù„Ù…Ø­Ø·Ø§Øª -->
                <div class="space-y-3 pt-2">
                    <div class="flex justify-between items-center text-[10px] font-black text-slate-400 px-1">
                        <span class="{{ $goal['progress'] >= 0 ? 'text-teal-600' : '' }}">Ø§Ù„Ø¨Ø¯Ø§ÙŠØ©</span>
                        <span class="{{ $goal['progress'] >= 25 ? 'text-teal-600' : '' }}">25%</span>
                        <span class="{{ $goal['progress'] >= 50 ? 'text-teal-600' : '' }}">50%</span>
                        <span class="{{ $goal['progress'] >= 75 ? 'text-teal-600' : '' }}">75%</span>
                        <span class="{{ $goal['progress'] >= 100 ? 'text-emerald-600' : '' }}">Ø¥ØªÙ‚Ø§Ù†</span>
                    </div>
                    
                    <div class="relative w-full h-3 bg-slate-100 rounded-full overflow-hidden flex shadow-inner">
                        <!-- Segments -->
                        <div class="h-full border-r border-white/40 transition-all duration-700 {{ $goal['progress'] >= 25 ? 'bg-teal-500' : 'bg-transparent' }}" style="width: 25%"></div>
                        <div class="h-full border-r border-white/40 transition-all duration-700 {{ $goal['progress'] >= 50 ? 'bg-teal-500' : 'bg-transparent' }}" style="width: 25%"></div>
                        <div class="h-full border-r border-white/40 transition-all duration-700 {{ $goal['progress'] >= 75 ? 'bg-teal-500' : 'bg-transparent' }}" style="width: 25%"></div>
                        <div class="h-full transition-all duration-700 {{ $goal['progress'] >= 100 ? 'bg-emerald-500' : 'bg-transparent' }}" style="width: 25%"></div>
                        
                        <!-- Overlay for exact progress if not exactly on a quarter -->
                        <div class="absolute top-0 left-0 h-full bg-teal-400/30 transition-all duration-1000 rounded-full" style="width: {{ $goal['progress'] }}%"></div>
                    </div>
                </div>

                <div class="pt-3 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-400 font-medium">
                    <span>Ø§Ù„Ù…Ø¹Ø§Ù„Ø¬: <strong class="text-slate-700">{{ $goal['specialist'] }}</strong></span>
                    <span>Ø§Ù„Ù…ÙˆØ¹Ø¯ Ø§Ù„Ù…Ø³ØªÙ‡Ø¯Ù: <strong class="text-slate-700 font-mono">{{ $goal['target_date'] }}</strong></span>
                </div>

            </div>
            @endforeach
        </div>

    </div>

    <!-- ==================== ØªØ¨ÙˆÙŠØ¨ 2: Ø§Ù„Ù…Ù„Ù Ø§Ù„Ø·Ø¨ÙŠ ÙˆØ§Ù„Ø³Ø±ÙŠØ±ÙŠ ==================== -->
    <div x-show="activeTab === 'medical'" class="grid grid-cols-1 md:grid-cols-2 gap-6">
        
        <!-- Ø¨Ø·Ø§Ù‚Ø© Ø§Ù„ØªØ´Ø®ÙŠØµØ§Øª ÙˆØ§Ù„Ù…Ø® ÙˆØ§Ù„Ø£Ø¹ØµØ§Ø¨ -->
        <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm space-y-5">
            <div class="flex items-center gap-2 border-b border-slate-100 pb-3">
                <i class="fa-solid fa-stethoscope text-base" style="color: #0d9488;"></i>
                <h4 class="font-extrabold text-sm text-slate-800">Ø§Ù„ØªØ´Ø®ÙŠØµØ§Øª Ø§Ù„Ø·Ø¨ÙŠØ© ÙˆØ§Ù„Ù…Ø® ÙˆØ§Ù„Ø£Ø¹ØµØ§Ø¨</h4>
            </div>

            <!-- Ø³Ø·ÙˆØ± Ø§Ù„ØªØ´Ø®ÙŠØµØ§Øª Ø§Ù„Ù…Ø³ØªÙ‚Ù„Ø© -->
            <div class="space-y-2">
                <span class="text-xs font-bold text-slate-400">Ø³Ø·ÙˆØ± Ø§Ù„ØªØ´Ø®ÙŠØµØ§Øª Ø§Ù„Ù…Ø¹ØªÙ…Ø¯Ø©:</span>
                <div class="space-y-1.5">
                    @foreach($child->diagnoses_list as $idx => $d)
                    <div class="p-3 rounded-2xl bg-purple-50/80 border border-purple-100 text-purple-950 font-bold text-xs flex items-center gap-2.5">
                        <span class="w-5 h-5 rounded-lg bg-purple-200 text-purple-800 flex items-center justify-center text-[10px]">{{ $idx + 1 }}</span>
                        <span>{{ $d }}</span>
                    </div>
                    @endforeach
                </div>
            </div>

            <div class="space-y-1 text-xs">
                <span class="text-slate-400 font-bold">Ø·Ø¨ÙŠØ¨ Ø§Ù„Ù…Ø® ÙˆØ§Ù„Ø£Ø¹ØµØ§Ø¨ Ø§Ù„Ù…ØªØ§Ø¨Ø¹:</span>
                <p class="p-3 rounded-2xl bg-blue-50 text-blue-900 font-bold">
                    {{ $child->neurologist_name ?? 'Ù„Ø§ ÙŠÙˆØ¬Ø¯ Ø·Ø¨ÙŠØ¨ Ù…Ø® ÙˆØ£Ø¹ØµØ§Ø¨ Ù…Ø³Ø¬Ù„' }}
                </p>
            </div>

            <div class="space-y-2 text-xs">
                <span class="text-slate-400 font-bold">Ø§Ù„Ø£Ø¯ÙˆÙŠØ© ÙˆØ§Ù„Ø¹Ù„Ø§Ø¬Ø§Øª Ø§Ù„Ø­Ø§Ù„ÙŠØ©:</span>
                <p class="p-3 rounded-2xl bg-amber-50 text-amber-900 font-semibold leading-relaxed">
                    {{ $child->current_medications ?? 'Ù„Ø§ ÙŠØªÙ†Ø§ÙˆÙ„ Ø£Ø¯ÙˆÙŠØ© Ø­Ø§Ù„ÙŠØ§Ù‹' }}
                </p>
                
                @if($child->medications_file)
                    <div class="flex items-center justify-between p-3 rounded-2xl bg-amber-50 border border-amber-100">
                        <div class="flex items-center gap-2">
                            <i class="fa-solid fa-file-medical text-amber-600 text-lg"></i>
                            <span class="font-bold text-amber-900">Ù…Ù„Ù Ø§Ù„Ø£Ø¯ÙˆÙŠØ© Ø§Ù„Ù…Ø±ÙÙ‚</span>
                        </div>
                        <a href="{{ asset('storage/' . $child->medications_file) }}" target="_blank" class="px-3 py-1.5 bg-amber-600 hover:bg-amber-700 text-white rounded-xl text-xs font-bold transition">
                            <i class="fa-solid fa-download"></i> Ø¹Ø±Ø¶
                        </a>
                    </div>
                @endif

                <form action="{{ route('children.upload_medications', $child) }}" method="POST" enctype="multipart/form-data" class="mt-2 flex items-center gap-2">
                    @csrf
                    <div class="relative flex-1">
                        <input type="file" name="medications_file" required class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" title="Ø§Ø®ØªØ± Ù…Ù„Ù">
                        <div class="px-4 py-2 border-2 border-dashed border-slate-200 rounded-xl bg-slate-50 text-slate-500 font-medium flex items-center justify-center gap-2 pointer-events-none">
                            <i class="fa-solid fa-cloud-arrow-up"></i>
                            <span>Ø¥Ø±ÙØ§Ù‚ ØµÙˆØ±Ø© Ø£Ùˆ Ù…Ù„Ù Ø£Ø¯ÙˆÙŠØ©</span>
                        </div>
                    </div>
                    <button type="submit" class="px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white rounded-xl font-bold transition shadow-md whitespace-nowrap">
                        Ø±ÙØ¹
                    </button>
                </form>
            </div>
        </div>

        <!-- Ø¨Ø·Ø§Ù‚Ø© Ø§Ø®ØªØ¨Ø§Ø±Ø§Øª Ø§Ù„Ø°ÙƒØ§Ø¡ ÙˆØ§Ù„ØªØ§Ø±ÙŠØ® Ø§Ù„Ù…Ø±Ø¶ÙŠ -->
        <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm space-y-5">
            <div class="flex items-center gap-2 border-b border-slate-100 pb-3">
                <i class="fa-solid fa-brain text-base" style="color: #0d9488;"></i>
                <h4 class="font-extrabold text-sm text-slate-800">Ø§Ø®ØªØ¨Ø§Ø±Ø§Øª Ø§Ù„Ø°ÙƒØ§Ø¡ ÙˆØ§Ù„Ù…Ù‚Ø§ÙŠÙŠØ³ Ø§Ù„Ø³Ø§Ø¨Ù‚Ø©</h4>
            </div>

            <div class="space-y-3 text-xs">
                <span class="text-slate-400 font-bold">Ø³Ø¬Ù„ Ø§Ø®ØªØ¨Ø§Ø±Ø§Øª ÙˆÙ…Ù‚Ø§ÙŠÙŠØ³ Ø§Ù„Ø°ÙƒØ§Ø¡ Ø§Ù„Ø³Ø§Ø¨Ù‚Ø©:</span>
                @if($child->iq_tests_history)
                <p class="p-3.5 rounded-2xl bg-slate-50 text-slate-800 font-medium leading-relaxed">
                    {{ $child->iq_tests_history }}
                </p>
                @endif

                @foreach($child->tests as $test)
                    <div class="p-3 rounded-2xl border border-slate-100 bg-white flex flex-col gap-2 relative group">
                        <div class="flex items-center justify-between">
                            <span class="font-bold text-slate-800">{{ $test->test_name }}</span>
                            <span class="text-[10px] text-slate-400 bg-slate-50 px-2 py-0.5 rounded-full">{{ $test->test_date->format('Y-m-d') }}</span>
                        </div>
                        @if($test->score)
                            <div class="text-[11px] font-bold text-emerald-600 bg-emerald-50 px-2 py-1 rounded-lg w-fit">
                                Ø§Ù„Ø¯Ø±Ø¬Ø© / Ø§Ù„Ù†ØªÙŠØ¬Ø©: {{ $test->score }}
                            </div>
                        @endif
                        @if($test->notes)
                            <p class="text-slate-600 font-medium">{{ $test->notes }}</p>
                        @endif
                        <div class="flex items-center gap-2 mt-1">
                            @if($test->file_path)
                                <a href="{{ asset('storage/' . $test->file_path) }}" target="_blank" class="px-3 py-1.5 bg-blue-50 hover:bg-blue-100 text-blue-700 rounded-xl text-[10px] font-bold flex items-center gap-1.5 transition">
                                    <i class="fa-solid fa-file-pdf"></i> Ø¹Ø±Ø¶ Ø§Ù„Ù…Ù„Ù
                                </a>
                            @endif
                            <form action="{{ route('children.tests.destroy', $test) }}" method="POST" class="inline" onsubmit="return confirm('ØªØ£ÙƒÙŠØ¯ Ø§Ù„Ø­Ø°ÙØŸ')">
                                @csrf @method('DELETE')
                                <button type="submit" class="px-3 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-700 rounded-xl text-[10px] font-bold transition">
                                    <i class="fa-solid fa-trash"></i> Ø­Ø°Ù
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach

                <!-- Add New Test Form -->
                <form action="{{ route('children.tests.store', $child) }}" method="POST" enctype="multipart/form-data" class="mt-4 p-4 rounded-2xl border border-slate-200 bg-slate-50/50 space-y-3">
                    @csrf
                    <p class="font-bold text-slate-700 mb-2">Ø¥Ø¶Ø§ÙØ© ØªÙ‚ÙŠÙŠÙ… / Ø§Ø®ØªØ¨Ø§Ø± Ø¬Ø¯ÙŠØ¯</p>
                    <div class="grid grid-cols-2 gap-3">
                        <input type="text" name="test_name" placeholder="Ø§Ø³Ù… Ø§Ù„Ø§Ø®ØªØ¨Ø§Ø± (Ù…Ø«Ù„: Ø³ØªØ§Ù†ÙÙˆØ±Ø¯ Ø¨ÙŠÙ†ÙŠÙ‡)" required class="w-full px-3 py-2 border border-slate-200 rounded-xl outline-none focus:border-emerald-500 font-medium text-xs">
                        <input type="date" name="test_date" required value="{{ date('Y-m-d') }}" class="w-full px-3 py-2 border border-slate-200 rounded-xl outline-none focus:border-emerald-500 font-medium text-xs">
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <input type="text" name="score" placeholder="Ø§Ù„Ø¯Ø±Ø¬Ø© Ø£Ùˆ Ø§Ù„Ù†ØªÙŠØ¬Ø© (Ø§Ø®ØªÙŠØ§Ø±ÙŠ)" class="w-full px-3 py-2 border border-slate-200 rounded-xl outline-none focus:border-emerald-500 font-medium text-xs">
                        <div class="relative w-full">
                            <input type="file" name="file_path" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" title="Ø¥Ø±ÙØ§Ù‚ Ù…Ù„Ù">
                            <div class="w-full px-3 py-2 border-2 border-dashed border-slate-200 rounded-xl bg-white text-slate-500 font-medium flex items-center justify-center gap-2 pointer-events-none text-[10px]">
                                <i class="fa-solid fa-paperclip"></i> Ø¥Ø±ÙØ§Ù‚ Ù…Ù„Ù Ø§Ù„Ø§Ø®ØªØ¨Ø§Ø±
                            </div>
                        </div>
                    </div>
                    <input type="text" name="notes" placeholder="Ù…Ù„Ø§Ø­Ø¸Ø§Øª Ø¥Ø¶Ø§ÙÙŠØ© Ø¹Ù† Ø§Ù„Ø§Ø®ØªØ¨Ø§Ø± (Ø§Ø®ØªÙŠØ§Ø±ÙŠ)" class="w-full px-3 py-2 border border-slate-200 rounded-xl outline-none focus:border-emerald-500 font-medium text-xs">
                    <button type="submit" class="w-full py-2 bg-slate-800 hover:bg-slate-900 text-white rounded-xl font-bold transition">
                        Ø­ÙØ¸ Ø§Ù„Ø§Ø®ØªØ¨Ø§Ø±
                    </button>
                </form>
            </div>

            <div class="space-y-1 text-xs">
                <span class="text-slate-400 font-bold">Ø§Ù„Ø£Ø¬Ù‡Ø²Ø© Ø§Ù„Ù…Ø³Ø§Ø¹Ø¯Ø© Ø§Ù„Ù…Ø³ØªØ®Ø¯Ù…Ø©:</span>
                <p class="p-3 rounded-2xl bg-slate-50 text-slate-800 font-semibold">
                    {{ $child->assistive_devices ?? 'Ù„Ø§ ØªÙˆØ¬Ø¯ Ø£Ø¬Ù‡Ø²Ø© Ù…Ø³Ø§Ø¹Ø¯Ø©' }}
                </p>
            </div>

            <div class="space-y-1 text-xs">
                <span class="text-slate-400 font-bold">Ø§Ù„ØªØ§Ø±ÙŠØ® Ø§Ù„Ù…Ø±Ø¶ÙŠ ÙˆÙ…Ù„Ø§Ø­Ø¸Ø§Øª Ø§Ù„Ø­Ù…Ù„ ÙˆØ§Ù„ÙˆÙ„Ø§Ø¯Ø©:</span>
                <p class="p-3 rounded-2xl bg-slate-50 text-slate-700 font-medium leading-relaxed">
                    {{ $child->medical_notes ?? 'Ù„Ø§ ØªÙˆØ¬Ø¯ Ù…Ù„Ø§Ø­Ø¸Ø§Øª Ù…Ø±Ø¶ÙŠØ© Ø³Ø§Ø¨Ù‚Ø© Ù…Ø³Ø¬Ù„Ø©' }}
                </p>
            </div>
        </div>

    </div>

    <!-- ==================== ØªØ¨ÙˆÙŠØ¨ 3: Ø³Ø¬Ù„ Ø§Ù„Ø¬Ù„Ø³Ø§Øª ÙˆØ§Ù„ØªÙ‚Ø§Ø±ÙŠØ± ==================== -->
    <div x-show="activeTab === 'sessions'" class="space-y-4">
        
        <div class="flex items-center justify-between">
            <div>
                <h3 class="font-black text-lg text-slate-800">Ø³Ø¬Ù„ Ø§Ù„Ø¬Ù„Ø³Ø§Øª Ø§Ù„Ù…Ù†ÙØ°Ø© ÙˆØªÙ‚Ø§Ø±ÙŠØ± Ø§Ù„Ø£Ø®ØµØ§Ø¦ÙŠÙŠÙ†</h3>
                <p class="text-xs text-slate-400 font-semibold mt-0.5">ØªÙ‚Ø§Ø±ÙŠØ± Ø§Ù„Ø¬Ù„Ø³Ø§Øª Ø§Ù„Ù…ÙˆØ«Ù‚Ø© Ø¨ÙˆØ§Ø³Ø·Ø© Ø§Ù„Ø£Ø·Ø¨Ø§Ø¡ ÙˆØªÙØ§ØµÙŠÙ„ Ø§Ù„Ø§Ø³ØªØ¬Ø§Ø¨Ø©</p>
            </div>

            @if(Auth::check() && Auth::user()->role === 'specialist')
            <a href="{{ route('doctor.sessions.create', ['child_id' => $child->id]) }}" class="px-4 py-2 rounded-2xl text-white font-bold text-xs shadow-md transition flex items-center gap-2 hover:opacity-95" style="background-color: #0d9488;">
                <i class="fa-solid fa-plus"></i>
                <span>ØªØ³Ø¬ÙŠÙ„ ØªÙ‚Ø±ÙŠØ± Ø¬Ù„Ø³Ø© Ø¬Ø¯ÙŠØ¯Ø©</span>
            </a>
            @endif
        </div>

        <div class="space-y-4">
            @foreach($recentSessions as $sess)
            <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm space-y-4">
                
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-slate-100">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl flex items-center justify-center text-white text-sm" style="background-color: #0d9488;">
                            <i class="fa-solid fa-calendar-day"></i>
                        </div>
                        <div>
                            <h4 class="font-black text-sm text-slate-800">{{ $sess['date'] }}</h4>
                            <p class="text-[11px] text-slate-400 font-mono font-semibold">{{ $sess['time'] }} â€¢ {{ $sess['room'] }}</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        <span class="px-3 py-1 rounded-xl text-xs font-black bg-emerald-100 text-emerald-800">
                            Ø§Ø³ØªØ¬Ø§Ø¨Ø© Ø§Ù„Ø·ÙÙ„: {{ $sess['mood'] }}
                        </span>

                        @if($sess['has_video'])
                        <button type="button" @click="openVideo('{{ $sess['video_path'] ?? '#' }}', 'ÙÙŠØ¯ÙŠÙˆ Ø¬Ù„Ø³Ø© {{ $sess['date'] }}')" class="px-3 py-1 rounded-xl text-xs font-black bg-purple-50 text-purple-700 hover:bg-purple-600 hover:text-white transition flex items-center gap-1.5 shadow-2xs">
                            <i class="fa-solid fa-play text-[10px]"></i>
                            <span>Ù…Ø´Ø§Ù‡Ø¯Ø© Ø§Ù„ÙÙŠØ¯ÙŠÙˆ ({{ $sess['video_duration'] }})</span>
                        </button>
                        @endif
                    </div>
                </div>

                <!-- Ù†Øµ ØªÙ‚Ø±ÙŠØ± Ø§Ù„Ø¬Ù„Ø³Ø© ÙˆØ§Ù„ØªÙ…Ø±ÙŠÙ† Ø§Ù„Ù…Ù†Ø²Ù„ÙŠ -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs font-medium">
                    <div class="p-3.5 rounded-2xl bg-slate-50 space-y-1">
                        <span class="text-slate-400 font-bold block text-[11px]"><i class="fa-regular fa-file-lines ml-1 text-teal-600"></i> ØªÙ‚Ø±ÙŠØ± Ø§Ù„Ø£Ø®ØµØ§Ø¦ÙŠ Ø¹Ù† Ø§Ù„Ø¬Ù„Ø³Ø©:</span>
                        <p class="text-slate-700 leading-relaxed font-semibold">{{ $sess['notes'] }}</p>
                    </div>

                    <div class="p-3.5 rounded-2xl bg-amber-50/70 border border-amber-100 space-y-1">
                        <span class="text-amber-900 font-bold block text-[11px]"><i class="fa-solid fa-house-user ml-1 text-amber-600"></i> ØªÙ…Ø±ÙŠÙ† Ù…Ù†Ø²Ù„ÙŠ Ù…Ø·Ù„ÙˆØ¨ Ù…Ù† ÙˆÙ„ÙŠ Ø§Ù„Ø£Ù…Ø±:</span>
                        <p class="text-amber-950 font-semibold leading-relaxed">{{ $sess['home_exercise'] ?? 'Ù…ØªØ§Ø¨Ø¹Ø© Ø§Ù„ØªÙˆØ¬ÙŠÙ‡Ø§Øª Ø§Ù„Ø¹Ø§Ù…Ø©' }}</p>
                    </div>
                </div>

                <!-- Ø§Ù„Ø£Ù‡Ø¯Ø§Ù Ø§Ù„ØªÙŠ ØªÙ… ØªÙ‚ÙŠÙŠÙ…Ù‡Ø§ ÙÙŠ Ù‡Ø°Ù‡ Ø§Ù„Ø¬Ù„Ø³Ø© -->
                @if(!empty($sess['goals']) && is_array($sess['goals']))
                <div class="space-y-2">
                    <span class="text-xs font-bold text-slate-500 flex items-center gap-1.5">
                        <i class="fa-solid fa-bullseye text-teal-600"></i>
                        Ø§Ù„Ø£Ù‡Ø¯Ø§Ù Ø§Ù„ØªÙŠ ØªÙ… Ø§Ù„ØªØ¯Ø±ÙŠØ¨ Ø¹Ù„ÙŠÙ‡Ø§ ÙˆØªÙ‚ÙŠÙŠÙ…Ù‡Ø§ ({{ count($sess['goals']) }} Ø£Ù‡Ø¯Ø§Ù):
                    </span>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                        @foreach($sess['goals'] as $g)
                        @php
                            $goalText = is_array($g) ? ($g['text'] ?? 'Ù‡Ø¯Ù') : $g;
                            $goalPct = is_array($g) ? (int)($g['percentage'] ?? 0) : 0;
                            $pctColor = $goalPct >= 100 ? 'emerald' : ($goalPct >= 50 ? 'blue' : ($goalPct >= 25 ? 'amber' : 'rose'));
                        @endphp
                        <div class="p-3 rounded-xl bg-slate-50 border border-slate-100 space-y-2">
                            <p class="text-xs font-bold text-slate-800">{{ $goalText }}</p>
                            @if(is_array($g) && isset($g['percentage']))
                            <div class="flex items-center gap-2">
                                <div class="flex-1 h-2 bg-slate-200 rounded-full overflow-hidden">
                                    <div class="h-full bg-{{ $pctColor }}-500 rounded-full transition-all duration-500" style="width: {{ $goalPct }}%"></div>
                                </div>
                                <span class="text-[10px] font-black text-{{ $pctColor }}-600 whitespace-nowrap">{{ $goalPct }}%</span>
                            </div>
                            @endif
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <div class="pt-1 flex items-center justify-between text-[11px] text-slate-400 font-medium">
                    <span>Ø§Ù„Ø£Ø®ØµØ§Ø¦ÙŠ Ø§Ù„Ù…Ù†ÙØ°: <strong class="text-slate-700">{{ $sess['specialist'] }}</strong></span>
                </div>

            </div>
            @endforeach
        </div>
    </div>

    <!-- ==================== ØªØ¨ÙˆÙŠØ¨ 4: Ù…Ù„Ø§Ø­Ø¸Ø§Øª ÙˆØªÙˆØ§ØµÙ„ ÙˆÙ„ÙŠ Ø§Ù„Ø£Ù…Ø± Ø§Ù„Ù…Ø¨Ø§Ø´Ø± ==================== -->
    <div x-show="activeTab === 'parent_notes'" class="space-y-5">
        <h3 class="font-black text-lg text-slate-800">Ø­Ø§Ø¦Ø· ØªÙˆØ§ØµÙ„ ÙˆÙ…Ù„Ø§Ø­Ø¸Ø§Øª ÙˆÙ„ÙŠ Ø§Ù„Ø£Ù…Ø± Ø§Ù„Ù…Ø¨Ø§Ø´Ø±Ø©</h3>

        <div class="space-y-4">
            @foreach($parentNotes as $pnote)
            <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm space-y-4">
                
                <!-- Ø±Ø³Ø§Ù„Ø© ÙˆÙ„ÙŠ Ø§Ù„Ø£Ù…Ø± -->
                <div class="p-4 rounded-2xl bg-purple-50/60 border border-purple-100 space-y-2">
                    <div class="flex items-center justify-between text-xs">
                        <span class="font-black text-purple-950">{{ $pnote['author'] }} ({{ $pnote['relation'] }})</span>
                        <span class="text-[11px] text-slate-400">{{ $pnote['date'] }}</span>
                    </div>
                    <p class="text-xs text-purple-900 leading-relaxed font-semibold">{{ $pnote['text'] }}</p>
                </div>

                <!-- Ø±Ø¯ Ø§Ù„Ø¯ÙƒØªÙˆØ± Ø§Ù„Ù…ØªØ§Ø¨Ø¹ -->
                <div class="p-4 rounded-2xl bg-emerald-50/60 border border-emerald-100 space-y-2 mr-6">
                    <div class="flex items-center justify-between text-xs">
                        <span class="font-black text-emerald-950"><i class="fa-solid fa-reply ml-1"></i> Ø±Ø¯ {{ $pnote['doctor'] }}</span>
                        <span class="text-[10px] text-emerald-700 font-bold">ØªÙ… Ø§Ù„Ø±Ø¯</span>
                    </div>
                    <p class="text-xs text-emerald-900 leading-relaxed font-semibold">{{ $pnote['reply'] }}</p>
                </div>

            </div>
            @endforeach
        </div>
    </div>

    <!-- Ù†Ø§ÙØ°Ø© ØªØ´ØºÙŠÙ„ Ø§Ù„ÙÙŠØ¯ÙŠÙˆ Ø§Ù„Ù…Ù†Ø¨Ø«Ù‚Ø© (Video Player Modal) -->
    <div x-show="videoModalOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/80 backdrop-blur-xs">
        <div @click.outside="videoModalOpen = false" class="bg-white rounded-3xl shadow-2xl border border-slate-100 max-w-lg w-full p-6 space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div class="flex items-center gap-2 text-purple-700 font-bold text-sm">
                    <i class="fa-solid fa-video"></i>
                    <span x-text="activeVideoTitle"></span>
                </div>
                <button @click="videoModalOpen = false" class="text-slate-400 hover:text-slate-600 text-lg">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <div class="rounded-2xl overflow-hidden bg-slate-950 flex items-center justify-center aspect-video">
                <template x-if="activeVideoSrc && activeVideoSrc !== '#' && !isYoutube(activeVideoSrc)">
                    <video :src="activeVideoSrc" controls class="w-full h-full object-contain" autoplay></video>
                </template>
                <template x-if="activeVideoSrc && activeVideoSrc !== '#' && isYoutube(activeVideoSrc)">
                    <iframe :src="'https://www.youtube.com/embed/' + getYoutubeId(activeVideoSrc) + '?autoplay=1'" title="YouTube video" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen class="w-full h-full"></iframe>
                </template>
                <template x-if="!activeVideoSrc || activeVideoSrc === '#'">
                    <div class="text-center text-white/60 p-6 space-y-2">
                        <i class="fa-solid fa-play text-4xl text-purple-400"></i>
                        <p class="text-xs font-bold">Ù…Ø¹Ø§ÙŠÙ†Ø© ÙÙŠØ¯ÙŠÙˆ Ø§Ù„Ø¬Ù„Ø³Ø© Ø§Ù„ØªÙˆØ«ÙŠÙ‚ÙŠ</p>
                    </div>
                </template>
            </div>

            <div class="pt-2 flex justify-end">
                <button @click="videoModalOpen = false" class="px-5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs">Ø¥ØºÙ„Ø§Ù‚</button>
            </div>
        </div>
    </div>

</div>
@endsection


