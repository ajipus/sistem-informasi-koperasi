<h3>Edit Item</h3>
<form method="post">
  <div class="row">
    <div class="col-md-6">
      <div class="mb-3">
        <label class="form-label">Nama</label>
        <input class="form-control" name="nama_item" value="<?php echo htmlspecialchars($item['nama_item'] ?? ''); ?>" required>
      </div>
      <div class="mb-3">
        <label class="form-label">UOM</label>
        <input class="form-control" name="uom" value="<?php echo htmlspecialchars($item['uom'] ?? ''); ?>" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Harga Beli</label>
        <input type="number" step="0.01" class="form-control" name="harga_beli" value="<?php echo htmlspecialchars($item['harga_beli'] ?? ''); ?>" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Harga Jual</label>
        <input type="number" step="0.01" class="form-control" name="harga_jual" value="<?php echo htmlspecialchars($item['harga_jual'] ?? ''); ?>" required>
      </div>
      <div class="form-check mb-3">
        <input class="form-check-input" type="checkbox" name="is_active" value="1" <?php echo (int)($item['is_active'] ?? 1) ? 'checked' : ''; ?>>
        <label class="form-check-label">Aktif</label>
      </div>
      <button class="btn btn-primary">Simpan</button>
      <a class="btn btn-secondary" href="index.php?r=items/index">Batal</a>
    </div>
  </div>
</form>
