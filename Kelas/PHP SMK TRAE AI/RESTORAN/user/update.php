<?php 

if (isset($_GET['id'])){
  $id = $_GET['id'];
  $aktif = 1;
  
  $row = $db -> getITEM("SELECT * FROM tbluser WHERE iduser = $id");

  if ($row['aktif'] == 0){
    $aktif = 1;
  } else {
    $aktif = 0;
  }

  $sql = "UPDATE tbluser  SET aktif = $aktif WHERE iduser = $id";

  $db->runSQL($sql);

  // header("Location: ?f=user&m=select");
  echo '<script>window.location.href="?f=user&m=select";</script>';
}

?>