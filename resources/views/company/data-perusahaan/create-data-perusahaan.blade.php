<!-- MODAL REGISTRASI PERUSAHAAN MITRA -->
<div id="modalCreatePerusahaan" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-[130] hidden flex items-center justify-center p-3 sm:p-4 transition-all duration-200 overflow-y-auto">
    <div class="fixed inset-0" onclick="document.getElementById('modalCreatePerusahaan').classList.add('hidden')"></div>

    <div class="relative bg-white w-full max-w-[95vw] sm:max-w-lg md:max-w-xl max-h-[92vh] flex flex-col my-auto shadow-2xl border border-slate-200 overflow-hidden rounded-xs z-[135]">
        
        <!-- Header Modal Warna #1e293b -->
        <div class="px-4 sm:px-5 py-3 bg-[#1e293b] text-white flex items-center justify-between shrink-0 select-none border-b border-slate-700">
            <div class="flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-[#22a0f6] ring-2 ring-blue-400/30 shrink-0"></span>
                <h3 class="text-xs font-black text-white uppercase tracking-wider truncate">Registrasi Perusahaan Mitra</h3>
            </div>
            <button type="button" onclick="document.getElementById('modalCreatePerusahaan').classList.add('hidden')" class="w-6 h-6 flex items-center justify-center text-slate-400 hover:text-white hover:bg-slate-800 rounded-xs font-bold text-lg leading-none cursor-pointer transition-colors border-none bg-transparent">&times;</button>
        </div>

        <form action="{{ route('perusahaan.store') }}" method="POST" class="p-4 sm:p-5 space-y-3.5 overflow-y-auto flex-1">
            @csrf
            
            <!-- Nama Perusahaan -->
            <div>
                <label class="block text-[9.5px] font-black uppercase tracking-wider text-slate-700 mb-1">Nama Perusahaan / Instansi <span class="text-rose-500">*</span></label>
                <input type="text" name="nama_perusahaan" required class="w-full bg-slate-50 border border-slate-200 px-2.5 py-1.5 text-xs font-bold text-slate-800 rounded-xs focus:bg-white focus:outline-none focus:border-[#22a0f6] uppercase" placeholder="Contoh: PT ASTRA HONDA MOTOR">
            </div>

            <!-- PIC & Kontak (Grid 1:1) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                <div>
                    <label class="block text-[9.5px] font-black uppercase tracking-wider text-slate-700 mb-1">Penanggung Jawab (PIC)</label>
                    <input type="text" name="penanggung_jawab" class="w-full bg-slate-50 border border-slate-200 px-2.5 py-1.5 text-xs font-bold text-slate-800 rounded-xs focus:bg-white focus:outline-none focus:border-[#22a0f6] uppercase" placeholder="Nama HRD / PIC">
                </div>
                <div>
                    <label class="block text-[9.5px] font-black uppercase tracking-wider text-slate-700 mb-1">Nomor WhatsApp PIC</label>
                    <div class="flex items-center bg-slate-50 border border-slate-200 rounded-xs focus-within:border-[#22a0f6] focus-within:bg-white">
                        <span class="px-2.5 py-1.5 bg-slate-100 border-r border-slate-200 text-[10px] font-black text-slate-500 select-none">+62</span>
                        <input type="text" name="kontak_pic" maxlength="13" oninput="this.value = this.value.replace(/[^0-9]/g, '').replace(/^(0|62)/, '');" class="w-full bg-transparent px-2.5 py-1.5 text-xs font-bold text-slate-800 font-mono focus:outline-none border-none" placeholder="81234567890">
                    </div>
                </div>
            </div>

            <!-- Email Perusahaan -->
            <div>
                <label class="block text-[9.5px] font-black uppercase tracking-wider text-slate-700 mb-1">Email Resmi Perusahaan</label>
                <input type="email" name="email_perusahaan" class="w-full bg-slate-50 border border-slate-200 px-2.5 py-1.5 text-xs font-medium text-slate-800 rounded-xs focus:bg-white focus:outline-none focus:border-[#22a0f6] lowercase" placeholder="hrd@company.com">
            </div>

            <!-- Alamat Kantor -->
            <div>
                <label class="block text-[9.5px] font-black uppercase tracking-wider text-slate-700 mb-1">Alamat Lengkap Kantor</label>
                <textarea name="alamat" rows="2" class="w-full bg-slate-50 border border-slate-200 px-2.5 py-1.5 text-xs font-medium text-slate-800 rounded-xs focus:bg-white focus:outline-none focus:border-[#22a0f6]" placeholder="Jl. Raya Kawasan Industri..."></textarea>
            </div>

            <!-- Action Buttons -->
            <div class="pt-2 flex gap-2">
                <button type="button" onclick="document.getElementById('modalCreatePerusahaan').classList.add('hidden')" class="w-1/3 border border-slate-200 text-slate-600 hover:bg-slate-100 text-[9.5px] font-black uppercase tracking-wider py-2 rounded-xs cursor-pointer">
                    BATAL
                </button>
                <button type="submit" class="w-2/3 bg-slate-900 hover:bg-slate-800 text-white text-[9.5px] font-black uppercase tracking-widest py-2 rounded-xs cursor-pointer border-none shadow-xs">
                    DAFTARKAN PERUSAHAAN
                </button>
            </div>
        </form>
    </div>
</div>