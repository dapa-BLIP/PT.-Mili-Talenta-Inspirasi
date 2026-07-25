<!-- ========================================================================= -->
<!-- 1. MODAL IMPORT MASTER PELATIHAN (SERAGAM BULK ENGINE SYSTEM)             -->
<!-- ========================================================================= -->
<div id="modalImportPelatihan" class="fixed inset-0 bg-slate-900/60 z-[110] hidden flex items-center justify-center p-4 print:hidden">
    <div class="bg-white w-full max-w-md shadow-2xl border border-slate-200 overflow-hidden rounded-none">
        <div class="px-5 py-3 border-b border-emerald-100 bg-emerald-50 text-emerald-700 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="text-[9px] font-black uppercase tracking-widest bg-emerald-600 text-white px-1.5 py-0.5 rounded-sm">Engine</span>
                <h3 class="text-[10px] font-black uppercase tracking-widest text-emerald-800">PELATIHAN IMPORT SYSTEM</h3>
            </div>
            <button type="button" onclick="document.getElementById('modalImportPelatihan').classList.add('hidden')" class="text-emerald-500 hover:text-emerald-700 font-bold text-sm">&times;</button>
        </div>
        <form action="{{ route('academic.importPelatihan') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="p-5 space-y-4">
                <div class="bg-slate-50 p-3 border border-slate-200">
                    <h4 class="text-[9px] font-black text-slate-700 uppercase tracking-wide mb-1">📢 Catatan Operasional Spreadsheet:</h4>
                    <p class="text-[8px] font-bold text-slate-400 uppercase leading-relaxed">*PENTING: Kolom 'NAMA LENGKAP MENTOR' wajib diisi nama mentor terdaftar. Gunakan pemisah koma (,) untuk komponen penilaian.</p>
                </div>
                <div>
                    <label class="block text-[9px] font-black text-slate-600 uppercase tracking-widest mb-1.5">Pilih File Spreadsheet Pelatihan</label>
                    <input type="file" name="file_excel" accept=".xlsx, .xls" required class="w-full text-[10px] font-bold text-slate-500 bg-white p-1.5 border border-slate-300 rounded-none file:mr-3 file:py-1 file:px-3 file:border-0 file:text-[9px] file:font-black file:uppercase file:tracking-widest file:bg-slate-800 file:text-white hover:file:bg-slate-700 cursor-pointer">
                </div>
            </div>
            <div class="p-4 bg-slate-50 border-t border-slate-100 flex gap-2">
                <button type="button" onclick="document.getElementById('modalImportPelatihan').classList.add('hidden')" class="w-1/2 border border-slate-200 text-slate-400 py-2 text-[9px] font-black uppercase tracking-widest hover:bg-slate-100 rounded-none">BATAL</button>
                <button type="submit" class="w-1/2 bg-emerald-600 text-white py-2 text-[9px] font-black uppercase tracking-widest hover:bg-emerald-700 shadow-sm rounded-none">IMPORT</button>
            </div>
        </form>
    </div>
</div>

<!-- ========================================================================= -->
<!-- 2. FORM MODAL CREATE PELATIHAN                                            -->
<!-- ========================================================================= -->
<div id="modalCreatePelatihan" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-[100] hidden flex items-center justify-center p-4">
    <div class="bg-white w-full max-w-md shadow-xl border border-slate-200 overflow-hidden rounded-none">
        <div class="px-5 py-3 border-b border-slate-100 bg-slate-50 flex justify-between items-center">
            <h3 class="text-[10px] font-black text-slate-800 uppercase tracking-widest">BUAT MASTER PELATIHAN</h3>
            <button type="button" onclick="document.getElementById('modalCreatePelatihan').classList.add('hidden')" class="text-slate-400 hover:text-rose-500 text-lg leading-none">&times;</button>
        </div>
        <form action="{{ route('academic.storePelatihan') }}" method="POST" class="p-5 space-y-4">
            @csrf
            <div>
                <label class="block text-[8px] font-black uppercase tracking-widest text-slate-400 mb-1">Nama Kelas Pelatihan</label>
                <input type="text" name="nama_pelatihan" class="w-full bg-slate-50 border border-slate-200 rounded-none px-2.5 py-1.5 text-[11px] font-medium text-slate-800 focus:outline-none" required placeholder="Panen Kelapa Sawit">
            </div>
            <div>
                <label class="block text-[8px] font-black uppercase tracking-widest text-slate-400 mb-1">Kategori Spektrum</label>
                <select name="spektrum_kategori" class="w-full bg-slate-50 border border-slate-200 rounded-none px-2 py-1.5 text-[10px] font-bold text-slate-600 focus:outline-none" required>
                    <option value="Pengetahuan Teknis">PENGETAHUAN TEKNIS</option>
                    <option value="Pengetahuan Non Teknis">PENGETAHUAN NON TEKNIS</option>
                </select>
            </div>
            <div>
                <label class="block text-[8px] font-black uppercase tracking-widest text-slate-400 mb-1">Pilih Mentor Pengampu</label>
                <select name="trainer_id" class="w-full bg-slate-50 border border-slate-200 rounded-none px-2 py-1.5 text-[10px] font-bold text-slate-600 uppercase focus:outline-none" required>
                    <option value="">-- CHOOSE MENTOR --</option>
                    @foreach($trainers as $t)
                        <option value="{{ $t->id }}">{{ $t->nama_mentor }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-[8px] font-black uppercase tracking-widest text-slate-400 mb-1">Komponen Bobot Nilai</label>
                <input type="text" name="raw_komponen" class="w-full bg-slate-50 border border-slate-200 rounded-none px-2.5 py-1.5 text-[11px] font-medium text-slate-800 focus:outline-none" required placeholder="Tugas, UTS, UAS, Praktik">
            </div>
            <div class="pt-2 flex gap-2">
                <button type="button" onclick="document.getElementById('modalCreatePelatihan').classList.add('hidden')" class="w-1/3 border border-slate-200 text-[9px] font-black uppercase py-2">CANCEL</button>
                <button type="submit" class="w-2/3 bg-slate-900 text-white text-[9px] font-black uppercase py-2">COMMIT TO MASTER</button>
            </div>
        </form>
    </div>
</div>