<div class="cart-container">
    <div class="header-section">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div class="section-title-container">
                <h3 class="section-title">
                    <i class="fas fa-shopping-cart mr-2"></i> Keranjang Belanja
                </h3>
                <p class="section-subtitle">Review dan sesuaikan pesanan Anda sebelum checkout</p>
            </div>
            <div class="header-actions">
                <a href="?f=home&m=produk" class="btn btn-outline-primary continue-shopping-btn">
                    <i class="fas fa-arrow-left mr-2"></i> Lanjut Belanja
                </a>
            </div>
        </div>
    </div>

    <?php 
        // Start session and include dependencies if needed
        // session_start(); // If not already started in a parent file
        
        // Handle cart actions and redirects BEFORE any output
        if (isset($_GET['hapus'])) {
            $id = $_GET['hapus'];
            unset($_SESSION['_'.$id]);
            echo '<script>window.location.href="?f=home&m=beli";</script>';
            exit();
        }
        
        if (isset($_GET['tambah'])) {
            $id = $_GET['tambah'];
            $_SESSION['_'.$id]++;
            echo '<script>window.location.href="?f=home&m=beli";</script>';
            exit();
        }
        
        if (isset($_GET['kurang'])) {
            $id = $_GET['kurang'];
            $_SESSION['_'.$id]--;
        
            if ($_SESSION['_'.$id]==0) {
                unset($_SESSION['_'.$id]);
            }
            echo '<script>window.location.href="?f=home&m=beli";</script>';
            exit();
        }
        
        // Check if user is logged in
        if (!isset($_SESSION['pelanggan'])) {
            // header("location:?f=home&m=login");
            echo '<script>window.location.href="?f=home&m=login";</script>';
            exit();
        } else {
            if (isset($_GET['id'])) {
                $id = $_GET['id'];
                isi($id);
                echo '<script>window.location.href="?f=home&m=beli";</script>';
                exit();
            } else {
                keranjang();
            }
        }

        // Function to add item to cart
        function isi($id){
            if (isset($_SESSION['_'.$id])) {
                $_SESSION['_'.$id]++;
            } else {
                $_SESSION['_'.$id]=1;
            }
        }

        // Function to display cart
        function keranjang(){
            global $db;
            $total = 0;
            global $total;
            
            // Check if cart is empty
            $cartEmpty = true;
            foreach ($_SESSION as $key => $value) {
                if ($key<>'pelanggan' && $key<> 'idpelanggan' && $key<> 'user' && $key<> 'level' && $key<> 'iduser' && substr($key,0,1)=='_') {
                    $cartEmpty = false;
                    break;
                }
            }
            
            if ($cartEmpty) {
                // Display empty cart message
                echo '<div class="card empty-cart-card">
                        <div class="card-body text-center">
                            <div class="empty-cart-icon">
                                <i class="fas fa-shopping-cart fa-4x"></i>
                            </div>
                            <h4 class="mt-3">Keranjang Belanja Anda Kosong</h4>
                            <p class="text-muted">Anda belum menambahkan menu apapun ke keranjang.</p>
                            <a href="?f=home&m=produk" class="btn btn-primary mt-3">
                                <i class="fas fa-utensils mr-2"></i> Lihat Menu
                            </a>
                        </div>
                      </div>';
            } else {
                // Display cart items
                echo '<div class="card cart-card">
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table cart-table mb-0">
                                    <thead class="thead-primary">
                                        <tr>
                                            <th width="40%">Menu</th>
                                            <th width="15%">Harga</th>
                                            <th width="20%">Jumlah</th>
                                            <th width="15%">Total</th>
                                            <th width="10%">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>';
                
                foreach ($_SESSION as $key => $value) { 
                    if ($key<>'pelanggan' && $key<> 'idpelanggan' && $key<> 'user' && $key<> 'level' && $key<> 'iduser' && substr($key,0,1)=='_') {
                        $id = substr($key,1);
                        $sql = "SELECT * FROM tblmenu WHERE idmenu=$id";
                        $row = $db->getAll($sql);

                        if ($row) { // Tambahkan pengecekan apakah $row tidak null
                            foreach ($row as $r) {
                                echo '<tr>
                                        <td>
                                            <div class="menu-info">
                                                <img src="upload/'.$r['gambar'].'" alt="'.$r['menu'].'" class="menu-image">
                                                <div class="menu-details">
                                                    <h5 class="menu-name">'.$r['menu'].'</h5>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                        <div class="price-badge">
                                            Rp '.number_format($r['harga'], 0, ',', '.').'
                                        </div>
                                    </td>
                                    <td>
                                        <div class="quantity-control">
                                            <a href="?f=home&m=beli&kurang='.$r['idmenu'].'" class="quantity-btn minus">
                                                <i class="fas fa-minus"></i>
                                            </a>
                                            <span class="quantity-value">'.$value.'</span>
                                            <a href="?f=home&m=beli&tambah='.$r['idmenu'].'" class="quantity-btn plus">
                                                <i class="fas fa-plus"></i>
                                            </a>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="total-price">
                                            Rp '.number_format($r['harga'] * $value, 0, ',', '.').'
                                        </div>
                                    </td>
                                    <td>
                                        <a href="?f=home&m=beli&hapus='.$r['idmenu'].'" class="btn btn-sm btn-outline-danger delete-btn" onclick="return confirm(\'Apakah Anda yakin ingin menghapus item ini?\')">
                                            <i class="fas fa-trash-alt"></i>
                                        </a>
                                    </td>
                                  </tr>';
                                $total = $total + ($value * $r['harga']);
                            }
                        } else {
                            // Menu tidak ditemukan, hapus dari session
                            unset($_SESSION['_'.$id]);
                        }
                    }
                }
                
                echo '</tbody>
                      </table>
                      </div>
                      </div>
                      </div>';
                
                // Order summary card
                echo '<div class="card order-summary-card mt-4">
                        <div class="card-header order-summary-header">
                            <h5 class="mb-0">
                                <i class="fas fa-receipt mr-2"></i> Ringkasan Pesanan
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="summary-item">
                                <span class="summary-label">Subtotal</span>
                                <span class="summary-value">Rp '.number_format($total, 0, ',', '.').'</span>
                            </div>
                            <div class="summary-item">
                                <span class="summary-label">Biaya Pengiriman</span>
                                <span class="summary-value">Gratis</span>
                            </div>
                            <div class="summary-divider"></div>
                            <div class="summary-item grand-total">
                                <span class="summary-label">Total Pembayaran</span>
                                <span class="summary-value">Rp '.number_format($total, 0, ',', '.').'</span>
                            </div>
                            
                            <a href="?f=home&m=checkout&total='.$total.'" class="btn btn-primary btn-block checkout-btn mt-3">
                                <i class="fas fa-check-circle mr-2"></i> CHECKOUT
                            </a>
                        </div>
                      </div>';
            }
        }
    ?>
