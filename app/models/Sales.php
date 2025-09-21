<?php
// app/models/Sales.php
require_once __DIR__.'/BaseModel.php';

class Sales extends BaseModel {
  public function all($q = '', $status = '') {
    $sql = "SELECT s.*, c.nama_customer,
              COALESCE(SUM(t.amount),0) AS grand_total
            FROM sales s
            JOIN customers c ON c.id_customer = s.id_customer
            LEFT JOIN transactions t ON t.id_sales = s.id_sales";
    $where = [];
    $params = [];
    if ($q) { $where[] = "(c.nama_customer LIKE ? OR s.do_number LIKE ?)"; $params[] = "%$q%"; $params[] = "%$q%"; }
    if ($status) { $where[] = "s.status = ?"; $params[] = $status; }
    if ($where) { $sql .= " WHERE " . implode(" AND ", $where); }
    $sql .= " GROUP BY s.id_sales ORDER BY s.id_sales DESC";
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
  }

  public function find($id_sales) {
    $stmt = $this->pdo->prepare("SELECT s.*, c.nama_customer FROM sales s JOIN customers c ON c.id_customer=s.id_customer WHERE id_sales=?");
    $stmt->execute([$id_sales]);
    return $stmt->fetch();
  }

  public function createHeader($data) {
    $stmt = $this->pdo->prepare("INSERT INTO sales(tgl_sales, id_customer, do_number, status, created_by) VALUES(?,?,?,?,?)");
    $ok = $stmt->execute([$data['tgl_sales'], $data['id_customer'], $data['do_number'], $data['status'] ?? 'draft', $data['created_by']]);
    if ($ok) return $this->pdo->lastInsertId();
    return false;
  }

  public function setStatus($id_sales, $status) {
    $stmt = $this->pdo->prepare("UPDATE sales SET status=? WHERE id_sales=?");
    return $stmt->execute([$status, $id_sales]);
  }

  public function details($id_sales) {
    $stmt = $this->pdo->prepare("SELECT t.*, i.nama_item, i.uom FROM transactions t JOIN items i ON i.id_item=t.id_item WHERE t.id_sales=?");
    $stmt->execute([$id_sales]);
    return $stmt->fetchAll();
  }
}
