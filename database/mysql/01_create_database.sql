-- Run once in phpMyAdmin / MySQL client before `php artisan migrate:fresh --seed`.
CREATE DATABASE IF NOT EXISTS bank_ta
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

-- Optional: create a dedicated local development user instead of using root.
-- CREATE USER 'bank_ta_user'@'localhost' IDENTIFIED BY 'ganti_password_kuat';
-- GRANT ALL PRIVILEGES ON bank_ta.* TO 'bank_ta_user'@'localhost';
-- FLUSH PRIVILEGES;
