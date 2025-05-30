import { apiUrl } from '../config.js';

// Fungsi untuk menampilkan data pelanggan
export function getPelanggan() {
    const dataContainer = document.getElementById('data-container');
    if (!dataContainer) {
        console.error('Element #data-container tidak ditemukan.');
        return;
    }
    dataContainer.innerHTML = '<p class="text-center">Memuat data...</p>';
    
    axios({
        method: 'get',
        url: apiUrl
    })
    .then(function (response) {
        console.log("Data berhasil diambil:", response.data);
        
        dataContainer.innerHTML = '';
        
        if (response.data && response.data.length > 0) {
            const table = document.createElement('table');
            table.className = 'table table-striped table-hover';
            
            const thead = document.createElement('thead');
            thead.innerHTML = `
                <tr class="table-dark">
                    <th>ID</th>
                    <th>Nama Pelanggan</th>
                    <th>Alamat</th>
                    <th>Telepon</th>
                    <th>Aksi</th>
                </tr>
            `;
            table.appendChild(thead);
            
            const tbody = document.createElement('tbody');
            response.data.forEach(item => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>${item.idpelanggan || '-'}</td>
                    <td>${item.pelanggan || '-'}</td>
                    <td>${item.alamat || '-'}</td>
                    <td>${item.telp || '-'}</td>
                    <td>
                        <button class="btn btn-sm btn-info" onclick="window.app.showPelanggan(${item.idpelanggan})">Detail</button>
                        <button class="btn btn-sm btn-warning" onclick="window.app.prepareUpdate(${item.idpelanggan})">Edit</button>
                        <button class="btn btn-sm btn-danger" onclick="window.app.deletePelanggan(${item.idpelanggan})">Hapus</button>
                    </td>
                `;
                tbody.appendChild(tr);
            });
            
            table.appendChild(tbody);
            dataContainer.appendChild(table);
        } else {
            dataContainer.innerHTML = '<div class="alert alert-info">Tidak ada data pelanggan yang ditemukan.</div>';
        }
    })
    .catch(function (error) {
        dataContainer.innerHTML = '<div class="alert alert-danger">Gagal memuat data. Silakan cek konsol.</div>';
        if (error.response) {
            console.log("Error Data:", error.response.data);
            console.log("Error Status:", error.response.status);
            console.log("Error Headers:", error.response.headers);
        } else if (error.request) {
            console.log("Error Request:", error.request);
        } else {
            console.log('Error Message:', error.message);
        }
        console.log("Error Config:", error.config);
    });
}