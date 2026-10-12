<!-- SCRIPT ENGINE PESERTA -->
<script>
document.getElementById('liveSearchInput').addEventListener('keyup', applyFilters);

function applyFilters() {
    const query = document.getElementById('liveSearchInput').value.toUpperCase();
    const selectedSektor = document.getElementById('sektorDropdownFilter').value;
    const selectedPerusahaan = document.getElementById('perusahaanDropdownFilter').value;
    const rows = document.querySelectorAll('.row-peserta');

    rows.forEach(row => {
        const name = (row.querySelector('.participant-name')?.textContent || '').toUpperCase();
        const rowSektor = (row.getAttribute('data-sektor') || '').toUpperCase();
        const rowPerusahaan = row.getAttribute('data-perusahaan');

        const matchSearch = name.indexOf(query) > -1;
        
        let matchSektor = (selectedSektor === 'ALL');
        if (!matchSektor) {
            const cleanSelected = selectedSektor.replace(/KELAS|PELATIHAN|PENGETAHUAN/g, '').replace(/[^A-Z0-9]/g, '').trim();
            const cleanRow = rowSektor.replace(/KELAS|PELATIHAN|PENGETAHUAN/g, '').replace(/[^A-Z0-9]/g, '').trim();

            const selHasNon = selectedSektor.includes('NON');
            const rowHasNon = rowSektor.includes('NON');

            if (selHasNon !== rowHasNon) {
                matchSektor = false;
            } else if (rowSektor === selectedSektor || rowSektor.includes(selectedSektor) || selectedSektor.includes(rowSektor)) {
                matchSektor = true;
            } else if (cleanSelected && cleanRow && (cleanRow.includes(cleanSelected) || cleanSelected.includes(cleanRow))) {
                matchSektor = true;
            }
        }

        const matchPerusahaan = (selectedPerusahaan === 'ALL') || (rowPerusahaan === selectedPerusahaan);

        if (matchSearch && matchSektor && matchPerusahaan) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}

const PESERTA_BASE_URL = "{{ url('peserta') }}";

function openDetailPesertaModalFromRow(rowElement) {
    try {
        const rawData = rowElement.getAttribute('data-peserta');
        const data = JSON.parse(rawData);
        openDetailPesertaModal(data);
    } catch(e) {
        console.error("Gagal parse data peserta:", e);
    }
}

function openEditPesertaModalFromRow(buttonElement) {
    try {
        const row = buttonElement.closest('tr');
        const rawData = row.getAttribute('data-peserta');
        const data = JSON.parse(rawData);
        openEditPesertaModal(data);
    } catch(e) {
        console.error("Gagal parse data edit:", e);
    }
}

function openImportPesertaModal() { document.getElementById('modalImportPeserta').classList.remove('hidden'); }
function closeImportPesertaModal() { document.getElementById('modalImportPeserta').classList.add('hidden'); }
function openCreatePesertaModal() { document.getElementById('modalCreatePeserta').classList.remove('hidden'); document.getElementById('formCreatePeserta').reset(); }
function closeCreatePesertaModal() { document.getElementById('modalCreatePeserta').classList.add('hidden'); }
function closeEditPesertaModal() { document.getElementById('modalEditPeserta').classList.add('hidden'); }

let isPesertaCardFlipped = false;
function putarKartuPeserta() {
    const flipper = document.getElementById('cardFlipperPeserta');
    if (!flipper) return;
    isPesertaCardFlipped = !isPesertaCardFlipped;
    flipper.style.transform = isPesertaCardFlipped ? 'rotateY(180deg)' : 'rotateY(0deg)';
}

function closeDetailPesertaModal() { 
    const modal = document.getElementById('modalDetailPeserta');
    const flipper = document.getElementById('cardFlipperPeserta');
    if (modal) modal.classList.add('hidden'); 
    if (flipper) {
        isPesertaCardFlipped = false;
        flipper.style.transform = 'rotateY(0deg)';
    }
}

function triggerDeleteConfirmation(id, name) { 
    document.getElementById('formConfirmDelete').action = PESERTA_BASE_URL + '/' + id; 
    document.getElementById('delete_target_name').innerText = name; 
    document.getElementById('modalDeleteConfirmation').classList.remove('hidden'); 
}
function closeDeleteConfirmation() { document.getElementById('modalDeleteConfirmation').classList.add('hidden'); }

let currentEditPeserta = null;

function showModalWarningProgram(progName) {
    const modal = document.getElementById('modalWarningProgramPeserta');
    const nameEl = document.getElementById('modal_warning_program_name');
    if (nameEl) nameEl.textContent = progName || '-';
    if (modal) modal.classList.remove('hidden');
}

function closeModalWarningProgram() {
    const modal = document.getElementById('modalWarningProgramPeserta');
    if (modal) modal.classList.add('hidden');
    if (currentEditPeserta && document.getElementById('edit_pelatihan')) {
        const orig = (currentEditPeserta.pelatihan || '').trim();
        document.getElementById('edit_pelatihan').value = orig;
    }
    const warningBox = document.getElementById('edit_pelatihan_warning');
    if (warningBox) warningBox.classList.add('hidden');
}

function onEditPelatihanChange() {
    if (!currentEditPeserta) return;
    const orig = (currentEditPeserta.pelatihan || '').trim();
    const sel = document.getElementById('edit_pelatihan');
    const current = (sel ? sel.value : '').trim();
    const warningBox = document.getElementById('edit_pelatihan_warning');
    const warningProg = document.getElementById('edit_pelatihan_warning_prog');

    const hasNilai = Boolean(currentEditPeserta.has_nilai);

    if (current !== orig && hasNilai) {
        if (warningProg) warningProg.textContent = orig;
        if (warningBox) warningBox.classList.remove('hidden');
        showModalWarningProgram(orig);
    } else {
        if (warningBox) warningBox.classList.add('hidden');
    }
}

function openEditPesertaModal(data) {
    currentEditPeserta = data;
    const modal = document.getElementById('modalEditPeserta');
    if (modal) {
        const warningBox = document.getElementById('edit_pelatihan_warning');
        if (warningBox) warningBox.classList.add('hidden');

        if (data && data.has_nilai === undefined && data.id) {
            fetch(`/ajax/check-peserta-nilai?peserta_id=${data.id}&program=${encodeURIComponent(data.pelatihan || '')}`)
                .then(r => r.json())
                .then(res => {
                    if (currentEditPeserta && currentEditPeserta.id === data.id) {
                        currentEditPeserta.has_nilai = res.has_nilai;
                        onEditPelatihanChange();
                    }
                })
                .catch(e => console.warn("Check nilai error:", e));
        }

        document.getElementById('formEditPeserta').action = PESERTA_BASE_URL + '/' + data.id;
        if(document.getElementById('edit_nama_lengkap')) document.getElementById('edit_nama_lengkap').value = data.nama_lengkap;
        if(document.getElementById('edit_gender')) document.getElementById('edit_gender').value = data.gender;
        if(document.getElementById('edit_email')) document.getElementById('edit_email').value = data.email;
        
        let phoneClean = (data.no_hp || '').replace(/^\+62|^62|^0/, '');
        if(document.getElementById('edit_no_hp')) document.getElementById('edit_no_hp').value = phoneClean;
        
        if(document.getElementById('edit_perusahaan_id')) document.getElementById('edit_perusahaan_id').value = data.perusahaan_id || '';
        if(document.getElementById('edit_pelatihan')) {
            const sel = document.getElementById('edit_pelatihan');
            const val = data.pelatihan ? data.pelatihan.trim() : '';
            let exists = false;
            for (let i = 0; i < sel.options.length; i++) {
                if (sel.options[i].value === val) {
                    exists = true;
                    break;
                }
            }
            if (!exists && val !== '') {
                const opt = document.createElement('option');
                opt.value = val;
                opt.textContent = val;
                sel.appendChild(opt);
            }
            sel.value = val;
        }

        if (data.ttl) {
            const parts = data.ttl.split(',');
            if (parts.length > 0 && document.getElementById('edit_tempat_lahir')) {
                document.getElementById('edit_tempat_lahir').value = parts[0].trim();
            }
            if (parts.length > 1 && document.getElementById('edit_tanggal_lahir')) {
                const tglRaw = parts[1].trim();
                const dParts = tglRaw.split('-');
                if (dParts.length === 3) {
                    document.getElementById('edit_tanggal_lahir').value = `${dParts[2]}-${dParts[1]}-${dParts[0]}`;
                }
            }
        }

        const prevImg = document.getElementById('edit_preview_peserta_foto');
        const prevPlc = document.getElementById('edit_preview_peserta_placeholder');
        if (data.foto && data.foto.trim() !== '') {
            prevImg.src = data.foto;
            prevImg.classList.remove('hidden');
            prevPlc.classList.add('hidden');
        } else {
            prevImg.src = '';
            prevImg.classList.add('hidden');
            prevPlc.classList.remove('hidden');
        }

        modal.classList.remove('hidden');
    }
}

function openDetailPesertaModal(data) {
    const modal = document.getElementById('modalDetailPeserta');
    if (modal && modal.parentElement !== document.body) {
        document.body.appendChild(modal);
    }
    const flipper = document.getElementById('cardFlipperPeserta');
    if (flipper) {
        isPesertaCardFlipped = false;
        flipper.style.transform = 'rotateY(0deg)';
    }
    if (modal) {
        if (document.getElementById('detail_peserta_nama')) document.getElementById('detail_peserta_nama').innerText = data.nama_lengkap || '-';
        if (document.getElementById('detail_peserta_sector')) document.getElementById('detail_peserta_sector').innerText = data.pelatihan || '-';
        if (document.getElementById('detail_peserta_email')) document.getElementById('detail_peserta_email').innerText = data.email || '-';
        if (document.getElementById('detail_peserta_no_hp')) document.getElementById('detail_peserta_no_hp').innerText = data.no_hp || '-';
        if (document.getElementById('detail_peserta_ttl')) document.getElementById('detail_peserta_ttl').innerText = data.ttl || '-';
        if (document.getElementById('detail_peserta_kerja')) document.getElementById('detail_peserta_kerja').innerText = data.tempat_kerja || '-';
        
        if (document.getElementById('detail_peserta_gender')) {
            const g = (data.gender || '').toUpperCase();
            let gText = '-';
            if (g === 'PRIA' || g === 'L') gText = 'LAKI-LAKI';
            else if (g === 'WANITA' || g === 'P') gText = 'PEREMPUAN';
            else if (g) gText = g;
            document.getElementById('detail_peserta_gender').innerText = gText;
        }

        if (document.getElementById('detail_peserta_wa_link')) {
            let clean = String(data.no_hp || '').replace(/[^0-9]/g, '');
            if (clean.startsWith('0')) clean = '62' + clean.substring(1);
            else if (!clean.startsWith('62') && clean.length > 0) clean = '62' + clean;

            const waLink = document.getElementById('detail_peserta_wa_link');
            if (clean) {
                waLink.href = 'https://wa.me/' + clean;
                waLink.style.pointerEvents = 'auto';
            } else {
                waLink.href = '#';
                waLink.style.pointerEvents = 'none';
            }
        }

        const imgEl = document.getElementById('detail_peserta_foto');
        const noImgEl = document.getElementById('detail_peserta_no_img');
        if (imgEl && noImgEl) {
            if (data.foto && data.foto.trim() !== '') {
                imgEl.src = data.foto;
                imgEl.classList.remove('hidden');
                noImgEl.classList.add('hidden');
            } else {
                imgEl.src = '';
                imgEl.classList.add('hidden');
                noImgEl.classList.remove('hidden');
            }
        }

        const statusBadge = document.getElementById('detail_peserta_status');
        if (statusBadge) {
            const st = (data.status || 'AKTIF').toUpperCase();
            statusBadge.innerText = st;

            if (st === 'AKTIF') {
                statusBadge.className = 'inline-block text-[8px] font-black uppercase px-2.5 py-0.5 tracking-wider bg-emerald-100 text-emerald-800 rounded-xs shadow-2xs';
            } else if (st === 'NON-AKTIF') {
                statusBadge.className = 'inline-block text-[8px] font-black uppercase px-2.5 py-0.5 tracking-wider bg-rose-100 text-rose-800 rounded-xs shadow-2xs';
            } else {
                statusBadge.className = 'inline-block text-[8.5px] font-black uppercase px-2.5 py-0.5 tracking-wider bg-amber-100 text-amber-800 rounded-xs shadow-2xs';
            }
        }

        modal.classList.remove('hidden');
    }
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        const warningModal = document.getElementById('modalWarningProgramPeserta');
        if (warningModal && !warningModal.classList.contains('hidden')) {
            closeModalWarningProgram();
            return;
        }
        closeDetailPesertaModal();
        closeEditPesertaModal();
        closeCreatePesertaModal();
        closeImportPesertaModal();
        closeDeleteConfirmation();
    }
});

