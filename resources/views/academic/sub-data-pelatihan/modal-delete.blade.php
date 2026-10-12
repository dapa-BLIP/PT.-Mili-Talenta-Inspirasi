<!-- ========================================================================= -->
<!-- MODAL WARNING TIDAK BISA HAPUS (KARENA SUDAH ADA PESERTA / NILAI)         -->
<!-- ========================================================================= -->
<div id="modalWarningDeletePelatihan" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-[200] hidden flex items-center justify-center p-4 select-none animate-in fade-in duration-150">
    <div class="bg-white w-full max-w-md shadow-2xl border border-rose-200 rounded-sm overflow-hidden animate-in zoom-in-95 duration-150">
        <!-- Header Modal Warna #1e293b (Slate-800) -->
        <div class="px-5 py-3.5 bg-slate-800 text-white flex items-center justify-between border-b border-slate-700">
            <div class="flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-rose-500 animate-pulse"></span>
                <h4 class="text-xs font-black uppercase tracking-wider text-white">Peringatan: Kelas Terkunci</h4>
            </div>
            <button type="button" onclick="document.getElementById('modalWarningDeletePelatihan').classList.add('hidden')" class="text-slate-400 hover:text-white text-lg font-black leading-none bg-transparent border-none cursor-pointer">&times;</button>
        </div>

        <div class="p-5 space-y-4">
            <!-- Icon Alert & Pesan -->
            <div class="flex items-start gap-3 bg-rose-50 border border-rose-200 p-3.5 rounded-sm">
                <div class="p-2 bg-rose-100 text-rose-600 rounded-full shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
                <div>
                    <h5 class="text-xs font-black text-rose-900 uppercase tracking-tight">Kelas Tidak Dapat Dihapus!</h5>
                    <p class="text-[11px] font-medium text-rose-700 mt-0.5 leading-relaxed">
                        Kelas pelatihan ini telah aktif dan memiliki riwayat data akademik yang saling terhubung di dalam sistem.
                    </p>
                </div>
            </div>

            <!-- Target Kelas -->
            <div class="bg-slate-50 border border-slate-200 p-3 rounded-sm text-center">
                <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mb-0.5">Nama Kelas Pelatihan:</p>
                <p id="warning-pelatihan-target" class="text-xs font-black text-slate-800 uppercase tracking-tight">-</p>
            </div>

            <!-- Badges Indikator Relasi Data Terkunci -->
            <div class="grid grid-cols-2 gap-2">
                <div class="bg-blue-50 border border-blue-200 p-2.5 rounded-sm text-center">
                    <p class="text-[8.5px] font-black text-blue-500 uppercase tracking-wider">Peserta Terdaftar</p>
                    <p id="warning-peserta-count" class="text-xs font-black text-blue-700 uppercase tracking-tight mt-0.5">0 PESERTA</p>
                </div>
                <div class="bg-amber-50 border border-amber-200 p-2.5 rounded-sm text-center">
                    <p class="text-[8.5px] font-black text-amber-600 uppercase tracking-wider">Data Nilai Terinput</p>
                    <p id="warning-nilai-count" class="text-xs font-black text-amber-700 uppercase tracking-tight mt-0.5">0 RECORD</p>
                </div>
            </div>

            <p class="text-[10.5px] text-slate-500 font-medium leading-relaxed italic text-center">
                Untuk menjaga integritas database dan laporan akademik, Anda tidak diizinkan menghapus kelas yang sudah memiliki peserta atau penilaian aktif.
            </p>

            <div class="pt-1">
                <button type="button" onclick="document.getElementById('modalWarningDeletePelatihan').classList.add('hidden')" class="w-full bg-slate-800 hover:bg-slate-900 text-white py-2.5 text-xs font-black uppercase tracking-wider rounded-sm cursor-pointer shadow-sm transition-colors border-none">
                    Saya Mengerti & Tutup
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL SECURE DELETE (UNTUK KELAS YANG BELUM ADA PESERTA & BELUM ADA NILAI) -->
<!-- ========================================================================= -->
<div id="modalSecureDelete" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-[200] hidden flex items-center justify-center p-4 select-none">
    <div class="bg-white w-full max-w-md shadow-2xl border border-slate-200 rounded-sm overflow-hidden animate-in zoom-in-95 duration-150">
        <!-- Header Warna #1e293b (Slate-800) -->
        <div class="px-6 py-4 bg-slate-800 text-white flex items-center justify-between border-b border-slate-700">
            <div class="flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                <h4 class="text-xs font-black uppercase tracking-wider text-white">Konfirmasi Hapus Program</h4>
            </div>
            <button type="button" onclick="document.getElementById('modalSecureDelete').classList.add('hidden')" class="text-slate-400 hover:text-white text-lg font-black leading-none bg-transparent border-none cursor-pointer">&times;</button>
        </div>
        <form id="formSecureDelete" action="" method="POST" class="p-6 space-y-4">
            @csrf @method('DELETE')
            <p class="text-xs font-medium text-slate-600 leading-relaxed">
                Tindakan ini permanen. Kelas ini belum memiliki peserta dan nilai sehingga aman untuk dihapus.
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
                <button type="submit" id="btn-confirm-delete-final" disabled class="w-2/3 bg-slate-200 text-slate-400 py-2.5 text-xs font-black uppercase rounded-sm cursor-not-allowed transition-colors border-none">Hapus Permanen</button>
            </div>
        </form>
    </div>
</div>