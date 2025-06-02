-- --------------------------------------------------------
-- Inventory System Database Schema
-- --------------------------------------------------------

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

-- --------------------------------------------------------
-- DROP EXISTING TABLES
-- --------------------------------------------------------
DROP TABLE IF EXISTS inventory_transactions;
DROP TABLE IF EXISTS purchase_order_items;
DROP TABLE IF EXISTS purchase_orders;
DROP TABLE IF EXISTS sales_order_items;
DROP TABLE IF EXISTS sales_orders;
DROP TABLE IF EXISTS inventory_items;
DROP TABLE IF EXISTS categories;
DROP TABLE IF EXISTS suppliers;
DROP TABLE IF EXISTS customers;
DROP TABLE IF EXISTS users;
DROP TABLE IF EXISTS locations;

-- --------------------------------------------------------
-- TABLE STRUCTURE
-- --------------------------------------------------------

-- 
-- Table structure for locations
--
CREATE TABLE locations (
    location_id       INT             PRIMARY KEY AUTO_INCREMENT,
    location_name     VARCHAR(100)    NOT NULL,
    location_description VARCHAR(255),
    created_at        TIMESTAMP       DEFAULT CURRENT_TIMESTAMP,
    updated_at        TIMESTAMP       DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Table structure for users
--
CREATE TABLE users (
    user_id           INT             PRIMARY KEY AUTO_INCREMENT,
    username          VARCHAR(50)     NOT NULL UNIQUE,
    password          VARCHAR(255)    NOT NULL,
    full_name         VARCHAR(100)    NOT NULL,
    email             VARCHAR(100)    NOT NULL UNIQUE,
    role              ENUM('admin', 'manager', 'staff') NOT NULL,
    active            BOOLEAN         DEFAULT TRUE,
    created_at        TIMESTAMP       DEFAULT CURRENT_TIMESTAMP,
    updated_at        TIMESTAMP       DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Table structure for categories
--
CREATE TABLE categories (
    category_id       INT             PRIMARY KEY AUTO_INCREMENT,
    category_name     VARCHAR(100)    NOT NULL,
    category_description VARCHAR(255),
    created_at        TIMESTAMP       DEFAULT CURRENT_TIMESTAMP,
    updated_at        TIMESTAMP       DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Table structure for suppliers
--
CREATE TABLE suppliers (
    supplier_id       INT             PRIMARY KEY AUTO_INCREMENT,
    supplier_name     VARCHAR(100)    NOT NULL,
    contact_person    VARCHAR(100),
    contact_email     VARCHAR(100),
    contact_phone     VARCHAR(20),
    address           VARCHAR(255),
    city              VARCHAR(50),
    state             VARCHAR(50),
    postal_code       VARCHAR(20),
    country           VARCHAR(50),
    notes             TEXT,
    created_at        TIMESTAMP       DEFAULT CURRENT_TIMESTAMP,
    updated_at        TIMESTAMP       DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Table structure for customers
--
CREATE TABLE customers (
    customer_id       INT             PRIMARY KEY AUTO_INCREMENT,
    customer_name     VARCHAR(100)    NOT NULL,
    contact_person    VARCHAR(100),
    contact_email     VARCHAR(100),
    contact_phone     VARCHAR(20),
    address           VARCHAR(255),
    city              VARCHAR(50),
    state             VARCHAR(50),
    postal_code       VARCHAR(20),
    country           VARCHAR(50),
    notes             TEXT,
    created_at        TIMESTAMP       DEFAULT CURRENT_TIMESTAMP,
    updated_at        TIMESTAMP       DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Table structure for inventory_items
--
CREATE TABLE inventory_items (
    item_id           INT             PRIMARY KEY AUTO_INCREMENT,
    item_code         VARCHAR(50)     NOT NULL UNIQUE,
    item_name         VARCHAR(100)    NOT NULL,
    description       TEXT,
    category_id       INT,
    cost_price        DECIMAL(10,2)   NOT NULL,
    selling_price     DECIMAL(10,2)   NOT NULL,
    reorder_level     INT             NOT NULL DEFAULT 10,
    current_stock     INT             NOT NULL DEFAULT 0,
    created_at        TIMESTAMP       DEFAULT CURRENT_TIMESTAMP,
    updated_at        TIMESTAMP       DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(category_id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Table structure for purchase_orders
--
CREATE TABLE purchase_orders (
    po_id             INT             PRIMARY KEY AUTO_INCREMENT,
    po_number         VARCHAR(50)     NOT NULL UNIQUE,
    supplier_id       INT,
    order_date        DATE            NOT NULL,
    expected_date     DATE,
    status            ENUM('pending', 'approved', 'received', 'cancelled') DEFAULT 'pending',
    total_amount      DECIMAL(15,2)   DEFAULT 0,
    notes             TEXT,
    created_by        INT,
    created_at        TIMESTAMP       DEFAULT CURRENT_TIMESTAMP,
    updated_at        TIMESTAMP       DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (supplier_id) REFERENCES suppliers(supplier_id) ON DELETE SET NULL,
    FOREIGN KEY (created_by) REFERENCES users(user_id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Table structure for purchase_order_items
--
CREATE TABLE purchase_order_items (
    po_item_id        INT             PRIMARY KEY AUTO_INCREMENT,
    po_id             INT             NOT NULL,
    item_id           INT             NOT NULL,
    quantity          INT             NOT NULL,
    unit_price        DECIMAL(10,2)   NOT NULL,
    received_quantity INT             DEFAULT 0,
    location_id       INT,
    status            ENUM('pending', 'partially_received', 'fully_received', 'cancelled') DEFAULT 'pending',
    created_at        TIMESTAMP       DEFAULT CURRENT_TIMESTAMP,
    updated_at        TIMESTAMP       DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (po_id) REFERENCES purchase_orders(po_id) ON DELETE CASCADE,
    FOREIGN KEY (item_id) REFERENCES inventory_items(item_id) ON DELETE RESTRICT,
    FOREIGN KEY (location_id) REFERENCES locations(location_id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Table structure for sales_orders
--
CREATE TABLE sales_orders (
    so_id             INT             PRIMARY KEY AUTO_INCREMENT,
    so_number         VARCHAR(50)     NOT NULL UNIQUE,
    customer_id       INT,
    order_date        DATE            NOT NULL,
    shipping_date     DATE,
    status            ENUM('pending', 'processing', 'shipped', 'delivered', 'cancelled') DEFAULT 'pending',
    total_amount      DECIMAL(15,2)   DEFAULT 0,
    notes             TEXT,
    created_by        INT,
    created_at        TIMESTAMP       DEFAULT CURRENT_TIMESTAMP,
    updated_at        TIMESTAMP       DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES customers(customer_id) ON DELETE SET NULL,
    FOREIGN KEY (created_by) REFERENCES users(user_id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Table structure for sales_order_items
--
CREATE TABLE sales_order_items (
    so_item_id        INT             PRIMARY KEY AUTO_INCREMENT,
    so_id             INT             NOT NULL,
    item_id           INT             NOT NULL,
    quantity          INT             NOT NULL,
    unit_price        DECIMAL(10,2)   NOT NULL,
    shipped_quantity  INT             DEFAULT 0,
    location_id       INT,
    status            ENUM('pending', 'partially_shipped', 'fully_shipped', 'cancelled') DEFAULT 'pending',
    created_at        TIMESTAMP       DEFAULT CURRENT_TIMESTAMP,
    updated_at        TIMESTAMP       DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (so_id) REFERENCES sales_orders(so_id) ON DELETE CASCADE,
    FOREIGN KEY (item_id) REFERENCES inventory_items(item_id) ON DELETE RESTRICT,
    FOREIGN KEY (location_id) REFERENCES locations(location_id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Table structure for inventory_transactions
--
CREATE TABLE inventory_transactions (
    transaction_id    INT             PRIMARY KEY AUTO_INCREMENT,
    item_id           INT             NOT NULL,
    transaction_type  ENUM('purchase', 'sale', 'adjustment', 'transfer', 'return') NOT NULL,
    quantity          INT             NOT NULL,  -- positive for inbound, negative for outbound
    reference_id      INT,            -- PO item ID, SO item ID, or adjustment ID
    reference_type    VARCHAR(50),    -- 'purchase_order', 'sales_order', 'adjustment'
    from_location_id  INT,
    to_location_id    INT,
    transaction_date  TIMESTAMP       DEFAULT CURRENT_TIMESTAMP,
    unit_cost         DECIMAL(10,2),
    notes             TEXT,
    created_by        INT,
    FOREIGN KEY (item_id) REFERENCES inventory_items(item_id) ON DELETE CASCADE,
    FOREIGN KEY (from_location_id) REFERENCES locations(location_id) ON DELETE SET NULL,
    FOREIGN KEY (to_location_id) REFERENCES locations(location_id) ON DELETE SET NULL,
    FOREIGN KEY (created_by) REFERENCES users(user_id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- TRIGGERS
-- --------------------------------------------------------

--
-- Trigger for recording inbound inventory from purchase orders
--
DELIMITER $$
CREATE TRIGGER after_purchase_item_received
AFTER UPDATE ON purchase_order_items
FOR EACH ROW
BEGIN
    -- Local variables
    DECLARE qty_difference INT;
    
    -- Calculate the difference between new and old received quantities
    SET qty_difference = NEW.received_quantity - OLD.received_quantity;
    
    -- Only proceed if there's an actual change in received quantity
    IF qty_difference != 0 THEN
        -- Update the current stock in inventory_items
        UPDATE inventory_items 
        SET current_stock = current_stock + qty_difference
        WHERE item_id = NEW.item_id;
        
        -- Record the transaction in inventory_transactions
        INSERT INTO inventory_transactions (
            item_id, 
            transaction_type, 
            quantity, 
            reference_id, 
            reference_type, 
            to_location_id, 
            unit_cost, 
            notes
        ) 
        SELECT
            NEW.item_id,
            'purchase',
            qty_difference,
            NEW.po_item_id,
            'purchase_order',
            NEW.location_id,
            NEW.unit_price,
            CONCAT('Received from PO #', po_number)
        FROM 
            purchase_orders 
        WHERE 
            po_id = NEW.po_id;
    END IF;
END$$
DELIMITER ;

--
-- Trigger for recording outbound inventory from sales orders
--
DELIMITER $$
CREATE TRIGGER after_sales_item_shipped
AFTER UPDATE ON sales_order_items
FOR EACH ROW
BEGIN
    -- Local variables
    DECLARE qty_difference INT;
    
    -- Calculate the difference between new and old shipped quantities
    SET qty_difference = NEW.shipped_quantity - OLD.shipped_quantity;
    
    -- Only proceed if there's an actual change in shipped quantity
    IF qty_difference != 0 THEN
        -- Update the current stock in inventory_items
        UPDATE inventory_items 
        SET current_stock = current_stock - qty_difference
        WHERE item_id = NEW.item_id;
        
        -- Record the transaction in inventory_transactions
        INSERT INTO inventory_transactions (
            item_id, 
            transaction_type, 
            quantity, 
            reference_id, 
            reference_type, 
            from_location_id, 
            unit_cost, 
            notes
        ) 
        SELECT
            NEW.item_id,
            'sale',
            -qty_difference,  -- negative because it's outbound
            NEW.so_item_id,
            'sales_order',
            NEW.location_id,
            NEW.unit_price,
            CONCAT('Shipped for SO #', so_number)
        FROM 
            sales_orders 
        WHERE 
            so_id = NEW.so_id;
    END IF;
END$$
DELIMITER ;

-- --------------------------------------------------------
-- INITIAL DATA
-- --------------------------------------------------------

--
-- Insert default location
--
INSERT INTO locations (location_name, location_description) 
VALUES ('Main Warehouse', 'Primary storage location');

--
-- Insert default admin user (password: admin123)
--
INSERT INTO users (username, password, full_name, email, role) 
VALUES ('admin', '$2y$10$8IVCgxX8RmpUVQCZO3EoK.FV1NM5tUdcQwXldJWF5JwjzlKiI5HRe', 'System Administrator', 'admin@example.com', 'admin');

--
-- Insert sample categories
--
INSERT INTO categories (category_name) VALUES 
('Electronics'), 
('Office Supplies'), 
('Furniture'), 
('Raw Materials'), 
('Tools');
