<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between w-full print:hidden select-none">
            <div class="flex items-center gap-2">
                <h2 class="text-xs font-black text-slate-800 uppercase tracking-[0.2em]">
                    System Feedback
                </h2>
                <span class="text-slate-300 text-xs">/</span>
                <span class="text-[10px] font-bold text-[#22a0f6] uppercase tracking-widest italic">Real Feedback</span>
            </div>
            
            <a href="{{ $googleFormUrl ?? 'https://docs.google.com/forms/' }}" target="_blank" style="border-radius: 0.125rem;" class="bg-[#22a0f6] hover:bg-blue-600 text-white px-3.5 py-1.5 text-[9px] font-black uppercase tracking-widest transition-all flex items-center gap-1.5 shadow-xs">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                </svg>
                Open Google Form
            </a>
        </div>
    </x-slot>

    <div class="space-y-4">
        <!-- NOTIFIKASI SUKSES -->
        @if(session('success'))
            <div style="border-radius: 0.125rem;" class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-2.5 text-xs font-bold uppercase tracking-wider flex items-center justify-between shadow-xs">
                <div class="flex items-center gap-2">
                    <span style="border-radius: 0.125rem;" class="text-[10px] font-black uppercase bg-emerald-600 text-white px-1.5 py-0.5">OK</span>
                    <span>{{ session('success') }}</span>
                </div>
                <button onclick="this.parentElement.remove()" class="text-emerald-500 font-black cursor-pointer bg-transparent border-none">✕</button>
            </div>
        @endif

        <!-- 1. KOTAK ATAS: TOOLBAR FILTER & TOTAL DATA -->
        <div style="border-radius: 0.125rem;" class="bg-white border border-slate-200 p-3.5 sm:p-4 shadow-sm flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3 sm:gap-4">
            
            <!-- KIRI: JUDUL + TOTAL MASALAH -->
            <div class="flex items-center justify-between sm:justify-start gap-3">
                <h3 class="text-[11px] font-black text-slate-700 uppercase tracking-widest italic">Feedback Stream</h3>
                <span style="border-radius: 0.125rem;" class="text-[9px] font-black text-[#22a0f6] bg-blue-50 border border-blue-100 px-2.5 py-1 uppercase tracking-wider font-mono">
                    TOTAL: {{ sprintf('%02d', $feedbacks->count()) }} MASALAH
                </span>
            </div>

            <!-- KANAN: TAB FILTER TOMBOL (RESPONSIF 4 KOLOM DI MOBILE) -->
            <div style="border-radius: 0.125rem;" class="grid grid-cols-4 sm:flex items-center gap-1 bg-slate-100 p-1 border border-slate-200 w-full sm:w-auto text-center">
                <a href="{{ route('feedback.index') }}" style="border-radius: 0.125rem;" 
                    class="px-2 sm:px-4 py-1.5 text-[9px] font-black uppercase tracking-wider transition-all text-center flex items-center justify-center {{ $selectedCategory == 'all' ? 'bg-white text-slate-900 shadow-xs' : 'text-slate-500 hover:text-slate-900' }}">
                    Semua
                </a>
                <a href="{{ route('feedback.index', ['category' => 'Berat']) }}" style="border-radius: 0.125rem;" 
                    class="px-2 sm:px-4 py-1.5 text-[9px] font-black uppercase tracking-wider transition-all text-center flex items-center justify-center {{ strtolower($selectedCategory) == 'berat' ? 'bg-rose-600 text-white shadow-xs' : 'text-slate-500 hover:text-rose-600' }}">
                    Berat
                </a>
                <a href="{{ route('feedback.index', ['category' => 'Sedang']) }}" style="border-radius: 0.125rem;" 
                    class="px-2 sm:px-4 py-1.5 text-[9px] font-black uppercase tracking-wider transition-all text-center flex items-center justify-center {{ strtolower($selectedCategory) == 'sedang' ? 'bg-amber-500 text-white shadow-xs' : 'text-slate-500 hover:text-amber-600' }}">
                    Sedang
                </a>
                <a href="{{ route('feedback.index', ['category' => 'Umum']) }}" style="border-radius: 0.125rem;" 
                    class="px-2 sm:px-4 py-1.5 text-[9px] font-black uppercase tracking-wider transition-all text-center flex items-center justify-center {{ strtolower($selectedCategory) == 'umum' ? 'bg-blue-500 text-white shadow-xs' : 'text-slate-500 hover:text-blue-600' }}">
                    Umum
                </a>
            </div>

        </div>

        <!-- 2. KOTAK BAWAH: TABEL LIST FEEDBACK -->
        <div style="border-radius: 0.125rem;" class="bg-white border border-slate-200 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse table-fixed select-none">
                    <thead>
                        <tr class="bg-white border-b border-slate-200 text-slate-400">
                            <th class="w-[6%] px-4 py-3 text-[9px] font-black uppercase tracking-widest text-center">No.</th>
                            <th class="w-[16%] px-4 py-3 text-[9px] font-black uppercase tracking-widest">Profile</th>
                            <th class="w-[14%] px-4 py-3 text-[9px] font-black uppercase tracking-widest text-center">Kategori</th>
                            <th class="w-[36%] px-4 py-3 text-[9px] font-black uppercase tracking-widest">Message Payload & Solusi</th>
                            <th class="w-[12%] px-4 py-3 text-center text-[9px] font-black uppercase tracking-widest">Received</th>
                            <th class="w-[16%] px-4 py-3 text-center text-[9px] font-black uppercase tracking-widest">Action / Repair</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($feedbacks as $index => $f)
                        <tr class="hover:bg-slate-50/60 transition-colors">
                            <!-- NO -->
                            <td style="padding-top: 0.875rem; padding-bottom: 0.875rem;" class="px-4 text-[10px] font-black text-slate-400 text-center font-mono align-top">
                                {{ str_pad($index + 1, 3, '0', STR_PAD_LEFT) }}
                            </td>

                            <!-- PROFIL -->
                            <td style="padding-top: 0.875rem; padding-bottom: 0.875rem;" class="px-4 align-top">
                                <div class="text-[11.5px] font-black text-slate-800 uppercase tracking-tight truncate" title="{{ $f->nama_peserta ?: 'ANONYMOUS' }}">
                                    {{ $f->nama_peserta ?: 'ANONYMOUS' }}
                                </div>
                                @if($f->kegiatan)
                                    <span class="text-[8.5px] font-bold text-slate-400 uppercase tracking-wider block mt-0.5 truncate" title="{{ $f->kegiatan }}">
                                        {{ $f->kegiatan }}
                                    </span>
                                @endif
                            </td>

                            <!-- KATEGORI BADGE -->
                            <td style="padding-top: 0.875rem; padding-bottom: 0.875rem;" class="px-4 text-center align-top">
                                @php
                                    $kat = strtolower($f->kategori);
                                    if ($kat == 'berat' || $kat == 'bug' || str_contains($kat, 'tidak puas')) {
                                        $badgeStyle = "bg-rose-100 text-rose-800 border border-rose-200";
                                        $label = "● BERAT";
                                    } elseif ($kat == 'sedang' || $kat == 'performa' || str_contains($kat, 'kurang puas')) {
                                        $badgeStyle = "bg-amber-100 text-amber-800 border border-amber-200";
                                        $label = "○ SEDANG";
                                    } else {
                                        $badgeStyle = "bg-blue-100 text-blue-800 border border-blue-200";
                                        $label = "● UMUM";
                                    }
                                @endphp
                                <span style="border-radius: 0.125rem;" class="inline-block px-2.5 py-1 text-[8.5px] font-black uppercase tracking-wider shadow-2xs {{ $badgeStyle }}">
                                    {{ $label }}
                                </span>
                            </td>

                            <!-- PESAN & SOLUSI -->
                            <td style="padding-top: 0.875rem; padding-bottom: 0.875rem;" class="px-4 align-top">
                                <div class="text-[11px] font-medium text-slate-700 leading-relaxed break-words whitespace-pre-line">
                                    {{ $f->pesan ?? '-' }}
                                </div>

                                @if($f->status == 'resolved' && $f->solusi)
                                    <div style="border-radius: 0.125rem;" class="mt-2 p-2.5 bg-emerald-50 border border-emerald-200 text-emerald-800 shadow-2xs">
                                        <div class="text-[8px] font-black uppercase tracking-widest text-emerald-700 flex items-center gap-1">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                            </svg>
                                            CATATAN PERBAIKAN:
                                        </div>
                                        <div class="text-[10px] font-medium mt-1 leading-snug">
                                            {{ $f->solusi }}
                                        </div>
                                    </div>
                                @endif
                            </td>

                            <!-- TANGGAL -->
                            <td style="padding-top: 0.875rem; padding-bottom: 0.875rem;" class="px-4 text-center align-top text-slate-400 font-mono text-[10px] tabular-nums">
                                @if($f->created_at)
                                    <div>{{ $f->created_at->format('Y-m-d') }}</div>
                                    <div class="text-[8px] font-bold text-slate-300 uppercase tracking-tighter mt-0.5">{{ $f->created_at->format('H:i:s') }}</div>
                                @else
                                    {{ date('Y-m-d') }}
                                @endif
                            </td>

                            <!-- TOMBOL ACTION REPAIR & DELETE -->
                            <td style="padding-top: 0.875rem; padding-bottom: 0.875rem;" class="px-4 text-center align-top">
                                <div class="flex items-center justify-center gap-1.5 flex-wrap">
                                    @if($f->status == 'resolved')
                                        <button type="button" onclick="openRepairModal({{ $f->id }}, @js($f->pesan), @js($f->solusi))" title="Lihat/Ubah Catatan Perbaikan" style="border-radius: 0.125rem;" 
                                            class="inline-flex items-center gap-1 text-[8.5px] font-black text-emerald-800 bg-emerald-100 hover:bg-emerald-200 border border-emerald-300 px-2.5 py-1 uppercase tracking-wider shadow-2xs transition-colors cursor-pointer">
                                            ✓ RESOLVED
                                        </button>
                                    @else
                                        <button type="button" onclick="openRepairModal({{ $f->id }}, @js($f->pesan), '')" style="border-radius: 0.125rem;" 
                                            class="bg-amber-500 hover:bg-amber-600 text-white px-3 py-1 text-[8.5px] font-black uppercase tracking-widest transition-all shadow-2xs flex items-center justify-center gap-1 cursor-pointer border-none">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                            REPAIR
                                        </button>
                                    @endif

                                    <button type="button" onclick="openDeleteFeedbackModal({{ $f->id }}, @js(mb_substr($f->pesan, 0, 45) . '...'))" title="Hapus Feedback" class="p-1 bg-white border border-slate-200 hover:bg-rose-600 hover:text-white text-rose-500 transition-colors shadow-2xs rounded-xs cursor-pointer">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke-width="2.5"></path></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" style="padding-top: 2.5rem; padding-bottom: 2.5rem;" class="text-center text-[10px] font-black text-slate-300 uppercase tracking-[0.3em]">
                                TIDAK ADA DATA FEEDBACK UNTUK KATEGORI INI
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- MODAL POPUP REPAIR FEEDBACK -->
    <div id="repairModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs hidden items-center justify-center z-50 p-4 transition-all duration-150 select-none">
        <div class="fixed inset-0" onclick="closeRepairModal()"></div>
        <div style="border-radius: 0.125rem;" class="bg-white border border-slate-200 shadow-2xl w-full max-w-lg p-6 relative z-10 animate-in fade-in zoom-in-95 duration-150 my-auto">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3 mb-4">
                <h3 class="text-xs font-black text-slate-800 uppercase tracking-widest flex items-center gap-2">
                    <span class="h-2.5 w-2.5 bg-amber-500 inline-block"></span>
                    FORM PERBAIKAN / SOLUSI FEEDBACK
                </h3>
                <button type="button" onclick="closeRepairModal()" class="text-slate-400 hover:text-slate-700 font-bold text-lg leading-none cursor-pointer p-0.5 border-none bg-transparent">&times;</button>
            </div>

            <form id="repairForm" method="POST" action="">
                @csrf
                @method('PUT')

                <div class="space-y-4">
                    <div>
                        <label class="block text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">Pesan Masukan Peserta:</label>
                        <div id="modalPesanUser" style="border-radius: 0.125rem;" class="text-[11px] font-medium text-slate-700 bg-slate-50 border border-slate-200 p-3 italic leading-relaxed max-h-36 overflow-y-auto">
                            -
                        </div>
                    </div>

                    <div>
                        <label class="block text-[9px] font-black text-slate-800 uppercase tracking-widest mb-1">Catatan Perbaikan / Solusi <span class="text-rose-500">*</span></label>
                        <textarea id="modalSolusiTextarea" name="solusi" rows="4" required placeholder="Tuliskan tindakan/penanganan yang sudah dilakukan..." style="border-radius: 0.125rem;"
                            class="w-full text-[11px] font-medium text-slate-800 border border-slate-300 p-3 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 transition-all placeholder:text-slate-400 bg-white"></textarea>
                    </div>
                </div>

                <div class="mt-6 flex items-center justify-end gap-2 border-t border-slate-100 pt-4">
                    <button type="button" onclick="closeRepairModal()" style="border-radius: 0.125rem;" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 text-[9px] font-black uppercase tracking-widest transition-all cursor-pointer border-none">
                        BATAL
                    </button>
                    <button type="submit" style="border-radius: 0.125rem;" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-[9px] font-black uppercase tracking-widest transition-all shadow-xs cursor-pointer border-none">
                        SIMPAN & TANDAI SELESAI ✓
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL CONFIRM DELETE FEEDBACK -->
    <div id="modalDeleteFeedback" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs hidden items-center justify-center z-50 p-4 transition-all duration-150 select-none">
        <div class="fixed inset-0" onclick="closeDeleteFeedbackModal()"></div>
        <div style="border-radius: 0.125rem;" class="bg-white border border-rose-300 shadow-2xl w-full max-w-sm p-5 relative z-10 animate-in fade-in zoom-in-95 duration-150 my-auto">
            <div class="flex items-center gap-2.5 text-rose-600 border-b border-rose-100 pb-3 mb-3">
                <span class="text-lg">⚠️</span>
                <h3 class="text-xs font-black uppercase tracking-wider text-rose-800">Hapus Data Feedback</h3>
            </div>
            <p class="text-xs text-slate-600 font-medium leading-relaxed">
                Yakin ingin menghapus feedback ini secara permanen?
            </p>
            <p id="deleteFeedbackTarget" class="text-[11px] font-bold text-slate-800 bg-slate-50 border border-slate-200 p-2 mt-2 rounded-xs truncate italic">
                -
            </p>
            <div class="mt-5 flex items-center justify-end gap-2 border-t border-slate-100 pt-3">
                <button type="button" onclick="closeDeleteFeedbackModal()" style="border-radius: 0.125rem;" class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-600 text-[9px] font-black uppercase tracking-widest transition-all cursor-pointer border-none">
                    BATAL
                </button>
                <form id="formDeleteFeedback" action="" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" style="border-radius: 0.125rem;" class="px-4 py-1.5 bg-rose-600 hover:bg-rose-700 text-white text-[9px] font-black uppercase tracking-widest transition-all shadow-xs cursor-pointer border-none">
                        HAPUS
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- JAVASCRIPT HANDLER FEEDBACK -->
    <script>
        function openRepairModal(id, pesan, solusi = '') {
            const modal = document.getElementById('repairModal');
            const form = document.getElementById('repairForm');
            const pesanBox = document.getElementById('modalPesanUser');
            const solusiBox = document.getElementById('modalSolusiTextarea');

            if (form) form.action = `/feedback/${id}/resolve`;
            if (pesanBox) pesanBox.textContent = `"${pesan}"`;
            if (solusiBox) solusiBox.value = solusi || '';

            if (modal) {
                modal.classList.remove('hidden');
                modal.classList.add('flex');
            }
        }

        function closeRepairModal() {
            const modal = document.getElementById('repairModal');
            if (modal) {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }
        }

        function openDeleteFeedbackModal(id, previewText) {
            const modal = document.getElementById('modalDeleteFeedback');
            const form = document.getElementById('formDeleteFeedback');
            const targetText = document.getElementById('deleteFeedbackTarget');

            if (form) form.action = `/feedback/${id}`;
            if (targetText) targetText.textContent = previewText || '-';

            if (modal) {
                modal.classList.remove('hidden');
                modal.classList.add('flex');
            }
        }

        function closeDeleteFeedbackModal() {
            const modal = document.getElementById('modalDeleteFeedback');
            if (modal) {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }
        }

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeRepairModal();
                closeDeleteFeedbackModal();
            }
        });
    </script>
</x-app-layout>