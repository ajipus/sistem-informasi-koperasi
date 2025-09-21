<?php
// app/views/reports/sales_index.php

if (!function_exists('fmt_money')) {
    function fmt_money($n) { return number_format((float)$n, 2, ',', '.'); }
}
if (!function_exists('fmt_date')) {
    function fmt_date(?string $ts): string {
        if (!$ts) return '';
        // Jika ada jam -> d/m/Y H:i, jika hanya tanggal -> d/m/Y
        $t = strtotime($ts);
        if ($t === false) return htmlspecialchars($ts);
        $hasTime = strpos($ts, ':') !== false;
        return $hasTime ? date('d/m/Y H:i', $t) : date('d/m/Y', $t);
    }
}

$d1    = $d1   ?? date('Y-m-01');
$d2    = $d2   ?? date('Y-m-d');
$rows  = $rows ?? [];
$grand = $grand ?? 0;
?>
<div class="d-flex align-items-center mb-3">
  <h4 class="mb-0">Laporan Penjualan (Periode)</h4>
</div>

<form class="row g-2 mb-2" method="get">
  <input type="hidden" name="r" value="reports/sales">
  <div class="col-md-3">
    <label class="form-label">Dari</label>
    <input type="date" class="form-control" name="d1" value="<?= htmlspecialchars($d1) ?>">
  </div>
  <div class="col-md-3">
    <label class="form-label">Sampai</label>
    <input type="date" class="form-control" name="d2" value="<?= htmlspecialchars($d2) ?>">
  </div>
  <div class="col-md-3 d-flex align-items-end">
    <button class="btn btn-primary" type="submit">Terapkan</button>
  </div>
</form>

<p class="text-muted">
  Periode:
  <strong><?= htmlspecialchars($d1) ?></strong>
  s/d
  <strong><?= htmlspecialchars($d2) ?></strong>
</p>

<div class="table-responsive">
  <table class="table table-bordered align-middle">
    <thead class="table-light">
      <tr>
        <th class="text-center" style="width:60px">#</th>
        <th style="width:160px">Tanggal</th>
        <th>Customer</th>
        <th style="width:100px">DO</th>
        <th style="width:120px">Status</th>
        <th class="text-end" style="width:160px">Total</th>
        <th style="width:110px">Detail</th>
      </tr>
    </thead>
    <tbody>
      <?php if (empty($rows)): ?>
        <tr><td colspan="7" class="text-center text-muted">Tidak ada data</td></tr>
      <?php else: ?>
        <?php $no = 1; foreach ($rows as $r): ?>
          <?php
            // Ambil id untuk link detail (fallback id/id_sale jika id_sales tidak ada)
            $idSales = (int)($r['id_sales'] ?? $r['id'] ?? $r['id_sale'] ?? 0);
          ?>
          <tr>
            <td class="text-center"><?= $no++ ?></td>
            <td><?= fmt_date($r['tanggal'] ?? '') ?></td>
            <td><?= htmlspecialchars($r['customer'] ?? '') ?></td>
            <td><?= htmlspecialchars($r['do_number'] ?? '') ?></td>
            <td><?= htmlspecialchars($r['status'] ?? '') ?></td>
            <td class="text-end"><?= fmt_money($r['total'] ?? 0) ?></td>
            <td>
              <?php if ($idSales > 0): ?>
                <a class="btn btn-sm btn-outline-secondary"
                   href="index.php?r=reports/sales_detail&id=<?= $idSales ?>">
                  Detail
                </a>
              <?php else: ?>
                <span class="text-muted">-</span>
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
        <tr class="table-light fw-bold">
          <td colspan="5" class="text-end">Total Periode</td>
          <td class="text-end"><?= fmt_money($grand) ?></td>
          <td></td>
        </tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>
