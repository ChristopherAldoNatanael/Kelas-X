<?php 

    function belajar (){
        echo "Saya Belajar PHP";
    }

    function luasPersegi( $p = 10, $l = 10 ){
        $luas = $p * $l;

        echo $luas;
    }

    function luas($p = 10, $l = 10){
        $luas = $p * $l;

        return $luas;
    }
    function output(){
        return"Belajar Function";
    }

    echo '<h1>'.output() .'</h1>';
    echo luas(100,3) * 4;
    
?>