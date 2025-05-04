<form action="nav.php" method="post">  <!-- action untuk memanggil file lain supaya saat login masuk ke file lain -->
    Email :
    <input type="email" name="email">
    Password :
    <input type="password" name="password">
    <input type="submit" name="kirim" value="Login">
</form>

<?php 

    if (isset($_POST['kirim'])){

        $email = $_POST['email'];
        $password = $_POST['password'];
    
        echo $email;
        echo '<br>';
        echo $password;
    }

   
?>