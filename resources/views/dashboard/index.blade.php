<x-app-layout>
    <script src="https://cdn.jsdelivr.net/npm/exceljs@4.3.0/dist/exceljs.min.js"></script>
    @vite(['resources/js/report-export.js'])

    <div class="p-6 md:p-8 max-w-[1600px] mx-auto min-h-screen" 
         id="report-content"
         data-total-registrasi="{{ $totalRegistrasi }}"
         data-program-terpopuler="{{ $programTerpopuler }}"
         data-jumlah-program="{{ $jumlahJenisProgram }}"
         data-pria-total="{{ $priaTotal }}"
         data-pria-percent="{{ $priaPercent }}"
         data-wanita-total="{{ $wanitaTotal }}"
         data-wanita-percent="{{ $wanitaPercent }}"
         data-mon="{{ $daysOfWeek['Monday'] ?? 0 }}"
         data-tue="{{ $daysOfWeek['Tuesday'] ?? 0 }}"
         data-wed="{{ $daysOfWeek['Wednesday'] ?? 0 }}"
         data-thu="{{ $daysOfWeek['Thursday'] ?? 0 }}"
         data-fri="{{ $daysOfWeek['Friday'] ?? 0 }}"
         data-sat="{{ $daysOfWeek['Saturday'] ?? 0 }}"
         data-sun="{{ $daysOfWeek['Sunday'] ?? 0 }}"
         data-komposisi-program="{{ json_encode($komposisiProgram) }}">
        
        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-end gap-6 mb-8 print:hidden">
            <div>
                <h2 class="text-2xl font-black text-slate-800 tracking-tighter uppercase italic">Executive Monthly Report</h2>
                <p class="text-[10px] font-bold text-[#22a0f6] uppercase tracking-[0.2em]">System Overview & Data Analytics</p>
            </div>
            
            <div class="flex flex-wrap items-center gap-4 w-full lg:w-auto">
                <form method="GET" action="{{ url()->current() }}" class="flex items-center gap-3 bg-white h-10 px-4 border border-slate-200 rounded-sm shadow-sm transition-all focus-within:border-[#22a0f6]">
                    <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    
                    <select name="bulan" onchange="this.form.submit()" class="text-[10px] font-black uppercase tracking-wider border-none p-0 pr-6 text-center text-slate-700 bg-transparent focus:ring-0 focus:outline-none cursor-pointer">
                        @foreach(range(1, 12) as $m)
                            <option value="{{ sprintf('%02d', $m) }}" {{ $bulanAktif == sprintf('%02d', $m) ? 'selected' : '' }} class="text-center">
                                {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                            </option>
                        @endforeach
                    </select>
                    
                    <span class="text-slate-200 font-light select-none">/</span>

                    <select name="tahun" onchange="this.form.submit()" class="text-[10px] font-black uppercase tracking-wider border-none p-0 pr-6 text-center text-slate-700 bg-transparent focus:ring-0 focus:outline-none cursor-pointer">
                        @foreach(range(date('Y'), date('Y') - 4) as $y)
                            <option value="{{ $y }}" {{ $tahunAktif == $y ? 'selected' : '' }} class="text-center">
                                {{ $y }}
                            </option>
                        @endforeach
                    </select>
                </form>

                <div class="flex items-center gap-2 h-10">
                    <button onclick="exportToExcel()" class="flex items-center gap-2 px-5 h-full bg-emerald-600 hover:bg-emerald-700 text-white rounded-sm font-black text-[10px] uppercase tracking-widest transition shadow-sm cursor-pointer border-none">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h15.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        Export Excel
                    </button>

                    <button onclick="window.print()" class="flex items-center gap-2 px-5 h-full bg-slate-900 hover:bg-slate-800 text-white rounded-sm font-black text-[10px] uppercase tracking-widest transition shadow-sm cursor-pointer border-none">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                        Print PDF
                    </button>
                </div>
            </div>
        </div>

        <div class="hidden print:block mb-8 border-b-4 border-slate-900 pb-4">
            <div class="flex justify-between items-start">
                <div>
                    <h1 class="text-2xl font-black text-slate-900 uppercase tracking-tighter">PT MILI TALENTA INSPIRASI</h1>
                    <p class="text-xs font-bold text-slate-500 uppercase tracking-widest mt-0.5">EXECUTIVE MONTHLY REPORT • PORTAL SYSTEM</p>
                </div>
                <div class="text-right text-[10px] font-mono text-slate-500">
                    <p>Periode Laporan: {{ date('F', mktime(0, 0, 0, (int)$bulanAktif, 1)) }} {{ $tahunAktif }}</p>
                    <p>Waktu Pembuatan: {{ date('d F Y, H:i') }} WIB</p>
                </div>
            </div>
        </div>

        

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6 mb-6 page-break-avoid">
            <div class="bg-white p-6 border border-slate-100 rounded-sm shadow-sm print:border-slate-300">
                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-2">Total Registrasi Baru</p>
                <div class="flex items-baseline gap-1">
                    <span class="text-3xl font-black text-slate-800">{{ $totalRegistrasi }}</span>
                    <span class="text-[10px] font-bold text-slate-400 uppercase">Org</span>
                </div>
            </div>
            
            <div class="bg-white p-6 border border-slate-100 rounded-sm shadow-sm print:border-slate-300">
                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-2">Program Terpopuler</p>
                <div class="text-lg font-black text-[#22a0f6] print:text-slate-800 truncate uppercase mt-1">
                    {{ $programTerpopuler }}
                </div>
                <p class="text-[9px] font-bold text-slate-400 uppercase tracking-wider mt-0.5">Minat Terbanyak ({{ $totalProgramTerpopuler }} Org)</p>
            </div>
            
            <div class="bg-white p-6 border border-slate-100 rounded-sm shadow-sm print:border-slate-300">
                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-2">Varian Program Dibuka</p>
                <div class="flex items-baseline gap-1">
                    <span class="text-3xl font-black text-emerald-600 print:text-slate-800">{{ $jumlahJenisProgram }}</span>
                    <span class="text-[10px] font-bold text-slate-400 uppercase">Klaster</span>
                </div>
            </div>
            
            <div class="bg-slate-800 p-6 rounded-sm shadow-sm print:bg-white print:border print:border-slate-300" id="cell-rata-rata">
                <p class="text-[9px] font-black text-white/50 print:text-slate-400 uppercase tracking-widest mb-2">Rata-rata Harian</p>
                <div class="flex items-baseline gap-1 text-white print:text-slate-800">
                    <span class="text-3xl font-black italic">{{ number_format(($totalRegistrasi / 7), 1) }}</span>
                    <span class="text-[10px] font-bold text-white/40 print:text-slate-400 uppercase">Pendaftar/Hari</span>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 page-break-avoid">
            @php
                $grandTotal = $totalRegistrasi;
                $dashArrayValue = $grandTotal > 0 ? ($komposisiProgram->first() ? round(($komposisiProgram->first()->total / $grandTotal) * 100) : 0) : 0;
                $topProgramName = $komposisiProgram->first() ? $komposisiProgram->first()->pelatihan : 'N/A';
            @endphp
            
            <div class="bg-white p-6 border border-slate-100 rounded-sm shadow-sm col-span-1 lg:col-span-2 flex flex-col sm:flex-row items-center gap-8 print:border-slate-300">
                <div class="w-32 h-32 relative shrink-0 print:hidden">
                    <svg viewBox="0 0 36 36" class="w-full h-full rotate-[-90deg]">
                        <path class="text-slate-100" fill="none" stroke="currentColor" stroke-width="4" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"/>
                        <path class="text-[#22a0f6]" fill="none" stroke="currentColor" stroke-width="4" stroke-dasharray="{{ $dashArrayValue }}, 100" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"/>
                    </svg>
                    <div class="absolute inset-0 flex items-center justify-center flex-col text-center p-2">
                        <span class="text-xl font-black text-slate-800">{{ $dashArrayValue }}%</span>
                        <span class="text-[7px] font-bold uppercase text-slate-400 truncate max-w-[80px]">{{ $topProgramName }}</span>
                    </div>
                </div>
                
                <div class="flex-grow w-full space-y-3">
                    <h3 class="text-[10px] font-black text-slate-800 uppercase tracking-widest mb-2">Urutan Peminat Program (Real-time)</h3>
                    
                    @forelse($komposisiProgram as $prog)
                        @php $currPercent = $grandTotal > 0 ? round(($prog->total / $grandTotal) * 100) : 0; @endphp
                        <div>
                            <div class="flex justify-between text-[9px] font-black mb-1 uppercase text-slate-700">
                                <span>{{ $prog->pelatihan }} ({{ $prog->total }} Org)</span>
                                <span>{{ $currPercent }}%</span>
                            </div>
                            <div class="w-full bg-slate-50 border border-slate-100 h-2.5 rounded-full overflow-hidden print:bg-slate-100">
                                <div class="bg-[#22a0f6] print:bg-slate-700 h-full transition-all" style="width: {{ $currPercent }}%"></div>
                            </div>
                        </div>
                    @empty
                        <p class="text-xs font-bold text-slate-400 italic">Belum ada data pendaftaran program.</p>
                    @endforelse
                </div>
            </div>

            <div class="bg-white p-6 border border-slate-100 rounded-sm shadow-sm print:border-slate-300">
                <h3 class="text-[10px] font-black text-slate-800 uppercase tracking-widest mb-4">Gender Composition</h3>
                <div class="space-y-4">
                    <div>
                        <div class="flex justify-between text-[9px] font-black mb-1 uppercase text-slate-700">
                            <span>Pria</span>
                            <span>{{ $priaTotal }} Org ({{ $priaPercent }}%)</span>
                        </div>
                        <div class="w-full bg-slate-50 border border-slate-100 h-4 rounded-sm overflow-hidden print:bg-slate-100">
                            <div class="bg-[#22a0f6] print:bg-slate-700 h-full" style="width: {{ $priaPercent }}%"></div>
                        </div>
                    </div>
                    <div>
                        <div class="flex justify-between text-[9px] font-black mb-1 uppercase text-slate-700">
                            <span>Wanita</span>
                            <span>{{ $wanitaTotal }} Org ({{ $wanitaPercent }}%)</span>
                        </div>
                        <div class="w-full bg-slate-50 border border-slate-100 h-4 rounded-sm overflow-hidden print:bg-slate-100">
                            <div class="bg-slate-800 print:bg-slate-400 h-full" style="width: {{ $wanitaPercent }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6 page-break-avoid">
            @php $maxWeekly = !empty($daysOfWeek) ? max($daysOfWeek) : 0; @endphp
            
            <div class="bg-white p-6 border border-slate-100 rounded-sm shadow-sm print:border-slate-300">
                <h3 class="text-[10px] font-black text-slate-800 uppercase tracking-widest mb-6">Activity Trend (Weekly Registrations)</h3>
                <div class="flex items-end justify-between h-28 gap-2">
                    @foreach(['Monday' => 'Mon', 'Tuesday' => 'Tue', 'Wednesday' => 'Wed', 'Thursday' => 'Thu', 'Friday' => 'Fri', 'Saturday' => 'Sat', 'Sunday' => 'Sun'] as $fullDay => $shortDay)
                        @php
                            $dayCount = $daysOfWeek[$fullDay] ?? 0;
                            $barHeight = $maxWeekly > 0 ? ($dayCount / $maxWeekly) * 100 : 0;
                        @endphp
                        <div class="flex flex-col items-center flex-1 group relative h-full justify-end">
                            <span class="text-[9px] font-black text-slate-700 mb-1">{{ $dayCount }}</span>
                            <div class="w-full bg-slate-50 border border-slate-100 rounded-t-sm h-full flex items-end print:bg-slate-50">
                                <div class="w-full bg-[#22a0f6] print:bg-slate-600 rounded-t-sm" style="height: {{ $maxWeekly > 0 ? $barHeight : 0 }}%"></div>
                            </div>
                            <span class="text-[8px] font-black text-slate-400 mt-2 uppercase font-mono">{{ $shortDay }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="bg-white p-6 border border-slate-100 rounded-sm shadow-sm print:border-slate-300">
                <h3 class="text-[10px] font-black text-slate-800 uppercase tracking-widest mb-4">Recent Logged Status</h3>
                <div class="space-y-3">
                    @foreach(['TEFA Project A' => 'Completed', 'Maintenance' => 'Ongoing', 'Training' => 'Pending'] as $act => $status)
                    <div class="flex justify-between items-center border-b border-slate-100 pb-2.5 last:border-none">
                        <div>
                            <p class="text-[11px] font-black text-slate-700 uppercase">{{ $act }}</p>
                            <p class="text-[8px] font-bold text-slate-400 font-mono">Updated recently</p>
                        </div>
                        <span class="text-[8px] font-black px-2 py-0.5 rounded-sm border {{ $status == 'Completed' ? 'bg-emerald-50 text-emerald-600 border-emerald-200' : 'bg-amber-50 text-amber-600 border-amber-200' }} uppercase font-mono">{{ $status }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="mt-12 text-center text-slate-300 print:text-slate-500 border-t print:border-slate-200 pt-4">
            <p class="text-[9px] font-black uppercase tracking-[0.4em]">PT MILI SYSTEM • DEVELOPED BY DAFFA • POLITEKNIK MITRA INDUSTRI</p>
        </div>
    </div>

    <style>
        /* 💡 FIX DROPDOWN OPTION CENTER ALIGNMENT FOR CHROME/EDGE ENGINE */
        select, select option {
            text-align-last: center !important;
            text-align: center !important;
        }

        @media print {
            aside, header, nav, .print\:hidden { 
                display: none !important; 
            }
            body, main, #report-content {
                padding: 0 !important;
                margin: 0 !important;
                width: 100% !important;
                max-width: 100% !important;
                background: white !important;
                color: #1e293b !important;
            }
            .shadow-sm, .shadow-2xl {
                box-shadow: none !important;
            }
            .page-break-avoid {
                page-break-inside: avoid;
            }
            #cell-rata-rata {
                background-color: #ffffff !important;
                color: #1e293b !important;
                border: 1px solid #cbd5e1 !important;
            }
        }
    </style>
</x-app-layout>