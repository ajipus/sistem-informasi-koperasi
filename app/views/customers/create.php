<h3>Tambah Customer</h3>
<form method="post">
  <div class="row">
    <div class="col-md-6">
      <div class="mb-3">
        <label class="form-label">Nama</label>
        <input class="form-control" name="nama_customer" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Alamat</label>
        <textarea class="form-control" name="alamat"></textarea>
      </div>
      <div class="mb-3">
        <label class="form-label">Telp</label>
        <input class="form-control" name="telp">
      </div>
      <div class="mb-3">
        <label class="form-label">Fax</label>
        <input class="form-control" name="fax">
      </div>
      <div class="mb-3">
        <label class="form-label">Email</label>
        <input type="email" class="form-control" name="email">
      </div>
      <button class="btn btn-primary">Simpan</button>
      <a class="btn btn-secondary" href="index.php?r=customers/index">Batal</a>
    </div>
  </div>
</form>
