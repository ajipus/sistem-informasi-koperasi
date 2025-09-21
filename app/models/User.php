<?php
// app/models/User.php
require_once __DIR__.'/BaseModel.php';

class User extends BaseModel {
  public function findByUsername($username) {
    $stmt = $this->pdo->prepare("SELECT u.*, l.level_name FROM users u JOIN levels l ON l.id_level=u.id_level WHERE username=?");
    $stmt->execute([$username]);
    return $stmt->fetch();
  }
}
