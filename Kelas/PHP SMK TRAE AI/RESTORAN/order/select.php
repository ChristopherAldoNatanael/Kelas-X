<?php 
    $jumlahdata = $db->rowCOUNT("SELECT idorder FROM vorder ");
    $banyak = 2;

    $halaman = ceil($jumlahdata / $banyak);

    if (isset($_GET['p'])) {
        $p = $_GET['p'];
        $mulai = ($p * $banyak) - $banyak;
    } else {
        $mulai = 0;
    }

    $sql = "SELECT * FROM vorder ORDER BY status,idorder ASC LIMIT $mulai,$banyak ";
    $row = $db->getALL($sql);

    $no = 1 + $mulai;
?>

<div class="order-list-container">
    <div class="header-section">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div class="section-title-container">
                <h3 class="section-title">
                    <i class="fas fa-shopping-bag mr-2"></i> Order Pembelian
                </h3>
                <p class="section-subtitle">Kelola dan proses pembayaran order pelanggan</p>
            </div>
            <div class="header-actions">
                <div class="order-stats">
                    <div class="stat-item">
                        <div class="stat-value"><?php echo $jumlahdata; ?></div>
                        <div class="stat-label">Total Order</div>
                    </div>
                    <div class="stat-item">
                        <?php 
                            $sqlPending = "SELECT COUNT(*) as count FROM vorder WHERE status=0";
                            $pendingCount = $db->getITEM($sqlPending)['count'];
                        ?>
                        <div class="stat-value"><?php echo $pendingCount; ?></div>
                        <div class="stat-label">Belum Bayar</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card table-card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table order-table">
                    <thead class="thead-primary">
                        <tr>
                            <th width="5%">No</th>
                            <th width="20%">Pelanggan</th>
                            <th width="15%">Tanggal</th>
                            <th width="15%">Total</th>
                            <th width="15%">Bayar</th>
                            <th width="15%">Kembali</th>
                            <th width="15%">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(!empty($row)) { ?>
                            <?php foreach ($row as $r): ?>
                                <?php 
                                    if ($r['status'] == 0) {
                                        $statusClass = 'pending';
                                        $status = '<a href="?f=order&m=bayar&id='.$r['idorder'].'" class="btn btn-sm btn-primary payment-btn">
                                                    <i class="fas fa-cash-register mr-1"></i> Bayar
                                                   </a>';
                                    } else {
                                        $statusClass = 'paid';
                                        $status = '<span class="status-badge paid">
                                                    <i class="fas fa-check-circle mr-1"></i> LUNAS
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
                                        <span class="date-badge">
                                            <i class="fas fa-calendar-day mr-1"></i>
                                            <?php echo date('d M Y', strtotime($r['tglorder'])) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="amount-badge total">
                                            Rp <?php echo number_format($r['total'], 0, ',', '.') ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if($r['bayar'] > 0): ?>
                                            <span class="amount-badge payment">
                                                Rp <?php echo number_format($r['bayar'], 0, ',', '.') ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="amount-badge pending">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if($r['kembali'] > 0): ?>
                                            <span class="amount-badge change">
                                                Rp <?php echo number_format($r['kembali'], 0, ',', '.') ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="amount-badge pending">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo $status ?></td>
                                </tr>
                            <?php endforeach ?>
                        <?php } else { ?>
                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    <div class="empty-state">
                                        <i class="fas fa-shopping-cart fa-3x mb-3 text-muted"></i>
                                        <p>Tidak ada order yang ditemukan</p>
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
                        <a class="page-link" href="?f=order&m=select&p=<?php echo $i; ?>">
                            <?php echo $i; ?>
                        </a>
                    </li>
                <?php } ?>
            </ul>
        </nav>
    </div>
</div>

<style>
    .order-list-container {
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
    
    .order-stats {
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
    
    .order-table {
        margin-bottom: 0;
    }
    
    .thead-primary {
        background-color: var(--secondary);
        color: white;
    }
    
    .order-table th {
        font-weight: 500;
        border: none;
        padding: 15px;
    }
    
    .order-table td {
        padding: 15px;
        vertical-align: middle;
        border-top: 1px solid #f0f0f0;
    }
    
    .customer-info {
        display: flex;
        align-items: center;
    }
    
    .customer-icon {
        font-size: 1.5rem;
        color: var(--secondary);
        margin-right: 10px;
    }
    
    .customer-name {
        font-weight: 500;
    }
    
    .date-badge {
        background-color: #f0f0f0;
        padding: 5px 10px;
        border-radius: 20px;
        font-size: 0.85rem;
        color: #555;
        display: inline-block;
    }
    
    .amount-badge {
        padding: 5px 10px;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 500;
        display: inline-block;
    }
    
    .amount-badge.total {
        background-color: #e3f2fd;
        color: #0d47a1;
    }
    
    .amount-badge.payment {
        background-color: #e8f5e9;
        color: #2e7d32;
    }
    
    .amount-badge.change {
        background-color: #fff8e1;
        color: #ff8f00;
    }
    
    .amount-badge.pending {
        background-color: #f5f5f5;
        color: #757575;
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
    
    .payment-btn {
        background-color: var(--primary);
        border-color: var(--primary);
        transition: all 0.3s ease;
    }
    
    .payment-btn:hover {
        background-color: #e55a2a;
        border-color: #e55a2a;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(255, 107, 53, 0.3);
    }
    
    .pending-row {
        background-color: #fff8f8;
    }
    
    .paid-row {
        background-color: #f8fff8;
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
        .order-table {
            min-width: 800px;
        }
        
        .order-stats {
            flex-direction: column;
            gap: 10px;
        }
    }
</style>
