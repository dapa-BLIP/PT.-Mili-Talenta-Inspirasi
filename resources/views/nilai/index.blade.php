<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between w-full print:hidden">
            <div class="flex items-center gap-2">
                <h2 class="text-xs font-black text-slate-800 uppercase tracking-[0.2em]">Kelola Nilai Kolektif</h2>
                <span class="text-slate-300 text-xs">/</span>
                <span class="text-[10px] font-bold text-[#22a0f6] uppercase tracking-widest italic">Matrix Grading System</span>
            </div>
            
            <div class="flex items-center gap-1.5">
                <button type="button" onclick="validasiAksiExcel('import')" style="border-radius: 0.125rem;" 
                    class="px-3.5 py-1.5 bg-emerald-600 border border-emerald-700 text-white text-[9px] font-black hover:bg-emerald-700 transition uppercase tracking-widest shadow-xs flex items-center justify-center gap-1">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                    Import Excel
                </button>

                <button type="button" onclick="validasiAksiExcel('export')" style="border-radius: 0.125rem;" 
                    class="px-3.5 py-1.5 bg-slate-800 border border-slate-900 text-white text-[9px] font-black hover:bg-slate-900 transition uppercase tracking-widest shadow-xs flex items-center justify-center gap-1">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                    Export Excel
                </button>
            </div>
        </div>
    </x-slot>

    <!-- FORM TERSEMBUNYI UNTUK IMPORT EXCEL -->
    <form id="form-import" action="{{ route('nilai.import') }}" method="POST" enctype="multipart/form-data" class="hidden">
        @csrf
        <input type="hidden" name="pelatihan_id" id="import-pelatihan-id">
        <input type="hidden" name="trainer_id" id="import-trainer-id">
        <input type="file" id="file-import" name="file_excel" accept=".xlsx, .xls, .csv" onchange="document.getElementById('form-import').submit();">
    </form>

    <div class="space-y-4 print:py-0 print:px-0">
        
        <!-- ALERT NOTIFIKASI AJAX -->
        <div id="ajax-notification" class="hidden">
            <div id="ajax-notif-box" style="border-radius: 0.125rem;" class="px-4 py-2 bg-emerald-50 border border-emerald-200 text-emerald-800 shadow-xs flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span style="border-radius: 0.125rem;" class="text-[10px] font-black uppercase bg-emerald-600 text-white px-1.5 py-0.5">OK</span>
                    <p id="ajax-notif-msg" class="text-[10px] font-bold tracking-wide uppercase"></p>
                </div>
                <button type="button" onclick="document.getElementById('ajax-notification').classList.add('hidden')" class="text-emerald-600 font-bold">&times;</button>
            </div>
        </div>

        @if(session('success'))
            <div style="border-radius: 0.125rem;" class="px-4 py-2 bg-emerald-50 border border-emerald-200 text-emerald-800 shadow-xs flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span style="border-radius: 0.125rem;" class="text-[10px] font-black uppercase bg-emerald-600 text-white px-1.5 py-0.5">OK</span>
                    <p class="text-[10px] font-bold tracking-wide uppercase">{{ session('success') }}</p>
                </div>
                <button type="button" class="text-emerald-600 font-black text-sm" onclick="this.parentElement.remove()">&times;</button>
            </div>
        @endif

        <form id="matrix-form" onsubmit="handleMatrixSubmit(event)" class="space-y-4">
            @csrf
            <input type="hidden" id="form-pelatihan-id" name="pelatihan_id">
            <input type="hidden" id="form-trainer-id" name="trainer_id">

            <!-- 1. KOTAK FILTER PARAMETER -->
            <div style="border-radius: 0.125rem;" class="bg-white border border-slate-200 p-4 shadow-sm print:hidden">
                <div class="border-b border-slate-100 pb-2.5 mb-3 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <h3 class="text-[11px] font-black text-slate-700 uppercase tracking-widest italic">Filter Parameter Nilai</h3>
                        <span style="border-radius: 0.125rem;" class="text-[8px] font-black text-[#22a0f6] bg-blue-50 border border-blue-100 px-1.5 py-0.5 uppercase tracking-wider">Dynamic Matrix</span>
                    </div>
                    <span id="filter-status-info" class="text-[9px] font-bold text-amber-600 uppercase tracking-wide">
                        ● Pilih Kategori Spektrum Terlebih Dahulu
                    </span>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                    <!-- SPEKTRUM KATEGORI -->
                    <div>
                        <label class="block text-[9px] font-black text-slate-600 uppercase tracking-widest mb-1">Kategori Spektrum</label>
                        <select id="filter-sektor" name="spektrum_kategori" required style="border-radius: 0.125rem;"
                                class="w-full px-2.5 py-1.5 bg-white border border-slate-300 text-[10px] font-black text-slate-800 uppercase tracking-wider cursor-pointer focus:ring-1 focus:ring-blue-500 shadow-2xs">
                            <option value="">-- PILIH SPEKTRUM KATEGORI --</option>
                            @foreach($sektors as $sektor)
                                <option value="{{ $sektor }}">{{ strtoupper($sektor) }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- PROGRAM PELATIHAN -->
                    <div>
                        <label class="block text-[9px] font-black text-slate-600 uppercase tracking-widest mb-1">Program Pelatihan</label>
                        <select id="filter-pelatihan" name="pelatihan_id" disabled required style="border-radius: 0.125rem;"
                                class="w-full px-2.5 py-1.5 bg-slate-50 border border-slate-200 text-[10px] font-black uppercase tracking-wider cursor-not-allowed transition-all focus:ring-1 focus:ring-blue-500 shadow-2xs">
                            <option value="">-- KUNCI KATEGORI SPEKTRUM DULU --</option>
                        </select>
                    </div>

                    <!-- MENTOR AUTO-DETECT -->
                    <div>
                        <label class="block text-[9px] font-black text-slate-600 uppercase tracking-widest mb-1">Mentor Pengampu</label>
                        <input type="text" id="display-mentor" disabled placeholder="-- KUNCI PROGRAM KELAS DULU --" style="border-radius: 0.125rem;"
                               class="w-full px-2.5 py-1.5 bg-slate-50 border border-slate-200 text-[10px] font-black uppercase tracking-wider text-slate-600 cursor-not-allowed shadow-2xs transition-all">
                        <input type="hidden" id="filter-dosen" name="trainer_id">
                    </div>
                </div>
            </div>

            <!-- 2. TABEL ENTRY MATRIKS (RINGKAS & COMPACT) -->
            <div id="matrix-table-container" style="border-radius: 0.125rem;" class="bg-white border border-slate-200 shadow-sm overflow-hidden opacity-40 pointer-events-none transition-all duration-300">
                <div class="px-4 py-2.5 border-b border-slate-200 bg-slate-50 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <h3 class="text-[11px] font-black text-slate-700 uppercase tracking-widest italic">Input Nilai Matrix</h3>
                        <span style="border-radius: 0.125rem;" class="text-[8.5px] font-black text-[#22a0f6] bg-blue-50 border border-blue-100 px-1.5 py-0.2 uppercase tracking-wider">
                            Total: <span id="visible-count">{{ sprintf('%02d', $pesertas->count()) }}</span> Peserta
                        </span>
                    </div>

                    <button type="submit" id="btn-save-matrix" style="border-radius: 0.125rem;" 
                        class="px-4 py-1.5 bg-[#22a0f6] text-white text-[9px] font-black uppercase tracking-widest hover:bg-blue-600 transition shadow-xs flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
                        SIMPAN DATA NILAI
                    </button>
                </div>

                <!-- SCROLL CONTAINER RINGKAS -->
                <div class="w-full overflow-x-auto">
                    <table class="w-full text-left border-collapse table-fixed min-w-[750px]" id="matrix-main-table">
                        <thead>
                            <tr class="bg-slate-50/80 border-b border-slate-200 text-slate-500" id="matrix-header-row">
                                <th class="w-12 px-2 py-2 text-[9px] font-black uppercase tracking-widest text-center sticky left-0 bg-slate-50 z-20 border-r border-slate-200">No.</th>
                                <th class="w-52 px-3 py-2 text-[9px] font-black uppercase tracking-widest sticky left-12 bg-slate-50 z-20 border-r border-slate-200 shadow-[2px_0_4px_-2px_rgba(0,0,0,0.05)]">Participant Name</th>
                                <!-- Kolom Dinamis (UTS, UAS, Praktek) disuntikkan sebelum kolom Catatan -->
                                <th class="w-56 px-3 py-2 text-[9px] font-black uppercase tracking-widest dynamic-header-mark" id="th-catatan">Catatan / Evaluasi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100" id="participant-rows">
                            @foreach($pesertas as $index => $p)
                            @php
                                $rawStatus = strtoupper(trim($p->status ?? 'AKTIF'));
                                $isAktif = in_array($rawStatus, ['AKTIF', 'ACTIVE', '']);
                            @endphp
                            <tr class="transition-colors row-peserta {{ $isAktif ? 'hover:bg-slate-50/70' : 'bg-slate-50/50 opacity-75' }}" 
                                data-sektor="{{ trim($p->pelatihan ?? '') }}"
                                data-status="{{ $isAktif ? 'AKTIF' : $rawStatus }}"
                                data-peserta-id="{{ $p->id }}">
                                
                                <td class="px-2 py-2 text-[10px] font-black text-slate-400 text-center font-mono align-middle index-kolom sticky left-0 bg-white z-10 border-r border-slate-100">
                                    {{ str_pad($index + 1, 4, '0', STR_PAD_LEFT) }}
                                </td>

                                <td class="px-3 py-2 align-middle sticky left-12 bg-white z-10 border-r border-slate-100 shadow-[2px_0_4px_-2px_rgba(0,0,0,0.05)]">
                                    <div class="truncate">
                                        <div class="flex items-center gap-1 leading-tight">
                                            <span class="text-[10.5px] font-black text-slate-800 uppercase tracking-tight truncate">{{ $p->nama_lengkap }}</span>
                                            @if(in_array(strtolower($p->gender ?? ''), ['pria', 'l']))
                                                <span style="border-radius: 0.125rem;" class="text-[6.5px] font-black bg-blue-50 text-blue-600 border border-blue-100 px-1 py-0.2 font-mono shrink-0">L</span>
                                            @else
                                                <span style="border-radius: 0.125rem;" class="text-[6.5px] font-black bg-pink-50 text-pink-600 border border-pink-100 px-1 py-0.2 font-mono shrink-0">P</span>
                                            @endif
                                        </div>
                                        
                                        <div class="flex items-center gap-1.5 mt-0.5 truncate">
                                            <span class="text-[8px] font-bold text-slate-400 uppercase tracking-tight truncate">{{ $p->tempat_kerja ?? 'PT MILI' }}</span>
                                            @if(!$isAktif)
                                                <span style="border-radius: 0.125rem;" class="text-[7px] font-black px-1 py-0.2 uppercase tracking-wider {{ in_array($rawStatus, ['NON-AKTIF', 'NONAKTIF', 'INACTIVE']) ? 'bg-rose-100 text-rose-700' : 'bg-amber-100 text-amber-800' }}">
                                                    ● {{ $rawStatus }}
                                                </span>
                                            @endif
                                        </div>

                                        @if($isAktif)
                                            <input type="hidden" name="nilai[{{ $index }}][peserta_id]" value="{{ $p->id }}" class="input-peserta-id">
                                        @endif
                                    </div>
                                </td>

                                <!-- CATATAN (COMPACT H-7) -->
                                <td class="px-3 py-2 align-middle default-catatan-cell cell-catatan">
                                    <input type="text" name="nilai[{{ $index }}][catatan]" 
                                           placeholder="{{ $isAktif ? 'Ketik catatan...' : 'Status ' . $rawStatus . ' (Terkunci)' }}" 
                                           disabled style="border-radius: 0.125rem;"
                                           class="{{ $isAktif ? 'input-pintar input-keterangan bg-white text-slate-800' : 'bg-slate-100 text-slate-400 cursor-not-allowed italic' }} w-full px-2.5 py-1 h-7 border border-slate-300 text-[9.5px] font-medium placeholder:text-slate-300 focus:ring-1 focus:ring-blue-500 transition-all">
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </form>
    </div>

    <!-- WARNING MODAL -->
    <div id="matrix-warning-modal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs hidden">
        <div style="border-radius: 0.125rem;" class="bg-white border border-slate-300 max-w-md w-full shadow-2xl overflow-hidden">
            <div class="bg-slate-50 px-4 py-3 border-b border-slate-200 flex items-center justify-between">
                <h4 class="text-[10px] font-black text-slate-700 uppercase tracking-widest">System Matrix Warning</h4>
                <button type="button" onclick="tutupWarningModal()" class="text-slate-400 hover:text-slate-600 font-bold text-sm">&times;</button>
            </div>
            <div class="p-5">
                <p id="modal-warning-message" class="text-[11px] font-bold text-slate-600 uppercase tracking-wide leading-relaxed"></p>
            </div>
            <div class="bg-slate-50 px-4 py-2.5 border-t border-slate-100 flex justify-end">
                <button type="button" onclick="tutupWarningModal()" style="border-radius: 0.125rem;" 
                        class="bg-slate-800 hover:bg-slate-900 text-white px-4 py-1.5 text-[9px] font-black uppercase tracking-widest transition shadow-xs">
                    UNDERSTOOD
                </button>
            </div>
        </div>
    </div>

    <!-- JAVASCRIPT MATRIX CONTROLLER -->
    <script>
        let globalPelatihans = [];
        let currentKomponen = [];

        // 1. FILTER SPEKTRUM
        document.getElementById('filter-sektor').addEventListener('change', async function() {
            const sektor = this.value;
            const selectPelatihan = document.getElementById('filter-pelatihan');
            const displayMentor = document.getElementById('display-mentor');
            const hiddenMentor = document.getElementById('filter-dosen');
            const statusInfo = document.getElementById('filter-status-info');

            resetMatrixTable();
            filterTableRowsBySektor(sektor);

            selectPelatihan.innerHTML = '<option value="">-- MEMUAT MATERI... --</option>';
            displayMentor.value = '';
            displayMentor.placeholder = '-- KUNCI PROGRAM KELAS DULU --';
            hiddenMentor.value = '';
            selectPelatihan.disabled = true;
            selectPelatihan.className = "w-full px-2.5 py-1.5 bg-slate-50 border border-slate-200 text-[10px] font-black text-slate-400 uppercase tracking-wider cursor-not-allowed shadow-2xs";

            if (!sektor) {
                selectPelatihan.innerHTML = '<option value="">-- KUNCI KATEGORI SPEKTRUM DULU --</option>';
                statusInfo.innerText = "● Pilih Kategori Spektrum Terlebih Dahulu";
                statusInfo.className = "text-[9px] font-bold text-amber-600 uppercase tracking-wide";
                return;
            }

            try {
                const res = await fetch(`{{ route('nilai.getMateri') }}?sektor=${encodeURIComponent(sektor)}`);
                globalPelatihans = await res.json();

                selectPelatihan.innerHTML = '<option value="">-- PILIH PROGRAM PELATIHAN --</option>';
                globalPelatihans.forEach(p => {
                    selectPelatihan.innerHTML += `<option value="${p.id}">${p.nama_pelatihan.toUpperCase()}</option>`;
                });

                selectPelatihan.disabled = false;
                selectPelatihan.className = "w-full px-2.5 py-1.5 bg-white border border-slate-300 text-[10px] font-black text-slate-800 uppercase tracking-wider cursor-pointer focus:ring-1 focus:ring-blue-500 shadow-2xs";
                statusInfo.innerText = "● Silakan Pilih Program Pelatihan";
                statusInfo.className = "text-[9px] font-bold text-blue-500 uppercase tracking-wide";
            } catch (err) {
                console.error("Gagal load materi:", err);
            }
        });

        // 2. PILIH PROGRAM PELATIHAN
        document.getElementById('filter-pelatihan').addEventListener('change', async function() {
            const pelatihanId = this.value;
            const displayMentor = document.getElementById('display-mentor');
            const hiddenMentor = document.getElementById('filter-dosen');
            const statusInfo = document.getElementById('filter-status-info');
            const tableContainer = document.getElementById('matrix-table-container');

            resetMatrixInputs();

            if (!pelatihanId) {
                displayMentor.value = '';
                displayMentor.placeholder = '-- KUNCI PROGRAM KELAS DULU --';
                hiddenMentor.value = '';
                statusInfo.innerText = "● Silakan Pilih Program Pelatihan";
                tableContainer.classList.add('opacity-40', 'pointer-events-none');
                return;
            }

            const currentPelatihan = globalPelatihans.find(p => p.id == pelatihanId);

            // Auto-detect Mentor
            if (currentPelatihan && currentPelatihan.mentor) {
                displayMentor.value = currentPelatihan.mentor.nama_mentor.toUpperCase();
                hiddenMentor.value = currentPelatihan.mentor.id;
            } else if (currentPelatihan && currentPelatihan.trainer_id) {
                displayMentor.value = "MENTOR ID: " + currentPelatihan.trainer_id;
                hiddenMentor.value = currentPelatihan.trainer_id;
            } else {
                displayMentor.value = "MENTOR BELUM DITENTUKAN";
                hiddenMentor.value = "";
            }

            // Ambil array komponen_nilai asli
            currentKomponen = currentPelatihan?.komponen_nilai || [];
            rebuildMatrixColumns(currentKomponen);

            document.getElementById('form-pelatihan-id').value = pelatihanId;
            document.getElementById('form-trainer-id').value = hiddenMentor.value;

            // Buka Kunci Matriks
            tableContainer.classList.remove('opacity-40', 'pointer-events-none');
            document.querySelectorAll('.row-peserta').forEach(row => {
                const isAktif = row.getAttribute('data-status') === 'AKTIF';
                if (isAktif) {
                    row.querySelectorAll('.input-pintar').forEach(el => el.disabled = false);
                } else {
                    row.querySelectorAll('.input-pintar').forEach(el => el.disabled = true);
                }
            });

            statusInfo.innerText = "✓ Matriks Terbuka (Peserta Aktif Dapat Diinput)";
            statusInfo.className = "text-[9px] font-bold text-emerald-600 uppercase tracking-wide";

            // Load Existing Score JSON
            try {
                const res = await fetch(`{{ route('nilai.getNilaiExisting') }}?pelatihan_id=${encodeURIComponent(pelatihanId)}`);
                const existingData = await res.json();

                document.querySelectorAll('.row-peserta').forEach(row => {
                    const pesertaId = row.getAttribute('data-peserta-id');
                    const catatanInput = row.querySelector('.input-keterangan');
                    const isAktif = row.getAttribute('data-status') === 'AKTIF';

                    if (catatanInput && isAktif) catatanInput.value = '';
                    row.querySelectorAll('.input-score-dynamic').forEach(input => input.value = '');

                    const match = existingData.find(d => d.peserta_id == pesertaId);
                    if (match) {
                        if (match.scores) {
                            Object.keys(match.scores).forEach(k => {
                                const target = row.querySelector(`input[data-komponen="${k}"]`);
                                if (target) target.value = match.scores[k];
                            });
                        }
                        if (match.catatan && catatanInput && isAktif) {
                            catatanInput.value = match.catatan;
                        }
                    }
                });
            } catch (err) {
                console.error("Gagal load nilai existing:", err);
            }
        });

        // 3. FILTER BARIS SISWA
        function filterTableRowsBySektor(sektorVal) {
            const rows = document.querySelectorAll('.row-peserta');
            let count = 0;
            const sektorLower = (sektorVal || '').toLowerCase().trim();

            rows.forEach(row => {
                const rawPelatihan = (row.getAttribute('data-sektor') || '').toLowerCase().trim();
                let isMatch = false;

                if (!sektorVal) {
                    isMatch = true;
                } else if (sektorLower.includes('non')) {
                    isMatch = rawPelatihan.includes('non') || rawPelatihan.includes('soft') || rawPelatihan.includes('umum');
                } else if (sektorLower.includes('teknis')) {
                    isMatch = !rawPelatihan.includes('non');
                } else {
                    isMatch = rawPelatihan.includes(sektorLower);
                }

                if (isMatch) {
                    row.style.display = '';
                    count++;
                    const idxCol = row.querySelector('.index-kolom');
                    if (idxCol) idxCol.textContent = String(count).padStart(4, '0');
                } else {
                    row.style.display = 'none';
                }
            });

            document.getElementById('visible-count').textContent = String(count).padStart(2, '0');
        }

        // 4. REBUILD KOLOM DINAMIS (LEBAR & TINGGI COMPACT)
        function rebuildMatrixColumns(komponenList) {
            document.querySelectorAll('.th-dynamic-score').forEach(el => el.remove());
            document.querySelectorAll('.td-dynamic-score').forEach(el => el.remove());

            const thCatatan = document.getElementById('th-catatan');

            // Header Komponen Nilai: Lebar w-16
            komponenList.forEach(k => {
                const th = document.createElement('th');
                th.className = "th-dynamic-score w-16 px-1.5 py-2 text-[9px] font-black uppercase tracking-wider text-center text-slate-500 bg-slate-50/80 border-r border-slate-100";
                th.innerText = String(k).replace(/_/g, ' ').toUpperCase();
                thCatatan.parentNode.insertBefore(th, thCatatan);
            });

            // Input Komponen Nilai: Compact w-12 h-7
            document.querySelectorAll('.row-peserta').forEach((row, rowIndex) => {
                const cellCatatan = row.querySelector('.cell-catatan');
                const isAktif = row.getAttribute('data-status') === 'AKTIF';

                komponenList.forEach(k => {
                    const cleanKey = String(k).toLowerCase().replace(/[^a-z0-9]/g, '_');
                    const td = document.createElement('td');
                    td.className = "td-dynamic-score w-16 px-1 py-1.5 text-center align-middle border-r border-slate-100";

                    td.innerHTML = `
                        <input type="number" min="0" max="100" step="1" 
                            name="nilai[${rowIndex}][scores][${cleanKey}]" 
                            data-komponen="${cleanKey}"
                            placeholder="${isAktif ? '0' : '-'}" 
                            ${isAktif ? '' : 'disabled'}
                            style="border-radius: 0.125rem;"
                            class="input-pintar input-score-dynamic w-12 h-7 mx-auto px-1 bg-white border border-slate-300 text-[10px] font-black text-center text-slate-800 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 transition-all shadow-2xs ${isAktif ? '' : 'bg-slate-100 text-slate-400 cursor-not-allowed'}">
                    `;
                    cellCatatan.parentNode.insertBefore(td, cellCatatan);
                });
            });
        }

        // 5. SUBMIT MATRIX VIA AJAX
        async function handleMatrixSubmit(e) {
            e.preventDefault();
            const form = document.getElementById('matrix-form');
            const btnSave = document.getElementById('btn-save-matrix');
            btnSave.disabled = true;
            btnSave.innerText = 'MENYIMPAN...';

            const formData = new FormData(form);

            try {
                const res = await fetch("{{ route('nilai.store') }}", {
                    method: 'POST',
                    body: formData,
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                const result = await res.json();

                if (result.success) {
                    const notif = document.getElementById('ajax-notification');
                    document.getElementById('ajax-notif-msg').innerText = result.message;
                    notif.classList.remove('hidden');
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                } else {
                    alert(result.message || 'Gagal menyimpan data.');
                }
            } catch (err) {
                alert('Terjadi kendala saat menyimpan.');
            } finally {
                btnSave.disabled = false;
                btnSave.innerHTML = `
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
                    SIMPAN DATA NILAI
                `;
            }
        }

        function resetMatrixTable() {
            const tableContainer = document.getElementById('matrix-table-container');
            tableContainer.classList.add('opacity-40', 'pointer-events-none');
            resetMatrixInputs();
            document.getElementById('form-pelatihan-id').value = '';
            document.getElementById('form-trainer-id').value = '';
        }

        function resetMatrixInputs() {
            document.querySelectorAll('.input-pintar').forEach(el => {
                el.disabled = true;
                el.value = '';
            });
        }

        function tutupWarningModal() {
            document.getElementById('matrix-warning-modal').classList.add('hidden');
        }

        function validasiAksiExcel(tipe) {
            const sektor = document.getElementById('filter-sektor').value;
            const pelatihan = document.getElementById('filter-pelatihan').value;

            if (!sektor || !pelatihan) {
                document.getElementById('modal-warning-message').innerText = 
                    "Peringatan: Silakan pilih Kategori Spektrum dan Program Pelatihan terlebih dahulu sebelum melakukan " + tipe + " data Excel!";
                document.getElementById('matrix-warning-modal').classList.remove('hidden');
            } else {
                if (tipe === 'import') {
                    document.getElementById('import-pelatihan-id').value = pelatihan;
                    document.getElementById('import-trainer-id').value = document.getElementById('filter-dosen').value;
                    document.getElementById('file-import').click();
                } else if (tipe === 'export') {
                    window.location.href = "{{ route('nilai.export') }}?spektrum_kategori=" + encodeURIComponent(sektor) + "&pelatihan_id=" + encodeURIComponent(pelatihan);
                }
            }
        }
    </script>
</x-app-layout>