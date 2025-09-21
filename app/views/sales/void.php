<h3>Void / Batalkan Transaksi</h3>
<div class="card">
  <div class="card-body">
    <form method="post">
      <div class="mb-3">
        <label class="form-label">Alasan pembatalan</label>
        <textarea name="reason" class="form-control" rows="4" required placeholder="Tuliskan alasan pembatalan..."></textarea>
      </div>
      <a href="index.php?r=sales/show&id=<?php echo (int)($_GET['id'] ?? 0); ?>" class="btn btn-outline-secondary">Batal</a>
      <button class="btn btn-danger" type="submit" onclick="return confirm('Yakin batalkan transaksi ini?')">Void / Batalkan</button>
    </form>
  </div>
</div>
