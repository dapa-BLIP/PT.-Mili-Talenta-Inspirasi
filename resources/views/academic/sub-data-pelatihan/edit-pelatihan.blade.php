<!-- ========================================================================= -->
<!-- MODAL: EDIT PELATIHAN (SPLIT 2 KOLOM: FORM KIRI + MATERI KANAN)          -->
<!-- ========================================================================= -->
<div id="modalEditPelatihan" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-[200] hidden flex items-center justify-center p-3 sm:p-4 overflow-y-auto select-none">
    
    <div class="fixed inset-0" onclick="tutupModalEditPelatihan()"></div>

    <div class="relative w-full max-w-[95vw] md:max-w-[780px] bg-white border border-slate-200 shadow-2xl rounded-xs overflow-hidden flex flex-col z-[205] my-auto">
        
        <!-- Header Modal -->
        <div class="px-4 py-2.5 bg-slate-800 text-white flex items-center justify-between shrink-0">
            <div class="flex items-center gap-2">
                <span class="w-1.5 h-1.5 rounded-full bg-[#22a0f6]"></span>
                <h4 class="text-[11px] font-black uppercase tracking-wider">Edit Pelatihan</h4>
            </div>
            <button type="button" 
                    onclick="tutupModalEditPelatihan()" 
                    class="w-6 h-6 flex items-center justify-center text-slate-400 hover:text-white hover:bg-slate-700 rounded-xs font-bold text-lg leading-none cursor-pointer transition-colors">
                &times;
            </button>
        </div>

        <!-- Form Body (Grid 2 Kolom) -->
        <form id="formEditPelatihan" action="" method="POST" class="p-4 space-y-3 bg-slate-50/50">
            @csrf
            @method('PUT')

            <!-- Hidden Input: Nama Pelatihan otomatis mengikuti modul/program -->
            <input type="hidden" name="nama_pelatihan" id="edit_nama_pelatihan" value="">

            <div class="grid grid-cols-1 md:grid-cols-12 gap-3 items-stretch">
                
                <!-- ========================================================= -->
                <!-- KOLOM KIRI (7/12): PARAMETER PELATIHAN                    -->
                <!-- ========================================================= -->
                <div class="md:col-span-7 space-y-2.5 flex flex-col justify-start">
                    
                    <!-- 1. Program Pelatihan -->
                    <div class="bg-white border border-slate-200 p-2.5 rounded-xs shadow-2xs space-y-1">
                        <label class="block text-[9px] font-black uppercase tracking-wider text-slate-700">
                            Program Pelatihan <span class="text-rose-500">*</span>
                        </label>
                        <select id="edit_jenis_pelatihan" 
                                name="jenis_pelatihan" 
                                onchange="handleModulChangeEdit()" 
                                class="w-full bg-slate-50 border border-slate-300 px-2.5 py-1.5 text-[10.5px] font-bold text-slate-800 rounded-xs uppercase focus:outline-none focus:border-slate-800 focus:bg-white transition-colors cursor-pointer" 
                                required>
                            <option value="">-- PILIH PROGRAM PELATIHAN --</option>
                            @foreach ($kategoriPelatihans ?? [] as $kp)
                                <option value="{{ $kp->nama_kategori }}">{{ strtoupper($kp->nama_kategori) }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- 2. Mentor Pengampu -->
                    <div class="bg-white border border-slate-200 p-2.5 rounded-xs shadow-2xs space-y-1">
                        <label class="block text-[9px] font-black uppercase tracking-wider text-slate-700">
                            Mentor Pengampu <span class="text-rose-500">*</span>
                        </label>
                        <select id="edit_trainer_id" 
                                name="trainer_id" 
                                class="w-full bg-slate-50 border border-slate-300 px-2.5 py-1.5 text-[10.5px] font-bold text-slate-800 rounded-xs uppercase focus:outline-none focus:border-slate-800 focus:bg-white transition-colors cursor-pointer" 
                                required>
                            <option value="">- PILIH MENTOR -</option>
                            @foreach ($trainers ?? [] as $t)
                                <option value="{{ $t->id }}" data-program="{{ strtoupper(trim($t->program_pelatihan ?? '')) }}">{{ $t->nama_mentor }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- 3. Komponen Penilaian -->
                    <div class="bg-white border border-slate-200 p-2.5 rounded-xs shadow-2xs space-y-1">
                        <div class="flex justify-between items-center">
                            <label class="block text-[9px] font-black uppercase tracking-wider text-slate-700">
                                Komponen Penilaian <span class="text-rose-500">*</span>
                            </label>
                            <span class="text-[8px] text-slate-400 font-normal lowercase">(pisahkan koma)</span>
                        </div>
                        <input type="text" 
                               id="edit_raw_komponen" 
                               name="raw_komponen" 
                               placeholder="CONTOH: TUGAS, PRAKTIK, UJIAN AKHIR" 
                               class="w-full bg-slate-50 border border-slate-300 px-3 py-1.5 text-[11px] font-bold text-slate-800 rounded-xs uppercase focus:outline-none focus:border-slate-800 focus:bg-white transition-colors" 
                               required 
                               autocomplete="off">
                    </div>

                </div>

                <!-- ========================================================= -->
                <!-- KOLOM KANAN (5/12): CHECKLIST MATERI PEMBELAJARAN         -->
                <!-- ========================================================= -->
                <div class="md:col-span-5 bg-white border border-slate-200 p-3 rounded-xs shadow-2xs flex flex-col min-h-[260px] max-h-[290px]">
                    
                    <!-- Header Box Materi -->
                    <div class="flex items-center justify-between pb-2 mb-2 border-b border-slate-100 shrink-0">
                        <div class="flex items-center gap-1.5">
                            <label class="text-[9.5px] font-black uppercase tracking-wider text-slate-700">
                                Materi :
                            </label>
                            <span id="badge_counter_materi_edit" class="text-[8.5px] font-mono font-bold text-[#22a0f6] bg-sky-50 border border-sky-100 px-1.5 py-0.2 rounded-xs">
                                0 Terpilih
                            </span>
                        </div>
                        
                        <button type="button" 
                                id="btn_toggle_all_materi_edit" 
                                onclick="toggleSelectAllMateriEdit()" 
                                class="text-[9px] font-bold text-[#22a0f6] hover:text-blue-700 uppercase tracking-tight cursor-pointer transition-colors hidden">
                            Batalkan Semua
                        </button>
                    </div>

                    <!-- List Scrollable Materi -->
                    <div id="edit_checkbox_container" 
                         class="flex-1 overflow-y-auto space-y-1.5 pr-1 [scrollbar-width:thin] [scrollbar-color:#cbd5e1_transparent]">
                        <!-- Rendered via JS -->
                    </div>

                </div>

            </div>

            <!-- Footer Tombol Aksi -->
            <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-200/80">
                <button type="button" 
                        onclick="tutupModalEditPelatihan()" 
                        class="px-3.5 py-1.5 border border-slate-300 text-slate-600 hover:bg-slate-100 text-[10px] font-black uppercase tracking-wider rounded-xs cursor-pointer transition-colors">
                    Batal
                </button>
                <button type="submit" 
                        class="px-4 py-1.5 bg-slate-800 hover:bg-slate-900 text-white text-[10px] font-black uppercase tracking-wider rounded-xs shadow-xs cursor-pointer border-none transition-colors">
                    Simpan Perubahan
                </button>
            </div>
        </form>

    </div>
</div>

<script>
    function tutupModalEditPelatihan() {
        const modal = document.getElementById('modalEditPelatihan');
        if (modal) modal.classList.add('hidden');
    }
</script>