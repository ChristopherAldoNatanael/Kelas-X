<?php 
    $row = $db->getALL("SELECT * FROM tblkategori ORDER BY kategori ASC");

    if(isset($_GET['id'])){
        $id = $_GET['id'];
        $sql = "SELECT * FROM tblmenu WHERE idmenu=$id";
        $item = $db->getITEM($sql);
        $idkategori = $item['idkategori'];
    }
?>

<div class="update-menu-container">
    <div class="header-section">
        <div class="d-flex align-items-center mb-4">
            <div class="section-title-container">
                <h3 class="section-title">
                    <i class="fas fa-edit mr-2"></i> Update Menu
                </h3>
                <p class="section-subtitle">Edit the details of your menu item</p>
            </div>
        </div>
    </div>

    <div class="card form-card">
        <div class="card-body">
            <form action="" method="post" enctype="multipart/form-data" class="menu-form">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-section">
                            <h5 class="form-section-title">
                                <i class="fas fa-info-circle mr-2"></i> Basic Information
                            </h5>
                            
                            <div class="form-group mb-4">
                                <label for="idkategori" class="form-label">
                                    <i class="fas fa-tag mr-1"></i> Kategori
                                </label>
                                <select name="idkategori" id="idkategori" class="form-select">
                                    <?php foreach($row as $r): ?>
                                    <option <?php if ($idkategori == $r['idkategori']) echo "selected"; ?> value="<?php echo $r['idkategori'] ?>"><?php echo $r['kategori'] ?></option>
                                    <?php endforeach ?>
                                </select>
                            </div>
                            
                            <div class="form-group mb-4">
                                <label for="menu" class="form-label">
                                    <i class="fas fa-utensils mr-1"></i> Nama Menu
                                </label>
                                <input type="text" name="menu" id="menu" value="<?php echo $item['menu'];?>" required class="form-control">                      
                            </div>
                            
                            <div class="form-group mb-4">
                                <label for="harga" class="form-label">
                                    <i class="fas fa-tag mr-1"></i> Harga
                                </label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Rp</span>
                                    </div>
                                    <input type="number" name="harga" id="harga" value="<?php echo $item['harga'];?>" required class="form-control">
                                </div>
                                <small class="form-text text-muted">Masukkan harga tanpa titik atau koma</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-section">
                            <h5 class="form-section-title">
                                <i class="fas fa-image mr-2"></i> Menu Image
                            </h5>
                            
                            <div class="form-group mb-4">
                                <label class="form-label d-block">
                                    <i class="fas fa-image mr-1"></i> Current Image
                                </label>
                                
                                <div class="current-image-container">
                                    <img src="../upload/<?php echo $item['gambar']; ?>" alt="<?php echo $item['menu']; ?>" class="current-image">
                                </div>
                                <p class="current-image-name">
                                    <i class="fas fa-file-image mr-1"></i> <?php echo $item['gambar']; ?>
                                </p>
                            </div>
                            
                            <div class="form-group mb-4">
                                <label for="gambar" class="form-label d-block">
                                    <i class="fas fa-upload mr-1"></i> Upload New Image (Optional)
                                </label>
                                
                                <div class="image-upload-container">
                                    <div class="image-preview" id="imagePreview">
                                        <div class="image-upload-placeholder">
                                            <i class="fas fa-cloud-upload-alt fa-3x mb-2"></i>
                                            <p>Click to select or drag a new image here</p>
                                            <small class="text-muted">Leave empty to keep current image</small>
                                        </div>
                                        <img src="/placeholder.svg" alt="Preview" id="preview-image">
                                    </div>
                                    <input type="file" name="gambar" id="gambar" class="image-upload-input" accept="image/*" onchange="previewImage(this)">
                                </div>
                                <small class="form-text text-muted">Recommended size: 500x500 pixels, Max size: 2MB</small>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="form-actions">
                    <button type="submit" name="simpan" class="btn btn-primary submit-btn">
                        <i class="fas fa-save mr-2"></i> Update Menu
                    </button>
                    <a href="?f=menu&m=select" class="btn btn-outline-secondary cancel-btn">
                        <i class="fas fa-times mr-2"></i> Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
    
    <?php 
        if(isset($_POST['simpan'])){
            $idkategori = $_POST['idkategori']; 
            $menu = $_POST['menu'];
            $harga = $_POST['harga'];
            $gambar = $item['gambar'];
            $temp = $_FILES['gambar']['tmp_name'];

            if(!empty($temp)){
                $gambar = $_FILES['gambar']['name'];
                move_uploaded_file($temp, '../upload/'.$gambar);
            }
            
            $sql = "UPDATE tblmenu SET idkategori=$idkategori, menu='$menu', gambar = '$gambar', harga = $harga WHERE idmenu = $id";

            $db->runSQL($sql);
            
            echo '<div class="alert alert-success mt-4" role="alert">
                    <i class="fas fa-check-circle mr-2"></i> Menu berhasil diupdate!
                  </div>';
            echo '<script>
                    setTimeout(function() {
                        window.location.href = "?f=menu&m=select";
                    }, 1500);
                  </script>';
        }
    ?>
