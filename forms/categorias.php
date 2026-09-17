<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?= htmlspecialchars($categoria['nombre'] ?? 'Categorías') ?> | Shizen</title>
  <base href="../">
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="css/styles.css" />
  <link rel="stylesheet" href="css/nav.css" />
  <link rel="stylesheet" href="css/home.css" />
  <link rel="stylesheet" href="css/pages.css" />
  <link rel="stylesheet" href="css/modals.css" />
  <link rel="stylesheet" href="css/business-menu.css?v=20260912-1" />
</head>
<body>
  <header id="navigation">
    <?php include __DIR__ . '/navegacion.php'; ?>
  </header>
  <?php
    require_once __DIR__ . '/../BD/conexion.php';
    if (empty($_SESSION['csrf_token'])) {
      $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    $pdo = obtenerConexion();
    $favoriteItemIds = [];
    if (!empty($_SESSION['id_usuario'])) {
      $favoriteStatement = $pdo->prepare('SELECT id_menu_item FROM favorito WHERE id_usuario = ? AND id_menu_item IS NOT NULL');
      $favoriteStatement->execute([(int) $_SESSION['id_usuario']]);
      $favoriteItemIds = array_map('intval', $favoriteStatement->fetchAll(PDO::FETCH_COLUMN));
    }
  ?>

  <main id="app-content">
    <section class="cat-page">
      <?php
        $defaultCovers = [
          1 => 'https://images.unsplash.com/photo-1550547660-d9450f859349?w=1200&h=500&fit=crop&auto=format',
          2 => 'https://images.unsplash.com/photo-1473093226795-af9932fe5856?w=1200&h=500&fit=crop&auto=format',
          3 => 'https://images.unsplash.com/photo-1512621776951-a57141f2eefd?w=1200&h=500&fit=crop&auto=format',
          4 => 'https://images.unsplash.com/photo-1606313564200-e75d5e30476c?w=1200&h=500&fit=crop&auto=format',
          5 => 'https://images.unsplash.com/photo-1622597467836-f3285f2131b8?w=1200&h=500&fit=crop&auto=format',
          6 => 'https://images.unsplash.com/photo-1525351484163-7529414344d8?w=1200&h=500&fit=crop&auto=format',
          7 => 'https://images.unsplash.com/photo-1552332386-f8dd00dc2f85?w=1200&h=500&fit=crop&auto=format',
        ];

        $catId = (int)($categoria['id'] ?? 1);
        $rawCover = trim($categoria['cover_img'] ?? '');
        $coverImg = '';

        if (!empty($rawCover)) {
          if (preg_match('#^https?://#i', $rawCover)) {
            $coverImg = $rawCover;
          } else {
            $clean = preg_replace('#^\.\.?/#', '', $rawCover);
            if (file_exists(__DIR__ . '/../' . $clean)) {
              $coverImg = $clean;
            } elseif (file_exists(__DIR__ . '/../../shizen_movil/' . $clean)) {
              $coverImg = '../shizen_movil/' . $clean;
            }
          }
        }

        if (empty($coverImg)) {
          $coverImg = $defaultCovers[$catId] ?? $defaultCovers[1];
        }

        $coverImg  = htmlspecialchars($coverImg);
        $catIcon   = !empty($categoria['icon'])         ? htmlspecialchars($categoria['icon'])         : '🍔';
        $catName   = !empty($categoria['nombre'])       ? htmlspecialchars($categoria['nombre'])       : 'Comidas Rápidas';
        $catDesc   = !empty($categoria['descripcion'])  ? htmlspecialchars($categoria['descripcion'])  : 'Burgers, tacos y wraps plant-based para cuando el tiempo apremia.';
      ?>
      <div class="cat-hero" id="catHero"
           style="background-image:url('<?= $coverImg ?>')">
        <div class="cat-hero-overlay"></div>
        <a class="cat-hero-back" href="index.php" aria-label="Volver">
          <svg viewBox="0 0 24 24" aria-hidden="true">
            <path d="M19 12H5M12 19l-7-7 7-7" />
          </svg>
        </a>
        <div class="cat-hero-info">
          <div class="cat-hero-icon-name">
            <span class="cat-hero-icon" id="catIcon"><?= $catIcon ?></span>
            <span class="cat-hero-name" id="catName"><?= $catName ?></span>
          </div>
          <p class="cat-hero-desc" id="catDesc"><?= $catDesc ?></p>
        </div>
      </div>

      <div class="cat-filter-bar">
        <span>Ordenar por:</span>
        <select class="sort-select" id="sortSelect">
          <option value="relevance">Relevancia</option>
          <option value="low">Precio: menor</option>
          <option value="high">Precio: mayor</option>
        </select>
      </div>
      <div class="cat-items-inner">
        <div class="items-grid" id="itemsGrid">

          <?php if (empty($platos)): ?>
            <p class="cat-empty" style="grid-column: 1 / -1; text-align: center; padding: 40px 20px; color: #666; font-size: 1.05rem;">No hay platos registrados en esta categoría.</p>
          <?php else: ?>
            <?php foreach ($platos as $plato): ?>
              <?php
                $dishId = (int)($plato['id'] ?? 0);
                $dishBusinessId = (int)($plato['negocio_id'] ?? 0);
                $dishIsFavorite = $dishId > 0 && in_array($dishId, $favoriteItemIds, true);
                $rawImg = trim($plato['imagen_url'] ?? '');
                $imgUrl = htmlspecialchars(function_exists('resolverImagenUrl') ? resolverImagenUrl($rawImg) : ($rawImg ?: 'assets/image-6.png'));
                $hasPromo = !empty($plato['on_promo']) && !empty($plato['precio_promocion']);
                $precioReal = $hasPromo ? (float)$plato['precio_promocion'] : (float)$plato['precio'];
                $ratingNegocio = !empty($plato['negocio_calificacion']) ? number_format((float)$plato['negocio_calificacion'], 1) : '4.8';
              ?>
              <div class="dish-card">
                <div class="dish-img" style="background-image: url('<?= $imgUrl ?>')">
                  <span class="dish-tag-badge">Vegano</span>
                  <?php if ($hasPromo): ?>
                    <span class="dish-promo-badge" style="position:absolute;top:10px;left:10px;background:#ea580c;color:#fff;font-weight:700;padding:4px 8px;border-radius:6px;font-size:11px;box-shadow:0 2px 6px rgba(234,88,12,.5)">🔥 PROMOCIÓN</span>
                  <?php endif; ?>
                  <?php if (!empty($_SESSION['id_usuario']) && $dishId > 0): ?>
                    <form method="post" action="php/favorito.php" class="dish-favorite-form">
                      <input type="hidden" name="csrf_token" value="<?= htmlspecialchars((string)($_SESSION['csrf_token'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                      <input type="hidden" name="id_menu_item" value="<?= $dishId ?>">
                      <input type="hidden" name="id_negocio" value="<?= $dishBusinessId ?>">
                      <input type="hidden" name="redirect" value="categorias.php?categoria=<?= $catId ?>">
                      <button class="favorite-button dish-favorite-button <?= $dishIsFavorite ? 'is-favorite' : '' ?>" type="submit" aria-label="<?= $dishIsFavorite ? 'Quitar plato de favoritos' : 'Agregar plato a favoritos' ?>"><?= $dishIsFavorite ? '♥' : '♡' ?></button>
                    </form>
                  <?php elseif ($dishId > 0): ?>
                    <a class="favorite-button dish-favorite-button" href="php/login.php?redirect=php%2Fcategorias.php%3Fcategoria%3D<?= $catId ?>" aria-label="Inicia sesión para agregar el plato a favoritos" title="Inicia sesión para agregar el plato a favoritos">♡</a>
                  <?php endif; ?>
                </div>
                <div class="dish-body">
                  <div class="dish-name"><?= htmlspecialchars($plato['plato_nombre']) ?></div>
                  <div class="dish-restaurant">🏪 <?= htmlspecialchars($plato['negocio_nombre']) ?></div>
                  <p class="dish-description"><?= htmlspecialchars($plato['plato_desc'] ?? '') ?></p>
                  <div class="dish-meta">
                    <span>🕐 20 min</span>
                  </div>
                  <div class="dish-price-row">
                    <div class="dish-prices">
                      <?php if ($hasPromo): ?>
                        <span class="dish-price" style="color:#ea580c">$<?= number_format((float)$plato['precio_promocion'], 0, ',', '.') ?></span>
                        <span style="font-size:12px;color:#9ca3af;text-decoration:line-through;margin-left:6px">$<?= number_format((float)$plato['precio'], 0, ',', '.') ?></span>
                      <?php else: ?>
                        <span class="dish-price">$<?= number_format((float)$plato['precio'], 0, ',', '.') ?></span>
                      <?php endif; ?>
                    </div>
                    <button
                      class="btn-add-cart"
                      type="button"
                      onclick='addToCart(<?= json_encode([
                        "id" => (int) $plato["id"],
                        "name" => $plato["plato_nombre"],
                        "price" => $precioReal,
                        "restaurant" => $plato["negocio_nombre"],
                        "businessId" => (int) ($plato["id_negocio"] ?? 0),
                        "image" => $imgUrl
                      ], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>)'
                    >Añadir al carrito</button>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
          <?php endif; ?>

        </div>
      </div>
    </section>
  </main>

  <!-- Modales reutilizables -->
  <div id="overlays">
    <?php include __DIR__ . '/modales.php'; ?>
  </div>

  <script src="js/data.js?v=20260827-1"></script>
  <script>
    window.shizenLayoutReady = Promise.resolve();
    document.addEventListener('DOMContentLoaded', function () {
      var select = document.getElementById('sortSelect');
      var grid = document.getElementById('itemsGrid');
      if (!select || !grid) return;
      select.addEventListener('change', function () {
        var cards = Array.prototype.slice.call(grid.querySelectorAll('.dish-card'));
        cards.sort(function (a, b) {
          var price = function (card) {
            return parseFloat(card.querySelector('.dish-price').textContent.replace(/[^\d]/g, '')) || 0;
          };
          return select.value === 'low' ? price(a) - price(b) : select.value === 'high' ? price(b) - price(a) : 0;
        });
        cards.forEach(function (card) { grid.appendChild(card); });
      });
    });
  </script>
  <script src="js/app.js?v=20260827-1"></script>
</body>
</html>
