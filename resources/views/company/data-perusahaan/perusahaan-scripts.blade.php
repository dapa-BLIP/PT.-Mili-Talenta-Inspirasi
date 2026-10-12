<!-- ========================================================================= -->
<!-- JAVASCRIPT CONTROLLER: MODUL DATA PERUSAHAAN MITRA                        -->
<!-- ========================================================================= -->
<script>
    let targetHapusNama = '';

    // 1. AUTO MOUNT MODAL KE BODY & PENGUNCI ENTER KEY
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

    // 2. LIVE SEARCH FILTER DENGAN RE-INDEX OTOMATIS
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

    // 3. PARSER DATA ROW BASE64
    function getRowPerusahaanData(row) {
        const raw = row.getAttribute('data-item');
        if (!raw) return {};
        try {
            return JSON.parse(atob(raw));
        } catch(e) {
            return JSON.parse(raw);
        }
    }

    // 4. DETAIL PERUSAHAAN CONTROLLER (LANDSCAPE MODAL)
    function tutupModalDetailPerusahaan() {
        const modal = document.getElementById('modalDetailPerusahaan');
        if (modal) modal.classList.add('hidden');
    }

    function bukaModalDetailPerusahaanFromRow(row) {
        const data = getRowPerusahaanData(row);
        bukaModalDetailPerusahaan(data);
    }

    function bukaModalDetailPerusahaan(data) {
        const modal = document.getElementById('modalDetailPerusahaan');
        if (modal && modal.parentElement !== document.body) {
            document.body.appendChild(modal);
        }

        if (document.getElementById('detail_nama_perusahaan')) {
            document.getElementById('detail_nama_perusahaan').textContent = data.nama_perusahaan || '-';
        }
        if (document.getElementById('detail_pic')) {
            document.getElementById('detail_pic').textContent = data.penanggung_jawab || '-';
        }
        if (document.getElementById('detail_kontak')) {
            document.getElementById('detail_kontak').textContent = data.kontak_pic || '-';
        }
        if (document.getElementById('detail_wa_link')) {
            let clean = String(data.kontak_pic || '').replace(/[^0-9]/g, '');
            if (clean.startsWith('0')) clean = '62' + clean.substring(1);
            else if (!clean.startsWith('62') && clean.length > 0) clean = '62' + clean;

            const waLink = document.getElementById('detail_wa_link');
            if (clean) {
                waLink.href = 'https://wa.me/' + clean;
                waLink.style.pointerEvents = 'auto';
            } else {
                waLink.href = '#';
                waLink.style.pointerEvents = 'none';
            }
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
                tbody.innerHTML = '<tr><td colspan="2" class="p-4 text-center text-slate-400 italic text-[10px]">Belum ada peserta yang terdaftar di program pelatihan.</td></tr>';
            } else {
                keys.forEach(prog => {
                    const count = breakdown[prog];
                    const tr = document.createElement('tr');
                    tr.className = "hover:bg-slate-50/80 transition-colors";
                    tr.innerHTML = `
                        <td class="px-3.5 py-2.5 font-bold text-slate-700 uppercase text-[10px] align-middle">${prog}</td>
                        <td class="px-3.5 py-2.5 text-right font-mono font-bold text-[#22a0f6] text-[9.5px] align-middle">
                            <span class="inline-block px-2 py-0.5 bg-blue-50 border border-blue-100 rounded-xs">${count} PESERTA</span>
                        </td>
                    `;
                    tbody.appendChild(tr);
                });
            }
        }

        const badge = document.getElementById('detail_status_badge');
        if (badge) {
            const st = (data.status || 'AKTIF').toUpperCase();
            badge.innerText = st;
            if (st === 'AKTIF') {
                badge.className = "ml-2 inline-block text-[9px] font-bold uppercase px-2 py-0.5 rounded-xs shrink-0 bg-emerald-500/20 text-emerald-400 border border-emerald-500/30";
            } else if (st === 'PENDING') {
                badge.className = "ml-2 inline-block text-[9px] font-bold uppercase px-2 py-0.5 rounded-xs shrink-0 bg-amber-500/20 text-amber-400 border border-amber-500/30";
            } else {
                badge.className = "ml-2 inline-block text-[9px] font-bold uppercase px-2 py-0.5 rounded-xs shrink-0 bg-rose-500/20 text-rose-400 border border-rose-500/30";
            }
        }

        modal?.classList.remove('hidden');
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

    // 6. DELETE PERUSAHAAN CONTROLLER (2 TAHAP VALIDASI & PREVIEW LANDSCAPE)
    let currentDeleteStep = 1;

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

        // Ambil data detail perusahaan dari baris tabel jika ada
        const row = btn.closest('tr');
        const item = row ? getRowPerusahaanData(row) : {};

        targetHapusNama = namaTarget.trim().toUpperCase();
        const form = document.getElementById('formSecureDelete');
        if (form) form.action = deleteUrl;

        // Isi preview di Tahap 1
        const elNama = document.getElementById('delete_preview_nama');
        if (elNama) elNama.textContent = targetHapusNama || (item.nama_perusahaan || '-');

        const elPic = document.getElementById('delete_preview_pic');
        if (elPic) elPic.textContent = item.penanggung_jawab || '-';

        const elKontak = document.getElementById('delete_preview_kontak');
        if (elKontak) elKontak.textContent = item.kontak_pic || '-';

        const elEmail = document.getElementById('delete_preview_email');
        if (elEmail) elEmail.textContent = item.email_perusahaan || '-';

        const elStatus = document.getElementById('delete_preview_status');
        if (elStatus) elStatus.textContent = (item.status || 'AKTIF').toUpperCase();

        // Target name di Tahap 2
        const elTargetName = document.getElementById('delete-target-name');
        if (elTargetName) elTargetName.textContent = targetHapusNama;

        // Reset Form & Step
        const checkAck = document.getElementById('checkAcknowledgeDelete');
        if (checkAck) checkAck.checked = false;

        const inputVerify = document.getElementById('input-delete-verify');
        if (inputVerify) inputVerify.value = '';

        const statusMatch = document.getElementById('delete-match-status');
        if (statusMatch) {
            statusMatch.textContent = 'Status: Belum cocok';
            statusMatch.className = 'text-[9.5px] font-bold text-slate-400';
        }

        const btnStep2 = document.getElementById('btnNextToStep2');
        if (btnStep2) {
            btnStep2.disabled = true;
            btnStep2.className = 'px-5 py-2 bg-slate-200 text-slate-400 rounded-xs text-[9.5px] font-black uppercase tracking-widest cursor-not-allowed transition-colors flex items-center gap-2 border-none';
        }

        const btnFinal = document.getElementById('btn-confirm-delete-final');
        if (btnFinal) {
            btnFinal.disabled = true;
            btnFinal.className = 'px-5 py-2 bg-slate-200 text-slate-400 text-[9.5px] font-black uppercase tracking-widest rounded-xs cursor-not-allowed transition-all flex items-center gap-2 border-none';
        }

        goToDeleteStep(1);
        document.getElementById('modalSecureDelete')?.classList.remove('hidden');
    }

    function goToDeleteStep(step) {
        currentDeleteStep = step;
        const s1 = document.getElementById('deleteStep1Container');
        const s2 = document.getElementById('deleteStep2Container');
        const indicator = document.getElementById('deleteStepIndicator');

        if (step === 1) {
            if (s1) s1.classList.remove('hidden');
            if (s2) s2.classList.add('hidden');
            if (indicator) indicator.textContent = 'Tahap 1 dari 2: Penelaahan Risiko & Penjelasan Dampak';
        } else {
            if (s1) s1.classList.add('hidden');
            if (s2) s2.classList.remove('hidden');
            if (indicator) indicator.textContent = 'Tahap 2 dari 2: Konfirmasi Keamanan & Ketik Nama Target';
            const inputVerify = document.getElementById('input-delete-verify');
            if (inputVerify) {
                setTimeout(() => inputVerify.focus(), 50);
            }
        }
    }

    function toggleBtnStep1() {
        const checkAck = document.getElementById('checkAcknowledgeDelete');
        const btnStep2 = document.getElementById('btnNextToStep2');
        if (!btnStep2) return;

        if (checkAck && checkAck.checked) {
            btnStep2.disabled = false;
            btnStep2.className = 'px-5 py-2 bg-rose-600 hover:bg-rose-700 text-white rounded-xs text-[9.5px] font-black uppercase tracking-widest cursor-pointer shadow-xs transition-colors flex items-center gap-2 border-none';
        } else {
            btnStep2.disabled = true;
            btnStep2.className = 'px-5 py-2 bg-slate-200 text-slate-400 rounded-xs text-[9.5px] font-black uppercase tracking-widest cursor-not-allowed transition-colors flex items-center gap-2 border-none';
        }
    }

    function evaluasiTombolHapus() {
        const input = document.getElementById('input-delete-verify');
        const valUser = input ? input.value.trim().toUpperCase() : '';
        const btn = document.getElementById('btn-confirm-delete-final');
        const statusMatch = document.getElementById('delete-match-status');
        if (!btn) return;

        if (valUser === targetHapusNama && targetHapusNama !== '') {
            btn.disabled = false;
            btn.className = 'px-5 py-2 bg-rose-600 hover:bg-rose-700 text-white text-[9.5px] font-black uppercase tracking-widest rounded-xs cursor-pointer shadow-xs transition-all flex items-center gap-2 border-none';
            if (statusMatch) {
                statusMatch.textContent = '✅ Nama cocok! Tombol hapus permanen telah aktif.';
                statusMatch.className = 'text-[9.5px] font-black text-emerald-600';
            }
        } else {
            btn.disabled = true;
            btn.className = 'px-5 py-2 bg-slate-200 text-slate-400 text-[9.5px] font-black uppercase tracking-widest rounded-xs cursor-not-allowed transition-all flex items-center gap-2 border-none';
            if (statusMatch) {
                statusMatch.textContent = valUser.length > 0 ? '❌ Nama belum sesuai' : 'Status: Belum cocok';
                statusMatch.className = 'text-[9.5px] font-bold ' + (valUser.length > 0 ? 'text-rose-500' : 'text-slate-400');
            }
        }
    }

    function closeDeletePerusahaanModal() {
        document.getElementById('modalSecureDelete')?.classList.add('hidden');
        goToDeleteStep(1);
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            tutupModalDetailPerusahaan();
            document.getElementById('modalCreatePerusahaan')?.classList.add('hidden');
            document.getElementById('modalEditPerusahaan')?.classList.add('hidden');
            closeDeletePerusahaanModal();
            document.getElementById('modalBlockedDelete')?.classList.add('hidden');
        }
    });
</script>