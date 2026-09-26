<!-- MODAL DETAIL PESERTA (BALANCED & ERGONOMIC) -->
<div id="modalDetailPeserta" class="fixed inset-0 bg-slate-900/40 backdrop-blur-xs z-[130] hidden flex items-center justify-center p-4 print:hidden transition-all duration-300">
    <div style="border-radius: 0.125rem;" class="bg-white w-full max-w-md shadow-2xl border border-slate-200 overflow-hidden">
        
        <!-- Header Soft Blue -->
        <div class="relative px-5 py-4 bg-gradient-to-r from-sky-50 via-blue-50/40 to-white border-b border-slate-100 flex items-center justify-between">
            <div class="flex items-center gap-3.5 min-w-0 pr-4">
                <!-- Avatar Kiri -->
                <div style="border-radius: 0.125rem;" class="w-13 h-13 shrink-0 bg-white border border-blue-200/80 shadow-xs flex items-center justify-center overflow-hidden">
                    <img id="detail_peserta_foto" src="" class="object-cover w-full h-full hidden">
                    <span id="detail_peserta_no_img" class="text-blue-400 font-black text-[9px] tracking-widest uppercase">NO_IMG</span>
                </div>
                
                <!-- Nama & Program Pelatihan -->
                <div class="min-w-0 flex-1 space-y-0.5">
                    <h4 id="detail_peserta_nama" class="text-sm font-black uppercase tracking-tight text-slate-800 truncate">Nama Lengkap</h4>
                    <p id="detail_peserta_sector" class="text-[10px] font-bold text-blue-500 uppercase tracking-wider truncate">Program Pelatihan</p>
                </div>
            </div>

            <!-- Tombol Close & Status Badge -->
            <div class="flex flex-col items-end gap-2 shrink-0">
                <button type="button" onclick="closeDetailPesertaModal()" class="text-slate-400 hover:text-slate-700 font-black text-xl leading-none">&times;</button>
                <span id="detail_peserta_status" style="border-radius: 0.125rem;" class="inline-block text-[8.5px] font-black uppercase px-2.5 py-0.5 tracking-wider shadow-2xs">
                    STATUS
                </span>
            </div>
        </div>
        
        <!-- Metadata Detail Grid -->
        <div class="p-4 bg-white">
            <div class="grid grid-cols-2 gap-2 text-[11px]">
                
                <!-- Email -->
                <div style="border-radius: 0.125rem;" class="col-span-2 bg-slate-50 border border-slate-100 px-3 py-2 flex items-center justify-between">
                    <span class="text-[9px] font-black text-slate-400 uppercase tracking-wider">Email Peserta</span>
                    <span id="detail_peserta_email" class="font-bold text-slate-700 lowercase truncate max-w-[240px]"></span>
                </div>

                <!-- Gender -->
                <div style="border-radius: 0.125rem;" class="bg-slate-50 border border-slate-100 px-3 py-2 flex items-center justify-between">
                    <span class="text-[9px] font-black text-slate-400 uppercase tracking-wider">Gender</span>
                    <span id="detail_peserta_gender" class="font-black text-slate-800 uppercase"></span>
                </div>

                <!-- WhatsApp Phone -->
                <div style="border-radius: 0.125rem;" class="bg-slate-50 border border-slate-100 px-3 py-2 flex items-center justify-between">
                    <span class="text-[9px] font-black text-slate-400 uppercase tracking-wider">WhatsApp</span>
                    <span id="detail_peserta_no_hp" class="font-black text-emerald-600 font-mono"></span>
                </div>

                <!-- TTL -->
                <div style="border-radius: 0.125rem;" class="col-span-2 bg-slate-50 border border-slate-100 px-3 py-2 flex items-center justify-between">
                    <span class="text-[9px] font-black text-slate-400 uppercase tracking-wider">Tempat, Tgl Lahir</span>
                    <span id="detail_peserta_ttl" class="font-bold text-slate-700 uppercase truncate max-w-[240px]"></span>
                </div>

                <!-- Perusahaan Mitra / Instansi -->
                <div style="border-radius: 0.125rem;" class="col-span-2 bg-blue-50/50 border border-blue-100/80 px-3 py-2 flex items-center justify-between">
                    <span class="text-[9px] font-black text-blue-500 uppercase tracking-wider">Perusahaan Mitra</span>
                    <span id="detail_peserta_kerja" class="font-black text-slate-800 uppercase truncate max-w-[240px]"></span>
                </div>

            </div>
        </div>

        <!-- Footer Dismiss -->
        <div class="px-4 py-2.5 bg-slate-50 border-t border-slate-100 flex justify-end">
            <button type="button" onclick="closeDetailPesertaModal()" style="border-radius: 0.125rem;" class="w-full py-2 bg-slate-800 hover:bg-slate-900 text-white text-[9.5px] font-black uppercase tracking-widest transition-colors shadow-2xs">
                TUTUP DETAIL
            </button>
        </div>

    </div>
</div>