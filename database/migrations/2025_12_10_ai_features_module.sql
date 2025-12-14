-- AI MONITORING MODULE. Run to enable. Drop tables to remove.

CREATE TABLE ai_security_alerts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NULL,
    ip_address VARCHAR(45),
    input_payload TEXT,
    severity ENUM('low', 'medium', 'high') NOT NULL,
    detection_type VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_ai_security_alerts_created_at ON ai_security_alerts (created_at);

CREATE TABLE ai_log_summaries (
    id INT AUTO_INCREMENT PRIMARY KEY,
    summary_text TEXT,
    error_count INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);