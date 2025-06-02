-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 02, 2025 at 11:36 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `inventory_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `category_id` int(11) NOT NULL,
  `category_name` varchar(100) NOT NULL,
  `category_description` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`category_id`, `category_name`, `category_description`, `created_at`, `updated_at`) VALUES
(1, 'Electronics', NULL, '2025-06-02 04:17:37', '2025-06-02 04:17:37'),
(2, 'Office Supplies', NULL, '2025-06-02 04:17:37', '2025-06-02 04:17:37'),
(3, 'Furniture', NULL, '2025-06-02 04:17:37', '2025-06-02 04:17:37'),
(4, 'Raw Materials', NULL, '2025-06-02 04:17:37', '2025-06-02 04:17:37'),
(5, 'Tools', NULL, '2025-06-02 04:17:37', '2025-06-02 04:17:37');

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `customer_id` int(11) NOT NULL,
  `customer_name` varchar(100) NOT NULL,
  `contact_person` varchar(100) DEFAULT NULL,
  `contact_email` varchar(100) DEFAULT NULL,
  `contact_phone` varchar(20) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `city` varchar(50) DEFAULT NULL,
  `state` varchar(50) DEFAULT NULL,
  `postal_code` varchar(20) DEFAULT NULL,
  `country` varchar(50) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `inventory_items`
--

CREATE TABLE `inventory_items` (
  `item_id` int(11) NOT NULL,
  `item_code` varchar(50) NOT NULL,
  `item_name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `cost_price` decimal(10,2) NOT NULL,
  `selling_price` decimal(10,2) NOT NULL,
  `reorder_level` int(11) NOT NULL DEFAULT 10,
  `current_stock` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `inventory_transactions`
--

