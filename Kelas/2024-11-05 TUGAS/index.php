<?php
$judul_logo = ["SMP Katolik", "St Yustinus De Yacobis Krian"];

$navigasi = ["Beranda", "Sejarah", "Visi-misi", "Staf Pengajar", "Karyawan", "Sarana-Prasarana", "Ekstrakulikuler", "Yudeya Magz", "Berita", "Tentang"];

$navigasi2 = ["Pendaftaran", "PPDB Kelas 7 </br> 2024-2025", "Transfer/Pindahan</br> Siswa"];

$konten = ["Sejarah", "SMP Katolik Santo Yustinus de Yacobis di Krian, Sidoarjo, merupakan lembaga pendidikan Katolik yang berdiri dengan tujuan menyediakan pendidikan berkualitas yang berlandaskan nilai-nilai keagamaan Katolik. Sekolah ini didirikan sebagai bagian dari misi pendidikan dan sosial Gereja Katolik, khususnya untuk melayani anak-anak di sekitar wilayah Krian, Sidoarjo.", "Seiring dengan perkembangannya, SMP Katolik Santo Yustinus de Yacobis terus berupaya meningkatkan kualitas pendidikan, menyediakan fasilitas yang lebih baik, dan memperkuat program akademik dan ekstrakurikuler"];

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH"
        crossorigin="anonymous">
    <link rel="stylesheet" href="style.css">
    <link rel="icon" href="images/LOGOSMP.png">
    <link rel="stylesheet" href="https://unpkg.com/aos@next/dist/aos.css" />
    <title>SMPK YUSTINUS DE YACOBIS KRIAN</title>
</head>

<body>
    <div class="header">
        <!-- Navigation bar -->
        <nav class="navbar navbar-expand-lg bg-black" data-aos="fade-down">
            <a class="navbar-brand d-flex align-items-center" href="#">
                <img src="images/LOGOSMP.png" alt="Logo" width="40" height="40" class="me-2">
                <div class="logo">
                    <b>
                        <p class="text-light"><?php echo $judul_logo[0] ?></p>
                    </b>
                    <b>
                        <p class="text-light"><?php echo $judul_logo[1] ?></p>
                    </b>s
                </div>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse ms-auto" id="navbarSupportedContent">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php"><?php echo $navigasi[0] ?></a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <?php echo $navigasi[9] ?>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="pages/sejarah.php"><?php echo $navigasi[1] ?></a></li>
                            <li><a class="dropdown-item" href="pages/visi-misi.php"><?php echo $navigasi[2] ?></a></li>
                            <li><a class="dropdown-item" href="pages/stafpengajar.php"><?php echo $navigasi[3] ?></a></li>
                            <li><a class="dropdown-item" href="pages/karyawan.php"><?php echo $navigasi[4] ?></a></li>
                            <li><a class="dropdown-item" href="pages/sarana.php"><?php echo $navigasi[5] ?></a></li>
                            <li><a class="dropdown-item" href="pages/ekstra.php"><?php echo $navigasi[6] ?></a></li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Pendaftaran
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#"> <?php echo $navigasi2[1] ?></a></li>
                            <li><a class="dropdown-item" href="#"> <?php echo $navigasi2[2] ?></a></li>
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
        </nav>
    </div>
    <div id="carouselExampleControls" class="carousel slide" data-bs-ride="carousel">
        <div class="carousel-inner">
            <div class="carousel-item active">
                <img src="images/foto2.png" class="d-block" style="width: 80rem; height:100rem; opacity:0.8;" alt="First slide">
            </div>
            <div class="carousel-item">
                <img src="images/foto1.png" class="d-block" style="width: 80rem; height:100rem;  opacity:0.8;" alt="Second slide">
            </div>
            <div class="carousel-item">
                <img src="images/foto3.png" class="d-block" style="width: 80rem; height:100rem;  opacity:0.8;" alt="Third slide">
            </div>
        </div>

        <!-- Add navigation controls inside the carousel -->
        <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleControls" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden"></span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleControls" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden"></span>
        </button>
    </div>



    <div class="hubungi-kami" data-aos="fade-right">
        <img src="images/hubungi.png" width="1263" alt="Hubungi Kami">
    </div>
    <div class="smpkatolik" data-aos="fade-up">
        <h4>SMP KATOLIK YUSTINUS DE YACOBIS</h4>
        <div class="rumah">
            <div class="kotak1" data-aos="fade-up">
                <h2 style="font-size:28px; margin-top: 40px; margin-left:30px;"><b>Sejarah</b></h2>
                <p style="margin-left: 30px;">SMP Katolik Santo Yustinus De Yacobis</br> memiliki sejarah yang sangat menarik</p>
                <button style="color: white; background-color: rgb(82, 82, 253);"><a style="color: white;" href="pages/sejarah.php"><b>Baca</b></a></button>
            </div>
            <div class="kotak2" data-aos="fade-up">
                <h2 style="font-size:28px; margin-top: 40px; margin-left:30px;"><b>Visi-misi</b></h2>
                <p style="margin-left: 30px;">SMP Katolik Santo Yustinus De Yacobis</br>Mengembangkan pribadi yang beriman, cerdas, terampil, mencintai sesama, dan alam ciptaan-Nya.menginspirasi, membimbing, dan menciptakan lingkungan belajar yang positif</p>
                <button style="color: white; background-color: rgb(82, 82, 253);"><a style="color: white;" href="pages/visi-misi.php"><b>Baca</b></a></button>
            </div>
            <div class="kotak3" data-aos="fade-up">
                <h2 style="font-size:28px; margin-top: 40px; margin-left:30px;"><b>GURU</b></h2>
                <p style="margin-left: 30px;">Sekolah kami didukung oleh guru-guru profesional yang bekerja dan berkarya sesuai dengan bidangnya</p>
                <button style="color: white; background-color: rgb(82, 82, 253);"><a style="color: white;" href="pages/stafpengajar.php"><b>Baca</b></a></button>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="https://unpkg.com/aos@next/dist/aos.js"></script>
    <script>
        AOS.init();
    </script>
</body>

</html>