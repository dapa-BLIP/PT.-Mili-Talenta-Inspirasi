<script>
    // 1. AUTO-MOUNT KE BODY & PENGUNCI ENTER KEY GLOBAL
    document.addEventListener('DOMContentLoaded', () => {
        ['modalDetailMentor', 'modalCreateTrainer', 'modalEditTrainer', 'modalImportMentor', 'modalDeleteConfirmation'].forEach(id => {
            const el = document.getElementById(id);
            if (el && el.parentElement !== document.body) {
                document.body.appendChild(el);
            }
        });

        // KUNCI TOMBOL ENTER DI SEMUA INPUT FORM MODAL (TIDAK BISA SUBMIT OTOMATIS)
        document.querySelectorAll('#modalCreateTrainer form, #formEditTrainer').forEach(form => {
            form.addEventListener('keydown', function(e) {
                if (e.key === 'Enter' && e.target.tagName === 'INPUT' && e.target.type !== 'submit') {
                    e.preventDefault(); // Matikan auto-submit browser
                    
                    // Pindahkan fokus ke input berikutnya
                    const inputs = Array.from(form.querySelectorAll('input:not([type="hidden"]):not([disabled]), select:not([disabled])'));
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

    // 2. LIVE SEARCH & FILTER
    document.getElementById('liveSearchInput')?.addEventListener('keyup', applyFilters);

    function applyFilters() {
        const query = document.getElementById('liveSearchInput')?.value.toUpperCase().trim() || '';
        const selectedProg = document.getElementById('programDropdownFilter')?.value || 'ALL';
        const selectedStatus = document.getElementById('statusDropdownFilter')?.value || 'ALL';
        
        const rows = document.querySelectorAll('.row-mentor');
        rows.forEach(row => {
            const name = (row.querySelector('.mentor-name')?.textContent || '').toUpperCase();
            const email = (row.querySelector('.mentor-email')?.textContent || '').toUpperCase();
            const rowProg = row.getAttribute('data-program') || '';
            const rowStatus = row.getAttribute('data-status') || '';

            const matchSearch = (!query) || (name.indexOf(query) > -1) || (email.indexOf(query) > -1);
            const matchProg = (selectedProg === 'ALL') || (rowProg === selectedProg);
            const matchStatus = (selectedStatus === 'ALL') || (rowStatus === selectedStatus);

            row.style.display = (matchSearch && matchProg && matchStatus) ? '' : 'none';
        });

        const printGroups = document.querySelectorAll('.print-group-program');
        printGroups.forEach(group => {
            const groupProg = group.getAttribute('data-program') || '';
            const matchGroupProg = (selectedProg === 'ALL') || (groupProg === selectedProg);

            if (!matchGroupProg) {
                group.style.display = 'none';
                return;
            }

            const printRows = group.querySelectorAll('.row-print-target');
            let countVisible = 0;

            printRows.forEach(pRow => {
                const pName = (pRow.getAttribute('data-name') || '').toUpperCase();
                const pEmail = (pRow.getAttribute('data-email') || '').toUpperCase();
                const pStatus = (pRow.getAttribute('data-status') || '').toUpperCase();

                const matchSearch = (!query) || (pName.indexOf(query) > -1) || (pEmail.indexOf(query) > -1);
                const matchStatus = (selectedStatus === 'ALL') || (pStatus === selectedStatus);

                if (matchSearch && matchStatus) {
                    pRow.style.display = '';
                    countVisible++;
                    const numCell = pRow.querySelector('.print-no-cell');
                    if (numCell) numCell.textContent = String(countVisible).padStart(3, '0');
                } else {
                    pRow.style.display = 'none';
                }
            });

            const subtotalText = group.querySelector('.print-subtotal-text');
            if (subtotalText) subtotalText.textContent = 'SUBTOTAL: ' + countVisible + ' SDM';
            group.style.display = countVisible > 0 ? '' : 'none';
        });
    }

    // 3. PARSER DATA ROW
    function getMentorRowData(rowElement) {
        const raw = rowElement.getAttribute('data-mentor');
        if (!raw) return {};
        try {
            return JSON.parse(atob(raw));
        } catch(e) {
            return JSON.parse(raw);
        }
    }

    // 4. DETAIL MENTOR
    function openDetailMentorModalFromRow(rowElement) {
        try {
            const data = getMentorRowData(rowElement);
            const fotoUrl = rowElement.getAttribute('data-fotourl');
            bukaModalDetailMentor(data, fotoUrl);
        } catch(e) {
            console.error("Gagal parse data detail mentor:", e);
        }
    }

    function bukaModalDetailMentor(data, fotoUrl) {
        const modal = document.getElementById('modalDetailMentor');
        if (!modal) return;
        if (modal.parentElement !== document.body) {
            document.body.appendChild(modal);
        }

        if (document.getElementById('detail_mentor_nama')) document.getElementById('detail_mentor_nama').textContent = data.nama_mentor || '-';
        if (document.getElementById('detail_mentor_program')) document.getElementById('detail_mentor_program').textContent = data.program_pelatihan || '-';
        if (document.getElementById('detail_mentor_nidn')) document.getElementById('detail_mentor_nidn').textContent = data.nidn || '-';
        if (document.getElementById('detail_mentor_email')) document.getElementById('detail_mentor_email').textContent = data.email || '-';

        if (document.getElementById('detail_mentor_ttl')) {
            let ttlText = data.ttl || '';
            if (!ttlText && (data.tempat_lahir || data.tanggal_lahir)) {
                ttlText = [data.tempat_lahir, data.tanggal_lahir].filter(Boolean).join(', ');
            }
            document.getElementById('detail_mentor_ttl').textContent = ttlText || '-';
        }

        if (document.getElementById('detail_mentor_kontak')) {
            document.getElementById('detail_mentor_kontak').textContent = data.kontak || '-';
        }

        if (document.getElementById('detail_mentor_wa_link')) {
            let clean = String(data.kontak || '').replace(/[^0-9]/g, '');
            if (clean.startsWith('0')) clean = '62' + clean.substring(1);
            else if (!clean.startsWith('62') && clean.length > 0) clean = '62' + clean;

            const waLink = document.getElementById('detail_mentor_wa_link');
            if (clean) {
                waLink.href = 'https://wa.me/' + clean;
                waLink.style.pointerEvents = 'auto';
            } else {
                waLink.href = '#';
                waLink.style.pointerEvents = 'none';
            }
        }

        const badge = document.getElementById('detail_mentor_status');
        if (badge) {
            const st = (data.status || 'AKTIF').toUpperCase();
            badge.textContent = st;
            if (st === 'AKTIF') {
                badge.className = "inline-block text-[8px] font-black uppercase px-2.5 py-0.5 tracking-wider bg-emerald-100 text-emerald-800 rounded-xs shadow-2xs";
            } else if (st === 'NON-AKTIF') {
                badge.className = "inline-block text-[8px] font-black uppercase px-2.5 py-0.5 tracking-wider bg-rose-100 text-rose-800 rounded-xs shadow-2xs";
            } else {
                badge.className = "inline-block text-[8.5px] font-black uppercase px-2.5 py-0.5 tracking-wider bg-amber-100 text-amber-800 rounded-xs shadow-2xs";
            }
        }

        const img = document.getElementById('detail_mentor_foto');
        const noImg = document.getElementById('detail_mentor_no_img');
        if (img && noImg) {
            if (fotoUrl && fotoUrl.trim() !== '') {
                img.src = fotoUrl;
                img.classList.remove('hidden');
                noImg.classList.add('hidden');
            } else {
                img.src = '';
                img.classList.add('hidden');
                noImg.classList.remove('hidden');
            }
        }

        modal.classList.remove('hidden');
    }

    let isMentorCardFlipped = false;
    function putarKartuMentor() {
        const flipper = document.getElementById('cardFlipperMentor');
        if (!flipper) return;
        isMentorCardFlipped = !isMentorCardFlipped;
        flipper.style.transform = isMentorCardFlipped ? 'rotateY(180deg)' : 'rotateY(0deg)';
    }

    function tutupModalDetailMentor() {
        const modal = document.getElementById('modalDetailMentor');
        const flipper = document.getElementById('cardFlipperMentor');
        if (modal) modal.classList.add('hidden');
        if (flipper) {
            isMentorCardFlipped = false;
            flipper.style.transform = 'rotateY(0deg)';
        }
    }

    // 5. EDIT MENTOR
    function openEditMentorModalFromRow(btnElement, updateUrl) {
        try {
            const row = btnElement.closest('tr');
            const data = getMentorRowData(row);
            bukaModalEditMentor(data, updateUrl);
        } catch(e) {
            console.error("Gagal membaca data mentor:", e);
        }
    }

    function bukaModalEditMentor(data, updateUrl) {
        const form = document.getElementById('formEditTrainer');
        const modal = document.getElementById('modalEditTrainer');
        if (!form || !modal) return;

        form.action = updateUrl;
        
        if (document.getElementById('edit_nama_mentor')) document.getElementById('edit_nama_mentor').value = data.nama_mentor || '';
        if (document.getElementById('edit_nidn')) document.getElementById('edit_nidn').value = data.nidn || '';
        if (document.getElementById('edit_email')) document.getElementById('edit_email').value = data.email || '';

        let tempatLahir = data.tempat_lahir || '';
        let tanggalLahir = data.tanggal_lahir || '';

        if (data.ttl && data.ttl.includes(',')) {
            const parts = data.ttl.split(',');
            if (!tempatLahir) tempatLahir = parts[0].trim();
            
            const rawDate = parts[1].trim();
            const dateParts = rawDate.split('-');
            if (dateParts.length === 3) {
                if (dateParts[0].length === 4) {
                    tanggalLahir = rawDate;
                } else if (dateParts[2].length === 4) {
                    tanggalLahir = `${dateParts[2]}-${dateParts[1].padStart(2, '0')}-${dateParts[0].padStart(2, '0')}`;
                }
            }
        }

        if (document.getElementById('edit_tempat_lahir')) document.getElementById('edit_tempat_lahir').value = tempatLahir;
        if (document.getElementById('edit_tanggal_lahir')) document.getElementById('edit_tanggal_lahir').value = tanggalLahir;

        if (document.getElementById('edit_kontak')) {
            document.getElementById('edit_kontak').value = data.kontak || '';
        }

        if (document.getElementById('edit_program_pelatihan')) {
            const sel = document.getElementById('edit_program_pelatihan');
            const val = (data.program_pelatihan || '').trim();
            let exists = false;
            for (let i = 0; i < sel.options.length; i++) {
                if (sel.options[i].value.toUpperCase() === val.toUpperCase()) {
                    sel.selectedIndex = i;
                    exists = true;
                    break;
                }
            }
            if (!exists && val !== '') {
                const opt = document.createElement('option');
                opt.value = val;
                opt.textContent = val.toUpperCase();
                sel.appendChild(opt);
                sel.value = val;
            }
        }
        if (document.getElementById('edit_status')) document.getElementById('edit_status').value = data.status || 'AKTIF';

        modal.classList.remove('hidden');
    }

    function tutupModalEditMentor() {
        const modal = document.getElementById('modalEditTrainer');
        if (modal) modal.classList.add('hidden');
    }

    // 6. CREATE, IMPORT & DELETE
    function openImportMentorModal() {
        const modal = document.getElementById('modalImportMentor');
        if (modal) modal.classList.remove('hidden');
    }

    function closeImportMentorModal() {
        const modal = document.getElementById('modalImportMentor');
        if (modal) modal.classList.add('hidden');
    }

    function openCreateMentorModal() {
        const modal = document.getElementById('modalCreateTrainer');
        if (modal) {
            modal.classList.remove('hidden');
            const form = modal.querySelector('form');
            if (form) form.reset();
        }
    }

    function closeCreateMentorModal() {
        const modal = document.getElementById('modalCreateTrainer');
        if (modal) modal.classList.add('hidden');
    }

    function triggerDeleteConfirmation(deleteUrl, name) { 
        document.getElementById('formConfirmDelete').action = deleteUrl; 
        document.getElementById('delete_target_name').innerText = name; 
        document.getElementById('modalDeleteConfirmation').classList.remove('hidden'); 
    }

    function closeDeleteConfirmation() { 
        document.getElementById('modalDeleteConfirmation').classList.add('hidden'); 
    }
</script>