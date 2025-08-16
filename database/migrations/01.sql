-- crear base de datos
DROP DATABASE IF EXISTS chatbot_oficial;
CREATE DATABASE chatbot_oficial;

-- alumnos
CREATE TABLE IF NOT EXISTS alumnos (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nombres VARCHAR(255),
    apellidos VARCHAR(255),
    matricula VARCHAR(255),
    numero_telefono VARCHAR(255) NOT NULL UNIQUE,
    numero_seguro_social VARCHAR(255),
    clinica VARCHAR(255)
);

-- documentos
CREATE TABLE IF NOT EXISTS documentos (
    id INT PRIMARY KEY AUTO_INCREMENT,
    id_alumno INT,
    nombre VARCHAR(255),
    url VARCHAR(255),
    CONSTRAINT fk_documentos_alumnos FOREIGN KEY (id_alumno) REFERENCES alumnos(id)
);

-- sesiones
CREATE TABLE IF NOT EXISTS sesiones (
    id INT PRIMARY KEY AUTO_INCREMENT,
    numero_telefono VARCHAR(255) UNIQUE,
    estado VARCHAR(255)
);
