<?php
// app/controllers/SalesController.php (void action only; merge with your current file if needed)
require_once __DIR__.'/BaseController.php';
require_once __DIR__.'/../models/AuditLog.php';

class SalesController extends BaseController {
  public function void() {
    $this->requireLevel(['admin','manager']);
    $id = (int)($_GET['id'] ?? 0);
    if (!$id) { header('Location: index.php?r=sales/index'); exit; }

    if ($this->isPost()) {
      if (session_status() !== PHP_SESSION_ACTIVE) session_start();
      $reason = trim($_POST['reason'] ?? '');
      if ($reason === '') { $_SESSION['flash'] = 'Alasan wajib diisi'; header('Location: index.php?r=sales/void&id='.$id); exit; }
      $stmt = $this->pdo->prepare("UPDATE sales SET status='void', void_reason=?, void_by=?, void_at=NOW() WHERE id_sales=? AND status='posted'");
      $stmt->execute([$reason, $_SESSION['user']['id'], $id]);
      // Audit
      $log = new AuditLog($this->pdo);
      $log->log('void', 'sales', $id, $reason, $_SESSION['user']['id']);
      header('Location: index.php?r=sales/show&id='.$id); exit;
    }

    $this->view(__DIR__.'/../views/sales/void.php');
  }
}
