<?php 

    session_start();
    require_once "../dbcontroller.php";
    $db = new DB;


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../bootstrap/css/bootstrap.css">
    <title>Login Restoran</title>
</head>
<body>
    <div class="container">
        <div class="row">
            <div class="col-4 mx-auto mt-4 ">
                <div>

                <h3>LOGIN RESTORAN</h3>

                </div>
                 <div class="form-group">
                     <form action="" method="post">
                         <div class="form-group">
                        <label for="">Email</label>
                        <input type="email" name="email" required class="form-control">
                        </div>

                        <div class="form-group">
                        <label for="">PASSSWORD</label>
                        <input type="password" name="password" required class="form-control">
                        </div>
                 <div>
                  <input type="submit" name="login" value="Login" class="btn btn-primary">
                </div>
               </form>
            </div>
         </div>
        </div>
    </div>
</body>
</html>

<?php 

    if (isset($_POST['login'])) {
        $email = $_POST['email'];
        $password = hash('sha256',$_POST['password']) ;

        $sql = "SELECT * FROM tbluser WHERE email = '$email' AND PASSWORD='$password'";

        $count = $db->rowCOUNT($sql);

        if ($count == 0) {
            echo "<center><h3>EMAIL atau PASSWORD Salah</h3></center>";
        }else {
            $sql = "SELECT * FROM tbluser WHERE email = '$email' AND PASSWORD='$password'";
            $row = $db->getITEM($sql);

            $_SESSION['user']=$row['email'];
            $_SESSION['level']=$row['level'];
            $_SESSION['iduser']=$row['iduser'];
            
            header("location:index.php");
        }

       
    }

?>