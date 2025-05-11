<div class="menu-management-container">
    <div class="header-section">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div class="section-title-container">
                <h3 class="section-title">
                    <i class="fas fa-utensils mr-2"></i> Menu Management
                </h3>
                <p class="section-subtitle">Manage your restaurant menu items</p>
            </div>
            <a class="btn btn-primary add-btn" href="?f=menu&m=insert" role="button">
                <i class="fas fa-plus-circle mr-2"></i> Tambah Data
            </a>
        </div>
    </div>

    <?php 
        if(isset($_POST['opsi'])){
            $opsi = $_POST['opsi'];
            $where = "WHERE idkategori = $opsi";
        } else{
            $opsi = 0;
            $where = "";
        }
    ?>

    <div class="filter-section card">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <div class="filter-label">
                        <i class="fas fa-filter mr-2"></i> Filter by Category:
                    </div>
                </div>
                <div class="col-md-6">
                    <?php 
                        $row = $db->getALL("SELECT * FROM tblkategori ORDER BY kategori ASC");
                    ?>
                    <form action="" method="post" class="category-filter-form">
                        <select name="opsi" id="categoryFilter" class="form-select" onchange="this.form.submit()">
                            <option value="0" <?php if($opsi == 0) echo "selected"; ?>>All Categories</option>
                            <?php foreach($row as $r): ?>
                            <option <?php if($r['idkategori']==$opsi) echo "selected"; ?> value="<?php echo $r['idkategori'] ?>"><?php echo $r['kategori'] ?></option>
                            <?php endforeach ?>
                        </select>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <?php 
        $jumlahdata = $db->rowCOUNT("SELECT idmenu FROM tblmenu $where");
        
        $banyak = 3;
        
        $halaman = ceil($jumlahdata / $banyak);

        if(isset($_GET['p'])){
            $p = $_GET['p'];
            $mulai = ($p * $banyak) - $banyak;
        } else{
            $mulai = 0;
        };
        
        $sql = "SELECT * FROM tblmenu $where ORDER BY menu ASC LIMIT $mulai, $banyak";
        
        $row = $db->getALL($sql);
        
        $no = 1 + $mulai;
    ?>

    <div class="table-responsive mt-4">
        <div class="card table-card">
            <div class="card-body p-0">
                <table class="table menu-table">
                    <thead class="thead-primary">
                        <tr>
                            <th width="5%">No</th>
                            <th width="25%">Menu</th>
                            <th width="15%">Harga</th>
                            <th width="25%">Gambar</th>
                            <th width="15%">Update</th>
                            <th width="15%">Delete</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(!empty($row)){ ?>
                            <?php foreach($row as $r): ?>
                            <tr>
                                <td class="text-center"><?php echo $no++ ?></td>    
                                <td>
                                    <div class="menu-name">
                                        <?php echo $r['menu']; ?>
                                    </div>
                                </td>    
                                <td>
                                    <div class="price-badge">
                                        Rp <?php echo number_format($r['harga'], 0, ',', '.'); ?>
                                    </div>
                                </td>    
                                <td>
                                    <div class="menu-image-container">
                                        <img class="menu-image" src="../upload/<?php echo $r['gambar']; ?>" alt="<?php echo $r['menu']; ?>">
                                    </div>
                                </td>    
                                <td>
                                    <a href="?f=menu&m=update&id=<?php echo $r['idmenu']?>" class="btn btn-sm btn-outline-primary action-btn update-btn">
                                        <i class="fas fa-edit mr-1"></i> Update
                                    </a>
                                </td>      
                                <td>
                                    <a href="?f=menu&m=delete&id=<?php echo $r['idmenu']?>" class="btn btn-sm btn-outline-danger action-btn delete-btn" onclick="return confirm('Are you sure you want to delete this item?')">
                                        <i class="fas fa-trash-alt mr-1"></i> Delete
                                    </a>
                                </td>    
                            </tr>
                            <?php endforeach ?>
                        <?php } else { ?>
                            <tr>
                                <td colspan="6" class="text-center py-4">
                                    <div class="empty-state">
                                        <i class="fas fa-utensils fa-3x mb-3 text-muted"></i>
                                        <p>No menu items found. Add some items to get started.</p>
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
                <?php for ($i = 1; $i <= $halaman; $i++){ ?>
                    <li class="page-item <?php echo (isset($_GET['p']) && $_GET['p'] == $i) ? 'active' : ''; ?>">
                        <a class="page-link" href="?f=menu&m=select&p=<?php echo $i; ?>">
                            <?php echo $i; ?>
                        </a>
                    </li>
                <?php }; ?>
            </ul>
        </nav>
    </div>
</div>

<style>
    .menu-management-container {
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
    
    .add-btn {
        background-color: var(--primary);
        border-color: var(--primary);
        border-radius: 6px;
        font-weight: 500;
        padding: 10px 20px;
        transition: all 0.3s ease;
        box-shadow: 0 2px 5px rgba(255, 107, 53, 0.3);
    }
    
    .add-btn:hover {
        background-color: #e55a2a;
        border-color: #e55a2a;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(255, 107, 53, 0.4);
    }
    
    .filter-section {
        border: none;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        background-color: #f8f9fa;
    }
    
    .filter-label {
        font-weight: 500;
        color: var(--secondary);
    }
    
    .category-filter-form .form-select {
        border-radius: 6px;
        border: 1px solid #e0e0e0;
        padding: 10px 15px;
        transition: all 0.3s ease;
        background-color: white;
        width: 100%;
        cursor: pointer;
    }
    
    .category-filter-form .form-select:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 0.2rem rgba(255, 107, 53, 0.25);
    }
    
    .table-card {
        border: none;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    }
    
    .menu-table {
        margin-bottom: 0;
    }
    
    .thead-primary {
        background-color: var(--secondary);
        color: white;
    }
    
    .menu-table th {
        font-weight: 500;
        border: none;
        padding: 15px;
    }
    
    .menu-table td {
        padding: 15px;
        vertical-align: middle;
        border-top: 1px solid #f0f0f0;
    }
    
    .menu-name {
        font-weight: 600;
        color: var(--secondary);
        font-size: 1.05rem;
    }
    
    .price-badge {
        background-color: var(--accent);
        color: white;
        padding: 5px 10px;
        border-radius: 20px;
        display: inline-block;
        font-weight: 500;
        font-size: 0.9rem;
    }
    
    .menu-image-container {
        width: 100px;
        height: 100px;
        overflow: hidden;
        border-radius: 8px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        margin: 0 auto;
        position: relative;
    }
    
    .menu-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s ease;
    }
    
    .menu-image:hover {
        transform: scale(1.1);
    }
    
    .action-btn {
        width: 100%;
        margin-bottom: 5px;
        border-radius: 5px;
        transition: all 0.3s ease;
    }
    
    .update-btn {
        color: var(--primary);
        border-color: var(--primary);
    }
    
    .update-btn:hover {
        background-color: var(--primary);
        border-color: var(--primary);
        color: white;
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
        .menu-table {
            min-width: 800px;
        }
        
        .filter-label {
            margin-bottom: 10px;
        }
    }
</style>
