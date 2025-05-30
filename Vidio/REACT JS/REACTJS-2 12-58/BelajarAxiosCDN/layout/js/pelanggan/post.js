import { apiUrl } from '../config.js';

export function showPostForm() {
    const dataContainer = document.getElementById('data-container');
    if (!dataContainer) {
        console.error('Element #data-container tidak ditemukan.');
        return;
    }
    dataContainer.innerHTML = `
        <div class="card">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0">Tambah Pelanggan Baru</h5>
            </div>
            <div class="card-body">
                <form id="formTambahPelanggan">
                    <div class="mb-3">
                        <label for="pelanggan" class="form-label">Nama Pelanggan</label>
                        <input type="text" class="form-control" id="pelanggan" name="pelanggan" required>
                    </div>
                    <div class="mb-3">
                        <label for="alamat" class="form-label">Alamat</label>
                        <input type="text" class="form-control" id="alamat" name="alamat" required>
                    </div>
                    <div class="mb-3">
                        <label for="telp" class="form-label">Telepon</label>
                        <input type="text" class="form-control" id="telp" name="telp" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                    <button type="button" class="btn btn-secondary" onclick="window.app.getPelanggan()">Batal</button>
                </form>
            </div>
        </div>
        <div id="post-message" class="mt-3"></div>
    `;

    const formTambahPelanggan = document.getElementById('formTambahPelanggan');
    if (formTambahPelanggan) {
        formTambahPelanggan.addEventListener('submit', function(event) {
            event.preventDefault();
            postPelanggan(); // Panggil fungsi postPelanggan yang juga di-export atau di-scope modul ini
        });
    }
}

function postPelanggan() { // Dijadikan fungsi lokal, dipanggil oleh event listener di showPostForm
    const pelanggan = document.getElementById('pelanggan').value;
    const alamat = document.getElementById('alamat').value;
    const telp = document.getElementById('telp').value;
    const postMessageContainer = document.getElementById('post-message');
    if (!postMessageContainer) return;

    postMessageContainer.innerHTML = '<p class="text-info">Mengirim data...</p>';

    axios({
        method: 'post',
        url: apiUrl,
        data: { pelanggan, alamat, telp }
    })
    .then(function (response) {
        console.log("Data berhasil dikirim:", response.data);
        postMessageContainer.innerHTML = '<div class="alert alert-success">Pelanggan berhasil ditambahkan!</div>';
        const form = document.getElementById('formTambahPelanggan');
        if (form) form.reset();
        setTimeout(() => {
            if (window.app && window.app.getPelanggan) {
                window.app.getPelanggan();
            }
        }, 2000);
    })
    .catch(function (error) {
        console.error("Gagal mengirim data:", error);
        let errorMessage = 'Gagal menambahkan pelanggan.';
        if (error.response) {
            if (error.response.data && error.response.data.message) {
                errorMessage = error.response.data.message;
            } else if (error.response.data) {
                let errors = [];
                for (const key in error.response.data) {
                    if (Array.isArray(error.response.data[key])) {
                        errors.push(`${key}: ${error.response.data[key].join(', ')}`);
                    }
                }
                if (errors.length > 0) errorMessage += '<br>' + errors.join('<br>');
            }
        } else if (error.request) {
            errorMessage = 'Tidak dapat terhubung ke server.';
        } else {
            errorMessage = `Terjadi kesalahan: ${error.message}`;
        }
        postMessageContainer.innerHTML = `<div class="alert alert-danger">${errorMessage}</div>`;
    });
}