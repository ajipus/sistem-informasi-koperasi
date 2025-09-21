<div class="d-flex justify-content-between align-items-center mb-3">
  <h3>Petugas</h3>
  <a class="btn btn-primary" href="index.php?r=users/create">Tambah Petugas</a>
</div>
<table class="table table-striped">
  <thead><tr><th>#</th><th>Nama</th><th>Username</th><th>Level</th><th>Aksi</th></tr></thead>
  <tbody>
    <?php foreach($rows as $u): ?>
      <tr>
        <td><?php echo $u['id_user']; ?></td>
        <td><?php echo htmlspecialchars($u['nama_user']); ?></td>
        <td><?php echo htmlspecialchars($u['username']); ?></td>
        <td><?php echo htmlspecialchars($u['level_name']); ?></td>
        <td>
          <a class="btn btn-sm btn-warning" href="index.php?r=users/edit&id=<?php echo $u['id_user']; ?>">Edit</a>
          <a class="btn btn-sm btn-danger" href="index.php?r=users/delete&id=<?php echo $u['id_user']; ?>" onclick="return confirm('Hapus petugas ini?')">Hapus</a>
        </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>
