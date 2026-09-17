<?php
declare(strict_types=1);
require_once __DIR__ . '/../BD/conexion.php';
require_once __DIR__ . '/../funciones/funciones.php';
if (session_status() === PHP_SESSION_NONE) session_start();
$term = trim((string)($_GET['q'] ?? ''));
$like = '%' . $term . '%'; $pdo=obtenerConexion();
$categoryStmt = $pdo->prepare('SELECT id_categoria,nombre,icon,descripcion FROM categorias WHERE nombre LIKE ? ORDER BY nombre');
$categoryStmt->execute([$like]);
$categories = $categoryStmt->fetchAll();
$stmt=$pdo->prepare('SELECT m.id_menu_item,m.nombre,m.descripcion,m.precio,m.imagen_url,m.id_categoria,n.nombre negocio_nombre FROM menu_items m LEFT JOIN negocios n ON n.id_negocio=m.id_negocio WHERE m.nombre LIKE ? OR m.descripcion LIKE ? ORDER BY m.nombre');
$stmt->execute([$like,$like]); $dishes=$stmt->fetchAll();
foreach ($dishes as &$dish) $dish['imagen_url'] = resolverImagenUrl($dish['imagen_url'] ?? '');
unset($dish);
?>
<!doctype html><html lang="es"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><base href="../"><title>Buscar | Shizen</title><link rel="stylesheet" href="css/styles.css"><link rel="stylesheet" href="css/nav.css"><link rel="stylesheet" href="css/modals.css?v=20260816-2"><link rel="stylesheet" href="css/search.css"></head><body>
<?php include __DIR__ . '/../forms/navegacion.php'; ?><main class="search-page"><h1>Resultados de búsqueda</h1><p class="search-page-sub"><?php if ($term !== ''): ?>Resultados para <strong>“<?= htmlspecialchars($term) ?>”</strong><?php else: ?>Escribe algo para encontrar categorías de comida.<?php endif; ?></p>
<?php if (!$categories && !$dishes): ?><div class="search-empty">No encontramos resultados. Prueba con otra búsqueda.</div><?php endif; ?>
<?php if ($categories): ?><h2 class="search-section-title">Categorías</h2><div class="search-results"><?php foreach($categories as $category): ?><a class="search-result-card" href="categorias.php?categoria=<?= (int)$category['id_categoria'] ?>"><span class="search-result-icon"><?= htmlspecialchars($category['icon'] ?? '🍽') ?></span><span><strong><?= htmlspecialchars($category['nombre']) ?></strong><small><?= htmlspecialchars($category['descripcion'] ?? '') ?></small></span></a><?php endforeach; ?></div><?php endif; ?>
<?php if ($dishes): ?><h2 class="search-section-title">Platos</h2><div class="search-results"><?php foreach($dishes as $dish): ?><a class="search-result-card" href="categorias.php?categoria=<?= (int)$dish['id_categoria'] ?>"><img class="search-result-image" src="<?= htmlspecialchars($dish['imagen_url']) ?>" alt=""><span class="search-result-content"><strong><?= htmlspecialchars($dish['nombre']) ?></strong><small><?= htmlspecialchars($dish['negocio_nombre'] ?: 'Restaurante Shizen') ?> · $<?= number_format((float)$dish['precio'],0,',','.') ?></small><em><?= htmlspecialchars($dish['descripcion'] ?: 'Plato vegano de Shizen.') ?></em></span></a><?php endforeach; ?></div><?php endif; ?></main><script src="js/app.js"></script></body></html>
