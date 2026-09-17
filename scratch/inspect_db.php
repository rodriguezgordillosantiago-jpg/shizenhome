<?php
require_once __DIR__ . '/../BD/conexion.php';

$pdo = obtenerConexion();

echo "=== TABLA NEGOCIOS ===\n";
print_r($pdo->query("DESCRIBE negocios")->fetchAll(PDO::FETCH_ASSOC));

echo "=== NEGOCIOS ACTUALES ===\n";
print_r($pdo->query("SELECT * FROM negocios")->fetchAll(PDO::FETCH_ASSOC));

echo "=== PLATOS ACTUALES (COUNT) ===\n";
echo $pdo->query("SELECT COUNT(*) FROM menu_items")->fetchColumn() . "\n";

echo "=== PLATOS DISTRIBUCION ===\n";
print_r($pdo->query("SELECT id_negocio, COUNT(*) as count FROM menu_items GROUP BY id_negocio")->fetchAll(PDO::FETCH_ASSOC));

