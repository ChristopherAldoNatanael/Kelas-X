<div class="menu-container">
    <div class="header-section">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div class="section-title-container">
                <h3 class="section-title">
                    <i class="fas fa-utensils mr-2"></i> Menu
                </h3>
                <p class="section-subtitle">Pilih menu favorit Anda dari Warung Aldo</p>
            </div>
            <?php if (isset($_SESSION['pelanggan'])): ?>
            <div class="header-actions">
                <a href="?f=home&m=beli" class="btn btn-outline-primary cart-btn">
                    <i class="fas fa-shopping-cart mr-2"></i> Lihat Keranjang
                </a>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="mt-4 mb-4">
        <?php 
            if (isset($_GET['id'])) {
                $id = $_GET['id'];
                $where = "WHERE idkategori = $id";
                $idParam = "&id=".$id; // Perbaikan di sini, mengubah "$id=" menjadi "&id="
                
                // Get category name
                $sqlCategory = "SELECT kategori FROM tblkategori WHERE idkategori = $id";
                $category = $db->getITEM($sqlCategory);
                $categoryName = $category ? $category['kategori'] : 'Semua Menu';
            } else {
                $where = "";
                $idParam = "";
                $categoryName = "Semua Menu";
            }
        ?>
        
        <div class="category-header">
            <h4 class="category-title"><?php echo $categoryName; ?></h4>
            <?php if (isset($_GET['id'])): ?>
                <a href="?f=home&m=produk" class="btn btn-sm btn-outline-secondary">
                    <i class="fas fa-times mr-1"></i> Hapus Filter
                </a>
            <?php endif; ?>
        </div>
    </div>

    <?php 
        $jumlahdata = $db->rowCOUNT("SELECT idmenu FROM tblmenu $where");
        $banyak = 6; // Increased items per page
        
        $halaman = ceil($jumlahdata / $banyak);
        
        if(isset($_GET['p'])){
            $p = $_GET['p'];
            $mulai = ($p * $banyak) - $banyak;
        } else{
            $mulai = 0;
        }
        
        $sql = "SELECT * FROM tblmenu $where ORDER BY menu ASC LIMIT $mulai, $banyak";
        $row = $db->getALL($sql);
        
        $no = 1 + $mulai;
    ?>

    <?php if(empty($row)): ?>
        <div class="empty-menu">
            <div class="empty-menu-icon">
                <i class="fas fa-utensils fa-4x"></i>
            </div>
            <h4>Tidak Ada Menu</h4>
            <p>Tidak ada menu yang tersedia untuk kategori ini.</p>
            <a href="?f=home&m=produk" class="btn btn-primary">
                <i class="fas fa-list mr-2"></i> Lihat Semua Menu
            </a>
        </div>
    <?php else: ?>
        <div class="row menu-grid">
            <?php foreach($row as $r): ?>
                <div class="col-md-4 col-sm-6 mb-4">
                    <div class="menu-card">
                        <div class="menu-image-container">
                            <img src="upload/<?php echo $r['gambar']; ?>" class="menu-image" alt="<?php echo $r['menu']; ?>">
                            <?php 
                                // Get category name for badge
                                $sqlCat = "SELECT kategori FROM tblkategori WHERE idkategori = ".$r['idkategori'];
                                $cat = $db->getITEM($sqlCat);
                            ?>
                            <div class="menu-category-badge">
                                <?php echo $cat['kategori']; ?>
                            </div>
                        </div>
                        <div class="menu-card-body">
                            <h5 class="menu-title"><?php echo $r['menu']; ?></h5>
                            <div class="menu-price">
                                <span class="price-value">Rp <?php echo number_format($r['harga'], 0, ',', '.'); ?></span>
                            </div>
                            <div class="menu-actions">
                                <a class="btn btn-primary buy-btn" href="?f=home&m=beli&id=<?php echo $r['idmenu']?>" role="button">
                                    <i class="fas fa-shopping-cart mr-2"></i> BELI
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <div class="pagination-container mt-4">
            <nav aria-label="Page navigation">
                <ul class="pagination justify-content-center">
                    <?php for ($i = 1; $i <= $halaman; $i++): ?>
                        <li class="page-item <?php echo (isset($_GET['p']) && $_GET['p'] == $i) ? 'active' : ''; ?>">
                            <a class="page-link" href="?f=home&m=produk&p=<?php echo $i.$idParam; ?>">
                                <?php echo $i; ?>
                            </a>
                        </li>
                    <?php endfor; ?>
                </ul>
            </nav>
        </div>
    <?php endif; ?>
</div>

<style>
    .menu-container {
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
    
    .cart-btn {
        border-color: var(--primary);
        color: var(--primary);
        transition: all 0.3s ease;
    }
    
    .cart-btn:hover {
        background-color: var(--primary);
        color: white;
    }
    
    .category-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        background-color: #f8f9fa;
        padding: 15px 20px;
        border-radius: 10px;
        margin-bottom: 25px;
    }
    
    .category-title {
        margin-bottom: 0;
        color: var(--secondary);
        font-weight: 600;
    }
    
    .empty-menu {
        text-align: center;
        padding: 60px 20px;
        background-color: white;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    }
    
    .empty-menu-icon {
        color: #e0e0e0;
        margin-bottom: 20px;
    }
    
    .menu-grid {
        margin-right: -10px;
        margin-left: -10px;
    }
    
    .menu-card {
        background-color: white;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        height: 100%;
        transition: all 0.3s ease;
    }
    
    .menu-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    }
    
    .menu-image-container {
        position: relative;
        height: 180px;
        overflow: hidden;
    }
    
    .menu-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s ease;
    }
    
    .menu-card:hover .menu-image {
        transform: scale(1.1);
    }
    
    .menu-category-badge {
        position: absolute;
        top: 10px;
        right: 10px;
        background-color: rgba(46, 41, 78, 0.8);
        color: white;
        padding: 5px 10px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 500;
    }
    
    .menu-card-body {
        padding: 20px;
    }
    
    .menu-title {
        color: var(--secondary);
        font-weight: 600;
        margin-bottom: 10px;
        height: 48px;
        overflow: hidden;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
    }
    
    .menu-price {
        margin-bottom: 15px;
    }
    
    .price-value {
        color: var(--primary);
        font-weight: 700;
        font-size: 1.2rem;
    }
    
    .menu-actions {
        display: flex;
        justify-content: center;
    }
    
    .buy-btn {
        background-color: var(--primary);
        border-color: var(--primary);
        width: 100%;
        padding: 10px;
        font-weight: 500;
        transition: all 0.3s ease;
    }
    
    .buy-btn:hover {
        background-color: #e55a2a;
        border-color: #e55a2a;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(255, 107, 53, 0.3);
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
    
    @media (max-width: 768px) {
        .category-header {
            flex-direction: column;
            align-items: flex-start;
        }
        
        .category-header .btn {
            margin-top: 10px;
        }
    }
</style>