</div>

<style>
    .cart-container {
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
    
    .continue-shopping-btn {
        border-color: var(--primary);
        color: var(--primary);
        transition: all 0.3s ease;
    }
    
    .continue-shopping-btn:hover {
        background-color: var(--primary);
        color: white;
    }
    
    .cart-card, .order-summary-card, .empty-cart-card {
        border: none;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        background-color: white;
    }
    
    .empty-cart-card {
        padding: 40px 20px;
    }
    
    .empty-cart-icon {
        color: #e0e0e0;
        margin-bottom: 20px;
    }
    
    .cart-table {
        margin-bottom: 0;
    }
    
    .thead-primary {
        background-color: var(--secondary);
        color: white;
    }
    
    .cart-table th {
        font-weight: 500;
        border: none;
        padding: 15px;
    }
    
    .cart-table td {
        padding: 15px;
        vertical-align: middle;
        border-top: 1px solid #f0f0f0;
    }
    
    .menu-info {
        display: flex;
        align-items: center;
    }
    
    .menu-image {
        width: 60px;
        height: 60px;
        border-radius: 8px;
        object-fit: cover;
        margin-right: 15px;
    }
    
    .menu-name {
        font-weight: 500;
        font-size: 1rem;
        margin-bottom: 5px;
        color: var(--secondary);
    }
    
    .price-badge {
        background-color: #f0f0f0;
        padding: 5px 10px;
        border-radius: 20px;
        font-size: 0.9rem;
        font-weight: 500;
        display: inline-block;
    }
    
    .quantity-control {
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .quantity-btn {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        background-color: #f0f0f0;
        color: var(--secondary);
        display: flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        transition: all 0.3s ease;
    }
    
    .quantity-btn:hover {
        background-color: var(--primary);
        color: white;
    }
    
    .quantity-value {
        margin: 0 10px;
        font-weight: 500;
        min-width: 30px;
        text-align: center;
    }
    
    .total-price {
        font-weight: 600;
        color: var(--primary);
    }
    
    .delete-btn {
        color: #dc3545;
        border-color: #dc3545;
        transition: all 0.3s ease;
    }
    
    .delete-btn:hover {
        background-color: #dc3545;
        color: white;
    }
    
    .order-summary-header {
        background-color: var(--secondary);
        color: white;
        padding: 15px 20px;
    }
    
    .summary-item {
        display: flex;
        justify-content: space-between;
        margin-bottom: 15px;
    }
    
    .summary-label {
        color: #6c757d;
    }
    
    .summary-value {
        font-weight: 500;
    }
    
    .summary-divider {
        border-top: 1px dashed #e0e0e0;
        margin: 15px 0;
    }
    
    .grand-total {
        margin-top: 10px;
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
    
    .checkout-btn {
        background-color: var(--primary);
        border-color: var(--primary);
        padding: 12px;
        font-weight: 500;
        font-size: 1.1rem;
        transition: all 0.3s ease;
    }
    
    .checkout-btn:hover {
        background-color: #e55a2a;
        border-color: #e55a2a;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(255, 107, 53, 0.3);
    }
    
    @media (max-width: 768px) {
        .cart-table {
            min-width: 700px;
        }
        
        .menu-info {
            flex-direction: column;
            align-items: flex-start;
        }
        
        .menu-image {
            margin-bottom: 10px;
        }
    }
</style>
