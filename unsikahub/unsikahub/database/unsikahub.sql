CREATE DATABASE IF NOT EXISTS unsikahub CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE unsikahub;

DROP TABLE IF EXISTS portfolios;
DROP TABLE IF EXISTS portfolio_types;
DROP TABLE IF EXISTS users;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL,
    email VARCHAR(160) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    photo VARCHAR(255) NOT NULL,
    role ENUM('admin','user') NOT NULL DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE portfolio_types (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL UNIQUE,
    description TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE portfolios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    type_id INT NOT NULL,
    title VARCHAR(160) NOT NULL,
    description TEXT NOT NULL,
    project_url VARCHAR(255) NULL,
    cover_image VARCHAR(255) NULL,
    proof_file VARCHAR(255) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_portfolio_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_portfolio_type FOREIGN KEY (type_id) REFERENCES portfolio_types(id) ON DELETE RESTRICT
) ENGINE=InnoDB;

-- Akun admin awal: admin@unsikahub.test / admin12345
INSERT INTO users (name, email, password_hash, photo, role) VALUES
('Administrator', 'admin@unsikahub.test', '$2y$12$SJ48TsaGKtw3WkpeojseTuIxIwU.SgvyjClAx3DjvrhkammfgxyNS', 'uploads/profile/default-admin.svg', 'admin');

INSERT INTO portfolio_types (name, description) VALUES
('Website', 'Portofolio proyek website, UI web, dan aplikasi berbasis web.'),
('Desain', 'Portofolio desain grafis, identitas visual, poster, dan branding.'),
('Fotografi', 'Portofolio karya fotografi, dokumentasi, dan visual kreatif.'),
('Bisnis', 'Portofolio ide bisnis, rencana usaha, dan produk kewirausahaan.'),
('Lainnya', 'Jenis karya lain yang tetap relevan dengan portofolio mahasiswa.');
