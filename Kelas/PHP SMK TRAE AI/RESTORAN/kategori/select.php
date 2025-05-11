<?php 
    $jumlahdata = $db->rowCOUNT("SELECT idkategori FROM tblkategori");
    $banyak = 4;

    $halaman = ceil($jumlahdata / $banyak);

    if (isset($_GET['p'])) {
        $p = $_GET['p'];
        $mulai = ($p * $banyak) - $banyak;
    } else {
        $mulai = 0;
        $p = 1;
    }

    $sql = "SELECT * FROM tblkategori ORDER BY kategori ASC LIMIT $mulai,$banyak ";
    $row = $db->getALL($sql);

    $no = 1 + $mulai;
?>

<div class="kategori-container">
    <div class="header-section">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="section-title">Manajemen Kategori</h3>
                <p class="section-subtitle">Kelola kategori menu restoran Anda</p>
            </div>
            <div>
                <a class="btn btn-primary add-btn" href="?f=kategori&m=insert">
                    <i class="fas fa-plus-circle mr-2"></i>Tambah Kategori
                </a>
            </div>
        </div>
    </div>

    <div class="card kategori-card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <i class="fas fa-list-alt mr-2"></i>
                    <span class="font-weight-bold">Daftar Kategori</span>
                </div>
                <div class="kategori-count">
                    <span class="badge badge-primary">Total: <?php echo $jumlahdata; ?> kategori</span>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover kategori-table mb-0">
                    <thead class="thead-primary">
                        <tr>
                            <th width="10%">No</th>
                            <th width="50%">Nama Kategori</th>
                            <th width="20%" class="text-center">Hapus</th>
                            <th width="20%" class="text-center">Ubah</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(!empty($row)): ?>
                            <?php foreach ($row as $r): ?>
                                <tr class="table-row-hover">
                                    <td class="text-center"><?php echo $no++; ?></td>
                                    <td>
                                        <div class="kategori-name">
                                            <i class="fas fa-tag text-primary mr-2"></i>
                                            <?php echo $r['kategori']; ?>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <a href="?f=kategori&m=delete&id=<?php echo $r['idkategori']; ?>" class="btn btn-sm btn-outline-danger action-btn" onclick="return confirm('Apakah Anda yakin ingin menghapus kategori ini?')">
                                            <i class="fas fa-trash-alt mr-1"></i> Hapus
                                        </a>
                                    </td>
                                    <td class="text-center">
                                        <a href="?f=kategori&m=update&id=<?php echo $r['idkategori']; ?>" class="btn btn-sm btn-outline-success action-btn">
                                            <i class="fas fa-edit mr-1"></i> Ubah
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="text-center py-4">
                                    <div class="empty-state">
                                        <i class="fas fa-folder-open fa-3x mb-3 text-muted"></i>
                                        <p>Tidak ada data kategori yang tersedia</p>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer">
            <div class="pagination-container">
                <nav aria-label="Page navigation">
                    <ul class="pagination justify-content-center mb-0">
                        <?php if($p > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="?f=kategori&m=select&p=<?php echo $p-1; ?>" aria-label="Previous">
                                    <span aria-hidden="true">&laquo;</span>
                                </a>
                            </li>
                        <?php endif; ?>
                        
                        <?php for ($i=1; $i <= $halaman; $i++): ?>
                            <li class="page-item <?php echo ($i == $p) ? 'active' : ''; ?>">
                                <a class="page-link" href="?f=kategori&m=select&p=<?php echo $i; ?>"><?php echo $i; ?></a>
                            </li>
                        <?php endfor; ?>
                        
                        <?php if($p < $halaman): ?>
                            <li class="page-item">
                                <a class="page-link" href="?f=kategori&m=select&p=<?php echo $p+1; ?>" aria-label="Next">
                                    <span aria-hidden="true">&raquo;</span>
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
</div>

<style>
    :root {
        --primary: #4e73df;
        --secondary: #2e294e;
        --success: #1cc88a;
        --info: #36b9cc;
        --warning: #f6c23e;
        --danger: #e74a3b;
        --light: #f8f9fc;
        --dark: #5a5c69;
    }
    
    .kategori-container {
        padding: 15px;
        background-color: #f8f9fc;
        border-radius: 10px;
    }
    
    .header-section {
        margin-bottom: 25px;
    }
    
    .section-title {
        color: var(--secondary);
        font-weight: 600;
        border-left: 4px solid var(--primary);
        padding-left: 12px;
        margin-bottom: 5px;
    }
    
    .section-subtitle {
        color: #6c757d;
        font-size: 0.9rem;
    }
    
    .add-btn {
        border-radius: 50px;
        padding: 8px 20px;
        font-weight: 500;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        transition: all 0.3s ease;
    }
    
    .add-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.15);
    }
    
    .kategori-card {
        border: none;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    }
    
    .card-header {
        background-color: #f8f9fa;
        border-bottom: 1px solid rgba(0,0,0,0.05);
        padding: 15px 20px;
    }
    
    .kategori-count .badge {
        font-size: 0.8rem;
        padding: 5px 10px;
        border-radius: 20px;
    }
    
    .thead-primary {
        background-color: var(--secondary);
        color: white;
    }
    
    .kategori-table th {
        font-weight: 500;
        border: none;
        padding: 15px;
    }
    
    .kategori-table td {
        padding: 12px 15px;
        vertical-align: middle;
        border-top: 1px solid #f0f0f0;
    }
    
    .table-row-hover:hover {
        background-color: rgba(78, 115, 223, 0.05);
        transition: all 0.3s ease;
    }
    
    .kategori-name {
        font-weight: 500;
        color: var(--dark);
        display: flex;
        align-items: center;
    }
    
    .action-btn {
        border-radius: 50px;
        transition: all 0.3s ease;
    }
    
    .action-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
    
    .pagination-container {
        padding: 15px 0;
    }
    
    .pagination .page-link {
        border-radius: 5px;
        margin: 0 3px;
        color: var(--secondary);
        border-color: #dee2e6;
        padding: 6px 12px;
        font-weight: 500;
    }
    
    .pagination .page-item.active .page-link {
        background-color: var(--primary);
        border-color: var(--primary);
    }
    
    .empty-state {
        padding: 20px;
        text-align: center;
        color: #6c757d;
    }
</style>