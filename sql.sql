-- Crear la base de datos
CREATE DATABASE IF NOT EXISTS sql_injection_demo;

-- Usar la base de datos
USE sql_injection_demo;

-- Crear la tabla `users` para almacenar usuarios
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) NOT NULL,
    password VARCHAR(255) NOT NULL
);

-- Insertar datos de prueba en la tabla `users`
INSERT INTO users (username, password) VALUES
('admin', 'password123'),
('user', 'userpass'),
('guest', 'guest123');

-- Crear una tabla adicional para ejemplos avanzados si es necesario
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    price DECIMAL(10, 2) NOT NULL
);

-- Insertar datos de prueba en la tabla `products`
INSERT INTO products (name, price) VALUES
('Laptop', 1200.00),
('Smartphone', 800.00),
('Tablet', 500.00);
