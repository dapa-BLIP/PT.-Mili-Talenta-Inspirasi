<script>
    window.BANK_MATERI_GLOBAL = @json($materis ?? []);
    let targetHapusNama = '';

    document.addEventListener('DOMContentLoaded', () => {
        const modalIds = [
            'modalDetailPelatihan', 
            'modalCreatePelatihan', 
            'modalEditPelatihan', 
            'modalImportPelatihan', 
            'modalSecureDelete', 
            'modalModulPelatihan', 
            'modalBankMateri'
        ];

        // Pastikan modal menempel langsung ke body agar backdrop full screen
        modalIds.forEach(id => {
            const el = document.getElementById(id);
            if (el && el.parentElement !== document.body) {
                document.body.appendChild(el);
            }
        });

        // Cegah accidental form submission via tombol Enter
        document.querySelectorAll('form').forEach(form => {
            form.addEventListener('keydown', function(e) {
                if (e.key === 'Enter' && e.target.tagName === 'INPUT' && e.target.type !== 'submit' && e.target.id !== 'live-search-pelatihan') {
                    e.preventDefault();
                    const inputs = Array.from(form.querySelectorAll('input:not([type="hidden"]):not([disabled]), select:not([disabled])'));
                    const idx = inputs.indexOf(e.target);
                    if (idx > -1 && idx + 1 < inputs.length) {
                        inputs[idx + 1].focus();
                    }
                    return false;
                }
            });
        });
    });

    // =========================================================================
    // 1. FILTER PENCARIAN TABEL LIVE
    // =========================================================================
    function filterPelatihan() {
        const query = document.getElementById('live-search-pelatihan').value.trim().toLowerCase();
        const rows = document.querySelectorAll('#table-pelatihan .row-pelatihan');
        let count = 0;

        rows.forEach(row => {
            const items = row.querySelectorAll('.search-item');
            let match = false;
            items.forEach(it => {
                if (it.textContent.toLowerCase().includes(query)) match = true;
            });

            if (match || query === '') {
                row.style.display = '';
                count++;
                const indexElem = row.querySelector('.index-pelatihan');
                if (indexElem) indexElem.textContent = String(count).padStart(4, '0');
            } else {
                row.style.display = 'none';
            }
        });
    }

    // =========================================================================
    // 2. MODAL TAMBAH PELATIHAN
    // =========================================================================
    function handleModulChangeCreate() {
        const select = document.getElementById('create_jenis_pelatihan');
        const hiddenNama = document.getElementById('create_nama_pelatihan');
        const container = document.getElementById('create_checkbox_container');
        const btnToggle = document.getElementById('btn_toggle_all_materi');
        const badgeCounter = document.getElementById('badge_counter_materi');
        if (!select || !container) return;

        const val = (select.value || '').trim();

        // Otomatis samakan nama_pelatihan dengan program yang dipilih
        if (hiddenNama) {
            hiddenNama.value = val ? val.toUpperCase() : '';
        }

        container.innerHTML = '';
        if (!val) {
            container.innerHTML = '<div class="h-full flex flex-col items-center justify-center py-8 text-center text-[10px] text-slate-400 italic">Pilih program pelatihan di sebelah kiri.</div>';
            if (btnToggle) btnToggle.classList.add('hidden');
            if (badgeCounter) badgeCounter.textContent = '0 Terpilih';
            return;
        }

        const dataMateri = window.BANK_MATERI_GLOBAL || [];
        const matched = dataMateri.filter(m => (m.jenis_pelatihan || '').toLowerCase().trim() === val.toLowerCase());

        if (matched.length === 0) {
            container.innerHTML = '<div class="h-full flex flex-col items-center justify-center py-8 text-center text-[10px] text-slate-400 italic">Belum ada materi untuk program ini.</div>';
            if (btnToggle) btnToggle.classList.add('hidden');
            if (badgeCounter) badgeCounter.textContent = '0 Terpilih';
        } else {
            if (btnToggle) {
                btnToggle.classList.remove('hidden');
                btnToggle.textContent = 'Batalkan Semua';
            }

            matched.forEach((mat, idx) => {
                const label = document.createElement('label');
                label.className = "item-checkbox-materi flex items-center justify-between px-3 py-2 bg-sky-50/50 border border-sky-200/80 rounded-xs hover:border-sky-300 cursor-pointer transition-all";
                label.innerHTML = `
                    <div class="flex items-center gap-2.5 overflow-hidden">
                        <input type="checkbox" name="materi_ids[]" value="${mat.id}" checked onchange="updateCounterMateriCreate(this)" class="checkbox-materi w-4 h-4 rounded-xs text-[#22a0f6] border-slate-300 cursor-pointer focus:ring-0 focus:ring-offset-0">
                        <span class="text-[10px] font-bold text-slate-800 uppercase tracking-tight truncate">${mat.nama_materi}</span>
                    </div>
                    <span class="text-[9px] font-mono text-slate-400 font-bold shrink-0">#${String(idx + 1).padStart(2, '0')}</span>
                `;
                container.appendChild(label);
            });

            updateTotalBadgeMateri();
        }
    }

    function updateCounterMateriCreate(checkbox) {
        const label = checkbox.closest('.item-checkbox-materi');
        if (checkbox.checked) {
            label.className = "item-checkbox-materi flex items-center justify-between px-3 py-2 bg-sky-50/50 border border-sky-200/80 rounded-xs hover:border-sky-300 cursor-pointer transition-all";
        } else {
            label.className = "item-checkbox-materi flex items-center justify-between px-3 py-2 bg-slate-50 border border-slate-200 rounded-xs hover:bg-slate-100/70 cursor-pointer transition-all opacity-60";
        }
        updateTotalBadgeMateri();
    }

    function updateTotalBadgeMateri() {
        const total = document.querySelectorAll('#create_checkbox_container .checkbox-materi').length;
        const checked = document.querySelectorAll('#create_checkbox_container .checkbox-materi:checked').length;
        const badge = document.getElementById('badge_counter_materi');
        const btnToggle = document.getElementById('btn_toggle_all_materi');

        if (badge) badge.textContent = `${checked} dari ${total} Terpilih`;
        if (btnToggle) {
            btnToggle.textContent = checked === total ? 'Batalkan Semua' : 'Pilih Semua';
        }
    }

    function toggleSelectAllMateriCreate() {
        const checkboxes = document.querySelectorAll('#create_checkbox_container .checkbox-materi');
        const allChecked = Array.from(checkboxes).every(c => c.checked);

        checkboxes.forEach(cb => {
            cb.checked = !allChecked;
            const label = cb.closest('.item-checkbox-materi');
            if (cb.checked) {
                label.className = "item-checkbox-materi flex items-center justify-between px-3 py-2 bg-sky-50/50 border border-sky-200/80 rounded-xs hover:border-sky-300 cursor-pointer transition-all";
            } else {
                label.className = "item-checkbox-materi flex items-center justify-between px-3 py-2 bg-slate-50 border border-slate-200 rounded-xs hover:bg-slate-100/70 cursor-pointer transition-all opacity-60";
            }
        });

        updateTotalBadgeMateri();
    }

    // =========================================================================
    // 3. MODAL DETAIL PELATIHAN (WIDESCREEN LANDSCAPE)
    // =========================================================================
    function bukaModalDetailPelatihanFromRow(row) {
        const nama = row.getAttribute('data-nama') || '-';
        const jenis = row.getAttribute('data-jenis') || '';
        const mentor = row.getAttribute('data-mentor') || 'Belum Ditunjuk';
        const rawMateri = row.getAttribute('data-materi-ids') || '';
        const rawKomponen = row.getAttribute('data-komponen') || '';

        let materiIdsJson = '[]';
        let komponenJson = '[]';

        try { materiIdsJson = atob(rawMateri); } catch(e) { materiIdsJson = rawMateri; }
        try { komponenJson = atob(rawKomponen); } catch(e) { komponenJson = rawKomponen; }

        bukaModalDetailPelatihan(nama, jenis, mentor, materiIdsJson, komponenJson);
    }

    function bukaModalDetailPelatihan(nama, jenis, mentor, materiIdsJson, komponenJson) {
        const modal = document.getElementById('modalDetailPelatihan');
        if (!modal) return;

        if (document.getElementById('detail_nama_pelatihan')) {
            document.getElementById('detail_nama_pelatihan').textContent = nama || '-';
        }
        if (document.getElementById('detail_modul_pelatihan')) {
            document.getElementById('detail_modul_pelatihan').textContent = 'MODUL: ' + (jenis || '-').toUpperCase();
        }
        if (document.getElementById('detail_mentor_pelatihan')) {
            document.getElementById('detail_mentor_pelatihan').textContent = mentor || 'Belum Ditunjuk';
        }

        // Render Komponen Penilaian
        const kompContainer = document.getElementById('detail_komponen_container');
        if (kompContainer) {
            kompContainer.innerHTML = '';
            let komponenList = [];
            try { komponenList = JSON.parse(komponenJson || '[]'); } catch(e) {}

            if (komponenList.length === 0) {
                kompContainer.innerHTML = '<span class="text-[10px] text-slate-400 italic">Belum ada komponen nilai.</span>';
            } else {
                komponenList.forEach(k => {
                    const span = document.createElement('span');
                    span.className = "text-[9.5px] font-black bg-slate-100 border border-slate-200 text-slate-700 px-2.5 py-1 rounded-xs uppercase tracking-tight";
                    span.textContent = k.replace(/_/g, ' ');
                    kompContainer.appendChild(span);
                });
            }
        }

        // Render List Materi
        const matContainer = document.getElementById('detail_checkbox_container');
        if (matContainer) {
            matContainer.innerHTML = '';
            let savedIds = [];
            try { savedIds = JSON.parse(materiIdsJson || '[]'); } catch(e) {}

            const dataMateri = window.BANK_MATERI_GLOBAL || [];
            const matchedMateri = dataMateri.filter(m => (m.jenis_pelatihan || '').toLowerCase().trim() === (jenis || '').toLowerCase().trim());

            if (document.getElementById('detail_total_materi')) {
                document.getElementById('detail_total_materi').textContent = matchedMateri.length + ' Materi';
            }

            if (matchedMateri.length === 0) {
                matContainer.innerHTML = '<p class="text-[10px] text-slate-400 italic py-3 text-center col-span-2">Belum ada materi terdaftar untuk modul ini.</p>';
            } else {
                matchedMateri.forEach((mat, idx) => {
                    const isChecked = savedIds.length === 0 || savedIds.some(id => String(id) === String(mat.id));
                    const div = document.createElement('div');
                    div.className = "flex items-center justify-between px-2.5 py-1.5 bg-slate-50 border border-slate-200/80 rounded-xs";
                    
                    if (isChecked) {
                        div.innerHTML = `
                            <div class="flex items-center gap-2 overflow-hidden pr-1">
                                <span class="w-3.5 h-3.5 rounded-xs bg-[#22a0f6] text-white flex items-center justify-center shrink-0 shadow-2xs">
                                    <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" stroke-width="3.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                </span>
                                <span class="text-[10px] font-bold text-slate-800 uppercase tracking-tight truncate" title="${mat.nama_materi}">
                                    ${mat.nama_materi}
                                </span>
                            </div>
                            <span class="text-[8.5px] font-mono font-bold text-slate-400 shrink-0">#${String(idx + 1).padStart(2, '0')}</span>
                        `;
                    } else {
                        div.innerHTML = `
                            <div class="flex items-center gap-2 overflow-hidden pr-1 opacity-50">
                                <span class="w-3.5 h-3.5 rounded-xs border border-slate-300 bg-white shrink-0"></span>
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-tight truncate" title="${mat.nama_materi}">
                                    ${mat.nama_materi}
                                </span>
                            </div>
                            <span class="text-[8.5px] font-mono font-bold text-slate-300 shrink-0">#${String(idx + 1).padStart(2, '0')}</span>
                        `;
                    }
                    matContainer.appendChild(div);
                });
            }
        }

        modal.classList.remove('hidden');
    }

    // =========================================================================
    // 4. MODAL EDIT PELATIHAN
    // =========================================================================
    function bukaModalEditPelatihan(btn) {
        const data = btn.dataset;
        const form = document.getElementById('formEditPelatihan');
        if (!form) return;
        
        form.action = data.url || '';

        const namaVal = data.nama || data.jenis || '';
        if (document.getElementById('edit_nama_pelatihan')) {
            document.getElementById('edit_nama_pelatihan').value = namaVal;
        }
        if (document.getElementById('edit_jenis_pelatihan')) {
            document.getElementById('edit_jenis_pelatihan').value = data.jenis || '';
        }
        if (document.getElementById('edit_trainer_id')) {
            document.getElementById('edit_trainer_id').value = data.trainer || '';
        }
        if (document.getElementById('edit_raw_komponen')) {
            document.getElementById('edit_raw_komponen').value = (data.komponen || '').replace(/_/g, ' ');
        }

        let savedIds = [];
        try {
            const raw = data.materiIds || '[]';
            const decoded = (raw.startsWith('[') || raw.startsWith('{')) ? raw : atob(raw);
            savedIds = JSON.parse(decoded);
        } catch(e) {
            savedIds = [];
        }
        renderCheckboxesEdit(data.jenis, savedIds);

        document.getElementById('modalEditPelatihan')?.classList.remove('hidden');
    }

    function handleModulChangeEdit() {
        const selectElem = document.getElementById('edit_jenis_pelatihan');
        const hiddenNama = document.getElementById('edit_nama_pelatihan');
        const valJenis = (selectElem ? selectElem.value : '').trim();

        if (hiddenNama) {
            hiddenNama.value = valJenis ? valJenis.toUpperCase() : '';
        }

        renderCheckboxesEdit(valJenis, []);
    }

    function renderCheckboxesEdit(jenis, selectedIds = []) {
        const container = document.getElementById('edit_checkbox_container');
        const btnToggle = document.getElementById('btn_toggle_all_materi_edit');
        const badgeCounter = document.getElementById('badge_counter_materi_edit');
        if (!container) return;

        container.innerHTML = '';
        const valJenis = (jenis || '').toLowerCase().trim();

        if (!valJenis) {
            container.innerHTML = '<div class="h-full flex flex-col items-center justify-center py-8 text-center text-[10px] text-slate-400 italic">Pilih program pelatihan di sebelah kiri.</div>';
            if (btnToggle) btnToggle.classList.add('hidden');
            if (badgeCounter) badgeCounter.textContent = '0 Terpilih';
            return;
        }

        const dataMateri = window.BANK_MATERI_GLOBAL || [];
        const matched = dataMateri.filter(m => (m.jenis_pelatihan || '').toLowerCase().trim() === valJenis);

        if (matched.length === 0) {
            container.innerHTML = '<div class="h-full flex flex-col items-center justify-center py-8 text-center text-[10px] text-slate-400 italic">Belum ada materi untuk program ini.</div>';
            if (btnToggle) btnToggle.classList.add('hidden');
            if (badgeCounter) badgeCounter.textContent = '0 Terpilih';
        } else {
            if (btnToggle) btnToggle.classList.remove('hidden');

            matched.forEach((mat, idx) => {
                const isChecked = selectedIds.length === 0 || selectedIds.some(id => String(id) === String(mat.id));
                const label = document.createElement('label');
                label.className = `item-checkbox-materi-edit flex items-center justify-between px-3 py-2 rounded-xs cursor-pointer transition-all ${isChecked ? 'bg-sky-50/50 border border-sky-200/80 hover:border-sky-300' : 'bg-slate-50 border border-slate-200 hover:bg-slate-100/70 opacity-60'}`;
                label.innerHTML = `
                    <div class="flex items-center gap-2.5 overflow-hidden">
                        <input type="checkbox" 
                               name="materi_ids[]" 
                               value="${mat.id}" 
                               ${isChecked ? 'checked' : ''} 
                               onchange="updateCounterMateriEdit(this)"
                               class="checkbox-materi-edit w-4 h-4 rounded-xs text-[#22a0f6] border-slate-300 cursor-pointer focus:ring-0 focus:ring-offset-0">
                        <span class="text-[10px] font-bold text-slate-800 uppercase tracking-tight truncate">${mat.nama_materi}</span>
                    </div>
                    <span class="text-[9px] font-mono text-slate-400 font-bold shrink-0">#${String(idx + 1).padStart(2, '0')}</span>
                `;
                container.appendChild(label);
            });

            updateTotalBadgeMateriEdit();
        }
    }

    function updateCounterMateriEdit(checkbox) {
        const label = checkbox.closest('.item-checkbox-materi-edit');
        if (checkbox.checked) {
            label.className = "item-checkbox-materi-edit flex items-center justify-between px-3 py-2 bg-sky-50/50 border border-sky-200/80 rounded-xs hover:border-sky-300 cursor-pointer transition-all";
        } else {
            label.className = "item-checkbox-materi-edit flex items-center justify-between px-3 py-2 bg-slate-50 border border-slate-200 rounded-xs hover:bg-slate-100/70 cursor-pointer transition-all opacity-60";
        }
        updateTotalBadgeMateriEdit();
    }

    function updateTotalBadgeMateriEdit() {
        const total = document.querySelectorAll('#edit_checkbox_container .checkbox-materi-edit').length;
        const checked = document.querySelectorAll('#edit_checkbox_container .checkbox-materi-edit:checked').length;
        const badge = document.getElementById('badge_counter_materi_edit');
        const btnToggle = document.getElementById('btn_toggle_all_materi_edit');

        if (badge) badge.textContent = `${checked} dari ${total} Terpilih`;
        if (btnToggle) {
            btnToggle.textContent = checked === total ? 'Batalkan Semua' : 'Pilih Semua';
        }
    }

    function toggleSelectAllMateriEdit() {
        const checkboxes = document.querySelectorAll('#edit_checkbox_container .checkbox-materi-edit');
        const allChecked = Array.from(checkboxes).every(c => c.checked);

        checkboxes.forEach(cb => {
            cb.checked = !allChecked;
            const label = cb.closest('.item-checkbox-materi-edit');
            if (cb.checked) {
                label.className = "item-checkbox-materi-edit flex items-center justify-between px-3 py-2 bg-sky-50/50 border border-sky-200/80 rounded-xs hover:border-sky-300 cursor-pointer transition-all";
            } else {
                label.className = "item-checkbox-materi-edit flex items-center justify-between px-3 py-2 bg-slate-50 border border-slate-200 rounded-xs hover:bg-slate-100/70 cursor-pointer transition-all opacity-60";
            }
        });

        updateTotalBadgeMateriEdit();
    }

    // =========================================================================
    // 5. MODAL VERIFIKASI HAPUS AMAN
    // =========================================================================
    function mintaVerifikasiHapus(deleteUrl, namaTarget) {
        targetHapusNama = namaTarget.trim().toUpperCase();
        const form = document.getElementById('formSecureDelete');
        if (form) form.action = deleteUrl;

        if (document.getElementById('delete-target-name')) {
            document.getElementById('delete-target-name').textContent = targetHapusNama;
        }
        if (document.getElementById('input-delete-verify')) {
            document.getElementById('input-delete-verify').value = '';
        }
        
        const btn = document.getElementById('btn-confirm-delete-final');
        if (btn) {
            btn.disabled = true;
            btn.className = "w-2/3 bg-slate-200 text-slate-400 py-2.5 text-xs font-black uppercase rounded-sm cursor-not-allowed";
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
            btn.className = "w-2/3 bg-rose-600 hover:bg-rose-700 text-white py-2.5 text-xs font-black uppercase rounded-sm cursor-pointer shadow-xs transition-colors";
        } else {
            btn.disabled = true;
            btn.className = "w-2/3 bg-slate-200 text-slate-400 py-2.5 text-xs font-black uppercase rounded-sm cursor-not-allowed";
        }
    }
</script>