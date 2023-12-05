INSERT INTO customers (name, address, email, phone) VALUES 
('John Doe', '123 Main St, Townsville', 'johndoe@example.com', '123-456-7890'),
('Jane Smith', '456 Side St, Villagetown', 'janesmith@example.com', '234-567-8901'),
('Alex Johnson', '789 Circle Ave, Cityplace', 'alexjohnson@example.com', '345-678-9012');
INSERT INTO employees (name, email, password, created_by, is_manager) VALUES 
('Manager Mike', 'managermike@example.com', 'password', NULL, TRUE),  -- Manager
('Employee Emma', 'employeeemma@example.com', 'password', 1, FALSE);   -- Regular employee, assuming created by Manager Mike

INSERT INTO users (email, password, entity_type, entity_id) VALUES 
('johndoe@example.com' , 'password', 'customer', 1),
('janesmith@example.com', 'password', 'customer', 2),
('alexjohnson@example.com', 'password', 'customer', 3),
('managermike@example.com', 'password', 'employee', 1),
('employeeemma@example.com', 'password', 'employee', 2);

INSERT INTO rewards (name, points, added_by) VALUES 
('Artisan Coffee', 150, 1),
('Gourmet Sandwich', 250, 1),
('Afternoon High Tea', 350, 1),
('Wine Tasting Experience', 400, 1),
('Chef Special Dessert', 300, 1),
('Private Cooking Class', 600, 1),
('Exclusive Dinner for Two', 800, 1),
('Weekend Brunch Special', 450, 1),
('Craft Beer Sampler', 200, 1),
('Seasonal Fruit Basket', 250, 1);
