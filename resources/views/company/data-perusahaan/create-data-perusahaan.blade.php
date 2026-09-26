<!-- MODAL REGISTRASI PERUSAHAAN MITRA -->
<div id="modalCreatePerusahaan" class="fixed inset-0 bg-slate-900/50 backdrop-blur-xs z-[130] hidden flex items-center justify-center p-3 transition-all duration-200">
    <div class="bg-white w-full max-w-md shadow-2xl border border-slate-200 overflow-hidden rounded-xs">
        
        <!-- Header -->
        <div class="px-4 py-2.5 bg-gradient-to-r from-sky-50 via-slate-50 to-white border-b border-slate-200 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-[#22a0f6] ring-2 ring-sky-100"></span>
                <h3 class="text-[10.5px] font-black text-slate-800 uppercase tracking-widest">REGISTRASI PERUSAHAAN MITRA</h3>
            </div>
            <button type="button" onclick="document.getElementById('modalCreatePerusahaan').classList.add('hidden')" class="text-slate-400 hover:text-rose-600 font-bold text-lg leading-none cursor-pointer">&times;</button>
        </div>

        <form action="{{ route('perusahaan.store') }}" method="POST" class="p-3.5 space-y-2.5">
            @csrf
            
            <!-- Nama Perusahaan -->
            <div>
                <label class="block text-[8px] font-black uppercase tracking-wider text-slate-500 mb-0.5">Nama Perusahaan / Instansi <span class="text-rose-500">*</span></label>
                <input type="text" name="nama_perusahaan" required class="w-full bg-slate-50/80 border border-slate-200 px-2 py-1 text-[11px] font-bold text-slate-800 rounded-xs focus:bg-white focus:outline-none focus:border-[#22a0f6] uppercase" placeholder="Contoh: PT ASTRA HONDA MOTOR">
            </div>

            <!-- PIC & Kontak (Grid 1:1) -->
            <div class="grid grid-cols-2 gap-2">
                <div>
                    <label class="block text-[8px] font-black uppercase tracking-wider text-slate-500 mb-0.5">Penanggung Jawab (PIC)</label>
                    <input type="text" name="penanggung_jawab" class="w-full bg-slate-50/80 border border-slate-200 px-2 py-1 text-[11px] font-bold text-slate-800 rounded-xs focus:bg-white focus:outline-none focus:border-[#22a0f6] uppercase" placeholder="Nama HRD / PIC">
                </div>
                <div>
                    <label class="block text-[8px] font-black uppercase tracking-wider text-slate-500 mb-0.5">Nomor WhatsApp PIC</label>
                    <div class="flex items-center bg-slate-50/80 border border-slate-200 rounded-xs focus-within:border-[#22a0f6] focus-within:bg-white">
                        <span class="px-2 py-1 bg-slate-100 border-r border-slate-200 text-[9.5px] font-black text-slate-500 select-none">+62</span>
                        <input type="text" name="kontak_pic" maxlength="13" oninput="this.value = this.value.replace(/[^0-9]/g, '').replace(/^(0|62)/, '');" class="w-full bg-transparent px-2 py-1 text-[11px] font-bold text-slate-800 font-mono focus:outline-none border-none" placeholder="81234567890">
                    </div>
                </div>
            </div>

            <!-- Email Perusahaan -->
            <div>
                <label class="block text-[8px] font-black uppercase tracking-wider text-slate-500 mb-0.5">Email Resmi Perusahaan</label>
                <input type="email" name="email_perusahaan" class="w-full bg-slate-50/80 border border-slate-200 px-2 py-1 text-[10.5px] font-medium text-slate-700 rounded-xs focus:bg-white focus:outline-none focus:border-[#22a0f6] lowercase" placeholder="hrd@company.com">
            </div>

            <!-- Alamat Kantor -->
            <div>
                <label class="block text-[8px] font-black uppercase tracking-wider text-slate-500 mb-0.5">Alamat Lengkap Kantor</label>
                <textarea name="alamat" rows="2" class="w-full bg-slate-50/80 border border-slate-200 px-2 py-1 text-[10.5px] font-medium text-slate-700 rounded-xs focus:bg-white focus:outline-none focus:border-[#22a0f6]" placeholder="Jl. Raya Kawasan Industri..."></textarea>
            </div>

            <!-- Action Buttons -->
            <div class="pt-1 flex gap-2">
                <button type="button" onclick="document.getElementById('modalCreatePerusahaan').classList.add('hidden')" class="w-1/3 border border-slate-200 text-slate-500 hover:bg-slate-100 text-[9px] font-black uppercase tracking-wider py-1.5 rounded-xs cursor-pointer">
                    BATAL
                </button>
                <button type="submit" class="w-2/3 bg-slate-900 hover:bg-slate-800 text-white text-[9px] font-black uppercase tracking-widest py-1.5 rounded-xs cursor-pointer border-none shadow-xs">
                    DAFTARKAN PERUSAHAAN
                </button>
            </div>
        </form>
    </div>
</div>