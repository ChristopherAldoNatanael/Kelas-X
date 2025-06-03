import { apiUrl } from '../config.js';

export function deletePelanggan(id) {
    if (!confirm(`Apakah Anda yakin ingin menghapus pelanggan dengan ID: ${id}?`)) {
        return;
    }

    const dataContainer = document.getElementById('data-container');
    let messageDiv = document.getElementById('action-message');
    if (!messageDiv && dataContainer) {
        messageDiv = document.createElement('div');
        messageDiv.id = 'action-message';
        messageDiv.className = 'mt-3';
        dataContainer.parentNode.insertBefore(messageDiv, dataContainer);
    }
    if (messageDiv) {
         messageDiv.innerHTML = `<p class="text-info text-center">Menghapus data pelanggan ID: ${id}...</p>`;
    }
    
    axios({
        method: 'delete',
        url: `${apiUrl}/${id}`
    })
    .then(function (response) {
        console.log("Data berhasil dihapus:", response.data);
        if (messageDiv) {
            messageDiv.innerHTML = `<div class="alert alert-success text-center">Pelanggan dengan ID: ${id} berhasil dihapus.</div>`;
        }
        if (window.app && window.app.getPelanggan) {
            window.app.getPelanggan();
        }
        setTimeout(() => {
            if (messageDiv) messageDiv.innerHTML = '';
        }, 3000);
    })
    .catch(function (error) {
        console.error("Gagal menghapus data:", error);
        let errorMessage = `Gagal menghapus pelanggan dengan ID: ${id}.`;
        if (error.response) {
            if (error.response.data && error.response.data.message) {
                errorMessage = error.response.data.message;
            } else if (error.response.status === 404) {
                errorMessage = `Pelanggan dengan ID: ${id} tidak ditemukan.`;
            }
        } else if (error.request) {
            errorMessage = 'Tidak dapat terhubung ke server.';
        } else {
            errorMessage = `Terjadi kesalahan: ${error.message}`;
        }
        if (messageDiv) {
            messageDiv.innerHTML = `<div class="alert alert-danger text-center">${errorMessage}</div>`;
        }
    });
}