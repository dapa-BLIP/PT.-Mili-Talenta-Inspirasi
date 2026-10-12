<!-- MODAL EDIT DATA PESERTA (RESPONSIF MOBILE / TABLET / DESKTOP) -->
<div id="modalEditPeserta" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-[130] hidden flex items-center justify-center p-3 sm:p-4 transition-all duration-200 overflow-y-auto print:hidden">
    <div class="fixed inset-0" onclick="closeEditPesertaModal()"></div>

    <div class="relative bg-white w-full max-w-[95vw] sm:max-w-lg md:max-w-xl max-h-[92vh] flex flex-col my-auto shadow-2xl border border-slate-200 overflow-hidden rounded-xs z-[135]">
        
        <!-- Header Modal Warna #1e293b -->
        <div class="px-4 sm:px-5 py-3 bg-[#1e293b] text-white flex items-center justify-between shrink-0 select-none border-b border-slate-700">
            <div class="flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-[#22a0f6] ring-2 ring-blue-400/30 shrink-0"></span>
                <h3 class="text-xs font-black text-white uppercase tracking-wider truncate">Edit Data Peserta</h3>
            </div>
            <button type="button" onclick="closeEditPesertaModal()" class="w-6 h-6 flex items-center justify-center text-slate-400 hover:text-white hover:bg-slate-800 rounded-xs font-bold text-lg leading-none cursor-pointer transition-colors border-none bg-transparent">&times;</button>
        </div>
        
        <form id="formEditPeserta" action="" method="POST" enctype="multipart/form-data" class="p-4 sm:p-5 space-y-3.5 overflow-y-auto flex-1">
            @csrf
            @method('PUT')
            
            <!-- Nama & Gender (Responsif: 1 kolom di HP, 5 kolom di Tablet/Desktop) -->
            <div class="grid grid-cols-1 sm:grid-cols-5 gap-2.5">
                <div class="sm:col-span-3">
                    <label class="block text-[9.5px] font-black uppercase tracking-wider text-slate-700 mb-1">Nama Lengkap Peserta <span class="text-rose-500">*</span></label>
                    <input type="text" name="nama_lengkap" id="edit_nama_lengkap" class="w-full bg-slate-50 border border-slate-200 px-2.5 py-1.5 text-xs font-bold text-slate-800 rounded-xs focus:bg-white focus:outline-none focus:border-[#22a0f6] uppercase" required>
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-[9.5px] font-black uppercase tracking-wider text-slate-700 mb-1">Jenis Kelamin <span class="text-rose-500">*</span></label>
                    <select name="gender" id="edit_gender" class="w-full bg-slate-50 border border-slate-200 px-2.5 py-1.5 text-xs font-bold text-slate-800 rounded-xs focus:bg-white focus:outline-none focus:border-[#22a0f6] cursor-pointer" required>
                        <option value="pria">Laki-Laki (Pria)</option>
                        <option value="wanita">Perempuan (Wanita)</option>
                    </select>
                </div>
            </div>

            <!-- Tempat & Tanggal Lahir (1 kolom di HP, 2 kolom di Tablet/Desktop) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                <div>
                    <label class="block text-[9.5px] font-black uppercase tracking-wider text-slate-700 mb-1">Tempat Lahir</label>
                    <input type="text" name="tempat_lahir" id="edit_tempat_lahir" class="w-full bg-slate-50 border border-slate-200 px-2.5 py-1.5 text-xs font-bold text-slate-800 rounded-xs focus:bg-white focus:outline-none focus:border-[#22a0f6] uppercase" placeholder="Contoh: BEKASI">
                </div>
                <div>
                    <label class="block text-[9.5px] font-black uppercase tracking-wider text-slate-700 mb-1">Tanggal Lahir</label>
                    <input type="date" name="tanggal_lahir" id="edit_tanggal_lahir" class="w-full bg-slate-50 border border-slate-200 px-2.5 py-1.5 text-xs font-bold text-slate-800 rounded-xs focus:bg-white focus:outline-none focus:border-[#22a0f6]">
                </div>
            </div>

            <!-- Email & Kontak WhatsApp (1 kolom di HP, 2 kolom di Tablet/Desktop) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                <div>
                    <label class="block text-[9.5px] font-black uppercase tracking-wider text-slate-700 mb-1">Alamat Email <span class="text-rose-500">*</span></label>
                    <input type="email" name="email" id="edit_email" class="w-full bg-slate-50 border border-slate-200 px-2.5 py-1.5 text-xs font-bold text-slate-800 rounded-xs focus:bg-white focus:outline-none focus:border-[#22a0f6] lowercase" required>
                </div>
                <div>
                    <label class="block text-[9.5px] font-black uppercase tracking-wider text-slate-700 mb-1">Nomor WhatsApp</label>
                    <div class="flex items-center bg-slate-50 border border-slate-200 rounded-xs focus-within:border-[#22a0f6] focus-within:bg-white">
                        <span class="px-2 py-1.5 bg-slate-100 border-r border-slate-200 text-xs font-bold text-slate-500 select-none">+62</span>
                        <input type="text" name="no_hp" id="edit_no_hp" maxlength="13" oninput="this.value = this.value.replace(/[^0-9]/g, '').replace(/^(0|62)/, '');" class="w-full bg-transparent px-2.5 py-1 text-xs font-bold text-slate-800 font-mono focus:outline-none border-none" placeholder="81234567890">
                    </div>
                </div>
            </div>

            <!-- Dropdown Perusahaan Mitra -->
            <div>
                <div class="flex justify-between items-center mb-1">
                    <label class="block text-[9.5px] font-black uppercase tracking-wider text-slate-700">Perusahaan Mitra / Instansi Asal</label>
                    <a href="{{ route('perusahaan.index') }}" target="_blank" class="text-[9.5px] font-bold text-[#22a0f6] hover:underline uppercase tracking-tight">+ Kelola Mitra</a>
                </div>
                <select name="perusahaan_id" id="edit_perusahaan_id" class="w-full bg-slate-50 border border-slate-200 px-2.5 py-1.5 text-xs font-bold text-slate-800 rounded-xs focus:bg-white focus:outline-none focus:border-[#22a0f6] cursor-pointer">
                    <option value="">-- TANPA PERUSAHAAN MITRA / UMUM --</option>
                    @foreach($perusahaans as $pt)
                        <option value="{{ $pt->id }}">{{ $pt->nama_perusahaan }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Program Pelatihan -->
            <div>
                <div class="flex justify-between items-center mb-1">
                    <label class="block text-[9.5px] font-black uppercase tracking-wider text-slate-700">Program Pelatihan <span class="text-rose-500">*</span></label>
                    <a href="{{ route('academic.pelatihan.index') }}" target="_blank" class="text-[9.5px] font-bold text-[#22a0f6] hover:underline uppercase tracking-tight">+ Kelola Pelatihan</a>
                </div>
                <select name="pelatihan" id="edit_pelatihan" class="w-full bg-slate-50 border border-slate-200 px-2.5 py-1.5 text-xs font-bold text-slate-800 rounded-xs focus:bg-white focus:outline-none focus:border-[#22a0f6] cursor-pointer" required>
                    <option value="">-- PILIH PROGRAM PELATIHAN --</option>
                    @foreach($programs ?? [] as $prog)
                        <option value="{{ $prog }}">{{ strtoupper($prog) }}</option>
                    @endforeach
                </select>

                <!-- Warning Alert Saat Peserta Memiliki Nilai di Program Sebelumnya -->
                <div id="edit_pelatihan_warning" class="hidden mt-2 p-2.5 bg-amber-50 border border-amber-300 rounded-xs text-amber-900 shadow-2xs transition-all">
                    <div class="flex items-start gap-2">
                        <svg class="w-4 h-4 text-amber-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        <div class="flex-1">
                            <p class="text-[10.5px] font-black uppercase tracking-tight text-amber-900">
                                Peringatan: Peserta memiliki nilai di program "<span id="edit_pelatihan_warning_prog" class="underline decoration-amber-500">-</span>"
                            </p>
                            <p class="text-[9px] text-amber-700 font-medium mt-0.5 leading-tight">
                                Pastikan data nilai pada program sebelumnya telah dibersihkan terlebih dahulu sebelum mengubah program pelatihan.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Upload Foto Profil + Inline Preview -->
            <div class="bg-slate-50 border border-slate-200 p-2.5 rounded-xs">
                <div class="flex justify-between items-center mb-1.5">
                    <label class="text-[9.5px] font-black uppercase tracking-wider text-slate-700">Ubah Foto Profil</label>
                    <span class="text-[9px] font-bold text-amber-600">*Maks. 2MB (JPG, PNG)</span>
                </div>
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xs bg-white border border-slate-200 flex items-center justify-center shrink-0 overflow-hidden shadow-2xs">
                        <img id="edit_preview_peserta_foto" src="" class="w-full h-full object-cover hidden">
                        <span id="edit_preview_peserta_placeholder" class="text-[8px] font-bold text-slate-400 select-none">IMG</span>
                    </div>
                    <div class="flex-1 relative border border-dashed border-slate-300 hover:border-[#22a0f6] bg-white px-2.5 py-1.5 flex items-center justify-between rounded-xs cursor-pointer">
                        <span id="edit_foto_label_text" class="text-xs font-medium text-slate-500 truncate">Pilih berkas foto baru...</span>
                        <input type="file" name="foto" accept="image/jpeg,image/png,image/jpg" onchange="previewDanValidasiFoto(this, 'edit_foto_label_text', 'edit_preview_peserta_foto', 'edit_preview_peserta_placeholder')" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                        <span class="text-[9.5px] font-black uppercase px-2 py-0.5 bg-slate-100 text-slate-600 rounded-xs shrink-0 select-none">Browse</span>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="pt-2 flex gap-2 border-t border-slate-100">
                <button type="button" onclick="closeEditPesertaModal()" class="w-1/3 border border-slate-200 text-slate-600 hover:bg-slate-100 text-xs font-bold uppercase tracking-wider py-2 rounded-xs cursor-pointer transition-colors text-center">
                    Batal
                </button>
                <button type="submit" class="w-2/3 bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold uppercase tracking-wider py-2 rounded-xs cursor-pointer border-none shadow-xs transition-colors text-center">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>