<!-- HEADER TOP & ACTION BUTTONS -->
<div class="flex flex-col lg:flex-row justify-between items-start lg:items-end gap-6 mb-8 print:hidden">
    <div>
        <h2 class="text-2xl font-black text-slate-800 tracking-tighter uppercase italic">Laporan Bulanan</h2>
        <p class="text-[10px] font-bold text-[#22a0f6] uppercase tracking-[0.2em]">Ringkasan Sistem & Analisis Data</p>
    </div>
    
    <div class="flex flex-col sm:flex-row sm:items-center gap-3 sm:gap-4 w-full lg:w-auto">
        <form method="GET" action="{{ url()->current() }}" class="flex items-center justify-center gap-3 bg-white h-10 px-4 border border-slate-200 rounded-sm shadow-sm transition-all focus-within:border-[#22a0f6] w-full sm:w-auto">
            <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
            </svg>
            
            <select name="bulan" onchange="this.form.submit()" class="text-[10px] font-black uppercase tracking-wider border-none p-0 pr-6 text-center text-slate-700 bg-transparent focus:ring-0 focus:outline-none cursor-pointer">
                @foreach(range(1, 12) as $m)
                    <option value="{{ sprintf('%02d', $m) }}" {{ $bulanAktif == sprintf('%02d', $m) ? 'selected' : '' }}>
                        {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                    </option>
                @endforeach
            </select>
            
            <span class="text-slate-200 font-light select-none">/</span>

            <select name="tahun" onchange="this.form.submit()" class="text-[10px] font-black uppercase tracking-wider border-none p-0 pr-6 text-center text-slate-700 bg-transparent focus:ring-0 focus:outline-none cursor-pointer">
                @foreach(range(date('Y'), date('Y') - 4) as $y)
                    <option value="{{ $y }}" {{ $tahunAktif == $y ? 'selected' : '' }}>
                        {{ $y }}
                    </option>
                @endforeach
            </select>
        </form>

        <div class="grid grid-cols-2 sm:flex sm:items-center gap-2 h-10 w-full sm:w-auto">
            <button onclick="exportToExcel()" class="flex items-center justify-center gap-2 px-4 sm:px-5 h-full bg-emerald-600 hover:bg-emerald-700 text-white rounded-sm font-black text-[10px] uppercase tracking-widest transition shadow-sm cursor-pointer border-none w-full sm:w-auto">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h15.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                Export Excel
            </button>

            <button onclick="window.print()" class="flex items-center justify-center gap-2 px-4 sm:px-5 h-full bg-slate-900 hover:bg-slate-800 text-white rounded-sm font-black text-[10px] uppercase tracking-widest transition shadow-sm cursor-pointer border-none w-full sm:w-auto">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                Print PDF
            </button>
        </div>
    </div>
</div>

<!-- HEADER KOP SURAT LAPORAN (Khusus Cetak) -->
<div class="hidden print:block mb-5 border-b-4 border-slate-900 pb-2">
    <div class="flex justify-between items-start">
        <div>
            <h1 class="text-xl font-black text-slate-900 uppercase tracking-tighter">PT MILI TALENTA INSPIRASI</h1>
            <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mt-0.5">Laporan Bulanan • PORTAL SYSTEM</p>
        </div>
        <div class="text-right text-[9px] font-mono text-slate-500">
            <p>Periode: {{ date('F', mktime(0, 0, 0, (int)$bulanAktif, 1)) }} {{ $tahunAktif }}</p>
            <p>Waktu Pembuatan: {{ date('d F Y, H:i') }} WIB</p>
        </div>
    </div>
</div>

