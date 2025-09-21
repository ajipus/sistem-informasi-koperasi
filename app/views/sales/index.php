<div class="d-flex justify-content-between align-items-center mb-3">
  <h3>Daftar Transaksi Sales</h3>
  <a class="btn btn-primary" href="index.php?r=sales/start">Buat Transaksi</a>
</div>
<table class="table table-striped datatable">
  <thead><tr><th>#</th><th>Tanggal</th><th>Customer</th><th>DO</th><th>Status</th><th class="text-end">Total</th><th>Aksi</th></tr></thead>
  <tbody>
  <?php foreach($rows as $r): ?>
    <tr>
      <td><?php echo $r['id_sales']; ?></td>
      <td><?php echo htmlspecialchars($r['tgl_sales']); ?></td>
      <td><?php echo htmlspecialchars($r['nama_customer']); ?></td>
      <td><?php echo htmlspecialchars($r['do_number']); ?></td>
      <td><?php echo htmlspecialchars($r['status']); ?></td>
      <td class="text-end"><?php echo number_format($r['grand_total'] ?? 0, 2, ',', '.'); ?></td>
      <td>
        <a class="btn btn-sm btn-outline-secondary" href="index.php?r=sales/show&id=<?php echo $r['id_sales']; ?>">Detail</a>
      </td>
    </tr>
  <?php endforeach; ?>
  </tbody>
</table>
