<div style="margin: auto; width:900px;">

<h2><a href="http://localhost/PHP%20SMK/RESTORAN/kategori/insert.php">TAMBAH DATA</a></h2>
<?php 

    require_once "../function.php";

    if (isset($_GET['update'])) {
        $id = $_GET['update'];
       require_once "update.php";
    }

    if (isset($_GET['hapus'])) {
        $id = $_GET['hapus'];
        require_once "delete.php";
    }

    echo '<br>';

    $sql = "SELECT idkategori FROM tblkategori";
    $result = mysqli_query($koneksi, $sql);

    $jumlahdata = mysqli_num_rows($result);

    $mulai = 3;
    $banyak = 3;

    $halaman = ceil($jumlahdata / $banyak);

    for ($i=1; $i <= $halaman ; $i++) {
        echo '<a href="?p='.$i.'">'.$i.'</a>';
        echo '&nbsp &nbsp &nbsp';
    }

    echo '<br> <br>';

    if (isset($_GET['p'])) {
        $p = $_GET['p'];
        $mulai = ($p * $banyak) - $banyak;
        //    3 = (2 * 3) - 3
    }else {
        $mulai =0;
    }

    $sql = "SELECT * FROM tblkategori LIMIT $mulai,$banyak";

    $result = mysqli_query($koneksi, $sql);

    // var_dump($result);

    $jumlah = mysqli_num_rows($result);
    // echo '<br>';
    // echo $jumlah;

    echo '
        <table border="1px">
    <tr>
        <th>No</th>
        <th>Kategori</th>
        <th>Hapus</th>
        <th>Ubah</th>
    </tr>
    ';
    $no=$mulai+1;
    if ($jumlah > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            echo '<tr>';
            echo '<td>' .$no++.'</td>';
            echo '<td>' .$row['kategori'].'</td>';
            echo '<td><a href="?hapus='.$row['idkategori'].'">' .'Hapus'.'</a></td>';
            echo '<td><a href="?update='.$row['idkategori'].'">' .'Update'.'</a></td>';
            echo '</tr>';
        }
    }

    echo '</table>';

?>


   
</div>

