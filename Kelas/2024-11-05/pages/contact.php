<?php
$sql = "SELECT * FROM contact";

$hasil = mysqli_query($koneksi, $sql);

while ($row = mysqli_fetch_array($hasil)) {
?>
    <div class="contact">
        <h2><?= $row[1] ?></h2>
        <p><?= $row[2] ?></p>
    </div>
<?php
}
?>