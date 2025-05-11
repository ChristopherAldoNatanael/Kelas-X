<?php 

    if (isset($_GET['id'])) {
        $id = $_GET['id'];

        $sql = "DELETE FROM tblkategori WHERE idkategori=$id";

        $db->runSQL($sql);

        header("location:http://localhost/PHP%20SMK/RESTORAN/admin/?f=kategori&m=select");

    }

?>