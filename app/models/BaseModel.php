<?php
// app/models/BaseModel.php
class BaseModel {
  protected PDO $pdo;
  public function __construct(PDO $pdo) { $this->pdo = $pdo; }
}
