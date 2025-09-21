<h3>Identitas Koperasi</h3>
<form method="post" enctype="multipart/form-data">
  <div class="row">
    <div class="col-md-6">
      <div class="mb-3"><label class="form-label">Nama</label><input class="form-control" name="nama_identitas" value="<?php echo htmlspecialchars($company['nama_identitas'] ?? ''); ?>" required></div>
      <div class="mb-3"><label class="form-label">Badan Hukum</label><input class="form-control" name="badan_hukum" value="<?php echo htmlspecialchars($company['badan_hukum'] ?? ''); ?>"></div>
      <div class="mb-3"><label class="form-label">NPWP</label><input class="form-control" name="npwp" value="<?php echo htmlspecialchars($company['npwp'] ?? ''); ?>"></div>
      <div class="mb-3"><label class="form-label">Email</label><input class="form-control" type="email" name="email" value="<?php echo htmlspecialchars($company['email'] ?? ''); ?>"></div>
      <div class="mb-3"><label class="form-label">Website</label><input class="form-control" name="url" value="<?php echo htmlspecialchars($company['url'] ?? ''); ?>"></div>
    </div>
    <div class="col-md-6">
      <div class="mb-3"><label class="form-label">Alamat</label><textarea class="form-control" rows="4" name="alamat"><?php echo htmlspecialchars($company['alamat'] ?? ''); ?></textarea></div>
      <div class="row">
        <div class="col-md-6 mb-3"><label class="form-label">Telepon</label><input class="form-control" name="telp" value="<?php echo htmlspecialchars($company['telp'] ?? ''); ?>"></div>
        <div class="col-md-6 mb-3"><label class="form-label">Fax</label><input class="form-control" name="fax" value="<?php echo htmlspecialchars($company['fax'] ?? ''); ?>"></div>
      </div>
      <div class="mb-3"><label class="form-label">Rekening</label><input class="form-control" name="rekening" value="<?php echo htmlspecialchars($company['rekening'] ?? ''); ?>"></div>
      <div class="mb-3">
        <label class="form-label">Logo (jpg/png)</label>
        <input type="file" class="form-control" name="foto" accept=".jpg,.jpeg,.png">
        <?php 
          $logo_src = trim($company['foto'] ?? '');
          if ($logo_src && strpos($logo_src, 'public/') === 0) {
            $logo_src = substr($logo_src, strlen('public/'));
          }
          if(!empty($logo_src)): ?>
          <div class="mt-2"><img src="<?php echo htmlspecialchars($logo_src); ?>" alt="logo" style="height:60px"></div>
        <?php endif; ?>
      </div>
    </div>
  </div>
  <button class="btn btn-primary">Simpan</button>
</form>
