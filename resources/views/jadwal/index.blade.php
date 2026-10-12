<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between w-full print:hidden select-none gap-3 sm:gap-4">
            <div class="flex items-center gap-2">
                <h2 class="text-xs font-black text-slate-800 uppercase tracking-[0.2em]">Jadwal Schedule</h2>
                <span class="text-slate-300 text-xs">/</span>
                <span class="text-[10px] font-bold text-[#22a0f6] uppercase tracking-widest italic">Monitoring View</span>
            </div>
            
            <div class="flex items-center justify-between sm:justify-end gap-3 sm:gap-4">
                <div class="flex flex-col items-start sm:items-end sm:border-r sm:border-slate-200 sm:pr-4">
                    <span class="text-[8px] font-black uppercase tracking-widest text-slate-400">WAKTU INDONESIA (WIB)</span>
                    <span id="realtime-clock" class="text-[13px] sm:text-[14px] font-black text-slate-800 tracking-wider font-mono">00:00:00</span>
                </div>

                <button type="button" onclick="openCreateModal()" class="bg-[#22a0f6] hover:bg-blue-600 text-white px-3.5 sm:px-4 py-1.5 rounded-sm text-[9px] font-black uppercase tracking-widest transition-all flex items-center gap-1.5 shadow-sm border-none cursor-pointer shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                    </svg>
                    Tambah jadwal
                </button>
            </div>
        </div>
    </x-slot>

    <div class="print:py-0 print:px-0 py-4 max-w-[1600px] mx-auto px-3 sm:px-4 space-y-4">
        
        <!-- ALERT NOTIFICATIONS -->
        @if(session('success'))
        <div class="px-4 py-3 bg-emerald-50 border border-emerald-200 rounded-sm text-emerald-700 shadow-sm flex items-center justify-between print:hidden">
            <div class="flex items-center gap-2">
                <span class="text-[9px] font-black uppercase tracking-widest bg-emerald-500 text-white px-1.5 py-0.5 rounded-sm">Success</span>
                <span class="text-[10px] font-bold uppercase tracking-wide">{{ session('success') }}</span>
            </div>
            <button type="button" class="text-emerald-400 hover:text-emerald-600 font-bold text-sm leading-none border-none bg-transparent cursor-pointer" onclick="this.parentElement.remove()">&times;</button>
        </div>
        @endif

        @if($errors->any())
        <div class="px-4 py-3 bg-rose-50 border border-rose-200 rounded-sm text-rose-800 shadow-sm flex items-center justify-between print:hidden">
            <div class="space-y-1">
                <div class="flex items-center gap-2">
                    <span class="text-[9px] font-black uppercase tracking-widest bg-rose-600 text-white px-1.5 py-0.5 rounded-sm">Error</span>
                    <span class="text-[10px] font-bold uppercase tracking-wide">Terjadi Kesalahan:</span>
                </div>
                <ul class="list-disc list-inside text-[11px] font-medium text-rose-700 mt-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            <button type="button" class="text-rose-400 hover:text-rose-600 font-bold text-sm leading-none border-none bg-transparent cursor-pointer" onclick="this.parentElement.remove()">&times;</button>
        </div>
        @endif

        <!-- FILTER TOOLBAR PANEL -->
        @include('jadwal.sub-data-jadwal.filter-jadwal')
        
        <!-- DATA TABLE MONITOR MATRIX -->
        @include('jadwal.sub-data-jadwal.table-jadwal')

    </div>

    <!-- ENGINE DELETE FORM CONTEXT -->
    <form id="globalDeleteForm" method="POST" class="hidden">
        @csrf
        @method('DELETE')
    </form>

    <!-- INCLUDE MODAL & SCRIPTS MODULAR DARI SUB-DATA-JADWAL -->
    @include('jadwal.sub-data-jadwal.create-jadwal')
    @include('jadwal.sub-data-jadwal.edit-jadwal')
    @include('jadwal.sub-data-jadwal.detail-jadwal')
    @include('jadwal.sub-data-jadwal.delate-jadwal')
    @include('jadwal.sub-data-jadwal.script-jadwal')

</x-app-layout>