<!-- ========================================================================= -->
<!-- MODAL SECURE DELETE PERUSAHAAN (LANDSCAPE 2 TAHAP VALIDASI & DETAIL DAMPAK)-->
<!-- ========================================================================= -->
<div id="modalSecureDelete" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-[200] hidden flex items-center justify-center p-3 sm:p-4 print:hidden select-none overflow-y-auto">
    
    <!-- Backdrop Statis -->
    <div class="fixed inset-0" onclick="closeDeletePerusahaanModal()"></div>

    <div class="relative bg-white border border-slate-200 shadow-2xl max-w-[95vw] md:max-w-3xl lg:max-w-[820px] w-full rounded-xs overflow-hidden z-10 my-auto flex flex-col">
        
        <!-- HEADER MODAL LANDSCAPE -->
        <div class="px-5 py-3.5 bg-[#1e293b] border-b border-slate-700 flex items-center justify-between shrink-0 text-white">
            <div class="flex items-center gap-3">
                <span class="w-2.5 h-2.5 rounded-full bg-rose-500 ring-4 ring-rose-500/20 shrink-0 animate-pulse"></span>
                <div>
                    <h4 class="text-xs font-black uppercase tracking-wider text-white">Konfirmasi Penghapusan Perusahaan Mitra</h4>
                    <span id="deleteStepIndicator" class="text-[9px] font-bold text-slate-300 uppercase tracking-widest block mt-0.5">
                        Tahap 1 dari 2: Penelaahan Risiko & Penjelasan Dampak
                    </span>
                </div>
            </div>
            <button type="button" onclick="closeDeletePerusahaanModal()" class="text-slate-400 hover:text-white hover:bg-slate-800 rounded-xs font-bold text-xl leading-none w-7 h-7 flex items-center justify-center cursor-pointer border-none bg-transparent transition-colors">
                &times;
            </button>
        </div>

        <form id="formSecureDelete" action="" method="POST" class="flex flex-col flex-1">
            @csrf
            @method('DELETE')

            <!-- ============================================================= -->
            <!-- TAHAP 1: VALIDASI PERTAMA - PENELAAHAN RISIKO & PENJELASAN DAMPAK -->
            <!-- ============================================================= -->
            <div id="deleteStep1Container" class="p-5 space-y-4">
                
                <!-- LANDSCAPE SPLIT VIEW -->
                <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
                    
                    <!-- PANEL KIRI (5 KOLOM): IDENTITAS TARGET PERUSAHAAN -->
                    <div class="md:col-span-5 bg-slate-50 border border-slate-200 rounded-xs p-3.5 space-y-3">
                        <div class="border-b border-slate-200 pb-2">
                            <span class="text-[8.5px] font-black uppercase tracking-widest text-slate-400 block mb-0.5">PERUSAHAAN YANG DIPILIH</span>
                            <h5 id="delete_preview_nama" class="text-xs font-black text-slate-800 uppercase tracking-tight break-words">-</h5>
                        </div>

                        <div class="space-y-2 text-[10px]">
                            <div>
                                <span class="text-[8.5px] font-black uppercase tracking-wider text-slate-400 block">Penanggung Jawab (PIC)</span>
                                <span id="delete_preview_pic" class="font-bold text-slate-700 uppercase block truncate">-</span>
                            </div>
                            <div>
                                <span class="text-[8.5px] font-black uppercase tracking-wider text-slate-400 block">Nomor WhatsApp</span>
                                <span id="delete_preview_kontak" class="font-mono font-bold text-slate-700 block truncate">-</span>
                            </div>
                            <div>
                                <span class="text-[8.5px] font-black uppercase tracking-wider text-slate-400 block">Email Resmi</span>
                                <span id="delete_preview_email" class="font-mono font-medium text-slate-600 lowercase block truncate">-</span>
                            </div>
                            <div>
                                <span class="text-[8.5px] font-black uppercase tracking-wider text-slate-400 block">Status & Peserta Terdaftar</span>
                                <div class="flex items-center gap-2 mt-0.5">
                                    <span id="delete_preview_status" class="px-2 py-0.5 text-[8.5px] font-black uppercase rounded-xs bg-emerald-50 text-emerald-700 border border-emerald-200">AKTIF</span>
                                    <span class="text-[9px] font-bold text-slate-500 font-mono">0 Peserta (Aman)</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- PANEL KANAN (7 KOLOM): PENJELASAN DETAIL KONSEKUENSI PENGHAPUSAN -->
                    <div class="md:col-span-7 flex flex-col justify-between space-y-3">
                        <div class="space-y-2.5">
                            <div class="flex items-center gap-2 px-3 py-2 bg-rose-50 border border-rose-200/80 rounded-xs">
                                <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                </svg>
                                <div>
                                    <h6 class="text-[10px] font-black text-rose-800 uppercase tracking-wider">Perhatian: Tindakan Ini Bersifat Permanen</h6>
                                    <p class="text-[9px] font-bold text-rose-600">Data yang terhapus tidak dapat dipulihkan kembali dari sistem database.</p>
                                </div>
                            </div>

                            <div class="space-y-2 text-[10px] text-slate-600">
                                <p class="font-bold text-slate-700 uppercase tracking-wide text-[9.5px]">Konsekuensi Penghapusan Data Mitra:</p>
                                
                                <div class="flex items-start gap-2 bg-slate-50 p-2.5 border border-slate-200/70 rounded-xs">
                                    <span class="w-4 h-4 rounded-full bg-slate-200 text-slate-700 flex items-center justify-center font-bold text-[9px] shrink-0 mt-0.5">1</span>
                                    <p class="leading-relaxed">
                                        <strong class="text-slate-800">Penghapusan Profil Master:</strong> Seluruh profil instansi dan kontak penanggung jawab akan dihapus secara total dari direktori perusahaan.
                                    </p>
                                </div>

                                <div class="flex items-start gap-2 bg-slate-50 p-2.5 border border-slate-200/70 rounded-xs">
                                    <span class="w-4 h-4 rounded-full bg-slate-200 text-slate-700 flex items-center justify-center font-bold text-[9px] shrink-0 mt-0.5">2</span>
                                    <p class="leading-relaxed">
                                        <strong class="text-slate-800">Pemutusan Riwayat Kemitraan:</strong> Riwayat kemitraan program pelatihan/magang yang pernah tertaut ke entitas ini tidak lagi terhubung.
                                    </p>
                                </div>

                                <div class="flex items-start gap-2 bg-slate-50 p-2.5 border border-slate-200/70 rounded-xs">
                                    <span class="w-4 h-4 rounded-full bg-slate-200 text-slate-700 flex items-center justify-center font-bold text-[9px] shrink-0 mt-0.5">3</span>
                                    <p class="leading-relaxed">
                                        <strong class="text-slate-800">Verifikasi Integritas Relasi:</strong> Sistem memverifikasi tidak ada peserta aktif yang terdaftar di perusahaan ini sebelum izin penghapusan diberikan.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Checkbox Persetujuan Validasi Tahap 1 -->
                        <label class="flex items-center gap-2.5 p-2.5 bg-amber-50/70 border border-amber-200 rounded-xs cursor-pointer select-none hover:bg-amber-50 transition-colors">
                            <input type="checkbox" id="checkAcknowledgeDelete" onchange="toggleBtnStep1()" class="rounded-xs text-rose-600 focus:ring-rose-500 w-3.5 h-3.5 border-slate-300 cursor-pointer">
                            <span class="text-[9.5px] font-bold text-amber-900 leading-tight">
                                Saya telah menelaah profil di atas dan memahami seluruh dampak penghapusan ini.
                            </span>
                        </label>
                    </div>

                </div>

                <!-- FOOTER AKSI TAHAP 1 -->
                <div class="pt-3 border-t border-slate-200 flex items-center justify-between gap-3">
                    <button type="button" onclick="closeDeletePerusahaanModal()" class="px-4 py-2 border border-slate-300 text-slate-600 hover:bg-slate-100 rounded-xs text-[9.5px] font-black uppercase tracking-wider cursor-pointer transition-colors bg-white">
                        Batal
                    </button>
                    <button type="button" id="btnNextToStep2" onclick="goToDeleteStep(2)" disabled class="px-5 py-2 bg-slate-200 text-slate-400 rounded-xs text-[9.5px] font-black uppercase tracking-widest cursor-not-allowed transition-colors flex items-center gap-2 border-none">
                        <span>Lanjutkan ke Validasi Akhir</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </button>
                </div>

            </div>

            <!-- ============================================================= -->
            <!-- TAHAP 2: VALIDASI KEDUA - VERIFIKASI KEAMANAN DENGAN KETIK NAMA -->
            <!-- ============================================================= -->
            <div id="deleteStep2Container" class="p-5 space-y-4 hidden">
                
                <div class="bg-rose-50 border border-rose-200 p-3.5 rounded-xs space-y-1.5">
                    <div class="flex items-center gap-2">
                        <span class="px-2 py-0.5 bg-rose-600 text-white text-[8px] font-black uppercase rounded-xs tracking-widest">Validasi Final (2/2)</span>
                        <h5 class="text-[11px] font-black text-rose-900 uppercase tracking-tight">Ketik Nama Perusahaan Untuk Membuka Kunci Tombol Hapus</h5>
                    </div>
                    <p class="text-[10px] text-rose-700 leading-relaxed font-medium">
                        Sebagai bentuk konfirmasi ganda agar tidak terjadi kesalahan penghapusan, silakan ketik ulang nama perusahaan mitra yang tertera di kotak berikut:
                    </p>
                </div>

                <!-- BOX NAMA TARGET -->
                <div class="bg-slate-900 border border-slate-800 p-3.5 rounded-xs text-center select-none shadow-inner">
                    <span class="text-[8.5px] font-black uppercase tracking-[0.2em] text-slate-400 block mb-1">TARGET PENGHAPUSAN PERUSAHAAN</span>
                    <p id="delete-target-name" class="text-sm sm:text-base font-black text-rose-400 tracking-wider uppercase font-mono break-words"></p>
                </div>

                <!-- INPUT VERIFIKASI -->
                <div>
                    <label class="block text-[9.5px] font-black uppercase tracking-wider text-slate-700 mb-1.5">
                        Ketik Persis Nama Perusahaan di Atas: <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" 
                           id="input-delete-verify" 
                           onkeyup="evaluasiTombolHapus()" 
                           placeholder="KETIK NAMA PERUSAHAAN DISINI..." 
                           autocomplete="off"
                           class="w-full bg-slate-50 border-2 border-slate-300 px-3 py-2 text-xs font-black text-slate-800 uppercase rounded-xs focus:outline-none focus:border-rose-600 focus:bg-white placeholder-slate-400 transition-colors">
                    
                    <div class="flex items-center justify-between mt-1.5">
                        <span id="delete-match-status" class="text-[9.5px] font-bold text-slate-400">
                            Status: Belum cocok
                        </span>
                        <span class="text-[9px] font-bold text-slate-400 italic">Case-insensitive (huruf besar/kecil sama)</span>
                    </div>
                </div>

                <!-- FOOTER AKSI TAHAP 2 -->
                <div class="pt-3 border-t border-slate-200 flex items-center justify-between gap-3">
                    <button type="button" onclick="goToDeleteStep(1)" class="px-4 py-2 border border-slate-300 text-slate-600 hover:bg-slate-100 rounded-xs text-[9.5px] font-black uppercase tracking-wider cursor-pointer transition-colors flex items-center gap-1.5 bg-white">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                        <span>Kembali ke Tahap 1</span>
                    </button>

                    <div class="flex items-center gap-2">
                        <button type="button" onclick="closeDeletePerusahaanModal()" class="px-3.5 py-2 text-slate-400 hover:text-slate-600 text-[9.5px] font-black uppercase tracking-wider cursor-pointer border-none bg-transparent">
                            Batal
                        </button>
                        <button type="submit" 
                                id="btn-confirm-delete-final" 
                                disabled 
                                class="px-5 py-2 bg-slate-200 text-slate-400 text-[9.5px] font-black uppercase tracking-widest rounded-xs cursor-not-allowed transition-all flex items-center gap-2 border-none">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            <span>HAPUS PERMANEN PERUSAHAAN</span>
                        </button>
                    </div>
                </div>

            </div>

        </form>

    </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL BLOCKED DELETE (TIDAK DAPAT DIHAPUS KARENA TERKAIT PESERTA AKTIF)    -->