CREATE TABLE `inventory_transactions` (
  `transaction_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `transaction_type` enum('purchase','sale','adjustment','transfer','return') NOT NULL,
  `quantity` int(11) NOT NULL,
  `reference_id` int(11) DEFAULT NULL,
  `reference_type` varchar(50) DEFAULT NULL,
  `from_location_id` int(11) DEFAULT NULL,
  `to_location_id` int(11) DEFAULT NULL,
  `transaction_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `unit_cost` decimal(10,2) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `locations`
--

CREATE TABLE `locations` (
  `location_id` int(11) NOT NULL,
  `location_name` varchar(100) NOT NULL,
  `location_description` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `locations`
--

INSERT INTO `locations` (`location_id`, `location_name`, `location_description`, `created_at`, `updated_at`) VALUES
(1, 'Main Warehouse', 'Primary storage location', '2025-06-02 04:17:37', '2025-06-02 04:17:37');

-- --------------------------------------------------------

--
-- Table structure for table `purchase_orders`
--

CREATE TABLE `purchase_orders` (
  `po_id` int(11) NOT NULL,
  `po_number` varchar(50) NOT NULL,
  `supplier_id` int(11) DEFAULT NULL,
  `order_date` date NOT NULL,
  `expected_date` date DEFAULT NULL,
  `status` enum('pending','approved','received','cancelled') DEFAULT 'pending',
  `total_amount` decimal(15,2) DEFAULT 0.00,
  `notes` text DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `purchase_order_items`
--

CREATE TABLE `purchase_order_items` (
  `po_item_id` int(11) NOT NULL,
  `po_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `unit_price` decimal(10,2) NOT NULL,
  `received_quantity` int(11) DEFAULT 0,
  `location_id` int(11) DEFAULT NULL,
  `status` enum('pending','partially_received','fully_received','cancelled') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Triggers `purchase_order_items`
--
DELIMITER $$
CREATE TRIGGER `after_purchase_item_received` AFTER UPDATE ON `purchase_order_items` FOR EACH ROW BEGIN
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
        ) VALUES (
            NEW.item_id,
            'purchase',
            qty_difference,
            NEW.po_item_id,
            'purchase_order',
            NEW.location_id,
            NEW.unit_price,
            CONCAT('Received from PO #', (SELECT po_number FROM purchase_orders WHERE po_id = NEW.po_id))
        );
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `sales_orders`
--

CREATE TABLE `sales_orders` (
  `so_id` int(11) NOT NULL,
  `so_number` varchar(50) NOT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `order_date` date NOT NULL,
  `shipping_date` date DEFAULT NULL,
  `status` enum('pending','processing','shipped','delivered','cancelled') DEFAULT 'pending',
  `total_amount` decimal(15,2) DEFAULT 0.00,
  `notes` text DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sales_order_items`
--

CREATE TABLE `sales_order_items` (
  `so_item_id` int(11) NOT NULL,
  `so_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `unit_price` decimal(10,2) NOT NULL,
  `shipped_quantity` int(11) DEFAULT 0,
  `location_id` int(11) DEFAULT NULL,
  `status` enum('pending','partially_shipped','fully_shipped','cancelled') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Triggers `sales_order_items`
--
DELIMITER $$
CREATE TRIGGER `after_sales_item_shipped` AFTER UPDATE ON `sales_order_items` FOR EACH ROW BEGIN
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
        ) VALUES (
            NEW.item_id,
            'sale',
            -qty_difference,  -- negative because it's outbound
            NEW.so_item_id,
            'sales_order',
            NEW.location_id,
            NEW.unit_price,
            CONCAT('Shipped for SO #', (SELECT so_number FROM sales_orders WHERE so_id = NEW.so_id))
        );
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `suppliers`
--

CREATE TABLE `suppliers` (
  `supplier_id` int(11) NOT NULL,
  `supplier_name` varchar(100) NOT NULL,
  `contact_person` varchar(100) DEFAULT NULL,
  `contact_email` varchar(100) DEFAULT NULL,
  `contact_phone` varchar(20) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `city` varchar(50) DEFAULT NULL,
  `state` varchar(50) DEFAULT NULL,
  `postal_code` varchar(20) DEFAULT NULL,
  `country` varchar(50) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `role` enum('admin','manager','staff') NOT NULL,
  `active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `password`, `full_name`, `email`, `role`, `active`, `created_at`, `updated_at`) VALUES
(1, 'admin', '$2y$10$8IVCgxX8RmpUVQCZO3EoK.FV1NM5tUdcQwXldJWF5JwjzlKiI5HRe', 'System Administrator', 'admin@example.com', 'admin', 1, '2025-06-02 04:17:37', '2025-06-02 04:17:37');

--
-- Dumping data for table `suppliers`
--

INSERT INTO `suppliers` (`supplier_id`, `supplier_name`, `contact_person`, `contact_email`, `contact_phone`, `address`, `city`, `state`, `postal_code`, `country`, `notes`, `created_at`, `updated_at`) VALUES
(1, 'TechCorp Solutions', 'John Smith', 'john.smith@techcorp.com', '+1-555-0101', '123 Technology Drive', 'San Francisco', 'California', '94102', 'USA', 'Primary electronics supplier', NOW(), NOW()),
(2, 'Office Plus Supplies', 'Sarah Johnson', 'sarah@officeplus.com', '+1-555-0202', '456 Business Avenue', 'New York', 'New York', '10001', 'USA', 'Office supplies and stationery', NOW(), NOW()),
(3, 'Global Furniture Co.', 'Mike Chen', 'mike.chen@globalfurniture.com', '+1-555-0303', '789 Industrial Park', 'Chicago', 'Illinois', '60601', 'USA', 'Office and warehouse furniture', NOW(), NOW()),
(4, 'Steel & Materials Inc.', 'Lisa Rodriguez', 'lisa@steelmaterials.com', '+1-555-0404', '321 Manufacturing Blvd', 'Detroit', 'Michigan', '48201', 'USA', 'Raw materials and steel products', NOW(), NOW()),
(5, 'ToolMaster Equipment', 'David Wilson', 'david@toolmaster.com', '+1-555-0505', '654 Workshop Street', 'Houston', 'Texas', '77001', 'USA', 'Professional tools and equipment', NOW(), NOW());

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`customer_id`, `customer_name`, `contact_person`, `contact_email`, `contact_phone`, `address`, `city`, `state`, `postal_code`, `country`, `notes`, `created_at`, `updated_at`) VALUES
(1, 'ABC Manufacturing', 'Robert Taylor', 'robert.taylor@abcmfg.com', '+1-555-1001', '100 Factory Lane', 'Los Angeles', 'California', '90001', 'USA', 'Regular customer - monthly orders', NOW(), NOW()),
(2, 'XYZ Retail Chain', 'Emily Davis', 'emily.davis@xyzretail.com', '+1-555-1002', '200 Commerce Street', 'Miami', 'Florida', '33101', 'USA', 'Large retail chain - bulk orders', NOW(), NOW()),
(3, 'Tech Startup Hub', 'Alex Morgan', 'alex@techstartup.com', '+1-555-1003', '300 Innovation Drive', 'Austin', 'Texas', '73301', 'USA', 'Technology startup - office supplies', NOW(), NOW()),
(4, 'Construction Plus LLC', 'Jennifer Brown', 'jen.brown@constructionplus.com', '+1-555-1004', '400 Builder Avenue', 'Denver', 'Colorado', '80201', 'USA', 'Construction company - tools and materials', NOW(), NOW()),
(5, 'Metro Office Solutions', 'Kevin Lee', 'kevin@metrooffice.com', '+1-555-1005', '500 Business Plaza', 'Seattle', 'Washington', '98101', 'USA', 'Office furniture and supplies', NOW(), NOW());

--
-- Dumping data for table `inventory_items`
--

INSERT INTO `inventory_items` (`item_id`, `item_code`, `item_name`, `description`, `category_id`, `cost_price`, `selling_price`, `reorder_level`, `current_stock`, `created_at`, `updated_at`) VALUES
(1, 'ELC-001', 'Laptop Computer', 'Business grade laptop with 16GB RAM and 512GB SSD', 1, 750.00, 1200.00, 5, 25, NOW(), NOW()),
(2, 'ELC-002', 'Wireless Mouse', 'Ergonomic wireless optical mouse', 1, 15.00, 35.00, 20, 50, NOW(), NOW()),
(3, 'ELC-003', 'LED Monitor 24"', '24-inch Full HD LED monitor', 1, 120.00, 220.00, 10, 18, NOW(), NOW()),
(4, 'OFS-001', 'A4 Copy Paper', 'White copy paper 80gsm - 500 sheets pack', 2, 4.50, 8.99, 50, 200, NOW(), NOW()),
(5, 'OFS-002', 'Blue Ballpoint Pen', 'Medium tip blue ink ballpoint pen', 2, 0.25, 0.75, 100, 500, NOW(), NOW()),
(6, 'OFS-003', 'Stapler Heavy Duty', 'Heavy duty stapler for up to 50 sheets', 2, 12.00, 25.00, 15, 30, NOW(), NOW()),
(7, 'FUR-001', 'Office Chair Executive', 'Ergonomic executive office chair with lumbar support', 3, 180.00, 350.00, 8, 15, NOW(), NOW()),
(8, 'FUR-002', 'Desk Wooden L-Shape', 'L-shaped wooden desk with drawers', 3, 220.00, 450.00, 5, 12, NOW(), NOW()),
(9, 'FUR-003', 'Filing Cabinet 4-Drawer', 'Metal filing cabinet with 4 drawers and lock', 3, 95.00, 180.00, 10, 22, NOW(), NOW()),
(10, 'RAW-001', 'Steel Rod 10mm', 'Construction steel rod 10mm diameter - per meter', 4, 3.50, 6.00, 100, 500, NOW(), NOW()),
(11, 'RAW-002', 'Aluminum Sheet 2mm', 'Aluminum sheet 2mm thickness - per square meter', 4, 8.00, 15.00, 50, 150, NOW(), NOW()),
(12, 'TOL-001', 'Electric Drill 18V', 'Cordless electric drill with battery and charger', 5, 85.00, 160.00, 8, 20, NOW(), NOW()),
(13, 'TOL-002', 'Hammer Steel 500g', 'Steel claw hammer 500g with fiberglass handle', 5, 12.00, 24.00, 25, 45, NOW(), NOW()),
(14, 'TOL-003', 'Screwdriver Set', 'Professional screwdriver set with 12 pieces', 5, 18.00, 35.00, 15, 35, NOW(), NOW());

--
-- Dumping data for table `purchase_orders`
--

INSERT INTO `purchase_orders` (`po_id`, `po_number`, `supplier_id`, `order_date`, `expected_date`, `status`, `total_amount`, `notes`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 'PO-2025-001', 1, '2025-05-15', '2025-05-25', 'received', 19250.00, 'Monthly electronics order', 1, '2025-05-15 09:00:00', '2025-05-25 14:30:00'),
(2, 'PO-2025-002', 2, '2025-05-20', '2025-05-30', 'received', 1487.50, 'Office supplies restock', 1, '2025-05-20 10:15:00', '2025-05-30 11:45:00'),
(3, 'PO-2025-003', 3, '2025-05-25', '2025-06-05', 'approved', 5400.00, 'Furniture for new office section', 1, '2025-05-25 13:20:00', '2025-05-25 13:20:00'),
(4, 'PO-2025-004', 5, '2025-06-01', '2025-06-10', 'pending', 2850.00, 'Tool maintenance and new equipment', 1, '2025-06-01 08:30:00', '2025-06-01 08:30:00');

--
-- Dumping data for table `purchase_order_items`
--

INSERT INTO `purchase_order_items` (`po_item_id`, `po_id`, `item_id`, `quantity`, `unit_price`, `received_quantity`, `location_id`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 15, 750.00, 15, 1, 'fully_received', '2025-05-15 09:00:00', '2025-05-25 14:30:00'),
(2, 1, 2, 30, 15.00, 30, 1, 'fully_received', '2025-05-15 09:00:00', '2025-05-25 14:30:00'),
(3, 1, 3, 20, 120.00, 20, 1, 'fully_received', '2025-05-15 09:00:00', '2025-05-25 14:30:00'),
(4, 2, 4, 150, 4.50, 150, 1, 'fully_received', '2025-05-20 10:15:00', '2025-05-30 11:45:00'),
(5, 2, 5, 400, 0.25, 400, 1, 'fully_received', '2025-05-20 10:15:00', '2025-05-30 11:45:00'),
(6, 2, 6, 25, 12.00, 25, 1, 'fully_received', '2025-05-20 10:15:00', '2025-05-30 11:45:00'),
(7, 3, 7, 12, 180.00, 0, 1, 'pending', '2025-05-25 13:20:00', '2025-05-25 13:20:00'),
(8, 3, 8, 8, 220.00, 0, 1, 'pending', '2025-05-25 13:20:00', '2025-05-25 13:20:00'),
(9, 3, 9, 15, 95.00, 0, 1, 'pending', '2025-05-25 13:20:00', '2025-05-25 13:20:00'),
(10, 4, 12, 15, 85.00, 0, 1, 'pending', '2025-06-01 08:30:00', '2025-06-01 08:30:00'),
(11, 4, 13, 30, 12.00, 0, 1, 'pending', '2025-06-01 08:30:00', '2025-06-01 08:30:00'),
(12, 4, 14, 25, 18.00, 0, 1, 'pending', '2025-06-01 08:30:00', '2025-06-01 08:30:00');

--
-- Dumping data for table `sales_orders`
--

INSERT INTO `sales_orders` (`so_id`, `so_number`, `customer_id`, `order_date`, `shipping_date`, `status`, `total_amount`, `notes`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 'SO-2025-001', 1, '2025-05-28', '2025-05-30', 'delivered', 14400.00, 'Regular monthly order', 1, '2025-05-28 09:30:00', '2025-05-30 16:00:00'),
(2, 'SO-2025-002', 2, '2025-06-01', '2025-06-03', 'shipped', 8975.00, 'Bulk order for retail stores', 1, '2025-06-01 11:00:00', '2025-06-03 10:30:00'),
(3, 'SO-2025-003', 3, '2025-06-02', NULL, 'processing', 2250.00, 'Startup office setup', 1, '2025-06-02 14:15:00', '2025-06-02 14:15:00');

--
-- Dumping data for table `sales_order_items`
--

INSERT INTO `sales_order_items` (`so_item_id`, `so_id`, `item_id`, `quantity`, `unit_price`, `shipped_quantity`, `location_id`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 10, 1200.00, 10, 1, 'fully_shipped', '2025-05-28 09:30:00', '2025-05-30 16:00:00'),
(2, 1, 3, 8, 220.00, 8, 1, 'fully_shipped', '2025-05-28 09:30:00', '2025-05-30 16:00:00'),
(3, 1, 7, 2, 350.00, 2, 1, 'fully_shipped', '2025-05-28 09:30:00', '2025-05-30 16:00:00'),
(4, 2, 4, 100, 8.99, 100, 1, 'fully_shipped', '2025-06-01 11:00:00', '2025-06-03 10:30:00'),
(5, 2, 5, 200, 0.75, 200, 1, 'fully_shipped', '2025-06-01 11:00:00', '2025-06-03 10:30:00'),
(6, 2, 6, 15, 25.00, 15, 1, 'fully_shipped', '2025-06-01 11:00:00', '2025-06-03 10:30:00'),
(7, 2, 10, 500, 6.00, 500, 1, 'fully_shipped', '2025-06-01 11:00:00', '2025-06-03 10:30:00'),
(8, 2, 11, 100, 15.00, 100, 1, 'fully_shipped', '2025-06-01 11:00:00', '2025-06-03 10:30:00'),
(9, 3, 2, 15, 35.00, 0, 1, 'pending', '2025-06-02 14:15:00', '2025-06-02 14:15:00'),
(10, 3, 7, 5, 350.00, 0, 1, 'pending', '2025-06-02 14:15:00', '2025-06-02 14:15:00');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`category_id`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`customer_id`);

--
-- Indexes for table `inventory_items`
--
ALTER TABLE `inventory_items`
  ADD PRIMARY KEY (`item_id`),
  ADD UNIQUE KEY `item_code` (`item_code`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `inventory_transactions`
--
ALTER TABLE `inventory_transactions`
  ADD PRIMARY KEY (`transaction_id`),
  ADD KEY `item_id` (`item_id`),
  ADD KEY `from_location_id` (`from_location_id`),
  ADD KEY `to_location_id` (`to_location_id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `locations`
--
ALTER TABLE `locations`
  ADD PRIMARY KEY (`location_id`);

--
-- Indexes for table `purchase_orders`
--
ALTER TABLE `purchase_orders`
  ADD PRIMARY KEY (`po_id`),
  ADD UNIQUE KEY `po_number` (`po_number`),
  ADD KEY `supplier_id` (`supplier_id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `purchase_order_items`
--
ALTER TABLE `purchase_order_items`
  ADD PRIMARY KEY (`po_item_id`),
  ADD KEY `po_id` (`po_id`),
  ADD KEY `item_id` (`item_id`),
  ADD KEY `location_id` (`location_id`);

--
-- Indexes for table `sales_orders`
--
ALTER TABLE `sales_orders`
  ADD PRIMARY KEY (`so_id`),
  ADD UNIQUE KEY `so_number` (`so_number`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `sales_order_items`
--
ALTER TABLE `sales_order_items`
  ADD PRIMARY KEY (`so_item_id`),
  ADD KEY `so_id` (`so_id`),
  ADD KEY `item_id` (`item_id`),
  ADD KEY `location_id` (`location_id`);

--
-- Indexes for table `suppliers`
--
ALTER TABLE `suppliers`
  ADD PRIMARY KEY (`supplier_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `customer_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `inventory_items`
--
ALTER TABLE `inventory_items`
  MODIFY `item_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `inventory_transactions`
--
ALTER TABLE `inventory_transactions`
  MODIFY `transaction_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `locations`
--
ALTER TABLE `locations`
  MODIFY `location_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `purchase_orders`
--
ALTER TABLE `purchase_orders`
  MODIFY `po_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `purchase_order_items`
--
ALTER TABLE `purchase_order_items`
  MODIFY `po_item_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sales_orders`
--
ALTER TABLE `sales_orders`
  MODIFY `so_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sales_order_items`
--
ALTER TABLE `sales_order_items`
  MODIFY `so_item_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `suppliers`
--
ALTER TABLE `suppliers`
  MODIFY `supplier_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `inventory_items`
--
ALTER TABLE `inventory_items`
  ADD CONSTRAINT `inventory_items_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`category_id`) ON DELETE SET NULL;

--
-- Constraints for table `inventory_transactions`
--
ALTER TABLE `inventory_transactions`
  ADD CONSTRAINT `inventory_transactions_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `inventory_items` (`item_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `inventory_transactions_ibfk_2` FOREIGN KEY (`from_location_id`) REFERENCES `locations` (`location_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `inventory_transactions_ibfk_3` FOREIGN KEY (`to_location_id`) REFERENCES `locations` (`location_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `inventory_transactions_ibfk_4` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`) ON DELETE SET NULL;

--
-- Constraints for table `purchase_orders`
--
ALTER TABLE `purchase_orders`
  ADD CONSTRAINT `purchase_orders_ibfk_1` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`supplier_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `purchase_orders_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`) ON DELETE SET NULL;

--
-- Constraints for table `purchase_order_items`
--
ALTER TABLE `purchase_order_items`
  ADD CONSTRAINT `purchase_order_items_ibfk_1` FOREIGN KEY (`po_id`) REFERENCES `purchase_orders` (`po_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `purchase_order_items_ibfk_2` FOREIGN KEY (`item_id`) REFERENCES `inventory_items` (`item_id`),
  ADD CONSTRAINT `purchase_order_items_ibfk_3` FOREIGN KEY (`location_id`) REFERENCES `locations` (`location_id`) ON DELETE SET NULL;

--
-- Constraints for table `sales_orders`
--
ALTER TABLE `sales_orders`
  ADD CONSTRAINT `sales_orders_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`customer_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `sales_orders_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`) ON DELETE SET NULL;

--
-- Constraints for table `sales_order_items`
--
ALTER TABLE `sales_order_items`
  ADD CONSTRAINT `sales_order_items_ibfk_1` FOREIGN KEY (`so_id`) REFERENCES `sales_orders` (`so_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `sales_order_items_ibfk_2` FOREIGN KEY (`item_id`) REFERENCES `inventory_items` (`item_id`),
  ADD CONSTRAINT `sales_order_items_ibfk_3` FOREIGN KEY (`location_id`) REFERENCES `locations` (`location_id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
