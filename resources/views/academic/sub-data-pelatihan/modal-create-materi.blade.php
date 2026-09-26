<!-- ========================================================================= -->
<!-- MODAL: BANK MATERI (FORM TENGAH + DRAWER LIST MATERI DI KANAN)            -->
<!-- ========================================================================= -->
<div id="modalBankMateri" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-[200] hidden flex items-center justify-center p-3 sm:p-4 overflow-hidden select-none">
    
    <!-- Backdrop Terkunci (Tidak Menutup Jika Area Luar Diklik) -->
    <div class="fixed inset-0"></div>

    <!-- ===================================================================== -->
    <!-- 1. FORM TAMBAH MATERI DI TENGAH (TETAP UKURAN ASLI: 460PX)            -->
    <!-- ===================================================================== -->
   <div class="relative w-full max-w-[95vw] md:max-w-[590px] bg-white border border-slate-200 shadow-2xl rounded-xs overflow-hidden flex flex-col z-[205] my-auto">
        
        <!-- Header -->
        <div class="px-4 py-2.5 bg-slate-800 text-white flex items-center justify-between shrink-0">
            <div class="flex items-center gap-2">
                <span class="w-1.5 h-1.5 rounded-full bg-[#22a0f6]"></span>
                <h4 class="text-[11px] font-black uppercase tracking-wider">Tambah Materi Pelatihan</h4>
            </div>
            <button type="button" 
                    onclick="tutupModalBankMateri()" 
                    title="Tutup Modal" 
                    class="w-6 h-6 flex items-center justify-center text-slate-400 hover:text-white hover:bg-slate-700 rounded-xs font-bold text-lg leading-none cursor-pointer transition-colors">
                &times;
            </button>
        </div>

        <!-- Body Form -->
        <div class="p-3.5 sm:p-4 space-y-3 bg-slate-50/50">
            
            <div class="bg-white border border-slate-200 p-2.5 rounded-xs shadow-2xs space-y-0.5">
                <span class="text-[8.5px] font-black uppercase tracking-wider text-slate-400 block">Panduan Singkat :</span>
                <p class="text-[10.5px] font-medium text-slate-600 leading-relaxed">
                    Materi adalah topik pembahasan spesifik di dalam suatu program pelatihan.
                </p>
            </div>

            <form id="formTambahMateri" action="{{ route('academic.materi.store') }}" method="POST" class="bg-white border border-slate-200 p-3 rounded-xs shadow-2xs space-y-2.5">
                @csrf
                <div>
                    <label class="block text-[9.5px] font-black uppercase tracking-wider text-slate-700 mb-1">
                        Pilih Program / Modul <span class="text-rose-500">*</span>
                    </label>
                    <select name="jenis_pelatihan" class="w-full bg-slate-50 border border-slate-300 px-3 py-1.5 text-[11px] font-bold text-slate-800 rounded-xs uppercase focus:outline-none focus:border-slate-800 focus:bg-white transition-colors cursor-pointer" required>
                        <option value="">- PILIH PROGRAM PELATIHAN -</option>
                        @foreach ($kategoriPelatihans ?? [] as $kp)
                            <option value="{{ $kp->nama_kategori }}">{{ strtoupper($kp->nama_kategori) }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-[9.5px] font-black uppercase tracking-wider text-slate-700 mb-1">
                        Nama Materi Pelatihan <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="nama_materi" placeholder="CONTOH: DASAR KELISTRIKAN OTOMOTIF..." class="w-full bg-slate-50 border border-slate-300 px-3 py-1.5 text-[11px] font-bold text-slate-800 rounded-xs uppercase focus:outline-none focus:border-slate-800 focus:bg-white transition-colors" required autocomplete="off">
                </div>
            </form>

            <div class="flex items-center justify-end gap-2 pt-0.5">
                <button type="button" onclick="tutupModalBankMateri()" class="px-3.5 py-1.5 border border-slate-300 text-slate-600 hover:bg-slate-100 text-[10px] font-black uppercase tracking-wider rounded-xs cursor-pointer transition-colors">
                    Batal
                </button>
                <button type="submit" form="formTambahMateri" class="px-4 py-1.5 bg-slate-800 hover:bg-slate-900 text-white text-[10px] font-black uppercase tracking-wider rounded-xs shadow-xs cursor-pointer border-none transition-colors">
                    Simpan Materi
                </button>
            </div>

        </div>
    </div>

    <!-- ===================================================================== -->
    <!-- 2. DRAWER LIST MATERI DI KANAN (DENGAN TAB GANTUNG)                   -->
    <!-- ===================================================================== -->
    <div id="drawerListMateri" 
         class="fixed top-2 bottom-2 sm:top-3 sm:bottom-3 right-0 h-[calc(100vh-1rem)] sm:h-[calc(100vh-1.5rem)] w-[84vw] max-w-[340px] sm:w-[340px] bg-white border-y border-l border-slate-200 shadow-2xl rounded-l-xs z-[220] flex flex-col transition-transform duration-300 ease-in-out translate-x-full">
        
        <!-- Tab Gantung Vertikal -->
        <button type="button" 
                onclick="toggleDrawerMateri()" 
                id="btnTabDrawerMateri" 
                title="Buka / Tutup Daftar Materi" 
                class="absolute top-1/2 -left-8 sm:-left-8.5 -translate-y-1/2 w-8 sm:w-8.5 bg-slate-800 hover:bg-slate-900 text-white py-3 px-1 rounded-l-xs shadow-[-5px_0_12px_rgba(0,0,0,0.2)] border-y border-l border-slate-700 flex flex-col items-center gap-2.5 cursor-pointer transition-colors group select-none">
            
            <svg id="iconChevronDrawerMateri" class="w-3 h-3 text-[#22a0f6] group-hover:scale-110 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-width="3" stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"></path>
            </svg>

            <span class="text-[8px] font-black uppercase tracking-widest text-slate-300 [writing-mode:vertical-rl] rotate-180 py-0.5">
                DAFTAR MATERI
            </span>
        </button>

        <!-- Header Drawer -->
        <div class="px-3.5 py-2.5 bg-slate-800 text-white flex items-center justify-between shrink-0 rounded-tl-xs shadow-xs">
            <div class="flex items-center gap-1.5">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                <h4 class="text-[10.5px] font-black uppercase tracking-wider">Materi Terdaftar</h4>
            </div>
            
            <div class="flex items-center gap-1.5">
                <span id="badge_total_materi_drawer" class="text-[8.5px] font-mono font-bold text-[#22a0f6] bg-sky-950/60 border border-sky-800/80 px-1.5 py-0.2 rounded-xs">
                    {{ count($materis ?? []) }} Data
                </span>
                <button type="button" 
                        onclick="toggleDrawerMateri(false)" 
                        title="Tutup Panel Materi" 
                        class="w-6 h-6 flex items-center justify-center text-slate-400 hover:text-white hover:bg-slate-700 rounded-xs font-bold text-lg leading-none cursor-pointer transition-colors">
                    &times;
                </button>
            </div>
        </div>

        <!-- Filter & Search Toolbar -->
        <div class="p-2 bg-slate-100 border-b border-slate-200 shrink-0 space-y-1.5">
            <select id="filter_materi_drawer" onchange="filterMateriDrawerList()" class="w-full bg-white border border-slate-300 px-2 py-1 text-[10px] font-bold text-slate-700 rounded-xs uppercase focus:outline-none focus:border-slate-800 cursor-pointer">
                <option value="">- SEMUA PROGRAM PELATIHAN </option>
                @foreach ($kategoriPelatihans ?? [] as $kp)
                    <option value="{{ strtolower($kp->nama_kategori) }}">{{ strtoupper($kp->nama_kategori) }}</option>
                @endforeach
            </select>
            <input type="text" id="inputSearchMateriDrawer" onkeyup="filterMateriDrawerList()" placeholder="Cari judul materi..." class="w-full px-2.5 py-1 bg-white border border-slate-300 text-[10px] font-bold uppercase tracking-wider text-slate-700 rounded-xs focus:outline-none focus:border-slate-800">
        </div>

        <!-- List Area Scrollable -->
        <div class="flex-1 overflow-y-auto p-2.5 space-y-1 bg-slate-50/50 [scrollbar-width:thin] [scrollbar-color:#cbd5e1_transparent]" id="listContainerMateriDrawer">
            @forelse($materis ?? [] as $mat)
            <div class="item-materi-drawer px-2.5 py-1.5 bg-white border border-slate-200 hover:border-slate-300 rounded-xs flex items-center justify-between shadow-2xs transition-all" data-program="{{ strtolower($mat->jenis_pelatihan) }}">
                <div class="flex flex-col min-w-0 pr-2">
                    <span class="text-[10.5px] font-bold text-slate-800 uppercase tracking-tight truncate nama-materi-drawer-text" title="{{ $mat->nama_materi }}">
                        {{ $mat->nama_materi }}
                    </span>
                    <span class="text-[8px] font-bold text-[#22a0f6] uppercase tracking-wider">
                        {{ $mat->jenis_pelatihan }}
                    </span>
                </div>
                
                <form action="{{ route('academic.materi.destroy', $mat->id) }}" method="POST" onsubmit="return confirm('Hapus materi {{ $mat->nama_materi }}?')">
                    @csrf @method('DELETE')
                    <button type="submit" title="Hapus Materi" class="p-1 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-xs transition cursor-pointer shrink-0">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                    </button>
                </form>
            </div>
            @empty
            <div class="p-6 text-center text-[10px] text-slate-400 italic">Belum ada materi terdaftar.</div>
            @endforelse

            <div id="empty-state-materi-drawer" class="hidden p-6 text-center text-[10px] text-slate-400 italic">
                Tidak ada materi yang sesuai filter.
            </div>
        </div>

        <!-- Footer Drawer -->
        <div class="p-2 bg-white border-t border-slate-200 shrink-0 rounded-bl-xs">
            <p class="text-[8px] text-slate-400 italic text-center">
                * Materi yang aktif digunakan tidak dapat dihapus.
            </p>
        </div>

    </div>

</div>

<!-- JAVASCRIPT CONTROLLER DRAWER MATERI -->
<script>
    function toggleDrawerMateri(forceOpen = null) {
        const drawer = document.getElementById('drawerListMateri');
        const chevron = document.getElementById('iconChevronDrawerMateri');
        if (!drawer) return;

        const isClosed = drawer.classList.contains('translate-x-full');
        const shouldOpen = forceOpen !== null ? forceOpen : isClosed;

        if (shouldOpen) {
            drawer.classList.remove('translate-x-full');
            if (chevron) chevron.style.transform = 'rotate(180deg)';
        } else {
            drawer.classList.add('translate-x-full');
            if (chevron) chevron.style.transform = 'rotate(0deg)';
        }
    }

    function tutupModalBankMateri() {
        const modal = document.getElementById('modalBankMateri');
        if (modal) modal.classList.add('hidden');
        toggleDrawerMateri(false);
    }

    function filterMateriDrawerList() {
        const prog = document.getElementById('filter_materi_drawer').value.trim().toLowerCase();
        const search = document.getElementById('inputSearchMateriDrawer').value.trim().toLowerCase();
        const items = document.querySelectorAll('#listContainerMateriDrawer .item-materi-drawer');
        const emptyState = document.getElementById('empty-state-materi-drawer');
        let visible = 0;

        items.forEach(item => {
            const itemProg = (item.dataset.program || '').trim().toLowerCase();
            const itemText = item.querySelector('.nama-materi-drawer-text').textContent.toLowerCase();

            const matchProg = !prog || itemProg === prog;
            const matchSearch = !search || itemText.includes(search);

            if (matchProg && matchSearch) {
                item.style.display = 'flex';
                visible++;
            } else {
                item.style.display = 'none';
            }
        });

        const badge = document.getElementById('badge_total_materi_drawer');
        if (badge) badge.textContent = visible + ' Data';
        if (emptyState) emptyState.classList.toggle('hidden', visible > 0);
    }
</script>