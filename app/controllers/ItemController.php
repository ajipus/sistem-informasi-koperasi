<?php
// app/controllers/ItemController.php (dengan import/export CSV)
require_once __DIR__.'/BaseController.php';

class ItemController extends BaseController {
  public function index() {
    $this->requireLevel(['admin','manager']);
    $items = $this->pdo->query("SELECT * FROM items ORDER BY nama_item")->fetchAll();
    $this->view(__DIR__.'/../views/items/index.php', compact('items'));
  }
  public function create() {
    $this->requireLevel(['admin','manager']);
    if ($this->isPost()) {
      $stmt = $this->pdo->prepare("INSERT INTO items (nama_item,uom,harga_beli,harga_jual,is_active) VALUES (?,?,?,?,?)");
      $stmt->execute([$_POST['nama_item'] ?? '', $_POST['uom'] ?? '', (float)($_POST['harga_beli'] ?? 0), (float)($_POST['harga_jual'] ?? 0), (int)($_POST['is_active'] ?? 1)]);
      header('Location: index.php?r=items/index'); exit;
    }
    $this->view(__DIR__.'/../views/items/create.php');
  }
  public function edit() {
    $this->requireLevel(['admin','manager']);
    $id = (int)($_GET['id'] ?? 0);
    if ($this->isPost()) {
      $stmt = $this->pdo->prepare("UPDATE items SET nama_item=?, uom=?, harga_beli=?, harga_jual=?, is_active=? WHERE id_item=?");
      $stmt->execute([$_POST['nama_item'] ?? '', $_POST['uom'] ?? '', (float)($_POST['harga_beli'] ?? 0), (float)($_POST['harga_jual'] ?? 0), (int)($_POST['is_active'] ?? 1), $id]);
      header('Location: index.php?r=items/index'); exit;
    }
    $item = $this->pdo->prepare("SELECT * FROM items WHERE id_item=?");
    $item->execute([$id]);
    $item = $item->fetch();
    $this->view(__DIR__.'/../views/items/edit.php', compact('item'));
  }
  public function delete() {
    $this->requireLevel(['admin','manager']);
    $id = (int)($_GET['id'] ?? 0);
    $this->pdo->prepare("DELETE FROM items WHERE id_item=?")->execute([$id]);
    header('Location: index.php?r=items/index'); exit;
  }

  public function exportcsv() {
    $this->requireLevel(['admin','manager']);
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="items.csv"');
    $out = fopen('php://output','w');
    fputcsv($out, ['nama_item','uom','harga_beli','harga_jual','is_active']);
    $stmt = $this->pdo->query("SELECT nama_item,uom,harga_beli,harga_jual,is_active FROM items ORDER BY nama_item");
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
      $need = ['nama_item','uom','harga_beli','harga_jual','is_active'];
      for($i=0;$i<count($need);$i++){ if(!isset($map[$need[$i]])){ $info = "Kolom wajib '".$need[$i]."' tidak ditemukan."; fclose($f); $f=false; break; } }
      if ($f) {
        $ins = $this->pdo->prepare("INSERT INTO items (nama_item,uom,harga_beli,harga_jual,is_active) VALUES (?,?,?,?,?)");
        $n=0;
        while(($row = fgetcsv($f)) !== false) {
          $ins->execute([
            $row[$map['nama_item']] ?? '',
            $row[$map['uom']] ?? '',
            (float)($row[$map['harga_beli']] ?? 0),
            (float)($row[$map['harga_jual']] ?? 0),
            (int)($row[$map['is_active']] ?? 1)
          ]);
          $n++;
        }
        fclose($f);
        $info = "Import selesai: $n baris.";
      }
    }
    $this->view(__DIR__.'/../views/items/import.php', compact('info'));
  }
}
