-- =============================================================================
-- SCRIPT DE CREACIÓN Y OPTIMIZACIÓN DE LA BASE DE DATOS DE GoPET
-- Asignatura: Bases de Datos (1º DAW) / Proyecto Intermodular
-- =============================================================================
-- Este script documenta el modelado lógico de datos del proyecto GoPET,
-- incluyendo las restricciones de integridad (PK, FK, CHECK), disparadores
-- (TRIGGERS), vistas (VIEWS) y ejemplos de consultas complejas para la
-- optimización del rendimiento de la base de datos en MySQL.
-- =============================================================================

-- =============================================================================
-- 1. ESTRUCTURA DE TABLAS (DDL) CON RESTRICCIONES DE INTEGRIDAD
-- =============================================================================

-- Tabla de Usuarios
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    email_verified_at DATETIME NULL,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(50) NOT NULL DEFAULT 'user',
    avatar VARCHAR(255) NULL,
    remember_token VARCHAR(100) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT chk_users_role CHECK (role IN ('admin', 'user'))
);

-- Tabla de Perros
CREATE TABLE IF NOT EXISTS dogs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    breed VARCHAR(255) NOT NULL,
    age INT NULL,
    size VARCHAR(50) NOT NULL,
    photo VARCHAR(255) NULL,
    user_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT chk_dogs_age CHECK (age >= 0 AND age < 30),
    CONSTRAINT chk_dogs_size CHECK (size IN ('pequeño', 'mediano', 'grande'))
);

-- Tabla de Peticiones de Cuidado
CREATE TABLE IF NOT EXISTS care_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    price DECIMAL(8, 2) NOT NULL,
    description TEXT NULL,
    status VARCHAR(50) NOT NULL DEFAULT 'pending',
    accepted_by INT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (accepted_by) REFERENCES users(id) ON DELETE SET NULL,
    CONSTRAINT chk_care_requests_price CHECK (price >= 0),
    CONSTRAINT chk_care_requests_status CHECK (status IN ('pending', 'accepted', 'finalized')),
    CONSTRAINT chk_dates CHECK (end_date >= start_date)
);

-- Tabla Pivot: Relación de Perros asociados a cada Petición
CREATE TABLE IF NOT EXISTS care_request_dog (
    id INT AUTO_INCREMENT PRIMARY KEY,
    care_request_id INT NOT NULL,
    dog_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (care_request_id) REFERENCES care_requests(id) ON DELETE CASCADE,
    FOREIGN KEY (dog_id) REFERENCES dogs(id) ON DELETE CASCADE
);

