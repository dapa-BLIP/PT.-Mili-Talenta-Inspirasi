<!-- DATA TABLE MONITOR MATRIX -->
<div class="bg-white border border-slate-200 rounded-sm shadow-sm overflow-hidden">
    <!-- Mobile Table Scroll Notice -->
    <div class="sm:hidden px-4 py-2 bg-slate-50 border-b border-slate-100 flex items-center justify-between text-[8px] font-black text-slate-400 uppercase tracking-wider select-none">
        <span class="flex items-center gap-1.5">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
            </svg>
            Geser tabel untuk melihat seluruh kolom
        </span>
        <span class="text-[#22a0f6] font-bold">Swipe &rarr;</span>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse min-w-[840px] select-none">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-200 text-slate-400">
                    <th class="w-14 px-4 py-3 text-[9.5px] font-black uppercase tracking-widest text-center">NO.</th>
                    <th class="min-w-[240px] px-5 py-3 text-[9px] font-black uppercase tracking-widest">Nama Kegiatan</th>
                    <th class="min-w-[170px] px-5 py-3 text-[9px] font-black uppercase tracking-widest">Nama</th>
                    <th class="min-w-[220px] px-5 py-3 text-[9px] font-black uppercase tracking-widest text-center">Waktu Periode</th>
                    <th class="min-w-[140px] px-5 py-3 text-[9px] font-black uppercase tracking-widest text-center">Status </th>
                    <th class="min-w-[120px] px-5 py-3 text-right text-[9px] font-black uppercase tracking-widest print:hidden">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($jadwals as $index => $j)
                @php
                    $tglString = $j->tanggal_selesai instanceof \DateTimeInterface ? $j->tanggal_selesai->format('Y-m-d') : \Carbon\Carbon::parse($j->tanggal_selesai)->format('Y-m-d');
                    $waktuSelesaiJadwal = \Carbon\Carbon::parse($tglString . ' ' . $j->jam_selesai, 'Asia/Jakarta');
                    $waktuSekarang = \Carbon\Carbon::now('Asia/Jakarta');
                    $isComplete = in_array(strtolower($j->status), ['complete', 'selesai']) || $waktuSekarang->greaterThanOrEqualTo($waktuSelesaiJadwal);
                    $trainerName = $j->trainer->nama_mentor ?? 'No Trainer Assigned';
                    
                    $tglMulaiFormatted = $j->tanggal_mulai ? \Carbon\Carbon::parse($j->tanggal_mulai)->format('Y-m-d') : '';
                    $tglSelesaiFormatted = $j->tanggal_selesai ? \Carbon\Carbon::parse($j->tanggal_selesai)->format('Y-m-d') : '';

                    $jadwalDetailPayload = base64_encode(json_encode([
                        'id' => $j->id,
                        'judul' => $j->judul_kegiatan,
                        'description' => $j->description ?? '',
                        'trainer_name' => $trainerName,
                        'trainer_id' => $j->trainer_id,
                        'jenis' => $j->jenis_pelaksanaan,
                        'tgl_mulai' => $tglMulaiFormatted,
                        'tgl_selesai' => $tglSelesaiFormatted,
                        'jam_mulai' => $j->jam_mulai,
                        'jam_selesai' => $j->jam_selesai,
                        'is_complete' => $isComplete
                    ]));
                @endphp
                <tr onclick="openDetailJadwalFromRow(this)" 
                    data-jadwal="{{ $jadwalDetailPayload }}" 
                    class="hover:bg-slate-50/80 transition-colors cursor-pointer group">
                    
                    <!-- Nomor Urut -->
                    <td class="w-14 px-4 py-4 text-[10.5px] font-black text-slate-400 text-center font-mono align-middle">
                        {{ str_pad($index + 1, 3, '0', STR_PAD_LEFT) }}
                    </td>

                    <!-- Activity & Description -->
                    <td class="px-5 py-4">
                        <div class="text-[12px] font-black text-slate-800 uppercase tracking-tight max-w-[280px] truncate group-hover:text-[#22a0f6] transition-colors">
                            {{ $j->judul_kegiatan }}
                        </div>
                    </td>

                    <!-- Lead Trainer (Nama Saja, Tidak Terlalu Kecil Sesuai Evaluasi) -->
                    <td class="px-5 py-4">
                        <span class="text-[11.5px] font-black text-slate-800 uppercase tracking-tight block max-w-[190px] truncate" title="{{ $trainerName }}">
                            {{ $trainerName }}
                        </span>
                    </td>

                    <!-- Timeline Period -->
                    <td class="px-5 py-4 text-center">
                        <div class="inline-flex flex-col items-center justify-center gap-0.5 text-[10px] font-bold text-slate-500 tabular-nums">
                            <div class="flex items-center gap-1">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <span>{{ $tglMulaiFormatted ?: '-' }}</span>
                                <span class="text-slate-300 font-normal">→</span>
                                <span>{{ $tglSelesaiFormatted ?: '-' }}</span>
                            </div>
                            <div class="text-[9px] text-slate-500 font-semibold flex items-center gap-1 bg-slate-50 border border-slate-200 px-2 py-0.5 rounded-xs mt-0.5">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-2.5 w-2.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span>{{ \Carbon\Carbon::parse($j->jam_mulai)->format('H:i') }}</span> - <span>{{ \Carbon\Carbon::parse($j->jam_selesai)->format('H:i') }} WIB</span>
                            </div>
                        </div>
                    </td>

                    <!-- Status Matrix -->
                    <td class="px-5 py-4 text-center">
                        <div class="flex flex-col items-center justify-center gap-1">
                            <span class="px-2 py-0.5 border text-[8px] font-black uppercase rounded-xs tracking-wider {{ $isComplete ? 'bg-emerald-50 border-emerald-300 text-emerald-700' : 'bg-amber-50 border-amber-300 text-amber-700' }}">
                                {{ $isComplete ? 'COMPLETE' : 'ON-GOING' }}
                            </span>
                            <span class="text-[8px] font-black text-slate-400 uppercase tracking-widest text-center block">
                                {{ $j->jenis_pelaksanaan }}
                            </span>
                        </div>
                    </td>

                    <!-- Operations (Tombol Samakan Persis Dengan Kelola Mentor) -->
                    <td class="px-5 py-4 print:hidden" onclick="event.stopPropagation()">
                        <div class="flex justify-end gap-1 items-center" onclick="event.stopPropagation()">
                            <!-- Tombol Edit Jadwal (Sama Seperti di Kelola Mentor) -->
                            <button type="button" 
                                onclick="event.stopPropagation(); openEditModal({ 
                                    id: '{{ $j->id }}', 
                                    judul: '{{ base64_encode($j->judul_kegiatan) }}', 
                                    description: '{{ base64_encode($j->description ?? '') }}', 
                                    trainer_id: '{{ $j->trainer_id }}', 
                                    tgl_mulai: '{{ $tglMulaiFormatted }}', 
                                    tgl_selesai: '{{ $tglSelesaiFormatted }}', 
                                    jam_mulai: '{{ $j->jam_mulai }}', 
                                    jam_selesai: '{{ $j->jam_selesai }}', 
                                    jenis: '{{ $j->jenis_pelaksanaan }}' 
                                }, true)" 
                                title="Edit Data Jadwal" 
                                class="p-1 bg-white border border-slate-200 hover:bg-[#22a0f6] hover:text-white text-blue-500 transition-colors shadow-2xs rounded-xs cursor-pointer">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" stroke-width="2.5"></path>
                                </svg>
                            </button>

                            <!-- Tombol Hapus Jadwal (Sama Seperti di Kelola Mentor) -->
                            <button type="button" 
                                onclick="event.stopPropagation(); triggerDeleteModal('{{ $j->id }}', '{{ addslashes($j->judul_kegiatan) }}')" 
                                title="Hapus Jadwal" 
                                class="p-1 bg-white border border-slate-200 hover:bg-rose-600 hover:text-white text-rose-500 transition-colors shadow-2xs rounded-xs cursor-pointer">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke-width="2.5"></path>
                                </svg>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="py-16 text-center text-[10px] font-black text-slate-300 uppercase tracking-[0.5em]">Belum ada jadwal</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- SYSTEM LOGIC FOOTER PANEL -->
    <div class="bg-[#f8fafc] border-t border-slate-200 px-4 sm:px-6 py-3 flex flex-col sm:flex-row items-center justify-between gap-2 print:hidden select-none">
        <div class="text-[9px] font-black text-slate-400 uppercase tracking-widest flex items-center gap-2">
            <span class="opacity-80">PT MILI TALENTA INSPIRASI</span>
            <span class="text-slate-300">|</span>
            <span class="opacity-80">2026</span>
        </div>
    </div>
</div>
