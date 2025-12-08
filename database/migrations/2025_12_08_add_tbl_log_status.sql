-- Migration to add the log status table for notifications and other user-specific status tracking
-- Date: 2025-12-08

CREATE TABLE tbl_log_status (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    tipe_log VARCHAR(50) NOT NULL COMMENT 'cth: NOTIFIKASI_APPROVAL, REMINDER_LPJ, BOOKMARK',
    id_referensi INT NULL COMMENT 'cth: ID kegiatan, ID LPJ',
    status VARCHAR(20) NOT NULL COMMENT 'cth: BELUM_DIBACA, DIBACA, AKTIF',
    konten_json JSON NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    -- Foreign Key to user table
    FOREIGN KEY (user_id) REFERENCES tbl_user(userId) ON DELETE CASCADE,
    
    -- Indexes for performance
    INDEX idx_user_status (user_id, status),
    INDEX idx_tipe_log (tipe_log),
    INDEX idx_created_at (created_at)
);
