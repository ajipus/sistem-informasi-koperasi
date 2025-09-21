<?php
// app/models/TransactionTemp.php
require_once __DIR__.'/BaseModel.php';

class TransactionTemp extends BaseModel {
  private function sessionId() {
    if (session_status() !== PHP_SESSION_ACTIVE) session_start();
    if (empty($_SESSION['sid'])) $_SESSION['sid'] = session_id();
    return $_SESSION['sid'];
  }

  public function all() {
    $sid = $this->sessionId();
    $stmt = $this->pdo->prepare("SELECT tt.*, i.nama_item, i.uom FROM transaction_temp tt JOIN items i ON i.id_item=tt.id_item WHERE session_id=? ORDER BY id DESC");
    $stmt->execute([$sid]);
    return $stmt->fetchAll();
  }

  public function add($id_item, $qty, $price, $remark='') {
    $sid = $this->sessionId();
    $stmt = $this->pdo->prepare("INSERT INTO transaction_temp(session_id, id_item, quantity, price, remark) VALUES(?,?,?,?,?)");
    return $stmt->execute([$sid, $id_item, $qty, $price, $remark]);
  }

  public function remove($id) {
    $sid = $this->sessionId();
    $stmt = $this->pdo->prepare("DELETE FROM transaction_temp WHERE id=? AND session_id=?");
    return $stmt->execute([$id, $sid]);
  }

  public function clear() {
    $sid = $this->sessionId();
    $stmt = $this->pdo->prepare("DELETE FROM transaction_temp WHERE session_id=?");
    return $stmt->execute([$sid]);
  }
}
