<?php
// app/views/reports/stock_index.php

// formatter uang & angka
if (!function_exists('fmt_money')) {
    function fmt_money($n) {
        return number_format((float)$n, 2, ',', '.');
    }
}
if (!function_exists('fmt_int')) {
    function fmt_int($n) {
        // qty: tampilkan 2 desimal juga biar konsisten
        return number_format((float)$n, 2, ',', '.');
    }
}

$d1 = $d1 ?? date('Y-m-01');
$d2 = $d2 ?? date('Y-m-d');
$q  = $q  ?? '';
$rows = $rows ?? [];
$sumQty   = $sumQty   ?? 0;
$sumOmzet = $sumOmzet ?? 0;
?>
<div class="d-flex align-items-center mb-3">
  <h4 class="mb-0">Laporan Stok Produk (Periode)</h4>
</div>

<form class="row g-2 mb-2" method="get">
  <input type="hidden" name="r" value="reports/stock">
  <div class="col-md-3">
    <label class="form-label">Dari</label>
    <input type="date" class="form-control" name="d1" value="<?= htmlspecialchars($d1) ?>">
  </div>
  <div class="col-md-3">
    <label class="form-label">Sampai</label>
    <input type="date" class="form-control" name="d2" value="<?= htmlspecialchars($d2) ?>">
  </div>
  <div class="col-md-4">
    <label class="form-label">Cari Item</label>
    <input type="text" class="form-control" name="q" placeholder="nama item..." value="<?= htmlspecialchars($q) ?>">
  </div>
  <div class="col-md-2 d-flex align-items-end gap-2">
    <button class="btn btn-primary" type="submit">Terapkan</button>
    <button class="btn btn-outline-secondary" type="button" onclick="window.print()">Cetak</button>
    <!-- optional export CSV bila sudah dibuat route-nya -->
    <!-- <a class="btn btn-dark" href="index.php?r=reports/stock&csv=1&d1=<?= urlencode($d1) ?>&d2=<?= urlencode($d2) ?>&q=<?= urlencode($q) ?>">Export CSV</a> -->
  </div>
</form>

<p class="text-muted">Periode: <strong><?= htmlspecialchars($d1) ?></strong> s/d <strong><?= htmlspecialchars($d2) ?></strong></p>

<div class="table-responsive">
  <table class="table table-bordered align-middle">
    <thead class="table-light">
      <tr>
        <th class="text-center" style="width:60px">#</th>
        <th>Nama Item</th>
        <th class="text-center" style="width:100px">UOM</th>
        <th class="text-end" style="width:140px">Harga Jual</th>
        <th class="text-end" style="width:140px">Qty Terjual</th>
        <th class="text-end" style="width:160px">Omzet</th>
      </tr>
    </thead>
    <tbody>
      <?php if (empty($rows)): ?>
        <tr><td colspan="6" class="text-center text-muted">Tidak ada data</td></tr>
      <?php else: ?>
        <?php $no=1; foreach ($rows as $r): 
          $nama  = $r['nama_item'] ?? '';
          $uom   = $r['uom'] ?? '';
          $harga = $r['harga_jual'] ?? 0;
          $qty   = $r['qty_jual'] ?? 0;  // <- kunci: pakai qty_jual (bukan qty_terjual)
          $omzet = $r['omzet'] ?? 0;
        ?>
        <tr>
          <td class="text-center"><?= $no++ ?></td>
          <td><?= htmlspecialchars($nama) ?></td>
          <td class="text-center"><?= htmlspecialchars($uom) ?></td>
          <td class="text-end"><?= fmt_money($harga) ?></td>
          <td class="text-end"><?= fmt_int($qty) ?></td>
          <td class="text-end"><?= fmt_money($omzet) ?></td>
        </tr>
        <?php endforeach; ?>
        <tr class="table-light fw-bold">
          <td colspan="4" class="text-end">Total Periode</td>
          <td class="text-end"><?= fmt_int($sumQty) ?></td>
          <td class="text-end"><?= fmt_money($sumOmzet) ?></td>
        </tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>
