<h3>Import Customer (CSV)</h3>
<?php if(!empty($info)): ?><div class="alert alert-info"><?php echo $info; ?></div><?php endif; ?>
<div class="card"><div class="card-body">
  <form method="post" enctype="multipart/form-data">
    <p>Format header yang diterima: <code>nama_customer, alamat, telp, email</code></p>
    <p>Contoh cepat: ekspor dulu → edit → impor kembali.</p>
    <div class="mb-3"><input type="file" class="form-control" name="csv" accept=".csv" required></div>
    <a class="btn btn-outline-secondary" href="index.php?r=customers/index">Kembali</a>
    <button class="btn btn-primary">Upload</button>
    <a class="btn btn-success" href="index.php?r=customers/exportcsv">Unduh Template/Export</a>
  </form>
</div></div>
