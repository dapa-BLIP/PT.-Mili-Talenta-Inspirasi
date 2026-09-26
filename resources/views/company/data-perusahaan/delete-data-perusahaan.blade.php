<!-- MODAL SECURE DELETE PERUSAHAAN -->
<div id="modalSecureDelete" class="fixed inset-0 bg-slate-950/70 backdrop-blur-xs z-[200] hidden flex items-center justify-center p-4">
    <div class="bg-white w-full max-w-md shadow-2xl border border-slate-200 overflow-hidden rounded-xs">
        <div class="px-5 py-3.5 border-b border-rose-100 bg-rose-50/40 flex items-center gap-2">
            <span class="text-rose-500 font-bold text-sm">⚠️</span>
            <h4 class="text-[10px] font-black text-rose-700 uppercase tracking-widest">KONFIRMASI HAPUS PERUSAHAAN</h4>
        </div>
        <form id="formSecureDelete" action="" method="POST" class="p-4 space-y-3">
            @csrf
            @method('DELETE')
            <p class="text-[10.5px] font-bold text-slate-500 uppercase tracking-wide leading-relaxed">
                Tindakan ini permanen! Ketik kembali nama perusahaan di bawah untuk mengonfirmasi.
            </p>
            <div class="bg-slate-50 border border-slate-200 p-2 text-center select-none">
                <p id="delete-target-name" class="text-xs font-black text-slate-800 tracking-tight uppercase"></p>
            </div>
            <input type="text" id="input-delete-verify" onkeyup="evaluasiTombolHapus()" placeholder="Ketik kembali nama perusahaan..." class="w-full bg-white border border-slate-300 px-3 py-1.5 text-[10px] font-black text-slate-800 focus:outline-none focus:border-rose-500 uppercase">
            <div class="flex gap-2">
                <button type="button" onclick="document.getElementById('modalSecureDelete').classList.add('hidden')" class="w-1/3 border border-slate-200 text-slate-400 py-2 text-[9px] font-black uppercase rounded-xs cursor-pointer hover:bg-slate-50">
                    BATAL
                </button>
                <button type="submit" id="btn-confirm-delete-final" disabled class="w-2/3 bg-slate-200 text-slate-400 py-2 text-[9px] font-black uppercase tracking-widest rounded-xs cursor-not-allowed">
                    CONFIRM & DELETE
                </button>
            </div>
        </form>
    </div>
</div>