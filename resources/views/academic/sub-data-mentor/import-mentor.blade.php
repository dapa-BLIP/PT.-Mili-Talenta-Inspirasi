<!-- ========================================================================= -->
<!-- MODAL: IMPORT DATABASE MENTOR VIA EXCEL/CSV                              -->
<!-- ========================================================================= -->
<div id="modalImportMentor" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-[200] hidden flex items-center justify-center p-3 sm:p-4 print:hidden select-none overflow-y-auto">
    
    <div class="fixed inset-0" onclick="closeImportMentorModal()"></div>

    <div class="relative w-full max-w-[92vw] sm:max-w-[440px] bg-white border border-slate-200 shadow-2xl rounded-xs overflow-hidden flex flex-col z-[205] my-auto">
        
        <!-- Header -->
        <div class="px-4 py-2.5 border-b border-emerald-100 bg-emerald-50 text-emerald-800 flex items-center justify-between shrink-0">
            <div class="flex items-center gap-2">
                <span class="text-[8.5px] font-black uppercase tracking-wider bg-emerald-600 text-white px-1.5 py-0.2 rounded-xs">Spreadsheet</span>
                <h3 class="text-[10.5px] font-black uppercase tracking-wider text-emerald-950">Import Database Mentor</h3>
            </div>
            <button type="button" onclick="closeImportMentorModal()" class="text-emerald-700 hover:text-emerald-950 font-bold text-lg leading-none cursor-pointer">&times;</button>
        </div>

        <form action="{{ route('academic.importMentor') }}" method="POST" enctype="multipart/form-data" class="p-3.5 sm:p-4 space-y-3 bg-slate-50/50">
            @csrf
            
            <!-- Box Panduan Template -->
            <div class="bg-white border border-slate-200 p-3 rounded-xs shadow-2xs space-y-2">
                <div>
                    <h4 class="text-[9.5px] font-black text-slate-800 uppercase tracking-wide">Belum punya template spreadsheet?</h4>
                    <p class="text-[10px] font-medium text-slate-500 leading-relaxed mt-0.5">Gunakan format resmi agar kolom nama, kontak nomor WA, dan program pengampu terbaca otomatis tanpa gagal.</p>
                </div>
                <div>
                    <a href="{{ route('academic.templateMentor') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-[9px] font-black uppercase tracking-wider rounded-xs transition shadow-2xs cursor-pointer">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                        Unduh Template .CSV
                    </a>
                </div>
            </div>

            <!-- Input Berkas -->
            <div class="bg-white border border-slate-200 p-3 rounded-xs shadow-2xs space-y-1.5">
                <label class="block text-[9px] font-black text-slate-700 uppercase tracking-wider">Pilih Berkas Spreadsheet <span class="text-rose-500">*</span></label>
                <input type="file" name="file_excel" accept=".xlsx, .xls, .csv" required class="w-full text-[10px] font-bold text-slate-600 bg-slate-50 p-2 border border-slate-300 rounded-xs file:mr-2.5 file:py-1 file:px-2.5 file:border-0 file:text-[8.5px] file:font-black file:uppercase file:bg-slate-800 file:text-white hover:file:bg-slate-700 cursor-pointer">
                <p class="text-[8.5px] font-medium text-slate-400 italic">Format yang didukung: .xlsx, .xls, dan .csv</p>
            </div>

            <!-- Tombol Aksi -->
            <div class="flex items-center justify-end gap-2 pt-1">
                <button type="button" onclick="closeImportMentorModal()" class="px-3.5 py-1.5 border border-slate-300 text-slate-600 hover:bg-slate-100 text-[10px] font-black uppercase tracking-wider rounded-xs cursor-pointer transition-colors">
                    Batal
                </button>
                <button type="submit" class="px-4 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-[10px] font-black uppercase tracking-wider rounded-xs shadow-xs cursor-pointer border-none transition-colors">
                    Proses Import
                </button>
            </div>
        </form>

    </div>
</div>