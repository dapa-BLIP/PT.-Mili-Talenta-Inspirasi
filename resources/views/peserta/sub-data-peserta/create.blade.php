<!-- MODAL REGISTRASI PESERTA BARU (RESPONSIF MOBILE / TABLET / DESKTOP) -->
<div id="modalCreatePeserta" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-[130] hidden flex items-center justify-center p-3 sm:p-4 transition-all duration-200 overflow-y-auto">
    <div class="fixed inset-0" onclick="closeCreatePesertaModal()"></div>

    <div class="relative bg-white w-full max-w-[95vw] sm:max-w-lg md:max-w-xl max-h-[92vh] flex flex-col my-auto shadow-2xl border border-slate-200 overflow-hidden rounded-xs z-[135]">
        
        <!-- Header Modal Warna #1e293b -->
        <div class="px-4 sm:px-5 py-3 bg-[#1e293b] text-white flex items-center justify-between shrink-0 select-none border-b border-slate-700">
            <div class="flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-[#22a0f6] ring-2 ring-blue-400/30 shrink-0"></span>
                <h3 class="text-xs font-black text-white uppercase tracking-wider truncate">Registrasi Peserta Baru</h3>
            </div>
            <button type="button" onclick="closeCreatePesertaModal()" class="w-6 h-6 flex items-center justify-center text-slate-400 hover:text-white hover:bg-slate-800 rounded-xs font-bold text-lg leading-none cursor-pointer transition-colors border-none bg-transparent">&times;</button>
        </div>
        
        <form id="formCreatePeserta" action="{{ route('peserta.store') }}" method="POST" enctype="multipart/form-data" class="p-4 sm:p-5 space-y-3.5 overflow-y-auto flex-1">
            @csrf
            
            <!-- Nama & Gender (Responsif: 1 kolom di HP, 5 kolom di Tablet/Desktop) -->
            <div class="grid grid-cols-1 sm:grid-cols-5 gap-2.5">
                <div class="sm:col-span-3">
                    <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-700 mb-1">Nama Lengkap Peserta <span class="text-rose-500">*</span></label>
                    <input type="text" name="nama_lengkap" class="w-full bg-slate-50 border border-slate-200 px-2.5 py-1.5 text-xs font-medium text-slate-800 rounded-xs focus:bg-white focus:outline-none focus:border-[#22a0f6] uppercase placeholder:normal-case placeholder:font-normal placeholder:text-slate-400" placeholder="Nama lengkap & gelar" required>
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-700 mb-1">Jenis Kelamin <span class="text-rose-500">*</span></label>
                    <select name="gender" class="w-full bg-slate-50 border border-slate-200 px-2.5 py-1.5 text-xs font-medium text-slate-800 rounded-xs focus:bg-white focus:outline-none focus:border-[#22a0f6] cursor-pointer" required>
                        <option value="pria">Laki-Laki (Pria)</option>
                        <option value="wanita">Perempuan (Wanita)</option>
                    </select>
                </div>
            </div>

            <!-- Tempat & Tanggal Lahir (1 kolom di HP, 2 kolom di Tablet/Desktop) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                <div>
                    <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-700 mb-1">Tempat Lahir</label>
                    <input type="text" name="tempat_lahir" class="w-full bg-slate-50 border border-slate-200 px-2.5 py-1.5 text-xs font-medium text-slate-800 rounded-xs focus:bg-white focus:outline-none focus:border-[#22a0f6] uppercase placeholder:normal-case placeholder:font-normal placeholder:text-slate-400" placeholder="Kota kelahiran (cth: Bekasi)">
                </div>
                <div>
                    <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-700 mb-1">Tanggal Lahir</label>
                    <input type="date" name="tanggal_lahir" class="w-full bg-slate-50 border border-slate-200 px-2.5 py-1.5 text-xs font-medium text-slate-800 rounded-xs focus:bg-white focus:outline-none focus:border-[#22a0f6]">
                </div>
            </div>

            <!-- Email & Kontak WhatsApp (1 kolom di HP, 2 kolom di Tablet/Desktop) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                <div>
                    <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-700 mb-1">Alamat Email <span class="text-rose-500">*</span></label>
                    <input type="email" name="email" class="w-full bg-slate-50 border border-slate-200 px-2.5 py-1.5 text-xs font-medium text-slate-800 rounded-xs focus:bg-white focus:outline-none focus:border-[#22a0f6] lowercase placeholder:normal-case placeholder:font-normal placeholder:text-slate-400" placeholder="nama@email.com" required>
                </div>
                <div>
                    <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-700 mb-1">Nomor WhatsApp</label>
                    <div class="flex items-center bg-slate-50 border border-slate-200 rounded-xs focus-within:border-[#22a0f6] focus-within:bg-white">
                        <span class="px-2.5 py-1.5 bg-slate-100 border-r border-slate-200 text-xs font-semibold text-slate-500 select-none">+62</span>
                        <input type="text" name="no_hp" maxlength="13" oninput="this.value = this.value.replace(/[^0-9]/g, '').replace(/^(0|62)/, '');" class="w-full bg-transparent px-2.5 py-1 text-xs font-medium text-slate-800 font-mono placeholder:font-sans placeholder:font-normal placeholder:text-slate-400 focus:outline-none border-none" placeholder="81234567890">
                    </div>
                </div>
            </div>

            <!-- Dropdown Perusahaan Mitra -->
            <div>
                <div class="flex justify-between items-center mb-1">
                    <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-700">Perusahaan Mitra / Instansi Asal</label>
                    <a href="{{ route('perusahaan.index') }}" target="_blank" class="text-[10px] font-semibold text-[#22a0f6] hover:underline uppercase tracking-tight">+ Kelola Mitra</a>
                </div>
                <select name="perusahaan_id" class="w-full bg-slate-50 border border-slate-200 px-2.5 py-1.5 text-xs font-medium text-slate-800 rounded-xs focus:bg-white focus:outline-none focus:border-[#22a0f6] cursor-pointer">
                    <option value="">-- Tanpa Perusahaan Mitra / Umum --</option>
                    @foreach($perusahaans as $pt)
                        <option value="{{ $pt->id }}">{{ $pt->nama_perusahaan }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Program Pelatihan -->
            <div>
                <div class="flex justify-between items-center mb-1">
                    <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-700">Program Pelatihan <span class="text-rose-500">*</span></label>
                    <a href="{{ route('academic.pelatihan.index') }}" target="_blank" class="text-[10px] font-semibold text-[#22a0f6] hover:underline uppercase tracking-tight">+ Kelola Pelatihan</a>
                </div>
                <select name="pelatihan" class="w-full bg-slate-50 border border-slate-200 px-2.5 py-1.5 text-xs font-medium text-slate-800 rounded-xs focus:bg-white focus:outline-none focus:border-[#22a0f6] cursor-pointer" required>
                    <option value="">-- Pilih Program Pelatihan --</option>
                    @foreach($programs ?? [] as $prog)
                        <option value="{{ $prog }}">{{ strtoupper($prog) }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Upload Foto Profil -->
            <div class="bg-slate-50 border border-slate-200 p-2.5 rounded-xs">
                <div class="flex justify-between items-center mb-1.5">
                    <label class="text-[10px] font-bold uppercase tracking-wider text-slate-700">Foto Profil Peserta</label>
                    <span class="text-[9.5px] font-semibold text-amber-600 uppercase tracking-wider">*Maks. 2MB (JPG, PNG)</span>
                </div>
                <div class="relative border border-dashed border-slate-300 hover:border-[#22a0f6] bg-white px-2.5 py-1.5 flex items-center justify-between rounded-xs cursor-pointer">
                    <span id="foto_label_text" class="text-xs font-medium text-slate-500 truncate">Pilih berkas foto...</span>
                    <input type="file" name="foto" accept="image/jpeg,image/png,image/jpg" onchange="previewDanValidasiFoto(this, 'foto_label_text')" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                    <span class="text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 bg-slate-100 text-slate-600 rounded-xs shrink-0 select-none">Browse</span>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="pt-2 flex gap-2 border-t border-slate-100">
                <button type="button" onclick="closeCreatePesertaModal()" class="w-1/3 border border-slate-200 text-slate-600 hover:bg-slate-100 text-xs font-bold uppercase tracking-wider py-2 rounded-xs cursor-pointer transition-colors text-center">
                    Batal
                </button>
                <button type="submit" class="w-2/3 bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold uppercase tracking-wider py-2 rounded-xs cursor-pointer border-none shadow-xs transition-colors text-center">
                    Simpan Peserta
                </button>
            </div>
        </form>
    </div>
</div>