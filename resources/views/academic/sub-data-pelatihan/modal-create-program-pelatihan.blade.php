<!-- ========================================================================= -->
<!-- MODAL MASTER: COMPACT SCALE (OPTIMAL DI DISPLAY SCALE 100% & 125%) -->
<!-- ========================================================================= -->
<div id="modalModulPelatihan" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-[200] hidden flex items-center justify-center p-3 sm:p-4 overflow-hidden select-none">
    
    <!-- ===================================================================== -->
    <!-- 1. MODAL CREATE TENGAH: SKALA COMPACT PROPORSIONAL (MAX-W-[460PX]) -->
    <!-- ===================================================================== -->
   <div class="relative w-full max-w-[95vw] md:max-w-[590px] bg-white border border-slate-200 shadow-2xl rounded-xs overflow-hidden flex flex-col z-[205] my-auto">
        
        <!-- Header Modal Tengah -->
        <div class="px-4 py-2.5 bg-slate-800 text-white flex items-center justify-between shrink-0">
            <div class="flex items-center gap-2">
                <span class="w-1.5 h-1.5 rounded-full bg-[#22a0f6]"></span>
                <h4 class="text-[11px] font-black uppercase tracking-wider">Tambah Program Modul</h4>
            </div>
            <!-- Tombol Silang Modal Tengah -->
            <button type="button" 
                    onclick="tutupModalModulPelatihan()" 
                    title="Tutup Modal" 
                    class="w-6 h-6 flex items-center justify-center text-slate-400 hover:text-white hover:bg-slate-700 rounded-xs font-bold text-lg leading-none cursor-pointer transition-colors">
                &times;
            </button>
        </div>

        <!-- Body Form Tengah (Skala Ramping & Ringkas) -->
        <div class="p-3.5 sm:p-4 space-y-3 bg-slate-50/50">
            
            <!-- Box Panduan Singkat -->
            <div class="bg-white border border-slate-200 p-2.5 rounded-xs shadow-2xs space-y-0.5">
                <span class="text-[8.5px] font-black uppercase tracking-wider text-slate-400 block">Panduan Singkat :</span>
                <p class="text-[10.5px] font-medium text-slate-600 leading-relaxed">
                    Masukkan nama modul baru. Modul ini otomatis terhubung sebagai data induk kurikulum kelas.
                </p>
            </div>

            <!-- Form Input -->
            <form id="formTambahKategori" action="{{ route('academic.kategori-pelatihans.store') }}" method="POST" class="bg-white border border-slate-200 p-3 rounded-xs shadow-2xs space-y-1.5">
                @csrf
                <label class="block text-[9.5px] font-black uppercase tracking-wider text-slate-700">
                    Nama Modul Pelatihan <span class="text-rose-500">*</span>
                </label>
                <input type="text" name="nama_kategori" class="w-full bg-slate-50 border border-slate-300 px-3 py-1.5 text-[11px] font-bold text-slate-800 rounded-xs uppercase focus:outline-none focus:border-slate-800 focus:bg-white transition-colors" placeholder="CONTOH: PEMROGRAMAN WEB LARAVEL" required autocomplete="off">
            </form>

            <!-- Tombol Aksi Bawah -->
            <div class="flex items-center justify-end gap-2 pt-0.5">
                <button type="button" onclick="tutupModalModulPelatihan()" class="px-3.5 py-1.5 border border-slate-300 text-slate-600 hover:bg-slate-100 text-[10px] font-black uppercase tracking-wider rounded-xs cursor-pointer transition-colors">
                    Batal
                </button>
                <button type="submit" form="formTambahKategori" class="px-4 py-1.5 bg-slate-800 hover:bg-slate-900 text-white text-[10px] font-black uppercase tracking-wider rounded-xs shadow-xs cursor-pointer border-none transition-colors">
                    Simpan Modul
                </button>
            </div>

        </div>
    </div>

    <!-- ===================================================================== -->
    <!-- 2. DRAWER LIST KANAN: LEBAR 320PX & TAB RAMPING SESUAI SKALA -->
    <!-- ===================================================================== -->
    <div id="drawerListModul" 
         class="fixed top-2 bottom-2 sm:top-3 sm:bottom-3 right-0 h-[calc(100vh-1rem)] sm:h-[calc(100vh-1.5rem)] w-[82vw] max-w-[320px] sm:w-[320px] bg-white border-y border-l border-slate-200 shadow-2xl rounded-l-xs z-[220] flex flex-col transition-transform duration-300 ease-in-out translate-x-full">
        
        <!-- TAB HANDLE: Slim & Rapi di Tengah Vertikal -->
        <button type="button" 
                onclick="toggleDrawerModul()" 
                id="btnTabDrawerModul" 
                title="Buka / Tutup Daftar Modul"
                class="absolute top-1/2 -left-8 sm:-left-8.5 -translate-y-1/2 w-8 sm:w-8.5 bg-slate-800 hover:bg-slate-900 text-white py-3 px-1 rounded-l-xs shadow-[-5px_0_12px_rgba(0,0,0,0.2)] border-y border-l border-slate-700 flex flex-col items-center gap-2.5 cursor-pointer transition-colors group select-none">
            
            <!-- Panah Chevron Berputar -->
            <svg id="iconChevronDrawer" class="w-3 h-3 text-[#22a0f6] group-hover:scale-110 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-width="3" stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"></path>
            </svg>

            <!-- Teks Vertikal Ramping -->
            <span class="text-[8px] font-black uppercase tracking-widest text-slate-300 [writing-mode:vertical-rl] rotate-180 py-0.5">
                DAFTAR MODUL
            </span>
        </button>

        <!-- Header Drawer Samping -->
        <div class="px-3.5 py-2.5 bg-slate-800 text-white flex items-center justify-between shrink-0 rounded-tl-xs shadow-xs">
            <div class="flex items-center gap-1.5">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                <h4 class="text-[10.5px] font-black uppercase tracking-wider">Modul Terdaftar</h4>
            </div>
            
            <div class="flex items-center gap-1.5">
                <span class="text-[8.5px] font-mono font-bold text-[#22a0f6] bg-sky-950/60 border border-sky-800/80 px-1.5 py-0.2 rounded-xs">
                    {{ count($kategoriPelatihans ?? []) }} Data
                </span>
                
                <!-- Tombol Silang Drawer -->
                <button type="button" 
                        onclick="toggleDrawerModul(false)" 
                        title="Tutup Panel Modul" 
                        class="w-6 h-6 flex items-center justify-center text-slate-400 hover:text-white hover:bg-slate-700 rounded-xs font-bold text-lg leading-none cursor-pointer transition-colors">
                    &times;
                </button>
            </div>
        </div>

        <!-- Filter Pencarian Cepat di Dalam Drawer -->
        <div class="p-2 bg-slate-100 border-b border-slate-200 shrink-0">
            <input type="text" id="inputSearchDrawerModul" onkeyup="filterModulDrawer()" placeholder="Cari modul..." class="w-full px-2.5 py-1 bg-white border border-slate-300 text-[10px] font-bold uppercase tracking-wider text-slate-700 rounded-xs focus:outline-none focus:border-slate-800">
        </div>

        <!-- Area List Scrollable -->
        <div class="flex-1 overflow-y-auto p-2.5 space-y-1 bg-slate-50/50" id="listContainerModulDrawer">
            @forelse($kategoriPelatihans ?? [] as $kp)
            <div class="item-modul-drawer px-2.5 py-1.5 bg-white border border-slate-200 hover:border-slate-300 rounded-xs flex items-center justify-between shadow-2xs transition-all">
                <div class="flex items-center gap-1.5 overflow-hidden pr-2">
                    <span class="w-1 h-1 rounded-full bg-slate-400 shrink-0"></span>
                    <span class="text-[10.5px] font-bold text-slate-800 uppercase tracking-tight truncate nama-modul-text">
                        {{ $kp->nama_kategori }}
                    </span>
                </div>
                
                <form action="{{ route('academic.kategori-pelatihans.destroy', $kp->id) }}" method="POST" onsubmit="return confirm('Hapus modul {{ $kp->nama_kategori }}?')">
                    @csrf @method('DELETE')
                    <button type="submit" title="Hapus Modul" class="p-1 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-xs transition cursor-pointer shrink-0">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                    </button>
                </form>
            </div>
            @empty
            <div class="p-6 text-center text-[10px] text-slate-400 italic">Belum ada modul terdaftar.</div>
            @endforelse
        </div>

        <!-- Footer Drawer -->
        <div class="p-2 bg-white border-t border-slate-200 shrink-0 rounded-bl-xs">
            <p class="text-[8px] text-slate-400 italic text-center">
                * Modul aktif kurikulum tidak dapat dihapus.
            </p>
        </div>

    </div>

