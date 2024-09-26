<?php 
    if(isset($_GET['id'])){
        $id = $_GET['id'];
        
        $sql = "SELECT * FROM tblorder WHERE idorder=$id";
        
        $row = $db->getITEM($sql);

    }

?>

<h3>Pembayaran Order</h3>
<div class="form-group">
    <form action="" method="post">
    <div class="form-group w-50">
        <label for="">Total</label>
        <input type="number" name="total" required value="<?php echo $row['total'] ?>" class="form-control">
    </div>

    <div class="form-group w-50">
        <label for="">Bayar</label>
        <input type="number" name="bayar" required class="form-control">
    </div>
    <div>
        <input type="submit" name="simpan" value="Bayar" class="btn btn-primary">

    </div>
  </form>
</div>

<?php 

    if (isset($_POST['simpan'])) {
        $bayar = $_POST['bayar'];

        $kembali = $bayar - $row['total'];

        $sql = "UPDATE tblorder SET bayar='$bayar', kembali = $kembali, status=1 WHERE idorder=$id";

        if ($kembali < 0) {
            echo "<h3>Pembayaran Kurang</h3>";
        }else {
            
        $db->runSQL($sql);

        header("location:?f=order&m=select");
        }

    }

?>