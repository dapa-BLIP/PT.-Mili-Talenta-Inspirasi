<!-- SUMMARY CARDS SECTION (6 KOLOM) -->
<div class="grid grid-cols-2 md:grid-cols-6 gap-3 mb-5 page-break-avoid">
    <!-- CARD 1: TOTAL PENDAFTARAN -->
    <div class="bg-white p-3.5 border border-slate-200/80 rounded-sm shadow-sm print:border-slate-300 flex flex-col justify-between">
        <p class="text-[8px] font-black text-slate-400 uppercase tracking-widest mb-1">Total Pendaftaran</p>
        <div class="flex items-baseline gap-1">
            <span class="text-2xl font-black text-slate-800">{{ $totalRegistrasi }}</span>
            <span class="text-[9px] font-bold text-slate-400 uppercase">Orang</span>
        </div>
    </div>

    <!-- CARD 2: TOTAL MENTOR -->
    <div class="bg-white p-3.5 border border-slate-200/80 rounded-sm shadow-sm print:border-slate-300 flex flex-col justify-between">
        <p class="text-[8px] font-black text-slate-400 uppercase tracking-widest mb-1">Total Mentor</p>
        <div class="flex items-baseline gap-1">
            <span class="text-2xl font-black text-[#22a0f6] print:text-slate-800">
                {{ $totalMentor ?? 0 }}
            </span>
            <span class="text-[9px] font-bold text-slate-400 uppercase">SDM</span>
        </div>
    </div>

    <!-- CARD 3: TOTAL PELATIHAN -->
    <div class="bg-white p-3.5 border border-slate-200/80 rounded-sm shadow-sm print:border-slate-300 flex flex-col justify-between">
        <p class="text-[8px] font-black text-slate-400 uppercase tracking-widest mb-1">Total Pelatihan</p>
        <div class="flex items-baseline gap-1">
            <span class="text-2xl font-black text-emerald-600 print:text-slate-800">
                {{ $totalPelatihan ?? 0 }}
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
        <p class="text-[7.5px] font-bold text-slate-400 uppercase tracking-wider mt-1">Minat ({{ $totalProgramTerpopuler }} Peserta)</p>
    </div>
    
    <!-- CARD 5: VARIAN PROGRAM -->
    <div class="bg-white p-3.5 border border-slate-200/80 rounded-sm shadow-sm print:border-slate-300 flex flex-col justify-between">
        <p class="text-[8px] font-black text-slate-400 uppercase tracking-widest mb-1">Varian Program</p>
        <div class="flex items-baseline gap-1">
            <span class="text-2xl font-black text-slate-700 print:text-slate-800">{{ $jumlahJenisProgram }}</span>
            <span class="text-[9px] font-bold text-slate-400 uppercase">Jenis</span>
        </div>
    </div>
    
    <!-- CARD 6: RATA-RATA HARIAN -->
    <div class="bg-slate-800 p-3.5 rounded-sm shadow-sm print:bg-white print:border print:border-slate-300 flex flex-col justify-between" id="cell-rata-rata">
        <p class="text-[8px] font-black text-white/50 print:text-slate-400 uppercase tracking-widest mb-1">Rata-Rata Harian</p>
        <div class="flex items-baseline gap-1 text-white print:text-slate-800">
            <span class="text-2xl font-black italic">{{ $totalRegistrasi > 0 ? number_format(($totalRegistrasi / 7), 1) : '0.0' }}</span>
            <span class="text-[8.5px] font-bold text-white/40 print:text-slate-400 uppercase">Daftar/Hari</span>
        </div>
    </div>
</div>

