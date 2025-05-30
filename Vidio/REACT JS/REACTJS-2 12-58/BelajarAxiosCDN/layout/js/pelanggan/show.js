import { apiUrl } from '../config.js';

export function showPelanggan(id) {
    const pelangganId = id || 1; 
    const dataContainer = document.getElementById('data-container');
    if (!dataContainer) {
        console.error('Element #data-container tidak ditemukan.');
        return;
    }
    dataContainer.innerHTML = `<p class="text-center">Memuat data pelanggan dengan ID: ${pelangganId}...</p>`;
    
    axios({
        method: 'get',
        url: `${apiUrl}/${pelangganId}`
    })
    .then(function (response) {
        console.log("Data pelanggan berhasil diambil:", response.data);
        dataContainer.innerHTML = '';
        const item = response.data;
        
        const card = document.createElement('div');
        card.className = 'card';
        card.innerHTML = `
            <div class="card-header bg-info text-white">
                <h5 class="mb-0">Detail Pelanggan ID: ${item.idpelanggan || '-'}</h5>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr><th width="150">ID Pelanggan</th><td>: ${item.idpelanggan || '-'}</td></tr>
                    <tr><th>Nama</th><td>: ${item.pelanggan || '-'}</td></tr>
                    <tr><th>Alamat</th><td>: ${item.alamat || '-'}</td></tr>
                    <tr><th>Telepon</th><td>: ${item.telp || '-'}</td></tr>
                </table>
            </div>
            <div class="card-footer">
                <button class="btn btn-secondary" onclick="window.app.getPelanggan()">Kembali ke Daftar</button>
            </div>
        `;
        dataContainer.appendChild(card);
    })
    .catch(function (error) {
        if (error.response) {
            console.log("Error Data:", error.response.data);
            console.log("Error Status:", error.response.status);
            if (error.response.status === 404) {
                dataContainer.innerHTML = `<div class="alert alert-warning">Pelanggan dengan ID ${pelangganId} tidak ditemukan.</div>`;
            } else {
                dataContainer.innerHTML = `<div class="alert alert-danger">Gagal memuat data pelanggan dengan ID ${pelangganId}. Status: ${error.response.status}</div>`;
            }
        } else if (error.request) {
            dataContainer.innerHTML = `<div class="alert alert-danger">Tidak dapat terhubung ke server untuk mengambil data pelanggan ID ${pelangganId}.</div>`;
        } else {
            dataContainer.innerHTML = `<div class="alert alert-danger">Terjadi kesalahan: ${error.message}</div>`;
        }
        console.log("Error Config:", error.config);
    });
}