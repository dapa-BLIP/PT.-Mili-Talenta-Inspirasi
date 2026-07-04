<div id="modalJadwal" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-[100] hidden flex items-center justify-center p-4">
    <div class="bg-white w-full max-w-md rounded-none shadow-xl border border-slate-200 overflow-hidden">
        
        <div class="px-5 py-3 border-b border-slate-100 bg-slate-50 flex justify-between items-center select-none">
            <h3 class="text-[10px] font-black text-slate-800 uppercase tracking-widest">DEPLOYMENT ENTRY FORM</h3>
            <button type="button" onclick="closeCreateModal()" class="text-slate-400 hover:text-rose-500 font-bold text-lg leading-none transition-colors border-none bg-transparent cursor-pointer">&times;</button>
        </div>
        
        <form action="{{ route('jadwal.store') }}" method="POST" class="p-5 space-y-3.5">
            @csrf
            
            <div>
                <label class="block text-[8px] font-black uppercase tracking-widest text-slate-400 mb-1">Activity Title</label>
                <input type="text" name="judul_kegiatan" class="w-full bg-slate-50 border border-slate-200 rounded-none px-2.5 py-1.5 text-[11px] font-medium text-slate-800 focus:outline-none focus:border-blue-400 focus:bg-white" required placeholder="Contoh: Maintenance Server">
            </div>

            <div>
                <label class="block text-[8px] font-black uppercase tracking-widest text-slate-400 mb-1">Trainer / Mentor</label>
                <select name="trainer_id" class="w-full bg-slate-50 border border-slate-200 rounded-none px-2 py-1.5 text-[11px] font-bold text-slate-600 focus:outline-none focus:border-blue-400 focus:bg-white cursor-pointer" required>
                    <option value="" disabled selected>-- CHOOSE MASTER TRAINER --</option>
                    @foreach($trainers as $t)
                        <option value="{{ $t->id }}">{{ strtoupper($t->nama_mentor) }}</option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-[8px] font-black uppercase tracking-widest text-slate-400 mb-1">Start Date</label>
                    <input type="date" id="create_tgl_mulai" name="tanggal_mulai" class="w-full bg-slate-50 border border-slate-200 rounded-none px-2 py-1.5 text-[11px] font-medium text-slate-700 focus:outline-none focus:border-blue-400" required>
                </div>
                <div>
                    <label class="block text-[8px] font-black uppercase tracking-widest text-slate-400 mb-1">End Date</label>
                    <input type="date" id="create_tgl_selesai" name="tanggal_selesai" class="w-full bg-slate-50 border border-slate-200 rounded-none px-2 py-1.5 text-[11px] font-medium text-slate-700 focus:outline-none focus:border-blue-400" required>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-[8px] font-black uppercase tracking-widest text-slate-400 mb-1">Start Time</label>
                    <input type="time" name="jam_mulai" class="w-full bg-slate-50 border border-slate-200 rounded-none px-2 py-1.5 text-[11px] font-medium text-slate-700 focus:outline-none focus:border-blue-400" required>
                </div>
                <div>
                    <label class="block text-[8px] font-black uppercase tracking-widest text-slate-400 mb-1">End Time</label>
                    <input type="time" name="jam_selesai" class="w-full bg-slate-50 border border-slate-200 rounded-none px-2 py-1.5 text-[11px] font-medium text-slate-700 focus:outline-none focus:border-blue-400" required>
                </div>
            </div>

            <div>
                <label class="block text-[8px] font-black uppercase tracking-widest text-slate-400 mb-1">Execution Type</label>
                <select name="jenis_pelaksanaan" class="w-full bg-slate-50 border border-slate-200 rounded-none px-2 py-1.5 text-[10px] font-bold text-slate-600 focus:outline-none focus:border-blue-400 focus:bg-white cursor-pointer" required>
                    <option value="offline">ON-SITE / IN-PERSON</option>
                    <option value="online">REMOTE / VIRTUAL</option>
                </select>
            </div>

            <div>
                <label class="block text-[8px] font-black uppercase tracking-widest text-slate-400 mb-1">Activity Description</label>
                <textarea name="description" rows="2" class="w-full bg-slate-50 border border-slate-200 rounded-none px-2.5 py-1.5 text-[11px] font-medium text-slate-700 focus:outline-none focus:border-blue-400 resize-none" placeholder="Opsional..."></textarea>
            </div>

            <div class="pt-2 flex gap-2">
                <button type="button" onclick="closeCreateModal()" class="w-1/3 border border-slate-200 text-slate-400 py-2 rounded-none text-[9px] font-black uppercase tracking-widest hover:bg-slate-50 transition-colors cursor-pointer bg-white">
                    CANCEL
                </button>
                <button type="submit" class="w-2/3 bg-slate-900 hover:bg-blue-600 text-white py-2 rounded-none text-[9px] font-black uppercase tracking-widest transition-colors shadow-sm border-none cursor-pointer">
                    COMMIT TO RECORDS
                </button>
            </div>
        </form>
    </div>
</div>