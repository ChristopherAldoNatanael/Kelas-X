<?php 

    $nama = array('aldo','haqi','angga', 100);

    // var_dump($nama);

    // echo '<br>';

    // foreach ($nama as $key ) {
    //     echo $key. '<br>';
    // }

    $nama = array (
        "aldo" => "Sidoarjo",
        "haqi" => "Surabaya",
        "angga" => "Buduran",
    );

    var_dump($nama);
    echo "<br>";

    foreach ($nama as $a => $b) {
        echo $a. '-'. $b;
        echo '<br>';
    }

?>