<?php 
    $jumlahdata = $db->rowCOUNT("SELECT idpelanggan FROM tblpelanggan");
    $banyak = 4;

    $halaman = ceil($jumlahdata / $banyak);

    if (isset($_GET['p'])) {
        $p = $_GET['p'];
        $mulai = ($p * $banyak) - $banyak;
    } else {
        $mulai = 0;
    }

    $sql = "SELECT * FROM tblpelanggan ORDER BY pelanggan ASC LIMIT $mulai,$banyak ";
    $row = $db->getALL($sql);

    $no = 1 + $mulai;
?>

<div class="customer-list-container">
    <div class="header-section">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div class="section-title-container">
                <h3 class="section-title">
                    <i class="fas fa-users mr-2"></i> Daftar Pelanggan
                </h3>
                <p class="section-subtitle">Kelola data pelanggan Warung Aldo</p>
            </div>
            <div class="header-actions">
                <div class="customer-stats">
                    <div class="stat-item">
                        <div class="stat-value"><?php echo $jumlahdata; ?></div>
                        <div class="stat-label">Total Pelanggan</div>
                    </div>
                    <div class="stat-item">
                        <?php 
                            $sqlActive = "SELECT COUNT(*) as count FROM tblpelanggan WHERE aktif=1";
                            $activeCount = $db->getITEM($sqlActive)['count'];
                        ?>
                        <div class="stat-value"><?php echo $activeCount; ?></div>
                        <div class="stat-label">Pelanggan Aktif</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card table-card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table customer-table">
                    <thead class="thead-primary">
                        <tr>
                            <th width="5%">No</th>
                            <th width="15%">Pelanggan</th>
                            <th width="20%">Alamat</th>
                            <th width="15%">Telp</th>
                            <th width="20%">Email</th>
                            <th width="10%">Status</th>
                            <th width="15%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(!empty($row)) { ?>
                            <?php foreach ($row as $r): ?>
                                <?php 
                                    if ($r['aktif'] == 1) {
                                        $statusClass = 'active';
                                        $status = '<span class="status-badge active">
                                                    <i class="fas fa-check-circle mr-1"></i> AKTIF
                                                   </span>';
                                    } else {
                                        $statusClass = 'inactive';
                                        $status = '<span class="status-badge inactive">
                                                    <i class="fas fa-times-circle mr-1"></i> TIDAK AKTIF
                                                   </span>';
                                    }
                                ?> 
                                <tr class="<?php echo $statusClass; ?>-row">
                                    <td><?php echo $no++ ?></td>
                                    <td>
                                        <div class="customer-info">
                                            <i class="fas fa-user-circle customer-icon"></i>
                                            <span class="customer-name"><?php echo $r['pelanggan'] ?></span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="address-info">
                                            <i class="fas fa-map-marker-alt address-icon"></i>
                                            <span class="address-text"><?php echo $r['alamat'] ?></span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="contact-info">
                                            <i class="fas fa-phone contact-icon"></i>
                                            <span class="contact-text"><?php echo $r['telp'] ?></span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="email-info">
                                            <i class="fas fa-envelope email-icon"></i>
                                            <span class="email-text"><?php echo $r['email'] ?></span>
                                        </div>
                                    </td>
                                    <td>
                                        <a href="?f=pelanggan&m=update&id=<?php echo $r['idpelanggan'] ?>" class="status-link">
                                            <?php echo $status ?>
                                        </a>
                                    </td>
                                    <td>
                                        <div class="action-buttons">
                                            <a href="?f=pelanggan&m=delete&id=<?php echo $r['idpelanggan'] ?>" class="btn btn-sm btn-outline-danger action-btn delete-btn" onclick="return confirm('Apakah Anda yakin ingin menghapus pelanggan ini?')">
                                                <i class="fas fa-trash-alt"></i>
                                                <span class="action-text">Delete</span>
                                            </a>
                                            <a href="#" class="btn btn-sm btn-outline-primary action-btn view-btn" data-customer="<?php echo $r['pelanggan'] ?>">
                                                <i class="fas fa-eye"></i>
                                                <span class="action-text">Detail</span>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach ?>
                        <?php } else { ?>
                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    <div class="empty-state">
                                        <i class="fas fa-users fa-3x mb-3 text-muted"></i>
                                        <p>Tidak ada data pelanggan yang ditemukan</p>
                                    </div>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="pagination-container mt-4">
        <nav aria-label="Page navigation">
            <ul class="pagination justify-content-center">
                <?php for ($i=1; $i <= $halaman; $i++) { ?>
                    <li class="page-item <?php echo (isset($_GET['p']) && $_GET['p'] == $i) ? 'active' : ''; ?>">
                        <a class="page-link" href="?f=pelanggan&m=select&p=<?php echo $i; ?>">
                            <?php echo $i; ?>
                        </a>
                    </li>
                <?php } ?>
            </ul>
        </nav>
    </div>
</div>

<style>
    .customer-list-container {
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
    
    .customer-stats {
        display: flex;
        gap: 20px;
    }
    
    .stat-item {
        text-align: center;
        background-color: white;
        padding: 10px 20px;
        border-radius: 8px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        min-width: 100px;
    }
    
    .stat-value {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--secondary);
    }
    
    .stat-label {
        font-size: 0.8rem;
        color: #6c757d;
    }
    
    .table-card {
        border: none;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    }
    
    .customer-table {
        margin-bottom: 0;
    }
    
    .thead-primary {
        background-color: var(--secondary);
        color: white;
    }
    
    .customer-table th {
        font-weight: 500;
        border: none;
        padding: 15px;
    }
    
    .customer-table td {
        padding: 15px;
        vertical-align: middle;
        border-top: 1px solid #f0f0f0;
    }
    
    .customer-info, .address-info, .contact-info, .email-info {
        display: flex;
        align-items: center;
    }
    
    .customer-icon, .address-icon, .contact-icon, .email-icon {
        font-size: 1rem;
        margin-right: 10px;
        width: 20px;
        text-align: center;
    }
    
    .customer-icon {
        color: var(--secondary);
    }
    
    .address-icon {
        color: #e91e63;
    }
    
    .contact-icon {
        color: #4caf50;
    }
    
    .email-icon {
        color: #2196f3;
    }
    
    .customer-name {
        font-weight: 500;
    }
    
    .address-text, .contact-text, .email-text {
        font-size: 0.9rem;
        color: #555;
        max-width: 200px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    
    .address-text:hover, .contact-text:hover, .email-text:hover {
        white-space: normal;
        overflow: visible;
    }
    
    .status-badge {
        padding: 5px 10px;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 500;
        display: inline-block;
        transition: all 0.3s ease;
    }
    
    .status-badge.active {
        background-color: #d4edda;
        color: #155724;
    }
    
    .status-badge.inactive {
        background-color: #f8d7da;
        color: #721c24;
    }
    
    .status-link:hover .status-badge {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    
    .action-buttons {
        display: flex;
        gap: 5px;
    }
    
    .action-btn {
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 5px 10px;
        transition: all 0.3s ease;
    }
    
    .action-text {
        display: none;
        margin-left: 5px;
    }
    
    .action-btn:hover .action-text {
        display: inline;
    }
    
    .delete-btn {
        color: #dc3545;
        border-color: #dc3545;
    }
    
    .delete-btn:hover {
        background-color: #dc3545;
        border-color: #dc3545;
        color: white;
    }
    
    .view-btn {
        color: var(--primary);
        border-color: var(--primary);
    }
    
    .view-btn:hover {
        background-color: var(--primary);
        border-color: var(--primary);
        color: white;
    }
    
    .active-row {
        background-color: #f8fff8;
    }
    
    .inactive-row {
        background-color: #fff8f8;
    }
    
    .pagination-container {
        margin-top: 30px;
    }
    
    .pagination .page-link {
        color: var(--secondary);
        border: none;
        margin: 0 3px;
        border-radius: 5px;
        transition: all 0.3s ease;
    }
    
    .pagination .page-item.active .page-link {
        background-color: var(--primary);
        border-color: var(--primary);
        color: white;
    }
    
    .pagination .page-link:hover {
        background-color: #f0f0f0;
        color: var(--primary);
    }
    
    .empty-state {
        padding: 30px;
        text-align: center;
        color: #6c757d;
    }
    
    @media (max-width: 768px) {
        .customer-table {
            min-width: 900px;
        }
        
        .customer-stats {
            flex-direction: column;
            gap: 10px;
        }
        
        .action-buttons {
            flex-direction: column;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // View customer details (placeholder functionality)
        const viewButtons = document.querySelectorAll('.view-btn');
        viewButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                const customerName = this.getAttribute('data-customer');
                alert(`Detail pelanggan untuk ${customerName} akan ditampilkan di sini.`);
            });
        });
    });
</script>
