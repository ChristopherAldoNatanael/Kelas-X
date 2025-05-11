<?php 
    $jumlahdata = $db->rowCOUNT("SELECT iduser FROM tbluser");
    $banyak = 4;

    $halaman = ceil($jumlahdata / $banyak);

    if (isset($_GET['p'])) {
        $p = $_GET['p'];
        $mulai = ($p * $banyak) - $banyak;
    } else {
        $mulai = 0;
        $p = 1;
    }

    $sql = "SELECT * FROM tbluser ORDER BY user ASC LIMIT $mulai,$banyak";
    $row = $db->getALL($sql);

    $no = 1 + $mulai;
?>

<div class="user-management-container">
    <div class="header-section">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="section-title">Manajemen User</h3>
                <p class="section-subtitle">Kelola semua akun pengguna sistem</p>
            </div>
            <div>
                <a class="btn btn-primary add-user-btn" href="?f=user&m=insert">
                    <i class="fas fa-user-plus mr-2"></i>Tambah User Baru
                </a>
            </div>
        </div>
    </div>

    <div class="card user-table-card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <i class="fas fa-users mr-2"></i>
                    <span class="font-weight-bold">Daftar User</span>
                </div>
                <div class="user-count">
                    <span class="badge badge-primary">Total: <?php echo $jumlahdata; ?> user</span>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover user-table mb-0">
                    <thead class="thead-primary">
                        <tr>
                            <th width="5%">No</th>
                            <th width="25%">Username</th>
                            <th width="30%">Email</th>
                            <th width="15%">Level</th>
                            <th width="10%">Status</th>
                            <th width="15%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(!empty($row)): ?>
                            <?php foreach ($row as $r): ?>
                                <?php 
                                    if ($r['aktif'] == 1) {
                                        $status = '<span class="badge badge-success">AKTIF</span>';
                                        $statusClass = 'text-success';
                                    } else {
                                        $status = '<span class="badge badge-danger">BANNED</span>';
                                        $statusClass = 'text-danger';
                                    }
                                    
                                    switch($r['level']) {
                                        case 'admin':
                                            $levelBadge = '<span class="badge badge-primary">Admin</span>';
                                            break;
                                        case 'kasir':
                                            $levelBadge = '<span class="badge badge-info">Kasir</span>';
                                            break;
                                        case 'koki':
                                            $levelBadge = '<span class="badge badge-warning">Koki</span>';
                                            break;
                                        default:
                                            $levelBadge = '<span class="badge badge-secondary">'.$r['level'].'</span>';
                                    }
                                ?>
                                <tr>
                                    <td class="text-center"><?php echo $no++; ?></td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="user-avatar mr-2">
                                                <i class="fas fa-user-circle"></i>
                                            </div>
                                            <div class="user-name font-weight-bold">
                                                <?php echo $r['user']; ?>
                                            </div>
                                        </div>
                                    </td>
                                    <td><?php echo $r['email']; ?></td>
                                    <td><?php echo $levelBadge; ?></td>
                                    <td class="<?php echo $statusClass; ?>"><?php echo $status; ?></td>
                                    <td>
                                        <div class="btn-group action-buttons">
                                            <a href="?f=user&m=update&id=<?php echo $r['iduser']; ?>" class="btn btn-sm btn-outline-primary" title="Ubah Status">
                                                <i class="fas fa-sync-alt"></i>
                                            </a>
                                            <a href="?f=user&m=delete&id=<?php echo $r['iduser']; ?>" class="btn btn-sm btn-outline-danger" title="Hapus" onclick="return confirm('Apakah Anda yakin ingin menghapus user ini?')">
                                                <i class="fas fa-trash-alt"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center py-4">
                                    <div class="empty-state">
                                        <i class="fas fa-users-slash fa-3x mb-3 text-muted"></i>
                                        <p>Tidak ada data user yang tersedia</p>
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
                                <a class="page-link" href="?f=user&m=select&p=<?php echo $p-1; ?>" aria-label="Previous">
                                    <span aria-hidden="true">&laquo;</span>
                                </a>
                            </li>
                        <?php endif; ?>
                        
                        <?php for ($i=1; $i <= $halaman; $i++): ?>
                            <li class="page-item <?php echo ($i == $p) ? 'active' : ''; ?>">
                                <a class="page-link" href="?f=user&m=select&p=<?php echo $i; ?>"><?php echo $i; ?></a>
                            </li>
                        <?php endfor; ?>
                        
                        <?php if($p < $halaman): ?>
                            <li class="page-item">
                                <a class="page-link" href="?f=user&m=select&p=<?php echo $p+1; ?>" aria-label="Next">
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
    .user-management-container {
        padding: 15px;
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
    
    .add-user-btn {
        border-radius: 50px;
        padding: 8px 20px;
        font-weight: 500;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        transition: all 0.3s ease;
    }
    
    .add-user-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.15);
    }
    
    .user-table-card {
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
    
    .user-count .badge {
        font-size: 0.8rem;
        padding: 5px 10px;
        border-radius: 20px;
    }
    
    .thead-primary {
        background-color: var(--secondary);
        color: white;
    }
    
    .user-table th {
        font-weight: 500;
        border: none;
        padding: 15px;
    }
    
    .user-table td {
        padding: 12px 15px;
        vertical-align: middle;
        border-top: 1px solid #f0f0f0;
    }
    
    .user-avatar {
        font-size: 1.5rem;
        color: #6c757d;
    }
    
    .badge {
        padding: 5px 10px;
        border-radius: 20px;
        font-weight: 500;
        font-size: 0.75rem;
    }
    
    .action-buttons .btn {
        padding: 5px 10px;
        margin: 0 2px;
        border-radius: 5px;
    }
    
    .action-buttons .btn:hover {
        transform: translateY(-2px);
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
        color: #6c757d;
    }
</style>
