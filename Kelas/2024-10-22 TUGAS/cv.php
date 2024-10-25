<?php

$nama_pendidikan = ["Christopher Aldo Natanael", "Siswa"];

$keterangan = "Siswa SMKN 2 Buduran Di jurusan Rekayasa </br>  Perangkat Lunak. Seorang yang cekatan, sigap,</br> dapat dan diandalkan,mampu bekerja dalam tim </br> dan dapat berkomunikasi dengan baik.";

$skill = ["Front-end Development", "Python Newbie", "Data Scientist"];

$keahlian = "KEAHLIAN";

$kontak = "KONTAK";

$kontaks = ["+62857-3127-9959", "Perum TAS III Wonoayu, Kepuh Kemiri", "https://github.com/ChristopherAldoNatanael"];

$dataspribadi = "DATA PRIBADI";

$datapribadi = ["Tempat,Tanggal Lahir", "Sidoarjo,25 Desember 2008", "Alamat", "Perum TAS III Wonoayu, Kepuh Kemiri", "Nomor Telephone", "+62857-3127-9959", "Jenis Kelamin", "Laki-Laki", "Agama", "Kristen", "Kewarga Negaraan", "Indonesia", "Email", "christopheraldo26@gmail.com", "Status", "Belum Menikah"];

$judul_pendidikan = "PENDIDIKAN";

$pendidikan = ["SDK Yudeya Krian", "(2015-2021)", "SMPK Yudeya Krian", "(2021-2024)", "SMKN 2 Buduran", "(2024-2027)"];

$hobi = "HOBI";

$hobis = ["Bulutangkis", "Belajar Coding", "Renang", "Hiking"];

$projek = "PROJECT";

$projeks = ["Membuat Website Untuk PKN", "Membuat Website Untuk UTS", "Membuat Grafik Menggunakan Python"]

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="style.css">
  <link rel="stylesheet" href="https://unpkg.com/aos@next/dist/aos.css" />
  <link rel="icon" href="image/logo.png">
  <title>Belajar Bikin CV</title>
</head>

<body>
  <div class="konten">
    <div class="bagian-kiri" data-aos="fade-right">
      <div class="image">
        <img src="image/foto.jpeg " width="300px" alt="">
      </div>
      <div class="judul">
        <h1><?php echo $nama_pendidikan[0]; ?></h1>
        <h2><i><?php echo $nama_pendidikan[1]; ?></i></h2>
        <div class="keterangan">
          <p><?php echo $keterangan; ?></p>
        </div>
      </div>
      <div class="keahlian">
        <h1><?php echo $keahlian; ?></h1>
        <ul>
          <li><?php echo $skill[0] ?></li>
          <li><?php echo $skill[1] ?></li>
          <li><?php echo $skill[2] ?></li>
        </ul>
      </div>
      <div class="kontak">
        <h1><?php echo $kontak ?></h1>
        <div class="telepon">
          <span>☎</span>
          <p><?php echo $kontaks[0]; ?></p>
        </div>
        <div class="lokasi">
          <img src="image/lokasi.png" width="40px" height="45px" alt="">
          <p><?php echo $kontaks[1] ?></p>
        </div>
        <div class="website">
          <img src="image/github.png" width="40px" height="30px" alt="">
          <p><?php echo  $kontaks[2] ?></p>
        </div>
      </div>
    </div>
    <div class="bagian-kanan" data-aos="fade-left">
      <div class="isi-datapribadi">
        <h1><?php echo $dataspribadi ?></h1>
        <table>
          <tbody>
            <tr>
              <td>
                <ul>
                  <li><?php echo $datapribadi[0] ?> &nbsp;: </li>
                </ul>
              </td>
              <td><?php echo $datapribadi[1] ?></td>
            </tr>
            <tr>
              <td>
                <ul>
                  <li><?php echo $datapribadi[2] ?> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;: </li>
                </ul>
              </td>
              <td><?php echo $datapribadi[3] ?></td>
            </tr>
            <tr>
              <td>
                <ul>
                  <li><?php echo $datapribadi[4] ?> &nbsp; &nbsp; &nbsp; &nbsp; : </li>
                </ul>
              </td>
              <td><?php echo $datapribadi[5] ?></td>
            </tr>
            <tr>
              <td>
                <ul>
                  <li><?php echo $datapribadi[6] ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp; :</li>
                </ul>
              </td>
              <td><?php echo $datapribadi[7] ?></td>
            </tr>
            <tr>
              <td>
                <ul>
                  <li><?php echo $datapribadi[8] ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</li>
                </ul>
              </td>
              <td><?php echo $datapribadi[9] ?></td>
            </tr>
            <tr>
              <td>
                <ul>
                  <li><?php echo $datapribadi[10] ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</li>
                </ul>
              </td>
              <td><?php echo $datapribadi[11] ?></td>
            </tr>
            <tr>
              <td>
                <ul>
                  <li><?php echo $datapribadi[12] ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</li>
                </ul>
              </td>
              <td><?php echo $datapribadi[13] ?></td>
            </tr>
            <tr>
              <td>
                <ul>
                  <li><?php echo $datapribadi[14] ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</li>
                </ul>
              </td>
              <td><?php echo $datapribadi[15] ?></td>
            </tr>
          </tbody>
        </table>
      </div>
      <div class="pendidikan">
        <div class="isi-pendidikan">
          <h1><?php echo $judul_pendidikan ?></h1>
          <table>
            <tbody>
              <tr>
                <td>
                  <ul>
                    <li><?php echo $pendidikan[0] ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; :</li>
                  </ul>
                </td>
                <td><?php echo $pendidikan[1] ?></td>
              </tr>
              <tr>
                <td>
                  <ul>
                    <li><?php echo $pendidikan[2] ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</li>
                  </ul>
                </td>
                <td><?php echo $pendidikan[3] ?></td>
              </tr>
              <tr>
                <td>
                  <ul>
                    <li><?php echo $pendidikan[4] ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</li>
                  </ul>
                </td>
                <td><?php echo $pendidikan[5] ?></td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
      <div class="hobi">
        <div class="isi-hobi">
          <h1><?php echo $hobi ?></h1>
          <table>
            <tr>
              <td>
                <ul>
                  <li><?php echo $hobis[0] ?></li>
                </ul>
              </td>
            </tr>
            <tr>
              <td>
                <ul>
                  <li><?php echo $hobis[1] ?></li>
                </ul>
              </td>
            </tr>
            <tr>
              <td>
                <ul>
                  <li><?php echo $hobis[2] ?></li>
                </ul>
              </td>
            </tr>
            <tr>
              <td>
                <ul>
                  <li><?php echo $hobis[3] ?></li>
                </ul>
              </td>
            </tr>
          </table>
        </div>
      </div>
      <div class="projek">
        <div class="isi-projek">
          <h1><?php echo $projek ?></h1>
          <table>
            <tbody>
              <tr>
                <td>
                  <ul>
                    <li><?php echo $projeks[0] ?></li>
                  </ul>
                </td>
              </tr>
              <tr>
                <td>
                  <ul>
                    <li><?php echo $projeks[1] ?></li>
                  </ul>
                </td>
              </tr>
              <tr>
                <td>
                  <ul>
                    <li><?php echo $projeks[2] ?></li>
                  </ul>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
  <script src="https://unpkg.com/aos@next/dist/aos.js"></script>
  <script>
    AOS.init();
  </script>
</body>

</html>