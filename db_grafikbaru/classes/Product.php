<?php
class Product {
    private $conn;
    private $table = "products";

    public function __construct($db) {
        $this->conn = $db;
    }

    // READ ALL
    public function getAll() {
        return $this->conn->query("SELECT * FROM {$this->table} ORDER BY created_at DESC");
    }

    // GET BY ID
    public function getById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM {$this->table} WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    // CREATE
    public function store($data) {
        $stmt = $this->conn->prepare(
            "INSERT INTO {$this->table} (name, category, price, stock, description, image) 
             VALUES (?, ?, ?, ?, ?, ?)"
        );
        $stmt->bind_param("ssdiss", 
            $data['name'],
            $data['category'],
            $data['price'],
            $data['stock'],
            $data['description'],
            $data['image']
        );
        
        if ($stmt->execute()) {
            return ['status' => true, 'message' => 'Produk berhasil ditambahkan'];
        }
        return ['status' => false, 'message' => 'Gagal: ' . $this->conn->error];
    }

    // UPDATE
    public function update($id, $data) {
        $stmt = $this->conn->prepare(
            "UPDATE {$this->table} 
             SET name = ?, category = ?, price = ?, stock = ?, description = ?, image = ?
             WHERE id = ?"
        );
        $stmt->bind_param("ssdissi",
            $data['name'],
            $data['category'],
            $data['price'],
            $data['stock'],
            $data['description'],
            $data['image'],
            $id
        );
        
        if ($stmt->execute()) {
            return ['status' => true, 'message' => 'Produk berhasil diperbarui'];
        }
        return ['status' => false, 'message' => 'Gagal: ' . $this->conn->error];
    }

    // DELETE
    public function delete($id) {
        $stmt = $this->conn->prepare("DELETE FROM {$this->table} WHERE id = ?");
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            return ['status' => true, 'message' => 'Produk berhasil dihapus'];
        }
        return ['status' => false, 'message' => 'Gagal menghapus produk'];
    }
}
?>