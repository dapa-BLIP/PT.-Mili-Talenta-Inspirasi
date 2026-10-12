<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between w-full gap-2 sm:gap-0 print:hidden select-none">
            <div class="flex items-center gap-2">
                <h2 class="text-xs font-black text-slate-800 uppercase tracking-[0.2em]">Data Mentor</h2>
                <span class="text-slate-300 text-xs">/</span>
                <span class="text-[10px] font-bold text-emerald-600 uppercase tracking-widest italic">Monitoring View</span>
            </div>
            
            <!-- Toolbar Tombol Aksi (Responsif & Rapi di Mobile / Layar Kecil) -->
            <div class="grid grid-cols-2 sm:flex sm:items-center gap-1.5 w-full sm:w-auto">
                <button type="button" onclick="openImportMentorModal()" class="w-full sm:w-auto px-3 py-1.5 bg-emerald-600 border border-emerald-700 text-white text-[9px] font-black hover:bg-emerald-700 transition uppercase tracking-wider rounded-xs shadow-xs flex items-center justify-center gap-1 cursor-pointer">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                    Import Excel
                </button>
                <a href="{{ route('academic.exportMentor') }}" class="w-full sm:w-auto px-3 py-1.5 bg-slate-800 border border-slate-900 text-white text-[9px] font-black hover:bg-slate-900 transition uppercase tracking-wider rounded-xs shadow-xs flex items-center justify-center gap-1">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                    Export Excel
                </a>
                <button type="button" onclick="window.print()" class="w-full sm:w-auto px-3 py-1.5 bg-slate-600 text-white text-[9px] font-black hover:bg-slate-700 transition uppercase tracking-wider rounded-xs shadow-xs cursor-pointer flex items-center justify-center">
                    Print Report
                </button>
                <button type="button" onclick="openCreateMentorModal()" class="w-full sm:w-auto px-3.5 py-1.5 bg-[#22a0f6] text-white text-[9px] font-black uppercase tracking-wider hover:bg-blue-600 transition rounded-xs shadow-xs cursor-pointer flex items-center justify-center">
                    + Add Mentor
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
                <h3 class="text-[10.5px] sm:text-[11px] font-black text-slate-700 uppercase tracking-widest italic">DATABASE MENTOR</h3>
                <span class="text-[8.5px] sm:text-[9px] font-black text-[#22a0f6] bg-blue-50 border border-blue-100 px-2 py-0.5 uppercase tracking-wider font-mono rounded-xs">
                    TOTAL: {{ sprintf('%02d', count($trainers ?? [])) }} SDM
                </span>
            </div>

            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 w-full md:w-auto">
                <div class="relative w-full sm:w-56 lg:w-60">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-2.5 pointer-events-none text-slate-400">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </span>
                    <input type="text" id="liveSearchInput" placeholder="CARI NAMA / EMAIL..." 
                           class="w-full pl-8 pr-3 py-1.5 bg-slate-50 border border-slate-200 text-[10px] font-bold uppercase tracking-wider text-slate-700 rounded-xs focus:outline-none focus:border-[#22a0f6] focus:bg-white placeholder-slate-400 shadow-inner">
                </div>

                <div class="w-full sm:w-44 lg:w-48">
                    <select id="programDropdownFilter" onchange="applyFilters()" class="w-full px-2 py-1.5 bg-white border border-slate-200 text-[9.5px] sm:text-[10px] font-black uppercase tracking-wider text-slate-700 rounded-xs cursor-pointer focus:outline-none focus:border-slate-800 shadow-2xs">
                        <option value="ALL">SEMUA PROGRAM PELATIHAN</option>
                        @php
                            $uniquePrograms = collect($trainers ?? [])->pluck('program_pelatihan')->unique()->filter();
                        @endphp
                        @foreach ($uniquePrograms as $prog)
                            <option value="{{ strtoupper($prog) }}">{{ strtoupper($prog) }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="w-full sm:w-36 lg:w-40">
                    <select id="statusDropdownFilter" onchange="applyFilters()" class="w-full px-2 py-1.5 bg-white border border-slate-200 text-[9.5px] sm:text-[10px] font-black uppercase tracking-wider text-slate-700 rounded-xs cursor-pointer focus:outline-none focus:border-slate-800 shadow-2xs">
                        <option value="ALL">SEMUA STATUS</option>
                        <option value="AKTIF">AKTIF</option>
                        <option value="PENDING">PENDING</option>
                        <option value="NON-AKTIF">NON-AKTIF</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- 2. TABEL DATA MASTER MENTOR (WEB VIEW) -->
        <div class="bg-white border border-slate-200 rounded-xs shadow-xs overflow-x-auto print:hidden">
            <table class="w-full min-w-[820px] text-left border-collapse select-none">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-slate-400">
                        <th class="w-16 px-4 py-3 text-[9.5px] font-black uppercase tracking-widest text-center">NO.</th>
                        <th class="px-4 py-3 text-[9.5px] font-black uppercase tracking-widest min-w-[260px]">NAMA MENTOR & EMAIL</th>
                        <th class="px-4 py-3 text-[9.5px] font-black uppercase tracking-widest min-w-[180px]">PROGRAM PELATIHAN </th>
                        <th class="px-4 py-3 text-[9.5px] font-black uppercase tracking-widest min-w-[170px]">KONTAK MENTOR</th>
                        <th class="px-4 py-3 text-[9.5px] font-black uppercase tracking-widest text-center w-28">STATUS</th>
                        <th class="px-4 py-3 text-[9.5px] font-black uppercase tracking-widest text-center w-20">AKSI</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($trainers as $index => $t)
                    @php
                        $st = strtoupper($t->status ?? 'AKTIF');
                        $programNama = $t->program_pelatihan ?? '-';
                        $fotoUrl = $t->foto ? Storage::url($t->foto) : '';

                        $rawDigits = preg_replace('/[^0-9]/', '', $t->kontak ?? '');
                        if (str_starts_with($rawDigits, '62')) {
                            $waNumber = $rawDigits;
                        } elseif (str_starts_with($rawDigits, '0')) {
                            $waNumber = '62' . substr($rawDigits, 1);
                        } elseif (!empty($rawDigits)) {
                            $waNumber = '62' . $rawDigits;
                        } else {
                            $waNumber = '';
                        }
                    @endphp
                    <tr data-program="{{ strtoupper($programNama) }}" 
                        data-status="{{ $st }}"
                        data-mentor="{{ base64_encode(json_encode($t)) }}"
                        data-fotourl="{{ $fotoUrl }}"
                        onclick="openDetailMentorModalFromRow(this)" 
                        class="hover:bg-slate-50/80 cursor-pointer transition-colors row-mentor group">
                        
                        <td class="px-4 py-2.5 text-[10.5px] font-black text-slate-400 text-center font-mono align-middle">
                            {{ str_pad($index + 1, 4, '0', STR_PAD_LEFT) }}
                        </td>
                        
                        <td class="px-4 py-2.5 align-middle">
                            <div class="truncate min-w-0">
                                <span class="text-[11px] font-black text-slate-800 uppercase tracking-tight truncate mentor-name group-hover:text-[#22a0f6] transition-colors block">
                                    {{ $t->nama_mentor }}
                                </span>
                                <div class="text-[8.5px] font-bold text-slate-400 italic truncate mt-0.5 mentor-email">
                                    {{ $t->email ?: 'Email belum ditambahkan' }}
                                </div>
                            </div>
                        </td>

                        <td class="px-4 py-2.5 align-middle">
                            <span class="inline-block px-2 py-0.5 border text-[8px] font-black uppercase tracking-tight truncate max-w-[180px] rounded-xs {{ $t->program_pelatihan == 'Pelatihan Teknis' ? 'bg-blue-50/50 border-blue-200 text-[#22a0f6]' : 'bg-slate-50 border-slate-200 text-slate-600' }}">
                                {{ $programNama }}
                            </span>
                        </td>

                        <td class="px-4 py-2.5 align-middle" onclick="event.stopPropagation()">
                            @if(!empty($t->kontak))
                                <a href="https://wa.me/{{ $waNumber }}" target="_blank" title="Hubungi via WhatsApp ({{ $t->kontak }})" class="inline-flex items-center gap-1.5 px-2 py-0.5 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 text-emerald-700 rounded-xs transition-colors">
                                    <svg class="w-3 h-3 text-emerald-600 shrink-0" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12.04 2c-5.46 0-9.91 4.45-9.91 9.91 0 1.75.46 3.45 1.32 4.95L2.05 22l5.25-1.38c1.45.79 3.08 1.21 4.74 1.21 5.46 0 9.91-4.45 9.91-9.91 0-2.65-1.03-5.14-2.9-7.01A9.816 9.816 0 0012.04 2zm.01 1.67c2.2 0 4.26.86 5.82 2.42a8.225 8.225 0 012.41 5.83c0 4.54-3.7 8.24-8.24 8.24-1.45 0-2.87-.38-4.12-1.1l-.3-.18-3.12.82.83-3.04-.2-.31a8.19 8.19 0 01-1.26-4.43c0-4.54 3.7-8.24 8.24-8.24zm4.52 11.66c-.25-.13-1.47-.72-1.7-.81-.23-.08-.39-.13-.56.13-.17.25-.64.81-.79.98-.14.17-.29.19-.54.06-.25-.13-1.06-.39-2.01-1.24-.74-.66-1.25-1.48-1.39-1.73-.14-.25-.02-.39.11-.51.11-.11.25-.29.37-.43.13-.15.17-.25.25-.42.08-.17.04-.31-.02-.44-.06-.13-.56-1.34-.76-1.84-.2-.49-.4-.42-.56-.43h-.47c-.17 0-.44.06-.67.31-.23.25-.88.86-.88 2.1 0 1.24.9 2.44 1.03 2.61.13.17 1.77 2.71 4.3 3.8 2.53 1.09 2.53.73 2.99.69.46-.04 1.47-.6 1.68-1.18.21-.58.21-1.08.15-1.18-.06-.1-.23-.17-.48-.29z"/>
                                    </svg>
                                    <span class="text-[9.5px] font-bold font-mono tracking-tight">{{ $t->kontak }}</span>
                                </a>
                            @else
                                <span class="text-[10px] font-bold text-slate-400 italic">-</span>
                            @endif
                        </td>

                        <td class="px-4 py-2.5 text-center align-middle" onclick="event.stopPropagation()">
                            @if($st === 'AKTIF')
                                <span class="inline-block text-[8px] font-black uppercase px-2 py-0.5 tracking-wider bg-emerald-100 text-emerald-800 rounded-xs shadow-2xs">● AKTIF</span>
                            @elseif($st === 'NON-AKTIF')
                                <span class="inline-block text-[8px] font-black uppercase px-2 py-0.5 tracking-wider bg-rose-100 text-rose-800 rounded-xs shadow-2xs">× NON-AKTIF</span>
                            @else
                                <span class="inline-block text-[8px] font-black uppercase px-2 py-0.5 tracking-wider bg-amber-100 text-amber-800 rounded-xs shadow-2xs">○ PENDING</span>
                            @endif
                        </td>

                        <td class="px-4 py-2.5 text-center align-middle" onclick="event.stopPropagation()">
                            <div class="flex items-center justify-center gap-1">
                                <button type="button" onclick="openEditMentorModalFromRow(this, '{{ route('academic.updateTrainer', $t->id) }}')" title="Edit Mentor" class="p-1 bg-white border border-slate-200 hover:bg-[#22a0f6] hover:text-white text-blue-500 transition-colors shadow-2xs rounded-xs cursor-pointer">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" stroke-width="2.5"></path></svg>
                                </button>
                                <button type="button" onclick="triggerDeleteConfirmation('{{ route('academic.destroyTrainer', $t->id) }}', '{{ addslashes($t->nama_mentor) }}')" title="Hapus Mentor" class="p-1 bg-white border border-slate-200 hover:bg-rose-600 hover:text-white text-rose-500 transition-colors shadow-2xs rounded-xs cursor-pointer">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke-width="2.5"></path></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-8 text-center text-[10px] font-black text-slate-300 uppercase tracking-widest italic">
                            TIDAK ADA DATA MENTOR DITEMUKAN
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- 3. TEMPLATE PRINT REPORT -->
        @include('academic.sub-data-mentor.print-mentor')

    </div>

    <!-- 4. INCLUDE SEMUA MODAL SUB-DATA -->
    @include('academic.sub-data-mentor.create-mentor')
    @include('academic.sub-data-mentor.edit-mentor')
    @include('academic.sub-data-mentor.detail-mentor')
    @include('academic.sub-data-mentor.import-mentor')
    @include('academic.sub-data-mentor.delete-mentor')

    <!-- 5. ENGINE JAVASCRIPT -->
    @include('academic.sub-data-mentor.mentor-scripts')

</x-app-layout>