<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between w-full gap-2 sm:gap-0 print:hidden select-none">
            <div class="flex items-center gap-2">
                <h2 class="text-xs font-black text-slate-800 uppercase tracking-[0.2em]">Data Program Pelatihan</h2>
                <span class="text-slate-300 text-xs">/</span>
                <span class="text-[10px] font-bold text-[#22a0f6] uppercase tracking-widest italic">Monitoring Pelatihan</span>
            </div>
            
            <div class="flex flex-wrap items-center gap-1.5 w-full sm:w-auto justify-start sm:justify-end">
                <button type="button" 
                        onclick="document.getElementById('modalImportPelatihan').classList.remove('hidden')" 
                        class="px-3 py-1.5 bg-emerald-600 border border-emerald-700 text-white text-[9px] font-black hover:bg-emerald-700 transition uppercase tracking-wider rounded-xs shadow-xs flex items-center gap-1 cursor-pointer">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                    Import Excel
                </button>
                <a href="{{ route('academic.exportPelatihan') }}" 
                   class="px-3 py-1.5 bg-slate-800 border border-slate-900 text-white text-[9px] font-black hover:bg-slate-900 transition uppercase tracking-wider rounded-xs shadow-xs flex items-center gap-1">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                    Export Excel
                </a>
            </div>
        </div>
    </x-slot>

    <div class="space-y-3.5 sm:space-y-4 print:py-0 print:px-0">
        
        <!-- ALERT NOTIFICATION SYSTEM (IDENTIK DENGAN MENTOR) -->
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

        <!-- 3 KARTU STATISTIK (SKALA COMPACT & PROPORSIONAL) -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5 sm:gap-3">
            <div class="bg-white p-3 sm:p-3.5 rounded-xs border border-slate-200 shadow-xs flex items-center justify-between">
                <div>
                    <p class="text-[9px] sm:text-[9px] font-black uppercase tracking-widest text-slate-400">Total Program Pelatihan</p>
                    <p class="text-lg sm:text-xl font-black text-slate-800 mt-0.5 font-mono">{{ count($kategoriPelatihans ?? []) }}</p>
                </div>
                <div class="p-2 bg-slate-100 text-slate-700 rounded-xs border border-slate-200 shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
                </div>
            </div>

            <div class="bg-white p-3 sm:p-3.5 rounded-xs border border-slate-200 shadow-xs flex items-center justify-between">
                <div>
                    <p class="text-[9px] sm:text-[9px] font-black uppercase tracking-widest text-slate-400">Total Materi Pelatihan</p>
                    <p class="text-lg sm:text-xl font-black text-slate-800 mt-0.5 font-mono">{{ count($materis ?? []) }}</p>
                </div>
                <div class="p-2 bg-slate-800 text-white rounded-xs shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                </div>
            </div>

            <div class="bg-white p-3 sm:p-3.5 rounded-xs border border-slate-200 shadow-xs flex items-center justify-between">
                <div>
                    <p class="text-[9px] sm:text-[9git statuspx] font-black uppercase tracking-widest text-slate-400">Total Kelas Pelatihan</p>
                    <p class="text-lg sm:text-xl font-black text-slate-800 mt-0.5 font-mono">{{ count($pelatihans ?? []) }}</p>
                </div>
                <div class="p-2 bg-sky-50 text-[#22a0f6] rounded-xs border border-sky-100 shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                </div>
            </div>
        </div>

        <!-- 1. TOOLBAR PENCARIAN & ACTION BUTTONS -->
        <div class="bg-white border border-slate-200 p-3 sm:p-3.5 rounded-xs shadow-xs flex flex-col md:flex-row items-stretch md:items-center justify-between gap-2.5 sm:gap-3 print:hidden">
            <div class="flex items-center gap-2.5">
                <h3 class="text-[10.5px] sm:text-[11px] font-black text-slate-700 uppercase tracking-widest italic">DATABASE KELAS PELATIHAN</h3>
                <span class="text-[8.5px] sm:text-[9px] font-black text-[#22a0f6] bg-blue-50 border border-blue-100 px-2 py-0.5 uppercase tracking-wider font-mono rounded-xs">
                    TOTAL: {{ sprintf('%02d', count($pelatihans ?? [])) }} KELAS
                </span>
            </div>

            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 w-full md:w-auto">
                <div class="relative w-full sm:w-56 lg:w-60">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-2.5 pointer-events-none text-slate-400">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </span>
                    <input type="text" 
                           id="live-search-pelatihan" 
                           onkeyup="filterPelatihan()" 
                           placeholder="CARI KELAS / MODUL..." 
                           class="w-full pl-8 pr-3 py-1.5 bg-slate-50 border border-slate-200 text-[10px] font-bold uppercase tracking-wider text-slate-700 rounded-xs focus:outline-none focus:border-[#22a0f6] focus:bg-white placeholder-slate-400 shadow-inner">
                </div>

                <div class="flex flex-wrap items-center gap-1.5 w-full sm:w-auto">
                    <button type="button" 
                            onclick="document.getElementById('modalModulPelatihan').classList.remove('hidden')" 
                            class="px-3 py-1.5 bg-slate-600 border border-slate-700 text-white text-[9px] font-black hover:bg-slate-700 transition uppercase tracking-wider rounded-xs shadow-xs cursor-pointer">
                        + Kelola Program Pelatihan
                    </button>
                    <button type="button" 
                            onclick="document.getElementById('modalBankMateri').classList.remove('hidden')" 
                            class="px-3 py-1.5 bg-slate-800 border border-slate-900 text-white text-[9px] font-black hover:bg-black transition uppercase tracking-wider rounded-xs shadow-xs cursor-pointer">
                        + Kelola Materi Pelatihan
                    </button>
                    <button type="button" 
                            onclick="document.getElementById('modalCreatePelatihan').classList.remove('hidden')" 
                            class="px-3.5 py-1.5 bg-[#22a0f6] text-white text-[9px] font-black uppercase tracking-wider hover:bg-blue-600 transition rounded-xs shadow-xs cursor-pointer">
                        + Tambah Kelas Pelatihan
                    </button>
                </div>
            </div>
        </div>

        <!-- 2. TABEL DATA MASTER KELAS PELATIHAN -->
        <div class="bg-white border border-slate-200 rounded-xs shadow-xs overflow-x-auto print:hidden">
            <table class="w-full min-w-[820px] text-left border-collapse select-none" id="table-pelatihan">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-slate-400">
                        <th class="w-16 px-4 py-3 text-[9.5px] font-black uppercase tracking-widest text-center">NO.</th>
                        <th class="px-4 py-3 text-[9.5px] font-black uppercase tracking-widest min-w-[260px]">NAMA KELAS PELATIHAN</th>
                        <th class="px-4 py-3 text-[9.5px] font-black uppercase tracking-widest min-w-[180px]">MENTOR PELATIHAN</th>
                        <th class="px-4 py-3 text-[9.5px] font-black uppercase tracking-widest min-w-[200px]">KOMPONEN PENILAIAN</th>
                        <th class="px-4 py-3 text-[9.5px] font-black uppercase tracking-widest text-center w-20">AKSI</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($pelatihans as $index => $p)
                    @php
                        $cleanTitle = trim(str_ireplace('KELAS', '', $p->nama_pelatihan ?: $p->jenis_pelatihan));
                        $komponenArray = is_array($p->komponen_nilai) ? $p->komponen_nilai : (json_decode($p->komponen_nilai, true) ?? []);
                        $komponenText = implode(', ', $komponenArray);
                        $komponenJson = json_encode($komponenArray);
                        $materiIdsJson = json_encode($p->materi_ids ?? []);
                    @endphp
                    <tr data-nama="{{ $cleanTitle }}" 
                        data-jenis="{{ $p->jenis_pelatihan }}" 
                        data-mentor="{{ $p->mentor->nama_mentor ?? 'Belum Ditunjuk' }}" 
                        data-materi-ids="{{ base64_encode($materiIdsJson) }}" 
                        data-komponen="{{ base64_encode($komponenJson) }}" 
                        onclick="bukaModalDetailPelatihanFromRow(this)" 
                        class="hover:bg-slate-50/80 cursor-pointer transition-colors row-pelatihan group">
                        
                        <td class="px-4 py-2.5 text-[10.5px] font-black text-slate-400 text-center font-mono align-middle index-pelatihan">
                            {{ str_pad($index + 1, 4, '0', STR_PAD_LEFT) }}
                        </td>
                        
                        <td class="px-4 py-2.5 align-middle">
                            <div class="truncate min-w-0">
                                <span class="text-[11px] font-black text-slate-800 uppercase tracking-tight truncate search-item group-hover:text-[#22a0f6] transition-colors block">
                                    {{ $p->nama_pelatihan }}
                                </span>
                            </div>
                        </td>

                        <td class="px-4 py-2.5 align-middle">
                            <span class="text-[10px] font-bold text-slate-700 uppercase tracking-tight truncate block search-item">
                                {{ $p->mentor->nama_mentor ?? 'Belum Ditunjuk' }}
                            </span>
                        </td>

                        <td class="px-4 py-2.5 align-middle">
                            <div class="flex flex-wrap gap-1 max-w-[280px]">
                                @forelse($komponenArray as $komp)
                                    <span class="inline-block px-1.5 py-0.5 border text-[8px] font-black uppercase tracking-tight rounded-xs bg-slate-50 border-slate-200 text-slate-600">
                                        {{ str_replace('_', ' ', $komp) }}
                                    </span>
                                @empty
                                    <span class="text-[9px] text-slate-400 italic font-medium">-</span>
                                @endforelse
                            </div>
                        </td>

                        <td class="px-4 py-2.5 text-center align-middle" onclick="event.stopPropagation()">
                            <div class="flex items-center justify-center gap-1">
                                <button type="button" 
                                        data-nama="{{ $p->nama_pelatihan }}" 
                                        data-jenis="{{ $p->jenis_pelatihan }}" 
                                        data-trainer="{{ $p->trainer_id }}" 
                                        data-komponen="{{ $komponenText }}" 
                                        data-materi-ids="{{ base64_encode($materiIdsJson) }}" 
                                        data-url="{{ route('academic.updatePelatihan', $p->id) }}" 
                                        onclick="bukaModalEditPelatihan(this)" 
                                        title="Edit Kelas" 
                                        class="p-1 bg-white border border-slate-200 hover:bg-[#22a0f6] hover:text-white text-blue-500 transition-colors shadow-2xs rounded-xs cursor-pointer">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" stroke-width="2.5"></path></svg>
                                </button>
                                <button type="button" 
                                        onclick="mintaVerifikasiHapus('{{ route('academic.destroyPelatihan', $p->id) }}', '{{ addslashes($cleanTitle) }}')" 
                                        title="Hapus Kelas" 
                                        class="p-1 bg-white border border-slate-200 hover:bg-rose-600 hover:text-white text-rose-500 transition-colors shadow-2xs rounded-xs cursor-pointer">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke-width="2.5"></path></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-8 text-center text-[10px] font-black text-slate-300 uppercase tracking-widest italic">
                            TIDAK ADA DATA KELAS DITEMUKAN
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>

    <!-- INCLUDE MODAL SUB-DATA -->
    @include('academic.sub-data-pelatihan.modal-create-materi')
    @include('academic.sub-data-pelatihan.modal-create-pelatihan')
    @include('academic.sub-data-pelatihan.modal-create-program-pelatihan')
    @include('academic.sub-data-pelatihan.edit-pelatihan')
    @include('academic.sub-data-pelatihan.modal-detail')
    @include('academic.sub-data-pelatihan.modal-import')
    @include('academic.sub-data-pelatihan.modal-delete')

    <!-- ENGINE JAVASCRIPT -->
    @include('academic.sub-data-pelatihan.pelatihan-scripts')

</x-app-layout>