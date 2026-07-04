<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between w-full print:hidden select-none">
            <div class="flex items-center gap-2">
                <h2 class="text-xs font-black text-slate-800 uppercase tracking-[0.2em]">Training Schedule</h2>
                <span class="text-slate-300 text-xs">/</span>
                <span class="text-[10px] font-bold text-[#22a0f6] uppercase tracking-widest italic">Monitoring View</span>
            </div>
            
            <div class="flex items-center gap-4">
                <div class="flex flex-col items-end border-r border-slate-200 pr-4">
                    <span class="text-[8px] font-black uppercase tracking-widest text-slate-400">WAKTU INDONESIA (WIB)</span>
                    <span id="realtime-clock" class="text-[14px] font-black text-slate-800 tracking-wider font-mono">00:00:00</span>
                </div>

                <button type="button" onclick="openCreateModal()" class="bg-[#22a0f6] hover:bg-blue-600 text-white px-4 py-1.5 rounded-sm text-[9px] font-black uppercase tracking-widest transition-all flex items-center gap-1.5 shadow-sm border-none cursor-pointer">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                    </svg>
                    New Entry
                </button>
            </div>
        </div>
    </x-slot>

    <div class="print:py-0 print:px-0 py-4 max-w-[1600px] mx-auto px-4">
        
        @if(session('success'))
        <div class="mb-4 px-4 py-3 bg-emerald-50 border border-emerald-200 rounded-sm text-emerald-700 shadow-sm flex items-center justify-between print:hidden">
            <div class="flex items-center gap-2">
                <span class="text-[9px] font-black uppercase tracking-widest bg-emerald-500 text-white px-1.5 py-0.5 rounded-sm">Success</span>
                <span class="text-[10px] font-bold uppercase tracking-wide">{{ session('success') }}</span>
            </div>
            <button type="button" class="text-emerald-400 hover:text-emerald-600 font-bold text-sm leading-none border-none bg-transparent cursor-pointer" onclick="this.parentElement.remove()">&times;</button>
        </div>
        @endif

        <!-- FILTER FORM PANEL (MENGIRIM PARAMETER GET KE CONTROLLER) -->
        <div class="bg-white border border-slate-200 rounded-sm shadow-sm p-4 mb-4 print:hidden">
            <form method="GET" action="{{ url()->current() }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3 items-end">
                <div class="lg:col-span-2">
                    <label class="text-[8px] font-black text-slate-400 uppercase tracking-widest block mb-1.5">System Monitor</label>
                    <div class="bg-slate-900 border border-slate-800 rounded-sm px-3 flex items-center justify-between h-[31px] tabular-nums">
                        <span class="text-[8px] font-black text-slate-400 uppercase tracking-widest">Total</span>
                        <span class="text-[11px] font-black text-blue-400 bg-slate-950/50 px-2 py-0.5 rounded-sm border border-slate-800">
                            {{ sprintf('%02d', count($jadwals)) }}
                        </span>
                    </div>
                </div>

                <div class="lg:col-span-2">
                    <label class="text-[8px] font-black text-slate-400 uppercase tracking-widest block mb-1.5">Timeline Start</label>
                    <input type="date" id="filter_start_date" name="start_date" value="{{ request('start_date') }}" 
                        class="w-full bg-slate-50 border border-slate-200 rounded-sm px-3 py-1.5 h-[31px] text-[10px] font-bold text-slate-700 uppercase tracking-tight focus:outline-none focus:border-[#22a0f6] transition-colors">
                </div>
                
                <div class="lg:col-span-2">
                    <label class="text-[8px] font-black text-slate-400 uppercase tracking-widest block mb-1.5">Timeline End</label>
                    <input type="date" id="filter_end_date" name="end_date" value="{{ request('end_date') }}" 
                        class="w-full bg-slate-50 border border-slate-200 rounded-sm px-3 py-1.5 h-[31px] text-[10px] font-bold text-slate-700 uppercase tracking-tight focus:outline-none focus:border-[#22a0f6] transition-colors">
                </div>

                <div class="lg:col-span-2">
                    <label class="text-[8px] font-black text-slate-400 uppercase tracking-widest block mb-1.5">Select Trainer</label>
                    <select name="trainer_id" class="w-full bg-slate-50 border border-slate-200 rounded-sm px-2 py-1 h-[31px] text-[10px] font-bold text-slate-700 uppercase focus:outline-none focus:border-[#22a0f6] transition-colors cursor-pointer">
                        <option value="">-- ALL TRAINERS --</option>
                        @foreach($trainers as $trainer)
                            <option value="{{ $trainer->id }}" {{ request('trainer_id') == $trainer->id ? 'selected' : '' }}>
                                {{ strtoupper($trainer->nama_mentor) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="lg:col-span-2">
                    <label class="text-[8px] font-black text-slate-400 uppercase tracking-widest block mb-1.5">Select Status</label>
                    <select name="status" class="w-full bg-slate-50 border border-slate-200 rounded-sm px-2 py-1 h-[31px] text-[10px] font-bold text-slate-700 uppercase focus:outline-none focus:border-[#22a0f6] transition-colors cursor-pointer">
                        <option value="">-- ALL STATUS --</option>
                        <option value="on-going" {{ request('status') == 'on-going' ? 'selected' : '' }}>ON-GOING</option>
                        <option value="complete" {{ request('status') == 'complete' ? 'selected' : '' }}>COMPLETE</option>
                    </select>
                </div>
                
                <div class="flex gap-2 lg:col-span-2">
                    <button type="submit" class="flex-1 bg-slate-800 hover:bg-slate-900 text-white h-[31px] rounded-sm text-[9px] font-black uppercase tracking-widest transition-all shadow-sm border-none cursor-pointer">Query</button>
                    @if(request('start_date') || request('end_date') || request('trainer_id') || request('status'))
                        <a href="{{ url()->current() }}" class="border border-slate-200 bg-white hover:bg-slate-50 text-slate-400 px-2.5 h-[31px] rounded-sm text-[9px] font-black uppercase tracking-widest transition-all text-center flex items-center justify-center decoration-none">Clear</a>
                    @endif
                </div>
            </form>
        </div>
        
        <!-- DATA TABLE MONITOR MATRIX -->
        <div class="bg-white border border-slate-200 rounded-sm shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse table-fixed">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200 text-slate-400">
                            <th class="w-[32%] px-5 py-3 text-[9px] font-black uppercase tracking-widest">Activity & Description</th>
                            <th class="w-[15%] px-5 py-3 text-[9px] font-black uppercase tracking-widest">Lead Trainer</th>
                            <th class="w-[26%] px-5 py-3 text-[9px] font-black uppercase tracking-widest text-center">Timeline Period</th>
                            <th class="w-[15%] px-5 py-3 text-[9px] font-black uppercase tracking-widest text-center">Status Matrix</th>
                            <th class="w-[12%] px-5 py-3 text-right text-[9px] font-black uppercase tracking-widest print:hidden">Operations</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($jadwals as $j)
                        <tr class="hover:bg-slate-50/40 transition-colors group">
                            <td class="px-5 py-4">
                                <div class="text-[11px] font-black text-slate-700 uppercase tracking-tight truncate" title="{{ $j->judul_kegiatan }}">{{ $j->judul_kegiatan }}</div>
                                <div class="text-[9px] text-slate-400 font-medium mt-0.5 truncate italic" title="{{ $j->description }}">{{ $j->description ?? 'No extra details provided' }}</div>
                            </td>
                            <td class="px-5 py-4">
                                <span class="text-[10px] font-bold text-slate-600 uppercase tracking-tighter truncate">{{ $j->trainer->nama_mentor ?? 'No Trainer Assigned' }}</span>
                            </td>
                            <td class="px-5 py-4 text-center">
                                <div class="inline-flex flex-col items-center justify-center gap-0.5 text-[10px] font-bold text-slate-500 tabular-nums">
                                    <div class="flex items-center gap-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                        <span>{{ $j->tanggal_mulai ? \Carbon\Carbon::parse($j->tanggal_mulai)->format('Y-m-d') : '-' }}</span>
                                        <span class="text-slate-300 font-normal">→</span>
                                        <span>{{ $j->tanggal_selesai ? \Carbon\Carbon::parse($j->tanggal_selesai)->format('Y-m-d') : '-' }}</span>
                                    </div>
                                    <div class="text-[9px] text-slate-400 font-semibold flex items-center gap-1 bg-slate-50 border border-slate-100 px-1.5 py-0.2 rounded-sm mt-0.5">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-2.5 w-2.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                        <span>{{ \Carbon\Carbon::parse($j->jam_mulai)->format('H:i') }}</span> - <span>{{ \Carbon\Carbon::parse($j->jam_selesai)->format('H:i') }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-4 text-center">
                                <div class="flex flex-col items-center justify-center gap-1">
                                    @php
                                        $tglString = $j->tanggal_selesai instanceof \DateTimeInterface ? $j->tanggal_selesai->format('Y-m-d') : \Carbon\Carbon::parse($j->tanggal_selesai)->format('Y-m-d');
                                        $waktuSelesaiJadwal = \Carbon\Carbon::parse($tglString . ' ' . $j->jam_selesai, 'Asia/Jakarta');
                                        $waktuSekarang = \Carbon\Carbon::now('Asia/Jakarta');
                                        $isComplete = in_array(strtolower($j->status), ['complete', 'selesai']) || $waktuSekarang->greaterThanOrEqualTo($waktuSelesaiJadwal);
                                    @endphp
                                    <span class="px-2 py-0.5 border text-[8px] font-black uppercase rounded-sm tracking-wider {{ $isComplete ? 'bg-emerald-50 border-emerald-200 text-emerald-600' : 'bg-amber-50 border-amber-200 text-amber-600' }}">
                                        {{ $isComplete ? 'COMPLETE' : 'ON-GOING' }}
                                    </span>
                                    <span class="text-[8px] font-black text-slate-400 uppercase tracking-widest text-center block">{{ $j->jenis_pelaksanaan }}</span>
                                </div>
                            </td>
                            <td class="px-5 py-4 print:hidden">
                                <div class="flex justify-end gap-2 items-center">
                                    <!-- 🛠️ FIX SECURE STRING FORMAT DI BAWAH INI (BEBAS BACKSLASH ERROR) -->
                                    <button type="button" onclick="openEditModal({ id: '{{ $j->id }}', judul: '{{ base64_encode($j->judul_kegiatan) }}', description: '{{ base64_encode($j->description ?? '') }}', trainer_id: '{{ $j->trainer_id }}', tgl_mulai: '{{ $j->tanggal_mulai ? \Carbon\Carbon::parse($j->tanggal_mulai)->format('Y-m-d') : '' }}', tgl_selesai: '{{ $j->tanggal_selesai ? \Carbon\Carbon::parse($j->tanggal_selesai)->format('Y-m-d') : '' }}', jam_mulai: '{{ $j->jam_mulai }}', jam_selesai: '{{ $j->jam_selesai }}', jenis: '{{ $j->jenis_pelaksanaan }}' }, true)" class="p-2.5 border border-slate-200 bg-white text-slate-600 hover:bg-[#22a0f6] hover:text-white hover:border-[#22a0f6] rounded-none transition-all flex items-center justify-center shadow-sm cursor-pointer">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                    </button>
                                    <button type="button" onclick="triggerDeleteModal('{{ $j->id }}', '{{ addslashes($j->judul_kegiatan) }}')" class="p-2.5 border border-slate-200 bg-white text-rose-500 hover:bg-rose-600 hover:text-white hover:border-rose-600 rounded-none transition-all flex items-center justify-center shadow-sm cursor-pointer">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="py-16 text-center text-[10px] font-black text-slate-300 uppercase tracking-[0.5em]">No Active Deployments Found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- SYSTEM LOGIC FOOTER PANEL -->
            <div class="bg-[#f8fafc] border-t border-slate-200 px-6 py-3 flex items-center justify-between print:hidden">
                <div class="flex items-center gap-3">
                    <span class="text-[9px] font-black text-slate-400 uppercase italic tracking-[0.15em]">SYSTEM LOGIC: JADWAL_RESOURCE_V1</span>
                    <div class="flex items-center gap-2 ml-4">
                        <span class="relative flex h-1.5 w-1.5">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-1.5 w-1.5 bg-emerald-500"></span>
                        </span>
                        <span class="text-[9px] font-black text-emerald-600 uppercase tracking-widest">TERMINAL ACTIVE</span>
                    </div>
                </div>
                <div class="text-[9px] font-black text-slate-400 uppercase tracking-widest flex items-center gap-2">
                    <span class="opacity-80">PT MILI TALENTA INSPIRASI</span>
                    <span class="text-slate-300">|</span>
                    <span class="opacity-80">2026</span>
                </div>
            </div>
        </div>
    </div>

    <!-- ENGINE DELETE FORM CONTEXT -->
    <form id="globalDeleteForm" method="POST" class="hidden">
        @csrf
        @method('DELETE')
    </form>

    <!-- STRUCTURAL DELETE MODAL -->
    <div id="modalDeleteJadwal" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-[200] hidden flex items-center justify-center p-4">
        <div class="bg-white w-full max-w-sm rounded-none shadow-xl border border-slate-200 overflow-hidden">
            <div class="px-5 py-3 border-b border-rose-100 bg-rose-50/50 flex justify-between items-center">
                <div class="flex items-center gap-2 text-rose-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                    <h3 class="text-[9px] font-black uppercase tracking-widest">Structural Deletion</h3>
                </div>
                <button onclick="closeDeleteModal()" class="text-slate-400 hover:text-slate-600 font-bold text-xl leading-none border-none bg-transparent cursor-pointer">&times;</button>
            </div>
            <div class="p-5 space-y-4">
                <p class="text-[10px] text-slate-500 font-bold uppercase tracking-wide leading-relaxed">Are you sure you want to permanently delete this record? This action cannot be reverted.</p>
                <div class="bg-slate-50 border border-slate-200 p-3 rounded-sm">
                    <span class="text-[8px] font-black text-slate-400 block uppercase tracking-widest mb-1">Target Activity:</span>
                    <span id="deleteTargetTitle" class="text-[10px] font-black text-slate-700 uppercase tracking-tight block truncate">Activity Name</span>
                </div>
            </div>
            <div class="p-4 bg-slate-50 border-t border-slate-100 flex gap-2">
                <button type="button" onclick="closeDeleteModal()" class="w-1/3 border border-slate-200 bg-white hover:bg-slate-50 text-slate-400 py-2 rounded-none text-[9px] font-black uppercase tracking-widest transition-colors text-center cursor-pointer">ABORT</button>
                <button id="btnConfirmDelete" type="button" class="w-2/3 bg-rose-600 hover:bg-rose-700 text-white py-2 rounded-none text-[9px] font-black uppercase tracking-widest transition-colors shadow-sm border-none cursor-pointer">CONFIRM DELETION</button>
            </div>
        </div>
    </div>

    @include('jadwal.create-jadwal')
    @include('jadwal.edit-jadwal')

    <!-- ASSET LOADING VIA VITE -->
    @push('scripts')
        @vite('resources/js/training-schedule.js')
    @endpush
</x-app-layout>