<?php 

$judul_logo = ["SMP Katolik","St Yustinus De Yacobis Krian"];

$navigasi = ["Beranda","Sejarah","Visi-misi","Staf Pengajar","Karyawan","Sarana-Prasarana","Ekstrakulikuler","Yudeya Magz","Berita","Tentang"];

$navigasi2 = ["Pendaftaran","PPDB Kelas 7 </br> 2024-2025","Transfer/Pindahan</br> Siswa"];

$konten = ["Sarana-Prasarana"];
?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH"
        crossorigin="anonymous">
    <link rel="stylesheet" href="../style.css">
    <link rel="icon" href="../images/LOGOSMP.png">
    <link rel="stylesheet" href="https://unpkg.com/aos@next/dist/aos.css" />
    <title>Sarana-Prasarana - SMPK YUSTINUS DE YACOBIS KRIAN</title>
    </style>
</head>

<body>
    <div class="header">
        <div class="navigation">
            <nav class="navbar navbar-expand-lg bg-black h-auto" data-aos="fade-down">
                <!-- Left dots -->
                <div class="dot"></div>
                <div class="dot"></div>
                <!-- Gambar/logo di sebelah kiri -->
                <a class="navbar-brand d-flex align-items-center gambar" href="#">
                    <img src="../images/LOGOSMP.png" alt="Logo" width="40" height="40" class="d-inline-block align-text-top me-2">
                    <div class="logo">
                        <b>
                            <p class="text-light"><?php echo $judul_logo[0] ?></p>
                        </b>
                        <b>
                            <p class="text-light"><?php echo $judul_logo[1] ?></p>
                        </b>
                    </div>
                </a>

                <!-- Tombol toggler -->
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <!-- Navbar di sebelah kanan -->
                <div class="navigasi collapse navbar-collapse ms-auto" id="navbarSupportedContent">
                    <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                        <li class="nav-item">
                            <a class="nav-link" aria-current="page" href="../index.php"><?php echo $navigasi[0] ?></a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <?php echo $navigasi[9] ?>
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="sejarah.php"><?php echo $navigasi[1] ?></a></li>
                                <li><a class="dropdown-item" href="visi-misi.php"><?php echo $navigasi[2] ?></a></li>
                                <li><a class="dropdown-item" href="stafpengajar.php"><?php echo $navigasi[3] ?></a></li>
                                <li><a class="dropdown-item" href="karyawan.php"><?php echo $navigasi[4] ?></a></li>
                                <li><a class="dropdown-item" href="sarana.php"><?php echo $navigasi[5] ?></a></li>
                                <li><a class="dropdown-item" href="ekstra.php"><?php echo $navigasi[6] ?></a></li>
                            </ul>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <?php echo $navigasi2[0] ?>
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#">   <?php echo $navigasi2[1] ?></a></li>
                                <li><a class="dropdown-item" href="#">   <?php echo $navigasi2[2] ?></a></li>
                            </ul>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" aria-current="page" href="https://online.fliphtml5.com/rcxap/zqcb/#p=1"><?php echo $navigasi[7] ?></a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" aria-current="page" href="#"><?php echo $navigasi[8] ?></a>
                        </li>
                    </ul>
                </div>
        </div>
        </nav>
    </div>
    <div class="gambar">
        <img src="../images/gambar1.png" width="1263px" height="700px" alt="">
        <h2><b><?php echo $konten[0] ?></b></h2>
        <button><a style="color: black;" href="../index.php"><?php echo $navigasi[0] ?></a></button>
    </div>
    <div class="content" data-aos="fade-right">
        <div class="kotak4">
        <a href="https://youtu.be/VS9CYhTSKgM?si=pfMd8gS4PQLwBUI2"><img src="../images/youtube.png" width="500px" height="500px" alt=""></a>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="https://unpkg.com/aos@next/dist/aos.js"></script>
    <script>
        AOS.init();
    </script>
</body>

</html>