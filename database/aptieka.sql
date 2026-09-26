CREATE DATABASE IF NOT EXISTS aptieka
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE aptieka;


CREATE TABLE users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role VARCHAR(30) NOT NULL DEFAULT 'employee',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP
);


CREATE TABLE categories (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP
);


CREATE TABLE medicines (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    category_id INT UNSIGNED NULL,

    name VARCHAR(150) NOT NULL,
    active_substance VARCHAR(150) NULL,
    manufacturer VARCHAR(150) NULL,
    dosage_form VARCHAR(80) NULL,
    strength VARCHAR(80) NULL,

    price DECIMAL(10,2) NOT NULL,
    stock_quantity INT UNSIGNED NOT NULL DEFAULT 0,

    prescription_required TINYINT(1) NOT NULL DEFAULT 0,

    image VARCHAR(255) NULL,

    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_medicines_category
        FOREIGN KEY (category_id)
        REFERENCES categories(id)
        ON UPDATE CASCADE
        ON DELETE SET NULL
);


CREATE TABLE prescriptions (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    prescription_number VARCHAR(50) NOT NULL UNIQUE,
    patient_name VARCHAR(150) NOT NULL,
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
        ON UPDATE CURRENT_TIMESTAMP
);


CREATE TABLE prescription_items (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    prescription_id INT UNSIGNED NOT NULL,
    medicine_id INT UNSIGNED NOT NULL,
    quantity INT UNSIGNED NOT NULL DEFAULT 1,

    CONSTRAINT fk_prescription_items_prescription
        FOREIGN KEY (prescription_id)
        REFERENCES prescriptions(id)
        ON DELETE CASCADE,

    CONSTRAINT fk_prescription_items_medicine
        FOREIGN KEY (medicine_id)
        REFERENCES medicines(id)
        ON DELETE RESTRICT
);


CREATE TABLE orders (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    customer_name VARCHAR(150) NOT NULL,
    customer_email VARCHAR(150) NULL,

    status ENUM(
        'Jauns',
        'Apstrādē',
        'Gatavs',
        'Izsniegts',
        'Atcelts'
    ) NOT NULL DEFAULT 'Jauns',

    total DECIMAL(10,2) NOT NULL DEFAULT 0.00,

    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP
);


CREATE TABLE order_items (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    order_id INT UNSIGNED NOT NULL,
    medicine_id INT UNSIGNED NOT NULL,

    quantity INT UNSIGNED NOT NULL,
    unit_price DECIMAL(10,2) NOT NULL,

    CONSTRAINT fk_order_items_order
        FOREIGN KEY (order_id)
        REFERENCES orders(id)
        ON DELETE CASCADE,

    CONSTRAINT fk_order_items_medicine
        FOREIGN KEY (medicine_id)
        REFERENCES medicines(id)
        ON DELETE RESTRICT
);