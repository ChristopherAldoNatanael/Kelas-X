<?php
class Barang
{
    private $conn;
    private $table_name = "barang";

    public $id;
    public $nama_barang;
    public $harga;
    public $stok;
    public $gambar;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function create()
    {
        $query = "INSERT INTO " . $this->table_name . " (nama_barang, harga, stok, gambar) VALUES (:nama_barang, :harga, :stok, :gambar)";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":nama_barang", $this->nama_barang);
        $stmt->bindParam(":harga", $this->harga);
        $stmt->bindParam(":stok", $this->stok);
        $stmt->bindParam(":gambar", $this->gambar);

        return $stmt->execute();
    }

    public function read()
    {
        $query = "SELECT * FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // UPDATE
    public function update()
    {
        $query = "UPDATE " . $this->table_name . " SET nama_barang = :nama_barang, harga = :harga, stok = :stok, gambar = :gambar WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":id", $this->id);
        $stmt->bindParam(":nama_barang", $this->nama_barang);
        $stmt->bindParam(":harga", $this->harga);
        $stmt->bindParam(":stok", $this->stok);
        $stmt->bindParam(":gambar", $this->gambar);

        return $stmt->execute();
    }

    //DELETE
    public function delete()
    {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $this->id);

        if ($stmt->execute()) {
            $this->resetAutoIncrement();
            return true;
        }

        return false;
    }

    public function resetAutoIncrement()
    {
        $query = "SET @num := 0; 
                  UPDATE " . $this->table_name . " SET id = (@num := @num + 1);
                  ALTER TABLE " . $this->table_name . " AUTO_INCREMENT = 1;";

        $stmt = $this->conn->prepare($query);
        return $stmt->execute();
    }

    public function getById($id)
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
