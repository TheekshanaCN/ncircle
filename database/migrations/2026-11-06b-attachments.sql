-- Migration: images, files and GIFs on forum posts and group walls. Keeps all existing data.
SET NAMES utf8mb4;

CREATE TABLE attachments (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    owner_type ENUM('question','answer','group_post') NOT NULL,
    owner_id INT UNSIGNED NOT NULL,
    user_id INT UNSIGNED NOT NULL,
    kind ENUM('image','file','gif') NOT NULL,
    original_name VARCHAR(255) NOT NULL,
    stored_name VARCHAR(80) NULL,
    mime VARCHAR(120) NOT NULL,
    size INT UNSIGNED NOT NULL DEFAULT 0,
    width SMALLINT UNSIGNED NULL,
    height SMALLINT UNSIGNED NULL,
    giphy_id VARCHAR(64) NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_attachment_owner (owner_type, owner_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE group_posts ADD COLUMN is_hidden TINYINT(1) NOT NULL DEFAULT 0 AFTER body;
ALTER TABLE reports MODIFY target_type ENUM('resource','question','answer','comment','help_request','group_post') NOT NULL;
