<!-- ========================================================================= -->
<!-- MODAL: EDIT JADWAL PELATIHAN (HEADER #1e293b - PROPORTIONAL LAYOUT)      -->
<!-- ========================================================================= -->
<div id="modalEditJadwal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-[100] hidden flex items-center justify-center p-3 sm:p-4 print:hidden select-none overflow-y-auto">
    <div class="relative w-full max-w-[94vw] sm:max-w-xl bg-white border border-slate-200 shadow-2xl rounded-sm overflow-hidden flex flex-col my-auto">
        
        <!-- Header Modal Warna #1e293b -->
        <div class="px-5 py-3.5 bg-[#1e293b] text-white flex justify-between items-center select-none shrink-0 border-b border-slate-700">
            <div class="flex items-center gap-2.5">
                <div class="w-7 h-7 rounded-sm bg-blue-500/20 border border-blue-400/40 flex items-center justify-center text-[#22a0f6]">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-[11px] font-black text-white uppercase tracking-wider">Perbarui Jadwal Pelatihan</h3>
                </div>
            </div>
            <button type="button" onclick="closeEditModal()" class="w-7 h-7 flex items-center justify-center text-slate-400 hover:text-white hover:bg-slate-800 rounded-sm font-bold text-lg leading-none transition-colors border-none bg-transparent cursor-pointer">&times;</button>
        </div>
        
        <!-- Form Content -->
        <form id="formEditJadwal" method="POST" class="p-5 space-y-3.5 bg-slate-50/50">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                <!-- Activity Title -->
                <div class="sm:col-span-2">
                    <label class="block text-[8px] font-black uppercase tracking-widest text-slate-500 mb-1">
                        Nama / Judul Kegiatan <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="judul_kegiatan" id="edit_judul" class="w-full bg-white border border-slate-200 rounded-sm px-3 py-2 text-[11px] font-semibold text-slate-800 focus:outline-none focus:border-[#22a0f6] focus:ring-1 focus:ring-[#22a0f6] transition-all" required>
                </div>

                <!-- Master Trainer -->
                <div class="sm:col-span-2">
                    <label class="block text-[8px] font-black uppercase tracking-widest text-slate-500 mb-1">
                        Nama Mentor <span class="text-rose-500">*</span>
                    </label>
                    <select name="trainer_id" id="edit_trainer" class="w-full bg-white border border-slate-200 rounded-sm px-3 py-2 text-[11px] font-bold text-slate-700 focus:outline-none focus:border-[#22a0f6] focus:ring-1 focus:ring-[#22a0f6] transition-all cursor-pointer" required>
                        @foreach($trainers as $t)
                            <option value="{{ $t->id }}">{{ strtoupper($t->nama_mentor) }} {{ !empty($t->program_pelatihan) ? '('.$t->program_pelatihan.')' : '' }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Start Date -->
                <div>
                    <label class="block text-[8px] font-black uppercase tracking-widest text-slate-500 mb-1">
                        Tanggal Mulai <span class="text-rose-500">*</span>
                    </label>
                    <input type="date" name="tanggal_mulai" id="edit_tgl_mulai" class="w-full bg-white border border-slate-200 rounded-sm px-3 py-2 text-[11px] font-semibold text-slate-700 focus:outline-none focus:border-[#22a0f6] focus:ring-1 focus:ring-[#22a0f6] transition-all" required>
                </div>

                <!-- End Date -->
                <div>
                    <label class="block text-[8px] font-black uppercase tracking-widest text-slate-500 mb-1">
                        Tanggal Selesai <span class="text-rose-500">*</span>
                    </label>
                    <input type="date" name="tanggal_selesai" id="edit_tgl_selesai" class="w-full bg-white border border-slate-200 rounded-sm px-3 py-2 text-[11px] font-semibold text-slate-700 focus:outline-none focus:border-[#22a0f6] focus:ring-1 focus:ring-[#22a0f6] transition-all" required>
                </div>

                <!-- Start Time -->
                <div>
                    <label class="block text-[8px] font-black uppercase tracking-widest text-slate-500 mb-1">
                        Jam Mulai (WIB) <span class="text-rose-500">*</span>
                    </label>
                    <input type="time" name="jam_mulai" id="edit_jam_mulai" class="w-full bg-white border border-slate-200 rounded-sm px-3 py-2 text-[11px] font-semibold text-slate-700 focus:outline-none focus:border-[#22a0f6] focus:ring-1 focus:ring-[#22a0f6] transition-all" required>
                </div>

                <!-- End Time -->
                <div>
                    <label class="block text-[8px] font-black uppercase tracking-widest text-slate-500 mb-1">
                        Jam Selesai (WIB) <span class="text-rose-500">*</span>
                    </label>
                    <input type="time" name="jam_selesai" id="edit_jam_selesai" class="w-full bg-white border border-slate-200 rounded-sm px-3 py-2 text-[11px] font-semibold text-slate-700 focus:outline-none focus:border-[#22a0f6] focus:ring-1 focus:ring-[#22a0f6] transition-all" required>
                </div>

                <!-- Execution Type -->
                <div class="sm:col-span-2">
                    <label class="block text-[8px] font-black uppercase tracking-widest text-slate-500 mb-1">
                        Jenis Pelaksanaan <span class="text-rose-500">*</span>
                    </label>
                    <select name="jenis_pelaksanaan" id="edit_jenis" class="w-full bg-white border border-slate-200 rounded-sm px-3 py-2 text-[11px] font-bold text-slate-700 focus:outline-none focus:border-[#22a0f6] focus:ring-1 focus:ring-[#22a0f6] transition-all cursor-pointer" required>
                        <option value="offline">ON-SITE / IN-PERSON (TATAP MUKA)</option>
                        <option value="online">REMOTE / VIRTUAL (ONLINE)</option>
                    </select>
                </div>

                <!-- Description -->
                <div class="sm:col-span-2">
                    <label class="block text-[8px] font-black uppercase tracking-widest text-slate-500 mb-1">
                        Deskripsi
                    </label>
                    <textarea name="description" id="edit_description" rows="2" class="w-full bg-white border border-slate-200 rounded-sm px-3 py-2 text-[11px] font-medium text-slate-700 focus:outline-none focus:border-[#22a0f6] focus:ring-1 focus:ring-[#22a0f6] transition-all resize-none" placeholder="Catatan materi atau detail ruang lingkup pelatihan..."></textarea>
                </div>
            </div>

            <!-- Footer Buttons -->
            <div class="pt-3 border-t border-slate-200 flex gap-2.5">
                <button type="button" onclick="closeEditModal()" class="w-1/3 border border-slate-300 text-slate-600 py-2 rounded-sm text-[10px] font-black uppercase tracking-wider hover:bg-slate-100 transition-colors cursor-pointer bg-white">
                    Batal
                </button>
                <button type="submit" class="w-2/3 bg-[#1e293b] hover:bg-blue-600 text-white py-2 rounded-sm text-[10px] font-black uppercase tracking-wider transition-all shadow-sm border-none cursor-pointer flex items-center justify-center gap-1.5">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>