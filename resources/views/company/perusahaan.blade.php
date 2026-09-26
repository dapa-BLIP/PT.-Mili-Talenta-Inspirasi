<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between w-full h-full print:hidden select-none">
            <div class="flex items-center gap-2">
                <h2 class="text-sm font-black text-slate-700 uppercase tracking-tighter">Manajemen Peserta</h2>
                <span class="text-slate-300 text-xs">/</span>
                <span class="text-[10px] font-bold text-[#22a0f6] uppercase tracking-widest italic">Kelola Data Perusahaan</span>
            </div>
            <div class="text-[10px] font-black text-slate-400 uppercase tracking-widest font-mono">
                Total: {{ count($perusahaans ?? []) }} Mitra
            </div>
        </div>
    </x-slot>

    <div class="py-4 max-w-[1400px] mx-auto px-1 space-y-4">
        <!-- ALERT SYSTEM -->
        @if(session('success'))
            <div class="px-4 py-3 bg-white border-l-4 border-l-emerald-500 border-y border-r border-slate-200 shadow-xs flex items-center justify-between rounded-r-xs select-none">
                <div class="flex items-center gap-3">
                    <div class="p-1 bg-emerald-50 rounded-full text-emerald-600 shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                    </div>
                    <p class="text-[11px] font-bold text-slate-600 tracking-wide">{{ session('success') }}</p>
                </div>
                <button type="button" class="text-slate-400 hover:text-slate-600 font-black text-lg p-1 cursor-pointer" onclick="this.parentElement.remove()">&times;</button>
            </div>
        @endif

        @if ($errors->any())
            <div class="px-4 py-3 bg-white border-l-4 border-l-rose-500 border-y border-r border-slate-200 shadow-xs flex items-center justify-between rounded-r-xs select-none">
                <div class="space-y-0.5">
                    <h4 class="text-[10px] font-black text-rose-800 uppercase tracking-widest">Gagal Memproses Data</h4>
                    <div class="text-[11px] font-bold text-slate-600 tracking-wide">
                        @foreach ($errors->all() as$error)
                            <p>* {{ $error }}</p>
                        @endforeach
                    </div>
                </div>
                <button type="button" class="text-slate-400 hover:text-slate-600 font-black text-lg p-1 cursor-pointer" onclick="this.parentElement.remove()">&times;</button>
            </div>
        @endif

        <!-- FILTER & ACTION BAR -->
        <div class="bg-white border border-slate-200 py-3 px-4 shadow-xs">
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-4">
                <div class="relative w-full sm:w-72">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-2.5 pointer-events-none">
                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="3" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </span>
                    <input type="text" id="live-search-perusahaan" onkeyup="filterPerusahaan()" placeholder="Cari nama perusahaan / PIC..." class="w-full pl-8 pr-3 py-1.5 bg-slate-50 border border-slate-200 text-[10px] font-bold uppercase tracking-wider text-slate-700 focus:outline-none focus:border-[#22a0f6] focus:bg-white placeholder-slate-400 shadow-inner rounded-xs">
                </div>

                <div class="flex flex-wrap items-center gap-2">
                    <button type="button" onclick="document.getElementById('modalCreatePerusahaan').classList.remove('hidden')" class="px-4 py-1.5 bg-[#22a0f6] text-white rounded-xs text-[10px] font-black uppercase tracking-widest hover:bg-blue-600 transition shadow-xs cursor-pointer border-none">+ Tambah Perusahaan</button>
                </div>
            </div>
        </div>

        <!-- TABLE VIEW DATA PERUSAHAAN -->
        <div class="bg-white border border-slate-200 shadow-sm overflow-x-auto rounded-xs">
            <table class="w-full text-left border-collapse whitespace-nowrap select-none" id="table-perusahaan">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200">
                        <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase w-16 text-center">No.</th>
                        <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase min-w-[260px]">Nama Perusahaan</th>
                        <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase min-w-[200px]">PIC & WhatsApp</th>
                        <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase w-36 text-center">Total Program</th>
                        <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase w-36 text-center">Total Peserta</th>
                        <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase w-24 text-center">Status</th>
                        <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase w-24 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($perusahaans as $index =>$item)
                    @php
                        // Aman dari tabrakan tanda kutip dua HTML
                        $itemBase64 = base64_encode(json_encode($item));
                    @endphp
                    <tr data-item="{{ $itemBase64 }}"
                        onclick="bukaModalDetailPerusahaanFromRow(this)" 
                        class="hover:bg-slate-50 transition-colors row-perusahaan cursor-pointer group">
                        
                        <td class="px-6 py-4 text-[11px] font-black text-slate-400 text-center index-perusahaan font-mono">{{ str_pad($index + 1, 3, '0', STR_PAD_LEFT) }}</td>
                        
                        <!-- Nama Perusahaan & Email -->
                        <td class="px-6 py-4 text-[12px] font-black text-slate-700 uppercase searchable-item group-hover:text-[#22a0f6] transition-colors">
                            <div>{{ $item->nama_perusahaan }}</div>
                            <div class="text-[11px] font-semibold text-slate-500 lowercase font-mono">{{ $item->email_perusahaan ?: '-' }}</div>
                        </td>

                        <!-- PIC & Kontak WA -->
                        <td class="px-6 py-4 searchable-item">
                            <div class="text-[11px] font-bold text-slate-700 uppercase tracking-tight">{{ $item->penanggung_jawab ?: '-' }}</div>
                            <div class="text-[12px] text-emerald-600 font-mono font-semibold">{{ $item->kontak_pic ?: '-' }}</div>
                        </td>

                        <!-- Total Program Pelatihan Diikuti -->
                        <td class="px-6 py-4 text-center">
                            <span class="px-2.5 py-1 text-[10.5px] font-black bg-slate-100 text-slate-700 border border-slate-200 rounded-xs font-mono">
                                {{ $item->total_program_count ?? 0 }} Pelatihan
                            </span>
                        </td>

                        <!-- Total Peserta Terdaftar -->
                        <td class="px-6 py-4 text-center">
                            <span class="px-2.5 py-1 text-[10.5px] font-black bg-blue-50 text-blue-700 border border-blue-100 rounded-xs font-mono">
                                {{ $item->pesertas_count ?? 0 }} Peserta
                            </span>
                        </td>

                        <!-- Status Badge -->
                        <td class="px-6 py-4 text-center">
                            @if($item->status === 'AKTIF')
                                <span class="text-[10px] font-black px-2 py-0.5 rounded-sm bg-emerald-50 text-emerald-600 border border-emerald-200">AKTIF</span>
                            @elseif($item->status === 'PENDING')
                                <span class="text-[10px] font-black px-2 py-0.5 rounded-sm bg-amber-50 text-amber-600 border border-amber-200">PENDING</span>
                            @else
                                <span class="text-[10px] font-black px-2 py-0.5 rounded-sm bg-rose-50 text-rose-600 border border-rose-200">NON-AKTIF</span>
                            @endif
                        </td>

                        <!-- Tombol Aksi -->
                        <td class="px-6 py-4 text-right pr-6" onclick="event.stopPropagation()">
                            <div class="flex justify-end gap-1.5 items-center">
                                <button type="button" 
                                        data-url="{{ route('perusahaan.update', $item->id) }}"
                                        onclick="bukaModalEditPerusahaanFromRow(this)" 
                                        title="Edit Perusahaan" 
                                        class="p-1.5 border border-slate-200 text-[#22a0f6] hover:bg-[#22a0f6] hover:text-white transition-colors cursor-pointer rounded-xs shadow-2xs">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                </button>
                                <button type="button" 
                                        data-url="{{ route('perusahaan.destroy', $item->id) }}"
                                        data-nama="{{ $item->nama_perusahaan }}"
                                        data-peserta="{{ $item->pesertas_count ?? 0 }}"
                                        onclick="mintaVerifikasiHapusFromBtn(this)" 
                                        title="Hapus Perusahaan" 
                                        class="p-1.5 border border-slate-200 text-rose-500 hover:bg-rose-500 hover:text-white transition-colors cursor-pointer rounded-xs shadow-2xs">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke-width="2.5"></path></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="px-6 py-8 text-center text-[10px] font-black text-slate-400 uppercase tracking-widest italic">Belum ada data perusahaan mitra.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- MODAL SECTION -->
    <div class="print:hidden">
        @include('company.data-perusahaan.create-data-perusahaan')
        @include('company.data-perusahaan.edit-data-perusahaan')
        @include('company.data-perusahaan.detail-data-perusahaan')
        @include('company.data-perusahaan.delete-data-perusahaan')

        <!-- MODAL WARNING BLOCKED DELETE (BERSIH TANPA BLUR GLITCH) -->
        <div id="modalBlockedDelete" class="fixed inset-0 z-[10000] flex items-center justify-center bg-slate-900/60 p-4 hidden transition-all duration-200 select-none">
            <div class="bg-white border border-slate-200 shadow-2xl max-w-sm w-full p-6 text-center rounded-xs">
                <div class="w-12 h-12 bg-amber-50 text-amber-600 rounded-full flex items-center justify-center mx-auto mb-3.5 border border-amber-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>

                <h3 class="text-xs font-black text-slate-800 uppercase tracking-wider mb-1">Perusahaan Tidak Bisa Dihapus</h3>
                <p class="text-[11px] font-medium text-slate-500 leading-relaxed mb-4">
                    Masih terdapat <span id="blocked-peserta-count" class="font-bold text-slate-800 font-mono">0</span> peserta yang terdaftar aktif di bawah perusahaan ini. Harap pindahkan atau hapus data peserta terlebih dahulu.
                </p>

                <button type="button" 
                        onclick="document.getElementById('modalBlockedDelete').classList.add('hidden')" 
                        class="w-full py-2 bg-slate-800 hover:bg-slate-900 text-white text-[10px] font-black uppercase tracking-widest rounded-xs transition-colors cursor-pointer border-none shadow-xs">
                    Saya Mengerti
                </button>
            </div>
        </div>
    </div>

    <!-- JAVASCRIPT LOGIC -->
    <script>
        let targetHapusNama = '';

        // 1. AUTO MOUNT KE BODY & PENGUNCI ENTER KEY
        document.addEventListener('DOMContentLoaded', () => {
            const modalIds = [
                'modalCreatePerusahaan', 
                'modalEditPerusahaan', 
                'modalDetailPerusahaan', 
                'modalSecureDelete', 
                'modalBlockedDelete'
            ];

            modalIds.forEach(id => {
                const el = document.getElementById(id);
                if (el && el.parentElement !== document.body) {
                    document.body.appendChild(el);
                }
            });

            // Cegah submit tak sengaja via Enter
            document.querySelectorAll('#modalCreatePerusahaan form, #formEditPerusahaan').forEach(form => {
                form.addEventListener('keydown', function(e) {
                    if (e.key === 'Enter' && e.target.tagName === 'INPUT' && e.target.type !== 'submit' && e.target.id !== 'live-search-perusahaan') {
                        e.preventDefault();
                        const inputs = Array.from(form.querySelectorAll('input:not([type="hidden"]):not([disabled]), select:not([disabled]), textarea:not([disabled])'));
                        const idx = inputs.indexOf(e.target);
                        if (idx > -1 && idx + 1 < inputs.length) {
                            inputs[idx + 1].focus();
                        }
                        return false;
                    }
                });

                form.addEventListener('submit', function() {
                    const btn = form.querySelector('button[type="submit"]');
                    if (btn) {
                        btn.disabled = true;
                        btn.classList.add('opacity-70', 'cursor-not-allowed');
                        btn.textContent = 'Menyimpan...';
                    }
                });
            });
        });

        // 2. LIVE SEARCH FILTER
        function filterPerusahaan() {
            const query = document.getElementById('live-search-perusahaan').value.trim().toLowerCase();
            const rows = document.querySelectorAll('#table-perusahaan .row-perusahaan');
            let count = 0;

            rows.forEach(row => {
                const items = row.querySelectorAll('.searchable-item');
                let match = false;
                items.forEach(it => {
                    if (it.textContent.toLowerCase().includes(query)) match = true;
                });

                if (match || query === '') {
                    row.style.display = '';
                    count++;
                    const indexElem = row.querySelector('.index-perusahaan');
                    if (indexElem) indexElem.textContent = String(count).padStart(3, '0');
                } else {
                    row.style.display = 'none';
                }
            });
        }

        // 3. PARSER DATA ROW DENGAN BASE64
        function getRowPerusahaanData(row) {
            const raw = row.getAttribute('data-item');
            if (!raw) return {};
            try {
                return JSON.parse(atob(raw));
            } catch(e) {
                return JSON.parse(raw);
            }
        }

        // 4. DETAIL PERUSAHAAN CONTROLLER
        function bukaModalDetailPerusahaanFromRow(row) {
            const data = getRowPerusahaanData(row);
            bukaModalDetailPerusahaan(data);
        }

        function bukaModalDetailPerusahaan(data) {
            if (document.getElementById('detail_nama_perusahaan')) {
                document.getElementById('detail_nama_perusahaan').textContent = data.nama_perusahaan || '-';
            }
            if (document.getElementById('detail_pic')) {
                document.getElementById('detail_pic').textContent = data.penanggung_jawab || '-';
            }
            if (document.getElementById('detail_kontak')) {
                document.getElementById('detail_kontak').textContent = data.kontak_pic || '-';
            }
            if (document.getElementById('detail_email')) {
                document.getElementById('detail_email').textContent = data.email_perusahaan || '-';
            }
            if (document.getElementById('detail_alamat')) {
                document.getElementById('detail_alamat').textContent = data.alamat || 'Alamat belum diinputkan.';
            }
            
            const totalSDM = data.pesertas_count || 0;
            if (document.getElementById('detail_peserta_count')) {
                document.getElementById('detail_peserta_count').textContent = 'TOTAL: ' + totalSDM + ' PESERTA';
            }

            const tbody = document.getElementById('detail_breakdown_body');
            if (tbody) {
                tbody.innerHTML = '';
                const breakdown = data.breakdown_program || {};
                const keys = Object.keys(breakdown);

                if (keys.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="2" class="p-3 text-center text-slate-400 italic text-xs">Belum ada peserta yang terdaftar di program pelatihan.</td></tr>';
                } else {
                    keys.forEach(prog => {
                        const count = breakdown[prog];
                        const tr = document.createElement('tr');
                        tr.className = "hover:bg-slate-50/80 transition-colors";
                        tr.innerHTML = `
                            <td class="p-2.5 pl-3 font-semibold text-slate-700 uppercase text-xs">${prog}</td>
                            <td class="p-2.5 text-right pr-4 font-mono font-bold text-blue-600 text-xs">${count} Peserta</td>
                        `;
                        tbody.appendChild(tr);
                    });
                }
            }

            const badge = document.getElementById('detail_status_badge');
            if (badge) {
                if (data.status === 'AKTIF') {
                    badge.className = "inline-block text-[10px] font-bold uppercase px-2 py-0.5 rounded-xs bg-emerald-50 text-emerald-600 border border-emerald-200 mt-1";
                    badge.textContent = "AKTIF";
                } else if (data.status === 'PENDING') {
                    badge.className = "inline-block text-[10px] font-bold uppercase px-2 py-0.5 rounded-xs bg-amber-50 text-amber-600 border border-amber-200 mt-1";
                    badge.textContent = "PENDING";
                } else {
                    badge.className = "inline-block text-[10px] font-bold uppercase px-2 py-0.5 rounded-xs bg-rose-50 text-rose-600 border border-rose-200 mt-1";
                    badge.textContent = "NON-AKTIF";
                }
            }

            document.getElementById('modalDetailPerusahaan')?.classList.remove('hidden');
        }

        // 5. EDIT PERUSAHAAN CONTROLLER
        function bukaModalEditPerusahaanFromRow(btn) {
            const row = btn.closest('tr');
            const data = getRowPerusahaanData(row);
            const updateUrl = btn.getAttribute('data-url');

            const form = document.getElementById('formEditPerusahaan');
            if (form) form.action = updateUrl;

            if (document.getElementById('edit_nama_perusahaan')) {
                document.getElementById('edit_nama_perusahaan').value = data.nama_perusahaan || '';
            }
            if (document.getElementById('edit_penanggung_jawab')) {
                document.getElementById('edit_penanggung_jawab').value = data.penanggung_jawab || '';
            }
            if (document.getElementById('edit_kontak_pic')) {
                document.getElementById('edit_kontak_pic').value = data.kontak_pic || '';
            }
            if (document.getElementById('edit_email_perusahaan')) {
                document.getElementById('edit_email_perusahaan').value = data.email_perusahaan || '';
            }
            if (document.getElementById('edit_status')) {
                document.getElementById('edit_status').value = data.status || 'AKTIF';
            }
            if (document.getElementById('edit_alamat')) {
                document.getElementById('edit_alamat').value = data.alamat || '';
            }

            document.getElementById('modalEditPerusahaan')?.classList.remove('hidden');
        }

        // 6. DELETE PERUSAHAAN CONTROLLER
        function mintaVerifikasiHapusFromBtn(btn) {
            const deleteUrl = btn.getAttribute('data-url');
            const namaTarget = btn.getAttribute('data-nama') || '';
            const pesertacount = parseInt(btn.getAttribute('data-peserta') || '0', 10);

            if (pesertacount > 0) {
                const countElem = document.getElementById('blocked-peserta-count');
                if (countElem) countElem.textContent = pesertacount;
                document.getElementById('modalBlockedDelete')?.classList.remove('hidden');
                return;
            }

            targetHapusNama = namaTarget.trim().toUpperCase();
            const form = document.getElementById('formSecureDelete');
            if (form) form.action = deleteUrl;

            if (document.getElementById('delete-target-name')) {
                document.getElementById('delete-target-name').textContent = targetHapusNama;
            }
            if (document.getElementById('input-delete-verify')) {
                document.getElementById('input-delete-verify').value = '';
            }
            
            const btnConfirm = document.getElementById('btn-confirm-delete-final');
            if (btnConfirm) {
                btnConfirm.disabled = true;
                btnConfirm.className = "w-2/3 bg-slate-200 text-slate-400 py-2 text-[9px] font-black uppercase tracking-widest rounded-xs cursor-not-allowed";
            }
            document.getElementById('modalSecureDelete')?.classList.remove('hidden');
        }

        function evaluasiTombolHapus() {
            const input = document.getElementById('input-delete-verify');
            const valUser = input ? input.value.trim().toUpperCase() : '';
            const btn = document.getElementById('btn-confirm-delete-final');
            if (!btn) return;

            if (valUser === targetHapusNama && targetHapusNama !== '') {
                btn.disabled = false;
                btn.className = "w-2/3 bg-rose-600 text-white py-2 text-[9px] font-black uppercase tracking-widest hover:bg-rose-700 rounded-xs cursor-pointer shadow-xs transition-colors";
            } else {
                btn.disabled = true;
                btn.className = "w-2/3 bg-slate-200 text-slate-400 py-2 text-[9px] font-black uppercase tracking-widest rounded-xs cursor-not-allowed";
            }
        }
    </script>
</x-app-layout>