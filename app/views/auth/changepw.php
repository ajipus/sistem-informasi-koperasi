<?php
// app/views/auth/changepw.php
$msg = $msg ?? '';
$err = $err ?? '';
?>
<div class="row justify-content-center mt-4">
  <div class="col-md-5">
    <div class="card">
      <div class="card-header">Ganti Password</div>
      <div class="card-body">
        <?php if ($msg): ?><div class="alert alert-success"><?= htmlspecialchars($msg) ?></div><?php endif; ?>
        <?php if ($err): ?><div class="alert alert-danger"><?= htmlspecialchars($err) ?></div><?php endif; ?>
        <form method="post" action="index.php?r=auth/changepw">
          <div class="mb-3">
            <label class="form-label">Password Lama</label>
            <input type="password" name="old" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Password Baru</label>
            <input type="password" name="new" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Ulangi Password Baru</label>
            <input type="password" name="new2" class="form-control" required>
          </div>
          <button class="btn btn-primary">Simpan</button>
          <a class="btn btn-light" href="index.php?r=customers/index">Batal</a>
        </form>
      </div>
    </div>
  </div>
</div>