-- Tabla de Favoritos (N:M entre Usuarios y Peticiones)
CREATE TABLE IF NOT EXISTS care_request_favorites (
    care_request_id INT NOT NULL,
    user_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (care_request_id, user_id),
    FOREIGN KEY (care_request_id) REFERENCES care_requests(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Tabla de Chats de Comunicación
CREATE TABLE IF NOT EXISTS chats (
    id INT AUTO_INCREMENT PRIMARY KEY,
    care_request_id INT NOT NULL,
    user_id INT NOT NULL,      -- Cuidador interesado que inicia el chat
    creator_id INT NOT NULL,   -- Dueño de la petición (creador de la petición)
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (care_request_id) REFERENCES care_requests(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (creator_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Tabla de Mensajes
CREATE TABLE IF NOT EXISTS messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    chat_id INT NOT NULL,
    sender_id INT NOT NULL,
    content TEXT NOT NULL,
    read_at DATETIME NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (chat_id) REFERENCES chats(id) ON DELETE CASCADE,
    FOREIGN KEY (sender_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Tabla de Pagos en Depósito (Escrow)
CREATE TABLE IF NOT EXISTS payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    care_request_id INT NOT NULL,
    user_id INT NOT NULL,       -- Dueño del perro que paga
    receiver_id INT NOT NULL,   -- Cuidador que recibe el pago neto
    amount DECIMAL(8, 2) NOT NULL,
    fee DECIMAL(8, 2) NOT NULL,
    net_amount DECIMAL(8, 2) NOT NULL,
    status VARCHAR(50) NOT NULL DEFAULT 'escrow',
    card_last_four VARCHAR(4) NOT NULL,
    transaction_id VARCHAR(255) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (care_request_id) REFERENCES care_requests(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (receiver_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT chk_payments_amount CHECK (amount > 0),
    CONSTRAINT chk_payments_fee CHECK (fee >= 0),
    CONSTRAINT chk_payments_net CHECK (net_amount > 0),
    CONSTRAINT chk_payments_status CHECK (status IN ('escrow', 'released', 'refunded')),
    CONSTRAINT chk_payments_card CHECK (char_length(card_last_four) = 4)
);

-- Tabla de Reseñas / Valoraciones bilaterales
CREATE TABLE IF NOT EXISTS reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    care_request_id INT NOT NULL,
    reviewer_id INT NOT NULL,
    reviewee_id INT NOT NULL,
    rating INT NOT NULL,
    comment TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (care_request_id) REFERENCES care_requests(id) ON DELETE CASCADE,
    FOREIGN KEY (reviewer_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (reviewee_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT chk_reviews_rating CHECK (rating >= 1 AND rating <= 5),
    CONSTRAINT chk_reviews_comment CHECK (char_length(comment) >= 5)
);

-- Tabla de auditoría interna de pagos (opcional para el tribunal)
CREATE TABLE IF NOT EXISTS payment_audits (
    id INT AUTO_INCREMENT PRIMARY KEY,
    payment_id INT NOT NULL,
    old_status VARCHAR(50),
    new_status VARCHAR(50),
    changed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- =============================================================================
-- 2. DISPARADORES DE BASE DE DATOS (TRIGGERS)
-- =============================================================================

-- Trigger 1: Validar reglas de negocio temporales de forma nativa en la base de datos.
-- Evita insertar una petición de cuidado con fechas inconsistentes.
DELIMITER $$
CREATE TRIGGER tg_validate_care_request_dates
BEFORE INSERT ON care_requests
FOR EACH ROW
BEGIN
    IF NEW.end_date < NEW.start_date THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Error de Integridad: La fecha de finalización no puede ser previa a la de inicio.';
    END IF;
END$$
DELIMITER ;

-- Trigger 2: Registro de auditoría ante el cambio de estado de un pago.
DELIMITER $$
CREATE TRIGGER tg_audit_payment_status
AFTER UPDATE ON payments
FOR EACH ROW
BEGIN
    IF OLD.status <> NEW.status THEN
        INSERT INTO payment_audits (payment_id, old_status, new_status, changed_at)
        VALUES (OLD.id, OLD.status, NEW.status, CURRENT_TIMESTAMP);
    END IF;
END$$
DELIMITER ;

-- =============================================================================
-- 3. VISTAS DE BASE DE DATOS (VIEWS)
-- =============================================================================

-- Vista 1: Resumen financiero totalizador de comisiones y volúmenes de la plataforma.
CREATE OR REPLACE VIEW view_platform_financial_summary AS
SELECT 
    COUNT(id) AS total_transactions,
    SUM(amount) AS total_volume,
    SUM(fee) AS platform_revenue,
    SUM(CASE WHEN status = 'released' THEN net_amount ELSE 0 END) AS total_paid_out,
    SUM(CASE WHEN status = 'escrow' THEN amount ELSE 0 END) AS total_in_escrow,
    SUM(CASE WHEN status = 'refunded' THEN amount ELSE 0 END) AS total_refunded
FROM payments;

-- Vista 2: Ranking y puntuación media de cuidadores.
CREATE OR REPLACE VIEW view_caretaker_rankings AS
SELECT 
    u.id AS caretaker_id,
    u.name AS caretaker_name,
    COUNT(r.id) AS total_reviews_received,
    ROUND(AVG(r.rating), 2) AS average_rating,
    (SELECT COUNT(cr.id) FROM care_requests cr WHERE cr.accepted_by = u.id AND cr.status = 'finalized') AS completed_services
FROM users u
LEFT JOIN reviews r ON u.id = r.reviewee_id
WHERE u.role = 'user'
GROUP BY u.id, u.name;

-- =============================================================================
-- 4. OPTIMIZACIÓN Y CONSULTAS COMPLEJAS (DML)
-- =============================================================================

-- Creación de Índices para optimizar las búsquedas frecuentes (Green Code en DB)
CREATE INDEX idx_care_requests_dates ON care_requests(start_date, end_date);
CREATE INDEX idx_care_requests_status ON care_requests(status);
CREATE INDEX idx_dogs_user_id ON dogs(user_id);
CREATE INDEX idx_payments_status ON payments(status);

-- Consulta Compleja de Ejemplo:
-- Obtener los cuidadores que tengan un rating medio mayor o igual a 4.0, ordenados
-- por volumen de facturación cobrada y cantidad de perros atendidos de tamaño grande.
SELECT 
    u.id AS caretaker_id,
    u.name AS caretaker_name,
    AVG(rev.rating) AS rating_medio,
    SUM(p.net_amount) AS total_ganado,
    COUNT(DISTINCT d.id) AS perros_grandes_cuidados
FROM users u
INNER JOIN payments p ON u.id = p.receiver_id
INNER JOIN care_requests cr ON p.care_request_id = cr.id
INNER JOIN care_request_dog crd ON cr.id = crd.care_request_id
INNER JOIN dogs d ON crd.dog_id = d.id
LEFT JOIN reviews rev ON u.id = rev.reviewee_id
WHERE u.role = 'user'
  AND d.size = 'grande'
  AND p.status = 'released'
GROUP BY u.id, u.name
HAVING rating_medio >= 4.0
ORDER BY total_ganado DESC, perros_grandes_cuidados DESC;
