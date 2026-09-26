<!-- MODAL DETAIL PERUSAHAAN (DENGAN REKAP LENGKAP PROGRAM) -->
<div id="modalDetailPerusahaan" class="fixed inset-0 bg-slate-900/40 backdrop-blur-xs z-[120] hidden flex items-center justify-center p-4 transition-all duration-300">
    <div class="bg-white w-full max-w-lg shadow-2xl border border-slate-200 overflow-hidden rounded-xs">
        
        <!-- Header -->
        <div class="px-5 py-3.5 bg-gradient-to-r from-sky-50 via-blue-50/30 to-white border-b border-slate-100 flex items-center justify-between">
            <div class="flex items-center gap-2.5 min-w-0 pr-3">
                <h4 id="detail_nama_perusahaan" class="text-sm font-bold uppercase tracking-tight text-slate-800 truncate">Nama Perusahaan</h4>
                <span id="detail_status_badge" class="inline-block text-[10px] font-bold uppercase px-2 py-0.5 rounded-xs bg-emerald-50 text-emerald-600 border border-emerald-200 shrink-0">AKTIF</span>
            </div>
            <button type="button" onclick="document.getElementById('modalDetailPerusahaan').classList.add('hidden')" class="text-slate-400 hover:text-slate-700 font-bold text-xl leading-none cursor-pointer shrink-0">&times;</button>
        </div>
        
        <div class="p-4 space-y-3 text-xs max-h-[75vh] overflow-y-auto">
            <!-- Data Pokok PIC & Email -->
            <div class="grid grid-cols-2 gap-2">
              <div class="bg-slate-50 border border-slate-100 p-2.5 rounded-xs">
                    <span class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider block mb-0.5">PIC / Penanggung Jawab</span>
                <div class="flex items-center justify-between gap-2 mt-0.5">
                    <span id="detail_pic" class="text-xs font-bold text-slate-700 uppercase truncate"></span>
                    <span id="detail_kontak" class="text-xs font-mono font-semibold text-emerald-600 shrink-0"></span>
               </div>
            </div>
                <div class="bg-slate-50 border border-slate-100 p-2.5 rounded-xs">
                    <span class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider block">Email Resmi</span>
                    <span id="detail_email" class="font-medium text-slate-700 block lowercase truncate font-mono mt-0.5"></span>
                </div>
            </div>

            <!-- Alamat Kantor -->
            <div class="bg-slate-50 border border-slate-100 p-2.5 rounded-xs">
                <span class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider block mb-0.5">Alamat Lengkap</span>
                <p id="detail_alamat" class="text-xs font-normal text-slate-600 leading-relaxed"></p>
            </div>

            <!-- Rekap Program Pelatihan & Jumlah Peserta -->
            <div class="border border-slate-200 rounded-xs overflow-hidden">
                <div class="px-3 py-2 bg-slate-50 border-b border-slate-200 flex justify-between items-center">
                    <span class="text-xs font-bold text-slate-700 uppercase tracking-wide">Rincian Program Pelatihan Diikuti</span>
                    <span id="detail_peserta_count" class="text-xs font-bold text-blue-600 font-mono">0 Peserta</span>
                </div>
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-slate-100/70 text-slate-500 text-[10px] font-bold uppercase border-b border-slate-200">
                            <th class="p-2.5 pl-3">Program Pelatihan</th>
                            <th class="p-2.5 text-right pr-4 w-36">Jumlah Peserta</th>
                        </tr>
                    </thead>
                    <tbody id="detail_breakdown_body" class="divide-y divide-slate-100 font-medium text-slate-700">
                        <!-- Populated via JS -->
                    </tbody>
                </table>
            </div>
        </div>

        <div class="px-4 py-2.5 bg-slate-50 border-t border-slate-100">
            <button type="button" onclick="document.getElementById('modalDetailPerusahaan').classList.add('hidden')" class="w-full py-1.5 bg-slate-800 hover:bg-slate-900 text-white text-xs font-bold uppercase tracking-wider rounded-xs border-none cursor-pointer transition-colors">
                TUTUP DETAIL
            </button>
        </div>
    </div>
</div>