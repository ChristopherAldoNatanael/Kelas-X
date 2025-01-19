<?php
session_start();
include '../db/connection.php';

// Cek apakah pengguna adalah admin
if ($_SESSION['role'] != 'admin') {
    header('Location: ../index.php');
    exit();
}

$username = $_SESSION['username'];
$user_id = $_SESSION['id'];

// Ambil semua produk dari database
$products = $conn->query("SELECT * FROM products");

// Daftar kategori yang tersedia
$categories = ['kaos_pria', 'kaos_wanita', 'jam_tangan', 'sepatu'];

// Cek apakah ada produk yang ingin diedit
$product_to_edit = null;
if (isset($_GET['edit'])) {
    $product_id = $_GET['edit'];
    $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $product_to_edit = $result->fetch_assoc();
    }
}

// Proses untuk menyimpan produk baru atau mengupdate produk yang ada
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validasi input
    if (!empty($_POST['product_name']) && !empty($_POST['product_price']) && !empty($_POST['product_detail']) && !empty($_POST['product_category'])) {
        $product_name = $_POST['product_name'];
        $product_price = $_POST['product_price'];
        $product_detail = $_POST['product_detail'];
        $product_category = $_POST['product_category'];
        $target_file = '';

        // Handle image upload
        if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] == 0) {
            $target_dir = "../images/";
            $imageFileType = strtolower(pathinfo($_FILES["product_image"]["name"], PATHINFO_EXTENSION));
            $target_file = uniqid() . '.' . $imageFileType;

            // Validate file type
            $allowed_types = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            if (in_array($imageFileType, $allowed_types)) {
                if (!move_uploaded_file($_FILES["product_image"]["tmp_name"], $target_dir . $target_file)) {
                    $_SESSION['message'] = "Gagal mengupload gambar.";
                    $_SESSION['message_type'] = "error";
                    header("Location: home.php");
                    exit();
                }
            } else {
                $_SESSION['message'] = "Format gambar tidak valid.";
                $_SESSION['message_type'] = "error";
                header("Location: home.php");
                exit();
            }
        }

        // Jika tidak ada gambar diupload, pastikan target_file tetap null atau kosong
        if ($target_file == '') {
            $target_file = NULL;
        }

        if (isset($_POST['product_id'])) {
            // Update existing product
            $product_id = $_POST['product_id'];
            if ($target_file != NULL) {
                // Jika ada gambar baru, update juga kolom image
                $stmt = $conn->prepare("UPDATE products SET name=?, price=?, detail=?, category=?, image=? WHERE id=?");
                $stmt->bind_param("sisssi", $product_name, $product_price, $product_detail, $product_category, $target_file, $product_id);
            } else {
                // Jika tidak ada gambar baru, jangan update kolom image
                $stmt = $conn->prepare("UPDATE products SET name=?, price=?, detail=?, category=? WHERE id=?");
                $stmt->bind_param("sissi", $product_name, $product_price, $product_detail, $product_category, $product_id);
            }
        } else {
            // Insert new product
            $stmt = $conn->prepare("INSERT INTO products (name, price, detail, category, image) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sisss", $product_name, $product_price, $product_detail, $product_category, $target_file);
        }

        // Eksekusi query dan tangani error jika ada
        if ($stmt->execute()) {
            $_SESSION['message'] = "Produk berhasil disimpan!";
            $_SESSION['message_type'] = "success";
            header("Location: home.php");
            exit();
        } else {
            $_SESSION['message'] = "Error: " . htmlspecialchars($stmt->error);
            $_SESSION['message_type'] = "error";
            error_log("SQL Error: " . htmlspecialchars($stmt->error));
        }
        // Tutup statement
        $stmt->close();
    } else {
        $_SESSION['message'] = "Semua field wajib diisi!";
        $_SESSION['message_type'] = "error";
    }
}
?>


