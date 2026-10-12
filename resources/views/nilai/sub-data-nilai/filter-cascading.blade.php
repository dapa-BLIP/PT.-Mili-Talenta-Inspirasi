@php
    if (!isset($programs)) {
        $kategoriPrograms = \App\Models\KategoriPelatihan::orderBy('nama_kategori', 'asc')->pluck('nama_kategori')->toArray();
        $pelatihanPrograms = \App\Models\Pelatihan::whereNotNull('jenis_pelatihan')->where('jenis_pelatihan', '!=', '')->distinct()->pluck('jenis_pelatihan')->toArray();
        $programs = array_values(array_unique(array_filter(array_merge($kategoriPrograms, $pelatihanPrograms))));
        sort($programs);
    }
@endphp

<!-- 1. KOTAK FILTER PARAMETER KELAS PELATIHAN (3 TAHAP: PROGRAM -> MENTOR -> KELAS) -->
<div class="bg-white border border-slate-200 p-4 rounded-xs shadow-xs print:hidden select-none">
    <div class="border-b border-slate-100 pb-2.5 mb-3 flex flex-col sm:flex-row sm:items-center justify-between gap-1.5">
        <div class="flex items-center gap-2">
            <h3 class="text-xs font-black text-slate-800 uppercase tracking-[0.2em] italic">Filter Penilaian</h3>
            <span class="text-[8.5px] font-black text-[#22a0f6] bg-blue-50 border border-blue-100 px-2 py-0.5 uppercase tracking-wider rounded-xs">3-Step Filter</span>
        </div>
        <span id="filter-status-info" class="text-[9.5px] font-bold text-amber-600 uppercase tracking-wider">
            ● 1. Silakan Pilih Program Pelatihan
        </span>
    </div>
    
    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
        <!-- TAHAP 1: PILIH PROGRAM PELATIHAN (MODUL) -->
        <div>
            <label class="block text-[9.5px] font-black text-slate-700 uppercase tracking-wider mb-1">
                Program Pelatihan <span class="text-rose-500">*</span>
            </label>
            <select id="filter-program" name="program_pelatihan" required
                    onchange="handleProgramChange()"
                    class="w-full text-xs font-bold text-slate-800 bg-white border border-slate-200 px-2.5 py-1.5 rounded-xs focus:bg-white focus:outline-none focus:border-[#22a0f6] shadow-2xs transition-all cursor-pointer">
                <option value="">- PILIH PROGRAM PELATIHAN -</option>
                @foreach($programs as $prog)
                    <option value="{{ $prog }}">{{ strtoupper($prog) }}</option>
                @endforeach
            </select>
        </div>

        <!-- TAHAP 2: PILIH MENTOR PENGAMPU -->
        <div>
            <label class="block text-[9.5px] font-black text-slate-700 uppercase tracking-wider mb-1">
                Mentor Pelatihan <span class="text-rose-500">*</span>
            </label>
            <select id="filter-mentor" name="trainer_id_select" disabled required
                    onchange="handleMentorChange()"
                    class="w-full text-xs font-bold text-slate-400 bg-slate-50 border border-slate-200 px-2.5 py-1.5 rounded-xs cursor-not-allowed shadow-2xs transition-all">
                <option value="">- PILIH PROGRAM DULU -</option>
            </select>
            <input type="hidden" id="filter-dosen" name="trainer_id">
        </div>

        <!-- TAHAP 3: PILIH KELAS PELATIHAN -->
        <div>
            <label class="block text-[9.5px] font-black text-slate-700 uppercase tracking-wider mb-1">
                Kelas Pelatihan <span class="text-rose-500">*</span>
            </label>
            <select id="filter-pelatihan" name="pelatihan_id_select" disabled required
                    onchange="handlePelatihanChange()"
                    class="w-full text-xs font-bold text-slate-400 bg-slate-50 border border-slate-200 px-2.5 py-1.5 rounded-xs cursor-not-allowed shadow-2xs transition-all">
                <option value="">- PILIH MENTOR DULU -</option>
            </select>
        </div>
    </div>

    <!-- BARIS INFORMASI & KOMPONEN PENILAIAN -->
    <div class="mt-3 pt-2.5 border-t border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
        <div class="flex items-center gap-2">
            <span class="text-[9px] font-black text-slate-500 uppercase tracking-wider">Komponen Penilaian:</span>
            <div id="komponen-badges-container" class="flex flex-wrap items-center gap-1 min-h-[22px]">
                <span id="komponen-placeholder" class="text-[9px] text-slate-400 italic">Pilih kelas untuk memuat komponen</span>
            </div>
        </div>
        <div id="filter-kelas-info" class="text-[9.5px] font-black text-[#22a0f6] uppercase tracking-wider hidden sm:block">
            <!-- Info Kelas & Mentor Terpilih -->
        </div>
    </div>
</div>

