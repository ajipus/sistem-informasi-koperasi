<?php
// public/index.php (router) - explicit class map
require_once __DIR__ . '/../config/db.php';

$route = $_GET['r'] ?? 'auth/login';
list($controller, $action) = array_pad(explode('/', $route), 2, null);

function dispatch($pdo, $controller, $action) {
  // file map
  $fileMap = [
    'auth'      => __DIR__.'/../app/controllers/AuthController.php',
    'customers' => __DIR__.'/../app/controllers/CustomerController.php',
    'items'     => __DIR__.'/../app/controllers/ItemController.php',
    'sales'     => __DIR__.'/../app/controllers/SalesController.php',
    'users'     => __DIR__.'/../app/controllers/UsersController.php',
    'reports'   => __DIR__.'/../app/controllers/ReportsController.php',
    'company'   => __DIR__.'/../app/controllers/CompanyController.php',
  ];
  // class map (handle plural route names)
  $classMap = [
    'auth'      => 'AuthController',
    'customers' => 'CustomerController',
    'items'     => 'ItemController',
    'sales'     => 'SalesController',
    'users'     => 'UsersController',
    'reports'   => 'ReportsController',
    'company'   => 'CompanyController',
  ];

  if (!isset($fileMap[$controller]) || !file_exists($fileMap[$controller])) {
    http_response_code(404); echo "Controller not found"; exit;
  }
  require_once $fileMap[$controller];

  $className = $classMap[$controller] ?? (ucfirst($controller).'Controller');
  if (!class_exists($className)) {
    http_response_code(500); echo "Class '$className' not found"; exit;
  }

  $obj = new $className($pdo);
  if (!$action || !method_exists($obj, $action)) { $action = 'index'; }
  $obj->$action();
}

dispatch($pdo, $controller, $action);
