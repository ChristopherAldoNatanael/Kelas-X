<h3>Registrasi Pelanggan</h3>
<div class="form-group">
    <form action="" method="post">
    <div class="form-group w-50">
        <label for="">Pelanggan</label>
        <input type="text" name="pelanggan" required placeholder="Isi Pelanggan" class="form-control">
    </div>

    <div class="form-group w-50">
        <label for="">Alamat</label>
        <input type="text" name="alamat" required placeholder="Isi Alamat" class="form-control">
    </div>

    <div class="form-group w-50">
        <label for="">Telp</label>
        <input type="text" name="telp" required placeholder="Isi Telp" class="form-control">
    </div>

    <div class="form-group w-50">
        <label for="">Email</label>
        <input type="text" name="email" required placeholder="Email" class="form-control">
    </div>

    <div class="form-group w-50">
        <label for="">Password</label>
        <input type="password" name="password" required placeholder="Password" class="form-control">
    </div>

    <div class="form-group w-50">
        <label for="">Konfirmasi Password</label>
        <input type="password" name="konfirmasi" required placeholder="Konfirmasi Password" class="form-control">
    </div>

    <div>
        <input type="submit" name="simpan" value="Simpan" class="btn btn-primary">

    </div>
  </form>
</div>

<?php 

    if (isset($_POST['simpan'])) {
        $pelanggan = $_POST['pelanggan'];
        $alamat = $_POST['alamat'];
        $telp = $_POST['telp'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        $konfirmasi = $_POST['konfirmasi'];

        if ($password === $konfirmasi) {
            $sql = "INSERT INTO tblpelanggan VALUES ('','$pelanggan','$alamat','$telp','$email','$password',1)";
            // echo $sql;
            $db->runSQL($sql);
             header("location:?f=home&m=info");
        }else {
            echo "<h2>PASSWORD TIDAK SAMA DENGAN KONFIRMASI</h2>";
        }
    }

?>