<?php
declare(strict_types=1);
require_once __DIR__ . '/../BD/conexion.php';
if (session_status() === PHP_SESSION_NONE) session_start();
if (empty($_SESSION['id_usuario'])) { header('Location: login.php?redirect=php%2Fpedidos.php'); exit; }

$orderId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$pdo = obtenerConexion();
$stmt = $pdo->prepare(
    'SELECT p.*, c.id_compra, c.total, c.metodo_pago, e.id_entrega, e.estado AS entrega_estado,
            e.codigo_entrega, e.fecha_confirmacion, n.nombre AS negocio_nombre
     FROM pedido p
     LEFT JOIN compra c ON c.id_pedido = p.id_pedido
     LEFT JOIN entrega e ON e.id_compra = c.id_compra
     LEFT JOIN negocios n ON n.id_negocio = p.id_negocio
     WHERE p.id_pedido = ? AND p.id_usuario = ? LIMIT 1'
);
$stmt->execute([$orderId, (int) $_SESSION['id_usuario']]);
$order = $stmt->fetch();
if (!$order) { http_response_code(404); exit('Pedido no encontrado.'); }
$code = $order['codigo_entrega'] ?? null;
$businessRatingCheck = $pdo->prepare('SELECT 1 FROM calificacion WHERE id_usuario = ? AND id_negocio = ? LIMIT 1');
$businessRatingCheck->execute([(int) $_SESSION['id_usuario'], (int) $order['id_negocio']]);
$businessRated = (bool) $businessRatingCheck->fetchColumn();
$courierRatingCheck = $pdo->prepare('SELECT 1 FROM calificacion_repartidor WHERE id_usuario = ? AND id_pedido = ? LIMIT 1');
$courierRatingCheck->execute([(int) $_SESSION['id_usuario'], (int) $order['id_pedido']]);
$courierRated = (bool) $courierRatingCheck->fetchColumn();
?>
<!doctype html>
<html lang="es"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><base href="../"><title>Pedido #<?= (int)$order['id_pedido'] ?> | Shizen</title><link rel="stylesheet" href="css/styles.css"><link rel="stylesheet" href="css/nav.css"><link rel="stylesheet" href="css/modals.css"><link rel="stylesheet" href="css/orders.css"></head><body>
<?php include __DIR__ . '/../forms/navegacion.php'; ?>
<main class="orders-page"><a class="back-link" href="php/pedidos.php" aria-label="Volver a mis pedidos" title="Volver a mis pedidos">← Volver a mis pedidos</a><section class="order-detail">
<h1>Pedido #<?= (int)$order['id_pedido'] ?></h1><p><?= htmlspecialchars($order['descripcion']) ?></p><p>Estado: <strong><?= htmlspecialchars($order['estado']) ?></strong></p><p>Dirección: <?= htmlspecialchars($order['direccion_entrega']) ?></p>
<div class="order-tracking" aria-label="Rastreo del pedido"><h2>Rastreo del pedido</h2><div class="tracking-line"><div class="tracking-step is-complete"><span>✓</span><strong>Pedido realizado</strong><small>Confirmado</small></div><div class="tracking-step is-complete"><span>✓</span><strong>En preparación</strong><small>El negocio está preparando tu pedido</small></div><div class="tracking-step <?= $order['fecha_confirmacion'] ? 'is-complete' : 'is-current' ?>"><span><?= $order['fecha_confirmacion'] ? '✓' : '3' ?></span><strong>En camino</strong><small><?= $order['fecha_confirmacion'] ? 'Entrega confirmada' : 'Rastreo estático por ahora' ?></small></div><div class="tracking-step <?= $order['fecha_confirmacion'] ? 'is-complete' : '' ?>"><span><?= $order['fecha_confirmacion'] ? '✓' : '4' ?></span><strong>Entregado</strong><small><?= $order['fecha_confirmacion'] ? 'Pedido recibido' : 'Pendiente de entrega' ?></small></div></div></div>
<?php if (!$order['fecha_confirmacion']): ?>
  <div class="delivery-code-box" style="background:#f0fdf4;border:2px dashed #22c55e;border-radius:12px;padding:16px;text-align:center;margin:20px 0">
    <div style="font-size:14px;color:#15803d;font-weight:700">🔑 Tu Código de Entrega:</div>
    <div style="font-size:28px;font-weight:900;letter-spacing:4px;color:#166534;margin:6px 0"><?= htmlspecialchars((string)($order['codigo_entrega'] ?: sprintf('%06d', ($order['id_pedido'] * 137461) % 900000 + 100000))) ?></div>
    <div style="font-size:12px;color:#4b5563">Entrégaselo al repartidor cuando llegue con tu pedido para confirmar la entrega.</div>
  </div>
<?php else: ?>
  <p class="order-success">✅ Pedido entregado. ¡Gracias por elegir Shizen!</p>
<?php endif; ?>
<?php if ($order['fecha_confirmacion'] && !$courierRated): ?><form class="form" method="post" action="php/calificar_repartidor.php"><input type="hidden" name="csrf_token" value="<?= htmlspecialchars((string)($_SESSION['csrf_token'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"><input type="hidden" name="id" value="<?= (int)$order['id_pedido'] ?>"><h2>Califica al repartidor</h2><fieldset class="rating-stars" aria-label="Selecciona una calificación de 1 a 5 estrellas"><?php for($i=1;$i<=5;$i++): ?><label class="rating-star"><input type="radio" name="puntuacion" value="<?= $i ?>" aria-label="<?= $i ?> estrellas" required><span aria-hidden="true"></span></label><?php endfor; ?></fieldset><textarea class="input" name="comentario" placeholder="Comentario opcional" rows="3"></textarea><button class="button" type="submit">Enviar calificación</button></form><?php elseif ($order['fecha_confirmacion']): ?><p class="order-success">Ya calificaste al repartidor.</p><?php endif; ?>
<?php if ($order['fecha_confirmacion'] && !$businessRated): ?><form class="form" method="post" action="php/calificar_negocio.php"><input type="hidden" name="csrf_token" value="<?= htmlspecialchars((string)($_SESSION['csrf_token'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"><input type="hidden" name="id" value="<?= (int)$order['id_pedido'] ?>"><h2>Califica al negocio</h2><fieldset class="rating-stars" aria-label="Selecciona una calificación de 1 a 5 estrellas"><?php for($i=1;$i<=5;$i++): ?><label class="rating-star"><input type="radio" name="puntuacion" value="<?= $i ?>" aria-label="<?= $i ?> estrellas" required><span aria-hidden="true"></span></label><?php endfor; ?></fieldset><textarea class="input" name="comentario" placeholder="Comentario opcional" rows="3"></textarea><button class="button" type="submit">Enviar calificación</button></form><?php elseif ($order['fecha_confirmacion']): ?><p class="order-success">Ya calificaste al negocio.</p><?php endif; ?>
</section></main><div id="overlays"><?php include __DIR__ . '/../forms/modales.php'; ?></div><script src="js/app.js"></script></body></html>
