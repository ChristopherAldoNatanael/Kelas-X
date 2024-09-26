<?php 

    class DB {
        private $host = "127.0.0.1";
        private $user = "root";
        private $password = "";
        private $database = "dbrestoran";
        private $koneksi;

        public function __construct(){
            $this->koneksi = $this->koneksiDB();
        }

        public function koneksiDB(){
            $koneksi = mysqli_connect($this->host, $this->user, $this->password, $this->database);
            return $koneksi;
        }

        public function getAll($sql){
            $result = mysqli_query($this->koneksi, $sql);
            
            while($row = mysqli_fetch_assoc($result)){
                $data[]=$row;
            }

            if (!empty($data)){
                return $result;
            }
            

        }

        public function getITEM($sql){
            $result = mysqli_query($this->koneksi, $sql);
            $row = mysqli_fetch_assoc($result);
            return $row;
        }
        
        public function rowCOUNT($sql){
            $result = mysqli_query($this->koneksi, $sql);
            $count = mysqli_num_rows($result);
            return $count;
        }

        public function runSQL($sql){
            $result = mysqli_query($this->koneksi, $sql);
        }

        public function pesan($text=""){
            echo $text;
        }

    }


    $db = new DB;

//    $db->runSQL("INSERT INTO tblkategori VALUES ('','MINUMAN')"); #untuk menambah data
   $db->runSQL("DELETE FROM tblkategori WHERE idkategori=2"); #untuk Menghapus

//    $db->pesan("berhasil");

    // var_dump($row);




?>