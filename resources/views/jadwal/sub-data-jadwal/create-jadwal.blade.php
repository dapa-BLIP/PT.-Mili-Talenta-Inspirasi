<!-- ========================================================================= -->
<!-- MODAL: CREATE JADWAL PELATIHAN (HEADER #1e293b - PROPORTIONAL LAYOUT)    -->
<!-- ========================================================================= -->
<div id="modalJadwal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-[100] hidden flex items-center justify-center p-3 sm:p-4 print:hidden select-none overflow-y-auto">
    <div class="relative w-full max-w-[94vw] sm:max-w-xl bg-white border border-slate-200 shadow-2xl rounded-sm overflow-hidden flex flex-col my-auto">
        
        <!-- Header Modal Warna #1e293b -->
        <div class="px-5 py-3.5 bg-[#1e293b] text-white flex justify-between items-center select-none shrink-0 border-b border-slate-700">
            <div class="flex items-center gap-2.5">
                <div class="w-7 h-7 rounded-sm bg-blue-500/20 border border-blue-400/40 flex items-center justify-center text-[#22a0f6]">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-[11px] font-black text-white uppercase tracking-wider">Tambah Jadwal Pelatihan</h3>
                    <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">Entry Baru Jadwal & Mentor</p>
                </div>
            </div>
            <button type="button" onclick="closeCreateModal()" class="w-7 h-7 flex items-center justify-center text-slate-400 hover:text-white hover:bg-slate-800 rounded-sm font-bold text-lg leading-none transition-colors border-none bg-transparent cursor-pointer">&times;</button>
        </div>
        
        <!-- Form Content -->
        <form action="{{ route('jadwal.store') }}" method="POST" class="p-5 space-y-3.5 bg-slate-50/50">
            @csrf
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                <!-- Activity Title -->
                <div class="sm:col-span-2">
                    <label class="block text-[8px] font-black uppercase tracking-widest text-slate-500 mb-1">
                        Nama / Judul Kegiatan <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="judul_kegiatan" class="w-full bg-white border border-slate-200 rounded-sm px-3 py-2 text-[11px] font-semibold text-slate-800 focus:outline-none focus:border-[#22a0f6] focus:ring-1 focus:ring-[#22a0f6] transition-all" required placeholder="Contoh: Hands-On Machine Learning & Data Pipeline">
                </div>

                <!-- Master Trainer -->
                <div class="sm:col-span-2">
                    <label class="block text-[8px] font-black uppercase tracking-widest text-slate-500 mb-1">
                        Trainer / Mentor Pengampu <span class="text-rose-500">*</span>
                    </label>
                    <select name="trainer_id" class="w-full bg-white border border-slate-200 rounded-sm px-3 py-2 text-[11px] font-bold text-slate-700 focus:outline-none focus:border-[#22a0f6] focus:ring-1 focus:ring-[#22a0f6] transition-all cursor-pointer" required>
                        <option value="" disabled selected>-- PILIH MASTER TRAINER --</option>
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
                    <input type="date" id="create_tgl_mulai" name="tanggal_mulai" class="w-full bg-white border border-slate-200 rounded-sm px-3 py-2 text-[11px] font-semibold text-slate-700 focus:outline-none focus:border-[#22a0f6] focus:ring-1 focus:ring-[#22a0f6] transition-all" required>
                </div>

                <!-- End Date -->
                <div>
                    <label class="block text-[8px] font-black uppercase tracking-widest text-slate-500 mb-1">
                        Tanggal Selesai <span class="text-rose-500">*</span>
                    </label>
                    <input type="date" id="create_tgl_selesai" name="tanggal_selesai" class="w-full bg-white border border-slate-200 rounded-sm px-3 py-2 text-[11px] font-semibold text-slate-700 focus:outline-none focus:border-[#22a0f6] focus:ring-1 focus:ring-[#22a0f6] transition-all" required>
                </div>

                <!-- Start Time -->
                <div>
                    <label class="block text-[8px] font-black uppercase tracking-widest text-slate-500 mb-1">
                        Jam Mulai (WIB) <span class="text-rose-500">*</span>
                    </label>
                    <input type="time" name="jam_mulai" class="w-full bg-white border border-slate-200 rounded-sm px-3 py-2 text-[11px] font-semibold text-slate-700 focus:outline-none focus:border-[#22a0f6] focus:ring-1 focus:ring-[#22a0f6] transition-all" required>
                </div>

                <!-- End Time -->
                <div>
                    <label class="block text-[8px] font-black uppercase tracking-widest text-slate-500 mb-1">
                        Jam Selesai (WIB) <span class="text-rose-500">*</span>
                    </label>
                    <input type="time" name="jam_selesai" class="w-full bg-white border border-slate-200 rounded-sm px-3 py-2 text-[11px] font-semibold text-slate-700 focus:outline-none focus:border-[#22a0f6] focus:ring-1 focus:ring-[#22a0f6] transition-all" required>
                </div>

                <!-- Execution Type -->
                <div class="sm:col-span-2">
                    <label class="block text-[8px] font-black uppercase tracking-widest text-slate-500 mb-1">
                        Jenis Pelaksanaan <span class="text-rose-500">*</span>
                    </label>
                    <select name="jenis_pelaksanaan" class="w-full bg-white border border-slate-200 rounded-sm px-3 py-2 text-[11px] font-bold text-slate-700 focus:outline-none focus:border-[#22a0f6] focus:ring-1 focus:ring-[#22a0f6] transition-all cursor-pointer" required>
                        <option value="offline">ON-SITE / IN-PERSON (TATAP MUKA)</option>
                        <option value="online">REMOTE / VIRTUAL (ONLINE)</option>
                    </select>
                </div>

                <!-- Description -->
                <div class="sm:col-span-2">
                    <label class="block text-[8px] font-black uppercase tracking-widest text-slate-500 mb-1">
                        Deskripsi / Agenda Kegiatan
                    </label>
                    <textarea name="description" rows="2" class="w-full bg-white border border-slate-200 rounded-sm px-3 py-2 text-[11px] font-medium text-slate-700 focus:outline-none focus:border-[#22a0f6] focus:ring-1 focus:ring-[#22a0f6] transition-all resize-none" placeholder="Catatan materi atau detail ruang lingkup pelatihan..."></textarea>
                </div>
            </div>

            <!-- Footer Buttons -->
            <div class="pt-3 border-t border-slate-200 flex gap-2.5">
                <button type="button" onclick="closeCreateModal()" class="w-1/3 border border-slate-300 text-slate-600 py-2 rounded-sm text-[10px] font-black uppercase tracking-wider hover:bg-slate-100 transition-colors cursor-pointer bg-white">
                    Batal
                </button>
                <button type="submit" class="w-2/3 bg-[#1e293b] hover:bg-blue-600 text-white py-2 rounded-sm text-[10px] font-black uppercase tracking-wider transition-all shadow-sm border-none cursor-pointer flex items-center justify-center gap-1.5">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                    Simpan Jadwal
                </button>
            </div>
        </form>
    </div>
</div>