CREATE DATABASE IF NOT EXISTS shizen
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_spanish2_ci;

USE shizen;

CREATE TABLE IF NOT EXISTS usuario (
  id_usuario INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(100) NOT NULL,
  apellido VARCHAR(100) NOT NULL,
  direccion VARCHAR(255) NULL,
  email VARCHAR(150) NOT NULL,
  password_hash VARCHAR(255) NOT NULL,
  fecha_registro DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  rol VARCHAR(20) NOT NULL DEFAULT 'usuario',
  ciudad VARCHAR(100) NULL DEFAULT 'Chapinero',
  telefono VARCHAR(50) NULL DEFAULT '+57 300 123 4567',
  id_cocina_negocio_asociado INT NULL,
  CONSTRAINT uq_usuario_email UNIQUE (email)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS negocios (
  id INT AUTO_INCREMENT PRIMARY KEY,
  id_usuario INT NOT NULL,
  gmail_negocio TEXT NOT NULL,
  nombre VARCHAR(100) NOT NULL,
  direccion VARCHAR(255) NOT NULL,
  cedula VARCHAR(20) NOT NULL,
  hora_apertura TIME NOT NULL,
  hora_cierre TIME NOT NULL,
  logo_url VARCHAR(500) NULL,
  rut_url VARCHAR(500) NOT NULL,
  documento_identidad_representante_url VARCHAR(500) NOT NULL,
  certificado_bancario_url VARCHAR(500) NOT NULL,
  certificado_camara_comercio_url VARCHAR(500) NULL,
  CONSTRAINT uq_negocios_usuario UNIQUE (id_usuario),
  CONSTRAINT fk_negocios_usuario FOREIGN KEY (id_usuario) REFERENCES usuario(id_usuario)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS repartidor (
  id_repartidor INT AUTO_INCREMENT PRIMARY KEY,
  id_usuario INT NOT NULL,
  nombre VARCHAR(100) NOT NULL,
  apellido VARCHAR(100) NOT NULL,
  email_repartidor VARCHAR(150) NULL,
  num_telefono VARCHAR(20) NULL,
  direccion VARCHAR(255) NULL,
  cedula VARCHAR(20) NOT NULL,
  vehiculo VARCHAR(50) NOT NULL,
  foto_url VARCHAR(500) NOT NULL,
  cedula_documento_url VARCHAR(500) NOT NULL,
  licencia_conduccion_url VARCHAR(500) NULL,
  soat_url VARCHAR(500) NULL,
  tarjeta_propiedad_url VARCHAR(500) NULL,
  estado VARCHAR(15) NOT NULL DEFAULT 'Activo',
  CONSTRAINT uq_repartidor_usuario UNIQUE (id_usuario),
  CONSTRAINT uq_repartidor_telefono UNIQUE (num_telefono),
  CONSTRAINT fk_repartidor_usuario FOREIGN KEY (id_usuario) REFERENCES usuario(id_usuario)
) ENGINE=InnoDB;

ALTER TABLE usuario
  ADD CONSTRAINT fk_usuario_cocina_negocio
  FOREIGN KEY (id_cocina_negocio_asociado) REFERENCES negocios(id);

CREATE TABLE IF NOT EXISTS pedidos (
  id INT AUTO_INCREMENT PRIMARY KEY,
  id_usuario INT NULL,
  nombre VARCHAR(60) NOT NULL,
  apellido VARCHAR(60) NOT NULL,
  tipo_documento VARCHAR(5) NOT NULL,
  numero_documento VARCHAR(20) NOT NULL,
  correo VARCHAR(120) NOT NULL,
  telefono VARCHAR(20) NOT NULL,
  localidad VARCHAR(60) NULL,
  direccion TEXT NOT NULL,
  total DECIMAL(12,2) NOT NULL DEFAULT 0,
  estado VARCHAR(20) NOT NULL DEFAULT 'recibido',
  fecha_registro DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

ALTER TABLE pedidos
  ADD COLUMN IF NOT EXISTS id_usuario INT NULL,
  ADD COLUMN IF NOT EXISTS total DECIMAL(12,2) NOT NULL DEFAULT 0,
  ADD COLUMN IF NOT EXISTS estado VARCHAR(20) NOT NULL DEFAULT 'recibido';

CREATE TABLE IF NOT EXISTS pedido_detalles (
  id INT AUTO_INCREMENT PRIMARY KEY,
  pedido_id INT NOT NULL,
  menu_item_id INT NOT NULL,
  nombre_producto VARCHAR(150) NOT NULL,
  cantidad INT NOT NULL,
  precio_unitario DECIMAL(12,2) NOT NULL,
  subtotal DECIMAL(12,2) NOT NULL,
  CONSTRAINT fk_detalle_pedido FOREIGN KEY (pedido_id) REFERENCES pedidos(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS contactos (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(80) NOT NULL,
  correo VARCHAR(120) NOT NULL,
  telefono VARCHAR(20) NOT NULL,
  mensaje TEXT NOT NULL,
  fecha_registro DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS categorias (
  id           INT AUTO_INCREMENT PRIMARY KEY,
  nombre       VARCHAR(100) NOT NULL,
  icon         VARCHAR(10)  NOT NULL,
  descripcion  TEXT         NULL,
  cover_img    VARCHAR(255) NULL,
  bg_color     VARCHAR(20)  NULL,
  accent_color VARCHAR(20)  NULL
) ENGINE=InnoDB;

ALTER TABLE menu_items
  ADD COLUMN IF NOT EXISTS categoria_id INT NULL,
  ADD CONSTRAINT fk_menu_categoria
    FOREIGN KEY (categoria_id) REFERENCES categorias(id)
    ON DELETE SET NULL;

CREATE TABLE IF NOT EXISTS favorito (
  id_favorito INT AUTO_INCREMENT PRIMARY KEY,
  id_negocio INT NULL,
  id_menu_item INT NULL,
  id_usuario INT NULL,
  fecha_reg DATE NOT NULL DEFAULT (CURRENT_DATE),
  CONSTRAINT uq_favorito_usuario_negocio UNIQUE (id_usuario, id_negocio),
  CONSTRAINT uq_favorito_usuario_menu_item UNIQUE (id_usuario, id_menu_item),
  CONSTRAINT fk_favorito_negocio FOREIGN KEY (id_negocio) REFERENCES negocios(id_negocio),
  CONSTRAINT fk_favorito_menu_item FOREIGN KEY (id_menu_item) REFERENCES menu_items(id_menu_item) ON DELETE SET NULL,
  CONSTRAINT fk_favorito_usuario FOREIGN KEY (id_usuario) REFERENCES usuario(id_usuario)
) ENGINE=InnoDB;

ALTER TABLE entrega
  ADD COLUMN IF NOT EXISTS codigo_entrega VARCHAR(6) NULL AFTER estado,
  ADD COLUMN IF NOT EXISTS fecha_confirmacion DATETIME NULL AFTER codigo_entrega;

CREATE TABLE IF NOT EXISTS carrito (
  id_carrito INT AUTO_INCREMENT PRIMARY KEY,
  id_usuario INT NOT NULL,
  id_negocio INT NOT NULL,
  id_menu_item INT NOT NULL,
  cantidad INT NOT NULL DEFAULT 1,
  precio_unitario DECIMAL(12,2) NOT NULL,
  fecha_creacion DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  fecha_actualizacion DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT uq_carrito_usuario_item UNIQUE (id_usuario, id_menu_item),
  CONSTRAINT fk_carrito_usuario FOREIGN KEY (id_usuario) REFERENCES usuario(id_usuario) ON DELETE CASCADE,
  CONSTRAINT fk_carrito_negocio FOREIGN KEY (id_negocio) REFERENCES negocios(id_negocio) ON DELETE CASCADE,
  CONSTRAINT fk_carrito_menu_item FOREIGN KEY (id_menu_item) REFERENCES menu_items(id_menu_item) ON DELETE CASCADE
) ENGINE=InnoDB;

