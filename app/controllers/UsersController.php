<?php
// app/controllers/UsersController.php
require_once __DIR__.'/BaseController.php';
require_once __DIR__.'/../models/UserAdmin.php';

class UsersController extends BaseController {
  private function guardAdmin() {
    if (session_status() !== PHP_SESSION_ACTIVE) session_start();
    if (!isset($_SESSION['user'])) { header('Location: index.php?r=auth/login'); exit; }
    if (($_SESSION['user']['level'] ?? '') !== 'admin') { http_response_code(403); echo "Forbidden"; exit; }
  }

  public function index() {
    $this->guardAdmin();
    $mdl = new UserAdmin($this->pdo);
    $rows = $mdl->all();
    $levels = $mdl->levels();
    $this->view(__DIR__.'/../views/users/index.php', compact('rows','levels'));
  }

  public function create() {
    $this->guardAdmin();
    $mdl = new UserAdmin($this->pdo);
    if ($this->isPost()) {
      $mdl->create($_POST);
      $this->redirect('index.php?r=users/index');
    }
    $levels = $mdl->levels();
    $this->view(__DIR__.'/../views/users/create.php', compact('levels'));
  }

  public function edit() {
    $this->guardAdmin();
    $id = (int)($_GET['id'] ?? 0);
    $mdl = new UserAdmin($this->pdo);
    if ($this->isPost()) {
      $mdl->update($id, $_POST);
      $this->redirect('index.php?r=users/index');
    }
    $levels = $mdl->levels();
    $user = $mdl->find($id);
    $this->view(__DIR__.'/../views/users/edit.php', compact('levels','user'));
  }

  public function delete() {
    $this->guardAdmin();
    $id = (int)($_GET['id'] ?? 0);
    $mdl = new UserAdmin($this->pdo);
    $mdl->delete($id);
    $this->redirect('index.php?r=users/index');
  }
}
