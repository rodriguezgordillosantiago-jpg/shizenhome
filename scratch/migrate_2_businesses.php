<?php
declare(strict_types=1);

require_once __DIR__ . '/../BD/conexion.php';

$pdo = obtenerConexion();
try {
    $pdo->beginTransaction();

        // 1. Asegurar usuarios para los 2 negocios
        // Usuario 1 -> Negocio 1
        $pdo->exec("
            INSERT INTO usuario (id_usuario, nombre, apellido, direccion, email, password_hash, rol, ciudad)
            VALUES (1, 'Carlos', 'Mendoza', 'Calle 45 #7-12, Bogotá', 'contacto@veganocentral.com', '" . password_hash('123456', PASSWORD_DEFAULT) . "', 'negocio', 'Chapinero')
            ON DUPLICATE KEY UPDATE nombre='Carlos', apellido='Mendoza', direccion='Calle 45 #7-12, Bogotá', rol='negocio'
        ");

        // Usuario 2 -> Negocio 2
        $pdo->exec("
            INSERT INTO usuario (id_usuario, nombre, apellido, direccion, email, password_hash, rol, ciudad)
            VALUES (2, 'Laura', 'Gómez', 'Carrera 13 #58-30, Bogotá', 'contacto@ecomarket.com', '" . password_hash('123456', PASSWORD_DEFAULT) . "', 'negocio', 'Chapinero')
            ON DUPLICATE KEY UPDATE nombre='Laura', apellido='Gómez', direccion='Carrera 13 #58-30, Bogotá', rol='negocio'
        ");

        // 2. Asegurar que existan únicamente los 2 Negocios con TODOS sus datos de registro completos
        $pdo->exec("
            INSERT INTO negocios (id_negocio, id_usuario, gmail_negocio, nombre, direccion, cedula, hora_apertura, hora_cierre, logo_url, rut_url, documento_identidad_representante_url, certificado_bancario_url, certificado_camara_comercio_url)
            VALUES (
                1, 1, 'contacto@veganocentral.com', 'Restaurante Vegano Central', 'Calle 45 #7-12, Bogotá', '1010203040', '08:00:00', '22:00:00',
                '../Imagenes_prueba/logo.jpg',
                'legales/negocio/negocio_1010203040/rut_1010203040.pdf',
                'legales/negocio/negocio_1010203040/doc_representante_1010203040.pdf',
                'legales/negocio/negocio_1010203040/cert_bancario_1010203040.pdf',
                'legales/negocio/negocio_1010203040/camara_comercio_1010203040.pdf'
            )
            ON DUPLICATE KEY UPDATE
                gmail_negocio='contacto@veganocentral.com', nombre='Restaurante Vegano Central', direccion='Calle 45 #7-12, Bogotá',
                cedula='1010203040', hora_apertura='08:00:00', hora_cierre='22:00:00', logo_url='../Imagenes_prueba/logo.jpg',
                rut_url='legales/negocio/negocio_1010203040/rut_1010203040.pdf',
                documento_identidad_representante_url='legales/negocio/negocio_1010203040/doc_representante_1010203040.pdf',
                certificado_bancario_url='legales/negocio/negocio_1010203040/cert_bancario_1010203040.pdf',
                certificado_camara_comercio_url='legales/negocio/negocio_1010203040/camara_comercio_1010203040.pdf'
        ");

        $pdo->exec("
            INSERT INTO negocios (id_negocio, id_usuario, gmail_negocio, nombre, direccion, cedula, hora_apertura, hora_cierre, logo_url, rut_url, documento_identidad_representante_url, certificado_bancario_url, certificado_camara_comercio_url)
            VALUES (
                2, 2, 'contacto@ecomarket.com', 'Eco Market Chapinero', 'Carrera 13 #58-30, Bogotá', '1020304050', '08:00:00', '22:00:00',
                '../Imagenes_prueba/logo2.jpg',
                'legales/negocio/negocio_1020304050/rut_1020304050.pdf',
                'legales/negocio/negocio_1020304050/doc_representante_1020304050.pdf',
                'legales/negocio/negocio_1020304050/cert_bancario_1020304050.pdf',
                'legales/negocio/negocio_1020304050/camara_comercio_1020304050.pdf'
            )
            ON DUPLICATE KEY UPDATE
                gmail_negocio='contacto@ecomarket.com', nombre='Eco Market Chapinero', direccion='Carrera 13 #58-30, Bogotá',
                cedula='1020304050', hora_apertura='08:00:00', hora_cierre='22:00:00', logo_url='../Imagenes_prueba/logo2.jpg',
                rut_url='legales/negocio/negocio_1020304050/rut_1020304050.pdf',
                documento_identidad_representante_url='legales/negocio/negocio_1020304050/doc_representante_1020304050.pdf',
                certificado_bancario_url='legales/negocio/negocio_1020304050/cert_bancario_1020304050.pdf',
                certificado_camara_comercio_url='legales/negocio/negocio_1020304050/camara_comercio_1020304050.pdf'
        ");

        // Desactivar temporalmente llaves foráneas para reestructuración limpia
        $pdo->exec("SET FOREIGN_KEY_CHECKS = 0");

        // 3. Redistribuir TODOS los platos existentes (menu_items) entre id_negocio 1 y 2 (mitad y mitad)
        $dishes = $pdo->query("SELECT id_menu_item FROM menu_items ORDER BY id_menu_item ASC")->fetchAll(PDO::FETCH_COLUMN);
        $half = (int)ceil(count($dishes) / 2);

        $i = 0;
        foreach ($dishes as $mId) {
            $targetBusinessId = ($i < $half) ? 1 : 2;
            $pdo->prepare("UPDATE menu_items SET id_negocio = ? WHERE id_menu_item = ?")->execute([$targetBusinessId, $mId]);
            $pdo->prepare("UPDATE promociones SET id_negocio = ? WHERE id_menu_item = ?")->execute([$targetBusinessId, $mId]);
            $i++;
        }

        // 4. Limpiar carritos, pedidos, favoritos y promociones de otros negocios inexistentes
        $pdo->exec("UPDATE carrito SET id_negocio = 1 WHERE id_negocio NOT IN (1, 2)");
        $pdo->exec("UPDATE pedido SET id_negocio = 1 WHERE id_negocio NOT IN (1, 2)");
        $pdo->exec("DELETE FROM favorito WHERE id_negocio IS NOT NULL AND id_negocio NOT IN (1, 2)");
        $pdo->exec("DELETE FROM promociones WHERE id_negocio NOT IN (1, 2)");

        // 5. Eliminar otros negocios que no sean el 1 o el 2
        $pdo->exec("DELETE FROM negocios WHERE id_negocio NOT IN (1, 2)");

        // Reactivar llaves foráneas
        $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");

    $pdo->commit();
    echo "✅ Base de datos migrada a únicamente 2 negocios con todos sus registros completos.\n";
} catch (Throwable $e) {
    if (isset($pdo) && $pdo->inTransaction()) $pdo->rollBack();
    echo "❌ Error: " . $e->getMessage() . "\n";
}
