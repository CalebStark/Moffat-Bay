--Database queries
-- Create the database (if it doesn't exist)
CREATE DATABASE IF NOT EXISTS moffat_bay_marina;

-- Use the database
USE moffat_bay_marina;


-- Create Customers Table
CREATE TABLE customers (
    customerId INT PRIMARY KEY AUTO_INCREMENT,
    email VARCHAR(255) UNIQUE NOT NULL,
    firstName VARCHAR(100) NOT NULL,
    lastName VARCHAR(100) NOT NULL,
    telephone VARCHAR(20),
    boatId INT NOT NULL,
    passwordHash VARCHAR(255) NOT NULL
);

-- Create Boats Table
CREATE TABLE boats (
    boatId INT PRIMARY KEY AUTO_INCREMENT,
    boatName VARCHAR(100),
    boatLength INT,
    slipId INT NOT NULL
);

-- Create Slips Table
CREATE TABLE slips (
    slipId INT PRIMARY KEY AUTO_INCREMENT,
    slipLength INT NOT NULL,
    available BOOLEAN NOT NULL
);

-- Create Reservations Table
CREATE TABLE reservations (
    reservationId INT PRIMARY KEY AUTO_INCREMENT,
    customerId INT NOT NULL,
    slipId INT NOT NULL,
    checkInDate DATE NOT NULL,
    reservationDate TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (customerId) REFERENCES customers(customerId) ON DELETE CASCADE,
    FOREIGN KEY (slipId) REFERENCES slips(slipId) ON DELETE CASCADE
);

-- Create Waitlist Table
CREATE TABLE waitlist (
    waitListId INT PRIMARY KEY AUTO_INCREMENT,
    customerId INT NOT NULL,
    slipId INT NOT NULL,
    waitListDate TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (customerId) REFERENCES customers(customerId) ON DELETE CASCADE
);

INSERT INTO customers (email, firstName, lastName, telephone, boatId, passwordHash) VALUES
('bob@example.com', 'Bob', 'Smith', '555-123-4567', 1, '$2a$10$abcdefghijklmnopqrstuvwxYZ'), -- Example Hash
('alice@example.com', 'Alice', 'Johnson', '555-987-6543', 3, '$2a$10$zyxwvutsrqponmlkjihgfedcBA'), -- Example Hash
('charlie@example.com', 'Charlie', 'Brown', '555-111-2222', 2, '$2a$10$1234567890abcdefghijklMN'); -- Example Hash

INSERT INTO slips (slipLength, available) VALUES
(0, FALSE),
(26, TRUE),
(40, FALSE),
(26, FALSE),
(50, FALSE),
(26, TRUE),
(40, TRUE),
(50, TRUE),
(50, TRUE),
(40, TRUE);

INSERT INTO boats (boatName, boatLength, slipId) VALUES
("Saint Maria", 40, 3),
("Al Monica", 26, 4),
("Alice Mobile", 50, 5);

INSERT INTO reservations (customerId, slipId, checkInDate) VALUES
(1, 3, '2024-07-15'), -- Bob reserves slip 3 (40 ft)
(2, 5, '2024-08-01'), -- Alice reserves slip 5 (50 ft)
(3, 4, '2024-09-10'); -- Charlie reserves slip 4 (26 ft)

INSERT INTO waitList (customerId, slipId) VALUES
(1, 3), -- Bob is on the waitlist for a 40 ft slip
(2, 5),  -- Alice is on the waitlist for a 50 ft slip
(3, 4);  -- Charlie is on the waitlist for a 26 ft slip




