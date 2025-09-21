<?php
// app/models/Transaction.php
require_once __DIR__.'/BaseModel.php';

class Transaction extends BaseModel {
  public function insertDetail($id_sales, $id_item, $qty, $price) {
    $stmt = $this->pdo->prepare("INSERT INTO transactions(id_sales, id_item, quantity, price) VALUES(?,?,?,?)");
    return $stmt->execute([$id_sales, $id_item, $qty, $price]);
  }
}
