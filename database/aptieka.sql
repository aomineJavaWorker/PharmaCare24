SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;


/* =========================================================
   DZĒŠAM TIKAI APTIEKAS PROJEKTA TABULAS
   ========================================================= */

DROP TABLE IF EXISTS order_items;
DROP TABLE IF EXISTS prescription_items;
DROP TABLE IF EXISTS orders;
DROP TABLE IF EXISTS prescriptions;
DROP TABLE IF EXISTS medicines;
DROP TABLE IF EXISTS categories;
DROP TABLE IF EXISTS audit_logs;
DROP TABLE IF EXISTS users;


/* =========================================================
   LIETOTĀJI
   ========================================================= */

CREATE TABLE users (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    username VARCHAR(50) NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('admin', 'moderator') NOT NULL DEFAULT 'moderator',

    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    PRIMARY KEY (id),
    UNIQUE KEY uq_users_username (username)

) ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_unicode_ci;


/* =========================================================
   AUDIT LOGS
   ========================================================= */

CREATE TABLE audit_logs (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,

    user_id INT UNSIGNED DEFAULT NULL,

    username VARCHAR(50) NOT NULL,
    role VARCHAR(30) NOT NULL,

    action VARCHAR(50) NOT NULL,
    entity_type VARCHAR(50) NOT NULL,

    entity_id INT UNSIGNED DEFAULT NULL,

    details TEXT DEFAULT NULL,
    ip_address VARCHAR(45) DEFAULT NULL,

    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    PRIMARY KEY (id),

    KEY idx_audit_user_id (user_id),
    KEY idx_audit_created_at (created_at),

    CONSTRAINT fk_audit_user
        FOREIGN KEY (user_id)
        REFERENCES users(id)
        ON DELETE SET NULL
        ON UPDATE CASCADE

) ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_unicode_ci;


/* =========================================================
   KATEGORIJAS
   ========================================================= */

CREATE TABLE categories (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,

    name VARCHAR(100) NOT NULL,

    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    PRIMARY KEY (id),
    UNIQUE KEY uq_categories_name (name)

) ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_unicode_ci;


/* =========================================================
   ZĀLES
   ========================================================= */

CREATE TABLE medicines (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,

    category_id INT UNSIGNED DEFAULT NULL,

    name VARCHAR(150) NOT NULL,
    active_substance VARCHAR(150) DEFAULT NULL,
    api_search_name VARCHAR(150) DEFAULT NULL,
    manufacturer VARCHAR(150) DEFAULT NULL,

    dosage_form VARCHAR(80) DEFAULT NULL,
    strength VARCHAR(80) DEFAULT NULL,

    price DECIMAL(10,2) NOT NULL,

    stock_quantity INT UNSIGNED NOT NULL DEFAULT 0,

    prescription_required TINYINT(1) NOT NULL DEFAULT 0,

    image VARCHAR(255) DEFAULT NULL,

    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    deleted_at DATETIME DEFAULT NULL,

    PRIMARY KEY (id),

    KEY idx_medicines_category (category_id),

    CONSTRAINT fk_medicines_category
        FOREIGN KEY (category_id)
        REFERENCES categories(id)
        ON DELETE SET NULL
        ON UPDATE CASCADE

) ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_unicode_ci;


/* =========================================================
   RECEPTES
   ========================================================= */

CREATE TABLE prescriptions (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,

    prescription_number VARCHAR(50) NOT NULL,

    patient_name VARCHAR(150) NOT NULL,
    personal_code VARCHAR(12) NOT NULL,
    doctor_name VARCHAR(150) NOT NULL,

    issued_at DATE NOT NULL,
    valid_until DATE NOT NULL,

    status ENUM(
        'Aktīva',
        'Izmantota',
        'Beidzies termiņš',
        'Atcelta'
    ) NOT NULL DEFAULT 'Aktīva',

    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    deleted_at DATETIME DEFAULT NULL,

    PRIMARY KEY (id),

    UNIQUE KEY uq_prescription_number (
        prescription_number
    )

) ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_unicode_ci;


/* =========================================================
   RECEPŠU ZĀLES
   ========================================================= */

CREATE TABLE prescription_items (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,

    prescription_id INT UNSIGNED NOT NULL,
    medicine_id INT UNSIGNED NOT NULL,

    quantity INT UNSIGNED NOT NULL DEFAULT 1,

    PRIMARY KEY (id),

    KEY idx_prescription_items_prescription (
        prescription_id
    ),

    KEY idx_prescription_items_medicine (
        medicine_id
    ),

    CONSTRAINT fk_prescription_items_prescription
        FOREIGN KEY (prescription_id)
        REFERENCES prescriptions(id)
        ON DELETE CASCADE,

    CONSTRAINT fk_prescription_items_medicine
        FOREIGN KEY (medicine_id)
        REFERENCES medicines(id)
        ON DELETE RESTRICT

) ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_unicode_ci;


/* =========================================================
   PASŪTĪJUMI
   ========================================================= */

CREATE TABLE orders (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,

    customer_name VARCHAR(150) NOT NULL,
    customer_email VARCHAR(150) DEFAULT NULL,

    delivery_name VARCHAR(150) DEFAULT NULL,
    delivery_address VARCHAR(255) DEFAULT NULL,
    delivery_city VARCHAR(100) DEFAULT NULL,
    delivery_postal_code VARCHAR(20) DEFAULT NULL,
    delivery_country VARCHAR(10) DEFAULT NULL,

    status ENUM(
        'Jauns',
        'Apstrādē',
        'Gatavs',
        'Izsniegts',
        'Atcelts'
    ) NOT NULL DEFAULT 'Jauns',

    payment_status ENUM(
        'Neapmaksāts',
        'Apmaksāts',
        'Neizdevās'
    ) NOT NULL DEFAULT 'Neapmaksāts',

    stripe_session_id VARCHAR(255) DEFAULT NULL,

    total DECIMAL(10,2) NOT NULL DEFAULT 0.00,

    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    deleted_at DATETIME DEFAULT NULL,

    PRIMARY KEY (id),

    UNIQUE KEY uq_orders_stripe_session_id (
        stripe_session_id
    )

) ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_unicode_ci;


/* =========================================================
   PASŪTĪJUMU ZĀLES
   ========================================================= */

CREATE TABLE order_items (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,

    order_id INT UNSIGNED NOT NULL,

    medicine_id INT UNSIGNED NOT NULL,

    prescription_id INT UNSIGNED DEFAULT NULL,

    quantity INT UNSIGNED NOT NULL,

    unit_price DECIMAL(10,2) NOT NULL,

    PRIMARY KEY (id),

    KEY idx_order_items_order (
        order_id
    ),

    KEY idx_order_items_medicine (
        medicine_id
    ),

    KEY idx_order_items_prescription (
        prescription_id
    ),

    CONSTRAINT fk_order_items_order
        FOREIGN KEY (order_id)
        REFERENCES orders(id)
        ON DELETE CASCADE,

    CONSTRAINT fk_order_items_medicine
        FOREIGN KEY (medicine_id)
        REFERENCES medicines(id)
        ON DELETE RESTRICT,

    CONSTRAINT fk_order_items_prescription
        FOREIGN KEY (prescription_id)
        REFERENCES prescriptions(id)
        ON DELETE SET NULL
        ON UPDATE CASCADE

) ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_unicode_ci;


SET FOREIGN_KEY_CHECKS = 1;