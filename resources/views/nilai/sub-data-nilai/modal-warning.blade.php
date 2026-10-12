<!-- WARNING MODAL (PEMBERITAHUAN VALIDASI SISTEM & FILTER CASCADING) -->
<div id="matrix-warning-modal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs hidden select-none">
    <div class="bg-white border border-slate-300 max-w-md w-full shadow-2xl overflow-hidden rounded-xs">
        <div class="bg-[#1e293b] px-4 py-3 border-b border-slate-700 flex items-center justify-between text-white">
            <div class="flex items-center gap-2">
                <span class="text-amber-400 font-bold text-sm">⚠️</span>
                <h4 class="text-[10px] font-black text-white uppercase tracking-widest">Peringatan Sistem</h4>
            </div>
            <button type="button" onclick="tutupWarningModal()" class="text-slate-400 hover:text-white font-bold text-lg leading-none cursor-pointer border-none bg-transparent">&times;</button>
        </div>
        <div class="p-5">
            <p id="modal-warning-message" class="text-[11px] font-bold text-slate-600 uppercase tracking-wide leading-relaxed"></p>
        </div>
        <div class="bg-slate-50 px-4 py-2.5 border-t border-slate-100 flex justify-end">
            <button type="button" onclick="tutupWarningModal()" 
                    class="bg-slate-800 hover:bg-slate-900 text-white px-4 py-1.5 text-[9px] font-black uppercase tracking-widest transition shadow-xs cursor-pointer rounded-xs border-none">
                MENGERTI
            </button>
        </div>
    </div>
</div>

