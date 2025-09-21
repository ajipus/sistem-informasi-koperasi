<?php
// app/controllers/CompanyController.php
require_once __DIR__.'/BaseController.php';

class CompanyController extends BaseController {
  private function guardAdmin() {
    if (session_status() !== PHP_SESSION_ACTIVE) session_start();
    if (!isset($_SESSION['user'])) { header('Location: index.php?r=auth/login'); exit; }
    if (($_SESSION['user']['level'] ?? '') !== 'admin') { http_response_code(403); echo "Forbidden"; exit; }
  }

  public function index() {
    $this->guardAdmin();
    $company = $this->pdo->query("SELECT * FROM company_identity LIMIT 1")->fetch();

    if ($this->isPost()) {
      $data = [
        'nama_identitas' => $_POST['nama_identitas'] ?? '',
        'badan_hukum' => $_POST['badan_hukum'] ?? '',
        'npwp' => $_POST['npwp'] ?? '',
        'email' => $_POST['email'] ?? '',
        'url' => $_POST['url'] ?? '',
        'alamat' => $_POST['alamat'] ?? '',
        'telp' => $_POST['telp'] ?? '',
        'fax' => $_POST['fax'] ?? '',
        'rekening' => $_POST['rekening'] ?? ''
      ];

      // handle upload logo
      if (!empty($_FILES['foto']['name'])) {
        $dir = __DIR__ . '/../../public/uploads/logo';
        if (!is_dir($dir)) { @mkdir($dir, 0777, true); }
        $ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
        $fname = 'logo_'.date('Ymd_His').'.'.$ext;
        $target = $dir . '/' . $fname;
        if (is_uploaded_file($_FILES['foto']['tmp_name'])) {
          if (@move_uploaded_file($_FILES['foto']['tmp_name'], $target)) {
            $data['foto'] = 'uploads/logo/' . $fname;
          }
        }
      }

      if ($company) {
        // update
        $sql = "UPDATE company_identity SET nama_identitas=:nama_identitas, badan_hukum=:badan_hukum, npwp=:npwp,
                email=:email, url=:url, alamat=:alamat, telp=:telp, fax=:fax, rekening=:rekening"
              . (isset($data['foto']) ? ", foto=:foto" : "") . " WHERE id_identitas=:id";
        $stmt = $this->pdo->prepare($sql);
        $data['id'] = $company['id_identitas'];
        $stmt->execute($data);
      } else {
        // insert
        $cols = array_keys($data);
        $sql = "INSERT INTO company_identity (" . implode(",", $cols) . ") VALUES (:" . implode(",:", $cols) . ")";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($data);
      }
      header('Location: index.php?r=company/index'); exit;
    }

    $this->view(__DIR__.'/../views/company/index.php', compact('company'));
  }
}
