<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between w-full print:hidden select-none">
            <div class="flex items-center gap-2">
                <h2 class="text-xs font-black text-slate-800 uppercase tracking-[0.2em]">Kelola Nilai Kolektif</h2>
                <span class="text-slate-300 text-xs">/</span>
                <span class="text-[10px] font-bold text-[#22a0f6] uppercase tracking-widest italic">Matrix Grading System</span>
            </div>
        </div>
    </x-slot>

    <div class="space-y-4 print:py-0 print:px-0">
        <!-- ALERTS NOTIFIKASI -->
        @include('nilai.sub-data-nilai.alerts')

        <!-- FORM UTAMA MATRIX NILAI -->
        <form id="matrix-form" onsubmit="handleMatrixSubmit(event)" class="space-y-4">
            @csrf
            <input type="hidden" id="form-pelatihan-id" name="pelatihan_id">
            <input type="hidden" id="form-trainer-id" name="trainer_id">

            <!-- 1. FILTER PILIH KELAS PELATIHAN (3 TAHAP: PROGRAM -> MENTOR -> KELAS) -->
            @include('nilai.sub-data-nilai.filter-cascading')

            <!-- 2. TABEL MATRIX INPUT NILAI -->
            @include('nilai.sub-data-nilai.table-nilai')
        </form>
    </div>

    <!-- MODAL & SCRIPT INCLUDES -->
    @include('nilai.sub-data-nilai.modal-warning')
    @include('nilai.sub-data-nilai.script-nilai')
</x-app-layout>
