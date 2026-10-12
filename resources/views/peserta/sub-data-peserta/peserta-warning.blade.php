<!-- ========================================================================= -->
<!-- MODAL PERINGATAN PERUBAHAN PROGRAM PELATIHAN                             -->
<!-- ========================================================================= -->
<div id="modalWarningProgramPeserta" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-[150] hidden flex items-center justify-center p-3 sm:p-4 print:hidden select-none transition-all duration-200">
    <!-- Backdrop (Klik luar menutup modal) -->
    <div class="fixed inset-0" onclick="closeModalWarningProgram()"></div>

    <div class="relative bg-white w-full max-w-[440px] shadow-2xl border border-amber-400 rounded-xs overflow-hidden flex flex-col my-auto z-[155]">
        <!-- Header -->
        <div class="px-4 py-2.5 border-b border-amber-200 bg-amber-50 flex items-center justify-between shrink-0">
            <div class="flex items-center gap-2">
                <span class="text-amber-600 font-bold text-sm">⚠️</span>
                <h3 class="text-xs font-bold uppercase tracking-wider text-amber-900">Peringatan</h3>
            </div>
            <button type="button" onclick="closeModalWarningProgram()" class="text-amber-500 hover:text-rose-600 font-bold text-lg leading-none cursor-pointer p-1">&times;</button>
        </div>

        <!-- Body -->
        <div class="p-5 text-center">
            <div class="w-12 h-12 mx-auto mb-3 rounded-full bg-amber-100 border border-amber-200 flex items-center justify-center text-amber-600 shadow-2xs">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
            <p class="text-xs font-bold text-slate-800 uppercase tracking-tight leading-relaxed">
                Peserta memiliki nilai di program <br>
                <span id="modal_warning_program_name" class="inline-block mt-1.5 px-2.5 py-1 bg-amber-100 text-amber-950 border border-amber-300 font-black rounded-xs text-xs tracking-tight">-</span>
            </p>
            <p class="text-[10.5px] text-slate-500 font-medium mt-2 leading-relaxed">
                Program pelatihan tidak dapat diubah sebelum nilai pada program sebelumnya dibersihkan.
            </p>
        </div>

        <!-- Footer -->
        <div class="p-3 bg-slate-50 border-t border-slate-100 flex gap-2">
            <button type="button" onclick="closeModalWarningProgram()" class="w-full bg-slate-900 hover:bg-slate-800 text-white py-2 text-xs font-bold uppercase tracking-wider rounded-xs shadow-xs cursor-pointer border-none transition-colors text-center">
                Mengerti
            </button>
        </div>
    </div>
</div>
