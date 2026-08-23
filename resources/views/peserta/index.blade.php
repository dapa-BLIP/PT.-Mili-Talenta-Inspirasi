<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between w-full print:hidden">
            <div class="flex items-center gap-2">
                <h2 class="text-xs font-black text-slate-800 uppercase tracking-[0.2em]">Database Peserta</h2>
                <span class="text-slate-300 text-xs">/</span>
                <span class="text-[10px] font-bold text-[#22a0f6] uppercase tracking-widest italic">Monitoring View</span>
            </div>
            <div class="flex items-center gap-1.5">
                <button type="button" onclick="openImportPesertaModal()" style="border-radius: 0.125rem;" class="px-3.5 py-1.5 bg-emerald-600 border border-emerald-700 text-white text-[9px] font-black hover:bg-emerald-700 transition uppercase tracking-widest shadow-xs flex items-center gap-1">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                    Import Excel
                </button>
                <a href="{{ route('peserta.export') }}" style="border-radius: 0.125rem;" class="px-3.5 py-1.5 bg-slate-800 border border-slate-900 text-white text-[9px] font-black hover:bg-slate-900 transition uppercase tracking-widest shadow-xs flex items-center gap-1">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                    Export Excel
                </a>
                <button onclick="window.print()" style="border-radius: 0.125rem;" class="px-3.5 py-1.5 bg-slate-600 text-white text-[9px] font-black hover:bg-slate-700 transition uppercase tracking-widest shadow-xs">
                    Print Report
                </button>
                <button type="button" onclick="openCreatePesertaModal()" style="border-radius: 0.125rem;" class="px-3.5 py-1.5 bg-[#22a0f6] text-white text-[9px] font-black uppercase tracking-widest hover:bg-blue-600 transition shadow-xs">
                    + Add New Entry
                </button>
            </div>
        </div>
    </x-slot>

    <div class="space-y-4 print:py-0 print:px-0">
        
        <!-- ALERT NOTIFICATION SYSTEM -->
        <div id="global-alert-container" class="relative z-10 select-none print:hidden">
            @if(session('success'))
                <div style="border-radius: 0.125rem;" class="mb-3 px-4 py-2.5 bg-emerald-50 border border-emerald-200 text-emerald-800 shadow-sm flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span style="border-radius: 0.125rem;" class="text-[10px] font-black uppercase bg-emerald-600 text-white px-1.5 py-0.5">OK</span>
                        <p class="text-[10px] font-bold tracking-wide uppercase">{{ session('success') }}</p>
                    </div>
                    <button type="button" class="text-emerald-600 hover:text-emerald-800 font-black text-sm" onclick="this.parentElement.remove()">&times;</button>
                </div>
            @endif

            @if($errors->has('import_error'))
                <div style="border-radius: 0.125rem;" class="mb-3 px-4 py-2.5 bg-rose-50 border border-rose-200 text-rose-800 shadow-sm flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span style="border-radius: 0.125rem;" class="text-[10px] font-black uppercase bg-rose-600 text-white px-1.5 py-0.5">ERR</span>
                        <p class="text-[10px] font-bold tracking-wide uppercase">{{ $errors->first('import_error') }}</p>
                    </div>
                    <button type="button" class="text-rose-600 hover:text-rose-800 font-black text-sm" onclick="this.parentElement.remove()">&times;</button>
                </div>
            @endif
        </div>

        <!-- 1. KOTAK ATAS: TOOLBAR PENCARIAN & FILTER (DENGAN SHADOW) -->
        <div style="border-radius: 0.125rem;" class="bg-white border border-slate-200 p-4 shadow-sm flex flex-wrap items-center justify-between gap-3 print:hidden">
            <div class="flex items-center gap-3">
                <h3 class="text-[11px] font-black text-slate-700 uppercase tracking-widest italic">DATA PESERTA PELATIHAN</h3>
                <span style="border-radius: 0.125rem;" class="text-[9px] font-black text-[#22a0f6] bg-blue-50 border border-blue-100 px-2 py-0.5 uppercase tracking-wider">
                    TOTAL: {{ sprintf('%02d', $pesertas->count()) }} UNIT(S)
                </span>
            </div>

            <div class="flex flex-col sm:flex-row items-center gap-2 w-full sm:w-auto">
                <!-- SEARCH INPUT (SESUAI CONTOH GAMBAR ICON KIRI & PLACEHOLDER ABU) -->
                <div class="relative w-full sm:w-64">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-slate-400">
                        <svg class="w-4 h-4 text-slate-400 font-bold" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </span>
                    <input type="text" id="liveSearchInput" placeholder="KETIK KATA KUNCI..." style="border-radius: 0.125rem;"
                       class="w-full pl-8 pr-3 py-1.5 bg-slate-50 border border-slate-200 text-[10px] font-bold uppercase tracking-wider text-slate-700 rounded-none focus:outline-none focus:border-[#22a0f6] focus:bg-white placeholder-slate-400 shadow-inner">
                </div>

                <!-- DROPDOWN FILTER SEKTOR (MODERN ELEGAN) -->
                <div class="w-full sm:w-52 relative">
                    <select id="sektorDropdownFilter" onchange="applyFilters()" style="border-radius: 0.125rem;" class="w-full px-3 py-2 bg-white border border-slate-300 text-[10px] font-black uppercase tracking-widest text-slate-700 cursor-pointer focus:ring-1 focus:ring-blue-500 shadow-2xs">
                        <option value="ALL">SEMUA PELATIHAN</option>
                        <option value="TEKNIS">TEKNIS</option>
                        <option value="NON_TEKNIS">NON TEKNIS</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- 2. KOTAK BAWAH: TABEL DATA LIST (DENGAN SHADOW) -->
        <div style="border-radius: 0.125rem;" class="bg-white border border-slate-200 shadow-sm overflow-hidden print:hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse table-fixed">
                    <thead>
                        <tr class="bg-white border-b border-slate-200 text-slate-400">
                            <th class="w-[8%] px-4 py-3 text-[9px] font-black uppercase tracking-widest text-center">NO.</th>
                            <th class="w-[36%] px-4 py-3 text-[9px] font-black uppercase tracking-widest">NAMA LENGKAP PESERTA</th>
                            <th class="w-[28%] px-4 py-3 text-[9px] font-black uppercase tracking-widest text-center">SPESIALIS</th>
                            <th class="w-[16%] px-4 py-3 text-[9px] font-black uppercase tracking-widest text-center">STATUS</th>
                            <th class="w-[12%] px-4 py-3 text-[9px] font-black uppercase tracking-widest text-center">AKSI</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($pesertas as $index => $p)
                        @php
                            $isNonTeknis = str_contains(strtolower($p->pelatihan), 'non') || str_contains(strtolower($p->pelatihan), 'soft') || str_contains(strtolower($p->pelatihan), 'umum');
                            $sektorType = $isNonTeknis ? 'NON_TEKNIS' : 'TEKNIS';
                            $labelSpesialis = $isNonTeknis ? 'PENGETAHUAN NON TEKNIS' : 'PENGETAHUAN TEKNIS';
                            $badgeClass = $isNonTeknis ? 'border-slate-300 text-slate-700 bg-white' : 'border-blue-300 text-[#22a0f6] bg-white';
                        @endphp
                        <tr data-sektor="{{ $sektorType }}" data-peserta="{{ json_encode([
                            'id' => $p->id,
                            'nama_lengkap' => $p->nama_lengkap,
                            'gender' => $p->gender,
                            'ttl' => $p->ttl,
                            'email' => $p->email,
                            'no_hp' => $p->no_hp,
                            'tempat_kerja' => $p->tempat_kerja,
                            'pelatihan' => $p->pelatihan,
                            'status' => $p->status,
                            'foto' => $p->foto ? asset('storage/'.$p->foto) : ''
                        ]) }}" onclick="openDetailPesertaModalFromRow(this)" class="hover:bg-slate-50/70 cursor-pointer transition-colors row-peserta">
                            
                            <!-- NO 4 DIGIT -->
                            <td style="padding-top: 0.875rem; padding-bottom: 0.875rem;" class="px-4 text-[10px] font-black text-slate-400 text-center font-mono align-middle">
                                {{ str_pad($index + 1, 4, '0', STR_PAD_LEFT) }}
                            </td>
                            
                            <!-- PARTICIPANT DETAIL -->
                            <td style="padding-top: 0.875rem; padding-bottom: 0.875rem;" class="px-4 align-middle">
                                <div class="flex items-center gap-2.5">
                                    <div style="border-radius: 0.125rem;" class="w-8 h-8 shrink-0 bg-slate-100 border border-slate-200 flex items-center justify-center font-black text-[7.5px] overflow-hidden text-slate-400">
                                        @if($p->foto)
                                            <img src="{{ asset('storage/'.$p->foto) }}" class="object-cover w-full h-full">
                                        @else
                                            NO_IMG
                                        @endif
                                    </div>
                                    <div class="truncate">
                                        <div class="flex items-center gap-1.5 leading-tight">
                                            <span class="text-[11.5px] font-black text-slate-800 uppercase tracking-tight truncate participant-name">{{ $p->nama_lengkap }}</span>
                                            @if(in_array(strtolower($p->gender), ['pria', 'l']))
                                                <span style="border-radius: 0.125rem;" class="text-[7px] font-black bg-blue-50 text-blue-600 border border-blue-100 px-1 py-0.2 font-mono shrink-0">L</span>
                                            @else
                                                <span style="border-radius: 0.125rem;" class="text-[7px] font-black bg-pink-50 text-pink-600 border border-pink-100 px-1 py-0.2 font-mono shrink-0">P</span>
                                            @endif
                                        </div>
                                        <div class="text-[9px] font-bold text-slate-400 italic truncate mt-0.5">{{ $p->email }}</div>
                                    </div>
                                </div>
                            </td>

                            <!-- SPESIALIS -->
                            <td style="padding-top: 0.875rem; padding-bottom: 0.875rem;" class="px-4 text-center align-middle">
                                <span style="border-radius: 0.125rem;" class="inline-block px-2.5 py-1 border text-[8.5px] font-black uppercase tracking-wider shadow-2xs {{ $badgeClass }}">
                                    {{ $labelSpesialis }}
                                </span>
                            </td>

                            <!-- STATUS (WARNA LEBIH HIDUP, BG LEBIH TEBAL, TANPA BORDER) -->
                            <td style="padding-top: 0.875rem; padding-bottom: 0.875rem;" class="px-4 text-center align-middle" onclick="event.stopPropagation()">
                                <form action="{{ route('peserta.updateStatus', $p->id) }}" method="POST" class="flex justify-center">
                                    @csrf
                                    @method('PATCH')
                                    <select name="status" onchange="this.form.submit()" style="border-radius: 0.125rem;" class="w-full max-w-[120px] text-[8.5px] font-black uppercase tracking-wider border-0 px-2.5 py-1 cursor-pointer appearance-none text-center transition-all shadow-xs
                                        {{ $p->status == 'AKTIF' ? 'bg-emerald-100 text-emerald-800 hover:bg-emerald-200' : '' }}
                                        {{ $p->status == 'NON-AKTIF' ? 'bg-rose-100 text-rose-800 hover:bg-rose-200' : '' }}
                                        {{ $p->status == 'PENDING' ? 'bg-amber-100 text-amber-800 hover:bg-amber-200' : '' }}">
                                        <option value="AKTIF" class="bg-white text-slate-800" {{ $p->status == 'AKTIF' ? 'selected' : '' }}>● AKTIF</option>
                                        <option value="PENDING" class="bg-white text-slate-800" {{ $p->status == 'PENDING' ? 'selected' : '' }}>○ PENDING</option>
                                        <option value="NON-AKTIF" class="bg-white text-slate-800" {{ $p->status == 'NON-AKTIF' ? 'selected' : '' }}>× NON-AKTIF</option>
                                    </select>
                                </form>
                            </td>

                            <!-- AKSI -->
                            <td style="padding-top: 0.875rem; padding-bottom: 0.875rem;" class="px-4 text-right align-middle" onclick="event.stopPropagation()">
                                <div class="flex items-center justify-end gap-1.5">
                                    <button type="button" onclick="openEditPesertaModalFromRow(this)" style="border-radius: 0.125rem;" class="p-1 bg-white border border-slate-200 hover:bg-[#22a0f6] hover:text-white text-blue-500 transition-colors shadow-2xs">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" stroke-width="2.5"></path></svg>
                                    </button>
                                    <button type="button" onclick="triggerDeleteConfirmation('{{ $p->id }}', '{{ addslashes($p->nama_lengkap) }}')" style="border-radius: 0.125rem;" class="p-1 bg-white border border-slate-200 hover:bg-rose-600 hover:text-white text-rose-500 transition-colors shadow-2xs">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke-width="2.5"></path></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" style="padding-top: 2rem; padding-bottom: 2rem;" class="text-center text-[10px] font-black text-slate-300 uppercase tracking-widest">
                                TIDAK ADA DATA PESERTA DITEMUKAN
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- TAMPILAN PRINT CETAK PDF -->
        <div class="hidden print:block text-black w-full layout-cetak">
            @php $groupedPesertas = $pesertas->groupBy('tempat_kerja'); @endphp
            @foreach($groupedPesertas as $perusahaan => $listPeserta)
                <div class="print-page {{ $loop->first ? '' : 'page-break-forced' }}">
                    <div class="border-b-2 border-slate-900 pb-1 mb-3">
                        <h1 class="text-base font-black uppercase tracking-tight">LAPORAN REKAPITULASI DATA PESERTA</h1>
                        <p class="text-[9px] font-bold text-slate-500 uppercase tracking-wider">Politeknik Mitra Industri • Tanggal Cetak: {{ date('d-m-Y H:i') }}</p>
                    </div>

                    <div class="mb-4">
                        <div class="bg-slate-100 px-2 py-1.5 border border-slate-400 mb-2">
                            <h2 class="text-[10px] font-black uppercase tracking-wider text-slate-800">PERUSAHAAN / MITRA INDUSTRI: {{ $perusahaan ?? 'NOT ASSIGNED' }}</h2>
                        </div>
                        <table class="w-full text-left border-collapse border border-slate-400 table-print">
                            <thead>
                                <tr class="bg-slate-50">
                                    <th class="border border-slate-400 px-2 py-1 text-[9px] font-black uppercase w-[6%] text-center">No.</th>
                                    <th class="border border-slate-400 px-2 py-1 text-[9px] font-black uppercase w-[44%]">Nama Lengkap</th>
                                    <th class="border border-slate-400 px-2 py-1 text-[9px] font-black uppercase w-[50%]">Alamat Email</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($listPeserta as $subIndex => $p)
                                    <tr class="row-print-target">
                                        <td class="border border-slate-300 px-2 text-[10px] font-bold text-center align-middle">{{ str_pad($subIndex + 1, 4, '0', STR_PAD_LEFT) }}</td>
                                        <td class="border border-slate-300 px-2 text-[10px] font-black uppercase align-middle">{{ $p->nama_lengkap }}</td>
                                        <td class="border border-slate-300 px-2 text-[10px] font-medium text-slate-700 align-middle">{{ $p->email }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div> 
            @endforeach
        </div>

        @include('peserta.create')
        @include('peserta.edit')
        @include('peserta.detail')

        <!-- MODAL IMPORT PESERTA -->
        <div id="modalImportPeserta" class="fixed inset-0 bg-slate-900/60 z-[110] hidden flex items-center justify-center p-4 print:hidden">
            <div style="border-radius: 0.125rem;" class="bg-white w-full max-w-md shadow-2xl border border-slate-200 overflow-hidden">
                <div class="px-5 py-3 border-b border-emerald-100 bg-emerald-50 text-emerald-700 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span style="border-radius: 0.125rem;" class="text-[9px] font-black uppercase tracking-widest bg-emerald-600 text-white px-1.5 py-0.5">Engine</span>
                        <h3 class="text-[10px] font-black uppercase tracking-widest text-emerald-800">BULK IMPORT SYSTEM</h3>
                    </div>
                    <button type="button" onclick="closeImportPesertaModal()" class="text-emerald-500 hover:text-emerald-700 font-bold text-sm">&times;</button>
                </div>
                <form action="{{ route('peserta.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="p-5 space-y-4">
                        <div style="border-radius: 0.125rem;" class="bg-slate-50 p-3 border border-slate-200">
                            <h4 class="text-[9px] font-black text-slate-700 uppercase tracking-wide mb-1">Panduan Struktur File Excel:</h4>
                            <p class="text-[8px] font-bold text-slate-400 uppercase leading-relaxed">Gunakan file hasil "Export Excel" sebagai acuan template kolom.</p>
                        </div>
                        <div>
                            <label class="block text-[9px] font-black text-slate-600 uppercase tracking-widest mb-1.5">Pilih File Spreadsheet</label>
                            <input type="file" name="file_excel" accept=".xlsx, .xls, .csv" required style="border-radius: 0.125rem;" class="w-full text-[10px] font-bold text-slate-500 bg-white p-1.5 border border-slate-300 file:mr-3 file:py-1 file:px-3 file:border-0 file:text-[9px] file:font-black file:uppercase file:tracking-widest file:bg-slate-800 file:text-white hover:file:bg-slate-700 cursor-pointer">
                        </div>
                    </div>
                    <div class="p-4 bg-slate-50 border-t border-slate-100 flex gap-2">
                        <button type="button" onclick="closeImportPesertaModal()" style="border-radius: 0.125rem;" class="w-1/2 border border-slate-200 text-slate-400 py-2 text-[9px] font-black uppercase tracking-widest hover:bg-slate-100">BATAL</button>
                        <button type="submit" style="border-radius: 0.125rem;" class="w-1/2 bg-emerald-600 text-white py-2 text-[9px] font-black uppercase tracking-widest hover:bg-emerald-700 shadow-xs">IMPORT</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- MODAL DELETE CONFIRMATION -->
        <div id="modalDeleteConfirmation" class="fixed inset-0 bg-slate-900/60 z-[110] hidden flex items-center justify-center p-4 print:hidden">
            <div style="border-radius: 0.125rem;" class="bg-white w-full max-w-sm shadow-2xl border border-rose-500 overflow-hidden">
                <div class="px-5 py-3 border-b border-rose-100 bg-rose-50 text-rose-700 flex items-center justify-between">
                    <h3 class="text-[10px] font-black uppercase tracking-widest">HAPUS REKORD</h3>
                    <button type="button" onclick="closeDeleteConfirmation()" class="text-rose-400 hover:text-rose-600 font-bold text-sm">&times;</button>
                </div>
                <div class="p-5 text-center">
                    <p class="text-[11px] text-slate-500 font-bold uppercase tracking-wide">Yakin ingin menghapus data?</p>
                    <p id="delete_target_name" class="text-[13px] font-black text-rose-600 uppercase tracking-tight mt-1"></p>
                </div>
                <div class="p-4 bg-slate-50 border-t border-slate-100 flex gap-2">
                    <button type="button" onclick="closeDeleteConfirmation()" style="border-radius: 0.125rem;" class="w-1/2 border border-slate-200 text-slate-400 py-2 text-[9px] font-black uppercase tracking-widest hover:bg-slate-100">BATAL</button>
                    <form id="formConfirmDelete" action="" method="POST" class="w-1/2">
                        @csrf 
                        @method('DELETE')
                        <button type="submit" style="border-radius: 0.125rem;" class="w-full bg-rose-600 text-white py-2 text-[9px] font-black uppercase tracking-widest hover:bg-rose-700 shadow-xs">KONFIRMASI</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
    // 1. ENGINE FILTER PENCARIAN & DROPDOWN SEKTOR
    document.getElementById('liveSearchInput').addEventListener('keyup', applyFilters);

    function applyFilters() {
        const query = document.getElementById('liveSearchInput').value.toUpperCase();
        const selectedSektor = document.getElementById('sektorDropdownFilter').value;
        const rows = document.querySelectorAll('.row-peserta');

        rows.forEach(row => {
            const name = (row.querySelector('.participant-name')?.textContent || '').toUpperCase();
            const rowSektor = row.getAttribute('data-sektor');

            const matchSearch = name.indexOf(query) > -1;
            const matchSektor = (selectedSektor === 'ALL') || (rowSektor === selectedSektor);

            if (matchSearch && matchSektor) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }

    // 2. BASE URL ROUTE PESERTA
    const PESERTA_BASE_URL = "{{ url('peserta') }}";

    // 3. HANDLER TRIGGER MODAL DARI BARIS TABEL
    function openDetailPesertaModalFromRow(rowElement) {
        const data = JSON.parse(rowElement.getAttribute('data-peserta'));
        openDetailPesertaModal(data);
    }

    function openEditPesertaModalFromRow(buttonElement) {
        const row = buttonElement.closest('tr');
        const data = JSON.parse(row.getAttribute('data-peserta'));
        openEditPesertaModal(data);
    }

    // 4. KONTROL BUKA & TUTUP MODAL
    function openImportPesertaModal() { document.getElementById('modalImportPeserta').classList.remove('hidden'); }
    function closeImportPesertaModal() { document.getElementById('modalImportPeserta').classList.add('hidden'); }
    function openCreatePesertaModal() { document.getElementById('modalCreatePeserta').classList.remove('hidden'); document.getElementById('formCreatePeserta').reset(); }
    function closeCreatePesertaModal() { document.getElementById('modalCreatePeserta').classList.add('hidden'); }
    function closeEditPesertaModal() { document.getElementById('modalEditPeserta').classList.add('hidden'); }
    function closeDetailPesertaModal() { document.getElementById('modalDetailPeserta').classList.add('hidden'); }
    
    // 5. MODAL KONFIRMASI HAPUS DATA
    function triggerDeleteConfirmation(id, name) { 
        document.getElementById('formConfirmDelete').action = PESERTA_BASE_URL + '/' + id; 
        document.getElementById('delete_target_name').innerText = name; 
        document.getElementById('modalDeleteConfirmation').classList.remove('hidden'); 
    }
    function closeDeleteConfirmation() { document.getElementById('modalDeleteConfirmation').classList.add('hidden'); }

    // 6. POPULATE DATA EDIT PESERTA
    function openEditPesertaModal(data) {
        const modal = document.getElementById('modalEditPeserta');
        if (modal) {
            document.getElementById('formEditPeserta').action = PESERTA_BASE_URL + '/' + data.id;
            if(document.getElementById('edit_nama_lengkap')) document.getElementById('edit_nama_lengkap').value = data.nama_lengkap;
            if(document.getElementById('edit_email')) document.getElementById('edit_email').value = data.email;
            if(document.getElementById('edit_no_hp')) document.getElementById('edit_no_hp').value = data.no_hp || '';
            if(document.getElementById('edit_tempat_kerja')) document.getElementById('edit_tempat_kerja').value = data.tempat_kerja || '';
            if(document.getElementById('edit_pelatihan')) document.getElementById('edit_pelatihan').value = data.pelatihan;
            modal.classList.remove('hidden');
        }
    }

    // 7. POPULATE DETAIL PESERTA (LAYOUT VERTIKAL + BADGE WARNA DINAMIS)
    function openDetailPesertaModal(data) {
        const modal = document.getElementById('modalDetailPeserta');
        if (modal) {
            document.getElementById('detail_peserta_nama').innerText = data.nama_lengkap;
            document.getElementById('detail_peserta_sector').innerText = data.pelatihan;
            document.getElementById('detail_peserta_email').innerText = data.email;
            document.getElementById('detail_peserta_no_hp').innerText = data.no_hp || '-';
            document.getElementById('detail_peserta_ttl').innerText = data.ttl || '-';
            document.getElementById('detail_peserta_kerja').innerText = data.tempat_kerja || '-';
            
            if(document.getElementById('detail_peserta_gender')) {
                document.getElementById('detail_peserta_gender').innerText = (data.gender || '-').toUpperCase();
            }

            // Tampilan Foto Avatar
            const imgEl = document.getElementById('detail_peserta_foto');
            const noImgEl = document.getElementById('detail_peserta_no_img');
            if (data.foto && data.foto.trim() !== '') {
                imgEl.src = data.foto;
                imgEl.classList.remove('hidden');
                noImgEl.classList.add('hidden');
            } else {
                imgEl.classList.add('hidden');
                noImgEl.classList.remove('hidden');
            }

            // Pewarnaan Badge Status Dinamis di Header Modal
            const statusBadge = document.getElementById('detail_peserta_status');
            const st = (data.status || 'PENDING').toUpperCase();
            statusBadge.innerText = '● ' + st;

            if (st === 'AKTIF') {
                statusBadge.className = 'inline-block text-[8px] font-black uppercase px-2.5 py-0.5 tracking-wider bg-emerald-100 text-emerald-800 shadow-2xs';
            } else if (st === 'NON-AKTIF') {
                statusBadge.className = 'inline-block text-[8px] font-black uppercase px-2.5 py-0.5 tracking-wider bg-rose-100 text-rose-800 shadow-2xs';
            } else {
                statusBadge.className = 'inline-block text-[8px] font-black uppercase px-2.5 py-0.5 tracking-wider bg-amber-100 text-amber-800 shadow-2xs';
            }

            modal.classList.remove('hidden');
        }
    }
</script>
</x-app-layout>