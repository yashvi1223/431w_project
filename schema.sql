
DROP TABLE IF EXISTS redemptions;
DROP TABLE IF EXISTS reward_points;
DROP TABLE IF EXISTS rewards;
DROP TABLE IF EXISTS visits;
DROP TABLE IF EXISTS users;
DROP TABLE IF EXISTS employees;
DROP TABLE IF EXISTS customers;
DROP TABLE IF EXISTS feedback;

CREATE TABLE customers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    address TEXT,
    email VARCHAR(255) NOT NULL,
    phone VARCHAR(50),
    inactive boolean DEFAULT FALSE,
    UNIQUE (email)
);


CREATE TABLE employees (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    password TEXT,
    created_by INT,
    is_manager BOOLEAN DEFAULT FALSE,
    inactive boolean DEFAULT FALSE,
    UNIQUE (email)

);

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL,
    password VARCHAR(255) NOT NULL,
    entity_type ENUM('customer', 'employee') NOT NULL,
    entity_id INT NOT NULL,
    UNIQUE (email, entity_type, entity_id)
  
);

CREATE TABLE visits (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT,
    employee_id INT,
    bill_amount DECIMAL(10, 2),
    date DATE,
    FOREIGN KEY (customer_id) REFERENCES customers(id),
    FOREIGN KEY (employee_id) REFERENCES employees(id)
);


CREATE TABLE rewards (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    points INT NOT NULL,
    added_by INT,
    inactive boolean DEFAULT FALSE,
    FOREIGN KEY (added_by) REFERENCES employees(id)
);


CREATE TABLE reward_points (
    id INT AUTO_INCREMENT PRIMARY KEY,
    visit_id INT,
    points INT NOT NULL,
    customer_id INT,
    added_by INT,
    FOREIGN KEY (visit_id) REFERENCES visits(id),
    FOREIGN KEY (customer_id) REFERENCES customers(id),
    FOREIGN KEY (added_by) REFERENCES employees(id)
);


CREATE TABLE redemptions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    points INT NOT NULL,
    reward_id INT,
    customer_id INT
);


CREATE TABLE feedback (
    id INT AUTO_INCREMENT PRIMARY KEY,
    comment TEXT,
    rating INT,
    visit_id INT,
    FOREIGN KEY (visit_id) REFERENCES visits(id),
    UNIQUE(visit_id)

    
);
