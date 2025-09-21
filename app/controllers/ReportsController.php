<?php
// app/controllers/ReportsController.php
require_once __DIR__ . '/BaseController.php';

class ReportsController extends BaseController
{
    /* ------------------------- small helpers ------------------------- */
    private function _get(string $key, $default = '')
    {
        if (!isset($_GET[$key])) return $default;
        $v = trim((string)$_GET[$key]);
        return ($v === '') ? $default : $v;
    }

    private function normDate(?string $v, string $default): string
    {
        $v = trim((string)$v);
        if ($v === '') return $default;
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $v)) return $v;
        if (preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $v)) {
            [$d,$m,$y] = explode('/', $v);
            return sprintf('%04d-%02d-%02d', $y, $m, $d);
        }
        return $default;
    }

    /* ------------------------- SALES (header) ------------------------ */
    public function sales()
{
    $pdo = $this->pdo;

    $d1 = $this->normDate($this->_get('d1', $this->_get('from')), date('Y-m-01'));
    $d2 = $this->normDate($this->_get('d2', $this->_get('to')),   date('Y-m-d'));

    // ---- introspeksi tabel sales ----
    $salesCols = $pdo->query("SHOW COLUMNS FROM `sales`")->fetchAll(PDO::FETCH_COLUMN);
    $hasS = fn($c) => in_array($c, $salesCols, true);

    $colDate   = $hasS('tanggal')   ? 'tanggal'   : ($hasS('tgl') ? 'tgl' : ($hasS('created_at') ? 'created_at' : null));
    $colDO     = $hasS('do_number') ? 'do_number' : ($hasS('no_do') ? 'no_do' : ($hasS('do') ? 'do' : null));
    $colStatus = $hasS('status')    ? 'status'    : null;
    $colCust   = 'id_customer';

    $whereDate = $colDate ? "DATE(s.`$colDate`) BETWEEN :d1 AND :d2" : "1=1";
    $orderBy   = $colDate ? "s.`$colDate`, s.`id_sales`" : "s.`id_sales`";

    // ---- introspeksi tabel transactions (detail) ----
    $trxCols = $pdo->query("SHOW COLUMNS FROM `transactions`")->fetchAll(PDO::FETCH_COLUMN);
    $hasT = fn($c) => in_array($c, $trxCols, true);

    // tambah 'quantity' dan kandidat lainnya
    $colQty   = $hasT('qty')        ? 'qty'
              : ($hasT('quantity')  ? 'quantity'
              : ($hasT('jumlah')    ? 'jumlah'
              : ($hasT('qty_item')  ? 'qty_item'
              : ($hasT('qty_jual')  ? 'qty_jual' : null))));

    $colPrice = $hasT('harga_jual') ? 'harga_jual'
              : ($hasT('harga')     ? 'harga'
              : ($hasT('price')     ? 'price'
              : ($hasT('unit_price')? 'unit_price'
              : ($hasT('harga_satuan') ? 'harga_satuan' : null))));

    // ekspresi aman (kalau tidak ketemu kolom → 0)
    $qtyExpr   = $colQty   ? "COALESCE(t.`$colQty`,0)" : "0";
    $priceExpr = $colPrice ? "COALESCE(t.`$colPrice`, i.`harga_jual`)" : "i.`harga_jual`";

    $sql = "
        SELECT
            s.`id_sales`,
            " . ($colDate ? "s.`$colDate`" : "NULL") . "   AS tanggal,
            " . ($colDO   ? "s.`$colDO`"   : "''")   . "   AS do_number,
            " . ($colStatus? "s.`$colStatus`" : "''") . " AS status,
            c.`nama_customer` AS customer,
            COALESCE(SUM($qtyExpr * $priceExpr),0) AS total
        FROM `sales` s
        LEFT JOIN `customers`   c ON c.`id_customer` = s.`$colCust`
        LEFT JOIN `transactions` t ON t.`id_sales`    = s.`id_sales`
        LEFT JOIN `items`       i ON i.`id_item`      = t.`id_item`
        WHERE $whereDate
        GROUP BY s.`id_sales`
        ORDER BY $orderBy
    ";

    $st = $pdo->prepare($sql);
    if ($colDate) $st->execute([':d1'=>$d1, ':d2'=>$d2]); else $st->execute();
    $rows = $st->fetchAll(PDO::FETCH_ASSOC);

    // bila ingin nol-kan transaksi berstatus 'void'
    foreach ($rows as &$r) {
        if (isset($r['status']) && strtolower((string)$r['status']) === 'void') {
            $r['total'] = 0;
        }
    }

    $grand = 0;
    foreach ($rows as $r) $grand += (float)$r['total'];

    return $this->view('reports/sales_index', [
        'd1'=>$d1,'d2'=>$d2,'rows'=>$rows,'grand'=>$grand
    ]);
}

