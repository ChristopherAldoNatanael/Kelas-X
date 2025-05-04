<h3>Insert Kategori</h3>
<div class="form-group">
    <form action="" method="post">
    <div class="form-group w-50">
        <label for="">Nama Kategori</label>
        <input type="text" name="kategori" required placeholder="Isi Kategori" class="form-control">
    </div>
    <div>
        <input type="submit" name="simpan" value="Simpan" class="btn btn-primary">

    </div>
  </form>
</div>

<?php 

    if (isset($_POST['simpan'])) {
        $kategori = $_POST['kategori'];

        $sql = "INSERT INTO tblkategori VALUES ('','$kategori')";
        echo $sql;

        $db->runSQL($sql);

        header("location:http://localhost/PHP%20SMK/RESTORAN/admin/?f=kategori&m=select");
    }

?>