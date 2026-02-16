DROP TABLE IF EXISTS Users;

CREATE TABLE Users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('ADMIN','CLIENT') NOT NULL DEFAULT 'CLIENT',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

echo password_hash('admin123', PASSWORD_DEFAULT);
INSERT INTO Users (username, email, password_hash, role)
VALUES ('admin', 'admin@test.com', 'HASH_ICI', 'ADMIN');
admin@test.com
admin123