<!-- ========================================================================= -->
<div id="modalBlockedDelete" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-[200] hidden flex items-center justify-center p-3 sm:p-4 print:hidden select-none overflow-y-auto">
    <div class="fixed inset-0" onclick="document.getElementById('modalBlockedDelete').classList.add('hidden')"></div>
    <div class="relative bg-white w-full max-w-[95vw] md:max-w-xl shadow-2xl border border-slate-200 overflow-hidden rounded-xs z-10 my-auto">
        <div class="px-5 py-3.5 border-b border-amber-200 bg-amber-50 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-amber-500 ring-4 ring-amber-500/20 shrink-0"></span>
                <h4 class="text-xs font-black text-amber-900 uppercase tracking-widest">Penghapusan Perusahaan Ditolak</h4>
            </div>
            <button type="button" onclick="document.getElementById('modalBlockedDelete').classList.add('hidden')" class="text-slate-400 hover:text-slate-700 font-bold text-xl leading-none cursor-pointer border-none bg-transparent">&times;</button>
        </div>
        <div class="p-5 space-y-3.5">
            <div class="p-3 bg-amber-50/50 border border-amber-200 rounded-xs flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-amber-100 text-amber-700 font-black text-sm flex items-center justify-center shrink-0">
                    ⚠️
                </div>
                <div>
                    <p class="text-xs font-bold text-slate-800 leading-snug">
                        Perusahaan ini masih memiliki <span id="blocked-peserta-count" class="text-rose-600 font-black font-mono text-sm">0</span> peserta aktif/terdaftar.
                    </p>
                    <p class="text-[9.5px] text-slate-500 mt-0.5">
                        Integritas data mencegah penghapusan entitas yang masih memiliki relasi data peserta aktif.
                    </p>
                </div>
            </div>
            
            <p class="text-[10px] text-slate-600 leading-relaxed bg-slate-50 p-3 rounded-xs border border-slate-200">
                <strong>Petunjuk Penyelesaian:</strong> Silakan buka menu <strong>Data Peserta</strong>, kemudian ubah atau pindahkan data tempat kerja peserta terkait ke perusahaan lain terlebih dahulu sebelum menghapus profil perusahaan mitra ini.
            </p>
            
            <div class="pt-2 flex justify-end">
                <button type="button" onclick="document.getElementById('modalBlockedDelete').classList.add('hidden')" class="px-5 py-2 bg-slate-900 hover:bg-slate-800 text-white text-[9.5px] font-black uppercase tracking-widest rounded-xs cursor-pointer border-none transition-colors">
                    Saya Mengerti
rsor-pointer">
                    MENGERTI
                </button>
            </div>
        </div>
    </div>
</div>