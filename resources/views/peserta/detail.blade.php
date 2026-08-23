<!-- MODAL DETAIL PESERTA (USER-FRIENDLY TYPOGRAPHY & BALANCED COMPACT) -->
<div id="modalDetailPeserta" class="fixed inset-0 bg-slate-900/40 backdrop-blur-xs z-[100] hidden flex items-center justify-center p-4 print:hidden transition-all duration-300">
    <div style="border-radius: 0.125rem;" class="bg-white w-full max-w-md shadow-2xl border border-slate-200 overflow-hidden transform transition-all">
        
        <!-- HEADER SOFT BLUE RINGKAS & JELAS -->
        <div class="relative px-5 py-4 bg-gradient-to-r from-sky-50 via-blue-50/40 to-white border-b border-slate-100 flex items-center justify-between">
            <div class="flex items-center gap-3.5 min-w-0 pr-4">
                <!-- AVATAR KIRI -->
                <div style="border-radius: 0.125rem;" class="w-14 h-14 shrink-0 bg-white border border-blue-200/80 shadow-xs flex items-center justify-center overflow-hidden">
                    <img id="detail_peserta_foto" src="" class="object-cover w-full h-full hidden">
                    <span id="detail_peserta_no_img" class="text-blue-400 font-black text-[9px] tracking-widest uppercase">NO_IMG</span>
                </div>
                
                <!-- NAMA & SEKTOR -->
                <div class="min-w-0 flex-1 space-y-0.5">
                    <h4 id="detail_peserta_nama" class="text-sm font-black uppercase tracking-tight text-slate-800 truncate">Nama Lengkap</h4>
                    <p id="detail_peserta_sector" class="text-[10px] font-bold text-blue-500 uppercase tracking-wider truncate">Sektor Pelatihan</p>
                </div>
            </div>

            <!-- TOMBOL CLOSE & STATUS -->
            <div class="flex flex-col items-end gap-2 shrink-0">
                <button type="button" onclick="closeDetailPesertaModal()" class="text-slate-400 hover:text-slate-700 font-black text-xl leading-none transition-colors">&times;</button>
                <span id="detail_peserta_status" style="border-radius: 0.125rem;" class="inline-block text-[8.5px] font-black uppercase px-2.5 py-0.5 tracking-wider shadow-2xs">
                    STATUS
                </span>
            </div>
        </div>
        
        <!-- METADATA DENGAN FONT LEBIH BESAR & MUDAH DIBACA -->
        <div class="p-4 bg-white">
            <div class="grid grid-cols-2 gap-2 text-[11px]">
                
                <!-- EMAIL FULL ROW -->
                <div style="border-radius: 0.125rem;" class="col-span-2 bg-slate-50 border border-slate-100 px-3 py-2 flex items-center justify-between">
                    <span class="text-[9px] font-black text-slate-400 uppercase tracking-wider">Email</span>
                    <span id="detail_peserta_email" class="font-bold text-slate-700 lowercase truncate max-w-[240px]"></span>
                </div>

                <!-- GENDER -->
                <div style="border-radius: 0.125rem;" class="bg-slate-50 border border-slate-100 px-3 py-2 flex items-center justify-between">
                    <span class="text-[9px] font-black text-slate-400 uppercase tracking-wider">Gender</span>
                    <span id="detail_peserta_gender" class="font-black text-slate-800 uppercase"></span>
                </div>

                <!-- PHONE -->
                <div style="border-radius: 0.125rem;" class="bg-slate-50 border border-slate-100 px-3 py-2 flex items-center justify-between">
                    <span class="text-[9px] font-black text-slate-400 uppercase tracking-wider">N0.</span>
                    <span id="detail_peserta_no_hp" class="font-black text-slate-800"></span>
                </div>

                <!-- TTL FULL ROW -->
                <div style="border-radius: 0.125rem;" class="col-span-2 bg-slate-50 border border-slate-100 px-3 py-2 flex items-center justify-between">
                    <span class="text-[9px] font-black text-slate-400 uppercase tracking-wider">Tempat dan tanggal lahir</span>
                    <span id="detail_peserta_ttl" class="font-bold text-slate-700 uppercase truncate max-w-[240px]"></span>
                </div>

                <!-- TEMPAT KERJA FULL ROW -->
                <div style="border-radius: 0.125rem;" class="col-span-2 bg-slate-50 border border-slate-100 px-3 py-2 flex items-center justify-between">
                    <span class="text-[9px] font-black text-slate-400 uppercase tracking-wider">Company</span>
                    <span id="detail_peserta_kerja" class="font-black text-slate-800 uppercase truncate max-w-[240px]"></span>
                </div>

            </div>
        </div>

        <!-- FOOTER DISMISS -->
        <div class="px-4 py-3 bg-slate-50 border-t border-slate-100 flex justify-end">
            <button type="button" onclick="closeDetailPesertaModal()" style="border-radius: 0.125rem;" class="w-full py-2 bg-slate-800 hover:bg-slate-900 text-white text-[10px] font-black uppercase tracking-widest transition-colors shadow-2xs">
                TUTUP DETAIL
            </button>
        </div>

    </div>
</div>