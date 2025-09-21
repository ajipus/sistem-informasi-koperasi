<h3>Rekap Penjualan per Item</h3>
<?php include __DIR__.'/filter_partial.php'; ?>
<table class="table table-striped">
  <thead><tr><th>#</th><th>Item</th><th>UOM</th><th class="text-end">Qty</th><th class="text-end">Total</th></tr></thead>
  <tbody>
    <?php $sum=0; foreach($rows as $i=>$r): $sum += $r['total']; ?>
    <tr>
      <td><?php echo $i+1; ?></td>
      <td><?php echo htmlspecialchars($r['nama_item']); ?></td>
      <td><?php echo htmlspecialchars($r['uom']); ?></td>
      <td class="text-end"><?php echo number_format($r['qty'],2,',','.'); ?></td>
      <td class="text-end"><?php echo number_format($r['total'],2,',','.'); ?></td>
    </tr>
    <?php endforeach; ?>
  </tbody>
  <tfoot><tr><th colspan="4" class="text-end">Total Periode</th><th class="text-end"><?php echo number_format($sum,2,',','.'); ?></th></tr></tfoot>
</table>
