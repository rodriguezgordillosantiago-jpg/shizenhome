<?php
declare(strict_types=1);

require_once __DIR__ . '/../BD/conexion.php';

$pdo = obtenerConexion();

$sqlPath = __DIR__ . '/../sql/shizen.sql';
$baseSql = file_get_contents($sqlPath);

// Cortar comentarios previos si existían datos seed al final
if (($pos = strpos($baseSql, "-- === SEED DATA FOR 2 BUSINESSES ===")) !== false) {
    $baseSql = substr($baseSql, 0, $pos);
}

$seedSql = "\n\n-- === SEED DATA FOR 2 BUSINESSES AND 46 DISHES ===\n";

// Seed Usuarios para Negocios
$seedSql .= "INSERT INTO usuario (id_usuario, nombre, apellido, direccion, email, password_hash, rol, ciudad) VALUES\n";
$seedSql .= "(1, 'Carlos', 'Mendoza', 'Calle 45 #7-12, Bogotá', 'contacto@veganocentral.com', '\$2y\$10\$h7V/7V0m764hR1aHkS4MGeB17Jd5o1A1l/06.f4e4W/m.g4s.m.l.', 'negocio', 'Chapinero'),\n";
$seedSql .= "(2, 'Laura', 'Gómez', 'Carrera 13 #58-30, Bogotá', 'contacto@ecomarket.com', '\$2y\$10\$h7V/7V0m764hR1aHkS4MGeB17Jd5o1A1l/06.f4e4W/m.g4s.m.l.', 'negocio', 'Chapinero')\n";
$seedSql .= "ON DUPLICATE KEY UPDATE nombre=VALUES(nombre), email=VALUES(email);\n\n";

// Seed Negocios
$seedSql .= "INSERT INTO negocios (id_negocio, id_usuario, gmail_negocio, nombre, direccion, cedula, hora_apertura, hora_cierre, logo_url, rut_url, documento_identidad_representante_url, certificado_bancario_url, certificado_camara_comercio_url) VALUES\n";
$seedSql .= "(1, 1, 'contacto@veganocentral.com', 'Restaurante Vegano Central', 'Calle 45 #7-12, Bogotá', '1010203040', '08:00:00', '22:00:00', '../Imagenes_prueba/logo.jpg', 'legales/negocio/negocio_1010203040/rut_1010203040.pdf', 'legales/negocio/negocio_1010203040/doc_representante_1010203040.pdf', 'legales/negocio/negocio_1010203040/cert_bancario_1010203040.pdf', 'legales/negocio/negocio_1010203040/camara_comercio_1010203040.pdf'),\n";
$seedSql .= "(2, 2, 'contacto@ecomarket.com', 'Eco Market Chapinero', 'Carrera 13 #58-30, Bogotá', '1020304050', '08:00:00', '22:00:00', '../Imagenes_prueba/logo2.jpg', 'legales/negocio/negocio_1020304050/rut_1020304050.pdf', 'legales/negocio/negocio_1020304050/doc_representante_1020304050.pdf', 'legales/negocio/negocio_1020304050/cert_bancario_1020304050.pdf', 'legales/negocio/negocio_1020304050/camara_comercio_1020304050.pdf')\n";
$seedSql .= "ON DUPLICATE KEY UPDATE nombre=VALUES(nombre), id_usuario=VALUES(id_usuario);\n\n";

// Seed Platos (menu_items)
$dishes = $pdo->query("SELECT * FROM menu_items ORDER BY id_menu_item ASC")->fetchAll(PDO::FETCH_ASSOC);

$seedSql .= "INSERT INTO menu_items (id_menu_item, id_negocio, id_categoria, nombre, descripcion, precio, imagen_url, on_promo, precio_promocion) VALUES\n";
$rows = [];
foreach ($dishes as $d) {
    $mId = (int)$d['id_menu_item'];
    $bId = (int)$d['id_negocio'];
    $catId = isset($d['id_categoria']) ? (int)$d['id_categoria'] : "NULL";
    $name = $pdo->quote((string)$d['nombre']);
    $desc = $pdo->quote((string)($d['descripcion'] ?? ''));
    $price = (float)$d['precio'];
    $img = $pdo->quote((string)($d['imagen_url'] ?? ''));
    $onPromo = (int)($d['on_promo'] ?? 0);
    $precioPromo = !empty($d['precio_promocion']) ? (float)$d['precio_promocion'] : "NULL";

    $rows[] = "({$mId}, {$bId}, {$catId}, {$name}, {$desc}, {$price}, {$img}, {$onPromo}, {$precioPromo})";
}
$seedSql .= implode(",\n", $rows) . "\n";
$seedSql .= "ON DUPLICATE KEY UPDATE id_negocio=VALUES(id_negocio), nombre=VALUES(nombre), precio=VALUES(precio);\n";

file_put_contents($sqlPath, trim($baseSql) . $seedSql);
echo "✅ shizen.sql actualizado con los 2 negocios y sus 46 platos.\n";

