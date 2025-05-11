<?php 
    // $hari =3;

    // switch ($hari) {
    //     case 1:
    //         echo 'Minggu';
    //         break;
    //     case 2:
    //         echo 'Senin';
    //         break;
    //     case 3:
    //         echo 'Selasa';
    //         break;
    //         default:
    //         echo 'Hari Belum Di buat : ';
    //         break;
    // }

    $pilihan = 'simpan';
    switch ($pilihan) {
        case 'tambah';
        echo 'anda memilih tambah';
        break;
        case 'ubah';
        echo 'anda memilih Ubah';
        break;
        case 'hapus';
        echo 'anda memilih Hapus';
        break;
        default:
        echo 'pilihan belum ada';
        break;


    }

?>