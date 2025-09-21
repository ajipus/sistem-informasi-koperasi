
-- Koperasi Procurement App Schema (MySQL)
-- Import this file in phpMyAdmin (XAMPP) or via: mysql -u root < schema.sql
-- Engine/charset
SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS=0;

DROP DATABASE IF EXISTS koperasi_app;
CREATE DATABASE koperasi_app CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE koperasi_app;

-- 1) Level (role/hak akses)
CREATE TABLE levels (
  id_level INT AUTO_INCREMENT PRIMARY KEY,
  level_name VARCHAR(50) NOT NULL UNIQUE
) ENGINE=InnoDB;

-- 2) Users (Petugas)
CREATE TABLE users (
  id_user INT AUTO_INCREMENT PRIMARY KEY,
  nama_user VARCHAR(100) NOT NULL,
  username VARCHAR(60) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  id_level INT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_users_level FOREIGN KEY (id_level) REFERENCES levels(id_level)
    ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB;

-- 3) Customers
CREATE TABLE customers (
  id_customer INT AUTO_INCREMENT PRIMARY KEY,
  nama_customer VARCHAR(120) NOT NULL,
  alamat TEXT,
  telp VARCHAR(30),
  fax VARCHAR(30),
  email VARCHAR(120)
) ENGINE=InnoDB;

-- 4) Items (Produk/Barang)
CREATE TABLE items (
  id_item INT AUTO_INCREMENT PRIMARY KEY,
  nama_item VARCHAR(150) NOT NULL,
  uom VARCHAR(30) NOT NULL, -- satuan
  harga_beli DECIMAL(14,2) NOT NULL DEFAULT 0.00,
  harga_jual DECIMAL(14,2) NOT NULL DEFAULT 0.00,
  is_active TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB;

-- 5) Sales (Header penjualan)
CREATE TABLE sales (
  id_sales INT AUTO_INCREMENT PRIMARY KEY,
  tgl_sales DATE NOT NULL,
  id_customer INT NOT NULL,
  do_number VARCHAR(50),
  status VARCHAR(20) NOT NULL DEFAULT 'draft',
  created_by INT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_sales_customer FOREIGN KEY (id_customer) REFERENCES customers(id_customer)
    ON UPDATE CASCADE ON DELETE RESTRICT,
  CONSTRAINT fk_sales_user FOREIGN KEY (created_by) REFERENCES users(id_user)
    ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB;

-- 6) Transaction (Detail barang untuk setiap sales) | memakai nama 'transactions' untuk menghindari ambigu kata kunci
CREATE TABLE transactions (
  id_transaction INT AUTO_INCREMENT PRIMARY KEY,
  id_sales INT NOT NULL,
  id_item INT NOT NULL,
  quantity DECIMAL(14,2) NOT NULL CHECK (quantity >= 0),
  price DECIMAL(14,2) NOT NULL CHECK (price >= 0),
  amount DECIMAL(14,2) AS (quantity * price) STORED,
  CONSTRAINT fk_trx_sales FOREIGN KEY (id_sales) REFERENCES sales(id_sales)
    ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT fk_trx_item FOREIGN KEY (id_item) REFERENCES items(id_item)
    ON UPDATE CASCADE ON DELETE RESTRICT,
  INDEX idx_trx_sales (id_sales),
  INDEX idx_trx_item (id_item)
) ENGINE=InnoDB;

-- 7) Transaction temp (keranjang sementara ketika input)
CREATE TABLE transaction_temp (
  id INT AUTO_INCREMENT PRIMARY KEY,
  session_id VARCHAR(64) NOT NULL,
  id_item INT NOT NULL,
  quantity DECIMAL(14,2) NOT NULL CHECK (quantity >= 0),
  price DECIMAL(14,2) NOT NULL CHECK (price >= 0),
  amount DECIMAL(14,2) AS (quantity * price) STORED,
  remark VARCHAR(200),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_trx_tmp_item FOREIGN KEY (id_item) REFERENCES items(id_item)
    ON UPDATE CASCADE ON DELETE RESTRICT,
  INDEX idx_tmp_session (session_id)
) ENGINE=InnoDB;

-- 8) Identitas perusahaan (profil koperasi)
CREATE TABLE company_identity (
  id_identitas INT AUTO_INCREMENT PRIMARY KEY,
  nama_identitas VARCHAR(150) NOT NULL,
  badan_hukum VARCHAR(150),
  npwp VARCHAR(50),
  email VARCHAR(150),
  url VARCHAR(200),
  alamat TEXT,
  telp VARCHAR(30),
  fax VARCHAR(30),
  rekening VARCHAR(150),
  foto VARCHAR(255)
) ENGINE=InnoDB;

-- Seed data: roles
INSERT INTO levels (level_name) VALUES ('admin'), ('kasir'), ('manager');

-- Seed data: default admin (password: admin123)
-- Please change after first login.
INSERT INTO users (nama_user, username, password_hash, id_level)
VALUES ('Administrator', 'admin', '$2y$10$4o8o3GfeDuoS7VM.2Oa7E.y1J3KJqgqoj2c4u2zqS/AhJvI.8yC0O', 1);

-- Seed sample: company
INSERT INTO company_identity (nama_identitas, email, telp, alamat)
VALUES ('Koperasi Pegawai Sejahtera', 'info@koperasi.local', '021-000000', 'Jl. Contoh No.1');

-- Seed sample: items & customers
INSERT INTO items (nama_item, uom, harga_beli, harga_jual) VALUES
('Detergen 1kg', 'pak', 15000, 20000),
('Sapu Ijuk', 'pcs', 18000, 25000),
('Pel Lantai', 'pcs', 25000, 32000);

INSERT INTO customers (nama_customer, alamat, telp, email) VALUES
('PT Rumah Bersih', 'Jl. Kenanga 5', '0812-1111-2222', 'admin@rumahbersih.co.id'),
('CV Rapi Sejahtera', 'Jl. Melati 7', '0813-2222-3333', 'halo@rapisejahtera.co.id');

SET FOREIGN_KEY_CHECKS=1;
