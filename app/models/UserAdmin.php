<?php
// app/models/UserAdmin.php
require_once __DIR__.'/BaseModel.php';

class UserAdmin extends BaseModel {
  public function all() {
    $sql = "SELECT u.*, l.level_name FROM users u JOIN levels l ON l.id_level=u.id_level ORDER BY id_user DESC";
    return $this->pdo->query($sql)->fetchAll();
  }
  public function find($id) {
    $stmt = $this->pdo->prepare("SELECT * FROM users WHERE id_user=?");
    $stmt->execute([$id]);
    return $stmt->fetch();
  }
  public function create($data) {
    $stmt = $this->pdo->prepare("INSERT INTO users(nama_user, username, password_hash, id_level) VALUES(?,?,?,?)");
    $pass = password_hash($data['password'], PASSWORD_BCRYPT);
    return $stmt->execute([$data['nama_user'], $data['username'], $pass, $data['id_level']]);
  }
  public function update($id, $data) {
    if (!empty($data['password'])) {
      $stmt = $this->pdo->prepare("UPDATE users SET nama_user=?, username=?, password_hash=?, id_level=? WHERE id_user=?");
      $pass = password_hash($data['password'], PASSWORD_BCRYPT);
      return $stmt->execute([$data['nama_user'], $data['username'], $pass, $data['id_level'], $id]);
    } else {
      $stmt = $this->pdo->prepare("UPDATE users SET nama_user=?, username=?, id_level=? WHERE id_user=?");
      return $stmt->execute([$data['nama_user'], $data['username'], $data['id_level'], $id]);
    }
  }
  public function delete($id) {
    $stmt = $this->pdo->prepare("DELETE FROM users WHERE id_user=?");
    return $stmt->execute([$id]);
  }
  public function levels() {
    return $this->pdo->query("SELECT * FROM levels ORDER BY id_level")->fetchAll();
  }
}
