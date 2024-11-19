<div class="login">
    <h1>Log-In</h1>
    <form action="" method="post">
        <input type="email" name="email" required placeholder="Masukan Email!">
        <input type="password" name="password" required placeholder="Masukan Password!">
        <input type="submit" name="login" value="Login">
    </form>
</div>
<?php
if (isset($_POST["login"])) {
    $email = $_POST["email"];
    $password = $_POST["password"];

    $sql = "SELECT * FROM customer WHERE email = '$email' AND password = '$password'";

    $hasil = mysqli_query($koneksi, $sql);
    $baris = mysqli_num_rows($hasil);

    if ($baris == 0) {
        echo "<h2>Email Atau Password Salah!</h2>";
    } else {
        $_SESSION["email"] = $email;
        header("location:index.php");
    }
}

?>