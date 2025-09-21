<?php
require_once __DIR__.'/BaseController.php';
require_once __DIR__.'/../models/Sales.php';
require_once __DIR__.'/../models/TransactionTemp.php';
require_once __DIR__.'/../models/Transaction.php';
require_once __DIR__ . '/../models/AuditLog.php';

class SalesController extends BaseController {
  private function guardAny() { $this->requireLevel(['admin','manager','kasir']); }
  public function index() {
    $this->guardAny();
    $mdl = new Sales($this->pdo);
    $q = $_GET['q'] ?? '';
    $status = $_GET['status'] ?? '';
    $rows = $mdl->all($q, $status);
    $this->view(__DIR__ . '/../views/sales/index.php', compact('rows','q','status'));
  }
  public function start() {
    $this->guardAny();
    $tmp = new TransactionTemp($this->pdo);
    $itemsTmp = $tmp->all();
    $customers = $this->pdo->query("SELECT id_customer, nama_customer FROM customers ORDER BY nama_customer")->fetchAll();
    $items = $this->pdo->query("SELECT id_item, nama_item, uom, harga_jual FROM items WHERE is_active=1 ORDER BY nama_item")->fetchAll();
    $today = date('Y-m-d');
    $this->view(__DIR__ . '/../views/sales/start.php', compact('itemsTmp','customers','items','today'));
  }
  public function addtemp() {
    $this->guardAny();
    if ($this->isPost()) {
      $id_item = (int)($_POST['id_item'] ?? 0);
      $qty = (float)($_POST['quantity'] ?? 0);
      $price = (float)($_POST['price'] ?? 0);
      $remark = $_POST['remark'] ?? '';
      $tmp = new TransactionTemp($this->pdo);
      if ($id_item && $qty > 0 && $price >= 0) { $tmp->add($id_item, $qty, $price, $remark); }
    }
    $this->redirect('index.php?r=sales/start');
  }
  public function removetemp() {
    $this->guardAny();
    $id = (int)($_GET['id'] ?? 0);
    $tmp = new TransactionTemp($this->pdo);
    $tmp->remove($id);
    $this->redirect('index.php?r=sales/start');
  }
  public function post() {
    $this->guardAny();
    if (!$this->isPost()) { $this->redirect('index.php?r=sales/start'); }
    $tmp = new TransactionTemp($this->pdo);
    $itemsTmp = $tmp->all();
    if (empty($itemsTmp)) {
      if (session_status() !== PHP_SESSION_ACTIVE) session_start();
      $_SESSION['flash'] = "Keranjang kosong.";
      $this->redirect('index.php?r=sales/start');
    }
    $sales = new Sales($this->pdo);
    $trx = new Transaction($this->pdo);
    $this->pdo->beginTransaction();
    try {
      if (session_status() !== PHP_SESSION_ACTIVE) session_start();
      $data = [
        'tgl_sales'   => $_POST['tgl_sales'] ?? date('Y-m-d'),
        'id_customer' => (int)($_POST['id_customer'] ?? 0),
        'do_number'   => $_POST['do_number'] ?? '',
        'status'      => 'posted',
        'created_by'  => $_SESSION['user']['id']
      ];
      $id_sales = $sales->createHeader($data);
      foreach ($itemsTmp as $row) {
        $trx->insertDetail($id_sales, $row['id_item'], $row['quantity'], $row['price']);
      }
      $tmp->clear();
      $this->pdo->commit();
      $this->redirect('index.php?r=sales/show&id='.$id_sales);
    } catch (\Throwable $e) {
      $this->pdo->rollBack();
      if (session_status() !== PHP_SESSION_ACTIVE) session_start();
      $_SESSION['flash'] = "Gagal posting: " . $e->getMessage();
      $this->redirect('index.php?r=sales/start');
    }
  }
  public function show() {
    $this->guardAny();
    $id = (int)($_GET['id'] ?? 0);
    $mdl = new Sales($this->pdo);
    $header = $mdl->find($id);
    $details = $mdl->details($id);
    $this->view(__DIR__ . '/../views/sales/show.php', compact('header','details'));
  }
  public function print() {
    $this->guardAny();
    $id = (int)($_GET['id'] ?? 0);
    $mdl = new Sales($this->pdo);
    $header = $mdl->find($id);
    if (!$header) { http_response_code(404); echo "Sales not found"; exit; }
    $details = $mdl->details($id);
    $company = $this->pdo->query("SELECT * FROM company_identity LIMIT 1")->fetch();
    $this->view(__DIR__.'/../views/sales/print.php', compact('header','details','company'));
  }
   function void() {
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

  public function pdf() {
  $this->requireLevel(['admin','manager','kasir']);

  $id = (int)($_GET['id'] ?? 0);
  $mdl = new Sales($this->pdo);
  $header  = $mdl->find($id);
  if (!$header) { http_response_code(404); echo "Not found"; exit; }
  $details = $mdl->details($id);
  $company = $this->pdo->query("SELECT * FROM company_identity LIMIT 1")->fetch();

  ob_start();
  $is_pdf = true;  
  include __DIR__.'/../views/sales/print.php';
  $html = ob_get_clean();

  require_once __DIR__.'/../libs/pdf.php';

  // PILIH MODE (anti “IDM” paling aman: embed atau save)
  $mode = 'embed';                  // default: tampilkan via data:URI (tidak streaming PDF)
  if (!empty($_GET['save'])) $mode = 'save';
  if (!empty($_GET['dl']))   $mode = 'download';
  if (!empty($_GET['embed']))$mode = 'embed';

  render_pdf($html, 'invoice-'.$id.'.pdf', 'A4', 'portrait', $mode);
} 
}