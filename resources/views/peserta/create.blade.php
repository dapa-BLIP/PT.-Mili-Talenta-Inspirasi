<div id="modalCreatePeserta" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-[100] hidden flex items-center justify-center p-4">
    <div class="bg-white w-full max-w-md shadow-xl border border-slate-200 overflow-hidden rounded-none">
        
        <div class="px-5 py-3 border-b border-slate-100 bg-slate-50 flex justify-between items-center rounded-none">
            <h3 class="text-[10px] font-black text-slate-800 uppercase tracking-widest">PARTICIPANT REGISTRATION FORM</h3>
            <button type="button" onclick="closeCreatePesertaModal()" class="text-slate-400 hover:text-rose-500 font-bold text-lg leading-none transition-colors">&times;</button>
        </div>
        
        <form id="formCreatePeserta" action="{{ route('peserta.store') }}" method="POST" enctype="multipart/form-data" class="p-5 space-y-3.5">
            @csrf
            
            <div class="grid grid-cols-3 gap-3">
                <div class="col-span-2">
                    <label class="block text-[8px] font-black uppercase tracking-widest text-slate-400 mb-1">Full Name</label>
                    <input type="text" name="nama_lengkap" class="w-full bg-slate-50 border border-slate-200 rounded-none px-2.5 py-1.5 text-[11px] font-medium text-slate-800 focus:outline-none focus:border-blue-400 focus:bg-white" placeholder="John Doe" required>
                </div>
                <div>
                    <label class="block text-[8px] font-black uppercase tracking-widest text-slate-400 mb-1">Gender</label>
                    <select name="gender" class="w-full bg-slate-50 border border-slate-200 rounded-none px-2 py-1.5 text-[10px] font-bold text-slate-600 focus:outline-none focus:border-blue-400 focus:bg-white" required>
                        <option value="pria">PRIA</option>
                        <option value="wanita">WANITA</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-[8px] font-black uppercase tracking-widest text-slate-400 mb-1">Place of Birth</label>
                    <input type="text" name="tempat_lahir" class="w-full bg-slate-50 border border-slate-200 rounded-none px-2.5 py-1.5 text-[11px] font-medium text-slate-800 focus:outline-none focus:border-blue-400 focus:bg-white" placeholder="Contoh: Bekasi" required>
                </div>
                <div>
                    <label class="block text-[8px] font-black uppercase tracking-widest text-slate-400 mb-1">Date of Birth</label>
                    <input type="date" name="tanggal_lahir" class="w-full bg-slate-50 border border-slate-200 rounded-none px-2.5 py-1.5 text-[11px] font-medium text-slate-700 focus:outline-none focus:border-blue-400 focus:bg-white" required>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-[8px] font-black uppercase tracking-widest text-slate-400 mb-1">Email Address</label>
                    <input type="email" name="email" class="w-full bg-slate-50 border border-slate-200 rounded-none px-2.5 py-1.5 text-[11px] font-medium text-slate-800 focus:outline-none focus:border-blue-400 focus:bg-white" placeholder="name@company.com" required>
                </div>
                <div>
                    <label class="block text-[8px] font-black uppercase tracking-widest text-slate-400 mb-1">Phone Number</label>
                    <input type="text" name="no_hp" class="w-full bg-slate-50 border border-slate-200 rounded-none px-2.5 py-1.5 text-[11px] font-medium text-slate-800 focus:outline-none focus:border-blue-400 focus:bg-white" placeholder="08xxxxxxxxxx">
                </div>
            </div>

            <div>
                <label class="block text-[8px] font-black uppercase tracking-widest text-slate-400 mb-1">Workplace / Institution</label>
                <input type="text" name="tempat_kerja" class="w-full bg-slate-50 border border-slate-200 rounded-none px-2.5 py-1.5 text-[11px] font-medium text-slate-800 focus:outline-none focus:border-blue-400 focus:bg-white" placeholder="Nama perusahaan atau universitas...">
            </div>

            <div>
                <label class="block text-[8px] font-black uppercase tracking-widest text-slate-400 mb-1">Training Spektrum</label>
                <select name="pelatihan" class="w-full bg-slate-50 border border-slate-200 rounded-none px-2 py-1.5 text-[10px] font-bold text-slate-600 focus:outline-none focus:border-blue-400 focus:bg-white" required>
                    <option value="Pengetahuan Teknis">PENGETAHUAN TEKNIS</option>
                    <option value="Pengetahuan Non Teknis">PENGETAHUAN NON TEKNIS</option>
                </select>
            </div>

            <div>
                <label class="block text-[8px] font-black uppercase tracking-widest text-slate-400 mb-1">Participant Photo</label>
                <div class="border border-dashed border-slate-200 bg-slate-50 p-3 flex flex-col items-center justify-center hover:bg-slate-100 relative cursor-pointer">
                    <span id="foto_label_text" class="text-[10px] font-semibold text-slate-400">Pilih berkas foto profil...</span>
                    <input type="file" name="foto" onchange="document.getElementById('foto_label_text').innerText = this.files[0].name" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                </div>
            </div>

            <div class="pt-2 flex gap-2">
                <button type="button" onclick="closeCreatePesertaModal()" class="w-1/3 border border-slate-200 text-slate-400 py-2 rounded-none text-[9px] font-black uppercase tracking-widest hover:bg-slate-50 transition-colors">
                    CANCEL
                </button>
                <button type="submit" class="w-2/3 bg-slate-900 hover:bg-slate-800 text-white py-2 rounded-none text-[9px] font-black uppercase tracking-widest transition-all shadow-sm">
                    COMMIT TO RECORDS
                </button>
            </div>
        </form>
    </div>
</div>