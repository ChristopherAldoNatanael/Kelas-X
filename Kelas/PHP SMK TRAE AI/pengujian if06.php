<?php 
    $tanggal = 34;

    $hasil = $tanggal > 0;

    // $hasil = $tanggal < 32;

    if ($tanggal < 32) {
       if ($tanggal > 0 ) {
        echo 'Benar';
       }else {
        echo 'Salah';
       }
    }else{
        echo 'Salah';
    }


    echo "<br>";




    $nilai = 100;
    // if ($nilai <=100) {
    //     if ($nilai >=0) {
    //         echo 'Nilai Benar';
    //     } else {
    //         echo 'Nilai Salah';
    //     }
    // }else {
    //     echo 'Nilai Salah';
    // }

    // echo '<br>';

    // if ($nilai >= 0 && $nilai <=100) {
    //     echo 'Nilai Benar';
    // }else {
    //     echo 'Nilai Salah';
    // }

    echo '<br>';

    if ($nilai >=100 || $nilai <=0) {
        echo 'Nilai Salah';
    }else {
        echo 'Nilai Benar';
    }

?>