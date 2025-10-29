-- Android Platform Control Dashboard - MySQL Database Setup
-- Run this SQL in your Hostinger MySQL database

-- Create database (if needed)
-- CREATE DATABASE IF NOT EXISTS android_control DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
-- USE android_control;

-- Drop existing tables if they exist (careful in production!)
DROP TABLE IF EXISTS notification_events;
DROP TABLE IF EXISTS analytics_events;
DROP TABLE IF EXISTS devices;
DROP TABLE IF EXISTS notifications;
DROP TABLE IF EXISTS switching_rules;
DROP TABLE IF EXISTS admob_accounts;
DROP TABLE IF EXISTS apps;
DROP TABLE IF EXISTS admin_users;

-- Admin Users Table
CREATE TABLE admin_users (
    id VARCHAR(36) PRIMARY KEY,
    username TEXT NOT NULL,
    password TEXT NOT NULL,
    role VARCHAR(50) NOT NULL DEFAULT 'viewer',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_username (username(255))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Android Apps Table
CREATE TABLE apps (
    id VARCHAR(36) PRIMARY KEY,
    package_name TEXT NOT NULL,
    app_name TEXT NOT NULL,
    description TEXT NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'active',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY unique_package (package_name(255)),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- AdMob Accounts Table
CREATE TABLE admob_accounts (
    id VARCHAR(36) PRIMARY KEY,
    app_id VARCHAR(36) NOT NULL,
    account_name TEXT NOT NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'active',
    priority INT NOT NULL DEFAULT 1,
    weight INT NOT NULL DEFAULT 50,
    banner_id TEXT NULL,
    interstitial_id TEXT NULL,
    rewarded_id TEXT NULL,
    app_open_id TEXT NULL,
    native_id TEXT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (app_id) REFERENCES apps(id) ON DELETE CASCADE,
    INDEX idx_app_id (app_id),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Switching Rules Table
CREATE TABLE switching_rules (
    id VARCHAR(36) PRIMARY KEY,
    app_id VARCHAR(36) NOT NULL UNIQUE,
    strategy VARCHAR(50) NOT NULL DEFAULT 'weighted_random',
    rotation_interval VARCHAR(20) NOT NULL DEFAULT 'daily',
    fallback_enabled BOOLEAN NOT NULL DEFAULT TRUE,
    ab_testing_enabled BOOLEAN NOT NULL DEFAULT FALSE,
    geographic_rules JSON NULL,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (app_id) REFERENCES apps(id) ON DELETE CASCADE,
    UNIQUE KEY unique_app (app_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Notifications Table
CREATE TABLE notifications (
    id VARCHAR(36) PRIMARY KEY,
    app_id VARCHAR(36) NOT NULL,
    title TEXT NOT NULL,
    message TEXT NOT NULL,
    type VARCHAR(20) NOT NULL DEFAULT 'popup',
    priority VARCHAR(20) NOT NULL DEFAULT 'normal',
    status VARCHAR(20) NOT NULL DEFAULT 'draft',
    target_countries JSON NULL,
    target_app_versions JSON NULL,
    min_android_version INT NULL,
    user_segments JSON NULL,
    start_date TIMESTAMP NULL,
    end_date TIMESTAMP NULL,
    recurring BOOLEAN NOT NULL DEFAULT FALSE,
    frequency VARCHAR(20) NOT NULL DEFAULT 'once',
    image_url TEXT NULL,
    action_button_text TEXT NULL,
    action_type VARCHAR(20) NULL,
    action_value TEXT NULL,
    cancelable BOOLEAN NOT NULL DEFAULT TRUE,
    max_displays INT NOT NULL DEFAULT 1,
    display_interval_hours INT NOT NULL DEFAULT 24,
    show_on_app_launch BOOLEAN NOT NULL DEFAULT FALSE,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (app_id) REFERENCES apps(id) ON DELETE CASCADE,
    INDEX idx_app_id (app_id),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Devices Table
CREATE TABLE devices (
    id VARCHAR(36) PRIMARY KEY,
    app_id VARCHAR(36) NOT NULL,
    fcm_token TEXT NOT NULL,
    country VARCHAR(10) NULL,
    app_version VARCHAR(20) NULL,
    android_version INT NULL,
    device_manufacturer VARCHAR(100) NULL,
    device_model VARCHAR(100) NULL,
    last_seen TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (app_id) REFERENCES apps(id) ON DELETE CASCADE,
    INDEX idx_app_id (app_id),
    INDEX idx_country (country)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Analytics Events Table
CREATE TABLE analytics_events (
    id VARCHAR(36) PRIMARY KEY,
    app_id VARCHAR(36) NOT NULL,
    account_id VARCHAR(36) NULL,
    event_type VARCHAR(50) NOT NULL,
    ad_type VARCHAR(50) NULL,
    value INT NULL,
    country VARCHAR(10) NULL,
    timestamp TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (app_id) REFERENCES apps(id) ON DELETE CASCADE,
    FOREIGN KEY (account_id) REFERENCES admob_accounts(id) ON DELETE SET NULL,
    INDEX idx_app_id (app_id),
    INDEX idx_event_type (event_type),
    INDEX idx_timestamp (timestamp)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Notification Events Table
CREATE TABLE notification_events (
    id VARCHAR(36) PRIMARY KEY,
    notification_id VARCHAR(36) NOT NULL,
    device_id VARCHAR(36) NULL,
    event_type VARCHAR(50) NOT NULL,
    timestamp TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (notification_id) REFERENCES notifications(id) ON DELETE CASCADE,
    FOREIGN KEY (device_id) REFERENCES devices(id) ON DELETE SET NULL,
    INDEX idx_notification_id (notification_id),
    INDEX idx_event_type (event_type),
    INDEX idx_timestamp (timestamp)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default admin user (password: admin123)
-- Password is bcrypt hashed version of "admin123"
INSERT INTO admin_users (id, username, password, role, created_at) VALUES 
(UUID(), 'admin', '$2b$10$xHjzPqF5K5W5Z5gZnW5Z5OqF5K5W5Z5gZnW5Z5OqF5K5W5Z5gZnW5u', 'super_admin', CURRENT_TIMESTAMP);

-- Sample app (optional - remove if not needed)
INSERT INTO apps (id, package_name, app_name, description, status, created_at, updated_at) VALUES
(UUID(), 'com.example.app', 'Sample App', 'This is a sample application for testing', 'active', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
