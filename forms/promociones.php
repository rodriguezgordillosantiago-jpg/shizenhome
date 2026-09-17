<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <base href="../" />
  <title>Promociones | Shizen</title>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="css/styles.css" />
  <link rel="stylesheet" href="css/nav.css" />
  <link rel="stylesheet" href="css/pages.css" />
  <link rel="stylesheet" href="css/modals.css" />
</head>
<body>

  <!-- Componente de navegación reutilizable -->
  <header id="navigation">
    <?php include __DIR__ . '/navegacion.php'; ?>
  </header>

  <main id="app-content">
    <section class="promos-page">

      <div class="page-header">
        <a class="btn-back back-link" href="index.php" aria-label="Volver">
          <svg viewBox="0 0 24 24" aria-hidden="true">
            <path d="M19 12H5M12 19l-7-7 7-7" />
          </svg>
        </a>
        <div>
          <h2>Promociones</h2>
          <p>Descuentos exclusivos en restaurantes veganos</p>
        </div>
      </div>

      <div class="promos-inner">
        <div class="filter-tabs" id="promoFilterTabs"></div>
        <div class="promos-grid" id="promosGrid"></div>
      </div>

    </section>
  </main>

  <!-- Componente de modales reutilizable -->
  <div id="overlays">
    <?php include __DIR__ . '/modales.php'; ?>
  </div>

  <script src="js/data.js"></script>
  <script>
    window.shizenLayoutReady = Promise.resolve();
    window.DB_PROMOS = <?= json_encode($promociones ?? [], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;
  </script>
  <script src="js/app.js"></script>
</body>
</html>
