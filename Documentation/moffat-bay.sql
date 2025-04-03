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
    slipSize INT NOT NULL,
    waitListDate TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (customerId) REFERENCES customers(customerId) ON DELETE CASCADE
);
