<?php

// array dimensi

$nama = array("joni", "rudi", "budi", "sitimainah", 100, 2.5);

var_dump($nama);

echo '<br>';

echo $nama[5];

echo '<br>';

// for ($i = 0; $i < 6; $i++){
//     // echo $i
//     echo $nama[$i].'<br>'; 
// }

foreach ($nama as $k){
    echo $k.'<br>';
}

// array asosiatif

// $nama = array(
//     "joni" => "surabaya",
//     "rudi" => "malang",
//     "budi" => "jakarta",
//     "siti" => "bandung"
// );

$nama['joni'] = "malang";
$nama['sikam'] = "sidaorjo";
$nama['sigma'] = "surabaya";

var_dump($nama);

echo '<br>';

// echo $nama['budi'];

foreach ($nama as $key => $value){
    echo $key.' => '.$key;
    echo '<br>'; 
}
 
?>
php