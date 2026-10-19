<!-- ========================================================================= -->
<!-- MODAL: IMPORT DATABASE PESERTA (LANDSCAPE WIDE VIEW)                      -->
<!-- ========================================================================= -->
<div id="modalImportPeserta" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-[200] hidden flex items-center justify-center p-3 sm:p-4 print:hidden select-none overflow-y-auto">
    
    <!-- Backdrop Statis (Klik luar menutup modal) -->
    <div class="fixed inset-0" onclick="closeImportPesertaModal()"></div>

    <div class="relative w-full max-w-[95vw] sm:max-w-xl md:max-w-2xl bg-white border border-slate-200 shadow-2xl rounded-xs overflow-hidden flex flex-col z-[205] my-auto">
        
        <!-- Header -->
        <div class="px-5 sm:px-6 py-3 border-b border-emerald-100 bg-emerald-50 text-emerald-800 flex items-center justify-between shrink-0 select-none">
            <div class="flex items-center gap-2">
                <span class="text-[8.5px] font-black uppercase tracking-wider bg-emerald-600 text-white px-2 py-0.5 rounded-xs">Spreadsheet</span>
                <h3 class="text-xs font-black uppercase tracking-wider text-emerald-950">Import Database Peserta</h3>
            </div>
            <button type="button" onclick="closeImportPesertaModal()" title="Tutup Modal" class="text-emerald-700 hover:text-emerald-950 font-black text-xl leading-none cursor-pointer border-none bg-transparent">&times;</button>
        </div>

        <form action="{{ route('peserta.import') }}" method="POST" enctype="multipart/form-data" class="p-5 sm:p-6 space-y-4 bg-slate-50/50">
            @csrf
            
            <!-- Box Panduan Template -->
            <div class="bg-white border border-slate-200 p-4 rounded-xs shadow-2xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div class="space-y-1">
                    <h4 class="text-[10px] font-black text-slate-800 uppercase tracking-wide">Belum punya template spreadsheet?</h4>
                    <p class="text-[10.5px] font-medium text-slate-500 leading-relaxed">
                        Gunakan format file acuan resmi agar kolom nama peserta, kontak WA, email, tempat kerja, dan program pelatihan terbaca akurat.
                    </p>
                </div>
                <div class="shrink-0">
                    <a href="{{ route('peserta.template') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-[9.5px] font-black uppercase tracking-wider rounded-xs transition shadow-2xs cursor-pointer whitespace-nowrap">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                        <span>Unduh Template .XLSX</span>
                    </a>
                </div>
            </div>

            <!-- Input Berkas -->
            <div class="bg-white border border-slate-200 p-4 rounded-xs shadow-2xs space-y-2">
                <label class="block text-[9.5px] font-black text-slate-700 uppercase tracking-wider">
                    Pilih Berkas Spreadsheet <span class="text-rose-500">*</span>
                </label>
                <input type="file" name="file_excel" accept=".xlsx, .xls, .csv" required 
                       class="w-full text-xs font-bold text-slate-600 bg-slate-50 p-2.5 border border-slate-300 rounded-xs file:mr-3.5 file:py-1 file:px-3 file:border-0 file:text-[9px] file:font-black file:uppercase file:bg-slate-800 file:text-white hover:file:bg-slate-700 cursor-pointer focus:outline-none focus:border-emerald-500 transition-colors">
                <p class="text-[9px] font-medium text-slate-400 italic">Format yang didukung: .xlsx, .xls, dan .csv</p>
            </div>

            <!-- Tombol Aksi -->
            <div class="flex items-center justify-end gap-2.5 pt-1">
                <button type="button" onclick="closeImportPesertaModal()" class="px-4 py-2 border border-slate-300 text-slate-600 hover:bg-slate-100 text-[10px] font-black uppercase tracking-wider rounded-xs cursor-pointer transition-colors bg-white">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-[10px] font-black uppercase tracking-wider rounded-xs shadow-xs cursor-pointer border-none transition-colors flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                    <span>Proses Import</span>
                </button>
            </div>
        </form>

    </div>
</div>
