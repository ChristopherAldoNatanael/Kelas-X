<?php

$sekolah = [
    "TK Insan Cendekia", // Array Satu dimensi
    "SDK Yudeya",
    "SMPK Yudeya",
    "SMKN 2 Buduran"
];
$sekolahs = [
    "TK" => "TK Insan Cendekia", //array assosiatif
    "SD" => "SDK Yudeya",
    "SMP" => "SMPK Yudeya",
    "SMK" => "SMKN 2 Buduran",
    "PT" => "Universitas Negeri Surabaya"
];

$skills = [
    "C++" => "Expert",
    "HTML" => "Newbie",
    "CSS" => "Intermediate",
    "PHP" => "Intermediate",
    "Javascript" => "Intermediate"
];

$identitas = [
    "Nama" => "Christopher Aldo Natanael",
    "Alamat" => "Perum TAS III Wonoayu,Kepuh Kemiri",
    "Jenis Kelamin" => "Laki-laki",
    "Email" => "christopheraldo26@gmail.com",
    "Instagram" => "@christopher.aldoo"
];

$hobi = [
    "Coding",
    "Musik",
    "Badminton",
    "Mancing",
    "Renang",
    "Membaca"
];

// echo $sekolah[0];
// echo "<br>";
// echo $sekolahs["TK"];
// echo "<br>";
// echo $sekolah[1];
// echo "<br>";
// echo $sekolahs["SD"];

// //Menampilkan Semua

// echo "<br>";
// echo "<br>";

// for ($i = 0; $i < 4; $i++) {
//     echo $sekolah[$i];
//     echo "<br>";
// }
// echo "<br>";

// foreach ($sekolah as $key) {
//     echo $key;
//     echo "<br>";
// }

// echo "<br>";

// foreach ($sekolahs as $key => $value) {
//     echo $key;
//     echo "=";
//     echo $value;
//     echo "<br>";
// }

// echo "<br>";

// foreach ($skills as $key => $value) {
//     echo $key;
//     echo "=";
//     echo $value;
//     echo "<br>";
// }

if (isset($_GET["menu"])) {
    $menu = $_GET["menu"];
    echo $menu;
}



?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Belajar PHP</title>
</head>

<body>
    <hr>
    <ul>
        <li><a href="?menu=home">Home</a></li>
        <li><a href="?menu=cv">CV</a></li>
        <li><a href="?menu=project">Project</a></li>
        <li><a href="?menu=contact">Contact</a></li>
    </ul>
    <h2>Identitas</h2>
    <table border="1px">
        <thead>
            <tr>
                <th>Identitas</th>
                <th>Deskripsi</th>
            </tr>
        </thead>
        <tbody>
            <?php
            foreach ($identitas as $key => $value) {
            ?>
                <tr>
                    <td><?php echo $key ?></td>
                    <td><?php echo $value ?></td>
                </tr>
            <?php
            }

            ?>

        </tbody>
    </table>

    <hr>

    <h2>Riwayat Sekolah</h2>
    <table border="1px">
        <thead>
            <tr>
                <th>Jenjang</th>
                <th>Nama Sekolah</th>
            </tr>
        </thead>
        <tbody>
            <?php
            foreach ($sekolahs as $key => $value) {
                echo "<tr>";
                echo "<td>";
                echo $key;
                echo "</td>";
                echo "<td>";
                echo $value;
                echo "</td>";
                echo "</tr>";
            }
            ?>
        </tbody>
    </table>
    <hr>
    <h2>Skill</h2>
    <table border="1px">
        <thead>
            <tr>
                <th>Skill</th>
                <th>Level</th>
            </tr>
        </thead>
        <tbody>
            <?php
            foreach ($skills as $key => $value) {
            ?>
                <tr>
                    <td><?php echo $key ?></td>
                    <td><?php echo $value ?></td>
                </tr>
            <?php
            }
            ?>
        </tbody>
    </table>
    <hr>
    <h2>Hobi</h2>
    <ol>
        <?php
        foreach ($hobi as $key) {
        ?>
            <li><?php echo $key ?></li>
        <?php
        }

        ?>
    </ol>
</body>

</html>