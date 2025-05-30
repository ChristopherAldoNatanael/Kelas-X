import { apiUrl } from '../config.js';

export function prepareUpdate(id) {
    const dataContainer = document.getElementById('data-container');
    if (!dataContainer) return;
    dataContainer.innerHTML = `<p class="text-center">Memuat data pelanggan ID: ${id} untuk diupdate...</p>`;

    axios({
        method: 'get',
        url: `${apiUrl}/${id}`
    })
    .then(function (response) {
        const pelanggan = response.data;
        if (!pelanggan || pelanggan.idpelanggan === undefined) {
            dataContainer.innerHTML = `<div class="alert alert-warning">Pelanggan dengan ID ${id} tidak ditemukan atau data tidak lengkap.</div>`;
            return;
        }

        dataContainer.innerHTML = `
            <div class="card">
                <div class="card-header bg-warning text-dark"><h5 class="mb-0">Update Pelanggan ID: ${pelanggan.idpelanggan}</h5></div>
                <div class="card-body">
                    <form id="formUpdatePelanggan">
                        <input type="hidden" id="idpelanggan-update" value="${pelanggan.idpelanggan}">
                        <div class="mb-3">
                            <label for="pelanggan-update" class="form-label">Nama Pelanggan</label>
                            <input type="text" class="form-control" id="pelanggan-update" name="pelanggan" value="${pelanggan.pelanggan || ''}" required>
                        </div>
                        <div class="mb-3">
                            <label for="alamat-update" class="form-label">Alamat</label>
                            <input type="text" class="form-control" id="alamat-update" name="alamat" value="${pelanggan.alamat || ''}" required>
                        </div>
                        <div class="mb-3">
                            <label for="telp-update" class="form-label">Telepon</label>
                            <input type="text" class="form-control" id="telp-update" name="telp" value="${pelanggan.telp || ''}" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                        <button type="button" class="btn btn-secondary" onclick="window.app.getPelanggan()">Batal</button>
                    </form>
                </div>
            </div>
            <div id="update-message" class="mt-3"></div>`;
        
        const formUpdatePelanggan = document.getElementById('formUpdatePelanggan');
        if (formUpdatePelanggan) {
            formUpdatePelanggan.addEventListener('submit', function(event) {
                event.preventDefault();
                submitUpdatePelanggan(pelanggan.idpelanggan); 
            });
        }
    })
    .catch(function (error) {
        console.error("Gagal mengambil data untuk update:", error);
        let errorMessage = `Gagal mengambil data pelanggan dengan ID: ${id}.`;
        if (error.response) {
            errorMessage = error.response.status === 404 ? `Pelanggan dengan ID ${id} tidak ditemukan.` : `Gagal mengambil data pelanggan ID ${id}. Status: ${error.response.status}`;
        } else if (error.request) {
            errorMessage = `Tidak dapat terhubung ke server untuk mengambil data pelanggan ID ${id}.`;
        }
        dataContainer.innerHTML = `<div class="alert alert-danger">${errorMessage}</div>`;
    });
}

function submitUpdatePelanggan(id) { // Fungsi lokal
    const pelangganNama = document.getElementById('pelanggan-update').value;
    const alamat = document.getElementById('alamat-update').value;
    const telp = document.getElementById('telp-update').value;
    const updateMessageContainer = document.getElementById('update-message');
    if (!updateMessageContainer) return;

    updateMessageContainer.innerHTML = '<p class="text-info">Mengirim pembaruan...</p>';

    axios({
        method: 'put',
        url: `${apiUrl}/${id}`,
        data: { pelanggan: pelangganNama, alamat, telp }
    })
    .then(function (response) {
        console.log("Data berhasil diupdate:", response.data);
        updateMessageContainer.innerHTML = '<div class="alert alert-success">Pelanggan berhasil diupdate!</div>';
        setTimeout(() => {
            if (window.app && window.app.getPelanggan) {
                window.app.getPelanggan();
            }
        }, 2000);
    })
    .catch(function (error) {
        console.error("Gagal mengupdate data:", error);
        let errorMessage = `Gagal mengupdate pelanggan dengan ID: ${id}.`;
        if (error.response) {
            if (error.response.data && (typeof error.response.data === 'string' || error.response.data.message)) {
                errorMessage = typeof error.response.data === 'string' ? error.response.data : error.response.data.message;
            } else if (error.response.data) {
                let errors = [];
                for (const key in error.response.data) {
                    if (Array.isArray(error.response.data[key])) errors.push(`${key}: ${error.response.data[key].join(', ')}`);
                }
                if (errors.length > 0) errorMessage += '<br>' + errors.join('<br>');
            }
        } else if (error.request) {
            errorMessage = 'Tidak dapat terhubung ke server.';
        } else {
            errorMessage = `Terjadi kesalahan: ${error.message}`;
        }
        updateMessageContainer.innerHTML = `<div class="alert alert-danger">${errorMessage}</div>`;
    });
}