<!-- 2. TABEL ENTRY MATRIKS (RINGKAS & COMPACT) -->
<div id="matrix-table-container" class="bg-white border border-slate-200 shadow-xs rounded-xs overflow-hidden opacity-40 pointer-events-none transition-all duration-300 select-none">
    <div class="px-4 py-2.5 border-b border-slate-200 bg-slate-50 flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-2.5 sm:gap-0">
        <div class="flex items-center gap-2">
            <h3 class="text-xs font-black text-slate-800 uppercase tracking-[0.2em] italic">Kolom Input Penilaian</h3>
            <span class="text-[8.5px] font-black text-[#22a0f6] bg-blue-50 border border-blue-100 px-2 py-0.5 uppercase tracking-wider font-mono rounded-xs">
                Total: <span id="visible-count">00</span> Peserta
            </span>
        </div>

        <div class="grid grid-cols-2 sm:flex sm:items-center gap-1.5 w-full sm:w-auto">
            <button type="button" id="btn-export-matrix-table" onclick="handleExportNilai(event)" 
               class="w-full sm:w-auto px-3 py-1.5 bg-slate-800 border border-slate-900 hover:bg-slate-900 text-white text-[9.5px] font-black uppercase tracking-wider rounded-xs shadow-xs flex items-center justify-center gap-1 cursor-pointer transition">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                Export Excel
            </button>

            <button type="submit" id="btn-save-matrix" 
                class="w-full sm:w-auto px-3.5 py-1.5 bg-[#22a0f6] text-white text-[9.5px] font-black uppercase tracking-wider hover:bg-blue-600 transition rounded-xs shadow-xs flex items-center justify-center gap-1 cursor-pointer">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
                SIMPAN NILAI
            </button>
        </div>
    </div>

    <!-- SCROLL CONTAINER RINGKAS -->
    <div class="w-full overflow-x-auto">
        <table class="w-full text-left border-collapse table-fixed min-w-[750px]" id="matrix-main-table">
            <thead>
                <tr class="bg-slate-50/80 border-b border-slate-200 text-slate-500" id="matrix-header-row">
                    <th class="w-14 px-2 py-2.5 text-[9.5px] font-black uppercase tracking-wider text-center sticky left-0 bg-slate-50 z-20 border-r border-slate-200">No.</th>
                    <th class="w-64 px-3 py-2.5 text-[9.5px] font-black uppercase tracking-wider sticky left-14 bg-slate-50 z-20 border-r border-slate-200 shadow-[2px_0_4px_-2px_rgba(0,0,0,0.05)]">Nama Peserta</th>
                    <!-- Kolom Dinamis (Tugas, UTS, UAS, Praktik) disuntikkan oleh JS sebelum th-catatan -->
                    <th class="w-64 px-3 py-2.5 text-[9.5px] font-black uppercase tracking-wider dynamic-header-mark" id="th-catatan">Catatan / Evaluasi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100" id="participant-rows">
                <tr id="row-empty-peserta" class="hidden">
                    <td colspan="10" class="p-6 text-center text-slate-400 text-xs font-bold uppercase tracking-wider italic">
                        Tidak ada peserta terdaftar untuk kelas pelatihan ini.
                    </td>
                </tr>
                @foreach($pesertas as $index => $p)
                @php
                    $rawStatus = strtoupper(trim($p->status ?? 'AKTIF'));
                    $isAktif = in_array($rawStatus, ['AKTIF', 'ACTIVE', '']);
                @endphp
                <tr class="transition-colors row-peserta {{ $isAktif ? 'hover:bg-slate-50/70' : 'bg-slate-50/50 opacity-75' }}" 
                    data-pelatihan="{{ trim($p->pelatihan ?? '') }}"
                    data-status="{{ $isAktif ? 'AKTIF' : $rawStatus }}"
                    data-peserta-id="{{ $p->id }}">
                    
                    <td class="px-2 py-2 text-xs font-bold text-slate-400 text-center font-mono align-middle index-kolom sticky left-0 bg-white z-10 border-r border-slate-100">
                        {{ str_pad($index + 1, 4, '0', STR_PAD_LEFT) }}
                    </td>

                    <td class="px-3 py-2 align-middle sticky left-14 bg-white z-10 border-r border-slate-100 shadow-[2px_0_4px_-2px_rgba(0,0,0,0.05)]">
                        <div class="truncate">
                            <div class="flex items-center gap-1 leading-tight">
                                <span class="text-xs font-black text-slate-800 uppercase tracking-tight truncate">{{ $p->nama_lengkap }}</span>
                                @if(in_array(strtolower($p->gender ?? ''), ['pria', 'l']))
                                    <span class="text-[7px] font-black bg-blue-50 text-blue-600 border border-blue-100 px-1 py-0.2 font-mono shrink-0 rounded-xs">L</span>
                                @else
                                    <span class="text-[7px] font-black bg-pink-50 text-pink-600 border border-pink-100 px-1 py-0.2 font-mono shrink-0 rounded-xs">P</span>
                                @endif
                            </div>
                            
                            <div class="flex items-center gap-1.5 mt-0.5 truncate">
                                <span class="text-[9px] font-bold text-slate-400 uppercase tracking-tight truncate">{{ $p->tempat_kerja ?? 'PT MILI' }}</span>
                                @if(!$isAktif)
                                    <span class="text-[7px] font-black px-1.5 py-0.2 uppercase tracking-wider rounded-xs {{ in_array($rawStatus, ['NON-AKTIF', 'NONAKTIF', 'INACTIVE']) ? 'bg-rose-100 text-rose-700' : 'bg-amber-100 text-amber-800' }}">
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
                               disabled
                               class="{{ $isAktif ? 'input-pintar input-keterangan bg-white text-slate-800' : 'bg-slate-100 text-slate-400 cursor-not-allowed italic' }} w-full px-2.5 py-1 h-7 border border-slate-200 text-xs font-medium placeholder:text-slate-300 rounded-xs focus:outline-none focus:border-[#22a0f6] transition-all">
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

