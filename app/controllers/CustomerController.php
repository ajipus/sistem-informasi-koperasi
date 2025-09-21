<?php
// app/controllers/CustomerController.php (dengan import/export CSV)
require_once __DIR__.'/BaseController.php';

class CustomerController extends BaseController {
  public function index() {
    $this->requireLevel(['admin','manager']);
    $customers = $this->pdo->query("SELECT * FROM customers ORDER BY nama_customer")->fetchAll();
    $this->view(__DIR__.'/../views/customers/index.php', compact('customers'));
  }
  public function create() {
    $this->requireLevel(['admin','manager']);
    if ($this->isPost()) {
      $stmt = $this->pdo->prepare("INSERT INTO customers (nama_customer, alamat, telp, email) VALUES (?,?,?,?)");
      $stmt->execute([$_POST['nama_customer'] ?? '', $_POST['alamat'] ?? '', $_POST['telp'] ?? '', $_POST['email'] ?? '']);
      header('Location: index.php?r=customers/index'); exit;
    }
    $this->view(__DIR__.'/../views/customers/create.php');
  }
  public function edit() {
    $this->requireLevel(['admin','manager']);
    $id = (int)($_GET['id'] ?? 0);
    if ($this->isPost()) {
      $stmt = $this->pdo->prepare("UPDATE customers SET nama_customer=?, alamat=?, telp=?, email=? WHERE id_customer=?");
      $stmt->execute([$_POST['nama_customer'] ?? '', $_POST['alamat'] ?? '', $_POST['telp'] ?? '', $_POST['email'] ?? '', $id]);
      header('Location: index.php?r=customers/index'); exit;
    }
    $cust = $this->pdo->prepare("SELECT * FROM customers WHERE id_customer=?");
    $cust->execute([$id]);
    $cust = $cust->fetch();
    $this->view(__DIR__.'/../views/customers/edit.php', compact('cust'));
  }
  public function delete() {
    $this->requireLevel(['admin','manager']);
    $id = (int)($_GET['id'] ?? 0);
    $this->pdo->prepare("DELETE FROM customers WHERE id_customer=?")->execute([$id]);
    header('Location: index.php?r=customers/index'); exit;
  }

  public function exportcsv() {
    $this->requireLevel(['admin','manager']);
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="customers.csv"');
    $out = fopen('php://output','w');
    fputcsv($out, ['nama_customer','alamat','telp','email']);
    $stmt = $this->pdo->query("SELECT nama_customer, alamat, telp, email FROM customers ORDER BY nama_customer");
    while($r = $stmt->fetch(PDO::FETCH_ASSOC)) { fputcsv($out, $r); }
    fclose($out); exit;
  }

  public function importcsv() {
    $this->requireLevel(['admin','manager']);
    $info = '';
    if ($this->isPost() && isset($_FILES['csv']) && is_uploaded_file($_FILES['csv']['tmp_name'])) {
      $f = fopen($_FILES['csv']['tmp_name'], 'r');
      $header = fgetcsv($f);
      $map = [];
      foreach($header as $i=>$h) { $map[strtolower(trim($h))] = $i; }
      $need = ['nama_customer','alamat','telp','email'];
      foreach($need as $n) if(!isset($map[$n])) { $info = "Kolom wajib '$n' tidak ditemukan di header CSV."; fclose($f); $f=false; break; }
      if ($f) {
        $ins = $this->pdo->prepare("INSERT INTO customers (nama_customer, alamat, telp, email) VALUES (?, ?, ?, ?)");
        $n=0;
        while(($row = fgetcsv($f)) !== false) {
          $ins->execute([ $row[$map['nama_customer']] ?? '', $row[$map['alamat']] ?? '', $row[$map['telp']] ?? '', $row[$map['email']] ?? '' ]);
          $n++;
        }
        fclose($f);
        $info = "Import selesai: $n baris.";
      }
    }
    $this->view(__DIR__.'/../views/customers/import.php', compact('info'));
  }
}
