(function () {
    document.addEventListener('DOMContentLoaded', function () {
        // Realtime System Engine Clock Component
        function updateClock() {
            const clockElement = document.getElementById('realtime-clock');
            if (!clockElement) return;

            const options = {
                timeZone: 'Asia/Jakarta',
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit',
                hour12: false
            };

            const formatter = new Intl.DateTimeFormat('id-ID', options);
            let timeString = formatter.format(new Date());
            timeString = timeString.replace(/\./g, ':');
            clockElement.textContent = timeString;
        }
        
        if (document.getElementById('realtime-clock')) {
            updateClock();
            setInterval(updateClock, 1000);
        }

        // Date Interlock Restriction Constraints
        const filterStart = document.getElementById('filter_start_date');
        const filterEnd = document.getElementById('filter_end_date');

        if (filterStart && filterEnd) {
            filterStart.addEventListener('change', function() {
                if (this.value) {
                    filterEnd.min = this.value;
                    if (filterEnd.value && filterEnd.value < this.value) filterEnd.value = '';
                } else {
                    filterEnd.removeAttribute('min');
                }
            });

            filterEnd.addEventListener('change', function() {
                if (this.value) {
                    filterStart.max = this.value;
                    if (filterStart.value && filterStart.value > this.value) filterStart.value = '';
                } else {
                    filterStart.removeAttribute('max');
                }
            });

            if (filterStart.value) filterEnd.min = filterStart.value;
            if (filterEnd.value) filterStart.max = filterEnd.value;
        }

        // Ekspos Fungsi Modal ke Scope Window agar tetap bisa dipanggil dari atribut tag HTML onclick
        window.openCreateModal = function() { 
            const m = document.getElementById('modalJadwal');
            if(m) m.classList.remove('hidden');
        }
        window.closeCreateModal = function() { 
            const m = document.getElementById('modalJadwal');
            if(m) m.classList.add('hidden');
        }

        window.openEditModal = function(data, isBase64 = false) {
            const modal = document.getElementById('modalEditJadwal');
            const form = document.getElementById('formEditJadwal');
            
            if(!modal || !form) return;
            
            form.action = `/jadwal/${data.id}`;
            
            let judulClean = isBase64 ? atob(data.judul) : data.judul;
            let descClean = isBase64 ? atob(data.description) : data.description;

            if(document.getElementById('edit_judul')) document.getElementById('edit_judul').value = judulClean;
            if(document.getElementById('edit_description')) document.getElementById('edit_description').value = descClean;
            if(document.getElementById('edit_trainer')) document.getElementById('edit_trainer').value = data.trainer_id;
            if(document.getElementById('edit_jenis')) document.getElementById('edit_jenis').value = data.jenis.toLowerCase();
            
            if(data.tgl_mulai && document.getElementById('edit_tgl_mulai')) {
                document.getElementById('edit_tgl_mulai').value = data.tgl_mulai;
                document.getElementById('edit_tgl_selesai').min = data.tgl_mulai;
            }
            if(data.tgl_selesai && document.getElementById('edit_tgl_selesai')) {
                document.getElementById('edit_tgl_selesai').value = data.tgl_selesai;
            }
            
            if(data.jam_mulai && document.getElementById('edit_jam_mulai')) {
                document.getElementById('edit_jam_mulai').value = data.jam_mulai.substring(0, 5);
            }
            if(data.jam_selesai && document.getElementById('edit_jam_selesai')) {
                document.getElementById('edit_jam_selesai').value = data.jam_selesai.substring(0, 5);
            }

            modal.classList.remove('hidden');
        }

        window.closeEditModal = function() {
            const modal = document.getElementById('modalEditJadwal');
            if(modal) modal.classList.add('hidden');
        }

        window.triggerDeleteModal = function(id, title) {
            const modal = document.getElementById('modalDeleteJadwal');
            const form = document.getElementById('globalDeleteForm');
            const titleTarget = document.getElementById('deleteTargetTitle');
            const btnConfirm = document.getElementById('btnConfirmDelete');
            
            if(!modal || !form || !titleTarget) return;
            
            titleTarget.innerText = title;
            form.action = `/jadwal/${id}`;
            
            btnConfirm.onclick = function() {
                form.submit();
            };
            
            modal.classList.remove('hidden');
        }

        window.closeDeleteModal = function() {
            const modal = document.getElementById('modalDeleteJadwal');
            if(modal) modal.classList.add('hidden');
        }
    });
})();