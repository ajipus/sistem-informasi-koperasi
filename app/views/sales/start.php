<?php if(!empty($_SESSION['flash'])): ?><div class="alert alert-warning"><?php echo $_SESSION['flash']; unset($_SESSION['flash']); ?></div><?php endif; ?>
<h3>Buat Transaksi (Keranjang)</h3>
<form class="row g-3 mb-4" method="post" action="index.php?r=sales/post">
  <div class="col-md-3">
    <label class="form-label">Tanggal</label>
    <input type="date" name="tgl_sales" class="form-control" value="<?php echo $today; ?>">
  </div>
  <div class="col-md-5">
    <label class="form-label">Customer</label>
    <select name="id_customer" class="form-select" required>
      <option value="">-- Pilih --</option>
      <?php foreach($customers as $c): ?>
        <option value="<?php echo $c['id_customer']; ?>"><?php echo htmlspecialchars($c['nama_customer']); ?></option>
      <?php endforeach; ?>
    </select>
  </div>
  <div class="col-md-4">
    <label class="form-label">DO Number</label>
    <input name="do_number" class="form-control" placeholder="Opsional">
  </div>
  <div class="col-12">
    <button class="btn btn-success">Posting</button>
    <a class="btn btn-secondary" href="index.php?r=sales/index">Batal</a>
  </div>
</form>

<hr>
<h5>Tambah Item ke Keranjang</h5>
<form class="row g-2" method="post" action="index.php?r=sales/addtemp" id="formAddTemp">
  <div class="col-md-5">
    <select name="id_item" class="form-select" required id="selectItem">
      <option value="">-- Pilih Item --</option>
      <?php foreach($items as $i): ?>
        <option value="<?php echo $i['id_item']; ?>" data-price="<?php echo $i['harga_jual']; ?>">
          <?php echo htmlspecialchars($i['nama_item']).' ('.$i['uom'].')'; ?>
        </option>
      <?php endforeach; ?>
    </select>
  </div>
  <div class="col-md-2"><input class="form-control" type="number" step="0.01" name="quantity" placeholder="Qty" required value="1"></div>
  <div class="col-md-3"><input class="form-control" type="number" step="0.01" name="price" placeholder="Harga" required id="inputPrice"></div>
  <div class="col-md-2"><button class="btn btn-primary w-100">Tambah</button></div>
</form>

<table class="table table-bordered mt-3">
  <thead><tr><th>#</th><th>Item</th><th>Qty</th><th>UOM</th><th>Harga</th><th>Amount</th><th></th></tr></thead>
  <tbody>
    <?php $grand=0; foreach($itemsTmp as $row): $grand += $row['amount']; ?>
      <tr>
        <td><?php echo $row['id']; ?></td>
        <td><?php echo htmlspecialchars($row['nama_item']); ?></td>
        <td><?php echo $row['quantity']; ?></td>
        <td><?php echo htmlspecialchars($row['uom']); ?></td>
        <td><?php echo number_format($row['price'],2,',','.'); ?></td>
        <td><?php echo number_format($row['amount'],2,',','.'); ?></td>
        <td><a class="btn btn-sm btn-danger" href="index.php?r=sales/removetemp&id=<?php echo $row['id']; ?>" onclick="return confirm('Hapus item ini?')">Hapus</a></td>
      </tr>
    <?php endforeach; ?>
  </tbody>
  <tfoot><tr><th colspan="5" class="text-end">Grand Total</th><th><?php echo number_format($grand,2,',','.'); ?></th><th></th></tr></tfoot>
</table>

<script>
document.addEventListener('DOMContentLoaded', function(){
  var sel = document.getElementById('selectItem');
  var priceInput = document.getElementById('inputPrice');
  if (!sel || !priceInput) return;

  function fillPriceFromSelected(){
    var opt = sel.options[sel.selectedIndex];
    if (!opt) return;
    var p = opt.getAttribute('data-price');
    if (p !== null && p !== '') {
      priceInput.value = p;
    }
  }
  sel.addEventListener('change', fillPriceFromSelected);
  // Auto-fill once on load if item sudah terpilih
  fillPriceFromSelected();
});
</script>
