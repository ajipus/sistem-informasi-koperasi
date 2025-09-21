<div class="d-flex justify-content-between align-items-center mb-3">
  <h3>Daftar Item</h3>
  <div class="btn-group">
    <a class="btn btn-success" href="index.php?r=items/exportcsv">Export CSV</a>
    <a class="btn btn-outline-success" href="index.php?r=items/importcsv">Import CSV</a>
    <a class="btn btn-primary" href="index.php?r=items/create">Tambah</a>
  </div>
</div>
<table class="table table-striped datatable">
  <thead><tr><th>#</th><th>Nama</th><th>UOM</th><th class="text-end">Harga Beli</th><th class="text-end">Harga Jual</th><th>Status</th><th>Aksi</th></tr></thead>
  <tbody>
  <?php foreach($items as $i): ?>
    <tr>
      <td><?php echo $i['id_item']; ?></td>
      <td><?php echo htmlspecialchars($i['nama_item']); ?></td>
      <td><?php echo htmlspecialchars($i['uom']); ?></td>
      <td class="text-end"><?php echo number_format($i['harga_beli'],2,',','.'); ?></td>
      <td class="text-end"><?php echo number_format($i['harga_jual'],2,',','.'); ?></td>
      <td><?php echo !empty($i['is_active']) ? 'Aktif' : 'Nonaktif'; ?></td>
      <td>
        <a class="btn btn-sm btn-outline-secondary" href="index.php?r=items/edit&id=<?php echo $i['id_item']; ?>">Edit</a>
        <a class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus item ini?')" href="index.php?r=items/delete&id=<?php echo $i['id_item']; ?>">Hapus</a>
      </td>
    </tr>
  <?php endforeach; ?>
  </tbody>
</table>
