<!-- ========================================================================= -->
<!-- MODAL: IMPORT KELAS PELATIHAN (EXTRA WIDE MAX-W-2XL) -->
<!-- ========================================================================= -->
<div id="modalImportPelatihan" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-[200] hidden flex items-center justify-center p-4 print:hidden overflow-y-auto">
    
    <!-- Kontainer Diperlebar (max-w-2xl) -->
    <div class="bg-white w-full max-w-2xl shadow-2xl border border-slate-200 rounded-sm overflow-hidden flex flex-col my-auto">
        
        <!-- Header Emerald -->
        <div class="px-6 py-3.5 border-b border-emerald-100 bg-emerald-50 text-emerald-800 flex items-center justify-between shrink-0">
            <div class="flex items-center gap-2">
                <span class="text-[8.5px] font-black uppercase tracking-widest bg-emerald-600 text-white px-2 py-0.5 rounded-xs">Spreadsheet</span>
                <h3 class="text-xs font-black uppercase tracking-wider text-emerald-950">IMPORT KELAS PELATIHAN</h3>
            </div>
            <button type="button" onclick="document.getElementById('modalImportPelatihan').classList.add('hidden')" title="Tutup Modal" class="text-emerald-700 hover:text-emerald-950 font-black text-xl leading-none cursor-pointer">&times;</button>
        </div>

        <!-- Form Content -->
        <form action="{{ route('academic.importPelatihan') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="p-6 space-y-4">
                
                <!-- Box Unduh Template Spreadsheet -->
                <div class="bg-emerald-50/60 border border-emerald-200/70 p-4 rounded-sm flex flex-col gap-3">
                    <div class="space-y-0.5">
                        <h4 class="text-[10px] font-black text-emerald-950 uppercase tracking-wide">Belum punya template spreadsheet?</h4>
                        <p class="text-xs font-medium text-slate-600 leading-relaxed">
                            Gunakan format resmi agar struktur kolom modul, mentor, dan komponen penilaian terbaca otomatis tanpa kendala.
                        </p>
                    </div>
                    <div>
                        <a href="{{ route('academic.templatePelatihan') }}" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-[9.5px] font-black uppercase tracking-wider rounded-xs transition shadow-2xs cursor-pointer">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-width="2.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                            </svg>
                            Unduh Template .CSV
                        </a>
                    </div>
                </div>

                <!-- Input Berkas File -->
                <div class="space-y-1">
                    <label class="block text-[9.5px] font-black text-slate-700 uppercase tracking-wider">
                        Pilih Berkas Spreadsheet <span class="text-rose-500">*</span>
                    </label>
                    <input type="file" name="file_excel" accept=".xlsx, .xls, .csv" required class="w-full text-xs font-bold text-slate-600 bg-slate-50 p-2.5 border border-slate-300 rounded-sm file:mr-3.5 file:py-1 file:px-3.5 file:border-0 file:text-[9.5px] file:font-black file:uppercase file:tracking-wider file:bg-slate-800 file:text-white hover:file:bg-slate-700 cursor-pointer">
                    <p class="text-[9px] font-medium text-slate-400 italic">Mendukung format berkas .xlsx, .xls, dan .csv</p>
                </div>

            </div>

            <!-- Footer Aksi -->
            <div class="px-6 py-3.5 bg-slate-50 border-t border-slate-100 flex items-center justify-end gap-2.5 shrink-0">
                <button type="button" onclick="document.getElementById('modalImportPelatihan').classList.add('hidden')" class="px-5 py-2 border border-slate-300 text-slate-600 hover:bg-slate-100 text-xs font-black uppercase tracking-wider rounded-sm cursor-pointer transition-colors">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-black uppercase tracking-wider rounded-sm shadow-xs cursor-pointer border-none transition-colors">
                    Proses Import
                </button>
            </div>
        </form>

    </div>
</div>