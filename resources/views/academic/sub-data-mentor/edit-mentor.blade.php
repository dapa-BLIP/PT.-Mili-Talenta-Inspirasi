<!-- ========================================================================= -->
<!-- MODAL: EDIT DATA MASTER MENTOR (STATIC BACKDROP)                          -->
<!-- ========================================================================= -->
<div id="modalEditTrainer" class="fixed inset-0 bg-slate-900/60 z-[9999] hidden flex items-center justify-center p-3 sm:p-4 overflow-y-auto select-none">
    
    <!-- Backdrop Statis: Klik luar TIDAK AKAN menutup modal -->
    <div class="fixed inset-0"></div>

    <div class="relative w-full max-w-[92vw] sm:max-w-[480px] bg-white border border-slate-200 shadow-2xl rounded-xs overflow-hidden flex flex-col z-10 my-auto">
        
        <div class="px-4 py-2.5 bg-slate-800 text-white flex items-center justify-between shrink-0">
            <div class="flex items-center gap-2">
                <span class="w-1.5 h-1.5 rounded-full bg-[#22a0f6]"></span>
                <h4 class="text-[11px] font-black uppercase tracking-wider">Edit Data Mentor</h4>
            </div>
            <button type="button" onclick="tutupModalEditMentor()" class="w-6 h-6 flex items-center justify-center text-slate-400 hover:text-white hover:bg-slate-700 rounded-xs font-bold text-lg leading-none cursor-pointer transition-colors">&times;</button>
        </div>

        <form id="formEditTrainer" action="" method="POST" enctype="multipart/form-data" class="p-3.5 sm:p-4 space-y-2.5 bg-slate-50/50">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-5 gap-2">
                <div class="col-span-3 bg-white border border-slate-200 p-2.5 rounded-xs shadow-2xs space-y-1">
                    <label class="block text-[9px] font-black uppercase tracking-wider text-slate-700">
                        Nama Lengkap & Gelar <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" id="edit_nama_mentor" name="nama_mentor" class="w-full bg-slate-50 border border-slate-300 px-3 py-1.5 text-[11px] font-bold text-slate-800 rounded-xs uppercase focus:outline-none focus:border-slate-800 focus:bg-white transition-colors" required>
                </div>
                <div class="col-span-2 bg-white border border-slate-200 p-2.5 rounded-xs shadow-2xs space-y-1">
                    <label class="block text-[9px] font-black uppercase tracking-wider text-slate-700">
                        NIDN / NIK
                    </label>
                    <input type="text" id="edit_nidn" name="nidn" maxlength="16" oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 16); checkDuplicateNidnEdit(this.value)" class="w-full bg-slate-50 border border-slate-300 px-2.5 py-1.5 text-[11px] font-bold font-mono text-slate-800 rounded-xs focus:outline-none focus:border-slate-800 focus:bg-white transition-colors" placeholder="Opsional">
                    <p id="edit_nidn_warning" class="hidden text-[8.5px] font-black text-rose-600 uppercase leading-tight mt-0.5"></p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                <div class="bg-white border border-slate-200 p-2.5 rounded-xs shadow-2xs space-y-1">
                    <label class="block text-[9px] font-black uppercase tracking-wider text-slate-700">Tempat Lahir</label>
                    <input type="text" id="edit_tempat_lahir" name="tempat_lahir" class="w-full bg-slate-50 border border-slate-300 px-2.5 py-1.5 text-[10.5px] font-bold text-slate-800 rounded-xs uppercase focus:outline-none focus:border-slate-800 focus:bg-white" placeholder="KOTA KELAHIRAN">
                </div>
                <div class="bg-white border border-slate-200 p-2.5 rounded-xs shadow-2xs space-y-1">
                    <label class="block text-[9px] font-black uppercase tracking-wider text-slate-700">Tanggal Lahir</label>
                    <input type="date" id="edit_tanggal_lahir" name="tanggal_lahir" class="w-full bg-slate-50 border border-slate-300 px-2.5 py-1.5 text-[10.5px] font-bold text-slate-700 rounded-xs focus:outline-none focus:border-slate-800 focus:bg-white">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                <div class="bg-white border border-slate-200 p-2.5 rounded-xs shadow-2xs space-y-1">
                    <label class="block text-[9px] font-black uppercase tracking-wider text-slate-700">Email Mentor</label>
                    <input type="email" id="edit_email" name="email" class="w-full bg-slate-50 border border-slate-300 px-2.5 py-1.5 text-[10.5px] font-medium text-slate-800 rounded-xs focus:outline-none focus:border-slate-800 focus:bg-white">
                </div>
                <div class="bg-white border border-slate-200 p-2.5 rounded-xs shadow-2xs space-y-1">
                    <label class="block text-[9px] font-black uppercase tracking-wider text-slate-700">Nomor WhatsApp <span class="text-rose-500">*</span></label>
                    <input type="text" id="edit_kontak" name="kontak" placeholder="+6281234567890" maxlength="16" oninput="this.value = this.value.replace(/[^0-9+]/g, '')" class="w-full bg-slate-50 border border-slate-300 px-2.5 py-1.5 text-[10.5px] font-bold font-mono text-slate-800 rounded-xs focus:outline-none focus:border-slate-800 focus:bg-white" required>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                <div class="bg-white border border-slate-200 p-2.5 rounded-xs shadow-2xs space-y-1">
                    <div class="flex justify-between items-center">
                        <label class="block text-[9px] font-black uppercase tracking-wider text-slate-700">Program Pelatihan <span class="text-rose-500">*</span></label>
                        <a href="{{ route('academic.pelatihan.index') }}" target="_blank" class="text-[9px] font-semibold text-blue-500 hover:underline uppercase tracking-tight">+ Kelola Program</a>
                    </div>
                    <select id="edit_program_pelatihan" name="program_pelatihan" class="w-full bg-slate-50 border border-slate-300 px-2.5 py-1.5 text-[10.5px] font-bold text-slate-800 rounded-xs uppercase focus:outline-none focus:border-slate-800 focus:bg-white" required>
                        <option value="">-- PILIH PROGRAM PELATIHAN --</option>
                        @foreach ($programs ?? [] as $prog)
                            <option value="{{ $prog }}">{{ strtoupper($prog) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="bg-white border border-slate-200 p-2.5 rounded-xs shadow-2xs space-y-1">
                    <label class="block text-[9px] font-black uppercase tracking-wider text-slate-700">Status Mentor <span class="text-rose-500">*</span></label>
                    <select id="edit_status" name="status" class="w-full bg-slate-50 border border-slate-300 px-2.5 py-1.5 text-[10.5px] font-bold text-slate-800 rounded-xs uppercase focus:outline-none focus:border-slate-800 focus:bg-white" required>
                        <option value="AKTIF">AKTIF</option>
                        <option value="PENDING">PENDING</option>
                        <option value="NON-AKTIF">NON-AKTIF</option>
                    </select>
                </div>
            </div>

            <div class="bg-white border border-slate-200 p-2.5 rounded-xs shadow-2xs space-y-1">
                <div class="flex justify-between items-center">
                    <label class="text-[9px] font-black uppercase tracking-wider text-slate-700">Ganti Foto Profil</label>
                    <span class="text-[8px] text-slate-400 italic">*Maks. 2MB (opsional)</span>
                </div>
                <input type="file" name="foto" accept="image/jpeg,image/png,image/jpg" class="w-full text-[10px] font-bold text-slate-500 bg-slate-50 p-1.5 border border-slate-300 rounded-xs file:mr-2.5 file:py-1 file:px-2.5 file:border-0 file:text-[8.5px] file:font-black file:uppercase file:bg-slate-800 file:text-white hover:file:bg-slate-700 cursor-pointer">
            </div>

            <div class="flex items-center justify-end gap-2 pt-1">
                <button type="button" onclick="tutupModalEditMentor()" class="px-3.5 py-1.5 border border-slate-300 text-slate-600 hover:bg-slate-100 text-[10px] font-black uppercase tracking-wider rounded-xs cursor-pointer transition-colors">
                    Batal
                </button>
                <button type="submit" class="px-4 py-1.5 bg-slate-800 hover:bg-slate-900 text-white text-[10px] font-black uppercase tracking-wider rounded-xs shadow-xs cursor-pointer border-none transition-colors">
                    Simpan Perubahan
                </button>
            </div>
        </form>

    </div>
</div>