<!-- PROGRAM COMPOSITION & GENDER GRID -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-5 page-break-avoid mb-5">
    @php
        $grandTotal = $totalRegistrasi;
        $dashArrayValue = $grandTotal > 0 ? ($komposisiProgram->first() ? round(($komposisiProgram->first()->total / $grandTotal) * 100) : 0) : 0;
        $topProgramName = $komposisiProgram->first() ? $komposisiProgram->first()->pelatihan : 'N/A';
    @endphp
    
    <!-- KOMPOSISI PROGRAM PELATIHAN -->
    <div class="bg-white p-4 border border-slate-100 rounded-sm shadow-sm col-span-1 lg:col-span-2 flex flex-col sm:flex-row items-center gap-5 print:border-slate-300">
        <div class="w-24 h-24 relative shrink-0 print:block">
            <svg viewBox="0 0 36 36" class="w-full h-full rotate-[-90deg]">
                <path class="text-slate-100" fill="none" stroke="currentColor" stroke-width="4" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"/>
                <path class="text-[#22a0f6] print:text-slate-800" fill="none" stroke="currentColor" stroke-width="4" stroke-dasharray="{{ $dashArrayValue }}, 100" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"/>
            </svg>
            <div class="absolute inset-0 flex items-center justify-center flex-col text-center p-1.5">
                <span class="text-base font-black text-slate-800">{{ $dashArrayValue }}%</span>
                <span class="text-[6px] font-bold uppercase text-slate-400 leading-tight break-words px-1">{{ $topProgramName }}</span>
            </div>
        </div>
        
        <div class="grow w-full space-y-2">
            <h3 class="text-[9.5px] font-black text-slate-800 uppercase tracking-widest mb-1">Peringkat Minat Program Pelatihan (Real-time)</h3>
            
            @forelse($komposisiProgram as $prog)
                @php $currPercent = $grandTotal > 0 ? round(($prog->total / $grandTotal) * 100) : 0; @endphp
                <div>
                    <div class="flex justify-between text-[8px] font-black mb-0.5 uppercase text-slate-700">
                        <span class="break-words">{{ $prog->pelatihan }} ({{ $prog->total }} Org)</span>
                        <span>{{ $currPercent }}%</span>
                    </div>
                    <div class="w-full bg-slate-50 border border-slate-100 h-1.5 rounded-full overflow-hidden print:bg-slate-100">
                        <div class="bg-[#22a0f6] print:bg-slate-700 h-full transition-all" style="width: {{ $currPercent }}%"></div>
                    </div>
                </div>
            @empty
                <p class="text-xs font-bold text-slate-400 italic">Belum ada data pendaftaran program.</p>
            @endforelse
        </div>
    </div>

    <!-- KOMPOSISI GENDER -->
    <div class="bg-white p-4 border border-slate-100 rounded-sm shadow-sm print:border-slate-300">
        <h3 class="text-[9.5px] font-black text-slate-800 uppercase tracking-widest mb-3">Gender Composition</h3>
        <div class="space-y-3">
            <div>
                <div class="flex justify-between text-[8.5px] font-black mb-1 uppercase text-slate-700">
                    <span>Pria</span>
                    <span>{{ $priaTotal }} Org ({{ $priaPercent }}%)</span>
                </div>
                <div class="w-full bg-slate-50 border border-slate-100 h-3 rounded-sm overflow-hidden print:bg-slate-100">
                    <div class="bg-[#22a0f6] print:bg-slate-700 h-full" style="width: {{ $priaPercent }}%"></div>
                </div>
            </div>
            <div>
                <div class="flex justify-between text-[8.5px] font-black mb-1 uppercase text-slate-700">
                    <span>Wanita</span>
                    <span>{{ $wanitaTotal }} Org ({{ $wanitaPercent }}%)</span>
                </div>
                <div class="w-full bg-slate-50 border border-slate-100 h-3 rounded-sm overflow-hidden print:bg-slate-100">
                    <div class="bg-slate-800 print:bg-slate-400 h-full" style="width: {{ $wanitaPercent }}%"></div>
                </div>
            </div>
        </div>
    </div>
</div>

