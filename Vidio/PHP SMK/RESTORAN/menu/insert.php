<?php 

    $row = $db->getALL("SELECT * FROM tblkategori ORDER BY kategori ASC");

?>

<h3>Insert Menu</h3>
<div class="form-group">
    <form action="" method="post" enctype="multipart/form-data">
        <div class="form-group w-50" >
            <label>Kategori</label>
        </div>
    
        <select name="idkategori" id="">
            <?php foreach($row as $r): ?>
            <option value="<?php echo $r['idkategori'] ?>"><?php echo $r['kategori'] ?></option>
            <?php endforeach ?>
        </select>
            
            <div class="form-group">
                <label>Nama Menu</label>
                <input type="text" name="menu" required placeholder="Isi menu" class="form-control w-50 mt-2">                      
            </div>
        
            <div class="form-group">
                <label>Harga:</label>
                <input type="text" name="harga" number required placeholder="Isi Harga" class="form-control w-50 mt-2">                      
            </div>
        
            <div class="form-group mt-5">
                <label>Gambar:</label>
                <br>
                <input type="file" name="gambar">                      
            </div>
        
            <div>
                <input type="submit" name="simpan" value="simpan" class="btn btn-primary mt-5">
            </div>
        </form>
        </div>

<?php 

    if (isset($_POST['simpan'])) {
        $idkategori = $_POST['idkategori'];
        $menu = $_POST['menu'];
        $harga = $_POST['harga'];
        $gambar = $_FILES['gambar']['name'];
        $temp = $_FILES['gambar']['tmp_name'];


        if (empty($gambar)) {
            echo "<h3>Gambar Kosong</h3>";
        }else {
            $sql = "INSERT INTO tblmenu VALUES ('',$idkategori,'$menu','$gambar',$harga)";
            move_uploaded_file($temp,'../upload/'.$gambar);
            $db->runSQL($sql);
            header("location:?f=menu&m=select");
        }
    }

?>