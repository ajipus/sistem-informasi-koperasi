<?php
// app/views/auth/login.php
$error = $error ?? '';
?>
<div class="row justify-content-center mt-5">
  <div class="col-md-4">
    <div class="card">
      <div class="card-header">Login</div>
      <div class="card-body">
        <?php if ($error): ?>
          <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <form method="post" action="index.php?r=auth/login">
          <div class="mb-3">
            <label class="form-label">Username</label>
            <input type="text" name="username" class="form-control" autofocus required>
          </div>
          <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control" required>
          </div>
          <button class="btn btn-primary w-100">Masuk</button>
        </form>
      </div>
    </div>
  </div>
</div>
