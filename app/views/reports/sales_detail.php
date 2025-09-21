<?php
$hdr = $header ?? [];
$rows = $rows ?? [];
$grand = $grand ?? 0;
?>
<h4 class="mb-3">Detail Transaksi</h4>

<div class="card mb-3">
  <div class="card-body">
    <div class="row">
      <div class="col-md-6">
        <div><strong>Customer</strong>: <?= htmlspecialchars($hdr['nama_customer'] ?? '-') ?></div>
        <div><strong>Status</strong>: <?= htmlspecialchars($hdr['status'] ?? '-') ?></div>
      </div>
      <div class="col-md-6">
        <div><strong>Tanggal</strong>: <?= htmlspecialchars($hdr['tanggal'] ?? $hdr['tgl'] ?? $hdr['created_at'] ?? '-') ?></div>
        <div><strong>DO</strong>: <?= htmlspecialchars($hdr['do_number'] ?? $hdr['no_do'] ?? $hdr['do'] ?? '-') ?></div>
      </div>
    </div>
  </div>
</div>

<div class="table-responsive">
<table class="table table-bordered table-sm align-middle">
  <thead class="table-light">
    <tr>
      <th style="width:40px;">#</th>
      <th>Nama Item</th>
      <th style="width:100px;">UOM</th>
      <th class="text-end" style="width:120px;">Qty</th>
      <th class="text-end" style="width:140px;">Harga</th>
      <th class="text-end" style="width:160px;">Subtotal</th>
    </tr>
  </thead>
  <tbody>
    <?php if (!$rows): ?>
      <tr><td colspan="6" class="text-center text-muted">Tidak ada detail.</td></tr>
    <?php else: foreach ($rows as $i=>$r): ?>
      <tr>
        <td><?= $i+1 ?></td>
        <td><?= htmlspecialchars($r['nama_item']) ?></td>
        <td><?= htmlspecialchars($r['uom']) ?></td>
        <td class="text-end"><?= number_format((float)$r['qty'], 2, ',', '.') ?></td>
        <td class="text-end"><?= number_format((float)$r['harga'], 2, ',', '.') ?></td>
        <td class="text-end"><?= number_format((float)$r['subtotal'], 2, ',', '.') ?></td>
      </tr>
    <?php endforeach; endif; ?>
  </tbody>
  <tfoot>
    <tr>
      <th colspan="5" class="text-end">Grand Total</th>
      <th class="text-end"><?= number_format((float)$grand, 2, ',', '.') ?></th>
    </tr>
  </tfoot>
</table>
</div>

<a href="index.php?r=reports/sales" class="btn btn-secondary">Kembali</a>
