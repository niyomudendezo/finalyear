CREATE DATABASE IF NOT EXISTS rdds CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE rdds;

CREATE TABLE IF NOT EXISTS tbl_system_settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    system_name VARCHAR(200) DEFAULT 'Road Damage Detection System',
    organization VARCHAR(200) DEFAULT 'Rwanda Transport Development Agency (RTDA)',
    system_logo VARCHAR(200) DEFAULT 'logo.png',
    contact_email VARCHAR(200) DEFAULT 'info@rtda.gov.rw',
    contact_phone VARCHAR(50) DEFAULT '+250 788 000 000',
    address VARCHAR(300) DEFAULT 'Kigali, Rwanda'
);

INSERT INTO tbl_system_settings (id) VALUES (1) ON DUPLICATE KEY UPDATE id=1;

CREATE TABLE IF NOT EXISTS tbl_roles (
    role_id INT AUTO_INCREMENT PRIMARY KEY,
    role_name VARCHAR(100) NOT NULL
);

INSERT INTO tbl_roles (role_id, role_name) VALUES
(1, 'Admin'),
(2, 'Transport Authority Official'),
(3, 'Engineer'),
(4, 'Road User')
ON DUPLICATE KEY UPDATE role_name=VALUES(role_name);

CREATE TABLE IF NOT EXISTS tbl_users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    firstname VARCHAR(100) NOT NULL,
    lastname VARCHAR(100) NOT NULL,
    email VARCHAR(200) NOT NULL UNIQUE,
    phone VARCHAR(50),
    username VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role_id INT NOT NULL DEFAULT 4,
    is_active TINYINT(1) DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (role_id) REFERENCES tbl_roles(role_id)
);

INSERT INTO tbl_users (firstname, lastname, email, phone, username, password, role_id) VALUES
('System', 'Admin', 'admin@rtda.gov.rw', '+250788000001', 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1),
('James', 'Hakizimana', 'james@rtda.gov.rw', '+250788000002', 'james', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 2),
('Ange', 'Abayisenga', 'ange@rtda.gov.rw', '+250788000003', 'ange', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3)
ON DUPLICATE KEY UPDATE firstname=VALUES(firstname);

CREATE TABLE IF NOT EXISTS tbl_damage_types (
    type_id INT AUTO_INCREMENT PRIMARY KEY,
    type_name VARCHAR(100) NOT NULL,
    description TEXT,
    icon VARCHAR(50) DEFAULT 'road'
);

INSERT INTO tbl_damage_types (type_id, type_name, description, icon) VALUES
(1, 'Pothole', 'A hole or depression in the road surface', 'circle'),
(2, 'Longitudinal Crack', 'Crack running parallel to the road direction', 'minus'),
(3, 'Transverse Crack', 'Crack running perpendicular to the road direction', 'arrows-h'),
(4, 'Rutting', 'Depressions or grooves worn into the road surface', 'road'),
(5, 'Surface Deterioration', 'General degradation of the road surface', 'warning'),
(6, 'Alligator Cracking', 'Interconnected cracks resembling alligator skin', 'th')
ON DUPLICATE KEY UPDATE type_name=VALUES(type_name);

CREATE TABLE IF NOT EXISTS tbl_road_reports (
    report_id INT AUTO_INCREMENT PRIMARY KEY,
    report_code VARCHAR(50) UNIQUE,
    reported_by INT NOT NULL,
    damage_type_id INT NOT NULL,
    severity ENUM('Low','Medium','High','Critical') NOT NULL DEFAULT 'Medium',
    description TEXT,
    location_name VARCHAR(300),
    latitude DECIMAL(10,8),
    longitude DECIMAL(11,8),
    province VARCHAR(100),
    district VARCHAR(100),
    sector VARCHAR(100),
    image_path VARCHAR(300),
    ai_confidence DECIMAL(5,2) DEFAULT NULL,
    status ENUM('Pending','Under Review','Approved','In Progress','Resolved','Rejected') DEFAULT 'Pending',
    priority_score INT DEFAULT 0,
    reported_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (reported_by) REFERENCES tbl_users(user_id),
    FOREIGN KEY (damage_type_id) REFERENCES tbl_damage_types(type_id)
);

CREATE TABLE IF NOT EXISTS tbl_maintenance_updates (
    update_id INT AUTO_INCREMENT PRIMARY KEY,
    report_id INT NOT NULL,
    updated_by INT NOT NULL,
    old_status VARCHAR(50),
    new_status VARCHAR(50),
    notes TEXT,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (report_id) REFERENCES tbl_road_reports(report_id),
    FOREIGN KEY (updated_by) REFERENCES tbl_users(user_id)
);

CREATE TABLE IF NOT EXISTS tbl_notifications (
    notif_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(200),
    message TEXT,
    is_read TINYINT(1) DEFAULT 0,
    report_id INT DEFAULT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES tbl_users(user_id)
);

CREATE TABLE IF NOT EXISTS tbl_audit_logs (
    log_id BIGINT AUTO_INCREMENT PRIMARY KEY,
    user_id INT DEFAULT NULL,
    action VARCHAR(100) NOT NULL,
    details VARCHAR(500),
    ip_address VARCHAR(45),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_audit_created (created_at),
    FOREIGN KEY (user_id) REFERENCES tbl_users(user_id) ON DELETE SET NULL
);

INSERT INTO tbl_road_reports (report_code, reported_by, damage_type_id, severity, description, location_name, latitude, longitude, province, district, sector, status, priority_score, ai_confidence) VALUES
('RPT-2026-001', 3, 1, 'High', 'Large pothole causing vehicle damage near Kigali Convention Centre', 'KN 5 Rd, Kigali', -1.9441, 30.0619, 'Kigali City', 'Gasabo', 'Kimironko', 'Approved', 85, 92.5),
('RPT-2026-002', 3, 2, 'Medium', 'Longitudinal crack along main road', 'KG 7 Ave, Kigali', -1.9536, 30.0606, 'Kigali City', 'Kicukiro', 'Niboye', 'Under Review', 60, 87.3),
('RPT-2026-003', 1, 3, 'Critical', 'Multiple transverse cracks causing road instability', 'RN1 Kigali-Musanze', -1.8700, 29.9800, 'Northern Province', 'Rulindo', 'Base', 'In Progress', 95, 95.1),
('RPT-2026-004', 3, 5, 'Low', 'Surface deterioration on rural road', 'Huye District Road', -2.5967, 29.7394, 'Southern Province', 'Huye', 'Ngoma', 'Pending', 30, 78.9),
('RPT-2026-005', 1, 4, 'High', 'Severe rutting on heavily trafficked road', 'RN3 Kigali-Rubavu', -1.7500, 29.5000, 'Western Province', 'Rubavu', 'Gisenyi', 'Approved', 80, 91.2)
ON DUPLICATE KEY UPDATE report_code=VALUES(report_code);
