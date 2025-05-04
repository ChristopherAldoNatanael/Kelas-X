<?php 
    if (isset($_GET['id'])) {
        $id = $_GET['id'];
    }

    // Get order information
    $sqlOrder = "SELECT * FROM tblorder WHERE idorder = $id";
    $order = $db->getITEM($sqlOrder);

    // Get customer information
    $idpelanggan = $order['idpelanggan'];
    $sqlPelanggan = "SELECT * FROM tblpelanggan WHERE idpelanggan = $idpelanggan";
    $pelanggan = $db->getITEM($sqlPelanggan);

    $jumlahdata = $db->rowCOUNT("SELECT idorderdetail FROM vorderdetail WHERE idorder = $id ");
    $banyak = 5; // Increased items per page

    $halaman = ceil($jumlahdata / $banyak);

    if (isset($_GET['p'])) {
        $p = $_GET['p'];
        $mulai = ($p * $banyak) - $banyak;
    } else {
        $mulai = 0;
    }

    $sql = "SELECT * FROM vorderdetail WHERE idorder = $id ORDER BY idorderdetail ASC LIMIT $mulai,$banyak";
    $row = $db->getALL($sql);

    // Calculate total
    $sqlTotal = "SELECT SUM(harga * jumlah) as total FROM vorderdetail WHERE idorder = $id";
    $total = $db->getITEM($sqlTotal)['total'];

    $no = 1 + $mulai;
?>

