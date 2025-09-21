<form class="row g-3 mb-3" method="get" action="index.php">
  <input type="hidden" name="r" value="reports/index">
  <input type="hidden" name="mode" value="<?php echo htmlspecialchars($mode); ?>">
  <div class="col-auto">
    <label class="form-label">Dari</label>
    <input type="date" class="form-control" name="from" value="<?php echo $from; ?>">
  </div>
  <div class="col-auto">
    <label class="form-label">Sampai</label>
    <input type="date" class="form-control" name="to" value="<?php echo $to; ?>">
  </div>
  <div class="col-auto align-self-end"><button class="btn btn-primary">Terapkan</button></div>
  <div class="col-auto align-self-end"><a class="btn btn-outline-secondary" href="index.php?r=reports/index">Daftar Transaksi</a></div>
</form>
