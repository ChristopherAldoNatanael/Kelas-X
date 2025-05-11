<?php
require_once 'config.php';
require_once 'Barang.php';

$database = new Database();
$db = $database->getConnection();
$barang = new Barang($db);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['create'])) {
        $barang->nama_barang = $_POST['nama_barang'];
        $barang->harga = $_POST['harga'];
        $barang->stok = $_POST['stok'];
        $barang->gambar = $_FILES['gambar']['name'];

        // Upload file
        move_uploaded_file($_FILES['gambar']['tmp_name'], 'uploads/' . $barang->gambar);

        if ($barang->create()) {
            echo "Barang berhasil ditambahkan.";
        }
    } elseif (isset($_POST['update'])) {
        $barang->id = $_POST['id'];
        $barang->nama_barang = $_POST['nama_barang'];
        $barang->harga = $_POST['harga'];
        $barang->stok = $_POST['stok'];
        $barang->gambar = $_FILES['gambar']['name'] ? $_FILES['gambar']['name'] : $_POST['existing_gambar'];

        // Upload file if new file is provided
        if ($_FILES['gambar']['name']) {
            move_uploaded_file($_FILES['gambar']['tmp_name'], 'uploads/' . $barang->gambar);
        }

        if ($barang->update()) {
            echo "Barang berhasil diperbarui.";
        }
    } elseif (isset($_POST['delete'])) {
        $barang->id = $_POST['id'];
        if ($barang->delete()) {
            echo "Barang berhasil dihapus.";
        }
    }
}

// Read all items
$stmt = $barang->read();
$barang_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRUD Barang</title>
    <style>
        /* Global Styles */
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f0f8ff;
            color: #333;
        }

        h1,
        h2 {
            text-align: center;
            color: #4CAF50;
            margin-bottom: 20px;
        }

        .container {
            max-width: 900px;
            margin: 50px auto;
            background: white;
            padding: 20px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            border-radius: 8px;
        }

        form {
            margin-bottom: 30px;
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        form input,
        form button {
            padding: 12px;
            font-size: 16px;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-sizing: border-box;
        }

        form input:focus {
            outline: none;
            border-color: #4CAF50;
            box-shadow: 0 0 5px rgba(76, 175, 80, 0.5);
        }

        form button {
            background-color: #4CAF50;
            color: white;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        form button:hover {
            background-color: #45a049;
        }

        form button:disabled {
            background-color: #ddd;
            cursor: not-allowed;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table th,
        table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: center;
            vertical-align: middle;
        }

        table th {
            background-color: #4CAF50;
            color: white;
            font-size: 16px;
        }

        table td img {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .actions {
            display: flex;
            justify-content: center;
            gap: 10px;
        }

        .actions button {
            border: none;
            color: white;
            padding: 8px 16px;
            /* Pastikan padding seragam */
            font-size: 14px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            width: 100%;
            /* Atur lebar tombol */
            max-width: 100px;
            /* Ukuran maksimal tombol */
            height: 50px;
            /* Samakan tinggi tombol */
        }

        /* Tombol Edit */
        .actions button:first-child {
            background-color: #007BFF;
            /* Warna biru untuk tombol Edit */
        }

        .actions button:first-child:hover {
            background-color: #0056b3;
            /* Warna biru gelap saat hover */
        }

        /* Tombol Hapus */
        .actions button:last-child {
            background-color: #FF4C4C;
            /* Warna merah untuk tombol Hapus */
        }

        .actions button:last-child:hover {
            background-color: #e60000;
            /* Warna merah gelap saat hover */
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>CRUD Barang</h1>
        <form action="" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="id" id="id">
            <input type="text" name="nama_barang" id="nama_barang" placeholder="Nama Barang" required>
            <input type="number" name="harga" id="harga" placeholder="Harga" required>
            <input type="number" name="stok" id="stok" placeholder="Stok" required>
            <input type="file" name="gambar" id="gambar">
            <input type="hidden" name="existing_gambar" id="existing_gambar">
            <button type="submit" name="create" id="create">Tambah Barang</button>
            <button type="submit" name="update" id="update" style="display:none;">Simpan Perubahan</button>
        </form>

        <h2>Data Barang</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nama Barang</th>
                    <th>Harga</th>
                    <th>Stok</th>
                    <th>Gambar</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($barang_list as $barang): ?>
                    <tr>
                        <td><?= $barang['id'] ?></td>
                        <td><?= $barang['nama_barang'] ?></td>
                        <td>Rp <?= number_format($barang['harga'], 0, ',', '.') ?></td>
                        <td><?= $barang['stok'] ?></td>
                        <td><img src="uploads/<?= $barang['gambar'] ?>" alt="Gambar Barang"></td>
                        <td class="actions">
                            <button type="button" onclick="editBarang(<?= htmlspecialchars(json_encode($barang)) ?>)" style="height: 50px;">Edit</button>
                            <form action="" method="POST" style="display:inline;">
                                <input type="hidden" name="id" value="<?= $barang['id'] ?>">
                                <button type="submit" name="delete" style="height: 50px;">Hapus</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <script>
        function editBarang(barang) {
            document.getElementById('id').value = barang.id;
            document.getElementById('nama_barang').value = barang.nama_barang;
            document.getElementById('harga').value = barang.harga;
            document.getElementById('stok').value = barang.stok;
            document.getElementById('existing_gambar').value = barang.gambar;

            document.getElementById('create').style.display = 'none';
            document.getElementById('update').style.display = 'block';
        }
    </script>
</body>

</html>