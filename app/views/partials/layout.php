<?php
// app/views/partials/layout.php
if (session_status() !== PHP_SESSION_ACTIVE) { session_start(); }
$__content = $content ?? ($GLOBALS['__view_content'] ?? '');
$lv = $_SESSION['user']['level'] ?? null;
$userName = $_SESSION['user']['name'] ?? 'Administrator';
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Koperasi App</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container-fluid">
    <a class="navbar-brand" href="index.php?r=customers/index">Koperasi App</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarsExample" aria-controls="navbarsExample" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarsExample">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <?php if(in_array($lv, ['admin','manager'])): ?>
          <li class="nav-item"><a class="nav-link" href="index.php?r=customers/index">Customer</a></li>
          <li class="nav-item"><a class="nav-link" href="index.php?r=items/index">Item</a></li>
        <?php endif; ?>
        <?php if(in_array($lv, ['admin','manager','kasir'])): ?>
          <li class="nav-item"><a class="nav-link" href="index.php?r=sales/index">Sales</a></li>
        <?php endif; ?>
        
        <?php if(in_array($lv, ['admin','manager'])): ?>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="navReport" role="button"
              data-bs-toggle="dropdown" aria-expanded="false">
              Laporan
            </a>
            <ul class="dropdown-menu" aria-labelledby="navReport">
              <li><a class="dropdown-item" href="index.php?r=reports/sales">Penjualan</a></li>
              <li><a class="dropdown-item" href="index.php?r=reports/stock">Stok Produk</a></li>
            </ul>
          </li>

        <?php endif; ?>
        <?php if($lv === 'admin'): ?>
          <li class="nav-item"><a class="nav-link" href="index.php?r=company/index">Identitas</a></li>
          <li class="nav-item"><a class="nav-link" href="index.php?r=users/index">Petugas</a></li>
        <?php endif; ?>
      </ul>
      <div class="d-flex">
        <?php if(isset($_SESSION['user'])): ?>
          <span class="navbar-text me-3"><?= htmlspecialchars($userName) ?> (<?= htmlspecialchars($lv ?? '-') ?>)</span>
          <a href="index.php?r=auth/changepw" class="btn btn-outline-light btn-sm me-2">Ganti Password</a>
          <a href="index.php?r=auth/logout" class="btn btn-outline-light btn-sm">Logout</a>
        <?php endif; ?>
      </div>
    </div>
  </div>
</nav>
<div class="container py-4">
  <?php
    $path = $__content;
    if (is_string($path) && $path !== '' && !file_exists($path)) {
      $path = __DIR__ . '/../' . trim($__content, '/') . '.php';
    }
    if (is_string($path) && file_exists($path)) {
      include $path;
    } else {
      echo "<div class='alert alert-danger'>View tidak ditemukan / content kosong.</div>";
    }
  ?>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script>
  $(function(){
    if ($.fn.dataTable) {
      $.fn.dataTable.ext.errMode = 'throw';
      $('table.datatable').DataTable({
        language: { url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/id.json' },
        pageLength: 10,
        lengthChange: false,
        autoWidth: false,
        order: []
      });
    }
  });
</script>
</body>
</html>
