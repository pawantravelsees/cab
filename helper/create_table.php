<?php
include "./db.php";
$db = new db();
// $sql = "CREATE TABLE car_results (
//     id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
//     sid VARCHAR(100) NOT NULL,
//     vender VARCHAR(50) NOT NULL DEFAULT 'cbz',
//     car_id VARCHAR(50) NOT NULL,
//     car_type VARCHAR(50) NOT NULL,
//     inc_distance DECIMAL(10,2) NOT NULL,
//     price DECIMAL(10,2) NOT NULL,
//     gst DECIMAL(10,2) NOT NULL,
//     extra_price DECIMAL(10,2) DEFAULT 0,
//     carrier_charge DECIMAL(10,2) DEFAULT 0,
//     carrier_gst DECIMAL(10,2) DEFAULT 0,
//     new_car_charge DECIMAL(10,2) DEFAULT 0,
//     new_car_gst DECIMAL(10,2) DEFAULT 0,
//     pet_charge DECIMAL(10,2) DEFAULT 0,
//     pet_gst DECIMAL(10,2) DEFAULT 0,
//     total_price DECIMAL(10,2) NOT NULL,
//     total_gst DECIMAL(10,2) NOT NULL,
//     created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
// )";

// $db->create_table($sql);

// $sql = "CREATE TABLE cab_filters (
//     id INT AUTO_INCREMENT PRIMARY KEY,
//     sid INT NOT NULL,                         
//     car_types VARCHAR(255) DEFAULT NULL,    
//     price_min INT DEFAULT NULL,             
//     price_max INT DEFAULT NULL,               
//     seating_capacity INT DEFAULT NULL,     
//     pet_allowed TINYINT(1) DEFAULT 0,       
//     carrier_charge TINYINT(1) DEFAULT 0,
//     new_car_charge TINYINT(1) DEFAULT 0,
//     sort_by VARCHAR(50) DEFAULT 'best',    
//     created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
// )";

// $sql = "CREATE TABLE selected_cab(
// id INT AUTO_INCREMENT PRIMARY KEY,
// sid INT NOT NULL,
// cid INT NOT NULL,
// updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
// created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP

// )";

// $db->create_table($sql);