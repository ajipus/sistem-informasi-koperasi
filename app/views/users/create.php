<h3>Tambah Petugas</h3>
<form method="post">
  <div class="row">
    <div class="col-md-6">
      <div class="mb-3"><label class="form-label">Nama</label><input class="form-control" name="nama_user" required></div>
      <div class="mb-3"><label class="form-label">Username</label><input class="form-control" name="username" required></div>
      <div class="mb-3"><label class="form-label">Password</label><input type="password" class="form-control" name="password" required></div>
      <div class="mb-3">
        <label class="form-label">Level</label>
        <select class="form-select" name="id_level" required>
          <?php foreach($levels as $l): ?>
            <option value="<?php echo $l['id_level']; ?>"><?php echo htmlspecialchars($l['level_name']); ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <button class="btn btn-primary">Simpan</button>
      <a class="btn btn-secondary" href="index.php?r=users/index">Batal</a>
    </div>
  </div>
</form>
