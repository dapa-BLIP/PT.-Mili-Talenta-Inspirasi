<!-- ========================================================================= -->
<!-- MODAL DETAIL PERUSAHAAN (LANDSCAPE WIDE SPLIT-VIEW - CLEAN & MODERN)      -->
<!-- ========================================================================= -->
<div id="modalDetailPerusahaan" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-[9999] hidden flex items-center justify-center p-3 sm:p-4 print:hidden select-none overflow-y-auto">
    
    <!-- Backdrop Statis Fullscreen (Klik luar menutup modal) -->
    <div class="fixed inset-0" onclick="tutupModalDetailPerusahaan()"></div>

    <div class="relative bg-white border border-slate-200 shadow-2xl max-w-[95vw] md:max-w-3xl lg:max-w-[800px] w-full rounded-xs overflow-hidden z-10 my-auto flex flex-col">
        
        <!-- HEADER -->
        <div class="px-5 py-3.5 bg-[#1e293b] border-b border-slate-700 flex items-center justify-between shrink-0 text-white">
            <div class="flex items-center gap-3 min-w-0 pr-3">
                <div class="w-9 h-9 rounded-xs bg-slate-700/80 border border-slate-600 flex items-center justify-center text-[#22a0f6] shrink-0 shadow-2xs">
                    <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                </div>
                <div class="min-w-0">
                    <h3 id="detail_nama_perusahaan" class="text-sm font-black uppercase text-white tracking-tight truncate leading-tight mt-0.5">-</h3>
                </div>
                <span id="detail_status_badge" class="ml-2 inline-block text-[9px] font-bold uppercase px-2 py-0.5 rounded-xs shrink-0 bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">
                    AKTIF
                </span>
            </div>
            
            <button type="button" 
                    onclick="tutupModalDetailPerusahaan()" 
                    title="Tutup Modal"
                    class="text-slate-400 hover:text-white hover:bg-slate-700/80 rounded-xs font-black text-xl leading-none w-7 h-7 flex items-center justify-center cursor-pointer transition-colors">
                &times;
            </button>
        </div>
        
        <!-- BODY (LANDSCAPE SPLIT 5:7 - DAFTAR PROGRAM LEBIH LEBAR) -->
        <div class="p-5 overflow-y-auto grid grid-cols-1 md:grid-cols-12 gap-4 bg-white">
            
            <!-- PANEL KIRI (5 KOLOM): IDENTITAS & KONTAK PIC -->
            <div class="md:col-span-5 space-y-3 flex flex-col justify-between">
                
                <!-- PIC & WHATSAPP (NOMOR WA SEJAJAR DI SAMPING NAMA) -->
                <div class="p-3 bg-slate-50 border border-slate-200 rounded-xs">
                    <span class="text-[8.5px] font-black uppercase tracking-widest text-slate-400 block mb-1">
                        PENANGGUNG JAWAB (PIC)
                    </span>
                    <div class="flex items-center justify-between gap-2 flex-wrap mt-0.5">
                        <span class="text-[11.5px] font-black text-slate-800 uppercase tracking-tight truncate" id="detail_pic">
                            -
                        </span>
                        <a id="detail_wa_link" href="#" target="_blank" rel="noopener noreferrer" onclick="event.stopPropagation()" title="Kirim Pesan WhatsApp" class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-emerald-50 hover:bg-emerald-600 text-emerald-700 hover:text-white border border-emerald-200/80 rounded-xs text-[10px] font-bold font-mono transition-colors shadow-2xs group shrink-0">
                            <svg class="w-3 h-3 fill-current text-emerald-600 group-hover:text-white transition-colors shrink-0" viewBox="0 0 24 24">
                                <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981z"/>
                            </svg>
                            <span id="detail_kontak">-</span>
                        </a>
                    </div>
                </div>

                <!-- EMAIL RESMI -->
                <div class="p-3 bg-slate-50 border border-slate-200 rounded-xs">
                    <span class="text-[8.5px] font-black uppercase tracking-widest text-slate-400 block mb-1">
                        EMAIL KORESPONDENSI
                    </span>
                    <span id="detail_email" class="text-[11px] font-semibold text-slate-700 font-mono lowercase block truncate mt-0.5">
                        -
                    </span>
                </div>

                <!-- ALAMAT LENGKAP -->
                <div class="p-3 bg-slate-50 border border-slate-200 rounded-xs flex-1 flex flex-col justify-between">
                    <span class="text-[8.5px] font-black uppercase tracking-widest text-slate-400 block mb-1">
                        ALAMAT KANTOR / OPERASIONAL
                    </span>
                    <p id="detail_alamat" class="text-[10.5px] font-medium text-slate-600 leading-relaxed mt-0.5">
                        -
                    </p>
                </div>

            </div>

            <!-- PANEL KANAN (7 KOLOM - LEBIH LEBAR): REKAPITULASI PROGRAM & ALOKASI PESERTA -->
            <div class="md:col-span-7 flex flex-col">
                <div class="border border-slate-200 rounded-xs overflow-hidden flex flex-col h-full bg-white">
                    
                    <!-- TABEL HEADER WITH BADGE COUNTER -->
                    <div class="px-3.5 py-2.5 bg-slate-50 border-b border-slate-200 flex justify-between items-center shrink-0">
                        <div class="flex items-center gap-2">
                            <span class="text-[10px] font-black text-slate-700 uppercase tracking-wider">
                                PROGRAM PELATIHAN DIIKUTI
                            </span>
                        </div>
                        <span id="detail_peserta_count" class="text-[9.5px] font-black text-[#22a0f6] bg-blue-50 border border-blue-100 px-2 py-0.5 rounded-xs font-mono uppercase tracking-wider">
                            TOTAL: 0 PESERTA
                        </span>
                    </div>
                    
                    <!-- DAFTAR PROGRAM -->
                    <div class="overflow-y-auto max-h-[200px] flex-1 bg-white min-h-[130px]">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-slate-100/70 border-b border-slate-200 text-slate-400">
                                    <th class="px-3.5 py-2 text-[9px] font-black uppercase tracking-widest">NAMA PROGRAM PELATIHAN</th>
                                    <th class="px-3.5 py-2 text-[9px] font-black uppercase tracking-widest text-right w-40">JUMLAH PESERTA</th>
                                </tr>
                            </thead>
                            <tbody id="detail_breakdown_body" class="divide-y divide-slate-100">
                                <!-- Diisi secara otomatis oleh JavaScript -->
                            </tbody>
                        </table>
                    </div>

                    <!-- FOOTNOTE KURIKULUM -->
                    <div class="p-2.5 bg-slate-50/80 border-t border-slate-200 text-[9px] text-slate-400 leading-normal shrink-0">
                        * Seluruh data peserta terafiliasi langsung dengan kurikulum dan kelas aktif di Master Akademik.
                    </div>
                </div>
            </div>

        </div>

        <!-- FOOTER MODAL (TOMBOL TUTUP) -->
        <div class="px-5 py-3 bg-slate-50 border-t border-slate-200 flex justify-end shrink-0">
            <button type="button" 
                    onclick="tutupModalDetailPerusahaan()" 
                    class="px-6 py-2 bg-slate-800 hover:bg-slate-900 text-white text-[10px] font-black uppercase tracking-widest rounded-xs transition-colors cursor-pointer border-none shadow-xs">
                TUTUP
            </button>
        </div>

    </div>
</div>