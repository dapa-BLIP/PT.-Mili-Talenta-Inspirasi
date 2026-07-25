<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between w-full print:hidden">
            <div class="flex items-center gap-2">
                <h2 class="text-sm font-black text-slate-700 uppercase tracking-tighter">Database Peserta</h2>
                <span class="text-slate-300 text-xs">/</span>
                <span class="text-[10px] font-bold text-[#22a0f6] uppercase tracking-widest italic">Monitoring View</span>
            </div>
            <div class="flex gap-2">
                <button type="button" onclick="openImportPesertaModal()" class="px-4 py-1.5 bg-emerald-600 border border-emerald-700 text-white rounded-sm text-[10px] font-black hover:bg-emerald-700 transition uppercase tracking-widest shadow-sm flex items-center justify-center gap-1">
                    Import Excel
                </button>
                <a href="{{ route('peserta.export') }}" class="px-4 py-1.5 bg-slate-700 border border-slate-800 text-white rounded-sm text-[10px] font-black hover:bg-slate-800 transition uppercase tracking-widest shadow-sm flex items-center justify-center">
                    Export Excel
                </a>
                <button onclick="window.print()" class="px-4 py-1.5 bg-slate-500 border border-slate-600 rounded-sm text-[10px] font-black text-white hover:bg-slate-600 transition uppercase tracking-widest shadow-sm">
                    Print Report
                </button>
                <button type="button" onclick="openCreatePesertaModal()" class="px-4 py-1.5 bg-[#22a0f6] text-white rounded-sm text-[10px] font-black uppercase tracking-widest hover:bg-blue-600 transition shadow-md">
                    + Add New Entry
                </button>
            </div>
        </div>
    </x-slot>

    <div class="py-4 max-w-[1400px] mx-auto px-1 print:py-0 print:px-0">
        
        <!-- ========================================================================= -->
        <!-- WADAH MONITORING NOTIFIKASI ALERT SYSTEM (SUCCESS & ERROR)               -->
        <!-- ========================================================================= -->
        <div id="global-alert-container" class="relative z-10 select-none print:hidden">
            <!-- 1. Notifikasi Sukses -->
            @if(session('success'))
                <div class="mb-4 px-4 py-3 bg-white border-l-4 border-l-emerald-500 border-y border-r border-slate-200 shadow-md flex items-center justify-between transition-all rounded-r-sm">
                    <div class="flex items-center gap-3">
                        <div class="p-1 bg-emerald-50 rounded-full text-emerald-600 shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="3" stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg>
                        </div>
                        <div class="space-y-0.5">
                            <h4 class="text-[10px] font-black text-emerald-800 uppercase tracking-widest">Sistem Berhasil</h4>
                            <p class="text-[11px] font-bold text-slate-600 tracking-wide uppercase">{{ session('success') }}</p>
                        </div>
                    </div>
                    <button type="button" class="text-slate-400 hover:text-slate-600 font-black text-lg p-1 leading-none focus:outline-none" onclick="this.parentElement.remove()">&times;</button>
                </div>
            @endif

            <!-- 2. Notifikasi Error Format Excel -->
            @if($errors->has('import_error'))
                <div class="mb-4 px-4 py-3 bg-white border-l-4 border-l-rose-500 border-y border-r border-slate-200 shadow-md flex items-center justify-between transition-all rounded-r-sm">
                    <div class="flex items-center gap-3">
                        <div class="p-1 bg-rose-50 rounded-full text-rose-600 shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="3" stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        </div>
                        <div class="space-y-0.5">
                            <h4 class="text-[10px] font-black text-rose-800 uppercase tracking-widest">Gagal Import Spreadsheet</h4>
                            <p class="text-[11px] font-bold text-slate-600 tracking-wide uppercase">* {{ $errors->first('import_error') }}</p>
                        </div>
                    </div>
                    <button type="button" class="text-slate-400 hover:text-slate-600 font-black text-lg p-1 leading-none focus:outline-none" onclick="this.parentElement.remove()">&times;</button>
                </div>
            @endif
        </div>

        <!-- MAIN TABLE CONTAINER -->
        <div class="bg-white border border-slate-200 shadow-sm rounded-none print:hidden">
            <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50 flex flex-col lg:flex-row items-start lg:items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <h3 class="text-[11px] font-black text-slate-500 uppercase tracking-widest italic">Detail Information Record</h3>
                    <div class="h-4 w-[1px] bg-slate-200"></div>
                    <span class="text-[10px] font-black text-[#22a0f6] bg-blue-50 px-2 py-0.5 rounded-full uppercase tracking-tighter italic">Total: {{ $pesertas->count() }} UNIT(S)</span>
                </div>
                <div class="flex flex-col sm:flex-row items-center gap-2 w-full lg:w-auto">
                    <div class="relative w-full sm:w-60">
                        <input type="text" id="liveSearchInput" placeholder="CARI NAMA PESERTA..." class="w-full pl-8 pr-4 py-1.5 bg-white border border-slate-300 text-[10px] font-black uppercase tracking-widest focus:ring-1 focus:ring-[#22a0f6] focus:border-[#22a0f6] rounded-none placeholder-slate-300 shadow-sm transition-all">
                    </div>
                    <form action="{{ route('peserta.index') }}" method="GET" class="w-full sm:w-56">
                        <select name="pelatihan" onchange="this.form.submit()" class="w-full px-3 py-1.5 bg-white border border-slate-300 text-[10px] font-black uppercase tracking-widest rounded-none cursor-pointer focus:ring-1 focus:ring-[#22a0f6]">
                            <option value="">-- SEMUA BATCH / ANGKATAN --</option>
                            @foreach($pesertas->pluck('pelatihan')->unique()->filter() as $angkatanItem)
                                <option value="{{ $angkatanItem }}" {{ request('pelatihan') == $angkatanItem ? 'selected' : '' }}>
                                    {{ strtoupper($angkatanItem) }}
                                </option>
                            @endforeach
                        </select>
                    </form>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse table-fixed">
                    <thead>
                        <tr class="bg-white border-b border-slate-200">
                            <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase w-20 text-center">No.</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase">Participant Detail</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase w-48">Company / Industry</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase w-48 text-center">Verification</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase text-right w-36">Operations</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($pesertas as $index => $p)
                        <tr data-peserta="{{ json_encode([
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
                        ]) }}" onclick="openDetailPesertaModalFromRow(this)" class="hover:bg-slate-50 cursor-pointer transition-colors duration-150">
                            
                            <td class="px-6 py-4 text-[11px] font-black text-slate-400 text-center">
                                {{ str_pad($index + 1, 8, '0', STR_PAD_LEFT) }}
                            </td>
                            
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-12 shrink-0 rounded-none bg-slate-100 border border-slate-200 flex items-center justify-center font-black text-[9px] overflow-hidden">
                                        @if($p->foto)
                                            <img src="{{ asset('storage/'.$p->foto) }}" class="object-cover w-full h-full">
                                        @else
                                            <span class="text-slate-400 font-bold">NO_IMG</span>
                                        @endif
                                    </div>
                                    <div class="space-y-0.5 truncate">
                                        <!-- 🚀 NAMA PESERTA + BADGE GENDER (L / P) -->
                                        <div class="flex items-center gap-2">
                                            <p class="text-[13px] font-black text-slate-700 uppercase truncate">{{ $p->nama_lengkap }}</p>
                                            
                                            @if(in_array(strtolower($p->gender), ['pria', 'l']))
                                                <span class="text-[7.5px] font-black bg-blue-50 text-[#22a0f6] border border-blue-200 px-1.5 py-0.5 rounded-none uppercase font-mono shrink-0">
                                                    L
                                                </span>
                                            @elseif(in_array(strtolower($p->gender), ['wanita', 'p']))
                                                <span class="text-[7.5px] font-black bg-pink-50 text-pink-600 border border-pink-200 px-1.5 py-0.5 rounded-none uppercase font-mono shrink-0">
                                                    P
                                                </span>
                                            @endif
                                        </div>
                                        <p class="text-[10px] font-bold text-slate-400 italic truncate">{{ $p->pelatihan }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-[11px] font-black text-slate-600 uppercase bg-slate-100 px-2 py-1 border border-slate-200 rounded-none shadow-sm">
                                    {{ $p->tempat_kerja ?? 'NOT ASSIGNED' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center" onclick="event.stopPropagation()">
                                <form action="{{ route('peserta.updateStatus', $p->id) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <select name="status" onchange="this.form.submit()" class="w-full text-[9px] font-black uppercase tracking-tighter rounded-none border-2 px-2 py-1.5 cursor-pointer appearance-none text-center transition-all
                                        {{ $p->status == 'AKTIF' ? 'bg-emerald-50 border-emerald-100 text-emerald-600' : '' }}
                                        {{ $p->status == 'NON-AKTIF' ? 'bg-rose-50 border-rose-100 text-rose-600' : '' }}
                                        {{ $p->status == 'PENDING' ? 'bg-amber-50 border-amber-100 text-amber-600' : '' }}">
                                        <option value="AKTIF" {{ $p->status == 'AKTIF' ? 'selected' : '' }}>● AKTIF</option>
                                        <option value="PENDING" {{ $p->status == 'PENDING' ? 'selected' : '' }}>○ PENDING</option>
                                        <option value="NON-AKTIF" {{ $p->status == 'NON-AKTIF' ? 'selected' : '' }}>× NON-AKTIF</option>
                                    </select>
                                </form>
                            </td>
                            <td class="px-6 py-4 text-right" onclick="event.stopPropagation()">
                                <div class="flex justify-end gap-1.5">
                                    <button type="button" onclick="openEditPesertaModalFromRow(this)" class="p-2 bg-white border border-slate-200 hover:bg-[#22a0f6] hover:text-white rounded-none text-slate-400 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" stroke-width="2.5"></path></svg>
                                    </button>
                                    <button type="button" onclick="triggerDeleteConfirmation('{{ $p->id }}', '{{ addslashes($p->nama_lengkap) }}')" class="p-2 bg-white border border-slate-200 hover:bg-rose-500 hover:text-white rounded-none text-rose-400 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke-width="2.5"></path></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- TAMPILAN PRINT CETAK PDF Halaman A4 -->
        <div class="hidden print:block text-black w-full layout-cetak">
            @php 
                $groupedPesertas = $pesertas->groupBy('tempat_kerja'); 
            @endphp

            @if($groupedPesertas->isEmpty())
                <div class="border-b-2 border-slate-900 pb-1 mb-4 relative">
                    <h1 class="text-base font-black uppercase tracking-tight">LAPORAN REKAPITULASI DATA PESERTA</h1>
                    <p class="text-[9px] font-bold text-slate-500 uppercase tracking-wider">Politeknik Mitra Industri • Tanggal Cetak: {{ date('d-m-Y H:i') }}</p>
                </div>
                <p class="text-center text-xs font-bold uppercase tracking-widest py-8 border border-dashed border-slate-400">TIDAK ADA DATA ENTRI</p>
            @else
                @foreach($groupedPesertas as $perusahaan => $listPeserta)
                    @php 
                        $totalData = $listPeserta->count();
                        $totalHalamanSektor = ceil($totalData / 20) ?: 1;
                        $sektorLineCounter = 0; 
                        $currentPageNum = 1;
                    @endphp

                    <div class="print-page {{ $loop->first ? '' : 'page-break-forced' }}">
                        <div class="border-b-2 border-slate-900 pb-1 mb-3 header-laporan relative">
                            <h1 class="text-base font-black uppercase tracking-tight">LAPORAN REKAPITULASI DATA PESERTA</h1>
                            <p class="text-[9px] font-bold text-slate-500 uppercase tracking-wider">Politeknik Mitra Industri • Tanggal Cetak: {{ date('d-m-Y H:i') }}</p>
                            <span class="absolute top-0 right-0 text-[10px] font-black bg-slate-100 border border-slate-300 px-1.5 py-0.5 rounded-none text-slate-700">PAGE {{ $currentPageNum }}/{{ $totalHalamanSektor }}</span>
                        </div>

                        <div class="mb-4 sektor-block">
                            <div class="bg-slate-100 px-2 py-1.5 border border-slate-400 mb-2 flex items-center justify-between label-sektor">
                                <h2 class="text-[10px] font-black uppercase tracking-wider text-slate-800">PERUSAHAAN / MITRA INDUSTRI: {{ $perusahaan ?? 'NOT ASSIGNED' }}</h2>
                            </div>

                            <table class="w-full text-left border-collapse border border-slate-400 table-print">
                                <thead>
                                    <tr class="bg-slate-50">
                                        <th class="border border-slate-400 px-2 py-1 text-[9px] font-black uppercase w-[5%] text-center">No.</th>
                                        <th class="border border-slate-400 px-2 py-1 text-[9px] font-black uppercase w-[30%]">Nama Lengkap</th>
                                        <th class="border border-slate-400 px-2 py-1 text-[9px] font-black uppercase w-[30%]">Alamat Email</th>
                                        <th class="border border-slate-400 px-2 py-1 text-[9px] font-black uppercase w-[15%]">No. HP</th>
                                        <th class="border border-slate-400 px-2 py-1 text-[9px] font-black uppercase w-[20%]">Batch Angkatan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($listPeserta as $subIndex => $p)
                                        @php $sektorLineCounter++; @endphp
                                        
                                        @if($sektorLineCounter > 1 && ($sektorLineCounter - 1) % 20 == 0)
                                            @php $currentPageNum++; @endphp
                                            </tbody>
                                            </table>
                                            </div> 
                                            </div> 

                                            <div class="print-page page-break-forced">
                                                <div class="border-b-2 border-slate-900 pb-1 mb-3 header-laporan relative">
                                                    <h1 class="text-base font-black uppercase tracking-tight">LAPORAN REKAPITULASI DATA PESERTA</h1>
                                                    <p class="text-[9px] font-bold text-slate-500 uppercase tracking-wider">Politeknik Mitra Industri • Rekaman Log Sistem</p>
                                                    <span class="absolute top-0 right-0 text-[10px] font-black bg-slate-100 border border-slate-300 px-1.5 py-0.5 rounded-none text-slate-700">PAGE {{ $currentPageNum }}/{{ $totalHalamanSektor }}</span>
                                                </div>
                                                
                                                <div class="mb-4 sektor-block">
                                                    <div class="bg-slate-100 px-2 py-1.5 border border-slate-400 mb-2 flex items-center justify-between label-sektor">
                                                        <div class="flex items-center gap-2">
                                                            <h2 class="text-[10px] font-black uppercase tracking-wider text-slate-800">PERUSAHAAN / MITRA INDUSTRI: {{ $perusahaan ?? 'NOT ASSIGNED' }}</h2>
                                                            <span class="text-[8px] font-black bg-slate-700 text-white px-1.5 py-0.5 uppercase tracking-widest rounded-none">LANJUTAN</span>
                                                        </div>
                                                    </div>
                                                    <table class="w-full text-left border-collapse border border-slate-400 table-print">
                                                        <thead>
                                                            <tr class="bg-slate-50">
                                                                <th class="border border-slate-400 px-2 py-1 text-[9px] font-black uppercase w-[5%] text-center">No.</th>
                                                                <th class="border border-slate-400 px-2 py-1 text-[9px] font-black uppercase w-[30%]">Nama Lengkap</th>
                                                                <th class="border border-slate-400 px-2 py-1 text-[9px] font-black uppercase w-[30%]">Alamat Email</th>
                                                                <th class="border border-slate-400 px-2 py-1 text-[9px] font-black uppercase w-[15%]">No. HP</th>
                                                                <th class="border border-slate-400 px-2 py-1 text-[9px] font-black uppercase w-[20%]">Batch Angkatan</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                        @endif

                                        <tr class="row-print-target">
                                            <td class="border border-slate-300 px-2 text-[10px] font-bold text-center align-middle">
                                                {{ $subIndex + 1 }}
                                            </td>
                                            <td class="border border-slate-300 px-2 text-[10px] font-black uppercase align-middle truncate-print">
                                                {{ $p->nama_lengkap }}
                                            </td>
                                            <td class="border border-slate-300 px-2 text-[10px] font-medium text-slate-700 align-middle truncate-print">
                                                {{ $p->email }}
                                            </td>
                                            <td class="border border-slate-300 px-2 text-[10px] font-medium text-slate-700 align-middle text-center">
                                                {{ $p->no_hp ?? '-' }}
                                            </td>
                                            <td class="border border-slate-300 px-2 text-[10px] font-black uppercase align-middle truncate-print">
                                                {{ $p->pelatihan }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div> 
                @endforeach
            @endif
        </div>

        @include('peserta.create')
        @include('peserta.edit')
        @include('peserta.detail')

        <!-- 🔴 MODAL IMPORT PESERTA (SERAGAM BULK ENGINE SYSTEM) -->
        <div id="modalImportPeserta" class="fixed inset-0 bg-slate-900/60 z-[110] hidden flex items-center justify-center p-4 print:hidden">
            <div class="bg-white w-full max-w-md shadow-2xl border border-slate-200 overflow-hidden rounded-none">
                <div class="px-5 py-3 border-b border-emerald-100 bg-emerald-50 text-emerald-700 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="text-[9px] font-black uppercase tracking-widest bg-emerald-600 text-white px-1.5 py-0.5 rounded-sm">Engine</span>
                        <h3 class="text-[10px] font-black uppercase tracking-widest text-emerald-800">BULK IMPORT SYSTEM</h3>
                    </div>
                    <button type="button" onclick="closeImportPesertaModal()" class="text-emerald-500 hover:text-emerald-700 font-bold text-sm">&times;</button>
                </div>
                <form action="{{ route('peserta.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="p-5 space-y-4">
                        <div class="bg-slate-50 p-3 border border-slate-200">
                            <h4 class="text-[9px] font-black text-slate-700 uppercase tracking-wide mb-1">Panduan Struktur File Excel:</h4>
                            <p class="text-[8px] font-bold text-slate-400 uppercase leading-relaxed">Gunakan langsung file hasil "Export Excel" sebagai template.</p>
                        </div>
                        <div>
                            <label class="block text-[9px] font-black text-slate-600 uppercase tracking-widest mb-1.5">Pilih File Spreadsheet</label>
                            <input type="file" name="file_excel" accept=".xlsx, .xls, .csv" required class="w-full text-[10px] font-bold text-slate-500 bg-white p-1.5 border border-slate-300 rounded-none file:mr-3 file:py-1 file:px-3 file:border-0 file:text-[9px] file:font-black file:uppercase file:tracking-widest file:bg-slate-800 file:text-white hover:file:bg-slate-700 cursor-pointer">
                        </div>
                    </div>
                    <div class="p-4 bg-slate-50 border-t border-slate-100 flex gap-2">
                        <button type="button" onclick="closeImportPesertaModal()" class="w-1/2 border border-slate-200 text-slate-400 py-2 text-[9px] font-black uppercase tracking-widest hover:bg-slate-100 rounded-none">BATAL</button>
                        <button type="submit" class="w-1/2 bg-emerald-600 text-white py-2 text-[9px] font-black uppercase tracking-widest hover:bg-emerald-700 shadow-sm rounded-none">IMPORT</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- 🔴 MODAL DELETE CONFIRMATION -->
        <div id="modalDeleteConfirmation" class="fixed inset-0 bg-slate-900/60 z-[110] hidden flex items-center justify-center p-4 print:hidden">
            <div class="bg-white w-full max-w-sm shadow-2xl border border-rose-500 overflow-hidden rounded-none">
                <div class="px-5 py-3 border-b border-rose-100 bg-rose-50 text-rose-700 flex items-center justify-between">
                    <h3 class="text-[10px] font-black uppercase tracking-widest">HAPUS REKORD</h3>
                    <button type="button" onclick="closeDeleteConfirmation()" class="text-rose-400 hover:text-rose-600 font-bold text-sm">&times;</button>
                </div>
                <div class="p-5 text-center">
                    <p class="text-[11px] text-slate-500 font-bold uppercase tracking-wide">Yakin ingin menghapus data?</p>
                    <p id="delete_target_name" class="text-[14px] font-black text-rose-600 uppercase tracking-tight mt-1"></p>
                </div>
                <div class="p-4 bg-slate-50 border-t border-slate-100 flex gap-2">
                    <button type="button" onclick="closeDeleteConfirmation()" class="w-1/2 border border-slate-200 text-slate-400 py-2 text-[9px] font-black uppercase tracking-widest hover:bg-slate-100 rounded-none">BATAL</button>
                    <form id="formConfirmDelete" action="" method="POST" class="w-1/2">
                        @csrf 
                        @method('DELETE')
                        <button type="submit" class="w-full bg-rose-600 text-white py-2 text-[9px] font-black uppercase tracking-widest hover:bg-rose-700 shadow-sm rounded-none">KONFIRMASI</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <style>
        @media print {
            @page { size: A4 portrait; margin: 8mm 10mm 10mm 10mm; }
            body { background: #ffffff !important; color: #000000 !important; font-family: system-ui, -apple-system, sans-serif; padding-top: 0 !important; margin-top: 0 !important; }
            html, body, main, div, app-layout, [class*="layout"] { overflow: visible !important; height: auto !important; max-height: none !important; position: static !important; background: #ffffff !important; }
            .page-break-forced { page-break-before: always !important; break-before: page !important; margin-top: 0 !important; }
            .print-page { width: 100% !important; display: block !important; position: relative; padding-top: 0 !important; }
            .header-laporan { margin-top: 0 !important; padding-top: 0 !important; }
            .sektor-block { page-break-inside: avoid !important; margin-bottom: 12px !important; }
            .table-print { width: 100% !important; border-collapse: collapse !important; table-layout: fixed !important; }
            .table-print th { height: 28px !important; background-color: #f8fafc !important; border: 1px solid #64748b !important; font-size: 9px !important; font-weight: 900 !important; }
            .row-print-target { height: 34px !important; page-break-inside: avoid !important; }
            .row-print-target td { height: 34px !important; padding: 0 6px !important; border: 1px solid #64748b !important; white-space: nowrap !important; overflow: hidden !important; text-overflow: ellipsis !important; vertical-align: middle !important; font-size: 10px !important; }
            .label-sektor { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; background-color: #f1f5f9 !important; border: 1px solid #64748b !important; }
        }
    </style>

    <script>
    document.getElementById('liveSearchInput').addEventListener('keyup', function() {
        const filter = this.value.toUpperCase();
        const rows = document.querySelectorAll('tbody tr');
        for (let i = 0; i < rows.length; i++) {
            const nameCell = rows[i].getElementsByTagName('td')[1];
            if (nameCell) {
                const textValue = nameCell.textContent || nameCell.innerText;
                rows[i].style.display = textValue.toUpperCase().indexOf(filter) > -1 ? "" : "none";
            }
        }
    });

    // 🚀 DYNAMIC JS ROUTE BASE URL (AMAN BILA DI-DEPLOY DI SUBFOLDER DOMAIN)
    const PESERTA_BASE_URL = "{{ url('peserta') }}";

    function openDetailPesertaModalFromRow(rowElement) {
        const data = JSON.parse(rowElement.getAttribute('data-peserta'));
        openDetailPesertaModal(data);
    }

    function openEditPesertaModalFromRow(buttonElement) {
        const row = buttonElement.closest('tr');
        const data = JSON.parse(row.getAttribute('data-peserta'));
        openEditPesertaModal(data);
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
            if(document.getElementById('edit_email')) document.getElementById('edit_email').value = data.email;
            if(document.getElementById('edit_no_hp')) document.getElementById('edit_no_hp').value = data.no_hp || '';
            if(document.getElementById('edit_tempat_kerja')) document.getElementById('edit_tempat_kerja').value = data.tempat_kerja || '';
            if(document.getElementById('edit_pelatihan')) document.getElementById('edit_pelatihan').value = data.pelatihan;
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
            
            modal.classList.remove('hidden');
        }
    }
    </script>
</x-app-layout>