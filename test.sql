-- Buat database & tabel
CREATE DATABASE IF NOT EXISTS stresser_db;
USE stresser_db;

CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  apikey VARCHAR(255) NOT NULL UNIQUE,
  maxtime INT NOT NULL DEFAULT 60,
  concurrents INT NOT NULL DEFAULT 1,
  expiry DATETIME NOT NULL
);

CREATE TABLE IF NOT EXISTS apis (
  id INT AUTO_INCREMENT PRIMARY KEY,
  apiurl TEXT NOT NULL
);

CREATE TABLE IF NOT EXISTS logs (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  host VARCHAR(255) NOT NULL,
  port INT NOT NULL,
  time INT NOT NULL,
  method VARCHAR(50) NOT NULL,
  concurrents INT NOT NULL,
  status VARCHAR(20) NOT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE IF NOT EXISTS blacklist (
  id INT AUTO_INCREMENT PRIMARY KEY,
  host VARCHAR(255) NOT NULL UNIQUE,
  reason VARCHAR(255),
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- Dummy data
INSERT INTO users (apikey, maxtime, concurrents, expiry) VALUES
('user123key', 300, 5, '2025-12-31 23:59:59'),
('expiredKey', 300, 5, '2020-01-01 00:00:00');

INSERT INTO apis (apiurl) VALUES
('http://127.0.0.1/api.php?host={host}&port={port}&time={time}&method={method}');

INSERT INTO blacklist (host, reason) VALUES
('bad.example.com', 'Malicious target');