</div>

<style>
    .update-menu-container {
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
    
    .form-card {
        border: none;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        background-color: white;
        margin-bottom: 20px;
    }
    
    .form-section {
        margin-bottom: 20px;
    }
    
    .form-section-title {
        color: var(--secondary);
        font-weight: 500;
        margin-bottom: 20px;
        padding-bottom: 10px;
        border-bottom: 1px solid #f0f0f0;
    }
    
    .form-label {
        font-weight: 500;
        color: var(--secondary);
        margin-bottom: 8px;
    }
    
    .form-control, .form-select {
        border-radius: 6px;
        border: 1px solid #e0e0e0;
        padding: 10px 15px;
        transition: all 0.3s ease;
    }
    
    .form-control:focus, .form-select:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 0.2rem rgba(255, 107, 53, 0.25);
    }
    
    .input-group-text {
        background-color: var(--secondary);
        color: white;
        border: none;
        border-radius: 6px 0 0 6px;
    }
    
    .current-image-container {
        width: 100%;
        height: 200px;
        border-radius: 10px;
        overflow: hidden;
        margin-bottom: 10px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
    
    .current-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    .current-image-name {
        font-size: 0.9rem;
        color: #6c757d;
        margin-bottom: 20px;
        padding: 5px 10px;
        background-color: #f8f9fa;
        border-radius: 5px;
        display: inline-block;
    }
    
    .image-upload-container {
        position: relative;
        width: 100%;
        margin-bottom: 15px;
    }
    
    .image-preview {
        width: 100%;
        height: 200px;
        border: 2px dashed #e0e0e0;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        position: relative;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    
    .image-preview:hover {
        border-color: var(--primary);
    }
    
    .image-preview img {
        max-width: 100%;
        max-height: 100%;
        object-fit: contain;
        display: none;
    }
    
    .image-upload-placeholder {
        text-align: center;
        color: #6c757d;
    }
    
    .image-upload-input {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        opacity: 0;
        cursor: pointer;
    }
    
    .form-actions {
        display: flex;
        gap: 10px;
        margin-top: 30px;
        padding-top: 20px;
        border-top: 1px solid #f0f0f0;
    }
    
    .submit-btn {
        background-color: var(--primary);
        border-color: var(--primary);
        padding: 10px 25px;
        font-weight: 500;
        transition: all 0.3s ease;
    }
    
    .submit-btn:hover {
        background-color: #e55a2a;
        border-color: #e55a2a;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(255, 107, 53, 0.3);
    }
    
    .cancel-btn {
        padding: 10px 25px;
        font-weight: 500;
    }
    
    .alert {
        border-radius: 8px;
        padding: 15px;
    }
    
    .alert-success {
        background-color: #d4edda;
        border-color: #c3e6cb;
        color: #155724;
    }
    
    @media (max-width: 768px) {
        .form-actions {
            flex-direction: column;
        }
        
        .submit-btn, .cancel-btn {
            width: 100%;
        }
    }
</style>

<script>
    function previewImage(input) {
        const preview = document.getElementById('preview-image');
        const placeholder = document.querySelector('.image-upload-placeholder');
        
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.style.display = 'block';
                placeholder.style.display = 'none';
            }
            
            reader.readAsDataURL(input.files[0]);
        } else {
            preview.style.display = 'none';
            placeholder.style.display = 'flex';
        }
    }
</script>
