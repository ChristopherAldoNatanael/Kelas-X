<?php 
    // OPERATOR MATEMATIKA

    $a = 2;
    $b = 2;
    $c = $a + $b;

    echo $c. '<br>';

    $c = $a - $b;
    echo $c. '<br>';

    $c = $a * $b;
    echo $c. '<br>';

    $c = $a / $b;
    echo floor($c). '<br>'; #Menggunakan floor berfungsi sebagai pembulatan ke bawah
    echo round($c). '<br>'; #Menggunakan round berfungsi untuk pembulatan ke atas

    $c = $a % $b;
    echo $c. '<br>';

    // OPERATOR LOGIKA
    $c = $a < $b;
    echo $c; #Jawaban Cuma True Atau False akan muncul 1 jika benar

    $c = $a > $b;
    echo $c;

    $c = $a == $b;
    echo $c;

    $c = $a != $b;
    echo $c. '<br>';

    // INCREMENT
    $a--;
    echo $a. '<br>';

    // OPERATOR STRING
    $kata = 'Sura';
    $kota = 'Baya';
    $hasil = $kata.$kota;
    $hasil .='KOTA PAHLAWAN';
    echo $hasil;

?>