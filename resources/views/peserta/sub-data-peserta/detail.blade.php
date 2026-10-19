<!-- ========================================================================= -->
<!-- MODAL DETAIL PESERTA (3D FLIP LANDSCAPE CARD - FULL VIEWPORT BLUR)        -->
<!-- ========================================================================= -->
<div id="modalDetailPeserta" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-[9999] hidden flex items-center justify-center p-3 sm:p-4 print:hidden select-none overflow-y-auto">
    
    <!-- Backdrop Statis Fullscreen -->
    <div class="fixed inset-0" onclick="closeDetailPesertaModal()"></div>

    <div class="relative w-full max-w-[94vw] sm:max-w-[520px] [perspective:1200px] z-[205] my-auto">
        
        <div id="cardFlipperPeserta" class="relative w-full transition-transform duration-700 [transform-style:preserve-3d] shadow-2xl rounded-sm">
            
            <!-- SISI DEPAN -->
            <div class="w-full bg-white border border-slate-200 rounded-sm overflow-hidden flex flex-col [backface-visibility:hidden]">
                
                <div class="px-5 py-3.5 bg-slate-800 text-white flex items-center justify-between shrink-0">
                    <div class="flex items-center gap-3 min-w-0 pr-2">
                        <div class="w-11 h-11 shrink-0 bg-slate-700 border border-slate-600 rounded-xs flex items-center justify-center overflow-hidden shadow-xs">
                            <img id="detail_peserta_foto" src="" class="object-cover w-full h-full hidden" alt="Foto Peserta">
                            <span id="detail_peserta_no_img" class="text-slate-400 font-black text-[8px] tracking-widest uppercase">NO_IMG</span>
                        </div>
                        
                        <div class="min-w-0 flex-1 space-y-0.5">
                            <h4 id="detail_peserta_nama" class="text-xs font-black uppercase tracking-tight text-white truncate">Nama Peserta</h4>
                            <p id="detail_peserta_sector" class="text-[9.5px] font-bold text-[#22a0f6] uppercase tracking-wider truncate">Program Pelatihan</p>
                        </div>
                    </div>

                    <div class="flex flex-col items-end gap-1.5 shrink-0">
                        <button type="button" 
                                onclick="closeDetailPesertaModal()" 
                                title="Tutup Modal" 
                                class="w-6 h-6 flex items-center justify-center text-slate-400 hover:text-white hover:bg-slate-700 rounded-xs font-bold text-lg leading-none cursor-pointer transition-colors">
                            &times;
                        </button>
                        <span id="detail_peserta_status" class="inline-block text-[8px] font-black uppercase px-2 py-0.5 tracking-wider rounded-xs shadow-2xs">
                            STATUS
                        </span>
                    </div>
                </div>

                <div class="p-4 bg-slate-50/60 space-y-2">
                    <div class="grid grid-cols-2 gap-2 text-[10.5px]">
                        <!-- Baris 1: Jenis Kelamin & Kontak WhatsApp -->
                        <div class="bg-white border border-slate-200 p-2.5 rounded-xs shadow-2xs flex flex-col justify-between">
                            <span class="text-[8px] font-black text-slate-400 uppercase tracking-wider">Jenis Kelamin</span>
                            <span id="detail_peserta_gender" class="font-bold text-slate-800 uppercase tracking-wide mt-0.5">-</span>
                        </div>

                        <div class="bg-white border border-slate-200 p-2.5 rounded-xs shadow-2xs flex flex-col justify-between">
                            <span class="text-[8px] font-black text-slate-400 uppercase tracking-wider">Kontak WhatsApp</span>
                            <a id="detail_peserta_wa_link" href="#" target="_blank" rel="noopener noreferrer" onclick="event.stopPropagation()" title="Kirim Pesan WhatsApp" class="inline-flex items-center gap-1.5 font-bold text-emerald-600 font-mono mt-0.5 hover:underline">
                                <span id="detail_peserta_no_hp">-</span>
                            </a>
                        </div>

                        <!-- Baris 2: Alamat Email & Tempat, Tanggal Lahir -->
                        <div class="bg-white border border-slate-200 p-2.5 rounded-xs shadow-2xs flex flex-col justify-between">
                            <span class="text-[8px] font-black text-slate-400 uppercase tracking-wider">Alamat Email</span>
                            <span id="detail_peserta_email" class="font-bold text-slate-800 lowercase truncate mt-0.5">-</span>
                        </div>

                        <div class="bg-white border border-slate-200 p-2.5 rounded-xs shadow-2xs flex flex-col justify-between">
                            <span class="text-[8px] font-black text-slate-400 uppercase tracking-wider">Tempat, Tanggal Lahir</span>
                            <span id="detail_peserta_ttl" class="font-bold text-slate-800 uppercase truncate mt-0.5">-</span>
                        </div>

                        <!-- Baris 3: Perusahaan Mitra / Instansi (Full Width) -->
                        <div class="col-span-2 bg-white border border-slate-200 p-2.5 rounded-xs shadow-2xs flex flex-col justify-between">
                            <span class="text-[8px] font-black text-slate-400 uppercase tracking-wider">Perusahaan Mitra / Instansi</span>
                            <span id="detail_peserta_kerja" class="font-bold text-slate-800 uppercase truncate mt-0.5">-</span>
                        </div>
                    </div>
                </div>

                <div class="px-4 py-2.5 bg-white border-t border-slate-200 flex items-center justify-between shrink-0">
                    <button type="button" 
                            onclick="putarKartuPeserta()" 
                            title="Balik ke Belakang Kartu" 
                            class="p-1 text-slate-400 hover:text-slate-800 transition-colors cursor-pointer flex items-center justify-center group">
                        <svg class="w-5 h-5 group-hover:-translate-x-0.5 group-hover:translate-y-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 5L5 19M5 19h10M5 19V9"/>
                        </svg>
                    </button>

                    <button type="button" 
                            onclick="closeDetailPesertaModal()" 
                            class="px-5 py-1.5 bg-slate-800 hover:bg-slate-900 text-white text-[9.5px] font-black uppercase tracking-wider rounded-xs transition-colors cursor-pointer border-none shadow-xs">
                        Tutup Detail
                    </button>
                </div>
            </div>

            <!-- SISI BELAKANG -->
            <div class="absolute inset-0 w-full h-full rounded-sm overflow-hidden [transform:rotateY(180deg)] [backface-visibility:hidden] flex flex-col justify-between p-5 bg-slate-900 border border-slate-700 shadow-2xl">
                
                <div class="absolute inset-0 bg-cover bg-center" 
                     style="background-image: url('{{ asset('images/detail-card/bg-sawit.jpg') }}');">
                    <div class="absolute inset-0 bg-gradient-to-t from-slate-950/80 via-black/40 to-slate-950/50"></div>
                </div>

                <div class="relative z-10 flex justify-between items-center text-white/90">
                    <span class="text-[9px] font-black uppercase tracking-[0.25em] text-white drop-shadow-md">PT MILI TALENTA INSPIRASI</span>
                    <button type="button" 
                            onclick="closeDetailPesertaModal()" 
                            class="w-6 h-6 flex items-center justify-center text-white/80 hover:text-white hover:bg-white/20 rounded-xs font-bold text-lg leading-none cursor-pointer">
                        &times;
                    </button>
                </div>

                <div class="relative z-10 flex flex-col items-center justify-center my-auto py-2">
                    <img src="{{ asset('images/detail-card/LOGO-CARD.jpg') }}" 
                         alt="Logo Mili" 
                         class="w-24 h-24 sm:w-28 sm:h-28 object-contain drop-shadow-[0_10px_20px_rgba(0,0,0,0.6)]">
                    <span class="text-[9.5px] font-black uppercase tracking-[0.3em] text-white/95 mt-3 drop-shadow-md font-mono">
                        PARTICIPANT CARD
                    </span>
                </div>

                <div class="relative z-10 flex items-center justify-end pt-2 border-t border-white/20">
                    <button type="button" 
                            onclick="putarKartuPeserta()" 
                            title="Balik ke Depan" 
                            class="p-1 text-white/70 hover:text-white transition-colors cursor-pointer flex items-center justify-center group">
                        <svg class="w-5 h-5 group-hover:translate-x-0.5 group-hover:translate-y-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 5l14 14m0 0H9m10 0V9"/>
                        </svg>
                    </button>
                </div>
            </div>

        </div>
    </div>
</div>