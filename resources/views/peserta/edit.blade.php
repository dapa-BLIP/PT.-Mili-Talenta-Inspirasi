<!-- MODAL EDIT DATA PESERTA -->
<div id="modalEditPeserta" class="fixed inset-0 bg-slate-900/50 backdrop-blur-xs z-[130] hidden flex items-center justify-center p-3 transition-all duration-200 print:hidden">
    <div class="bg-white w-full max-w-lg shadow-2xl border border-slate-200 overflow-hidden rounded-xs">
        
        <!-- Header -->
        <div class="px-5 py-3 bg-gradient-to-r from-sky-50 via-slate-50 to-white border-b border-slate-200 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-[#22a0f6] ring-2 ring-sky-100"></span>
                <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider">Edit Data Peserta</h3>
            </div>
            <button type="button" onclick="closeEditPesertaModal()" class="text-slate-400 hover:text-rose-600 font-bold text-lg leading-none cursor-pointer">&times;</button>
        </div>
        
        <form id="formEditPeserta" action="" method="POST" enctype="multipart/form-data" class="p-4 space-y-3">
            @csrf
            @method('PUT')
            
            <!-- Nama & Gender (Grid 3:2) -->
            <div class="grid grid-cols-5 gap-2.5">
                <div class="col-span-3">
                    <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1">Nama Lengkap Peserta <span class="text-rose-500">*</span></label>
                    <input type="text" name="nama_lengkap" id="edit_nama_lengkap" class="w-full bg-slate-50 border border-slate-200 px-2.5 py-1.5 text-xs font-bold text-slate-800 rounded-xs focus:bg-white focus:outline-none focus:border-[#22a0f6] uppercase" required>
                </div>
                <div class="col-span-2">
                    <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1">Jenis Kelamin <span class="text-rose-500">*</span></label>
                    <select name="gender" id="edit_gender" class="w-full bg-slate-50 border border-slate-200 px-2.5 py-1.5 text-xs font-semibold text-slate-700 rounded-xs focus:bg-white focus:outline-none focus:border-[#22a0f6] cursor-pointer" required>
                        <option value="pria">Laki-Laki (Pria)</option>
                        <option value="wanita">Perempuan (Wanita)</option>
                    </select>
                </div>
            </div>

            <!-- Tempat & Tanggal Lahir (Grid 1:1) -->
            <div class="grid grid-cols-2 gap-2.5">
                <div>
                    <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1">Tempat Lahir</label>
                    <input type="text" name="tempat_lahir" id="edit_tempat_lahir" class="w-full bg-slate-50 border border-slate-200 px-2.5 py-1.5 text-xs font-medium text-slate-800 rounded-xs focus:bg-white focus:outline-none focus:border-[#22a0f6] uppercase" placeholder="Contoh: BEKASI">
                </div>
                <div>
                    <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1">Tanggal Lahir</label>
                    <input type="date" name="tanggal_lahir" id="edit_tanggal_lahir" class="w-full bg-slate-50 border border-slate-200 px-2.5 py-1.5 text-xs font-medium text-slate-700 rounded-xs focus:bg-white focus:outline-none focus:border-[#22a0f6]">
                </div>
            </div>

            <!-- Email & Kontak WhatsApp (Grid 1:1) -->
            <div class="grid grid-cols-2 gap-2.5">
                <div>
                    <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1">Alamat Email <span class="text-rose-500">*</span></label>
                    <input type="email" name="email" id="edit_email" class="w-full bg-slate-50 border border-slate-200 px-2.5 py-1.5 text-xs font-medium text-slate-700 rounded-xs focus:bg-white focus:outline-none focus:border-[#22a0f6] lowercase" required>
                </div>
                <div>
                    <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1">Nomor WhatsApp</label>
                    <div class="flex items-center bg-slate-50 border border-slate-200 rounded-xs focus-within:border-[#22a0f6] focus-within:bg-white">
                        <span class="px-2 py-1.5 bg-slate-100 border-r border-slate-200 text-xs font-bold text-slate-500 select-none">+62</span>
                        <input type="text" name="no_hp" id="edit_no_hp" maxlength="13" oninput="this.value = this.value.replace(/[^0-9]/g, '').replace(/^(0|62)/, '');" class="w-full bg-transparent px-2.5 py-1 text-xs font-semibold text-slate-800 font-mono focus:outline-none border-none" placeholder="81234567890">
                    </div>
                </div>
            </div>

            <!-- Dropdown Perusahaan Mitra -->
            <div>
                <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1">Perusahaan Mitra / Instansi Asal</label>
                <select name="perusahaan_id" id="edit_perusahaan_id" class="w-full bg-slate-50 border border-slate-200 px-2.5 py-1.5 text-xs font-semibold text-slate-700 rounded-xs focus:bg-white focus:outline-none focus:border-[#22a0f6] cursor-pointer">
                    <option value="">-- TANPA PERUSAHAAN MITRA / UMUM --</option>
                    @foreach($perusahaans as $pt)
                        <option value="{{ $pt->id }}">{{ $pt->nama_perusahaan }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Program Pelatihan -->
            <div>
                <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1">Program Pelatihan <span class="text-rose-500">*</span></label>
                <select name="pelatihan" id="edit_pelatihan" class="w-full bg-slate-50 border border-slate-200 px-2.5 py-1.5 text-xs font-bold text-slate-700 rounded-xs focus:bg-white focus:outline-none focus:border-[#22a0f6] cursor-pointer" required>
                    @foreach($pelatihans as $pel)
                        <option value="{{ $pel->nama_pelatihan }}">{{ $pel->nama_pelatihan }} ({{ $pel->jenis_pelatihan }})</option>
                    @endforeach
                </select>
            </div>

            <!-- Upload Foto Profil + Inline Preview -->
            <div class="bg-slate-50 border border-slate-200 p-2.5 rounded-xs">
                <div class="flex justify-between items-center mb-1.5">
                    <label class="text-[10px] font-bold uppercase tracking-wider text-slate-600">Ubah Foto Profil</label>
                    <span class="text-[10px] font-medium text-amber-600">*Maks. 2MB (JPG, PNG)</span>
                </div>
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xs bg-white border border-slate-200 flex items-center justify-center shrink-0 overflow-hidden shadow-2xs">
                        <img id="edit_preview_peserta_foto" src="" class="w-full h-full object-cover hidden">
                        <span id="edit_preview_peserta_placeholder" class="text-[8px] font-bold text-slate-400 select-none">IMG</span>
                    </div>
                    <div class="flex-1 relative border border-dashed border-slate-300 hover:border-[#22a0f6] bg-white px-2.5 py-1.5 flex items-center justify-between rounded-xs cursor-pointer">
                        <span id="edit_foto_label_text" class="text-xs font-medium text-slate-500 truncate">Pilih berkas foto baru...</span>
                        <input type="file" name="foto" accept="image/jpeg,image/png,image/jpg" onchange="previewDanValidasiFoto(this, 'edit_foto_label_text', 'edit_preview_peserta_foto', 'edit_preview_peserta_placeholder')" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                        <span class="text-[10px] font-bold uppercase px-2 py-0.5 bg-slate-100 text-slate-600 rounded-xs shrink-0 select-none">Browse</span>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="pt-2 flex gap-2">
                <button type="button" onclick="closeEditPesertaModal()" class="w-1/3 border border-slate-200 text-slate-600 hover:bg-slate-100 text-xs font-bold uppercase tracking-wider py-2 rounded-xs cursor-pointer transition-colors">
                    Batal
                </button>
                <button type="submit" class="w-2/3 bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold uppercase tracking-wider py-2 rounded-xs cursor-pointer border-none shadow-xs transition-colors">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>