<div class="order-detail-container">
    <div class="header-section">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div class="section-title-container">
                <h3 class="section-title">
                    <i class="fas fa-receipt mr-2"></i> Detail Pembelian
                </h3>
                <p class="section-subtitle">Informasi detail untuk Order #<?php echo $id; ?></p>
            </div>
            <div class="header-actions">
                <a href="?f=home&m=histori" class="btn btn-outline-primary back-btn">
                    <i class="fas fa-arrow-left mr-2"></i> Kembali ke Histori
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="card info-card mb-4">
                <div class="card-header info-card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-info-circle mr-2"></i> Informasi Order
                    </h5>
                </div>
                <div class="card-body">
                    <div class="info-item">
                        <span class="info-label">Order ID</span>
                        <span class="info-value">#<?php echo $id; ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Tanggal Order</span>
                        <span class="info-value"><?php echo date('d M Y', strtotime($order['tglorder'])); ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Status</span>
                        <span class="info-value">
                            <?php if($order['status'] == 1): ?>
                                <span class="status-badge paid">
                                    <i class="fas fa-check-circle mr-1"></i> LUNAS
                                </span>
                            <?php else: ?>
                                <span class="status-badge pending">
                                    <i class="fas fa-clock mr-1"></i> BELUM BAYAR
                                </span>
                            <?php endif; ?>
                        </span>
                    </div>
                    <?php if($order['status'] == 1): ?>
                    <div class="info-item">
                        <span class="info-label">Pembayaran</span>
                        <span class="info-value">Rp <?php echo number_format($order['bayar'], 0, ',', '.'); ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Kembalian</span>
                        <span class="info-value">Rp <?php echo number_format($order['kembali'], 0, ',', '.'); ?></span>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="card info-card">
                <div class="card-header info-card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-user mr-2"></i> Informasi Pelanggan
                    </h5>
                </div>
                <div class="card-body">
                    <div class="info-item">
                        <span class="info-label">Nama</span>
                        <span class="info-value"><?php echo $pelanggan['pelanggan']; ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Alamat</span>
                        <span class="info-value"><?php echo $pelanggan['alamat']; ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Telepon</span>
                        <span class="info-value"><?php echo $pelanggan['telp']; ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Email</span>
                        <span class="info-value"><?php echo $pelanggan['email']; ?></span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card table-card">
                <div class="card-header table-card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-list mr-2"></i> Item Pesanan
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table order-detail-table mb-0">
                            <thead class="thead-primary">
                                <tr>
                                    <th width="5%">No</th>
                                    <th width="15%">Tanggal</th>
                                    <th width="40%">Menu</th>
                                    <th width="20%">Harga</th>
                                    <th width="10%">Jumlah</th>
                                    <th width="10%">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(!empty($row)): ?>
                                    <?php foreach ($row as $r): ?>
                                    <tr>
                                        <td><?php echo $no++ ?></td>
                                        <td>
                                            <span class="date-badge">
                                                <?php echo date('d M Y', strtotime($r['tglorder'])); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="menu-info">
                                                <span class="menu-name"><?php echo $r['menu']; ?></span>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="price-badge">
                                                Rp <?php echo number_format($r['harga'], 0, ',', '.'); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="quantity-badge">
                                                <?php echo $r['jumlah']; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="subtotal-value">
                                                Rp <?php echo number_format($r['harga'] * $r['jumlah'], 0, ',', '.'); ?>
                                            </span>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" class="text-center py-4">
                                            <div class="empty-state">
                                                <i class="fas fa-receipt fa-3x mb-3 text-muted"></i>
                                                <p>Tidak ada detail pesanan yang ditemukan</p>
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
                                <?php for ($i=1; $i <= $halaman; $i++): ?>
                                    <li class="page-item <?php echo (isset($_GET['p']) && $_GET['p'] == $i) ? 'active' : ''; ?>">
                                        <a class="page-link" href="?f=home&m=detail&id=<?php echo $id; ?>&p=<?php echo $i; ?>">
                                            <?php echo $i; ?>
                                        </a>
                                    </li>
                                <?php endfor; ?>
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>

            <div class="card summary-card mt-4">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="summary-notes">
                                <h6><i class="fas fa-sticky-note mr-2"></i> Catatan</h6>
                                <p class="mb-0">Terima kasih telah berbelanja di Warung Aldo. Kami harap Anda puas dengan pesanan Anda.</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="order-summary">
                                <div class="summary-item">
                                    <span class="summary-label">Subtotal</span>
                                    <span class="summary-value">Rp <?php echo number_format($total, 0, ',', '.'); ?></span>
                                </div>
                                <div class="summary-item">
                                    <span class="summary-label">Biaya Pengiriman</span>
                                    <span class="summary-value">Gratis</span>
                                </div>
                                <div class="summary-item">
                                    <span class="summary-label">Pajak</span>
                                    <span class="summary-value">Rp 0</span>
                                </div>
                                <div class="summary-divider"></div>
                                <div class="summary-item grand-total">
                                    <span class="summary-label">Total</span>
                                    <span class="summary-value">Rp <?php echo number_format($total, 0, ',', '.'); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer text-center">
                    <button class="btn btn-outline-primary print-btn" onclick="window.print()">
                        <i class="fas fa-print mr-2"></i> Cetak Invoice
                    </button>
                    <?php if($order['status'] == 0): ?>
                    <a href="?f=home&m=beli&id=<?php echo $id; ?>" class="btn btn-primary reorder-btn">
                        <i class="fas fa-shopping-cart mr-2"></i> Bayar Sekarang
                    </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .order-detail-container {
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
    
    .back-btn {
        border-color: var(--primary);
        color: var(--primary);
        transition: all 0.3s ease;
    }
    
    .back-btn:hover {
        background-color: var(--primary);
        color: white;
    }
    
    .info-card, .table-card, .summary-card {
        border: none;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        background-color: white;
        margin-bottom: 20px;
    }
    
    .info-card-header, .table-card-header {
        background-color: var(--secondary);
        color: white;
        padding: 15px 20px;
    }
    
    .info-item {
        display: flex;
        justify-content: space-between;
        margin-bottom: 15px;
    }
    
    .info-item:last-child {
        margin-bottom: 0;
    }
    
    .info-label {
        color: #6c757d;
        font-size: 0.9rem;
    }
    
    .info-value {
        font-weight: 500;
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
    
    .order-detail-table {
        margin-bottom: 0;
    }
    
    .thead-primary {
        background-color: var(--secondary);
        color: white;
    }
    
    .order-detail-table th {
        font-weight: 500;
        border: none;
        padding: 15px;
    }
    
    .order-detail-table td {
        padding: 15px;
        vertical-align: middle;
        border-top: 1px solid #f0f0f0;
    }
    
    .date-badge {
        background-color: #f0f0f0;
        padding: 5px 10px;
        border-radius: 20px;
        font-size: 0.85rem;
        color: #555;
        display: inline-block;
    }
    
    .menu-info {
        display: flex;
        flex-direction: column;
    }
    
    .menu-name {
        font-weight: 500;
        color: var(--secondary);
    }
    
    .price-badge {
        background-color: #e3f2fd;
        color: #0d47a1;
        padding: 5px 10px;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 500;
        display: inline-block;
    }
    
    .quantity-badge {
        background-color: var(--accent);
        color: white;
        width: 30px;
        height: 30px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        font-weight: 500;
    }
    
    .subtotal-value {
        font-weight: 600;
        color: var(--primary);
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
    
    .summary-notes {
        padding: 10px;
    }
    
    .summary-notes h6 {
        color: var(--secondary);
        margin-bottom: 10px;
    }
    
    .summary-notes p {
        color: #6c757d;
        font-size: 0.9rem;
    }
    
    .order-summary {
        padding: 10px;
    }
    
    .summary-item {
        display: flex;
        justify-content: space-between;
        margin-bottom: 10px;
    }
    
    .summary-label {
        color: #6c757d;
    }
    
    .summary-value {
        font-weight: 500;
    }
    
    .summary-divider {
        border-top: 1px dashed #e0e0e0;
        margin: 10px 0;
    }
    
    .grand-total .summary-label {
        font-weight: 600;
        color: var(--secondary);
        font-size: 1.1rem;
    }
    
    .grand-total .summary-value {
        font-weight: 700;
        color: var(--primary);
        font-size: 1.2rem;
    }
    
    .card-footer {
        background-color: #f8f9fa;
        border-top: 1px solid #f0f0f0;
        padding: 15px 20px;
    }
    
    .print-btn, .reorder-btn {
        margin: 0 5px;
        transition: all 0.3s ease;
    }
    
    .print-btn:hover, .reorder-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    
    .empty-state {
        padding: 30px;
        text-align: center;
        color: #6c757d;
    }
    
    @media (max-width: 768px) {
        .order-detail-table {
            min-width: 800px;
        }
        
        .summary-card .row {
            flex-direction: column;
        }
        
        .summary-notes {
            margin-bottom: 20px;
        }
    }
    
    @media print {
        .header-actions, .card-footer, .pagination-container {
            display: none;
        }
        
        .card {
            box-shadow: none;
            border: 1px solid #ddd;
        }
        
        .info-card-header, .table-card-header {
            background-color: #f0f0f0 !important;
            color: #333 !important;
        }
        
        .thead-primary {
            background-color: #f0f0f0 !important;
            color: #333 !important;
        }
    }
</style>