<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - KaosKita</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #3498db;
            --secondary-color: #2c3e50;
            --accent-color: #e74c3c;
            --background-color: #f6f8ff;
            --text-color: #2c3e50;
            --card-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            --hover-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background: var(--background-color);
            color: var(--text-color);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Navigation */
        nav {
            background: linear-gradient(135deg, var(--secondary-color), var(--primary-color));
            padding: 1rem 2rem;
            position: sticky;
            top: 0;
            z-index: 1000;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .nav-content {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .nav-content h1 {
            color: white;
            font-size: 1.5rem;
        }

        .user-info {
            color: white;
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        /* Main Content */
        .container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 1rem;
        }

        /* Form Styling */
        .product-form {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            box-shadow: var(--card-shadow);
            margin-bottom: 2rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: var(--text-color);
        }

        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 0.8rem;
            border: 1px solid #ddd;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .form-group input:focus,
        .form-group textarea:focus,
        .form-group select:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
        }

        .btn {
            padding: 0.8rem 1.5rem;
            border-radius: 8px;
            border: none;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-primary {
            background: var(--primary-color);
            color: white;
        }

        .btn-danger {
            background: var(--accent-color);
            color: white;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: var(--hover-shadow);
        }

        /* Table Styling */
        .products-table {
            background: white;
            border-radius: 15px;
            overflow-x: auto;
            /* Ensures horizontal scroll on small screens */
            box-shadow: var(--card-shadow);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            /* Fixed table layout for better column control */
        }

        th,
        td {
            padding: 1rem;
            vertical-align: top;
        }

        th {
            background: var(--primary-color);
            color: white;
            text-align: left;
        }

        td {
            border-bottom: 1px solid #eee;
        }

        /* Set specific widths for columns */
        table th:nth-child(1),
        /* Name */
        table td:nth-child(1) {
            width: 15%;
        }

        table th:nth-child(2),
        /* Category */
        table td:nth-child(2) {
            width: 12%;
        }

        table th:nth-child(3),
        /* Price */
        table td:nth-child(3) {
            width: 12%;
        }

        table th:nth-child(4),
        /* Detail */
        table td:nth-child(4) {
            width: 30%;
        }

        table th:nth-child(5),
        /* Image */
        table td:nth-child(5) {
            width: 15%;
        }

        table th:nth-child(6),
        /* Action */
        table td:nth-child(6) {
            width: 16%;
        }

        /* Handle long text in detail column */
        td:nth-child(4) {
            max-width: 300px;
            white-space: normal;
            overflow: hidden;
            text-overflow: ellipsis;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            /* Show only 3 lines of text */
            -webkit-box-orient: vertical;
        }

        /* Ensure action buttons stay visible */
        td:last-child {
            min-width: 140px;
        }

        /* Make sure action buttons stack nicely on smaller screens */
        td:last-child .btn {
            margin: 0.25rem 0;
            white-space: nowrap;
        }

        tr:hover {
            background: #f8f9fa;
        }

        /* Image Preview */
        .product-image {
            max-width: 100px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        /* Alert Messages */
        .alert {
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1rem;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .nav-content {
                flex-direction: column;
                gap: 1rem;
                text-align: center;
            }

            .products-table {
                overflow-x: auto;
            }
        }
    </style>
</head>

<body>
    <nav>
        <div class="nav-content">
            <h1>Admin Dashboard - KaJaTu Store</h1>
            <div class="user-info">
                <span><i class="fas fa-user"></i> <?php echo htmlspecialchars($username); ?></span>
                <a href="../user/logout.php" class="btn btn-danger">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
        </div>
    </nav>

    <div class="container">
        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert alert-<?php echo $_SESSION['message_type']; ?>">
                <?php
                echo $_SESSION['message'];
                unset($_SESSION['message']);
                unset($_SESSION['message_type']);
                ?>
            </div>
        <?php endif; ?>

        <div class="product-form">
            <h2><?php echo $product_to_edit ? 'Edit Produk' : 'Tambah Produk Baru'; ?></h2>
            <form method="POST" action="" enctype="multipart/form-data">
                <?php if ($product_to_edit): ?>
                    <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($product_to_edit['id']); ?>">
                <?php endif; ?>

                <div class="form-group">
                    <label for="product_name">Nama Produk</label>
                    <input type="text" id="product_name" name="product_name"
                        value="<?php echo htmlspecialchars($product_to_edit ? $product_to_edit['name'] : ''); ?>" required>
                </div>

                <div class="form-group">
                    <label for="product_price">Harga Produk</label>
                    <input type="number" id="product_price" name="product_price"
                        value="<?php echo htmlspecialchars($product_to_edit ? $product_to_edit['price'] : ''); ?>" required>
                </div>

                <div class="form-group">
                    <label for="product_detail">Detail Produk</label>
                    <textarea id="product_detail" name="product_detail" rows="4"><?php echo htmlspecialchars($product_to_edit ? $product_to_edit['detail'] : ''); ?></textarea>
                </div>

                <div class="form-group">
                    <label for="product_category">Kategori</label>
                    <select id="product_category" name="product_category" required>
                        <option value="">Pilih Kategori</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?php echo htmlspecialchars($category); ?>"
                                <?php echo ($product_to_edit && $product_to_edit['category'] == $category) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($category); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="product_image">Gambar Produk</label>
                    <input type="file" id="product_image" name="product_image"
                        accept="image/jpeg,image/png,image/gif" <?php echo $product_to_edit ? '' : 'required'; ?>>
                    <?php if ($product_to_edit && $product_to_edit['image']): ?>
                        <div style="margin-top: 0.5rem;">
                            <img src="../images/<?php echo htmlspecialchars($product_to_edit['image']); ?>"
                                alt="Current product image" class="product-image">
                        </div>
                    <?php endif; ?>
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i>
                    <?php echo $product_to_edit ? 'Simpan Perubahan' : 'Tambah Produk'; ?>
                </button>
            </form>
        </div>

        <div class="products-table">
            <h2 style="padding: 1rem;">Daftar Produk</h2>
            <table>
                <thead>
                    <tr>
                        <th>Nama Produk</th>
                        <th>Kategori</th>
                        <th>Harga</th>
                        <th>Detail</th>
                        <th>Gambar</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($products && $products->num_rows > 0) {
                        while ($row = $products->fetch_assoc()) {
                            echo '<tr>';
                            echo '<td>' . htmlspecialchars($row['name']) . '</td>';
                            echo '<td>' . htmlspecialchars($row['category']) . '</td>';
                            echo '<td>Rp. ' . htmlspecialchars(number_format($row['price'], 0, ',', '.')) . '</td>';
                            echo '<td>' . htmlspecialchars($row['detail']) . '</td>';
                            echo '<td><img src="../images/' . htmlspecialchars($row['image']) . '" alt="' .
                                htmlspecialchars($row['name']) . '" class="product-image"></td>';

                            echo '<td>';
                            echo '<div style="display: flex; gap: 0.5rem;">';
                            echo '<a href="?edit=' . htmlspecialchars($row['id']) . '" class="btn btn-primary">';
                            echo '<i class="fas fa-edit"></i> Edit';
                            echo '</a>';

                            echo '<form method="POST" action="delete_product.php" style="display: inline;">';
                            echo '<input type="hidden" name="product_id" value="' . htmlspecialchars($row['id']) . '">';
                            echo '<button type="submit" class="btn btn-danger" onclick="return confirm(\'Apakah Anda yakin ingin menghapus produk ini?\');">';
                            echo '<i class="fas fa-trash-alt"></i> Delete';
                            echo '</button>';
                            echo '</form>';
                            echo '</div>';
                            echo '</td>';
                            echo '</tr>';
                        }
                    } else {
                        echo '<tr><td colspan="6" style="text-align: center;">Tidak ada produk yang tersedia.</td></tr>';
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <footer>
        <div class="footer-content">
            <p>&copy; <?php echo date('Y'); ?> KaosKita - Admin Dashboard</p>
        </div>
    </footer>

    <script>
        // Preview image before upload
        document.getElementById('product_image').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.createElement('img');
                    preview.src = e.target.result;
                    preview.className = 'product-image';
                    preview.style.marginTop = '0.5rem';

                    const container = document.getElementById('product_image').parentElement;
                    const oldPreview = container.querySelector('img');
                    if (oldPreview) {
                        container.removeChild(oldPreview);
                    }
                    container.appendChild(preview);
                }
                reader.readAsDataURL(file);
            }
        });

        // Auto-hide alerts after 5 seconds
        document.addEventListener('DOMContentLoaded', function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                setTimeout(() => {
                    alert.style.transition = 'opacity 0.5s ease';
                    alert.style.opacity = '0';
                    setTimeout(() => {
                        alert.remove();
                    }, 500);
                }, 5000);
            });
        });
    </script>
</body>

</html>