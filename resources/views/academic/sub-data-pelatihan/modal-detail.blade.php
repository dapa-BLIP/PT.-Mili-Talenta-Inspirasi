<!-- ========================================================================= -->
<!-- MODAL DETAIL KELAS PELATIHAN: WIDESCREEN LANDSCAPE LAYOUT                 -->
<!-- ========================================================================= -->
<div id="modalDetailPelatihan" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-[200] hidden flex items-center justify-center p-4 sm:p-6 select-none overflow-y-auto">
    
    <!-- Kontainer Landscape Lebar (max-w-5xl) -->
    <div class="relative w-full max-w-[96vw] lg:max-w-5xl bg-white border border-slate-200 shadow-2xl rounded-xs overflow-hidden flex flex-col z-[205] my-auto">
        
        <!-- 1. Header Bar -->
        <div class="px-5 py-3 bg-slate-800 text-white flex items-center justify-between shrink-0 border-b border-slate-700">
            <div class="flex items-center gap-2.5">
                <span class="w-2 h-2 rounded-full bg-[#22a0f6]"></span>
                <h4 class="text-xs sm:text-sm font-black uppercase tracking-wider">Detail Kelas Pelatihan</h4>
            </div>
            <button type="button" 
                    onclick="document.getElementById('modalDetailPelatihan').classList.add('hidden')" 
                    class="w-7 h-7 flex items-center justify-center text-slate-400 hover:text-white hover:bg-slate-700 rounded-xs font-bold text-xl leading-none cursor-pointer transition-colors">
                &times;
            </button>
        </div>
        
        <!-- 2. Body Content Landscape -->
        <div class="p-4 sm:p-5 space-y-4 bg-slate-50/70">
            
            <!-- Banner Horizontal Atas: Nama Kelas + Badge Modul + Mentor Ringkas -->
            <div class="bg-white border border-slate-200 p-4 rounded-xs shadow-2xs flex flex-col md:flex-row md:items-center justify-between gap-3">
                <div class="space-y-1 min-w-0 pr-4">
                    <h3 id="detail_nama_pelatihan" class="text-base sm:text-lg lg:text-xl font-black text-slate-800 uppercase tracking-tight truncate leading-snug">
                        -
                    </h3>
                </div>

                <!-- Info Mentor Cepat -->
                <div class="flex items-center gap-3 shrink-0 bg-slate-50 border border-slate-200/80 px-3.5 py-2 rounded-xs">
                    <div class="w-8 h-8 rounded-xs bg-slate-800 text-white flex items-center justify-center font-black text-xs shrink-0 shadow-2xs">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    </div>
                    <div class="min-w-0">
                        <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider block">Mentor Pelatihan</span>
                        <p id="detail_mentor_pelatihan" class="text-xs font-black text-slate-800 uppercase truncate leading-tight"></p>
                    </div>
                </div>
            </div>

            <!-- Grid 2 Kolom Landscape (Rasio 4 : 8) -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-4 items-stretch">
                
                <!-- KOLOM KIRI (4/12): Penilaian & Parameter -->
                <div class="lg:col-span-4 flex flex-col gap-3">
                    
                    <!-- Kartu Komponen Penilaian -->
                    <div class="bg-white border border-slate-200 p-4 rounded-xs shadow-2xs flex-1 flex flex-col justify-start">
                        <div class="flex items-center justify-between pb-2 mb-3 border-b border-slate-100">
                            <div class="flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                                <span class="text-[10.5px] font-black uppercase tracking-wider text-slate-700">Komponen Penilaian</span>
                            </div>
                        </div>
                        
                        <p class="text-[9.5px] text-slate-500 mb-2 leading-relaxed">
                            Penilaian yang wajib diinput oleh mentor untuk kelas ini:
                        </p>

                        <div id="detail_komponen_container" class="flex flex-wrap gap-1.5 max-h-48 overflow-y-auto">
                            <!-- Rendered via JS -->
                        </div>
                    </div>

                    <!-- Petunjuk Operasional -->
                    <div class="p-3 bg-blue-50/60 border border-blue-100 rounded-xs">
                        <p class="text-[9.5px] text-blue-900 leading-relaxed font-medium">
                            <span class="font-black uppercase tracking-wider text-[#22a0f6] block mb-0.5">Catatan Kurikulum :</span>
                            Materi terpilih merupakan materi pembelajaran yang akan dinilai oleh mentor.
                        </p>
                    </div>

                </div>

                <!-- KOLOM KANAN (8/12): Materi dalam Grid 2 Kolom -->
                <div class="lg:col-span-8 bg-white border border-slate-200 p-4 rounded-xs shadow-2xs flex flex-col justify-between min-h-[300px]">
                    <div>
                        <div class="flex items-center justify-between pb-2 mb-3 border-b border-slate-100">
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-[#22a0f6]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <span class="text-[11px] font-black uppercase tracking-wider text-slate-700">Materi Terpilih</span>
                            </div>
                            <span id="detail_total_materi" class="text-[9.5px] font-mono font-bold text-[#22a0f6] bg-sky-50 border border-sky-100 px-2 py-0.5 rounded-xs">
                                0 Materi
                            </span>
                        </div>
                        
                        <!-- List Silabus dalam Grid 2 Kolom Landscape -->
                        <div id="detail_checkbox_container" 
                             class="grid grid-cols-1 sm:grid-cols-2 gap-2 overflow-y-auto max-h-[300px] pr-1.5 [scrollbar-width:thin] [scrollbar-color:#cbd5e1_transparent]">
                            <!-- Render via JS -->
                        </div>
                    </div>
                </div>

            </div>

        </div>
        
        <!-- 3. Footer Bar -->
        <div class="px-5 py-3 bg-white border-t border-slate-200 flex items-center justify-end shrink-0">
            <button type="button" 
                    onclick="document.getElementById('modalDetailPelatihan').classList.add('hidden')" 
                    class="px-5 py-1.5 bg-slate-800 hover:bg-slate-900 text-white text-[10.5px] font-black uppercase tracking-wider rounded-xs shadow-xs cursor-pointer border-none transition-colors">
                Tutup
            </button>
        </div>

    </div>
</div>