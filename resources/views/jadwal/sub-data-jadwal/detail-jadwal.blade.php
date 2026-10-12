<!-- ========================================================================= -->
<!-- MODAL: DETAIL JADWAL PELATIHAN (HEADER #1e293b - PROPORTIONAL CARD)      -->
<!-- ========================================================================= -->
<div id="modalDetailJadwal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-[9999] hidden flex items-center justify-center p-3 sm:p-4 print:hidden select-none overflow-y-auto">
    
    <!-- Backdrop Statis -->
    <div class="fixed inset-0" onclick="closeDetailJadwal()"></div>

    <div class="relative w-full max-w-[94vw] sm:max-w-2xl bg-white border border-slate-200 shadow-2xl rounded-sm overflow-hidden flex flex-col z-10 my-auto">
        
        <!-- Header Modal Warna #1e293b -->
        <div class="px-5 py-3.5 bg-[#1e293b] text-white flex justify-between items-center select-none shrink-0 border-b border-slate-700">
            <div class="flex items-center gap-2.5 min-w-0 pr-2">
                <div class="w-8 h-8 rounded-sm bg-blue-500/20 border border-blue-400/40 flex items-center justify-center text-[#22a0f6] shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                </div>
                <div class="min-w-0">
                    <h3 id="detail_jadwal_judul" class="text-xs font-black text-white uppercase tracking-wider truncate">Detail Jadwal Pelatihan</h3>
                    <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">Informasi Lengkap</p>
                </div>
            </div>
            
            <div class="flex items-center gap-2 shrink-0">
                <span id="detail_jadwal_status_badge" class="px-2.5 py-1 text-[8.5px] font-black uppercase tracking-wider rounded-sm border">
                    STATUS
                </span>
                <button type="button" onclick="closeDetailJadwal()" class="w-7 h-7 flex items-center justify-center text-slate-400 hover:text-white hover:bg-slate-800 rounded-sm font-bold text-lg leading-none transition-colors border-none bg-transparent cursor-pointer">&times;</button>
            </div>
        </div>

        <!-- Body Modal Detail -->
        <div class="p-5 space-y-4 bg-slate-50/50">
            
            <!-- Grid Info Card -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                
                <!-- Lead Trainer -->
                <div class="bg-white border border-slate-200 p-3 rounded-sm shadow-2xs">
                    <span class="text-[8px] font-black text-slate-400 uppercase tracking-widest block mb-1">Nama Mentor</span>
                    <div class="flex items-center gap-2">
                        <div class="w-6 h-6 rounded-xs bg-slate-100 border border-slate-200 flex items-center justify-center text-slate-600 font-bold text-[10px]">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7 7z" />
                            </svg>
                        </div>
                        <span id="detail_jadwal_trainer" class="text-[11px] font-black text-slate-700 uppercase tracking-tight truncate">-</span>
                    </div>
                </div>

                <!-- Jenis Pelaksanaan -->
                <div class="bg-white border border-slate-200 p-3 rounded-sm shadow-2xs">
                    <span class="text-[8px] font-black text-slate-400 uppercase tracking-widest block mb-1">Metode Pelaksanaan</span>
                    <div class="flex items-center gap-2">
                        <span id="detail_jadwal_jenis_badge" class="px-2 py-0.5 text-[9px] font-black uppercase rounded-xs tracking-wider border">
                            -
                        </span>
                        <span id="detail_jadwal_jenis_desc" class="text-[9.5px] font-bold text-slate-500 uppercase">-</span>
                    </div>
                </div>

                <!-- Periode Tanggal -->
                <div class="bg-white border border-slate-200 p-3 rounded-sm shadow-2xs">
                    <span class="text-[8px] font-black text-slate-400 uppercase tracking-widest block mb-1">Periode Tanggal</span>
                    <div class="flex items-center gap-1.5 text-[11px] font-black text-slate-700 font-mono">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-blue-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <span id="detail_jadwal_periode">-</span>
                    </div>
                </div>

                <!-- Jam Pelaksanaan WIB -->
                <div class="bg-white border border-slate-200 p-3 rounded-sm shadow-2xs">
                    <span class="text-[8px] font-black text-slate-400 uppercase tracking-widest block mb-1">Waktu Sesi (WIB)</span>
                    <div class="flex items-center gap-1.5 text-[11px] font-black text-slate-700 font-mono">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-blue-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span id="detail_jadwal_jam">-</span>
                    </div>
                </div>

                <!-- Deskripsi Kegiatan -->
                <div class="sm:col-span-2 bg-white border border-slate-200 p-3.5 rounded-sm shadow-2xs">
                    <span class="text-[8px] font-black text-slate-400 uppercase tracking-widest block mb-1.5">Deskripsi</span>
                    <p id="detail_jadwal_deskripsi" class="text-[11px] font-medium text-slate-700 leading-relaxed whitespace-pre-line">
                        -
                    </p>
                </div>
            </div>
        </div>

        <!-- Footer Modal Detail (Hanya tombol Tutup Detail, Tanpa Tombol Edit Sesuai Permintaan) -->
        <div class="px-5 py-3 bg-white border-t border-slate-200 flex justify-end items-center">
            <button type="button" onclick="closeDetailJadwal()" class="px-5 py-2 bg-[#1e293b] hover:bg-slate-900 text-white rounded-sm text-[9.5px] font-black uppercase tracking-wider transition-colors cursor-pointer border-none shadow-xs">
                Tutup Detail
            </button>
        </div>
    </div>
</div>