</div>

<!-- ========================================================================= -->
<!-- JAVASCRIPT CONTROLLER -->
<!-- ========================================================================= -->
<script>
    function toggleDrawerModul(forceOpen = null) {
        const drawer = document.getElementById('drawerListModul');
        const chevron = document.getElementById('iconChevronDrawer');
        if (!drawer) return;

        const isCurrentlyClosed = drawer.classList.contains('translate-x-full');
        const shouldOpen = forceOpen !== null ? forceOpen : isCurrentlyClosed;

        if (shouldOpen) {
            drawer.classList.remove('translate-x-full');
            if (chevron) chevron.style.transform = 'rotate(180deg)';
        } else {
            drawer.classList.add('translate-x-full');
            if (chevron) chevron.style.transform = 'rotate(0deg)';
        }
    }

    function tutupModalModulPelatihan() {
        document.getElementById('modalModulPelatihan').classList.add('hidden');
        toggleDrawerModul(false);
    }

    function filterModulDrawer() {
        const query = document.getElementById('inputSearchDrawerModul').value.trim().toLowerCase();
        const items = document.querySelectorAll('#listContainerModulDrawer .item-modul-drawer');

        items.forEach(item => {
            const text = item.querySelector('.nama-modul-text').textContent.toLowerCase();
            item.style.display = (text.includes(query) || query === '') ? 'flex' : 'none';
        });
    }

    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            const modal = document.getElementById('modalModulPelatihan');
            if (modal && !modal.classList.contains('hidden')) {
                tutupModalModulPelatihan();
            }
        }
    });
</script>