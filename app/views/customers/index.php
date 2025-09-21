<div class="d-flex justify-content-between align-items-center mb-3">
  <h3>Daftar Customer</h3>
  <div class="btn-group">
    <a class="btn btn-success" href="index.php?r=customers/exportcsv">Export CSV</a>
    <a class="btn btn-outline-success" href="index.php?r=customers/importcsv">Import CSV</a>
    <a class="btn btn-primary" href="index.php?r=customers/create">Tambah</a>
  </div>
</div>
<table class="table table-striped datatable">
  <thead><tr><th>#</th><th>Nama</th><th>Alamat</th><th>Telepon</th><th>Email</th><th>Aksi</th></tr></thead>
  <tbody>
  <?php foreach($customers as $c): ?>
    <tr>
      <td><?php echo $c['id_customer']; ?></td>
      <td><?php echo htmlspecialchars($c['nama_customer']); ?></td>
      <td><?php echo htmlspecialchars($c['alamat']); ?></td>
      <td><?php echo htmlspecialchars($c['telp']); ?></td>
      <td><?php echo htmlspecialchars($c['email']); ?></td>
      <td>
        <a class="btn btn-sm btn-outline-secondary" href="index.php?r=customers/edit&id=<?php echo $c['id_customer']; ?>">Edit</a>
        <a class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus data ini?')" href="index.php?r=customers/delete&id=<?php echo $c['id_customer']; ?>">Hapus</a>
      </td>
    </tr>
  <?php endforeach; ?>
  </tbody>
</table>
