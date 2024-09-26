<?php 
    session_start();
    require_once "../dbcontroller.php";
    $db = new DB;

    if (!isset($_SESSION['user'])) {
        header("location:login.php");
    }

    if (isset($_GET['log'])) {
       session_destroy();
       header("location:index.php");
    }

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin PAGE | Aplikasi Restoran SMK</title>
    <link rel="stylesheet" href="../bootstrap/css/bootstrap.css">
</head>
<body>
    <div class="container">

    <div class="row mt-4">
    <div class="col-md-3">
        <a href="index.php"><h3>Admin Page</h3></a>
    </div>
    <div class="col-md-9">
        <div class="float-right mt-4"><a href="?log=logout">Logout</a></div>
        <div class="float-right mt-4 mr-4">User : <a href="?f=user&m=updateuser&id=<?php echo $_SESSION['iduser'] ?>"><?php echo $_SESSION['user'] ?></a></div>
        <div class="float-right mt-4 mr-5">Level : <?php echo $_SESSION['level'] ?></div>


    </div>
    </div>

    <div class="row mt-5">
        <div class="col-md-3">
            <ul class="nav flex-column">

                <?php 
                
                    $level = $_SESSION['level'];
                    switch ($level) {
                        case 'admin':
                            echo '
                            
                            <li class="nav-item"><a class="nav-link" href="?f=kategori&m=select">Kategori</a></li>
                            <li class="nav-item"><a class="nav-link" href="?f=menu&m=select">Menu</a></li>
                            <li class="nav-item"><a class="nav-link" href="?f=pelanggan&m=select">Pelanggan</a></li>
                            <li class="nav-item"><a class="nav-link" href="?f=order&m=select">Order</a></li>
                            <li class="nav-item"><a class="nav-link" href="?f=orderdetail&m=select">Order Detail</a></li>
                            <li class="nav-item"><a class="nav-link" href="?f=user&m=select">User</a></li>
                            
                            ';
                            break;

                            case 'kasir':
                                echo '
                                
                                 <li class="nav-item"><a class="nav-link" href="?f=order&m=select">Order</a></li>
                                 <li class="nav-item"><a class="nav-link" href="?f=orderdetail&m=select">Order Detail</a></li>
                                
                                ';
                                break;
                                case 'koki':
                                echo '   
                                 <li class="nav-item"><a class="nav-link" href="?f=orderdetail&m=select">Order Detail</a></li>
                                    ';
                                 break;
                        default:
                            echo "Tidak Ada Menu";  
                            break;
                    }
                
                ?>


                
            </ul>
        </div>
        <div class="col-md-9">
            <?php 
            
                if (isset($_GET['f']) && isset($_GET['m'])) {
                    $f = $_GET['f'];
                    $m = $_GET['m'];

                    $file = '../'.$f.'/'.$m.'.php';

                    require_once $file;
                }
            ?>
        </div>
    </div>

    <div class="row mt-5">
    <div class="col">
        <p class="text-center">2024 -copyright @ChristopherAldo</p>
    </div>
    </div>

    </div>
</body>
</html>