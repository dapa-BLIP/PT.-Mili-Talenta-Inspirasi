<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between w-full print:hidden">
            <div class="flex items-center gap-2">
                <h2 class="text-xs font-black text-slate-800 uppercase tracking-[0.2em]">
                    System Feedback
                </h2>
                <span class="text-slate-300 text-xs">/</span>
                <span class="text-[10px] font-bold text-[#22a0f6] uppercase tracking-widest italic">Live Stream Feed</span>
            </div>
            
            <a href="https://docs.google.com/forms/" target="_blank" class="bg-[#22a0f6] hover:bg-blue-600 text-white px-4 py-1.5 rounded-sm text-[9px] font-black uppercase tracking-widest transition-all flex items-center gap-1.5 shadow-sm">
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
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-none text-xs font-bold uppercase tracking-wider flex items-center justify-between">
                <span>{{ session('success') }}</span>
                <button onclick="this.parentElement.remove()" class="text-emerald-500 font-black">✕</button>
            </div>
        @endif

        <!-- TABEL FEEDBACK WITH FILTER HEADER -->
        <div class="bg-white border border-slate-200 rounded-none shadow-sm overflow-hidden">
            <!-- HEADER BAR TABEL -->
            <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50 flex flex-wrap items-center justify-between gap-3">
                
                <!-- KIRI: JUDUL + TOTAL MASALAH -->
                <div class="flex items-center gap-3">
                    <h3 class="text-[11px] font-black text-slate-500 uppercase tracking-widest italic">Feedback Table</h3>
                    <span class="text-[9px] font-black text-[#22a0f6] bg-blue-50 border border-blue-100 px-2.5 py-0.5 uppercase tracking-wider">
                        TOTAL: {{ sprintf('%02d', $feedbacks->count()) }} MASALAH
                    </span>
                </div>

                <!-- KANAN: TAB FILTER TOMBOL -->
                <div class="flex items-center gap-1 bg-slate-200/60 p-0.5 rounded-none">
                    <a href="{{ route('feedback.index') }}" class="px-3 py-1 text-[8px] font-black uppercase tracking-wider {{ $selectedCategory == 'all' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-500 hover:text-slate-900' }}">Semua</a>
                    <a href="{{ route('feedback.index', ['category' => 'Berat']) }}" class="px-3 py-1 text-[8px] font-black uppercase tracking-wider {{ $selectedCategory == 'Berat' ? 'bg-rose-600 text-white shadow-sm' : 'text-slate-500 hover:text-rose-600' }}">Berat</a>
                    <a href="{{ route('feedback.index', ['category' => 'Sedang']) }}" class="px-3 py-1 text-[8px] font-black uppercase tracking-wider {{ $selectedCategory == 'Sedang' ? 'bg-amber-500 text-white shadow-sm' : 'text-slate-500 hover:text-amber-600' }}">Sedang</a>
                    <a href="{{ route('feedback.index', ['category' => 'Umum']) }}" class="px-3 py-1 text-[8px] font-black uppercase tracking-wider {{ $selectedCategory == 'Umum' ? 'bg-blue-500 text-white shadow-sm' : 'text-slate-500 hover:text-blue-600' }}">Umum</a>
                </div>

            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse table-fixed">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200 text-slate-400">
                            <th class="w-[6%] px-4 py-3 text-[9px] font-black uppercase tracking-widest text-center">No.</th>
                            <th class="w-[14%] px-4 py-3 text-[9px] font-black uppercase tracking-widest">Profile</th>
                            <th class="w-[12%] px-4 py-3 text-[9px] font-black uppercase tracking-widest text-center">Kategori</th>
                            <th class="w-[40%] px-4 py-3 text-[9px] font-black uppercase tracking-widest">Message Payload & Solusi</th>
                            <th class="w-[12%] px-4 py-3 text-center text-[9px] font-black uppercase tracking-widest">Received</th>
                            <th class="w-[16%] px-4 py-3 text-center text-[9px] font-black uppercase tracking-widest">Action / Repair</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($feedbacks as $index => $f)
                        <tr class="hover:bg-slate-50/40 transition-colors">
                            <!-- NO -->
                            <td class="px-4 py-4 text-[11px] font-black text-slate-400 text-center font-mono align-top">
                                {{ str_pad($index + 1, 3, '0', STR_PAD_LEFT) }}
                            </td>

                            <!-- PROFIL -->
                            <td class="px-4 py-4 align-top">
                                <div class="text-[11px] font-black text-slate-800 uppercase tracking-tight">
                                    ANONYMOUS
                                </div>
                            </td>

                            <!-- KATEGORI BADGE -->
                            <td class="px-4 py-4 text-center align-top">
                                @php
                                    $kat = strtolower($f->kategori);
                                    if ($kat == 'berat') {
                                        $badgeStyle = "bg-rose-50 border-rose-200 text-rose-600 font-black";
                                        $label = "🔴 BERAT";
                                    } elseif ($kat == 'sedang') {
                                        $badgeStyle = "bg-amber-50 border-amber-200 text-amber-600 font-black";
                                        $label = "🟡 SEDANG";
                                    } else {
                                        $badgeStyle = "bg-blue-50 border-blue-200 text-blue-600 font-black";
                                        $label = "🔵 UMUM";
                                    }
                                @endphp
                                <span class="inline-block px-2.5 py-1 border text-[8px] uppercase tracking-wider rounded-none {{ $badgeStyle }}">
                                    {{ $label }}
                                </span>
                            </td>

                            <!-- PESAN & SOLUSI -->
                            <td class="px-4 py-4 align-top">
                                <div class="text-[11px] font-medium text-slate-700 leading-relaxed break-words whitespace-pre-line">
                                    {{ $f->pesan ?? '-' }}
                                </div>

                                @if($f->status == 'resolved' && $f->solusi)
                                    <div class="mt-2.5 p-2.5 bg-emerald-50/80 border border-emerald-200 text-emerald-800 rounded-none">
                                        <div class="text-[8px] font-black uppercase tracking-widest text-emerald-600 flex items-center gap-1">
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
                            <td class="px-4 py-4 text-center align-top text-slate-400 font-mono text-[10px] tabular-nums">
                                @if($f->created_at)
                                    <div>{{ $f->created_at->format('Y-m-d') }}</div>
                                    <div class="text-[8px] font-bold text-slate-300 uppercase tracking-tighter mt-0.5">{{ $f->created_at->format('H:i:s') }}</div>
                                @else
                                    {{ date('Y-m-d') }}
                                @endif
                            </td>

                            <!-- TOMBOL ACTION REPAIR -->
                            <td class="px-4 py-4 text-center align-top">
                                @if($f->status == 'resolved')
                                    <span class="inline-flex items-center gap-1 text-[8px] font-black text-emerald-600 bg-emerald-50 border border-emerald-200 px-2.5 py-1.5 uppercase tracking-wider">
                                        ✓ RESOLVED
                                    </span>
                                @else
                                    <button onclick="openRepairModal({{ $f->id }}, @js($f->pesan))" 
                                        class="bg-amber-500 hover:bg-amber-600 text-white px-3 py-1.5 text-[9px] font-black uppercase tracking-widest transition-all shadow-sm flex items-center justify-center gap-1 mx-auto">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                        REPAIR
                                    </button>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="py-16 text-center text-[10px] font-black text-slate-300 uppercase tracking-[0.3em]">
                                Tidak Ada Data Feedback untuk Kategori Ini
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- MODAL POPUP REPAIR FEEDBACK -->
    <div id="repairModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs hidden items-center justify-center z-50 p-4">
        <div class="bg-white border border-slate-200 shadow-2xl w-full max-w-lg p-6 relative animate-in fade-in zoom-in-95 duration-150">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3 mb-4">
                <h3 class="text-xs font-black text-slate-800 uppercase tracking-widest flex items-center gap-2">
                    <span class="h-2.5 w-2.5 bg-amber-500 inline-block"></span>
                    FORM PERBAIKAN / SOLUSI FEEDBACK
                </h3>
                <button onclick="closeRepairModal()" class="text-slate-400 hover:text-slate-700 font-bold text-sm">✕</button>
            </div>

            <form id="repairForm" method="POST" action="">
                @csrf
                @method('PUT')

                <div class="space-y-4">
                    <div>
                        <label class="block text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">Pesan Masukan User:</label>
                        <div id="modalPesanUser" class="text-[11px] font-medium text-slate-700 bg-slate-50 border border-slate-200 p-3 italic leading-relaxed max-h-36 overflow-y-auto">
                            -
                        </div>
                    </div>

                    <div>
                        <label class="block text-[9px] font-black text-slate-800 uppercase tracking-widest mb-1">Catatan Perbaikan / Solusi <span class="text-rose-500">*</span></label>
                        <textarea name="solusi" rows="4" required placeholder="Tuliskan tindakan/penanganan yang sudah dilakukan..." 
                            class="w-full text-[11px] font-medium text-slate-800 border border-slate-300 p-3 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 transition-all rounded-none placeholder:text-slate-300"></textarea>
                    </div>
                </div>

                <div class="mt-6 flex items-center justify-end gap-2 border-t border-slate-100 pt-4">
                    <button type="button" onclick="closeRepairModal()" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 text-[9px] font-black uppercase tracking-widest transition-all">
                        BATAL
                    </button>
                    <button type="submit" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-[9px] font-black uppercase tracking-widest transition-all shadow-sm">
                        SIMPAN & TANDAI SELESAI ✓
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- JAVASCRIPT MODAL HANDLER -->
    <script>
        function openRepairModal(id, pesan) {
            const modal = document.getElementById('repairModal');
            const form = document.getElementById('repairForm');
            const pesanBox = document.getElementById('modalPesanUser');

            form.action = `/feedback/${id}/resolve`;
            pesanBox.textContent = `"${pesan}"`;

            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeRepairModal() {
            const modal = document.getElementById('repairModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }
    </script>
</x-app-layout>