public function sales_detail()
{
    $pdo = $this->pdo;
    $id  = (int)$this->_get('id', 0);

    // header
    $hdr = $pdo->prepare("
        SELECT s.*, c.nama_customer
        FROM sales s
        LEFT JOIN customers c ON c.id_customer = s.id_customer
        WHERE s.id_sales = :id
        LIMIT 1
    ");
    $hdr->execute([':id'=>$id]);
    $header = $hdr->fetch(PDO::FETCH_ASSOC);

    // detail
    $trxCols = $pdo->query("SHOW COLUMNS FROM `transactions`")->fetchAll(PDO::FETCH_COLUMN);
    $hasT = fn($c) => in_array($c, $trxCols, true);

    $colQty   = $hasT('qty')        ? 'qty'
              : ($hasT('quantity')  ? 'quantity'
              : ($hasT('jumlah')    ? 'jumlah'
              : ($hasT('qty_item')  ? 'qty_item'
              : ($hasT('qty_jual')  ? 'qty_jual' : 'qty')))); // terakhir fallback 'qty' biar tetap ada

    $colPrice = $hasT('harga_jual') ? 'harga_jual'
              : ($hasT('harga')     ? 'harga'
              : ($hasT('price')     ? 'price'
              : ($hasT('unit_price')? 'unit_price'
              : ($hasT('harga_satuan') ? 'harga_satuan' : null))));

    $qtyExpr   = "COALESCE(t.`$colQty`,0)";
    $priceExpr = $colPrice ? "COALESCE(t.`$colPrice`, i.`harga_jual`)" : "i.`harga_jual`";

    $orderDetailCol = $hasT('id_transaction') ? 'id_transaction' : ( $hasT('id') ? 'id' : 'id_sales' );

    $sql = "
        SELECT
            i.nama_item,
            i.uom,
            $qtyExpr   AS qty,
            $priceExpr AS harga,
            ($qtyExpr * $priceExpr) AS subtotal
        FROM transactions t
        JOIN items i ON i.id_item = t.id_item
        WHERE t.id_sales = :id
        ORDER BY t.`$orderDetailCol`
    ";
    $st = $pdo->prepare($sql);
    $st->execute([':id'=>$id]);
    $rows = $st->fetchAll(PDO::FETCH_ASSOC);

    $grand = 0;
    foreach ($rows as $r) $grand += (float)$r['subtotal'];

    return $this->view('reports/sales_detail', [
        'header'=>$header, 'rows'=>$rows, 'grand'=>$grand
    ]);
}


    /* ------------------------- STOCK PER ITEM ----------------------- */
    public function stock()
    {
        $this->requireLevel(['admin','manager']);

        $pdo = $this->pdo;

        // util introspeksi
        $tableExists = function(string $table) use ($pdo): bool {
            $sql = "SELECT COUNT(*) FROM information_schema.TABLES
                    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = :t";
            $st = $pdo->prepare($sql);
            $st->execute([':t' => $table]);
            return (bool)$st->fetchColumn();
        };
        $columnExists = function(string $table, string $col) use ($pdo, $tableExists): bool {
            if (!$tableExists($table)) return false;
            $sql = "SELECT COUNT(*) FROM information_schema.COLUMNS
                    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = :t AND COLUMN_NAME = :c";
            $st = $pdo->prepare($sql);
            $st->execute([':t' => $table, ':c' => $col]);
            return (bool)$st->fetchColumn();
        };
        $pickTable = function(array $candidates) use ($tableExists): ?string {
            foreach ($candidates as $t) if ($tableExists($t)) return $t;
            return null;
        };
        $pickColumn = function(string $table, array $candidates) use ($columnExists): ?string {
            foreach ($candidates as $c) if ($columnExists($table, $c)) return $c;
            return null;
        };

        // input
        $d1 = $this->normDate($this->_get('d1'), date('Y-m-01'));
        $d2 = $this->normDate($this->_get('d2'), date('Y-m-d'));
        $q  = trim((string)$this->_get('q', ''));

        // items structure
        $itemsTable   = 'items';
        $itemsIdCol   = $pickColumn($itemsTable, ['id_item','item_id','id']) ?? 'id_item';
        $itemNameCol  = $pickColumn($itemsTable, ['nama_item','name','item_name']) ?? 'nama_item';
        $itemUomCol   = $pickColumn($itemsTable, ['uom','satuan','unit']) ?? 'uom';
        $itemPriceCol = $pickColumn($itemsTable, ['harga_jual','harga','price','unit_price','harga_satuan']);

        // detail structure
        $detailTable    = $pickTable(['transactions','sales_details','sale_details','transaction_detail','trans_detail','detail_transaksi']);
        $detailItemCol  = $detailTable ? ($pickColumn($detailTable, ['id_item','item_id']) ?? null) : null;
        $qtyCol         = $detailTable ? ($pickColumn($detailTable, ['qty','quantity','jumlah','qty_jual']) ?? null) : null;
        $priceCol       = $detailTable ? ($pickColumn($detailTable, ['harga_jual','harga','price','unit_price','harga_satuan']) ?? null) : null;
        $subtotalCol    = $detailTable ? ($pickColumn($detailTable, ['subtotal','sub_total','line_total','total_line']) ?? null) : null;
        $detailDateCol  = $detailTable ? ($pickColumn($detailTable, ['created_at','tanggal','tgl','date','waktu']) ?? null) : null;

        // JOIN & filter tanggal di JOIN (kalau ada kolom tanggal di detail)
        $join = '';
        $dateFilterInJoin = '';
        if ($detailTable && $detailItemCol) {
            if ($detailDateCol) {
                $dateFilterInJoin = " AND DATE(t.`{$detailDateCol}`) BETWEEN :d1 AND :d2";
            }
            $join = "LEFT JOIN {$detailTable} t
                     ON t.`{$detailItemCol}` = i.`{$itemsIdCol}` {$dateFilterInJoin}";
        }

        // harga yang ditampilkan
        if ($detailTable && $priceCol) {
            $priceExpr = "COALESCE(AVG(t.`{$priceCol}`), " . ($itemPriceCol ? "i.`{$itemPriceCol}`" : "0") . ", 0)";
        } else {
            $priceExpr = ($itemPriceCol ? "COALESCE(i.`{$itemPriceCol}`,0)" : "0");
        }

        // qty & omzet
        $qtyExpr = ($detailTable && $qtyCol) ? "COALESCE(SUM(t.`{$qtyCol}`),0)" : "0";

        if ($detailTable) {
            if ($subtotalCol) {
                $omzetExpr = "COALESCE(SUM(t.`{$subtotalCol}`),0)";
            } elseif ($qtyCol && $priceCol) {
                $omzetExpr = "COALESCE(SUM(t.`{$qtyCol}` * t.`{$priceCol}`),0)";
            } elseif ($qtyCol && $itemPriceCol) {
                $omzetExpr = "COALESCE(SUM(t.`{$qtyCol}` * i.`{$itemPriceCol}`),0)";
            } else {
                $omzetExpr = "0";
            }
        } else {
            $omzetExpr = "0";
        }

        // filter nama item
        $whereName = '';
        $param = [':d1' => $d1, ':d2' => $d2];
        if ($q !== '') {
            $whereName = " AND i.`{$itemNameCol}` LIKE :q ";
            $param[':q'] = "%{$q}%";
        }

        // query utama
        $sql = "SELECT  i.`{$itemsIdCol}` AS id_item,
                        i.`{$itemNameCol}` AS nama_item,
                        i.`{$itemUomCol}`  AS uom,
                        {$priceExpr}       AS harga_jual,
                        {$qtyExpr}         AS qty_jual,
                        {$omzetExpr}       AS omzet
                FROM {$itemsTable} i
                {$join}
                WHERE 1=1 {$whereName}
                GROUP BY i.`{$itemsIdCol}`, i.`{$itemNameCol}`, i.`{$itemUomCol}`
                ORDER BY i.`{$itemNameCol}` ASC";

        // kalau JOIN detail tidak pakai tanggal, hapus param d1/d2
        if (!$detailDateCol) { unset($param[':d1'], $param[':d2']); }

        $st = $pdo->prepare($sql);
        $st->execute($param);
        $rows = $st->fetchAll(PDO::FETCH_ASSOC);

        $sumQty = 0; $sumOmzet = 0;
        foreach ($rows as $r) { $sumQty += (float)$r['qty_jual']; $sumOmzet += (float)$r['omzet']; }

        return $this->view('reports/stock_index', [
            'rows'     => $rows,
            'd1'       => $d1,
            'd2'       => $d2,
            'q'        => $q,
            'sumQty'   => $sumQty,
            'sumOmzet' => $sumOmzet,
        ]);
    }
}
