<?php 
    require_once "../function.php";

    // $id = 2;

    $sql = "DELETE FROM tblkategori WHERE idkategori = $id";

    $result = mysqli_query($koneksi, $sql);

    header("http://localhost/PHP%20SMK/RESTORAN/kategori/select.php")
?>