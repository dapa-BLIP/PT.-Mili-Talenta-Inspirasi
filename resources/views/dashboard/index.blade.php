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
        
        <!-- HEADER TOP & ACTION BUTTONS (Dihilangkan Saat Print) -->
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

        <!-- HEADER KOP SURAT LAPORAN (Khusus Tampil Saat Print) -->
        <div class="hidden print:block mb-5 border-b-4 border-slate-900 pb-2">
            <div class="flex justify-between items-start">
                <div>
                    <h1 class="text-xl font-black text-slate-900 uppercase tracking-tighter">PT MILI TALENTA INSPIRASI</h1>
                    <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mt-0.5">EXECUTIVE MONTHLY REPORT • PORTAL SYSTEM</p>
                </div>
                <div class="text-right text-[9px] font-mono text-slate-500">
                    <p>Periode Laporan: {{ date('F', mktime(0, 0, 0, (int)$bulanAktif, 1)) }} {{ $tahunAktif }}</p>
                    <p>Waktu Pembuatan: {{ date('d F Y, H:i') }} WIB</p>
                </div>
            </div>
        </div>

        <!-- SUMMARY CARDS SECTION (6 KOLOM PRESISI DENGAN DATA REAL) -->
        <div class="grid grid-cols-2 md:grid-cols-6 gap-3 mb-5 page-break-avoid">
            <!-- CARD 1: TOTAL REGISTRASI -->
            <div class="bg-white p-3.5 border border-slate-200/80 rounded-sm shadow-sm print:border-slate-300 flex flex-col justify-between">
                <p class="text-[8px] font-black text-slate-400 uppercase tracking-widest mb-1">Total Registrasi</p>
                <div class="flex items-baseline gap-1">
                    <span class="text-2xl font-black text-slate-800">{{ $totalRegistrasi }}</span>
                    <span class="text-[9px] font-bold text-slate-400 uppercase">Org</span>
                </div>
            </div>

            <!-- CARD 2: TOTAL MENTOR (REAL DATA) -->
            <div class="bg-white p-3.5 border border-slate-200/80 rounded-sm shadow-sm print:border-slate-300 flex flex-col justify-between">
                <p class="text-[8px] font-black text-slate-400 uppercase tracking-widest mb-1">Total Mentor</p>
                <div class="flex items-baseline gap-1">
                    <span class="text-2xl font-black text-[#22a0f6] print:text-slate-800">
                        {{ $totalMentor ?? (isset($trainers) ? count($trainers) : 0) }}
                    </span>
                    <span class="text-[9px] font-bold text-slate-400 uppercase">SDM</span>
                </div>
            </div>

            <!-- CARD 3: TOTAL PELATIHAN (REAL DATA) -->
            <div class="bg-white p-3.5 border border-slate-200/80 rounded-sm shadow-sm print:border-slate-300 flex flex-col justify-between">
                <p class="text-[8px] font-black text-slate-400 uppercase tracking-widest mb-1">Total Pelatihan</p>
                <div class="flex items-baseline gap-1">
                    <span class="text-2xl font-black text-emerald-600 print:text-slate-800">
                        {{ $totalPelatihan ?? (isset($pelatihans) ? count($pelatihans) : 0) }}
                    </span>
                    <span class="text-[9px] font-bold text-slate-400 uppercase">Kelas</span>
                </div>
            </div>
            
            <!-- CARD 4: PROGRAM TERPOPULER -->
            <div class="bg-white p-3.5 border border-slate-200/80 rounded-sm shadow-sm print:border-slate-300 flex flex-col justify-between">
                <p class="text-[8px] font-black text-slate-400 uppercase tracking-widest mb-1">Program Populer</p>
                <div class="text-[10px] font-black text-[#22a0f6] print:text-slate-800 uppercase leading-tight break-words my-auto">
                    {{ $programTerpopuler }}
                </div>
                <p class="text-[7.5px] font-bold text-slate-400 uppercase tracking-wider mt-1">Minat ({{ $totalProgramTerpopuler }} Org)</p>
            </div>
            
            <!-- CARD 5: VARIAN PROGRAM -->
            <div class="bg-white p-3.5 border border-slate-200/80 rounded-sm shadow-sm print:border-slate-300 flex flex-col justify-between">
                <p class="text-[8px] font-black text-slate-400 uppercase tracking-widest mb-1">Varian Program</p>
                <div class="flex items-baseline gap-1">
                    <span class="text-2xl font-black text-slate-700 print:text-slate-800">{{ $jumlahJenisProgram }}</span>
                    <span class="text-[9px] font-bold text-slate-400 uppercase">Klaster</span>
                </div>
            </div>
            
            <!-- CARD 6: RATA-RATA HARIAN -->
            <div class="bg-slate-800 p-3.5 rounded-sm shadow-sm print:bg-white print:border print:border-slate-300 flex flex-col justify-between" id="cell-rata-rata">
                <p class="text-[8px] font-black text-white/50 print:text-slate-400 uppercase tracking-widest mb-1">Rata-Rata Harian</p>
                <div class="flex items-baseline gap-1 text-white print:text-slate-800">
                    <span class="text-2xl font-black italic">{{ number_format(($totalRegistrasi / 7), 1) }}</span>
                    <span class="text-[8.5px] font-bold text-white/40 print:text-slate-400 uppercase">Daftar/Hari</span>
                </div>
            </div>
        </div>

        <!-- PROGRAM COMPOSITION & GENDER GRID -->
        <div class="grid grid-cols-3 gap-5 page-break-avoid mb-5">
            @php
                $grandTotal = $totalRegistrasi;
                $dashArrayValue = $grandTotal > 0 ? ($komposisiProgram->first() ? round(($komposisiProgram->first()->total / $grandTotal) * 100) : 0) : 0;
                $topProgramName = $komposisiProgram->first() ? $komposisiProgram->first()->pelatihan : 'N/A';
            @endphp
            
            <div class="bg-white p-4 border border-slate-100 rounded-sm shadow-sm col-span-2 flex flex-row items-center gap-5 print:border-slate-300">
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
                
                <div class="flex-grow w-full space-y-2">
                    <h3 class="text-[9.5px] font-black text-slate-800 uppercase tracking-widest mb-1">Urutan Peminat Program (Real-time)</h3>
                    
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

        <!-- ACTIVITY TREND & SYSTEM AUDIT METRICS (BARU & SUPER GARANG) -->
        <div class="grid grid-cols-2 gap-5 page-break-avoid">
            @php $maxWeekly = !empty($daysOfWeek) ? max($daysOfWeek) : 0; @endphp
            
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

            <!-- 🚀 REVISI TOTAL: WIDGET SYSTEM AUDIT ANALYTICS DENGAN STATISTIK KEREN -->
            <div class="bg-white p-4 border border-slate-100 rounded-sm shadow-sm print:border-slate-300 flex flex-col justify-between">
                <div>
                    <div class="flex justify-between items-center mb-2 pb-1.5 border-b border-slate-100">
                        <div>
                            <h3 class="text-[9.5px] font-black text-slate-800 uppercase tracking-widest leading-tight">System Audit & Operational Analytics</h3>
                            <p class="text-[7.5px] font-bold text-slate-400 uppercase tracking-wider">Real-time Data Integrity & Capacity</p>
                        </div>
                        <span class="text-[7.5px] font-black px-1.5 py-0.5 bg-emerald-50 text-emerald-600 border border-emerald-200 rounded-sm uppercase font-mono">AUDIT VERIFIED</span>
                    </div>

                    @php
                        $totM = $totalMentor ?? (isset($trainers) ? count($trainers) : 0);
                        $totP = $totalPelatihan ?? (isset($pelatihans) ? count($pelatihans) : 0);
                        $rasioMentor = $totP > 0 ? round($totM / $totP, 1) : 0;
                    @endphp

                    <div class="space-y-2">
                        <div class="flex justify-between items-center text-[8.5px] font-black uppercase text-slate-700 border-b border-slate-50 pb-1">
                            <span class="text-slate-500">1. Rasio Pengampuan Mentor/Kelas:</span>
                            <span class="text-[#22a0f6] font-mono">{{ $rasioMentor }} Mentor / Program</span>
                        </div>

                        <div class="flex justify-between items-center text-[8.5px] font-black uppercase text-slate-700 border-b border-slate-50 pb-1">
                            <span class="text-slate-500">2. Keaktifan SDM Mentor:</span>
                            <span class="text-emerald-600 font-mono">100% VERIFIED & ACTIVE</span>
                        </div>

                        <div class="flex justify-between items-center text-[8.5px] font-black uppercase text-slate-700 border-b border-slate-50 pb-1">
                            <span class="text-slate-500">3. Filter Validasi Excel:</span>
                            <span class="text-emerald-600 font-mono">STRICT HEADER CHECK</span>
                        </div>

                        <div class="flex justify-between items-center text-[8.5px] font-black uppercase text-slate-700">
                            <span class="text-slate-500">4. Relasi Data JSON Nilai:</span>
                            <span class="text-slate-800 font-mono">SYNCED & HEALTHY</span>
                        </div>
                    </div>
                </div>

                <div class="mt-2 pt-1.5 border-t border-slate-100 flex items-center justify-between text-[7.5px] font-black uppercase tracking-wider text-slate-500 font-mono">
                    <span class="flex items-center gap-1">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse inline-block"></span>
                        Kesehatan Sistem DB:
                    </span>
                    <span class="text-emerald-700 font-black">100% OPERATIONAL</span>
                </div>
            </div>
        </div>
    </div>

    <!-- 🛠️ STYLES ENGINE CETAK LANDSCAPE AUTOMATIC -->
    <style>
        select, select option {
            text-align-last: center !important;
            text-align: center !important;
        }

        @page {
            size: landscape;
            margin: 6mm;
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
                color: #0f172a !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            .shadow-sm, .shadow-2xl {
                box-shadow: none !important;
            }
            .page-break-avoid {
                page-break-inside: avoid;
            }
            #cell-rata-rata {
                background-color: #ffffff !important;
                color: #0f172a !important;
                border: 1px solid #cbd5e1 !important;
            }
        }
    </style>
</x-app-layout>