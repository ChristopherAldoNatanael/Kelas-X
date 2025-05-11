<div class="order-details-container">
    <div class="section-header">
        <h3 class="section-title">
            <i class="fas fa-file-invoice mr-2"></i>
            Detail Pembelian
        </h3>
        <p class="section-subtitle">Lihat dan filter riwayat pembelian berdasarkan tanggal</p>
    </div>

    <div class="card filter-card">
        <div class="card-body">
            <form action="" method="post" class="date-filter-form">
                <div class="row">
                    <div class="col-md-5">
                        <div class="form-group">
                            <label for="tawal">
                                <i class="fas fa-calendar-alt mr-1"></i> Tanggal Awal
                            </label>
                            <input type="date" name="tawal" required class="form-control">
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="form-group">
                            <label for="takhir">
                                <i class="fas fa-calendar-alt mr-1"></i> Tanggal Akhir
                            </label>
                            <input type="date" name="takhir" required class="form-control">
                        </div>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" name="simpan" class="btn btn-primary btn-block">
                            <i class="fas fa-search mr-1"></i> Cari
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <?php 
        $jumlahdata = $db->rowCOUNT("SELECT idorderdetail FROM vorderdetail ");
        $banyak = 3;

        $halaman = ceil($jumlahdata / $banyak);

        if (isset($_GET['p'])) {
            $p = $_GET['p'];
            $mulai = ($p * $banyak) - $banyak;
        } else {
            $mulai = 0;
        }

        $sql = "SELECT * FROM vorderdetail ORDER BY idorderdetail DESC LIMIT $mulai,$banyak ";

        if (isset ($_POST['simpan'])) {
            $tawal = $_POST['tawal'];
            $takhir = $_POST['takhir'];
            $sql = "SELECT * FROM vorderdetail WHERE tglorder BETWEEN '$tawal' AND '$takhir'";
        }

        $row = $db->getALL($sql);
        $no = 1 + $mulai;
        $total = 0;
    ?>

    <div class="table-responsive mt-4">
        <div class="card table-card">
            <div class="card-header gradient-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <i class="fas fa-list-alt mr-2"></i>
                        <span class="font-weight-bold">Daftar Pembelian</span>
                    </div>
                    <div class="order-count">
                        <span class="badge badge-light">Total: <?php echo $jumlahdata; ?> item</span>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                <table class="table table-hover order-table">
                    <thead class="thead-gradient">
                        <tr>
                            <th width="5%">No</th>
                            <th width="15%">Pelanggan</th>
                            <th width="12%">Tanggal</th>
                            <th width="15%">Menu</th>
                            <th width="10%">Harga</th>
                            <th width="8%">Jumlah</th>
                            <th width="10%">Total</th>
                            <th width="25%">Alamat</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(!empty($row)) { ?>
                            <?php foreach ($row as $r): ?>
                                <tr class="table-row-hover">
                                    <td><?php echo $no++ ?></td>    
                                    <td>
                                        <span class="customer-name">
                                            <i class="fas fa-user-circle text-primary mr-1"></i>
                                            <?php echo $r['pelanggan']; ?>
                                        </span>
                                    </td>         
                                    <td>
                                        <span class="date-badge">
                                            <i class="fas fa-calendar-day mr-1"></i>
                                            <?php echo $r['tglorder']; ?>
                                        </span>
                                    </td>    
                                    <td>
                                        <span class="menu-name">
                                            <i class="fas fa-utensils text-success mr-1"></i>
                                            <?php echo $r['menu']; ?>
                                        </span>
                                    </td>         
                                    <td class="text-primary">Rp <?php echo number_format($r['harga'], 0, ',', '.'); ?></td>         
                                    <td class="text-center">
                                        <span class="quantity-badge">
                                            <?php echo $r['jumlah']; ?>
                                        </span>
                                    </td>         
                                    <td class="font-weight-bold text-success">Rp <?php echo number_format($r['jumlah'] * $r['harga'], 0, ',', '.'); ?></td>         
                                    <td>
                                        <span class="address-text">
                                            <i class="fas fa-map-marker-alt text-danger mr-1"></i>
                                            <?php echo $r['alamat']; ?>
                                        </span>
                                    </td>    
                                    <?php $total = $total + ($r['jumlah'] * $r['harga']); ?>
                                </tr>
                            <?php endforeach ?>
                        <?php } else { ?>
                            <tr>
                                <td colspan="8" class="text-center py-4">
                                    <div class="empty-state">
                                        <i class="fas fa-receipt fa-3x mb-3 text-muted"></i>
                                        <p>Tidak ada data pembelian yang ditemukan</p>
                                        <button class="btn btn-outline-primary btn-sm mt-2">
                                            <i class="fas fa-sync-alt mr-1"></i> Muat Ulang
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="card grand-total-card mt-3">
        <div class="card-body d-flex justify-content-between align-items-center gradient-total">
            <h3 class="mb-0 text-white">Grand Total</h3>
            <div class="total-amount">
                <span class="amount-label">Total Pembayaran</span>
                <h4 class="mb-0 text-white">Rp <?php echo number_format($total, 0, ',', '.'); ?></h4>
            </div>
        </div>
    </div>

    <div class="pagination-container mt-4">
        <nav aria-label="Page navigation">
            <ul class="pagination justify-content-center">
                <?php if(isset($_GET['p']) && $_GET['p'] > 1): ?>
                    <li class="page-item">
                        <a class="page-link" href="?f=orderdetail&m=select&p=<?php echo $_GET['p']-1; ?>" aria-label="Previous">
                            <span aria-hidden="true">&laquo;</span>
                        </a>
                    </li>
                <?php endif; ?>
                
                <?php for ($i=1; $i <= $halaman; $i++) { ?>
                    <li class="page-item <?php echo (isset($_GET['p']) && $_GET['p'] == $i) ? 'active' : ''; ?>">
                        <a class="page-link" href="?f=orderdetail&m=select&p=<?php echo $i; ?>">
                            <?php echo $i; ?>
                        </a>
                    </li>
                <?php } ?>
                
                <?php if(isset($_GET['p']) && $_GET['p'] < $halaman): ?>
                    <li class="page-item">
                        <a class="page-link" href="?f=orderdetail&m=select&p=<?php echo $_GET['p']+1; ?>" aria-label="Next">
                            <span aria-hidden="true">&raquo;</span>
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </nav>
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
        --accent: #ff6b35;
        --gradient-start: #4e73df;
        --gradient-end: #224abe;
        --gradient-total-start: #36b9cc;
        --gradient-total-end: #1a8a98;
    }
    
    .order-details-container {
        padding: 20px;
        background-color: #f8f9fc;
        border-radius: 15px;
    }
    
    .section-header {
        margin-bottom: 25px;
        background: linear-gradient(45deg, rgba(78, 115, 223, 0.1), rgba(54, 185, 204, 0.1));
        padding: 20px;
        border-radius: 10px;
        border-left: 5px solid var(--primary);
    }
    
    .section-title {
        color: var(--secondary);
        font-weight: 700;
        margin-bottom: 5px;
        font-size: 1.5rem;
    }
    
    .section-subtitle {
        color: #6c757d;
        font-size: 0.9rem;
    }
    
    .filter-card {
        border: none;
        border-radius: 10px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.08);
        margin-bottom: 25px;
        background: white;
        transition: transform 0.3s ease;
    }
    
    .filter-card:hover {
        transform: translateY(-5px);
    }
    
    .date-filter-form label {
        font-weight: 600;
        color: var(--secondary);
        font-size: 0.9rem;
    }
    
    .date-filter-form .form-control {
        border-radius: 8px;
        border: 1px solid #e0e0e0;
        padding: 12px 15px;
        transition: all 0.3s ease;
        background-color: #f8f9fc;
    }
    
    .date-filter-form .form-control:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
        background-color: white;
    }
    
    .btn-primary {
        background: linear-gradient(45deg, var(--gradient-start), var(--gradient-end));
        border: none;
        border-radius: 8px;
        font-weight: 600;
        padding: 12px 20px;
        transition: all 0.3s ease;
        box-shadow: 0 4px 10px rgba(78, 115, 223, 0.3);
    }
    
    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 15px rgba(78, 115, 223, 0.4);
    }
    
    .table-card {
        border: none;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(0,0,0,0.08);
        background: white;
    }
    
    .gradient-header {
        background: linear-gradient(45deg, var(--gradient-start), var(--gradient-end));
        color: white;
        padding: 15px 20px;
    }
    
    .order-count .badge {
        font-size: 0.8rem;
        padding: 5px 10px;
        border-radius: 20px;
        background-color: rgba(255, 255, 255, 0.3);
        color: white;
    }
    
    .order-table {
        margin-bottom: 0;
    }
    
    .thead-gradient {
        background: linear-gradient(45deg, var(--secondary), #3d3761);
        color: white;
    }
    
    .order-table th {
        font-weight: 600;
        border: none;
        padding: 15px;
        font-size: 0.9rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .order-table td {
        padding: 15px;
        vertical-align: middle;
        border-top: 1px solid #f0f0f0;
    }
    
    .table-row-hover:hover {
        background-color: rgba(78, 115, 223, 0.05);
        transform: scale(1.01);
        transition: all 0.2s ease;
    }
    
    .customer-name {
        font-weight: 600;
        color: var(--secondary);
        display: flex;
        align-items: center;
    }
    
    .menu-name {
        display: flex;
        align-items: center;
    }
    
    .date-badge {
        background-color: #f0f0f0;
        padding: 5px 10px;
        border-radius: 20px;
        font-size: 0.85rem;
        color: #555;
        display: inline-flex;
        align-items: center;
    }
    
    .quantity-badge {
        background: linear-gradient(45deg, var(--accent), #e55a2a);
        color: white;
        width: 30px;
        height: 30px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        font-weight: 600;
        box-shadow: 0 2px 5px rgba(229, 90, 42, 0.3);
    }
    
    .address-text {
        display: block;
        font-size: 0.9rem;
        color: #555;
        max-width: 250px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        transition: all 0.3s ease;
    }
    
    .address-text:hover {
        white-space: normal;
        overflow: visible;
        background-color: #f8f9fc;
        padding: 5px 10px;
        border-radius: 5px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.05);
    }
    
    .grand-total-card {
        border: none;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(0,0,0,0.08);
    }
    
    .gradient-total {
        background: linear-gradient(45deg, var(--gradient-total-start), var(--gradient-total-end));
        color: white;
    }
    
    .total-amount {
        text-align: right;
    }
    
    .amount-label {
        display: block;
        font-size: 0.8rem;
        color: rgba(255, 255, 255, 0.8);
        margin-bottom: 5px;
    }
    
    .total-amount h4 {
        font-weight: 700;
        letter-spacing: 0.5px;
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
        font-weight: 600;
        box-shadow: 0 2px 5px rgba(0,0,0,0.05);
    }
    
    .pagination .page-item.active .page-link {
        background: linear-gradient(45deg, var(--gradient-start), var(--gradient-end));
        color: white;
        box-shadow: 0 4px 10px rgba(78, 115, 223, 0.3);
    }
    
    .pagination .page-link:hover {
        background-color: #f0f0f0;
        color: var(--primary);
        transform: translateY(-2px);
    }
    
    .empty-state {
        padding: 40px;
        text-align: center;
        color: #6c757d;
    }
    
    .empty-state i {
        color: #d1d3e2;
    }
    
    .btn-outline-primary {
        color: var(--primary);
        border-color: var(--primary);
        transition: all 0.3s ease;
    }
    
    .btn-outline-primary:hover {
        background-color: var(--primary);
        color: white;
    }
    
    @media (max-width: 768px) {
        .order-table {
            min-width: 800px;
        }
        
        .section-header {
            padding: 15px;
        }
        
        .filter-card .row {
            flex-direction: column;
        }
        
        .filter-card .col-md-2 {
            margin-top: 15px;
        }
    }
</style>