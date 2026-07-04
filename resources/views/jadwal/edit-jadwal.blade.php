<div id="modalEditJadwal" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-[100] hidden flex items-center justify-center p-4">
    <div class="bg-white border border-slate-200 w-full max-w-md shadow-xl rounded-none overflow-hidden">
        
        <div class="bg-slate-50 border-b border-slate-100 px-5 py-3 flex items-center justify-between rounded-none select-none">
            <h3 class="text-[10px] font-black text-slate-800 uppercase tracking-widest">EDIT JADWAL AKTIVITAS</h3>
            <button type="button" onclick="closeEditModal()" class="text-slate-400 hover:text-rose-500 font-bold text-lg border-none bg-transparent cursor-pointer">&times;</button>
        </div>
        
        <form id="formEditJadwal" method="POST" class="p-5 space-y-3.5 rounded-none">
            @csrf
            @method('PUT')
            
            <div>
                <label class="block text-[8px] font-black uppercase tracking-widest text-slate-400 mb-1">Nama Kegiatan</label>
                <input type="text" name="judul_kegiatan" id="edit_judul" class="w-full bg-slate-50 border border-slate-200 rounded-none px-2.5 py-1.5 text-[11px] font-medium text-slate-800 focus:outline-none focus:border-blue-400 focus:bg-white" required>
            </div>

            <div>
                <label class="block text-[8px] font-black uppercase tracking-widest text-slate-400 mb-1">Trainer / Mentor</label>
                <select name="trainer_id" id="edit_trainer" class="w-full bg-slate-50 border border-slate-200 rounded-none px-2 py-1.5 text-[11px] font-bold text-slate-600 focus:outline-none focus:border-blue-400 focus:bg-white cursor-pointer" required>
                    @foreach($trainers as $t)
                        <option value="{{ $t->id }}">{{ strtoupper($t->nama_mentor) }}</option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-[8px] font-black uppercase tracking-widest text-slate-400 mb-1">Tgl Mulai</label>
                    <input type="date" name="tanggal_mulai" id="edit_tgl_mulai" class="w-full bg-slate-50 border border-slate-200 rounded-none px-2 py-1.5 text-[11px] font-medium text-slate-700 focus:outline-none focus:border-blue-400" required>
                </div>
                <div>
                    <label class="block text-[8px] font-black uppercase tracking-widest text-slate-400 mb-1">Tgl Selesai</label>
                    <input type="date" name="tanggal_selesai" id="edit_tgl_selesai" class="w-full bg-slate-50 border border-slate-200 rounded-none px-2 py-1.5 text-[11px] font-medium text-slate-700 focus:outline-none focus:border-blue-400" required>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-[8px] font-black uppercase tracking-widest text-slate-400 mb-1">Jam Mulai</label>
                    <input type="time" name="jam_mulai" id="edit_jam_mulai" class="w-full bg-slate-50 border border-slate-200 rounded-none px-2 py-1.5 text-[11px] font-medium text-slate-700 focus:outline-none focus:border-blue-400" required>
                </div>
                <div>
                    <label class="block text-[8px] font-black uppercase tracking-widest text-slate-400 mb-1">Jam Selesai</label>
                    <input type="time" name="jam_selesai" id="edit_jam_selesai" class="w-full bg-slate-50 border border-slate-200 rounded-none px-2 py-1.5 text-[11px] font-medium text-slate-700 focus:outline-none focus:border-blue-400" required>
                </div>
            </div>

            <div>
                <label class="block text-[8px] font-black uppercase tracking-widest text-slate-400 mb-1">Jenis Pelaksanaan</label>
                <select name="jenis_pelaksanaan" id="edit_jenis" class="w-full bg-slate-50 border border-slate-200 rounded-none px-2 py-1.5 text-[10px] font-bold text-slate-600 focus:outline-none focus:border-blue-400 focus:bg-white cursor-pointer" required>
                    <option value="offline">ON-SITE / IN-PERSON</option>
                    <option value="online">REMOTE / VIRTUAL</option>
                </select>
            </div>

            <div>
                <label class="block text-[8px] font-black uppercase tracking-widest text-slate-400 mb-1">Deskripsi Kegiatan</label>
                <textarea name="description" id="edit_description" rows="2" class="w-full bg-slate-50 border border-slate-200 rounded-none px-2.5 py-1.5 text-[11px] font-medium text-slate-700 focus:outline-none focus:border-blue-400 resize-none" placeholder="Opsional..."></textarea>
            </div>

            <div class="pt-2 flex gap-2">
                <button type="button" onclick="closeEditModal()" class="w-1/3 border border-slate-200 text-slate-400 py-2 rounded-none text-[9px] font-black uppercase tracking-widest hover:bg-slate-50 transition-colors bg-white cursor-pointer">
                    BATAL
                </button>
                <button type="submit" class="w-2/3 bg-slate-900 hover:bg-blue-600 text-white py-2 rounded-none text-[9px] font-black uppercase tracking-widest transition-colors shadow-sm border-none cursor-pointer">
                    SAVE CHANGES
                </button>
            </div>
        </form>
    </div>
</div>