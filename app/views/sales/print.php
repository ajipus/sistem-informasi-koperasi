<?php
// app/views/sales/print.php
// Needs: $header, $details, $company; optional $is_pdf=true when rendering PDF

$is_pdf = $is_pdf ?? false;

function nf($n){ return number_format((float)$n, 2, ',', '.'); }

/* ---------- Base URL untuk HTML (viewer browser) ---------- */
$scriptBase = rtrim(str_replace('\\','/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/'); // /koperasi/public
$scheme     = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host       = $_SERVER['HTTP_HOST'] ?? 'localhost';
$baseUrl    = $scheme.'://'.$host.$scriptBase;

/* ---------- Resolve path filesystem public/ ---------- */
$docRoot   = rtrim(str_replace('\\','/', $_SERVER['DOCUMENT_ROOT'] ?? ''), '/');          // C:/xampp/htdocs
$publicFS  = $docRoot.$scriptBase;                                                        // C:/xampp/htdocs/koperasi/public
if (!is_dir($publicFS)) {
  // fallback dari posisi view (…/app/views/sales -> …/public)
  $publicFS = str_replace('\\','/', realpath(__DIR__.'/../../../public') ?: '');
}

/* ---------- Siapkan sumber LOGO ---------- */
$logoFile  = basename(trim($company['logo'] ?? '')); // basename untuk aman
$logoSrc   = '';    // yang dipakai <img src="…">
$logoDebug = '';    // pesan debug kecil jika gagal

if ($logoFile !== '') {
  $fs = $publicFS ? $publicFS.'/uploads/logo/'.$logoFile : '';
  if ($is_pdf && $fs && is_file($fs)) {
    // Saat PDF: embed data URI (paling stabil di Dompdf)
    $ext  = strtolower(pathinfo($fs, PATHINFO_EXTENSION));
    $mime = ($ext === 'png') ? 'image/png' : (($ext === 'gif') ? 'image/gif' : 'image/jpeg');
    $bin  = @file_get_contents($fs);
    if ($bin !== false) {
      $logoSrc = 'data:'.$mime.';base64,'.base64_encode($bin);
    } else {
      $logoDebug = 'Logo file not readable: '.$fs;
    }
  }
  // Fallback: pakai URL (untuk HTML / jika embed gagal)
  if ($logoSrc === '') {
    $testFs = $publicFS.'/uploads/logo/'.$logoFile;
    if (is_file($testFs)) {
      $logoSrc = $baseUrl.'/uploads/logo/'.rawurlencode($logoFile);
    } else {
      $logoDebug = 'Logo file not found: '.$testFs;
    }
  }
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Invoice / DO</title>
  <style>
    body{font-family:DejaVu Sans, Arial, Helvetica, sans-serif;font-size:12px;color:#000;margin:30px;}
    .no-print{margin:10px 0;}
    @media print {.no-print{display:none!important;}}
    <?php if ($is_pdf): ?>.no-print{display:none!important;}<?php endif; ?>

    .row{display:flex;gap:16px;align-items:flex-start;}
    .col{flex:1 1 0;}
    .right{text-align:right;}
    .heading{font-weight:700;font-size:18px;}
    .muted{color:#666;font-size:11px;}
    .mb-3{margin-bottom:12px}.mb-4{margin-bottom:16px}.mb-5{margin-bottom:24px}.mt-2{margin-top:8px}

    table{border-collapse:collapse;width:100%;}
    th,td{border:1px solid #666;padding:6px;}
    th{background:#f2f2f2;}
    .no-border{border:none!important}
    .text-right{text-align:right}

    .logo{max-height:60px;max-width:200px}
    .box{border:1px solid #666;padding:8px}

    .w-40{width:40px}.w-90{width:90px}.w-80{width:80px}.w-120{width:120px}.w-140{width:140px}

    /* tanda tangan */
    .ttd td{border:none!important;padding-top:30px;vertical-align:top}
    .ttd .left{text-align:left;width:50%}
    .ttd .right{text-align:right;width:50%}
    .line{display:inline-block;margin-top:50px}
    .debug{font-size:10px;color:#999;margin-top:4px;}
  </style>
</head>
<body>

  <!-- tombol yg tidak ikut tercetak/PDF -->
  <div class="no-print">
    <a href="javascript:window.print()">Cetak / Print</a>
    &nbsp;|&nbsp;
    <a href="index.php?r=sales/index">Kembali</a>
  </div>

  <!-- Header -->
  <div class="row mb-5">
    <div class="col">
      <?php if ($logoSrc): ?>
        <img src="<?php echo htmlspecialchars($logoSrc); ?>" alt="logo" class="logo">
      <?php else: ?>
        <div class="muted">logo</div>
      <?php endif; ?>
      <?php if ($logoDebug && $is_pdf): ?>
        <div class="debug"><?php echo htmlspecialchars($logoDebug); ?></div>
      <?php endif; ?>

      <div class="heading">
        <?php echo htmlspecialchars($company['nama_identitas'] ?? 'Koperasi Pegawai Sejahtera'); ?>
      </div>
      <div class="muted">
        <?php echo htmlspecialchars($company['alamat'] ?? ''); ?><br>
        Tel: <?php echo htmlspecialchars($company['telp'] ?? ''); ?>,
        Email: <?php echo htmlspecialchars($company['email'] ?? ''); ?>
      </div>
    </div>

    <div class="col right">
      <div class="heading">INVOICE / DO</div>
      <div class="box mt-2">
        <div>No: <?php echo htmlspecialchars($header['do_number'] ?? ''); ?></div>
        <div>Tanggal: <?php echo htmlspecialchars($header['tgl_sales'] ?? ''); ?></div>
        <div>#Transaksi: <?php echo htmlspecialchars($header['id_sales'] ?? ''); ?></div>
      </div>
    </div>
  </div>

  <!-- Kepada -->
  <div class="mb-3">
    <div><strong>Kepada Yth:</strong></div>
    <div><?php echo htmlspecialchars($header['nama_customer'] ?? ''); ?></div>
  </div>

  <!-- Tabel item -->
  <table class="mb-5">
    <thead>
      <tr>
        <th class="w-40">No</th>
        <th>Nama Barang</th>
        <th class="w-90 text-right">Qty</th>
        <th class="w-80">UOM</th>
        <th class="w-120 text-right">Harga</th>
        <th class="w-140 text-right">Jumlah</th>
      </tr>
    </thead>
    <tbody>
      <?php $grand=0; foreach (($details ?? []) as $i=>$d): $grand += (float)$d['amount']; ?>
        <tr>
          <td><?php echo $i+1; ?></td>
          <td><?php echo htmlspecialchars($d['nama_item']); ?></td>
          <td class="text-right"><?php echo nf($d['quantity']); ?></td>
          <td><?php echo htmlspecialchars($d['uom']); ?></td>
          <td class="text-right"><?php echo nf($d['price']); ?></td>
          <td class="text-right"><?php echo nf($d['amount']); ?></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
    <tfoot>
      <tr>
        <th class="no-border" colspan="4"></th>
        <th class="text-right">Grand Total</th>
        <th class="text-right"><?php echo nf($grand); ?></th>
      </tr>
    </tfoot>
  </table>

  <!-- Tanda tangan -->
  <table class="ttd">
    <tr>
      <td class="left">
        Penerima,<br><br><br>
        <span class="line">(________________)</span>
      </td>
      <td class="right">
        Hormat kami,<br><br><br>
        <span class="line">(________________)</span>
      </td>
    </tr>
  </table>

</body>
</html>
