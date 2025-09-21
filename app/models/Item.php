<?php
// app/models/Item.php
require_once __DIR__.'/BaseModel.php';

class Item extends BaseModel {
  public function all($q = '') {
    if ($q) {
      $stmt = $this->pdo->prepare("SELECT * FROM items WHERE nama_item LIKE ? ORDER BY id_item DESC");
      $stmt->execute(['%'.$q.'%']);
    } else {
      $stmt = $this->pdo->query("SELECT * FROM items ORDER BY id_item DESC");
    }
    return $stmt->fetchAll();
  }
  public function find($id) {
    $stmt = $this->pdo->prepare("SELECT * FROM items WHERE id_item = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
  }
  public function create($data) {
    $stmt = $this->pdo->prepare("INSERT INTO items(nama_item, uom, harga_beli, harga_jual, is_active) VALUES(?,?,?,?,?)");
    return $stmt->execute([$data['nama_item'], $data['uom'], $data['harga_beli'], $data['harga_jual'], 1]);
  }
  public function update($id, $data) {
    $stmt = $this->pdo->prepare("UPDATE items SET nama_item=?, uom=?, harga_beli=?, harga_jual=?, is_active=? WHERE id_item=?");
    return $stmt->execute([$data['nama_item'], $data['uom'], $data['harga_beli'], $data['harga_jual'], $data['is_active']??1, $id]);
  }
  public function delete($id) {
    $stmt = $this->pdo->prepare("DELETE FROM items WHERE id_item=?");
    return $stmt->execute([$id]);
  }
}
