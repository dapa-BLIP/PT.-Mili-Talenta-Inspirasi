<!-- MODAL SECURE DELETE -->
<div id="modalSecureDelete" class="fixed inset-0 bg-slate-900/40 backdrop-blur-xs z-[200] hidden flex items-center justify-center p-4">
    <div class="bg-white w-full max-w-md shadow-2xl border border-slate-200 rounded-sm overflow-hidden">
        <div class="px-6 py-4 bg-slate-800 text-white flex items-center gap-2">
            <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
            <h4 class="text-xs font-black uppercase tracking-wider">Konfirmasi Hapus Program</h4>
        </div>
        <form id="formSecureDelete" action="" method="POST" class="p-6 space-y-4">
            @csrf @method('DELETE')
            <p class="text-xs font-medium text-slate-600 leading-relaxed">
                Tindakan ini permanen. Menghapus program akan memutus relasi materi dan penilaian peserta terkait.
            </p>
            <div class="bg-slate-50 border border-slate-200 p-3 rounded-sm text-center">
                <p id="delete-target-name" class="text-xs font-black text-slate-800 uppercase"></p>
            </div>
            <div>
                <label class="block text-[9.5px] font-black uppercase tracking-wider text-slate-400 mb-1">Ketik nama modul di atas untuk konfirmasi:</label>
                <input type="text" id="input-delete-verify" onkeyup="evaluasiTombolHapus()" placeholder="Ketik persis nama di atas..." class="w-full bg-white border border-slate-300 px-3 py-2 text-xs font-bold text-slate-800 focus:outline-none focus:border-rose-500 uppercase rounded-sm">
            </div>
            <div class="flex gap-2.5 pt-1">
                <button type="button" onclick="document.getElementById('modalSecureDelete').classList.add('hidden')" class="w-1/3 border border-slate-300 text-slate-600 py-2.5 text-xs font-black uppercase rounded-sm cursor-pointer hover:bg-slate-50">Batal</button>
                <button type="submit" id="btn-confirm-delete-final" disabled class="w-2/3 bg-slate-200 text-slate-400 py-2.5 text-xs font-black uppercase rounded-sm cursor-not-allowed transition-colors">Hapus Permanen</button>
            </div>
        </form>
    </div>
</div>