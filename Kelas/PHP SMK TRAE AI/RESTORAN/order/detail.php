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

    // Get order details
    $sql = "SELECT * FROM vorderdetail WHERE idorder = $id ORDER BY idorderdetail ASC";
    $row = $db->getALL($sql);

    // Calculate total
    $sqlTotal = "SELECT SUM(harga * jumlah) as total FROM vorderdetail WHERE idorder = $id";
    $total = $db->getITEM($sqlTotal)['total'];
?>

<div class="order-detail-container">
    <div class="section-header">
        <h3 class="section-title">
            <i class="fas fa-file-invoice mr-2"></i>
            Detail Pesanan #<?php echo $id; ?>
        </h3>
        <p class="section-subtitle">Informasi lengkap tentang pesanan</p>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-info-circle mr-2"></i>
                        Informasi Pesanan
                    </h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <td width="40%"><strong>ID Pesanan</strong></td>
                            <td>: <?php echo $order['idorder']; ?></td>
                        </tr>
                        <tr>
                            <td><strong>Tanggal Pesanan</strong></td>
                            <td>: <?php echo date('d F Y', strtotime($order['tglorder'])); ?></td>
                        </tr>
                        <tr>
                            <td><strong>Total Pembayaran</strong></td>
                            <td>: Rp <?php echo number_format($order['total'], 0, ',', '.'); ?></td>
                        </tr>
                        <tr>
                            <td><strong>Status Pesanan</strong></td>
                            <td>: 
                                <?php if($order['status'] == 0): ?>
                                    <span class="badge badge-warning">Belum Dibayar</span>
                                <?php elseif($order['status'] == 1): ?>
                                    <span class="badge badge-success">Sudah Dibayar</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-user mr-2"></i>
                        Informasi Pelanggan
                    </h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <td width="40%"><strong>Nama</strong></td>
                            <td>: <?php echo $pelanggan['pelanggan']; ?></td>
                        </tr>
                        <tr>
                            <td><strong>Email</strong></td>
                            <td>: <?php echo $pelanggan['email']; ?></td>
                        </tr>
                        <tr>
                            <td><strong>Telepon</strong></td>
                            <td>: <?php echo $pelanggan['telp']; ?></td>
                        </tr>
                        <tr>
                            <td><strong>Alamat</strong></td>
                            <td>: <?php echo $pelanggan['alamat']; ?></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0">
                <i class="fas fa-shopping-basket mr-2"></i>
                Item Pesanan
            </h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="thead-light">
                        <tr>
                            <th width="5%">No</th>
                            <th width="40%">Menu</th>
                            <th width="15%">Harga</th>
                            <th width="10%">Jumlah</th>
                            <th width="15%">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(!empty($row)): ?>
                            <?php $no = 1; ?>
                            <?php foreach($row as $r): ?>
                                <tr>
                                    <td><?php echo $no++; ?></td>
                                    <td><?php echo $r['menu']; ?></td>
                                    <td>Rp <?php echo number_format($r['harga'], 0, ',', '.'); ?></td>
                                    <td class="text-center">
                                        <span class="badge badge-primary"><?php echo $r['jumlah']; ?></span>
                                    </td>
                                    <td>Rp <?php echo number_format($r['harga'] * $r['jumlah'], 0, ',', '.'); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center py-4">
                                    <div class="empty-state">
                                        <i class="fas fa-shopping-cart fa-3x mb-3 text-muted"></i>
                                        <p>Tidak ada item dalam pesanan ini</p>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                    <tfoot class="bg-light">
                        <tr>
                            <td colspan="4" class="text-right font-weight-bold">Grand Total:</td>
                            <td class="font-weight-bold">Rp <?php echo number_format($total, 0, ',', '.'); ?></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <div class="action-buttons">
        <?php if($order['status'] == 0): ?>
            <a href="?f=order&m=bayar&id=<?php echo $id; ?>" class="btn btn-success mr-2">
                <i class="fas fa-check-circle mr-1"></i> Konfirmasi Pembayaran
            </a>
        <?php endif; ?>
        <a href="?f=order&m=select" class="btn btn-secondary">
            <i class="fas fa-arrow-left mr-1"></i> Kembali ke Daftar Pesanan
        </a>
    </div>
</div>

<style>
    .order-detail-container {
        padding: 15px;
    }
    
    .section-header {
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
    
    .card {
        border: none;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    }
    
    .card-header {
        background-color: rgba(0,0,0,0.02);
        border-bottom: 1px solid rgba(0,0,0,0.05);
    }
    
    .card-title {
        color: var(--secondary);
        font-weight: 600;
    }
    
    .action-buttons {
        margin-top: 20px;
    }
    
    .empty-state {
        padding: 20px;
        color: #6c757d;
    }
</style>