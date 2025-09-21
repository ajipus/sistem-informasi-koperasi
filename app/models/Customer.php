<?php
// app/models/Customer.php
require_once __DIR__.'/BaseModel.php';

class Customer extends BaseModel {
  public function all($q = '') {
    if ($q) {
      $stmt = $this->pdo->prepare("SELECT * FROM customers WHERE nama_customer LIKE ? ORDER BY id_customer DESC");
      $stmt->execute(['%'.$q.'%']);
    } else {
      $stmt = $this->pdo->query("SELECT * FROM customers ORDER BY id_customer DESC");
    }
    return $stmt->fetchAll();
  }
  public function find($id) {
    $stmt = $this->pdo->prepare("SELECT * FROM customers WHERE id_customer = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
  }
  public function create($data) {
    $stmt = $this->pdo->prepare("INSERT INTO customers(nama_customer, alamat, telp, fax, email) VALUES(?,?,?,?,?)");
    return $stmt->execute([$data['nama_customer'], $data['alamat'], $data['telp'], $data['fax'], $data['email']]);
  }
  public function update($id, $data) {
    $stmt = $this->pdo->prepare("UPDATE customers SET nama_customer=?, alamat=?, telp=?, fax=?, email=? WHERE id_customer=?");
    return $stmt->execute([$data['nama_customer'], $data['alamat'], $data['telp'], $data['fax'], $data['email'], $id]);
  }
  public function delete($id) {
    $stmt = $this->pdo->prepare("DELETE FROM customers WHERE id_customer=?");
    return $stmt->execute([$id]);
  }
}
