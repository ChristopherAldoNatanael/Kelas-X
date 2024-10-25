<?php 

    $data = "Saya Belajar PHP Di SMKN 2 Buduran";

    $isi = "Variabel adalah wadah atau tempat untuk menyimpan data";

    $materi = "Materi Belajar PHP";

    $sekolahs = ["TK ANJAY", "SD ALOK","SMP ALOK", "SMK 2 Buduran"];

    $identitases = ["Christopher Aldo Natanael","Perum TAS III Kepuh Kemiri", "christopheraldo26@gmail.com", "christopher.aldoo"];

    $judul = "Curriculum Vitae";
    $hobbys = ["Renang","Billiard","Badminton"];
    $skills = ["HTML Expert", "CSS Expert", "PHP Newbie", "JavaScript Newbie"]; // TUGASSSSSSSSSSSS!!!

    $list1 = "Variabel";

    $list2 = "Array";

    $list3 = "Pengujian";

    $list4 = "Pengulangan";

    $list5 = "Function";

    $list6 = "Class";

    $list7 = "Object";

    $list8 = "Framework";

    $list9 = "PHP dan MySQL";

    $lists = [
    "Variabel",         //0
    "Array",            //1
    "Pengujian",        //2
    "Pengulangan",      //3
    "Function",         //4
    "Class",            //5
    "Object",           //6
    "Framework",        //7
    "PHP dan MySQL"     //8
    ];

    echo $data;    

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
        .kamar {
         text-align: center;
        }
        .list {
            display: flex;
            justify-content: center;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1><?php echo $judul; ?></h1>
    </div>
    <div class="identitas">
        <table>
            <thead>
                <tr>
                    <th>Identitas</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Nama</td>
                    <td><?php echo $identitases[0]; ?></td>
                </tr>
                <tr>
                    <td>Alamat</td>
                    <td><?php echo $identitases[1]; ?></td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="kamar">
        <h1><?php echo $data; ?></h1>
        <p><?php echo $isi; ?></p>
        <h2><?=$materi; ?></h2>
        <div class="list">
        <ol>
            <li><?php echo $lists[0] ?></li>
            <p>Variabel adalah wadah atau tempat untuk menyimpan data</p>
            <p>data bisa berupa text atau string, bisa juga berupa angka atau numerik, Data juga bisa gabungan antara Text,Angka,dan Simbol</p>
            <li><?php echo $lists[1] ?></li>
            <li><?php echo $lists[2] ?></li>
            <li><?php echo $lists[3] ?></li>
            <li><?php echo $lists[4] ?></li>
            <li><?php echo $lists[5] ?></li>
            <li><?php echo $lists[6] ?></li>
            <li><?php echo $lists[7] ?></li>
            <li><?php echo $lists[8] ?></li>
        </ol>
        </div>
    </div>
</body>
</html>