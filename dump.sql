CREATE DATABASE OSD;

CREATE USER 'osd_user'@'localhost' IDENTIFIED BY 'we3kdbr!m5xyP&fzO';
GRANT SELECT, INSERT, UPDATE ON OSD.* TO 'osd_user'@'localhost';

CREATE TABLE IF NOT EXISTS OSD_REGISTRATIONS
(
    reg_id        INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name          VARCHAR(30)                        NOT NULL,
    email         VARCHAR(50)                        NOT NULL,
    phone         varchar(50)                        NOT NULL,
    package       VARCHAR(50)                        NOT NULL,
    checked_in    bool     DEFAULT false,
    REG_TIMESTAMP DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL
);