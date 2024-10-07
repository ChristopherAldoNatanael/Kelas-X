<?php 

    require_once "koneksi.php";

    $data = stripslashes(file_get_contents("php://input"));
    $dataPelanggan = json_decode($data, true);
    
    $pelanggan = $dataPelanggan['pelanggan'];
    $alamat = $dataPelanggan['alamat'];
    $telp = $dataPelanggan['telp'];
    
    
    $sql = "INSERT INTO tblpelanggan (pelanggan, alamat, telp) VALUES ('$pelanggan', '$alamat', '$telp')";

    if (!empty($pelanggan) and !empty($alamat) and !empty($telp)) {
    

    if ($result = mysqli_query($con, $sql)) { 
        echo "Data Sudah Di Simpan !";
    } else {
        echo "Data Gagal Di Simpan"; 
    }

} else {
    echo "Data Kosong";
}
?>