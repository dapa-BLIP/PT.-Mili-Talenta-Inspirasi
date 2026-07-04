<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between w-full h-full print:hidden select-none">
            <div class="flex items-center gap-2">
                <h2 class="text-sm font-black text-slate-700 uppercase tracking-tighter">Master Academic</h2>
                <span class="text-slate-300 text-xs">/</span>
                <span class="text-[10px] font-bold text-[#22a0f6] uppercase tracking-widest italic">Academic Master Hub</span>
            </div>
            
            <div class="grid grid-cols-2 bg-slate-100 border border-slate-200 p-0.5 w-72 shrink-0 rounded-sm shadow-inner overflow-hidden">
                <button @click="$dispatch('change-academic-tab', 'pelatihan')" 
                        :style="currentTab === 'pelatihan' ? 'background-color: #22a0f6; color: white;' : ''"
                        onmouseenter="if(Alpine.store('academic_active_tab') !== 'pelatihan') { this.style.backgroundColor='#22a0f6'; this.style.color='white'; }"
                        onmouseleave="if(!this.getAttribute('class').includes('text-white')) { this.style.backgroundColor='transparent'; this.style.color='#64748b'; }"
                        :class="currentTab === 'pelatihan' ? 'text-white font-black shadow-sm border-b-2 border-[#1d8cd3]' : 'text-slate-500 font-bold'" 
                        class="py-1.5 text-[10px] font-black uppercase tracking-wider transition-all duration-150 rounded-sm focus:outline-none border-none cursor-pointer flex items-center justify-center gap-1.5">
                        DATA PELATIHAN
                </button>
                
                <button @click="$dispatch('change-academic-tab', 'mentor')" 
                        :style="currentTab === 'mentor' ? 'background-color: #047857; color: white;' : ''"
                        onmouseenter="if(Alpine.store('academic_active_tab') !== 'mentor') { this.style.backgroundColor='#047857'; this.style.color='white'; }"
                        onmouseleave="if(!this.getAttribute('class').includes('text-white')) { this.style.backgroundColor='transparent'; this.style.color='#64748b'; }"
                        :class="currentTab === 'mentor' ? 'text-white font-black shadow-sm border-b-2 border-[#035f44]' : 'text-slate-500 font-bold'" 
                        class="py-1.5 text-[10px] font-black uppercase tracking-wider transition-all duration-150 rounded-sm focus:outline-none border-none cursor-pointer flex items-center justify-center gap-1.5">
                        DATA MENTOR
                </button>
            </div>
        </div>
    </x-slot>

    <div class="py-4 max-w-[1400px] mx-auto px-1" 
         x-data="{ currentTab: '{{ session('active_tab', 'pelatihan') }}' }" 
         @change-academic-tab.window="currentTab = $event.detail; document.getElementById('academic-search-bar').value=''; jalankanLiveSearch();">
        
        @if(session('success'))
            <div class="mb-4 px-4 py-2.5 bg-emerald-50 border border-emerald-200 text-emerald-700 text-[10px] font-black uppercase tracking-wide flex justify-between items-center shadow-sm select-none rounded-none">
                <span>✅ {{ session('success') }}</span>
                <button type="button" class="font-bold text-sm leading-none" onclick="this.parentElement.remove()">&times;</button>
            </div>
        @endif

        <div class="bg-white border border-slate-200 py-3 px-4 mb-4 shadow-sm rounded-none">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                
                <div class="shrink-0 min-w-[200px] flex items-center">
                    <h3 x-show="currentTab === 'pelatihan'" class="text-[11px] font-black text-slate-700 uppercase tracking-widest">
                        Repositori Kelas Pelatihan
                    </h3>
                    <h3 x-show="currentTab === 'mentor'" class="text-[11px] font-black text-slate-700 uppercase tracking-widest" x-cloak>
                        Database Master Mentor
                    </h3>
                </div>

                <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 justify-end md:flex-1">
                    <div class="relative w-full sm:w-60">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-2.5 pointer-events-none">
                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="3" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </span>
                        <input type="text" 
                               id="academic-search-bar"
                               onkeyup="jalankanLiveSearch()"
                               placeholder="Ketik kata kunci..." 
                               class="w-full pl-8 pr-3 py-1.5 bg-slate-50 border border-slate-200 text-[10px] font-bold uppercase tracking-wider text-slate-700 rounded-none focus:outline-none focus:border-[#22a0f6] focus:bg-white placeholder-slate-400 shadow-inner">
                    </div>

                    <div x-show="currentTab === 'pelatihan'" class="flex flex-wrap items-center gap-2">
                        <button type="button" onclick="document.getElementById('modalImportPelatihan').classList.remove('hidden')" 
                                class="px-4 py-1.5 bg-emerald-600 border border-emerald-700 text-white rounded-sm text-[10px] font-black hover:bg-emerald-700 transition uppercase tracking-widest shadow-sm flex items-center justify-center gap-1 cursor-pointer">
                            Import Excel
                        </button>
                        <a href="{{ route('academic.exportPelatihan') }}" 
                           class="px-4 py-1.5 bg-slate-700 border border-slate-800 text-white rounded-sm text-[10px] font-black hover:bg-slate-800 transition uppercase tracking-widest shadow-sm flex items-center justify-center">
                            Export Excel
                        </a>
                        <button type="button" onclick="document.getElementById('modalCreatePelatihan').classList.remove('hidden')" 
                                class="px-4 py-1.5 bg-[#22a0f6] text-white rounded-sm text-[10px] font-black uppercase tracking-widest hover:bg-blue-600 transition shadow-md cursor-pointer border-none">
                            + Add New Entry
                        </button>
                    </div>

                    <div x-show="currentTab === 'mentor'" class="flex flex-wrap items-center gap-2" x-cloak>
                        <button type="button" onclick="document.getElementById('modalImportMentor').classList.remove('hidden')" 
                                class="px-4 py-1.5 bg-emerald-600 border border-emerald-700 text-white rounded-sm text-[10px] font-black hover:bg-emerald-700 transition uppercase tracking-widest shadow-sm flex items-center justify-center gap-1 cursor-pointer">
                            Import Excel
                        </button>
                        <a href="{{ route('academic.exportMentor') }}" 
                           class="px-4 py-1.5 bg-slate-700 border border-slate-800 text-white rounded-sm text-[10px] font-black hover:bg-slate-800 transition uppercase tracking-widest shadow-sm flex items-center justify-center">
                            Export Excel
                        </a>
                        <button type="button" onclick="document.getElementById('modalCreateTrainer').classList.remove('hidden')" 
                                class="px-4 py-1.5 bg-[#22a0f6] text-white rounded-sm text-[10px] font-black uppercase tracking-widest hover:bg-blue-600 transition shadow-md cursor-pointer border-none">
                            + Add New Entry
                        </button>
                    </div>
                </div>

            </div>
        </div>

        <div x-show="currentTab === 'pelatihan'" x-cloak>
            <div class="bg-white border border-slate-200 shadow-sm overflow-x-auto">
                <table class="w-full text-left border-collapse whitespace-nowrap" id="table-pelatihan-master">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200">
                            <th class="px-6 py-3.5 text-[10px] font-black text-slate-400 uppercase w-16 text-center">No.</th>
                            <th class="px-6 py-3.5 text-[10px] font-black text-slate-400 uppercase min-w-[280px]">Nama Program Pelatihan</th>
                            <th class="px-6 py-3.5 text-[10px] font-black text-slate-400 uppercase min-w-[220px]">Mentor Pengampu</th>
                            <th class="px-6 py-3.5 text-[10px] font-black text-slate-400 uppercase min-w-[320px]">Komponen Penilaian</th>
                            <th class="px-6 py-3.5 text-[10px] font-black text-slate-400 uppercase min-w-[180px]">Kategori Spektrum</th>
                            <th class="px-6 py-3.5 text-[10px] font-black text-slate-400 uppercase text-right w-24 pr-6">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($pelatihans as $index => $p)
                        <tr class="hover:bg-slate-50/60 transition-colors row-data-search">
                            <td class="px-6 py-3.5 text-[11px] font-black text-slate-400 text-center index-number">{{ str_pad($index + 1, 3, '0', STR_PAD_LEFT) }}</td>
                            <td class="px-6 py-3.5 text-[12px] font-bold text-slate-700 uppercase searchable-field">{{ $p->nama_pelatihan }}</td>
                            <td class="px-6 py-3.5 text-[11px] font-bold text-slate-600 uppercase searchable-field">{{ $p->mentor->nama_mentor ?? 'Belum Ditunjuk' }}</td>
                            <td class="px-6 py-3.5">
                                <div class="flex flex-wrap gap-1 max-w-[400px]">
                                    @foreach($p->komponen_nilai ?? [] as $komp)
                                        <span class="text-[8px] font-black bg-slate-100 border border-slate-200 px-1.5 py-0.5 text-slate-600 rounded-sm uppercase tracking-tight">{{ str_replace('_', ' ', $komp) }}</span>
                                    @endforeach
                                </div>
                            </td>
                            <td class="px-6 py-3.5">
                                <span class="text-[9px] font-black px-2 py-0.5 rounded-sm border uppercase tracking-wide searchable-field {{ $p->spektrum_kategori == 'Pengetahuan Teknis' ? 'bg-blue-50/80 border-blue-200 text-blue-600' : 'bg-white border-slate-200 text-slate-600 shadow-sm' }}">
                                    {{ $p->spektrum_kategori }}
                                </span>
                            </td>
                            <td class="px-6 py-3.5 text-right pr-6">
                                <div class="flex justify-end gap-1 items-center">
                                    <button type="button" onclick="bukaModalEditPelatihan({{ json_encode($p) }}, '{{ implode(', ', $p->komponen_nilai ?? []) }}')" class="p-1.5 border border-slate-200 text-[#22a0f6] hover:bg-[#22a0f6] hover:text-white transition-colors cursor-pointer">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    </button>
                                    <button type="button" onclick="mintaVerifikasiHapus('pelatihan', {{ $p->id }}, '{{ $p->nama_pelatihan }}')" class="p-1.5 border border-slate-200 text-rose-500 hover:bg-rose-500 hover:text-white transition-colors cursor-pointer">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke-width="2.5"></path></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr class="row-kosong"><td colspan="6" class="px-6 py-8 text-center text-[10px] font-black text-slate-400 uppercase tracking-widest italic">Belum ada program pelatihan di-input.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div x-show="currentTab === 'mentor'" x-cloak>
            <div class="bg-white border border-slate-200 shadow-sm overflow-x-auto">
                <table class="w-full text-left border-collapse whitespace-nowrap" id="table-mentor-master">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200">
                            <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase w-16 text-center">No.</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase w-24 text-center">Foto</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase min-w-[220px]">Nama Lengkap Mentor</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase min-w-[160px]">NIDN / NIK</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase min-w-[220px]">Tempat, Tanggal Lahir</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase min-w-[180px]">Sektor Keahlian</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase w-24 text-center">Status</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase text-right w-24 pr-6">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($trainers as $index => $t)
                        <tr class="hover:bg-slate-50 transition-colors row-data-search">
                            <td class="px-6 py-4 text-[11px] font-black text-slate-400 text-center index-number">{{ str_pad($index + 1, 3, '0', STR_PAD_LEFT) }}</td>
                            <td class="px-6 py-3 text-center">
                                <div class="w-9 h-9 mx-auto bg-slate-100 border border-slate-200 flex items-center justify-center font-black text-[8px] overflow-hidden shadow-sm">
                                    @if($t->foto) <img src="{{ asset('storage/'.$t->foto) }}" class="object-cover w-full h-full">
                                    @else <span class="text-slate-400 font-bold">NO_IMG</span> @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 text-[12px] font-black text-slate-700 uppercase truncate searchable-field">{{ $t->nama_mentor }}</td>
                            <td class="px-6 py-4 text-[11px] font-bold text-slate-600 tracking-wider uppercase searchable-field">{{ $t->nidn ?? '-' }}</td>
                            <td class="px-6 py-4 text-[11px] font-bold text-slate-500 uppercase truncate searchable-field">{{ $t->ttl ?? '-' }}</td>
                            <td class="px-6 py-4 text-[9px] font-black uppercase searchable-field">
                                <span class="px-2 py-0.5 rounded-sm border uppercase tracking-wide {{ $t->sektor_keahlian == 'Pengetahuan Teknis' ? 'bg-blue-50/80 border-blue-200 text-blue-600' : 'bg-white border-slate-200 text-slate-600 shadow-sm' }}">
                                    {{ $t->sektor_keahlian }}
                                endforeach
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="text-[8px] font-black px-1.5 py-0.5 rounded-sm {{ $t->status == 'AKTIF' ? 'bg-emerald-50 text-emerald-600 border border-emerald-200' : 'bg-rose-50 text-rose-600 border border-rose-200' }}">
                                    {{ $t->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right pr-6">
                                <div class="flex justify-end gap-1 items-center">
                                    <button type="button" onclick="bukaModalEditMentor({{ json_encode($t) }})" class="p-1.5 border border-slate-200 text-[#22a0f6] hover:bg-[#22a0f6] hover:text-white transition-colors cursor-pointer">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    </button>
                                    <button type="button" onclick="mintaVerifikasiHapus('mentor', {{ $t->id }}, '{{ $t->nama_mentor }}')" class="p-1.5 border border-slate-200 text-rose-500 hover:bg-rose-500 hover:text-white transition-colors cursor-pointer">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke-width="2.5"></path></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr class="row-kosong"><td colspan="8" class="px-6 py-8 text-center text-[10px] font-black text-slate-400 uppercase tracking-widest italic">Belum ada mentor pengampu terdaftar.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div id="modalSecureDelete" class="fixed inset-0 bg-slate-950/70 backdrop-blur-sm z-[200] hidden flex items-center justify-center p-4">
        <div class="bg-white w-full max-w-md shadow-2xl border border-slate-200 overflow-hidden rounded-none">
            <div class="px-5 py-3.5 border-b border-rose-100 bg-rose-50/40 flex items-center gap-2">
                <span class="text-rose-500 font-bold text-sm">⚠️</span>
                <h4 class="text-[10px] font-black text-rose-700 uppercase tracking-widest">CRITICAL RELATIONAL DELETION</h4>
            </div>
            <form id="formSecureDelete" method="POST" class="p-5 space-y-3.5">
                @csrf @method('DELETE')
                <p class="text-[11px] font-bold text-slate-500 uppercase tracking-wide leading-relaxed">
                    Tindakan ini permanen! Menghapus data dapat memutus interelasi nilai kompetensi JSON peserta di database.
                </p>
                <div class="bg-slate-50 border border-slate-200 p-2.5 text-center select-none">
                    <p id="delete-target-name" class="text-xs font-black text-slate-800 tracking-tight uppercase"></p>
                </div>
                <input type="text" id="input-delete-verify" onkeyup="evaluasiTombolHapus()" placeholder="Ketik kembali nama di atas..." class="w-full bg-white border border-slate-300 rounded-none px-3 py-2 text-[10px] font-black text-slate-800 focus:outline-none focus:border-rose-500 uppercase tracking-wider">
                <div class="flex gap-2">
                    <button type="button" onclick="tutupSecureDeleteModal()" class="w-1/3 border border-slate-200 text-slate-400 py-2 text-[9px] font-black uppercase">BATAL</button>
                    <button type="submit" id="btn-confirm-delete-final" disabled class="w-2/3 bg-slate-200 text-slate-400 py-2 text-[9px] font-black uppercase tracking-widest cursor-not-allowed">CONFIRM & DELETE</button>
                </div>
            </form>
        </div>
    </div>

    <div id="modalImportPelatihan" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-[100] hidden flex items-center justify-center p-4">
        <div class="bg-white w-full max-w-sm shadow-xl border border-slate-200 overflow-hidden rounded-none">
            <div class="px-5 py-3 border-b border-slate-100 bg-slate-50 flex justify-between items-center"><h3 class="text-[10px] font-black text-slate-800 uppercase tracking-widest">IMPORT PELATIHAN</h3><button type="button" onclick="document.getElementById('modalImportPelatihan').classList.add('hidden')" class="text-slate-400 text-lg">&times;</button></div>
            <form action="{{ route('academic.importPelatihan') }}" method="POST" enctype="multipart/form-data" class="p-5 space-y-4">@csrf
                <div><div class="border border-dashed border-slate-200 bg-slate-50 p-4 flex flex-col items-center justify-center relative cursor-pointer"><span id="lbl_imp_p" class="text-[10px] font-semibold text-slate-400">Pilih berkas Excel...</span><input type="file" name="file_excel" required accept=".xlsx, .xls" onchange="document.getElementById('lbl_imp_p').innerText = this.files[0].name" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"></div></div>
                <div class="pt-2 flex gap-2"><button type="button" onclick="document.getElementById('modalImportPelatihan').classList.add('hidden')" class="w-1/3 border border-slate-200 text-[9px] font-black uppercase py-2">CANCEL</button><button type="submit" class="w-2/3 bg-emerald-700 text-white text-[9px] font-black uppercase py-2">UPLOAD MASTER</button></div>
            </form>
        </div>
    </div>

    <div id="modalImportMentor" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-[100] hidden flex items-center justify-center p-4">
        <div class="bg-white w-full max-w-sm shadow-xl border border-slate-200 overflow-hidden rounded-none">
            <div class="px-5 py-3 border-b border-slate-100 bg-slate-50 flex justify-between items-center"><h3 class="text-[10px] font-black text-slate-800 uppercase tracking-widest">IMPORT MENTOR</h3><button type="button" onclick="document.getElementById('modalImportMentor').classList.add('hidden')" class="text-slate-400 text-lg">&times;</button></div>
            <form action="{{ route('academic.importMentor') }}" method="POST" enctype="multipart/form-data" class="p-5 space-y-4">@csrf
                <div><div class="border border-dashed border-slate-200 bg-slate-50 p-4 flex flex-col items-center justify-center relative cursor-pointer"><span id="lbl_imp_m" class="text-[10px] font-semibold text-slate-400">Pilih berkas Excel...</span><input type="file" name="file_excel" required accept=".xlsx, .xls" onchange="document.getElementById('lbl_imp_m').innerText = this.files[0].name" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"></div></div>
                <div class="pt-2 flex gap-2"><button type="button" onclick="document.getElementById('modalImportMentor').classList.add('hidden')" class="w-1/3 border border-slate-200 text-[9px] font-black uppercase py-2">CANCEL</button><button type="submit" class="w-2/3 bg-emerald-700 text-white text-[9px] font-black uppercase py-2">UPLOAD MASTER</button></div>
            </form>
        </div>
    </div>

    <div id="modalCreatePelatihan" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-[100] hidden flex items-center justify-center p-4">
        <div class="bg-white w-full max-w-md shadow-xl border border-slate-200 overflow-hidden rounded-none">
            <div class="px-5 py-3 border-b border-slate-100 bg-slate-50 flex justify-between items-center"><h3 class="text-[10px] font-black text-slate-800 uppercase tracking-widest">BUAT MASTER PELATIHAN</h3><button type="button" onclick="document.getElementById('modalCreatePelatihan').classList.add('hidden')" class="text-slate-400 hover:text-rose-500 text-lg leading-none">&times;</button></div>
            <form action="{{ route('academic.storePelatihan') }}" method="POST" class="p-5 space-y-4">@csrf
                <div><label class="block text-[8px] font-black uppercase tracking-widest text-slate-400 mb-1">Nama Kelas Pelatihan</label><input type="text" name="nama_pelatihan" class="w-full bg-slate-50 border border-slate-200 rounded-none px-2.5 py-1.5 text-[11px] font-medium text-slate-800 focus:outline-none" required placeholder="Panen Kelapa Sawit"></div>
                <div><label class="block text-[8px] font-black uppercase tracking-widest text-slate-400 mb-1">Kategori Spektrum</label><select name="spektrum_kategori" class="w-full bg-slate-50 border border-slate-200 rounded-none px-2 py-1.5 text-[10px] font-bold text-slate-600 focus:outline-none" required><option value="Pengetahuan Teknis">PENGETAHUAN TEKNIS</option><option value="Pengetahuan Non Teknis">PENGETAHUAN NON TEKNIS</option></select></div>
                <div><label class="block text-[8px] font-black uppercase tracking-widest text-slate-400 mb-1">Pilih Mentor Pengampu</label><select name="trainer_id" class="w-full bg-slate-50 border border-slate-200 rounded-none px-2 py-1.5 text-[10px] font-bold text-slate-600 uppercase focus:outline-none" required><option value="">-- CHOOSE MENTOR --</option>@foreach($trainers as $t)<option value="{{ $t->id }}">{{ $t->nama_mentor }}</option>@endforeach</select></div>
                <div><label class="block text-[8px] font-black uppercase tracking-widest text-slate-400 mb-1">Komponen Bobot Nilai</label><input type="text" name="raw_komponen" class="w-full bg-slate-50 border border-slate-200 rounded-none px-2.5 py-1.5 text-[11px] font-medium text-slate-800 focus:outline-none" required placeholder="Tugas, UTS, UAS, Praktik"></div>
                <div class="pt-2 flex gap-2"><button type="button" onclick="document.getElementById('modalCreatePelatihan').classList.add('hidden')" class="w-1/3 border border-slate-200 text-[9px] font-black uppercase py-2">CANCEL</button><button type="submit" class="w-2/3 bg-slate-900 text-white text-[9px] font-black uppercase py-2">COMMIT TO MASTER</button></div>
            </form>
        </div>
    </div>

    <div id="modalEditPelatihan" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-[100] hidden flex items-center justify-center p-4">
        <div class="bg-white w-full max-w-md shadow-xl border border-slate-200 overflow-hidden rounded-none">
            <div class="px-5 py-3 border-b border-slate-100 bg-slate-50 flex justify-between items-center"><h3 class="text-[10px] font-black text-slate-800 uppercase tracking-widest">EDIT PROGRAM PELATIHAN</h3><button type="button" onclick="document.getElementById('modalEditPelatihan').classList.add('hidden')" class="text-slate-400 hover:text-rose-500 text-lg leading-none">&times;</button></div>
            <form id="formEditPelatihan" method="POST" class="p-5 space-y-4">@csrf @method('PUT')
                <div><input type="text" id="edit_nama_pelatihan" name="nama_pelatihan" class="w-full bg-slate-50 border border-slate-200 rounded-none px-2.5 py-1.5 text-[11px] font-medium text-slate-800 focus:outline-none" required></div>
                <div><select id="edit_spektrum_kategori" name="spektrum_kategori" class="w-full bg-slate-50 border border-slate-200 rounded-none px-2 py-1.5 text-[10px] font-bold text-slate-600 focus:outline-none" required><option value="Pengetahuan Teknis">PENGETAHUAN TEKNIS</option><option value="Pengetahuan Non Teknis">PENGETAHUAN NON TEKNIS</option></select></div>
                <div><select id="edit_trainer_id" name="trainer_id" class="w-full bg-slate-50 border border-slate-200 rounded-none px-2 py-1.5 text-[10px] font-bold text-slate-600 uppercase focus:outline-none" required>@foreach($trainers as $t)<option value="{{ $t->id }}">{{ $t->nama_mentor }}</option>@endforeach</select></div>
                <div><input type="text" id="edit_raw_komponen" name="raw_komponen" class="w-full bg-slate-50 border border-slate-200 rounded-none px-2.5 py-1.5 text-[11px] font-medium text-slate-800 focus:outline-none" required></div>
                <div class="pt-2 flex gap-2"><button type="button" onclick="document.getElementById('modalEditPelatihan').classList.add('hidden')" class="w-1/3 border border-slate-200 text-[9px] font-black uppercase py-2">CANCEL</button><button type="submit" class="w-2/3 bg-slate-900 text-white text-[9px] font-black uppercase py-2">UPDATE CONFIG</button></div>
            </form>
        </div>
    </div>

    <div id="modalCreateTrainer" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-[100] hidden flex items-center justify-center p-4">
        <div class="bg-white w-full max-w-sm shadow-xl border border-slate-200 overflow-hidden rounded-none">
            <div class="px-5 py-3 border-b border-slate-100 bg-slate-50 flex justify-between items-center"><h3 class="text-[10px] font-black text-slate-800 uppercase tracking-widest">REGISTRASI MENTOR</h3><button type="button" onclick="document.getElementById('modalCreateTrainer').classList.add('hidden')" class="text-slate-400 hover:text-rose-500 text-lg leading-none">&times;</button></div>
            <form action="{{ route('academic.storeTrainer') }}" method="POST" enctype="multipart/form-data" class="p-5 space-y-3.5">@csrf
                <div class="grid grid-cols-3 gap-2"><div class="col-span-2"><input type="text" name="nama_mentor" class="w-full bg-slate-50 border border-slate-200 rounded-none px-2.5 py-1.5 text-[11px] font-medium text-slate-800 focus:outline-none" required placeholder="Nama & Gelar"></div><div><input type="text" name="nidn" class="w-full bg-slate-50 border border-slate-200 rounded-none px-2 py-1.5 text-[11px] font-medium text-slate-800" placeholder="NIDN"></div></div>
                <div class="grid grid-cols-2 gap-2"><div><input type="text" name="tempat_lahir" class="w-full bg-slate-50 border border-slate-200 rounded-none px-2.5 py-1.5 text-[11px] font-medium text-slate-800" required placeholder="Tempat Lahir"></div><div><input type="date" name="tanggal_lahir" class="w-full bg-slate-50 border border-slate-200 rounded-none px-2.5 py-1.5 text-[11px] font-medium text-slate-700" required></div></div>
                <div class="grid grid-cols-2 gap-2"><div><input type="email" name="email" class="w-full bg-slate-50 border border-slate-200 text-[11px] font-medium px-2.5 py-1.5" placeholder="Email"></div><div><input type="text" name="kontak" class="w-full bg-slate-50 border border-slate-200 text-[11px] font-medium px-2.5 py-1.5" placeholder="HP"></div></div>
                <div><select name="sektor_keahlian" class="w-full bg-slate-50 border border-slate-200 px-2 py-1.5 text-[10px] font-bold text-slate-600" required><option value="Pengetahuan Teknis">PENGETAHUAN TEKNIS</option><option value="Pengetahuan Non Teknis">PENGETAHUAN NON TEKNIS</option></select></div>
                <div><div class="border border-dashed border-slate-200 bg-slate-50 p-2.5 flex flex-col items-center justify-center relative cursor-pointer"><span id="label_foto_mentor" class="text-[10px] font-semibold text-slate-400">Pilih file foto...</span><input type="file" name="foto" onchange="document.getElementById('label_foto_mentor').innerText = this.files[0].name" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"></div></div>
                <div class="pt-2 flex gap-2"><button type="button" onclick="document.getElementById('modalCreateTrainer').classList.add('hidden')" class="w-1/3 border border-slate-200 text-[9px] font-black uppercase py-2">CANCEL</button><button type="submit" class="w-2/3 bg-slate-900 text-white text-[9px] font-black uppercase py-2">REGISTER</button></div>
            </form>
        </div>
    </div>

    <div id="modalEditTrainer" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-[100] hidden flex items-center justify-center p-4">
        <div class="bg-white w-full max-w-sm shadow-xl border border-slate-200 overflow-hidden rounded-none">
            <div class="px-5 py-3 border-b border-slate-100 bg-slate-50 flex justify-between items-center"><h3 class="text-[10px] font-black text-slate-800 uppercase tracking-widest">EDIT PROFIL MENTOR</h3><button type="button" onclick="document.getElementById('modalEditTrainer').classList.add('hidden')" class="text-slate-400 hover:text-rose-500 text-lg leading-none">&times;</button></div>
            <form id="formEditTrainer" method="POST" enctype="multipart/form-data" class="p-5 space-y-3.5">@csrf @method('PUT')
                <div class="grid grid-cols-3 gap-2"><div class="col-span-2"><input type="text" id="edit_nama_mentor" name="nama_mentor" class="w-full bg-slate-50 border border-slate-200 rounded-none px-2.5 py-1.5 text-[11px] font-medium text-slate-800 focus:outline-none" required></div><div><input type="text" id="edit_nidn" name="nidn" class="w-full bg-slate-50 border border-slate-200 rounded-none px-2 py-1.5 text-[11px] font-medium text-slate-800"></div></div>
                <div><input type="text" id="edit_ttl" name="ttl" class="w-full bg-slate-50 border border-slate-200 rounded-none px-2.5 py-1.5 text-[11px] font-medium text-slate-800" required></div>
                <div class="grid grid-cols-2 gap-2"><div><input type="email" id="edit_email" name="email" class="w-full bg-slate-50 border border-slate-200 text-[11px] font-medium px-2.5 py-1.5"></div><div><input type="text" id="edit_kontak" name="kontak" class="w-full bg-slate-50 border border-slate-200 text-[11px] font-medium px-2.5 py-1.5"></div></div>
                <div class="grid grid-cols-2 gap-2"><div><select id="edit_sektor_keahlian" name="sektor_keahlian" class="w-full bg-slate-50 border border-slate-200 text-[10px] font-bold text-slate-600" required><option value="Pengetahuan Teknis">PENGETAHUAN TEKNIS</option><option value="Pengetahuan Non Teknis">PENGETAHUAN NON TEKNIS</option></select></div><div><select id="edit_status" name="status" class="w-full bg-slate-50 border border-slate-200 text-[10px] font-bold text-slate-600" required><option value="AKTIF">AKTIF</option><option value="NON-AKTIF">NON-AKTIF</option></select></div></div>
                <div><div class="border border-dashed border-slate-200 bg-slate-50 p-2.5 flex flex-col items-center justify-center relative cursor-pointer"><span id="label_edit_foto" class="text-[10px] font-semibold text-slate-400">Ubah foto...</span><input type="file" name="foto" onchange="document.getElementById('label_edit_foto').innerText = this.files[0].name" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"></div></div>
                <div class="pt-2 flex gap-2"><button type="button" onclick="document.getElementById('modalEditTrainer').classList.add('hidden')" class="w-1/3 border border-slate-200 text-[9px] font-black uppercase py-2">CANCEL</button><button type="submit" class="w-2/3 bg-slate-900 text-white text-[9px] font-black uppercase py-2">SAVE CHANGES</button></div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        let targetHapusNama = '';

        function jalankanLiveSearch() {
            const inputText = document.getElementById('academic-search-bar').value.trim().toLowerCase();
            const tabAktif = document.querySelector('[x-data]').__x.$data.currentTab;
            const idTabel = tabAktif === 'pelatihan' ? 'table-pelatihan-master' : 'table-mentor-master';
            const rows = document.querySelectorAll(`#${idTabel} .row-data-search`);
            
            let barisTerlihat = 0;

            rows.forEach(row => {
                const fields = row.querySelectorAll('.searchable-field');
                let matches = false;

                fields.forEach(f => {
                    if(f.textContent.toLowerCase().includes(inputText)) {
                        matches = true;
                    }
                });

                if(matches || inputText === '') {
                    row.style.display = "";
                    barisTerlihat++;
                    const indexCol = row.querySelector('.index-number');
                    if(indexCol) indexCol.textContent = String(barisTerlihat).padStart(3, '0');
                } else {
                    row.style.display = "none";
                }
            });
        }

        function mintaVerifikasiHapus(jenis, id, namaTarget) {
            targetHapusNama = namaTarget.trim().toUpperCase();
            
            document.getElementById('formSecureDelete').action = `/academic-master/${jenis}/${id}`;
            document.getElementById('delete-target-name').textContent = targetHapusNama;
            
            document.getElementById('input-delete-verify').value = '';
            const btnFinal = document.getElementById('btn-confirm-delete-final');
            btnFinal.disabled = true;
            btnFinal.className = "w-2/3 bg-slate-200 text-slate-400 py-2 text-[9px] font-black uppercase tracking-widest cursor-not-allowed";

            document.getElementById('modalSecureDelete').classList.remove('hidden');
        }

        function evaluasiTombolHapus() {
            const valUser = document.getElementById('input-delete-verify').value.trim().toUpperCase();
            const btnFinal = document.getElementById('btn-confirm-delete-final');

            if (valUser === targetHapusNama) {
                btnFinal.disabled = false;
                btnFinal.className = "w-2/3 bg-rose-600 text-white py-2 text-[9px] font-black uppercase tracking-widest hover:bg-rose-700 transition-all shadow-md cursor-pointer";
            } else {
                btnFinal.disabled = true;
                btnFinal.className = "w-2/3 bg-slate-200 text-slate-400 py-2 text-[9px] font-black uppercase tracking-widest cursor-not-allowed";
            }
        }

        function tutupSecureDeleteModal() { document.getElementById('modalSecureDelete').classList.add('hidden'); }

        function bukaModalEditPelatihan(data, rawKomponen) {
            document.getElementById('formEditPelatihan').action = `/academic-master/pelatihan/${data.id}`;
            document.getElementById('edit_nama_pelatihan').value = data.nama_pelatihan;
            document.getElementById('edit_spektrum_kategori').value = data.spektrum_kategori;
            document.getElementById('edit_trainer_id').value = data.trainer_id;
            document.getElementById('edit_raw_komponen').value = rawKomponen.replace(/_/g, ' ');
            document.getElementById('modalEditPelatihan').classList.remove('hidden');
        }

        function bukaModalEditMentor(data) {
            document.getElementById('formEditTrainer').action = `/academic-master/mentor/${data.id}`;
            document.getElementById('edit_nama_mentor').value = data.nama_mentor;
            document.getElementById('edit_nidn').value = data.nidn || '';
            document.getElementById('edit_ttl').value = data.ttl || '';
            document.getElementById('edit_email').value = data.email || '';
            document.getElementById('edit_kontak').value = data.kontak || '';
            document.getElementById('edit_sektor_keahlian').value = data.sektor_keahlian;
            document.getElementById('edit_status').value = data.status;
            document.getElementById('label_edit_foto').innerText = "Ubah foto...";
            document.getElementById('modalEditTrainer').classList.remove('hidden');
        }
    </script>
    @endpush
</x-app-layout>