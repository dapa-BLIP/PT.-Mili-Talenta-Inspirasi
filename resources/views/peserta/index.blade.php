<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between w-full gap-2 sm:gap-0 print:hidden select-none">
            <div class="flex items-center gap-2">
                <h2 class="text-xs font-black text-slate-800 uppercase tracking-[0.2em]">Database Peserta</h2>
                <span class="text-slate-300 text-xs">/</span>
                <span class="text-[10px] font-bold text-[#22a0f6] uppercase tracking-widest italic">Monitoring View</span>
            </div>
            
            <!-- Toolbar Tombol Aksi (Aman di Layar Kecil & Skala 125%) -->
            <div class="flex flex-wrap items-center gap-1.5 w-full sm:w-auto justify-start sm:justify-end">
                <button type="button" onclick="openImportPesertaModal()" class="px-3 py-1.5 bg-emerald-600 border border-emerald-700 text-white text-[9px] font-black hover:bg-emerald-700 transition uppercase tracking-wider rounded-xs shadow-xs flex items-center gap-1 cursor-pointer">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                    Import Excel
                </button>
                <a href="{{ route('peserta.export') }}" class="px-3 py-1.5 bg-slate-800 border border-slate-900 text-white text-[9px] font-black hover:bg-slate-900 transition uppercase tracking-wider rounded-xs shadow-xs flex items-center gap-1">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                    Export Excel
                </a>
                <button type="button" onclick="window.print()" class="px-3 py-1.5 bg-slate-600 text-white text-[9px] font-black hover:bg-slate-700 transition uppercase tracking-wider rounded-xs shadow-xs cursor-pointer">
                    Print Report
                </button>
                <button type="button" onclick="openCreatePesertaModal()" class="px-3.5 py-1.5 bg-[#22a0f6] text-white text-[9px] font-black uppercase tracking-wider hover:bg-blue-600 transition rounded-xs shadow-xs cursor-pointer">
                    + Add New Entry
                </button>
            </div>
        </div>
    </x-slot>

    <div class="space-y-3.5 sm:space-y-4 print:py-0 print:px-0">
        
        <!-- ALERT NOTIFICATION SYSTEM -->
        <div id="global-alert-container" class="relative z-10 select-none print:hidden">
            @if(session('success'))
                <div class="mb-3 px-3.5 sm:px-4 py-2 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xs shadow-2xs flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="text-[9px] font-black uppercase bg-emerald-600 text-white px-1.5 py-0.5 rounded-xs">OK</span>
                        <p class="text-[10px] font-bold tracking-wide uppercase">{{ session('success') }}</p>
                    </div>
                    <button type="button" class="text-emerald-600 hover:text-emerald-800 font-black text-sm cursor-pointer" onclick="this.parentElement.remove()">&times;</button>
                </div>
            @endif

            @if($errors->any())
                <div class="mb-3 px-3.5 sm:px-4 py-2 bg-rose-50 border border-rose-200 text-rose-800 rounded-xs shadow-2xs flex items-center justify-between">
                    <div class="space-y-0.5">
                        <span class="text-[9px] font-black uppercase bg-rose-600 text-white px-1.5 py-0.5 rounded-xs">ERR</span>
                        @foreach ($errors->all() as $err)
                            <p class="text-[10px] font-bold tracking-wide uppercase mt-0.5">* {{ $err }}</p>
                        @endforeach
                    </div>
                    <button type="button" class="text-rose-600 hover:text-rose-800 font-black text-sm cursor-pointer" onclick="this.parentElement.remove()">&times;</button>
                </div>
            @endif
        </div>

        <!-- 1. TOOLBAR PENCARIAN & FILTER -->
        <div class="bg-white border border-slate-200 p-3 sm:p-3.5 rounded-xs shadow-xs flex flex-col md:flex-row items-stretch md:items-center justify-between gap-2.5 sm:gap-3 print:hidden">
            <div class="flex items-center gap-2.5">
                <h3 class="text-[10.5px] sm:text-[11px] font-black text-slate-700 uppercase tracking-widest italic">DATABASE PESERTA</h3>
                <span class="text-[8.5px] sm:text-[9px] font-black text-[#22a0f6] bg-blue-50 border border-blue-100 px-2 py-0.5 uppercase tracking-wider font-mono rounded-xs">
                    TOTAL: {{ sprintf('%02d', $pesertas->count()) }} UNIT(S)
                </span>
            </div>

            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 w-full md:w-auto">
                <!-- Search Input -->
                <div class="relative w-full sm:w-56 lg:w-60">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-2.5 pointer-events-none text-slate-400">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </span>
                    <input type="text" id="liveSearchInput" placeholder="CARI NAMA / EMAIL..." 
                           class="w-full pl-8 pr-3 py-1.5 bg-slate-50 border border-slate-200 text-[10px] font-bold uppercase tracking-wider text-slate-700 rounded-xs focus:outline-none focus:border-[#22a0f6] focus:bg-white placeholder-slate-400 shadow-inner">
                </div>

                <!-- Dropdown Filter Perusahaan Mitra -->
                <div class="w-full sm:w-48 lg:w-52">
                    <select id="perusahaanDropdownFilter" onchange="applyFilters()" class="w-full px-2 py-1.5 bg-white border border-slate-200 text-[9.5px] sm:text-[10px] font-black uppercase tracking-wider text-slate-700 rounded-xs cursor-pointer focus:outline-none focus:border-slate-800 shadow-2xs">
                        <option value="ALL">SEMUA PERUSAHAAN</option>
                        @foreach ($perusahaans as $pt)
                            <option value="{{ strtoupper($pt->nama_perusahaan) }}">{{ $pt->nama_perusahaan }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Dropdown Filter Sektor / Program -->
                <div class="w-full sm:w-40 lg:w-44">
                    <select id="sektorDropdownFilter" onchange="applyFilters()" class="w-full px-2 py-1.5 bg-white border border-slate-200 text-[9.5px] sm:text-[10px] font-black uppercase tracking-wider text-slate-700 rounded-xs cursor-pointer focus:outline-none focus:border-slate-800 shadow-2xs">
                        <option value="ALL">SEMUA PELATIHAN</option>
                        <option value="TEKNIS">TEKNIS</option>
                        <option value="NON_TEKNIS">NON TEKNIS</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- 2. TABEL DATA MASTER PESERTA (DIBERI MIN-W-820PX AGAR TIDAK GEPENG) -->
        <div class="bg-white border border-slate-200 rounded-xs shadow-xs overflow-x-auto print:hidden">
            <table class="w-full min-w-[820px] text-left border-collapse select-none">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-slate-400">
                        <th class="w-16 px-4 py-3 text-[9.5px] font-black uppercase tracking-widest text-center">NO.</th>
                        <th class="px-4 py-3 text-[9.5px] font-black uppercase tracking-widest min-w-[240px]">NAMA LENGKAP & EMAIL</th>
                        <th class="px-4 py-3 text-[9.5px] font-black uppercase tracking-widest min-w-[180px]">PERUSAHAAN MITRA</th>
                        <th class="px-4 py-3 text-[9.5px] font-black uppercase tracking-widest text-center min-w-[150px]">PROGRAM KELAS</th>
                        <th class="px-4 py-3 text-[9.5px] font-black uppercase tracking-widest text-center w-28">STATUS</th>
                        <th class="px-4 py-3 text-[9.5px] font-black uppercase tracking-widest text-center w-20">AKSI</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($pesertas as $index => $p)
                    @php
                        $isNonTeknis = str_contains(strtolower($p->pelatihan), 'non') || str_contains(strtolower($p->pelatihan), 'soft') || str_contains(strtolower($p->pelatihan), 'umum');
                        $sektorType = $isNonTeknis ? 'NON_TEKNIS' : 'TEKNIS';
                        $badgeClass = $isNonTeknis ? 'border-slate-200 text-slate-600 bg-slate-50' : 'border-blue-200 text-[#22a0f6] bg-blue-50/50';
                        $ptName = $p->perusahaan->nama_perusahaan ?? $p->tempat_kerja ?? '-';
                        
                        $payloadPeserta = [
                            'id' => $p->id,
                            'nama_lengkap' => $p->nama_lengkap,
                            'gender' => $p->gender,
                            'ttl' => $p->ttl,
                            'email' => $p->email,
                            'no_hp' => $p->no_hp,
                            'perusahaan_id' => $p->perusahaan_id,
                            'tempat_kerja' => $ptName,
                            'pelatihan' => $p->pelatihan,
                            'status' => $p->status,
                            'foto' => $p->foto ? asset('storage/'.$p->foto) : ''
                        ];
                    @endphp
                    <tr data-sektor="{{ $sektorType }}" 
                        data-perusahaan="{{ strtoupper($ptName) }}"
                        data-peserta='@json($payloadPeserta)'
                        onclick="openDetailPesertaModalFromRow(this)" 
                        class="hover:bg-slate-50/80 cursor-pointer transition-colors row-peserta group">
                        
                        <!-- NO -->
                        <td class="px-4 py-2.5 text-[10.5px] font-black text-slate-400 text-center font-mono align-middle">
                            {{ str_pad($index + 1, 4, '0', STR_PAD_LEFT) }}
                        </td>
                        
                        <!-- NAMA & AVATAR -->
                        <td class="px-4 py-2.5 align-middle">
                            <div class="flex items-center gap-2.5">
                                <div class="w-7 h-7 shrink-0 bg-slate-100 border border-slate-200 rounded-xs flex items-center justify-center font-black text-[7px] overflow-hidden text-slate-400">
                                    @if($p->foto)
                                        <img src="{{ asset('storage/'.$p->foto) }}" class="object-cover w-full h-full">
                                    @else
                                        NO_IMG
                                    @endif
                                </div>
                                <div class="truncate min-w-0">
                                    <div class="flex items-center gap-1.5 leading-tight">
                                        <span class="text-[11px] font-black text-slate-800 uppercase tracking-tight truncate participant-name group-hover:text-[#22a0f6] transition-colors">{{ $p->nama_lengkap }}</span>
                                        @if(in_array(strtolower($p->gender), ['pria', 'l']))
                                            <span class="text-[7px] font-black bg-blue-50 text-blue-600 border border-blue-100 px-1 py-0.2 font-mono shrink-0 rounded-xs">L</span>
                                        @else
                                            <span class="text-[7px] font-black bg-pink-50 text-pink-600 border border-pink-100 px-1 py-0.2 font-mono shrink-0 rounded-xs">P</span>
                                        @endif
                                    </div>
                                    <div class="text-[8.5px] font-bold text-slate-400 italic truncate mt-0.5">{{ $p->email }}</div>
                                </div>
                            </div>
                        </td>

                        <!-- PERUSAHAAN MITRA -->
                        <td class="px-4 py-2.5 align-middle">
                            <span class="text-[10px] font-bold text-slate-700 uppercase tracking-tight block truncate participant-company">
                                {{ $ptName }}
                            </span>
                        </td>

                        <!-- PROGRAM KELAS -->
                        <td class="px-4 py-2.5 text-center align-middle">
                            <span class="inline-block px-2 py-0.5 border text-[8px] font-black uppercase tracking-tight truncate max-w-[160px] rounded-xs {{ $badgeClass }}">
                                {{ $p->pelatihan }}
                            </span>
                        </td>

                        <!-- STATUS TOGGLE -->
                        <td class="px-4 py-2.5 text-center align-middle" onclick="event.stopPropagation()">
                            <form action="{{ route('peserta.updateStatus', $p->id) }}" method="POST" class="flex justify-center">
                                @csrf
                                @method('PATCH')
                                <select name="status" onchange="this.form.submit()" class="text-[8px] font-black uppercase tracking-wider border-0 px-2 py-0.5 cursor-pointer appearance-none text-center transition-all shadow-2xs rounded-xs
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
                        <td class="px-4 py-2.5 text-center align-middle" onclick="event.stopPropagation()">
                            <div class="flex items-center justify-center gap-1">
                                <button type="button" onclick="openEditPesertaModalFromRow(this)" title="Edit Peserta" class="p-1 bg-white border border-slate-200 hover:bg-[#22a0f6] hover:text-white text-blue-500 transition-colors shadow-2xs rounded-xs cursor-pointer">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" stroke-width="2.5"></path></svg>
                                </button>
                                <button type="button" onclick="triggerDeleteConfirmation('{{ $p->id }}', '{{ addslashes($p->nama_lengkap) }}')" title="Hapus Peserta" class="p-1 bg-white border border-slate-200 hover:bg-rose-600 hover:text-white text-rose-500 transition-colors shadow-2xs rounded-xs cursor-pointer">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke-width="2.5"></path></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-8 text-center text-[10px] font-black text-slate-300 uppercase tracking-widest italic">
                            TIDAK ADA DATA PESERTA DITEMUKAN
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- TAMPILAN CETAK PDF -->
        <div class="hidden print:block text-black w-full layout-cetak">
            @php $groupedPesertas = $pesertas->groupBy(fn($item) => $item->perusahaan->nama_perusahaan ?? $item->tempat_kerja ?? 'UMUM'); @endphp
            @foreach ($groupedPesertas as $perusahaan =>$listPeserta)
                <div class="print-page {{ $loop->first ? '' : 'page-break-forced' }}">
                    <div class="border-b-2 border-slate-900 pb-1 mb-3">
                        <h1 class="text-base font-black uppercase tracking-tight">LAPORAN REKAPITULASI DATA PESERTA</h1>
                        <p class="text-[9px] font-bold text-slate-500 uppercase tracking-wider">Politeknik Mitra Industri • Tanggal Cetak: {{ date('d-m-Y H:i') }}</p>
                    </div>

                    <div class="mb-4">
                        <div class="bg-slate-100 px-2 py-1.5 border border-slate-400 mb-2">
                            <h2 class="text-[10px] font-black uppercase tracking-wider text-slate-800">PERUSAHAAN / MITRA: {{ $perusahaan }}</h2>
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
                                @foreach ($listPeserta as $subIndex =>$p)
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
        <div id="modalImportPeserta" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-[110] hidden flex items-center justify-center p-3 sm:p-4 print:hidden select-none">
            <div class="bg-white w-full max-w-[420px] shadow-2xl border border-slate-200 rounded-xs overflow-hidden flex flex-col my-auto">
                <div class="px-4 py-2.5 border-b border-emerald-100 bg-emerald-50 text-emerald-700 flex items-center justify-between shrink-0">
                    <div class="flex items-center gap-2">
                        <span class="text-[8.5px] font-black uppercase tracking-wider bg-emerald-600 text-white px-1.5 py-0.2 rounded-xs">Engine</span>
                        <h3 class="text-[10.5px] font-black uppercase tracking-wider text-emerald-850">BULK IMPORT SYSTEM</h3>
                    </div>
                    <button type="button" onclick="closeImportPesertaModal()" class="text-emerald-500 hover:text-emerald-700 font-bold text-lg leading-none cursor-pointer">&times;</button>
                </div>
                <form action="{{ route('peserta.import') }}" method="POST" enctype="multipart/form-data" class="p-4 space-y-3">
                    @csrf
                    <div class="bg-slate-50 p-2.5 border border-slate-200 rounded-xs space-y-0.5">
                        <h4 class="text-[9px] font-black text-slate-700 uppercase tracking-wide">Panduan Struktur File Excel:</h4>
                        <p class="text-[8px] font-bold text-slate-400 uppercase leading-relaxed">Gunakan file hasil "Export Excel" sebagai acuan template kolom.</p>
                    </div>
                    <div>
                        <label class="block text-[9px] font-black text-slate-600 uppercase tracking-wider mb-1">Pilih File Spreadsheet</label>
                        <input type="file" name="file_excel" accept=".xlsx, .xls, .csv" required class="w-full text-[10px] font-bold text-slate-500 bg-white p-1.5 border border-slate-300 rounded-xs file:mr-2.5 file:py-1 file:px-2.5 file:border-0 file:text-[8.5px] file:font-black file:uppercase file:bg-slate-800 file:text-white hover:file:bg-slate-700 cursor-pointer">
                    </div>
                    <div class="pt-1 flex gap-2">
                        <button type="button" onclick="closeImportPesertaModal()" class="w-1/2 border border-slate-200 text-slate-400 py-1.5 text-[9px] font-black uppercase tracking-wider hover:bg-slate-100 rounded-xs cursor-pointer">BATAL</button>
                        <button type="submit" class="w-1/2 bg-emerald-600 text-white py-1.5 text-[9px] font-black uppercase tracking-wider hover:bg-emerald-700 rounded-xs shadow-xs cursor-pointer border-none">IMPORT</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- MODAL DELETE CONFIRMATION -->
        <div id="modalDeleteConfirmation" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-[110] hidden flex items-center justify-center p-3 sm:p-4 print:hidden select-none">
            <div class="bg-white w-full max-w-[360px] shadow-2xl border border-rose-500 rounded-xs overflow-hidden flex flex-col my-auto">
                <div class="px-4 py-2.5 border-b border-rose-100 bg-rose-50 text-rose-700 flex items-center justify-between shrink-0">
                    <h3 class="text-[10px] font-black uppercase tracking-wider">HAPUS REKORD PESERTA</h3>
                    <button type="button" onclick="closeDeleteConfirmation()" class="text-rose-400 hover:text-rose-600 font-bold text-lg leading-none cursor-pointer">&times;</button>
                </div>
                <div class="p-4 text-center">
                    <p class="text-[10.5px] text-slate-500 font-bold uppercase tracking-wide">Yakin ingin menghapus data?</p>
                    <p id="delete_target_name" class="text-xs font-black text-rose-600 uppercase tracking-tight mt-1 break-words"></p>
                </div>
                <div class="p-3 bg-slate-50 border-t border-slate-100 flex gap-2">
                    <button type="button" onclick="closeDeleteConfirmation()" class="w-1/2 border border-slate-200 text-slate-400 py-1.5 text-[9px] font-black uppercase tracking-wider hover:bg-slate-100 rounded-xs cursor-pointer">BATAL</button>
                    <form id="formConfirmDelete" action="" method="POST" class="w-1/2">
                        @csrf 
                        @method('DELETE')
                        <button type="submit" class="w-full bg-rose-600 text-white py-1.5 text-[9px] font-black uppercase tracking-wider hover:bg-rose-700 rounded-xs shadow-xs cursor-pointer border-none">KONFIRMASI</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- SCRIPT ENGINE -->
    <script>
    document.getElementById('liveSearchInput').addEventListener('keyup', applyFilters);

    function applyFilters() {
        const query = document.getElementById('liveSearchInput').value.toUpperCase();
        const selectedSektor = document.getElementById('sektorDropdownFilter').value;
        const selectedPerusahaan = document.getElementById('perusahaanDropdownFilter').value;
        const rows = document.querySelectorAll('.row-peserta');

        rows.forEach(row => {
            const name = (row.querySelector('.participant-name')?.textContent || '').toUpperCase();
            const rowSektor = row.getAttribute('data-sektor');
            const rowPerusahaan = row.getAttribute('data-perusahaan');

            const matchSearch = name.indexOf(query) > -1;
            const matchSektor = (selectedSektor === 'ALL') || (rowSektor === selectedSektor);
            const matchPerusahaan = (selectedPerusahaan === 'ALL') || (rowPerusahaan === selectedPerusahaan);

            if (matchSearch && matchSektor && matchPerusahaan) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }

    const PESERTA_BASE_URL = "{{ url('peserta') }}";

    function openDetailPesertaModalFromRow(rowElement) {
        try {
            const rawData = rowElement.getAttribute('data-peserta');
            const data = JSON.parse(rawData);
            openDetailPesertaModal(data);
        } catch(e) {
            console.error("Gagal parse data peserta:", e);
        }
    }

    function openEditPesertaModalFromRow(buttonElement) {
        try {
            const row = buttonElement.closest('tr');
            const rawData = row.getAttribute('data-peserta');
            const data = JSON.parse(rawData);
            openEditPesertaModal(data);
        } catch(e) {
            console.error("Gagal parse data edit:", e);
        }
    }

    function openImportPesertaModal() { document.getElementById('modalImportPeserta').classList.remove('hidden'); }
    function closeImportPesertaModal() { document.getElementById('modalImportPeserta').classList.add('hidden'); }
    function openCreatePesertaModal() { document.getElementById('modalCreatePeserta').classList.remove('hidden'); document.getElementById('formCreatePeserta').reset(); }
    function closeCreatePesertaModal() { document.getElementById('modalCreatePeserta').classList.add('hidden'); }
    function closeEditPesertaModal() { document.getElementById('modalEditPeserta').classList.add('hidden'); }
    function closeDetailPesertaModal() { document.getElementById('modalDetailPeserta').classList.add('hidden'); }
    
    function triggerDeleteConfirmation(id, name) { 
        document.getElementById('formConfirmDelete').action = PESERTA_BASE_URL + '/' + id; 
        document.getElementById('delete_target_name').innerText = name; 
        document.getElementById('modalDeleteConfirmation').classList.remove('hidden'); 
    }
    function closeDeleteConfirmation() { document.getElementById('modalDeleteConfirmation').classList.add('hidden'); }

    function openEditPesertaModal(data) {
        const modal = document.getElementById('modalEditPeserta');
        if (modal) {
            document.getElementById('formEditPeserta').action = PESERTA_BASE_URL + '/' + data.id;
            if(document.getElementById('edit_nama_lengkap')) document.getElementById('edit_nama_lengkap').value = data.nama_lengkap;
            if(document.getElementById('edit_gender')) document.getElementById('edit_gender').value = data.gender;
            if(document.getElementById('edit_email')) document.getElementById('edit_email').value = data.email;
            
            let phoneClean = (data.no_hp || '').replace(/^\+62|^62|^0/, '');
            if(document.getElementById('edit_no_hp')) document.getElementById('edit_no_hp').value = phoneClean;
            
            if(document.getElementById('edit_perusahaan_id')) document.getElementById('edit_perusahaan_id').value = data.perusahaan_id || '';
            if(document.getElementById('edit_pelatihan')) document.getElementById('edit_pelatihan').value = data.pelatihan;

            if (data.ttl) {
                const parts = data.ttl.split(',');
                if (parts.length > 0 && document.getElementById('edit_tempat_lahir')) {
                    document.getElementById('edit_tempat_lahir').value = parts[0].trim();
                }
                if (parts.length > 1 && document.getElementById('edit_tanggal_lahir')) {
                    const tglRaw = parts[1].trim();
                    const dParts = tglRaw.split('-');
                    if (dParts.length === 3) {
                        document.getElementById('edit_tanggal_lahir').value = `${dParts[2]}-${dParts[1]}-${dParts[0]}`;
                    }
                }
            }

            const prevImg = document.getElementById('edit_preview_peserta_foto');
            const prevPlc = document.getElementById('edit_preview_peserta_placeholder');
            if (data.foto && data.foto.trim() !== '') {
                prevImg.src = data.foto;
                prevImg.classList.remove('hidden');
                prevPlc.classList.add('hidden');
            } else {
                prevImg.src = '';
                prevImg.classList.add('hidden');
                prevPlc.classList.remove('hidden');
            }

            modal.classList.remove('hidden');
        }
    }

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

            const imgEl = document.getElementById('detail_peserta_foto');
            const noImgEl = document.getElementById('detail_peserta_no_img');
            if (data.foto && data.foto.trim() !== '') {
                imgEl.src = data.foto;
                imgEl.classList.remove('hidden');
                noImgEl.classList.add('hidden');
            } else {
                imgEl.src = '';
                imgEl.classList.add('hidden');
                noImgEl.classList.remove('hidden');
            }

            const statusBadge = document.getElementById('detail_peserta_status');
            const st = (data.status || 'PENDING').toUpperCase();
            statusBadge.innerText = '● ' + st;

            if (st === 'AKTIF') {
                statusBadge.className = 'inline-block text-[8px] font-black uppercase px-2 py-0.5 tracking-wider bg-emerald-100 text-emerald-800 rounded-xs shadow-2xs';
            } else if (st === 'NON-AKTIF') {
                statusBadge.className = 'inline-block text-[8px] font-black uppercase px-2 py-0.5 tracking-wider bg-rose-100 text-rose-800 rounded-xs shadow-2xs';
            } else {
                statusBadge.className = 'inline-block text-[8px] font-black uppercase px-2 py-0.5 tracking-wider bg-amber-100 text-amber-800 rounded-xs shadow-2xs';
            }

            modal.classList.remove('hidden');
        }
    }

    function previewDanValidasiFoto(input, labelId, imgId = null, plcId = null) {
        const file = input.files[0];
        const maxUkuran = 2 * 1024 * 1024;
        const labelElem = document.getElementById(labelId);

        if (file) {
            if (file.size > maxUkuran) {
                alert('⚠️ UKURAN FOTO MELEBIHI 2 MB!\nSilakan gunakan foto dengan ukuran maksimal 2.00 MB.');
                input.value = '';
                labelElem.innerText = 'Pilih berkas foto...';
                return;
            }
            labelElem.innerText = file.name;

            if (imgId && plcId) {
                const imgElem = document.getElementById(imgId);
                const plcElem = document.getElementById(plcId);
                const reader = new FileReader();
                reader.onload = function(e) {
                    imgElem.src = e.target.result;
                    imgElem.classList.remove('hidden');
                    plcElem.classList.add('hidden');
                };
                reader.readAsDataURL(file);
            }
        }
    }
    </script>
</x-app-layout>