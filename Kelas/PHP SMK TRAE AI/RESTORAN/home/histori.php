<?php 
    $email = $_SESSION['pelanggan'];
    
    // Get customer information
    $sqlPelanggan = "SELECT * FROM tblpelanggan WHERE email = '$email'";
    $pelanggan = $db->getITEM($sqlPelanggan);
    
    // Get total spent
    $sqlTotal = "SELECT SUM(total) as total_spent FROM tblorder WHERE idpelanggan = '".$pelanggan['idpelanggan']."'";
    $totalSpent = $db->getITEM($sqlTotal)['total_spent'];
    
    // Get order count
    $jumlahdata = $db->rowCOUNT("SELECT idorder FROM vorder WHERE email = '$email' ");
    $banyak = 4; // Increased items per page
    
    $halaman = ceil($jumlahdata / $banyak);
    
    if (isset($_GET['p'])) {
        $p = $_GET['p'];
        $mulai = ($p * $banyak) - $banyak;
    } else {
        $mulai = 0;
    }
    
    $sql = "SELECT * FROM vorder WHERE email = '$email' ORDER BY tglorder DESC LIMIT $mulai,$banyak";
    $row = $db->getALL($sql);
    
    $no = 1 + $mulai;
?>

<div class="history-container">
    <div class="header-section">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div class="section-title-container">
                <h3 class="section-title">
                    <i class="fas fa-history mr-2"></i> Histori Pembelian
                </h3>
                <p class="section-subtitle">Riwayat pesanan Anda di Warung Aldo</p>
            </div>
            <div class="header-actions">
                <a href="?f=home&m=produk" class="btn btn-outline-primary shop-btn">
                    <i class="fas fa-utensils mr-2"></i> Pesan Lagi
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="card customer-card mb-4">
                <div class="card-body text-center">
                    <div class="customer-avatar">
                        <i class="fas fa-user"></i>
                    </div>
                    <h5 class="customer-name mt-3"><?php echo $pelanggan['pelanggan']; ?></h5>
                    <p class="customer-email"><?php echo $email; ?></p>
                    <div class="customer-stats">
                        <div class="stat-item">
                            <div class="stat-value"><?php echo $jumlahdata; ?></div>
                            <div class="stat-label">Pesanan</div>
                        </div>
                        <div class="stat-divider"></div>
                        <div class="stat-item">
                            <div class="stat-value">Rp <?php echo number_format($totalSpent, 0, ',', '.'); ?></div>
                            <div class="stat-label">Total Belanja</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card help-card">
                <div class="card-body">
                    <h5 class="help-title">
                        <i class="fas fa-question-circle mr-2"></i> Butuh Bantuan?
                    </h5>
                    <p class="help-text">Jika Anda memiliki pertanyaan tentang pesanan Anda, silakan hubungi kami.</p>
                    <div class="help-contacts">
                        <div class="help-contact-item">
                            <i class="fas fa-phone-alt"></i>
                            <span>021-1234567</span>
                        </div>
                        <div class="help-contact-item">
                            <i class="fas fa-envelope"></i>
                            <span>info@warungaldo.com</span>
                        </div>
                        <div class="help-contact-item">
                            <i class="fas fa-comment"></i>
                            <span>Live Chat</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-8">
            <?php if(empty($row)): ?>
                <div class="card empty-history-card">
                    <div class="card-body text-center">
                        <div class="empty-history-icon">
                            <i class="fas fa-shopping-bag fa-4x"></i>
                        </div>
                        <h4 class="mt-3">Belum Ada Riwayat Pembelian</h4>
                        <p class="text-muted">Anda belum melakukan pembelian apapun di Warung Aldo.</p>
                        <a href="?f=home&m=produk" class="btn btn-primary mt-3">
                            <i class="fas fa-utensils mr-2"></i> Mulai Belanja
                        </a>
                    </div>
                </div>
            <?php else: ?>
                <div class="card history-card">
                    <div class="card-header history-card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-receipt mr-2"></i> Daftar Pesanan
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table history-table mb-0">
                                <thead class="thead-primary">
                                    <tr>
                                        <th width="10%">No</th>
                                        <th width="25%">Tanggal</th>
                                        <th width="25%">Total</th>
                                        <th width="20%">Status</th>
                                        <th width="20%">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($row as $r): ?>
                                    <tr>
                                        <td><?php echo $no++ ?></td>
                                        <td>
                                            <div class="order-date">
                                                <div class="date-icon">
                                                    <i class="fas fa-calendar-day"></i>
                                                </div>
                                                <div class="date-info">
                                                    <div class="date-primary"><?php echo date('d M Y', strtotime($r['tglorder'])); ?></div>
                                                    <div class="date-secondary"><?php echo date('H:i', strtotime($r['tglorder'])); ?> WIB</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="order-total">
                                                <span class="total-badge">
                                                    Rp <?php echo number_format($r['total'], 0, ',', '.'); ?>
                                                </span>
                                            </div>
                                        </td>
                                        <td>
                                            <?php if($r['status'] == 1): ?>
                                                <span class="status-badge paid">
                                                    <i class="fas fa-check-circle mr-1"></i> LUNAS
                                                </span>
                                            <?php else: ?>
                                                <span class="status-badge pending">
                                                    <i class="fas fa-clock mr-1"></i> BELUM BAYAR
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="action-buttons">
                                                <a href="?f=home&m=detail&id=<?php echo $r['idorder'] ?>" class="btn btn-sm btn-outline-primary detail-btn">
                                                    <i class="fas fa-eye mr-1"></i> Detail
                                                </a>
                                                <?php if($r['status'] == 0): ?>
                                                <a href="?f=home&m=beli&id=<?php echo $r['idorder'] ?>" class="btn btn-sm btn-outline-success pay-btn">
                                                    <i class="fas fa-credit-card mr-1"></i> Bayar
                                                </a>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="pagination-container">
                            <nav aria-label="Page navigation">
                                <ul class="pagination justify-content-center mb-0">
                                    <?php for ($i=1; $i <= $halaman; $i++): ?>
                                        <li class="page-item <?php echo (isset($_GET['p']) && $_GET['p'] == $i) ? 'active' : ''; ?>">
                                            <a class="page-link" href="?f=home&m=histori&p=<?php echo $i; ?>">
                                                <?php echo $i; ?>
                                            </a>
                                        </li>
                                    <?php endfor; ?>
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
    .history-container {
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
    
    .shop-btn {
        border-color: var(--primary);
        color: var(--primary);
        transition: all 0.3s ease;
    }
    
    .shop-btn:hover {
        background-color: var(--primary);
        color: white;
    }
    
    .customer-card, .help-card, .history-card, .empty-history-card {
        border: none;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        background-color: white;
        margin-bottom: 20px;
    }
    
    .customer-avatar {
        width: 80px;
        height: 80px;
        background-color: var(--secondary);
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 15px;
    }
    
    .customer-avatar i {
        font-size: 2rem;
    }
    
    .customer-name {
        color: var(--secondary);
        font-weight: 600;
        margin-bottom: 5px;
    }
    
    .customer-email {
        color: #6c757d;
        font-size: 0.9rem;
        margin-bottom: 20px;
    }
    
    .customer-stats {
        display: flex;
        justify-content: center;
        align-items: center;
        padding-top: 15px;
        border-top: 1px solid #f0f0f0;
    }
    
    .stat-item {
        text-align: center;
        padding: 0 20px;
    }
    
    .stat-divider {
        width: 1px;
        height: 40px;
        background-color: #f0f0f0;
    }
    
    .stat-value {
        font-size: 1.2rem;
        font-weight: 700;
        color: var(--primary);
        margin-bottom: 5px;
    }
    
    .stat-label {
        font-size: 0.8rem;
        color: #6c757d;
    }
    
    .help-title {
        color: var(--secondary);
        font-weight: 600;
        margin-bottom: 15px;
    }
    
    .help-text {
        color: #6c757d;
        font-size: 0.9rem;
        margin-bottom: 20px;
    }
    
    .help-contacts {
        border-top: 1px solid #f0f0f0;
        padding-top: 15px;
    }
    
    .help-contact-item {
        display: flex;
        align-items: center;
        margin-bottom: 10px;
    }
    
    .help-contact-item i {
        width: 30px;
        height: 30px;
        background-color: #f0f0f0;
        color: var(--secondary);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 10px;
    }
    
    .empty-history-card {
        padding: 40px 20px;
    }
    
    .empty-history-icon {
        color: #e0e0e0;
        margin-bottom: 20px;
    }
    
    .history-card-header {
        background-color: var(--secondary);
        color: white;
        padding: 15px 20px;
    }
    
    .history-table {
        margin-bottom: 0;
    }
    
    .thead-primary {
        background-color: var(--secondary);
        color: white;
    }
    
    .history-table th {
        font-weight: 500;
        border: none;
        padding: 15px;
    }
    
    .history-table td {
        padding: 15px;
        vertical-align: middle;
        border-top: 1px solid #f0f0f0;
    }
    
    .order-date {
        display: flex;
        align-items: center;
    }
    
    .date-icon {
        width: 40px;
        height: 40px;
        background-color: #f0f0f0;
        color: var(--secondary);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 10px;
    }
    
    .date-primary {
        font-weight: 500;
        color: var(--secondary);
    }
    
    .date-secondary {
        font-size: 0.8rem;
        color: #6c757d;
    }
    
    .total-badge {
        background-color: #e3f2fd;
        color: #0d47a1;
        padding: 5px 10px;
        border-radius: 20px;
        font-size: 0.9rem;
        font-weight: 500;
        display: inline-block;
    }
    
    .status-badge {
        padding: 5px 10px;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 500;
        display: inline-block;
    }
    
    .status-badge.paid {
        background-color: #d4edda;
        color: #155724;
    }
    
    .status-badge.pending {
        background-color: #fff3cd;
        color: #856404;
    }
    
    .action-buttons {
        display: flex;
        gap: 5px;
    }
    
    .detail-btn, .pay-btn {
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 5px 10px;
        transition: all 0.3s ease;
    }
    
    .detail-btn {
        color: var(--primary);
        border-color: var(--primary);
    }
    
    .detail-btn:hover {
        background-color: var(--primary);
        border-color: var(--primary);
        color: white;
    }
    
    .pay-btn {
        color: #28a745;
        border-color: #28a745;
    }
    
    .pay-btn:hover {
        background-color: #28a745;
        border-color: #28a745;
        color: white;
    }
    
    .card-footer {
        background-color: #f8f9fa;
        border-top: 1px solid #f0f0f0;
        padding: 15px 20px;
    }
    
    .pagination-container {
        padding: 10px 0;
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
    
    @media (max-width: 768px) {
        .history-table {
            min-width: 800px;
        }
    }
</style>
