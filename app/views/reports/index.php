<h3>Laporan Penjualan (Periode)</h3>
<form class="row g-3 mb-3" method="get" action="index.php">
  <input type="hidden" name="r" value="reports/index">
  <div class="col-auto">
    <label class="form-label">Dari</label>
    <input type="date" class="form-control" name="from" value="<?php echo $from; ?>">
  </div>
  <div class="col-auto">
    <label class="form-label">Sampai</label>
    <input type="date" class="form-control" name="to" value="<?php echo $to; ?>">
  </div>
  <div class="col-auto">
    <label class="form-label">Mode</label>
    <select class="form-select" name="mode">
      <option value="header" <?php echo $mode==='header'?'selected':''; ?>>Daftar Transaksi</option>
      <option value="customer" <?php echo $mode==='customer'?'selected':''; ?>>Rekap per Customer</option>
      <option value="item" <?php echo $mode==='item'?'selected':''; ?>>Rekap per Item</option>
    </select>
  </div>
  <div class="col-auto align-self-end"><button class="btn btn-primary">Terapkan</button></div>
</form>

<table class="table table-striped">
  <thead><tr><th>#</th><th>Tanggal</th><th>Customer</th><th>DO</th><th>Status</th><th class="text-end">Total</th><th></th></tr></thead>
  <tbody>
    <?php $sum=0; foreach($rows as $r): $sum += $r['grand_total']; ?>
    <tr>
      <td><?php echo $r['id_sales']; ?></td>
      <td><?php echo htmlspecialchars($r['tgl_sales']); ?></td>
      <td><?php echo htmlspecialchars($r['nama_customer']); ?></td>
      <td><?php echo htmlspecialchars($r['do_number']); ?></td>
      <td><?php echo htmlspecialchars($r['status']); ?></td>
      <td class="text-end"><?php echo number_format($r['grand_total'],2,',','.'); ?></td>
      <td><a class="btn btn-sm btn-outline-secondary" href="index.php?r=sales/show&id=<?php echo $r['id_sales']; ?>">Detail</a></td>
    </tr>
    <?php endforeach; ?>
  </tbody>
  <tfoot><tr><th colspan="5" class="text-end">Total Periode</th><th class="text-end"><?php echo number_format($sum,2,',','.'); ?></th><th></th></tr></tfoot>
</table>
