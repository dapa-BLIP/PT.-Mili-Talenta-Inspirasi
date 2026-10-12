<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between w-full gap-2 sm:gap-0 print:hidden select-none">
            <div class="flex items-center gap-2">
                <h2 class="text-xs font-black text-slate-800 uppercase tracking-[0.2em]">Manajemen Kemitraan</h2>
                <span class="text-slate-300 text-xs">/</span>
                <span class="text-[10px] font-bold text-[#22a0f6] uppercase tracking-widest italic">Data Perusahaan Mitra</span>
            </div>
            
            <div class="flex items-center gap-1.5 w-full sm:w-auto justify-start sm:justify-end">
                <span class="text-[10px] font-black text-slate-500 uppercase tracking-widest font-mono bg-slate-100 border border-slate-200 px-3 py-1 rounded-xs">
                    TOTAL: {{ sprintf('%02d', count($perusahaans ?? [])) }} MITRA
                </span>
            </div>
        </div>
    </x-slot>

    <div class="space-y-4 print:py-0 print:px-0">
        
        <!-- ALERT NOTIFICATION SYSTEM -->
        <div id="global-alert-container" class="relative z-10 select-none print:hidden">
            @if(session('success'))
                <div class="mb-3 px-4 py-2.5 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xs shadow-2xs flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="text-[9px] font-black uppercase bg-emerald-600 text-white px-1.5 py-0.5 rounded-xs">OK</span>
                        <p class="text-[10.5px] font-bold tracking-wide uppercase">{{ session('success') }}</p>
                    </div>
                    <button type="button" class="text-emerald-600 hover:text-emerald-800 font-black text-base cursor-pointer" onclick="this.parentElement.remove()">&times;</button>
                </div>
            @endif

            @if($errors->any())
                <div class="mb-3 px-4 py-2.5 bg-rose-50 border border-rose-200 text-rose-800 rounded-xs shadow-2xs flex items-center justify-between">
                    <div class="space-y-0.5">
                        <span class="text-[9px] font-black uppercase bg-rose-600 text-white px-1.5 py-0.5 rounded-xs">ERR</span>
                        @foreach ($errors->all() as $err)
                            <p class="text-[10.5px] font-bold tracking-wide uppercase mt-0.5">* {{ $err }}</p>
                        @endforeach
                    </div>
                    <button type="button" class="text-rose-600 hover:text-rose-800 font-black text-base cursor-pointer" onclick="this.parentElement.remove()">&times;</button>
                </div>
            @endif
        </div>

        <!-- 3 KARTU STATISTIK (SKALA STANDAR MENTOR) -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div class="bg-white p-3.5 sm:p-4 rounded-xs border border-slate-200 shadow-xs flex items-center justify-between">
                <div>
                    <p class="text-[9.5px] font-black uppercase tracking-widest text-slate-400">Total Mitra</p>
                    <p class="text-xl sm:text-2xl font-black text-slate-800 mt-0.5 font-mono">{{ count($perusahaans ?? []) }}</p>
                </div>
                <div class="p-2.5 bg-slate-100 text-slate-700 rounded-xs border border-slate-200 shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                </div>
            </div>

            <div class="bg-white p-3.5 sm:p-4 rounded-xs border border-slate-200 shadow-xs flex items-center justify-between">
                <div>
                    <p class="text-[9.5px] font-black uppercase tracking-widest text-slate-400">Mitra Aktif</p>
                    <p class="text-xl sm:text-2xl font-black text-emerald-600 mt-0.5 font-mono">
                        {{ collect($perusahaans ?? [])->where('status', 'AKTIF')->count() }}
                    </p>
                </div>
                <div class="p-2.5 bg-emerald-50 text-emerald-600 rounded-xs border border-emerald-100 shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </div>

            <div class="bg-white p-3.5 sm:p-4 rounded-xs border border-slate-200 shadow-xs flex items-center justify-between">
                <div>
                    <p class="text-[9.5px] font-black uppercase tracking-widest text-slate-400">Peserta Terhubung</p>
                    <p class="text-xl sm:text-2xl font-black text-[#22a0f6] mt-0.5 font-mono">
                        {{ collect($perusahaans ?? [])->sum('pesertas_count') }}
                    </p>
                </div>
                <div class="p-2.5 bg-sky-50 text-[#22a0f6] rounded-xs border border-sky-100 shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                </div>
            </div>
        </div>

        <!-- TOOLBAR PENCARIAN & ACTION BUTTON -->
        <div class="bg-white border border-slate-200 p-3 sm:p-3.5 rounded-xs shadow-xs flex flex-col md:flex-row items-stretch md:items-center justify-between gap-3 print:hidden">
            <div class="flex items-center gap-2.5">
                <h3 class="text-[11px] font-black text-slate-700 uppercase tracking-widest italic">DATABASE MITRA INDUSTRI</h3>
                <span class="text-[9px] font-black text-[#22a0f6] bg-blue-50 border border-blue-100 px-2 py-0.5 uppercase tracking-wider font-mono rounded-xs">
                    TOTAL: {{ sprintf('%02d', count($perusahaans ?? [])) }} MITRA
                </span>
            </div>

            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2.5 w-full md:w-auto">
                <div class="relative w-full sm:w-64 lg:w-72">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-2.5 pointer-events-none text-slate-400">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </span>
                    <input type="text" 
                           id="live-search-perusahaan" 
                           onkeyup="filterPerusahaan()" 
                           placeholder="CARI PERUSAHAAN / PIC..." 
                           class="w-full pl-8 pr-3 py-1.5 bg-slate-50 border border-slate-200 text-[10px] font-bold uppercase tracking-wider text-slate-700 rounded-xs focus:outline-none focus:border-[#22a0f6] focus:bg-white placeholder-slate-400 shadow-inner">
                </div>

                <button type="button" 
                        onclick="document.getElementById('modalCreatePerusahaan').classList.remove('hidden')" 
                        class="w-full sm:w-auto justify-center px-4 py-1.5 bg-[#22a0f6] text-white text-[10px] font-black uppercase tracking-wider hover:bg-blue-600 transition rounded-xs shadow-xs cursor-pointer border-none flex items-center justify-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path></svg>
                    Tambah Perusahaan
                </button>
            </div>
        </div>

        <!-- TABLE VIEW DATA PERUSAHAAN MITRA (7 KOLOM SEIMBANG) -->
        <div class="bg-white border border-slate-200 rounded-xs shadow-xs overflow-x-auto print:hidden">
            <table class="w-full table-fixed min-w-[920px] text-left border-collapse select-none" id="table-perusahaan">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-slate-400">
                        <th class="w-[6%] px-3.5 py-3 text-[10px] font-black uppercase tracking-widest text-center whitespace-nowrap">NO.</th>
                        <th class="w-[34%] px-4 py-3 text-[10px] font-black uppercase tracking-widest">NAMA PERUSAHAAN</th>
                        <th class="w-[24%] px-4 py-3 text-[10px] font-black uppercase tracking-widest">PENANGGUNG JAWAB (PIC)</th>
                        <th class="w-[12%] px-3 py-3 text-[10px] font-black uppercase tracking-widest text-center whitespace-nowrap">PROGRAM</th>
                        <th class="w-[12%] px-3 py-3 text-[10px] font-black uppercase tracking-widest text-center whitespace-nowrap">PESERTA</th>
                        <th class="w-[6%] px-2 py-3 text-[10px] font-black uppercase tracking-widest text-center whitespace-nowrap">STATUS</th>
                        <th class="w-[6%] px-2 py-3 text-[10px] font-black uppercase tracking-widest text-center whitespace-nowrap">AKSI</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($perusahaans as $index => $item)
                    @php
                        $itemBase64 = base64_encode(json_encode($item));
                    @endphp
                    <tr data-item="{{ $itemBase64 }}"
                        onclick="bukaModalDetailPerusahaanFromRow(this)" 
                        class="hover:bg-slate-50/80 transition-colors row-perusahaan cursor-pointer group">
                        
                        <!-- 1. Nomor Urut -->
                        <td class="px-3.5 py-3 text-[11px] font-black text-slate-400 text-center index-perusahaan font-mono align-middle whitespace-nowrap">
                            {{ str_pad($index + 1, 3, '0', STR_PAD_LEFT) }}
                        </td>
                        
                        <!-- 2. Nama Perusahaan & Email di Bawahnya -->
                        <td class="px-4 py-3 align-middle">
                            <div class="truncate leading-tight">
                                <span class="text-[12px] font-black text-slate-800 uppercase tracking-tight truncate searchable-item group-hover:text-[#22a0f6] transition-colors block">
                                    {{ $item->nama_perusahaan }}
                                </span>
                                <span class="text-[10px] font-semibold text-slate-400 lowercase font-mono truncate block mt-0.5 searchable-item">
                                    {{ $item->email_perusahaan ?: '-' }}
                                </span>
                            </div>
                        </td>

                        <!-- 3. PIC / Penanggung Jawab -->
                        <td class="px-4 py-3 align-middle">
                            <span class="text-[11.5px] font-bold text-slate-700 uppercase tracking-tight block searchable-item truncate">
                                {{ $item->penanggung_jawab ?: '-' }}
                            </span>
                        </td>

                        <!-- 4. Total Program Pelatihan -->
                        <td class="px-3 py-3 text-center align-middle whitespace-nowrap">
                            <span class="inline-block px-2.5 py-1 text-[10px] font-black bg-slate-100 text-slate-700 border border-slate-200 rounded-xs font-mono">
                                {{ $item->total_program_count ?? 0 }} Program
                            </span>
                        </td>

                        <!-- 5. Total Peserta Terdaftar -->
                        <td class="px-3 py-3 text-center align-middle whitespace-nowrap">
                            <span class="inline-block px-2.5 py-1 text-[10px] font-black bg-blue-50 text-blue-700 border border-blue-100 rounded-xs font-mono">
                                {{ $item->pesertas_count ?? 0 }} Peserta
                            </span>
                        </td>

                        <!-- 6. Status Badge -->
                        <td class="px-2 py-3 text-center align-middle whitespace-nowrap">
                            @if($item->status === 'AKTIF')
                                <span class="inline-block text-[9px] font-black px-1.5 py-0.5 rounded-xs bg-emerald-50 text-emerald-600 border border-emerald-200">AKTIF</span>
                            @elseif($item->status === 'PENDING')
                                <span class="inline-block text-[9px] font-black px-1.5 py-0.5 rounded-xs bg-amber-50 text-amber-600 border border-amber-200">PENDING</span>
                            @else
                                <span class="inline-block text-[9px] font-black px-1.5 py-0.5 rounded-xs bg-rose-50 text-rose-600 border border-rose-200">NON-AKTIF</span>
                            @endif
                        </td>

                        <!-- 7. Tombol Aksi -->
                        <td class="px-2 py-3 text-center align-middle whitespace-nowrap" onclick="event.stopPropagation()">
                            <div class="flex items-center justify-center gap-1.5">
                                <button type="button" 
                                        data-url="{{ route('perusahaan.update', $item->id) }}"
                                        onclick="bukaModalEditPerusahaanFromRow(this)" 
                                        title="Edit Perusahaan" 
                                        class="p-1.5 bg-white border border-slate-200 hover:bg-[#22a0f6] hover:text-white text-blue-500 transition-colors shadow-2xs rounded-xs cursor-pointer">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                </button>
                                <button type="button" 
                                        data-url="{{ route('perusahaan.destroy', $item->id) }}"
                                        data-nama="{{ $item->nama_perusahaan }}"
                                        data-peserta="{{ $item->pesertas_count ?? 0 }}"
                                        onclick="mintaVerifikasiHapusFromBtn(this)" 
                                        title="Hapus Perusahaan" 
                                        class="p-1.5 bg-white border border-slate-200 hover:bg-rose-600 hover:text-white text-rose-500 transition-colors shadow-2xs rounded-xs cursor-pointer">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke-width="2.5"></path></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-8 text-center text-[10px] font-black text-slate-300 uppercase tracking-widest italic">
                            TIDAK ADA DATA PERUSAHAAN MITRA DITEMUKAN
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>

    <!-- MODAL & SCRIPT INCLUDES -->
    <div class="print:hidden">
        @include('company.data-perusahaan.create-data-perusahaan')
        @include('company.data-perusahaan.edit-data-perusahaan')
        @include('company.data-perusahaan.detail-data-perusahaan')
        @include('company.data-perusahaan.delete-data-perusahaan')
        @include('company.data-perusahaan.perusahaan-scripts')
    </div>
</x-app-layout>