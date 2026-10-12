<!-- ========================================================================= -->
<!-- MODAL: KONFIRMASI HAPUS PESERTA (SAMAKAN KAYA DI KELOLA MENTOR)           -->
<!-- ========================================================================= -->
<div id="modalDeleteConfirmation" class="fixed inset-0 bg-slate-900/60 z-[9999] hidden flex items-center justify-center p-3 sm:p-4 print:hidden select-none overflow-y-auto">
    
    <!-- Backdrop Statis -->
    <div class="fixed inset-0" onclick="closeDeleteConfirmation()"></div>

    <div class="relative w-full max-w-[92vw] sm:max-w-[440px] bg-white border border-slate-200 shadow-2xl rounded-xs overflow-hidden flex flex-col z-10 my-auto">
        
        <div class="px-4 py-2.5 bg-slate-800 text-white flex items-center justify-between shrink-0">
            <h4 class="text-[11px] font-black uppercase tracking-wider">Hapus Data Peserta</h4>
            <button type="button" onclick="closeDeleteConfirmation()" class="text-slate-400 hover:text-white font-bold text-lg leading-none cursor-pointer border-none bg-transparent">&times;</button>
        </div>

        <div class="p-5 text-center space-y-1.5">
            <p class="text-[10.5px] font-bold text-slate-500 uppercase tracking-wide">Yakin ingin menghapus data peserta ini?</p>
            <p id="delete_target_name" class="text-xs font-black text-rose-600 uppercase tracking-tight break-words"></p>
            <p class="text-[9px] text-slate-400 italic">Data yang dihapus tidak dapat dikembalikan.</p>
        </div>

        <div class="p-3 bg-slate-50 border-t border-slate-200 flex gap-2">
            <button type="button" onclick="closeDeleteConfirmation()" class="w-1/2 border border-slate-300 text-slate-600 py-1.5 text-[10px] font-black uppercase tracking-wider hover:bg-slate-100 rounded-xs cursor-pointer transition-colors bg-white">
                Batal
            </button>
            <form id="formConfirmDelete" action="" method="POST" class="w-1/2">
                @csrf 
                @method('DELETE')
                <button type="submit" class="w-full bg-rose-600 hover:bg-rose-700 text-white py-1.5 text-[10px] font-black uppercase tracking-wider rounded-xs shadow-xs cursor-pointer border-none transition-colors">
                    Hapus
                </button>
            </form>
        </div>

    </div>
</div>
