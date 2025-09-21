<?php function nf($n){ return number_format((float)$n, 2, ',', '.'); } ?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Cetak - Laporan Stok Produk</title>
  <style>
    @page{margin:18mm 14mm}
    body{font-family:DejaVu Sans, Arial, Helvetica, sans-serif; font-size:12px; color:#000;}
    h3{margin:0 0 10px 0}
    .meta{margin:0 0 12px 0; color:#444}
    .no-print{margin-bottom:12px}
    @media print{.no-print{display:none!important}}
    table{width:100%; border-collapse:collapse}
    th,td{border:1px solid #666; padding:6px; vertical-align:middle}
    th{background:#f2f2f2}
    .right{text-align:right}
  </style>
</head>
<body>
  <div class="no-print">
    <button onclick="window.print()">Cetak / Print</button>
    <a href="index.php?r=reports/stock&from=<?= urlencode($from) ?>&to=<?= urlencode($to) ?>&q=<?= urlencode($q) ?>">Kembali</a>
  </div>

  <h3>Laporan Stok Produk (Periode)</h3>
  <div class="meta">
    Periode: <b><?= htmlspecialchars($from) ?></b> s/d <b><?= htmlspecialchars($to) ?></b>
    <?php if (($q ?? '') !== ''): ?> · Filter: "<?= htmlspecialchars($q) ?>"<?php endif; ?>
  </div>

  <table>
    <thead>
      <tr>
        <th style="width:40px">#</th>
        <th>Nama Item</th>
        <th style="width:90px">UOM</th>
        <th class="right" style="width:120px">Harga Jual</th>
        <th class="right" style="width:120px">Qty Terjual</th>
        <th class="right" style="width:140px">Omzet</th>
      </tr>
    </thead>
    <tbody>
      <?php $no=1; foreach ($rows as $r): ?>
        <tr>
          <td><?= $no++ ?></td>
          <td><?= htmlspecialchars($r['nama_item']) ?></td>
          <td><?= htmlspecialchars($r['uom']) ?></td>
          <td class="right"><?= nf($r['harga_jual']) ?></td>
          <td class="right"><?= nf($r['qty_terjual']) ?></td>
          <td class="right"><?= nf($r['omzet']) ?></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
    <tfoot>
      <tr>
        <th colspan="4" class="right">Total Periode</th>
        <th class="right"><?= nf($tQty) ?></th>
        <th class="right"><?= nf($tOmzet) ?></th>
      </tr>
    </tfoot>
  </table>
</body>
</html>