document.addEventListener('DOMContentLoaded', function() {
    ['modalDetailPeserta', 'modalCreatePeserta', 'modalEditPeserta', 'modalImportPeserta', 'modalDeleteConfirmation', 'modalWarningProgramPeserta'].forEach(function(id) {
        const el = document.getElementById(id);
        if (el && el.parentElement !== document.body) {
            document.body.appendChild(el);
        }
    });

    const selEditPelatihan = document.getElementById('edit_pelatihan');
    if (selEditPelatihan) {
        selEditPelatihan.addEventListener('change', onEditPelatihanChange);
    }

    const formEdit = document.getElementById('formEditPeserta');
    if (formEdit) {
        formEdit.addEventListener('submit', function(e) {
            if (!currentEditPeserta) return;
            const orig = (currentEditPeserta.pelatihan || '').trim();
            const sel = document.getElementById('edit_pelatihan');
            const current = (sel ? sel.value : '').trim();
            const hasNilai = Boolean(currentEditPeserta.has_nilai);

            if (current !== orig && hasNilai) {
                e.preventDefault();
                e.stopPropagation();

                const warningBox = document.getElementById('edit_pelatihan_warning');
                const warningProg = document.getElementById('edit_pelatihan_warning_prog');
                if (warningProg) warningProg.textContent = orig;
                if (warningBox) {
                    warningBox.classList.remove('hidden');
                }

                showModalWarningProgram(orig);
                return false;
            }
        });
    }
});

function previewDanValidasiFoto(input, labelId, imgId = null, plcId = null) {
    const file = input.files[0];
    const maxUkuran = 2 * 1024 * 1024;
    const labelElem = document.getElementById(labelId);

    if (file) {
        if (file.size > maxUkuran) {
            alert('⚠️ UKURAN FOTO MELEBIHI 2 MB!\nSilakan gunakan foto dengan ukuran maksimal 2.00 MB.');
            input.value = '';
            labelElem.innerText = 'Pilih berkas foto...';
            return;
        }
        labelElem.innerText = file.name;

        if (imgId && plcId) {
            const imgElem = document.getElementById(imgId);
            const plcElem = document.getElementById(plcId);
            const reader = new FileReader();
            reader.onload = function(e) {
                imgElem.src = e.target.result;
                imgElem.classList.remove('hidden');
                plcElem.classList.add('hidden');
            };
            reader.readAsDataURL(file);
        }
    }
}
</script>

