<?php
// app/views/sales/show.php (dengan tombol PDF)
?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <h3>Detail Transaksi #<?php echo $header['id_sales']; ?></h3>
  <div>
    <a class="btn btn-outline-secondary" href="index.php?r=sales/index">Kembali</a>
    <a class="btn btn-primary" target="_blank" href="index.php?r=sales/print&id=<?php echo $header['id_sales']; ?>">Cetak</a>
    <!-- semula: ?r=sales/pdf&id=... -->
    <a class="btn btn-dark" href="index.php?r=sales/pdf&id=<?= $header['id_sales']; ?>&embed=1">PDF</a>
    <?php if(in_array($_SESSION['user']['level'] ?? '', ['admin','manager']) && $header['status']==='posted'): ?>
      <a class="btn btn-danger" href="index.php?r=sales/void&id=<?php echo $header['id_sales']; ?>">Void</a>
    <?php endif; ?>
  </div>
</div>

<?php if($header['status']==='void'): ?>
  <div class="alert alert-warning"><strong>Status:</strong> DIBATALKAN pada <?php echo htmlspecialchars($header['void_at']); ?> oleh ID <?php echo htmlspecialchars($header['void_by']); ?><br>
    <strong>Alasan:</strong> <?php echo nl2br(htmlspecialchars($header['void_reason'])); ?></div>
<?php endif; ?>

<table class="table table-bordered">
  <tr><th style="width:180px;">Tanggal</th><td><?php echo htmlspecialchars($header['tgl_sales']); ?></td></tr>
  <tr><th>Customer</th><td><?php echo htmlspecialchars($header['nama_customer']); ?></td></tr>
  <tr><th>DO Number</th><td><?php echo htmlspecialchars($header['do_number']); ?></td></tr>
  <tr><th>Status</th><td><?php echo htmlspecialchars($header['status']); ?></td></tr>
</table>

<table class="table table-striped">
  <thead><tr><th>#</th><th>Item</th><th class="text-end">Qty</th><th>UOM</th><th class="text-end">Harga</th><th class="text-end">Jumlah</th></tr></thead>
  <tbody>
  <?php $grand=0; foreach($details as $i=>$d): $grand += $d['amount']; ?>
    <tr>
      <td><?php echo $i+1; ?></td>
      <td><?php echo htmlspecialchars($d['nama_item']); ?></td>
      <td class="text-end"><?php echo number_format($d['quantity'],2,',','.'); ?></td>
      <td><?php echo htmlspecialchars($d['uom']); ?></td>
      <td class="text-end"><?php echo number_format($d['price'],2,',','.'); ?></td>
      <td class="text-end"><?php echo number_format($d['amount'],2,',','.'); ?></td>
    </tr>
  <?php endforeach; ?>
  </tbody>
  <tfoot><tr><th colspan="5" class="text-end">Grand Total</th><th class="text-end"><?php echo number_format($grand,2,',','.'); ?></th></tr></tfoot>
</table>
