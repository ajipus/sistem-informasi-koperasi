<?php
// app/libs/pdf.php
/**
 * render_pdf(string $html, string $filename='document.pdf', string $paper='A4', string $orientation='portrait')
 * Wrapper kecil untuk Dompdf. Letakkan library Dompdf di salah satu path:
 * - vendor/autoload.php (instal via Composer) ATAU
 * - vendor/dompdf/autoload.inc.php (zip release tanpa Composer)
 */
function render_pdf($html, $filename='document.pdf', $paper='A4', $orientation='portrait') {
  $autoloads = [
    __DIR__ . '/../../vendor/autoload.php',
    __DIR__ . '/../../vendor/dompdf/autoload.inc.php',
    __DIR__ . '/../vendor/autoload.php',
    __DIR__ . '/../vendor/dompdf/autoload.inc.php'
  ];
  foreach ($autoloads as $a) {
    if (file_exists($a)) { require_once $a; break; }
  }
  if (class_exists('\\Dompdf\\Dompdf')) {
    $dompdf = new \Dompdf\Dompdf(['isRemoteEnabled' => true]);
    $dompdf->loadHtml($html);
    $dompdf->setPaper($paper, $orientation);
    $dompdf->render();
    header('Content-Type: application/pdf');
    header('Content-Disposition: inline; filename="'.$filename.'"');
    echo $dompdf->output();
    exit;
  } else {
    header('Content-Type: text/html; charset=utf-8');
    echo "<div style='padding:12px;background:#fff3cd;border:1px solid #ffeeba;'>";
    echo "Library <b>Dompdf</b> belum terpasang. Unduh dari ";
    echo "<a href='https://github.com/dompdf/dompdf/releases' target='_blank'>github.com/dompdf/dompdf</a> ";
    echo "lalu ekstrak ke folder <code>vendor/</code>. Setelah itu jalankan ulang fitur PDF.";
    echo "</div>";
    echo $html;
    exit;
  }
}
