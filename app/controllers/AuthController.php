<?php
// app/controllers/AuthController.php
require_once __DIR__ . '/BaseController.php';

class AuthController extends BaseController
{
    public function login()
    {
        // Jika sudah login, arahkan ke halaman utama
        if (isset($_SESSION['user'])) {
            return $this->redirect('index.php?r=customers/index');
        }

        $error = '';

        if ($this->isPost()) {
            $username = $this->inputPost('username', '');
            $password = $this->inputPost('password', '');

            if ($username === '' || $password === '') {
                $error = 'Username dan password wajib diisi.';
            } else {
                $sql = "SELECT u.id_user, u.nama_user, u.username, u.password_hash,
                               l.level_name AS level
                        FROM users u
                        JOIN levels l ON l.id_level = u.id_level
                        WHERE u.username = :u
                        LIMIT 1";
                $st = $this->pdo->prepare($sql);
                $st->execute([':u' => $username]);
                $row = $st->fetch(PDO::FETCH_ASSOC);

                if ($row && password_verify($password, $row['password_hash'])) {
                    $_SESSION['user'] = [
                        'id'       => (int)$row['id_user'],
                        'username' => $row['username'],
                        'name'     => $row['nama_user'],
                        'level'    => $row['level'],
                    ];
                    return $this->redirect('index.php?r=customers/index');
                } else {
                    $error = 'Username atau password salah.';
                }
            }
        }

        $this->view('auth/login', ['error' => $error]);
    }

    public function logout()
    {
        session_destroy();
        $this->redirect('index.php?r=auth/login');
    }

    public function changepw()
    {
        $this->requireLevel(['admin', 'manager', 'kasir']);

        $msg = '';
        $err = '';

        if ($this->isPost()) {
            $old  = $this->inputPost('old', '');
            $new  = $this->inputPost('new', '');
            $new2 = $this->inputPost('new2', '');

            if ($new === '' || $new !== $new2) {
                $err = 'Konfirmasi password baru tidak sama.';
            } else {
                $st = $this->pdo->prepare("SELECT password_hash FROM users WHERE id_user=:id LIMIT 1");
                $st->execute([':id' => $_SESSION['user']['id']]);
                $row = $st->fetch(PDO::FETCH_ASSOC);

                if (!$row || !password_verify($old, $row['password_hash'])) {
                    $err = 'Password lama salah.';
                } else {
                    $hash = password_hash($new, PASSWORD_BCRYPT);
                    $up = $this->pdo->prepare("UPDATE users SET password_hash=:p WHERE id_user=:id");
                    $up->execute([':p' => $hash, ':id' => $_SESSION['user']['id']]);
                    $msg = 'Password berhasil diubah.';
                }
            }
        }

        $this->view('auth/changepw', ['msg' => $msg, 'err' => $err]);
    }
}
