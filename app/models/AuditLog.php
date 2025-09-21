<?php
// app/models/AuditLog.php
class AuditLog {
  private PDO $pdo;
  public function __construct(PDO $pdo) { $this->pdo = $pdo; }
  public function log($action, $entity, $entity_id, $description, $actor_id) {
    $stmt = $this->pdo->prepare("INSERT INTO audit_logs (action, entity, entity_id, description, actor_id) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$action, $entity, $entity_id, $description, $actor_id]);
  }
}
