<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between w-full print:hidden">
            <div class="flex items-center gap-2">
                <h2 class="text-sm font-black text-slate-700 uppercase tracking-tighter">Kelola Nilai Kolektif</h2>
                <span class="text-slate-300 text-xs">/</span>
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest italic text-[#22a0f6]">Matrix Grading System</span>
            </div>
            
            <!-- 🛠️ UPDATE: Tombol kembali memicu validasi filter kata kunci Step 1 sebelum eksekusi Excel -->
            <div class="flex items-center gap-3">
                <button type="button" onclick="validasiAksiExcel('import')" 
                        class="px-4 py-1.5 bg-emerald-600 border border-emerald-700 text-white rounded-sm text-[10px] font-black hover:bg-emerald-700 transition uppercase tracking-widest shadow-sm flex items-center justify-center gap-1">
                    Import Excel
                </button>

                <button type="button" onclick="validasiAksiExcel('export')" 
                   class="px-4 py-1.5 bg-slate-700 border border-slate-800 text-white rounded-sm text-[10px] font-black hover:bg-slate-800 transition uppercase tracking-widest shadow-sm flex items-center justify-center">
                    Export Excel
                </button>
            </div>
        </div>
    </x-slot>

    <form id="form-import" action="{{ route('nilai.import') }}" method="POST" enctype="multipart/form-data" class="hidden">
        @csrf
        <input type="file" id="file-import" name="file_excel" accept=".xlsx, .xls, .csv" onchange="document.getElementById('form-import').submit();">
    </form>

    <div id="ajax-notification" class="max-w-[1400px] mx-auto px-2 mt-4 hidden">
        <div id="ajax-notif-box" class="p-3 text-[10px] font-black uppercase tracking-widest flex justify-between items-center shadow-sm">
            <span id="ajax-notif-msg"></span>
            <button type="button" onclick="document.getElementById('ajax-notification').classList.add('hidden')" class="font-bold text-sm">&times;</button>
        </div>
    </div>

    <div class="py-4 max-w-[1400px] mx-auto px-2">
        <form id="matrix-form" action="{{ route('nilai.store') }}" method="POST">
            @csrf

            <!-- STEP 1: MATRIX FILTER LOCK -->
            <div class="bg-white border border-slate-200 p-5 mb-5 shadow-sm rounded-none">
                <div class="border-b border-slate-100 pb-3 mb-4 flex items-center justify-between">
                    <h3 class="text-[11px] font-black text-slate-600 uppercase tracking-widest italic">Step 1: Matrix Filter Lock</h3>
                    <span class="text-[9px] font-bold text-[#22a0f6] uppercase tracking-wide">● Sistem AJAX Aktif: Auto-Adapt Komponen Nilai JSON</span>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                    <div>
                        <label class="block text-[9px] font-black text-slate-700 uppercase tracking-widest mb-1.5">1. Kategori Spektrum Pelatihan</label>
                        <select id="filter-sektor" name="spektrum_kategori" required
                                class="w-full px-3 py-2.5 bg-white border border-slate-300 text-[10px] font-black text-slate-800 uppercase tracking-widest rounded-none cursor-pointer focus:ring-1 focus:ring-[#22a0f6] focus:border-[#22a0f6]">
                            <option value="">-- PILIH SPEKTRUM KATEGORI --</option>
                            <option value="Pengetahuan Teknis">PENGETAHUAN TEKNIS</option>
                            <option value="Pengetahuan Non Teknis">PENGETAHUAN NON TEKNIS</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-[9px] font-black text-slate-700 uppercase tracking-widest mb-1.5">2. Pilih Kelas Program Pelatihan</label>
                        <select id="filter-pelatihan" name="pelatihan_id" disabled required
                                class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 text-[10px] font-black uppercase tracking-widest rounded-none cursor-not-allowed transition-all focus:ring-1 focus:ring-[#22a0f6] focus:border-[#22a0f6]">
                            <option value="">-- KUNCI KATEGORI SPEKTRUM DULU --</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-[9px] font-black text-slate-700 uppercase tracking-widest mb-1.5">3. Mentor Pengampu (Auto-Detect)</label>
                        <input type="text" id="display-mentor" disabled placeholder="-- KUNCI PROGRAM KELAS DULU --"
                               class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 text-[10px] font-black uppercase tracking-widest rounded-none cursor-not-allowed transition-all">
                        <input type="hidden" id="filter-dosen" name="trainer_id">
                    </div>
                </div>
            </div>

            <!-- STEP 2: PARTICIPANT SCORE ENTRY MATRIX WITH SCROLL & FIXED LAYOUT -->
            <div id="matrix-table-container" class="bg-white border border-slate-200 shadow-sm rounded-none overflow-hidden opacity-40 pointer-events-none transition-all duration-300">
                <div class="px-6 py-4 border-b border-slate-200 bg-slate-50 flex items-center justify-between">
                    <h3 class="text-[11px] font-black text-slate-600 uppercase tracking-widest italic">Step 2: Participant Score Entry Matrix</h3>
                    <button type="submit" id="btn-save-matrix" class="px-6 py-2.5 bg-[#22a0f6] text-white text-[10px] font-black uppercase tracking-widest hover:bg-blue-600 transition shadow-sm rounded-none border border-blue-600">
                        💾 SAVE ENTRY MATRIX
                    </button>
                </div>

                <!-- SCROLL CONTAINER OUTSIDE -->
                <div class="w-full overflow-x-auto scrollbar-thin scrollbar-thumb-slate-200">
                    <table class="w-full text-left border-collapse table-fixed min-w-[1600px]" id="matrix-main-table">
                        <thead>
                            <tr class="bg-white border-b-2 border-slate-100" id="matrix-header-row">
                                <th class="px-4 py-4 text-[10px] font-black text-slate-400 uppercase w-12 text-center sticky left-0 bg-white z-20 border-r border-slate-100">No.</th>
                                <th class="px-4 py-4 text-[10px] font-black text-slate-400 uppercase w-72 sticky left-12 bg-white z-20 border-r border-slate-100 shadow-[4px_0_8px_-4px_rgba(0,0,0,0.05)]">Participant Name</th>
                                <th class="px-4 py-4 text-[10px] font-black text-slate-400 uppercase dynamic-header-mark w-[300px]">Catatan / Evaluasi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100" id="participant-rows">
                            @foreach($pesertas as $index => $p)
                            <tr class="hover:bg-slate-50/80 transition-all row-peserta" data-sektor="{{ $p->pelatihan }}">
                                <td class="px-4 py-4 text-[11px] font-black text-slate-400 text-center index-kolom sticky left-0 bg-white z-10 border-r border-slate-100">000</td>
                                <td class="px-4 py-4 sticky left-12 bg-white z-10 border-r border-slate-100 shadow-[4px_0_8px_-4px_rgba(0,0,0,0.05)]">
                                    <p class="text-[12px] font-black text-slate-700 uppercase truncate max-w-[240px]">{{ $p->nama_lengkap }}</p>
                                    <p class="text-[9px] font-bold text-slate-400 italic uppercase tracking-wider mt-0.5">{{ $p->tempat_kerja ?? 'PT MILI' }}</p>
                                    <input type="hidden" name="nilai[{{ $index }}][peserta_id]" value="{{ $p->id }}" class="input-peserta-id">
                                </td>
                                <td class="px-4 py-3 default-catatan-cell w-[300px]">
                                    <input type="text" name="nilai[{{ $index }}][catatan]" placeholder="Ketik catatan..." disabled
                                           class="input-pintar input-keterangan w-full px-3 py-2 bg-white border border-slate-200 text-[10px] font-bold uppercase tracking-wide rounded-none placeholder-slate-300 focus:ring-1 focus:ring-[#22a0f6] focus:border-[#22a0f6]">
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </form>
    </div>

    <!-- WARNING MODAL MARKUP ASLI (Tetap murni bawaan lu bray) -->
    <div id="matrix-warning-modal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm hidden">
        <div class="bg-white border border-slate-300 max-w-md w-full shadow-xl rounded-none overflow-hidden">
            <div class="bg-slate-50 px-4 py-3 border-b border-slate-200 flex items-center gap-2">
                <span class="text-amber-500 text-sm">⚠️</span>
                <h4 class="text-[11px] font-black text-slate-700 uppercase tracking-widest italic">System Matrix Warning</h4>
            </div>
            <div class="p-5">
                <p id="modal-warning-message" class="text-[11px] font-bold text-slate-600 uppercase tracking-wide leading-relaxed">
                    Pesan peringatan sistem akan muncul di sini...
                </p>
            </div>
            <div class="bg-slate-50 px-4 py-2.5 border-t border-slate-100 flex justify-end">
                <button type="button" onclick="tutupWarningModal()" 
                        class="bg-slate-700 hover:bg-slate-800 text-white px-4 py-1.5 text-[10px] font-black uppercase tracking-widest transition shadow-sm rounded-none">
                    UNDERSTOOD
                </button>
            </div>
        </div>
    </div>

    <!-- 🛠️ JAVASCRIPT VALIDASI FILTER KATA KUNCI STEP 1 -->
    <script>
        function tutupWarningModal() {
            document.getElementById('matrix-warning-modal').classList.add('hidden');
        }

        function validasiAksiExcel(tipe) {
            const sektor = document.getElementById('filter-sektor').value;
            const pelatihan = document.getElementById('filter-pelatihan').value;

            // Jika kata kunci filter kategori atau program kelas masih kosong, tembak warning bawaan lu
            if (!sektor || !pelatihan) {
                document.getElementById('modal-warning-message').innerText = 
                    "Peringatan: Silakan masukkan kata kunci filter di 'Step 1: Matrix Filter Lock' (Kategori & Kelas Program Pelatihan) terlebih dahulu sebelum melakukan " + tipe + " data Excel!";
                document.getElementById('matrix-warning-modal').classList.remove('hidden');
            } else {
                // Jika sudah diisi semua, jalankan aksi aslinya
                if (tipe === 'import') {
                    jalankanImport();
                } else if (tipe === 'export') {
                    window.location.href = "{{ route('nilai.export') }}";
                }
            }
        }
    </script>

    @push('scripts')
        @vite('resources/js/matrix-grading.js')
    @endpush
</x-app-layout>