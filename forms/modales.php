<?php
if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
    session_start();
}
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrfToken = htmlspecialchars((string)($_SESSION['csrf_token'] ?? ''), ENT_QUOTES, 'UTF-8');
$profileUser = $_SESSION['id_usuario'] ?? null;
?>
<script>
  window.shizenUser = <?= json_encode(!empty($_SESSION['id_usuario']) ? ['id' => (int)$_SESSION['id_usuario'], 'nombre' => (string)($_SESSION['usuario_nombre'] ?? '')] : null) ?>;
</script>
<div
  class="modal-overlay"
  id="registerModal"
  onclick="handleRegisterOverlayClick(event)"
>
  <div class="modal-card">
    <button
      class="modal-close"
      onclick="closeRegisterModal()"
      aria-label="Cerrar"
    >
      ×
    </button>
    <div class="modal-icon" id="regModalIcon">🔐</div>
    <div class="modal-title" id="regModalTitle">
      Crea tu cuenta gratis
    </div>
    <div class="modal-sub" id="regModalSub">
      Para comprar en Shizen necesitas una cuenta. ¡Es gratis y rápido!
    </div>
    <button class="btn-modal-primary">
      Crear cuenta gratis
    </button>
    <button
      class="btn-modal-secondary"
      onclick="openAccountLogin()"
    >
      Ya tengo cuenta
    </button>
    <div class="offer-warning hidden" id="offerWarning">
      ⚠ Oferta por tiempo limitado - no pierdas el
      descuento! Oferta por tiempo limitado - no pierdas el
      descuento!
    </div>
  </div>
</div>
<?php if ($profileUser): ?>
<div class="modal-overlay" id="profileModal" onclick="handleProfileOverlayClick(event)">
  <div class="modal-card profile-card">
    <button class="modal-close" type="button" onclick="closeProfileModal()" aria-label="Cerrar">×</button>
    <div class="modal-title">Mis datos</div>
    <p class="modal-sub">Consulta y actualiza tu información de Shizen.</p>
    <form method="post" action="php/perfil.php">
      <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
      <input class="modal-input profile-field" name="nombre" value="<?= htmlspecialchars($_SESSION['usuario_nombre'] ?? '', ENT_QUOTES, 'UTF-8') ?>" placeholder="Nombre" readonly required>
      <input class="modal-input profile-field" name="apellido" value="<?= htmlspecialchars($_SESSION['usuario_apellido'] ?? '', ENT_QUOTES, 'UTF-8') ?>" placeholder="Apellido" readonly required>
      <input class="modal-input profile-wide profile-field" name="direccion" value="<?= htmlspecialchars($_SESSION['usuario_direccion'] ?? '', ENT_QUOTES, 'UTF-8') ?>" placeholder="Dirección" readonly>
      <input class="modal-input profile-field" name="ciudad" value="<?= htmlspecialchars($_SESSION['usuario_ciudad'] ?? '', ENT_QUOTES, 'UTF-8') ?>" placeholder="Ciudad" readonly>
      <button class="btn-modal-primary" id="profileEditButton" type="button" onclick="enableProfileEditing()">Editar datos</button>
      <button class="btn-modal-primary profile-save-button" id="profileSaveButton" type="submit" hidden>Guardar cambios</button>
    </form>
    <form class="profile-logout-form" action="auth/logout.php" method="post">
      <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
      <button class="profile-logout-button" type="submit">Cerrar sesión</button>
    </form>
  </div>
</div>
<?php endif; ?>
<button class="chat-bubble" type="button" onclick="toggleChat()" aria-label="Abrir chat">
  <img src="assets/shizen-chat-leaf.png" alt="">
</button>
<div class="chat-panel" id="chatPanel" aria-hidden="true">
  <div class="chat-header"><strong>Ayuda Shizen</strong><button type="button" onclick="toggleChat()" aria-label="Cerrar chat">×</button></div>
  <div class="chat-body"><p>¡Hola! ¿En qué podemos ayudarte?</p><button type="button" onclick="this.textContent='Un asesor te responderá pronto.'">Hablar con un asesor</button></div>
</div>
<div class="modal-overlay" id="cartModal" onclick="handleCartOverlayClick(event)">
  <div class="modal-card cart-card">
    <button class="modal-close" type="button" onclick="closeCart()" aria-label="Cerrar">×</button>
    <div class="modal-title">Tu carrito</div>
    <div id="cartItems" class="cart-items"></div>
    <div class="cart-total-row" id="cartTotalRow">
      <span>Total</span>
      <strong id="cartTotal">$0</strong>
    </div>
    <button class="btn-modal-primary" type="button" id="checkoutButton" onclick="openCheckout()">
      Continuar compra
    </button>
  </div>
</div>
<div class="modal-overlay" id="checkoutModal" onclick="handleCheckoutOverlayClick(event)">
  <div class="modal-card checkout-card">
    <button class="modal-close" type="button" onclick="closeCheckout()" aria-label="Cerrar">×</button>
    <div class="modal-title">Datos de entrega</div>
    <p class="modal-sub">Completa tus datos para registrar el pedido.</p>
    <form method="post" action="php/registrar_pedido.php" onsubmit="prepareCheckout(event)">
      <input type="hidden" name="items" id="checkoutItems">
      <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
      <div class="checkout-fields">
        <input class="modal-input" name="nombre" value="<?= htmlspecialchars($_SESSION['usuario_nombre'] ?? '', ENT_QUOTES, 'UTF-8') ?>" placeholder="Nombre" required maxlength="60">
        <input class="modal-input" name="apellido" value="<?= htmlspecialchars($_SESSION['usuario_apellido'] ?? '', ENT_QUOTES, 'UTF-8') ?>" placeholder="Apellido" required maxlength="60">
        <input class="modal-input" name="numero_documento" placeholder="Número de documento" required maxlength="20" inputmode="numeric">

        <input class="modal-input checkout-wide" name="correo" type="email" value="<?= htmlspecialchars($_SESSION['usuario_email'] ?? '', ENT_QUOTES, 'UTF-8') ?>" placeholder="Correo electrónico" required maxlength="120">
        <input class="modal-input checkout-wide" name="direccion" value="<?= htmlspecialchars($_SESSION['usuario_direccion'] ?? '', ENT_QUOTES, 'UTF-8') ?>" placeholder="Dirección de entrega" required maxlength="255">
        <input class="modal-input checkout-wide" name="localidad" value="<?= htmlspecialchars($_SESSION['usuario_ciudad'] ?? '', ENT_QUOTES, 'UTF-8') ?>" placeholder="Localidad (opcional)" maxlength="60">
        <select class="modal-input checkout-wide" name="metodo_pago" required>
          <option value="" disabled selected>Método de pago</option>
          <option value="Contraentrega">Contraentrega (efectivo)</option>
          <option value="Transferencia">Transferencia bancaria</option>
          <option value="Nequi">Nequi</option>
          <option value="Daviplata">Daviplata</option>
        </select>
      </div>
      <p id="checkoutError" class="login-error" hidden></p>
      <button class="btn-modal-primary" type="submit">Registrar pedido</button>
    </form>
  </div>
</div>
