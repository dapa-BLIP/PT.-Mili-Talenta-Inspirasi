<!-- ACTIVITY TREND & FEEDBACK RESOLUTION ANALYTICS -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-5 page-break-avoid">
    @php $maxWeekly = !empty($daysOfWeek) ? max($daysOfWeek) : 0; @endphp
    
    <!-- AKTIVITAS MINGGUAN -->
    <div class="bg-white p-4 border border-slate-100 rounded-sm shadow-sm print:border-slate-300">
        <h3 class="text-[9.5px] font-black text-slate-800 uppercase tracking-widest mb-3">Activity Trend (Weekly Registrations)</h3>
        <div class="flex items-end justify-between h-20 gap-2">
            @foreach(['Monday' => 'Mon', 'Tuesday' => 'Tue', 'Wednesday' => 'Wed', 'Thursday' => 'Thu', 'Friday' => 'Fri', 'Saturday' => 'Sat', 'Sunday' => 'Sun'] as $fullDay => $shortDay)
                @php
                    $dayCount = $daysOfWeek[$fullDay] ?? 0;
                    $barHeight = $maxWeekly > 0 ? ($dayCount / $maxWeekly) * 100 : 0;
                @endphp
                <div class="flex flex-col items-center flex-1 group relative h-full justify-end">
                    <span class="text-[8.5px] font-black text-slate-700 mb-0.5">{{ $dayCount }}</span>
                    <div class="w-full bg-slate-50 border border-slate-100 rounded-t-sm h-full flex items-end print:bg-slate-100">
                        <div class="w-full bg-[#22a0f6] print:bg-slate-700 rounded-t-sm" style="height: {{ $maxWeekly > 0 ? $barHeight : 0 }}%"></div>
                    </div>
                    <span class="text-[7.5px] font-black text-slate-400 mt-1 uppercase font-mono">{{ $shortDay }}</span>
                </div>
            @endforeach
        </div>
    </div>

    <!-- FEEDBACK & ISSUE RESOLUTION ANALYTICS (REAL DATA) -->
    <div class="bg-white p-4 border border-slate-100 rounded-sm shadow-sm print:border-slate-300 flex flex-col justify-between">
        <div>
            <div class="flex justify-between items-center mb-2 pb-1.5 border-b border-slate-100">
                <div>
                    <h3 class="text-[9.5px] font-black text-slate-800 uppercase tracking-widest leading-tight">Feedback & Issue Analytics</h3>
                    <p class="text-[7.5px] font-bold text-slate-400 uppercase tracking-wider">Monitoring Keluhan & Saran Peserta</p>
                </div>
                <span class="text-[7.5px] font-black px-1.5 py-0.5 {{ $pendingFeedback > 0 ? 'bg-amber-50 text-amber-600 border border-amber-200' : 'bg-emerald-50 text-emerald-600 border border-emerald-200' }} rounded-sm uppercase font-mono">
                    {{ $pendingFeedback > 0 ? $pendingFeedback . ' PENDING' : 'ALL RESOLVED' }}
                </span>
            </div>

            <div class="space-y-2">
                <div class="flex justify-between items-center text-[8.5px] font-black uppercase text-slate-700 border-b border-slate-50 pb-1">
                    <span class="text-slate-500">1. Total Feedback Masuk:</span>
                    <span class="text-slate-800 font-mono">{{ $totalFeedback }} Tiket</span>
                </div>

                <div class="flex justify-between items-center text-[8.5px] font-black uppercase text-slate-700 border-b border-slate-50 pb-1">
                    <span class="text-slate-500">2. Feedback Selesai Ditangani:</span>
                    <span class="text-emerald-600 font-mono">{{ $resolvedFeedback }} Resolved</span>
                </div>

                <div class="flex justify-between items-center text-[8.5px] font-black uppercase text-slate-700 border-b border-slate-50 pb-1">
                    <span class="text-slate-500">3. Menunggu Solusi (Pending):</span>
                    <span class="text-amber-600 font-mono">{{ $pendingFeedback }} Tiket</span>
                </div>

                <div class="flex justify-between items-center text-[8.5px] font-black uppercase text-slate-700">
                    <span class="text-slate-500">4. Tingkat Penyelesaian (Resolution Rate):</span>
                    <span class="text-[#22a0f6] font-mono">{{ $resolveRate }}%</span>
                </div>
            </div>
        </div>

        <div class="mt-2 pt-1.5 border-t border-slate-100 flex items-center justify-between text-[7.5px] font-black uppercase tracking-wider text-slate-500 font-mono">
            <span class="flex items-center gap-1">
                <span class="w-1.5 h-1.5 rounded-full {{ $pendingFeedback > 0 ? 'bg-amber-500 animate-pulse' : 'bg-emerald-500' }} inline-block"></span>
                Status Responsivitas:
            </span>
            <span class="{{ $pendingFeedback > 0 ? 'text-amber-600' : 'text-emerald-700' }} font-black">
                {{ $pendingFeedback > 0 ? 'ACTION NEEDED' : 'OPTIMAL / RESOLVED' }}
            </span>
        </div>
    </div>
</div>

