<?php
// untuk mengecheck apakah sudah login jika belum akan di arahkan ke login
if (!isset($_SESSION["email"])) {
    header("location:index.php?menu=login");
}

//menghapus isi table
if (isset($_GET["hapus"])) {
    $id = $_GET["hapus"];
    unset($_SESSION["cart"][$id]);
}

// untuk mengecek isi session, jika session kosong akan kembali ke dalam page product
$cart = count($_SESSION["cart"]);
if ($cart == 0) {
    header("location:index.php");
}


// mengambil data dari database dan menyimpan ke dalam session
if (isset($_GET["add"])) {
    $id = $_GET["add"];
    $sql = "SELECT * FROM product  WHERE id = $id";
    // echo $sql;

    $hasil = mysqli_query($koneksi, $sql);
    $row = mysqli_fetch_assoc($hasil);
    echo $row["id"];
    echo $row["product"];
    echo $row["harga"];

    $_SESSION["cart"][$row["id"]] = [
        "id"      => $row["id"],
        "product" => $row["product"],
        "harga"   => $row["harga"],
        "jumlah"  => isset($_SESSION["cart"][$row["id"]]) ? $_SESSION["cart"][$row["id"]]["jumlah"] + 1 : 1
    ];
}
?>

<div class="cart">
    <h1>Cart</h1>
    <table border="1px">
        <thead>
            <tr>
                <th>No</th>
                <th>Product</th>
                <th>Harga</th>
                <th>Jumlah</th>
                <th>Total</th>
                <th>Hapus</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $no = 1;
            foreach ($_SESSION["cart"] as $key) {
            ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= $key["product"] ?></td>
                    <td><?= $key["harga"] ?></td>
                    <td><?= $key["jumlah"] ?></td>
                    <td><?= $key["jumlah"] * $key["harga"] ?></td>
                    <td><a href="?menu=cart&hapus=<?= $key["id"] ?>">Hapus</a></td>
                </tr>
            <?php
            }
            ?>
        </tbody>
    </table>
</div>