<div id="modalDetailPeserta" class="fixed inset-0 bg-slate-900/40 backdrop-blur-md z-[100] hidden flex items-center justify-center p-4 print:hidden transition-all duration-300">
    <div class="bg-white w-full max-w-md shadow-2xl border border-slate-200 overflow-hidden rounded-none transform transition-all">
        
        <div class="relative px-6 py-5 bg-[#22a0f6] text-white overflow-hidden rounded-none">
            
            <div class="relative flex justify-between items-start">
                <div class="flex items-center gap-2">
                    <span class="text-[8px] font-black uppercase tracking-widest bg-white text-[#22a0f6] px-2 py-0.5 rounded-none">Identity</span>
                    <h3 class="text-[10px] font-black uppercase tracking-widest text-blue-50">Verified Participant Card</h3>
                </div>
                <button type="button" onclick="closeDetailPesertaModal()" class="text-blue-100 hover:text-white font-bold text-xl leading-none transition-colors duration-150">&times;</button>
            </div>

            <div class="relative flex items-center gap-5 mt-5">
                <div class="w-20 h-20 shrink-0 bg-white/10 border-2 border-white/20 flex items-center justify-center overflow-hidden rounded-none shadow-md">
                    <img id="detail_peserta_foto" src="" class="object-cover w-full h-full hidden">
                    <span id="detail_peserta_no_img" class="text-blue-100 font-black text-[10px] tracking-widest uppercase">NO_IMG</span>
                </div>
                
                <div class="space-y-0.5 min-w-0 flex-1">
                    <h4 id="detail_peserta_nama" class="text-base font-black uppercase tracking-tight text-white truncate">Nama Lengkap</h4>
                    <p id="detail_peserta_sector" class="text-[10px] font-black text-slate-900/80 uppercase tracking-widest italic truncate">Sektor Pelatihan</p>
                    
                    <div class="pt-1">
                        <span id="detail_peserta_status" class="inline-block text-[8px] font-black uppercase px-2 py-0.5 tracking-wider rounded-none">
                            STATUS
                        </span>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="bg-white p-6 space-y-4">
            <h5 class="text-[9px] font-black text-slate-400 uppercase tracking-widest border-b border-slate-100 pb-1.5">Core Record Metadata</h5>
            
            <div class="grid grid-cols-2 gap-4">
                <div class="col-span-2 bg-slate-50 border border-slate-100 p-3 flex flex-col justify-between">
                    <span class="text-[8px] font-black text-slate-400 uppercase tracking-wider mb-1">Email Address</span>
                    <span id="detail_peserta_email" class="text-[11px] text-slate-700 font-bold break-all lowercase"></span>
                </div>

                <div class="bg-slate-50 border border-slate-100 p-3 flex flex-col justify-between">
                    <span class="text-[8px] font-black text-slate-400 uppercase tracking-wider mb-1">Gender</span>
                    <span id="detail_peserta_gender" class="text-[11px] text-slate-800 font-black uppercase tracking-wide"></span>
                </div>

                <div class="bg-slate-50 border border-slate-100 p-3 flex flex-col justify-between">
                    <span class="text-[8px] font-black text-slate-400 uppercase tracking-wider mb-1">Phone Number</span>
                    <span id="detail_peserta_no_hp" class="text-[11px] text-slate-800 font-black tracking-wide"></span>
                </div>

                <div class="col-span-2 bg-slate-50 border border-slate-100 p-3 flex flex-col justify-between">
                    <span class="text-[8px] font-black text-slate-400 uppercase tracking-wider mb-1">Birth Info</span>
                    <span id="detail_peserta_ttl" class="text-[11px] text-slate-800 font-bold uppercase tracking-tighter"></span>
                </div>

                <div class="col-span-2 bg-slate-50 border border-slate-100 p-3 flex flex-col justify-between">
                    <span class="text-[8px] font-black text-slate-400 uppercase tracking-wider mb-1">Workplace / Institution</span>
                    <span id="detail_peserta_kerja" class="text-[11px] text-slate-800 font-black uppercase tracking-tight"></span>
                </div>
            </div>
        </div>

        <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex justify-end">
            <button type="button" onclick="closeDetailPesertaModal()" class="px-5 py-2 border border-slate-200 bg-white text-slate-600 rounded-none text-[9px] font-black uppercase tracking-widest hover:bg-[#22a0f6] hover:text-white hover:border-[#22a0f6] transition-all duration-150 shadow-sm">
                DISMISS PROFILE
            </button>
        </div>

    </div>
</div>