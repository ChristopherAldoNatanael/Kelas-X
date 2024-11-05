<?php
$host = "127.0.0.1";
$user = "root";
$password = "";
$database = "sekolah";
$koneksi = mysqli_connect($host, $user, $password, $database);

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SMKN 2 Buduran</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="container">
        <div class="header">
            <div class="logo">
                <a href="index.php"> <img src="images/LOGO.jpeg" alt=""></a>
            </div>
            <div class="sekolah">
                <h2>SMKN 2 Buduran</h2>
            </div>
            <div class="nav">
                <ul>
                    <li><a href="?menu=home">Home</a></li>
                    <li><a href="?menu=jurusan">Jurusan</a></li>
                    <li><a href="?menu=jadwal">Jadwal</a></li>
                    <li><a href="?menu=contact">Contact</a></li>
                    <li><a href="?menu=sejarah">Sejarah</a></li>
                    <li><a href="?menu=tentang">Tentang</a></li>
                </ul>
            </div>
        </div>
        <div class="content">
            <?php
            if (isset($_GET["menu"])) {
                $menu = $_GET["menu"];
                // echo $menu;
                if ($menu == "home") {
                    require_once("pages/home.php");
                }
                if ($menu == "jurusan") {
                    require_once("pages/jurusan.php");
                }
                if ($menu == "jadwal") {
                    require_once("pages/jadwal.php");
                }
                if ($menu == "contact") {
                    require_once("pages/contact.php");
                }
                if ($menu == "sejarah") {
                    require_once("pages/sejarah.php");
                }
                if ($menu == "tentang") {
                    require_once("pages/tentang.php");
                }
            } else {
                require_once("pages/home.php");
            }
            ?>
        </div>
        <div class="footer">
            <p>Web Ini Dibuat Oleh : Christopher Aldo Natanael</p>
        </div>
    </div>
</body>

</html>