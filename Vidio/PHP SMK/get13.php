<h1>GET</h1>

<form action="" method="get">
    Nama : 
    <input type="text" name="nama">
    Alamat :
    <input type="text" name="alamat">

    <input type="submit" name="kirim" value="Simpan">

</form>

<?php 

if (isset($_GET['kirim'])) {
    $nama = $_GET['nama'];
    $alamat = $_GET['alamat'];

    echo $nama;
    echo '<br>';
    echo $alamat;
}
   
?>