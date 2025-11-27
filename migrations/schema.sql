-- Use Railway's database (change if yours has a different name)
USE railway;

-- Create tables
CREATE TABLE IF NOT EXISTS pages (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    slug VARCHAR(100) NOT NULL UNIQUE,
    title VARCHAR(255) NOT NULL,
    content MEDIUMTEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(190) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('admin','editor','user') NOT NULL DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

ALTER TABLE users
    MODIFY role ENUM('admin','editor','user') NOT NULL DEFAULT 'user';

CREATE TABLE IF NOT EXISTS contact_messages (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(190) NOT NULL,
    subject VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS members (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NULL,
    full_name VARCHAR(150) NOT NULL,
    birthday DATE NULL,
    water_baptism_date DATE NULL,
    holy_ghost_baptism_date DATE NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_members_user
        FOREIGN KEY (user_id) REFERENCES users(id)
        ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS member_relationships (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    member_id INT UNSIGNED NOT NULL,
    related_member_id INT UNSIGNED NOT NULL,
    relationship_type ENUM('parent','child','spouse','sibling') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_rel_member
        FOREIGN KEY (member_id) REFERENCES members(id)
        ON DELETE CASCADE,
    CONSTRAINT fk_rel_related_member
        FOREIGN KEY (related_member_id) REFERENCES members(id)
        ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS bible_study_groups (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    description TEXT NULL,
    leader_member_id INT UNSIGNED NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_group_leader
        FOREIGN KEY (leader_member_id) REFERENCES members(id)
        ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS bible_study_group_members (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    group_id INT UNSIGNED NOT NULL,
    member_id INT UNSIGNED NOT NULL,
    joined_at DATE NOT NULL,
    CONSTRAINT fk_group_members_group
        FOREIGN KEY (group_id) REFERENCES bible_study_groups(id)
        ON DELETE CASCADE,
    CONSTRAINT fk_group_members_member
        FOREIGN KEY (member_id) REFERENCES members(id)
        ON DELETE CASCADE,
    UNIQUE KEY uniq_group_member (group_id, member_id)
);

-- Seed default pages
INSERT IGNORE INTO pages (slug, title, content) VALUES
('home', 'Welcome to UPC Mandaue', 'This is the home page content. You can edit this text from the database.'),
('about', 'About UPC Mandaue', 'This is the about page content. Describe the organization here.'),
('contact', 'Contact Us', 'This is the contact page content. Include address, phone, and email here.');
