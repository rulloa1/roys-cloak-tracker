-- Database schema for Roy's Cloak Tracker
-- This will create the tables needed for storing tracking data

CREATE TABLE IF NOT EXISTS tracking_logs (
    id SERIAL PRIMARY KEY,
    fingerprint VARCHAR(255),
    ip VARCHAR(45),
    user_agent TEXT,
    screen VARCHAR(50),
    lang VARCHAR(10),
    timezone VARCHAR(50),
    referrer TEXT,
    country VARCHAR(100),
    city VARCHAR(100),
    isp VARCHAR(255),
    geo_timezone VARCHAR(50),
    logged_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS redirect_logs (
    id SERIAL PRIMARY KEY,
    fingerprint VARCHAR(255),
    ip VARCHAR(45),
    screen VARCHAR(50),
    lang VARCHAR(10),
    referrer TEXT,
    timezone VARCHAR(50),
    logged_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create indexes for better performance
CREATE INDEX IF NOT EXISTS idx_tracking_logs_ip ON tracking_logs(ip);
CREATE INDEX IF NOT EXISTS idx_tracking_logs_logged_at ON tracking_logs(logged_at);
CREATE INDEX IF NOT EXISTS idx_redirect_logs_ip ON redirect_logs(ip);
CREATE INDEX IF NOT EXISTS idx_redirect_logs_logged_at ON redirect_logs(logged_at);
