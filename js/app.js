/* ── Countdown timers ──────────────────────────────────────────────── */
var countdowns = {};

function formatTime(secs) {
  var h = Math.floor(secs / 3600);
  var m = Math.floor((secs % 3600) / 60);
  var s = secs % 60;
  return [h, m, s]
    .map(function (n) {
      return String(n).padStart(2, "0");
    })
    .join(":");
}

function startCountdowns() {
  if (typeof ALL_PROMOS === "undefined") return;
  ALL_PROMOS.forEach(function (p) {
    countdowns[p.id] = p.endsIn;
  });
  setInterval(function () {
    ALL_PROMOS.forEach(function (p) {
      if (countdowns[p.id] > 0) {
        countdowns[p.id]--;
        var el = document.getElementById("timer-" + p.id);
        if (el)
          el.textContent = formatTime(countdowns[p.id]);
      }
    });
  }, 1000);
}

/* ── View management ───────────────────────────────────────────────── */
function showView(viewId) {
  document.querySelectorAll(".view").forEach(function (v) {
    v.classList.remove("active");
  });
  var target = document.getElementById("view-" + viewId);
  if (target) target.classList.add("active");
  window.scrollTo({ top: 0, behavior: "smooth" });

  var floatBtn = document.getElementById("floatBtn");
  if (!floatBtn) return;
  if (viewId === "home") {
    floatBtn.classList.remove("hidden");
  } else {
    floatBtn.classList.add("hidden");
  }
}

/* ── Category cards ────────────────────────────────────────────────── */
function renderCategoryCards() {
  var scroll = document.getElementById("categoryScroll");
  if (!scroll) return;
  scroll.innerHTML = "";
  CATEGORIES.forEach(function (cat) {
    var card = document.createElement("a");
    card.className = "cat-card";
    card.href = new URL(
      "php/categorias.php?categoria=" + cat.dbId,
      document.baseURI,
    ).href;
    card.style.background = cat.bg;
    card.innerHTML =
      '<div class="cat-emoji-wrap">' +
      cat.icon +
      "</div>" +
      '<div class="cat-label">' +
      cat.label +
      "</div>" +
      '<div class="cat-pill">Ver platos</div>';
    scroll.appendChild(card);
  });
}

function renderCategoryPage() {
  var itemsGrid = document.getElementById("itemsGrid");
  var catName = document.getElementById("catName");
  if (!itemsGrid || !catName) return;
  if (
    typeof CATEGORIES === "undefined" ||
    !CATEGORIES.length
  )
    return;

  var params = new URLSearchParams(window.location.search);
  var catParam = params.get("categoria") || "1";
  var category =
    CATEGORIES.find(function (c) {
      return (
        String(c.dbId) === catParam || c.id === catParam
      );
    }) || CATEGORIES[0];

  if (category) {
    var catHero = document.getElementById("catHero");
    var catIcon = document.getElementById("catIcon");
    var catDesc = document.getElementById("catDesc");
    if (
      catHero &&
      category.coverImg &&
      !catHero.style.backgroundImage
    ) {
      catHero.style.backgroundImage =
        "url('" + category.coverImg + "')";
    }
    if (catIcon && !catIcon.textContent.trim())
      catIcon.textContent = category.icon;
    if (catName && !catName.textContent.trim())
      catName.textContent = category.label;
    if (catDesc && !catDesc.textContent.trim())
      catDesc.textContent = category.description;
  }
}

/* ── Promos ────────────────────────────────────────────────────────── */
function renderPromoFilters() {
  var wrap = document.getElementById("promoFilterTabs");
  if (!wrap) return;
  wrap.innerHTML = "";
  PROMO_CATS.forEach(function (cat) {
    var btn = document.createElement("button");
    btn.className =
      "filter-tab" + (cat === "Todos" ? " active" : "");
    btn.textContent = cat;
    btn.onclick = function () {
      filterPromos(cat, btn);
    };
    wrap.appendChild(btn);
  });
}

function filterPromos(cat, btn) {
  document
    .querySelectorAll(".filter-tab")
    .forEach(function (b) {
      b.classList.remove("active");
    });
  btn.classList.add("active");
  document
    .querySelectorAll(".promo-card")
    .forEach(function (card) {
      card.style.display =
        cat === "Todos" || card.dataset.cat === cat
          ? ""
          : "none";
    });
}

function getActivePromosList() {
  if (
    window.DB_PROMOS &&
    Array.isArray(window.DB_PROMOS) &&
    window.DB_PROMOS.length > 0
  ) {
    return window.DB_PROMOS.map(function (item, idx) {
      var catName = "Todos";
      if (typeof CATEGORIES !== "undefined") {
        var foundCat = CATEGORIES.find(function (c) {
          return c.dbId === Number(item.id_categoria);
        });
        if (foundCat) catName = foundCat.name;
      }
      var discPct = 0;
      var normalPrice = parseFloat(item.precio) || 0;
      var promoPrice =
        parseFloat(item.precio_promocion) || normalPrice;
      if (normalPrice > 0 && promoPrice < normalPrice) {
        discPct = Math.round(
          (1 - promoPrice / normalPrice) * 100,
        );
      }
      return {
        id: Number(item.id) || idx + 100,
        dbId: Number(item.id) || idx + 100,
        title: item.promo_nombre,
        emoji: "🔥",
        restaurant:
          item.negocio_nombre || "Restaurante Shizen",
        cat: catName,
        desc: item.promo_desc || "",
        badge: discPct > 0 ? discPct + "% OFF" : "PROMO",
        badgeColor: "#ea580c",
        tag: "Plato en Oferta",
        img: item.imagen_url || "assets/image-6.png",
        price:
          "$" +
          Math.round(promoPrice).toLocaleString("es-CO"),
        originalPrice:
          "$" +
          Math.round(normalPrice).toLocaleString("es-CO"),
        discount: discPct > 0 ? discPct + "%" : "OFERTA",
        rawPriceNum: promoPrice,
        endsIn: 7200,
      };
    });
  }
  return ALL_PROMOS;
}

function renderPromos() {
  var grid = document.getElementById("promosGrid");
  if (!grid) return;
  grid.innerHTML = "";
  var list = getActivePromosList();
  list.forEach(function (p) {
    var div = document.createElement("div");
    div.className = "promo-card";
    div.dataset.cat = p.cat;
    div.innerHTML =
      '<div class="promo-card-img" style="background-image:url(\'' +
      p.img +
      "')\">" +
      '<span class="promo-badge" style="background:' +
      p.badgeColor +
      '">' +
      p.badge +
      "</span>" +
      '<span class="promo-tag-top">' +
      p.tag +
      "</span>" +
      "</div>" +
      '<div class="promo-card-body">' +
      "<h3>" +
      p.emoji +
      " " +
      p.title +
      "</h3>" +
      '<div class="promo-restaurant">&#127978; ' +
      p.restaurant +
      "</div>" +
      '<p class="promo-desc">' +
      p.desc +
      "</p>" +
      '<div class="promo-timer">&#8987; <span id="timer-' +
      p.id +
      '">' +
      formatTime(p.endsIn) +
      "</span></div>" +
      '<div class="promo-prices">' +
      '<span class="promo-price-new">' +
      p.price +
      "</span>" +
      '<span class="promo-price-old">' +
      p.originalPrice +
      "</span>" +
      '<span class="promo-discount">-' +
      p.discount +
      "</span>" +
      "</div>" +
      '<button class="btn-add-cart btn-promo-cart" type="button" onclick="addPromoToCart(' +
      p.id +
      ')">Añadir al carrito</button>' +
      "</div>";
    grid.appendChild(div);
  });
}

function addPromoToCart(promoId) {
  var list = getActivePromosList();
  var p = list.find(function (item) {
    return item.id === promoId;
  });
  if (!p) return;
  var price =
    p.rawPriceNum ||
    parseFloat(
      String(p.price || "0").replace(/[^0-9]/g, ""),
    ) ||
    0;
  var dbId = p.dbId || p.id || 6;
  addToCart({
    id: dbId,
    name: p.title,
    price: price,
    restaurant: p.restaurant,
    image: p.img,
  });
}

/* ── Login modal ───────────────────────────────────────────────────── */
var loginModalOpen = false;

function openProfileModal() {
  var modal = document.getElementById("profileModal");
  if (!modal) return;
  modal
    .querySelectorAll(".profile-field")
    .forEach(function (field) {
      field.setAttribute("readonly", "readonly");
    });
  var editButton = document.getElementById(
    "profileEditButton",
  );
  var saveButton = document.getElementById(
    "profileSaveButton",
  );
  if (editButton) editButton.hidden = false;
  if (saveButton) saveButton.hidden = true;
  modal.classList.add("open");
}

function closeProfileModal() {
  var modal = document.getElementById("profileModal");
  if (modal) modal.classList.remove("open");
}

function enableProfileEditing() {
  document
    .querySelectorAll("#profileModal .profile-field")
    .forEach(function (field) {
      field.removeAttribute("readonly");
    });
  var editButton = document.getElementById(
    "profileEditButton",
  );
  var saveButton = document.getElementById(
    "profileSaveButton",
  );
  if (editButton) editButton.hidden = true;
  if (saveButton) saveButton.hidden = false;
}

function handleProfileOverlayClick(event) {
  if (event.target.id === "profileModal")
    closeProfileModal();
}

function toggleChat() {
  var panel = document.getElementById("chatPanel");
  if (!panel) return;
  var isOpen = panel.classList.toggle("open");
  panel.setAttribute("aria-hidden", String(!isOpen));
}

function showLoginErrorFromQuery() {
  if (
    !window.loginError &&
    window.location.hash !== "#error"
  )
    return;
  var error = document.getElementById("login-error");
  if (error) error.hidden = false;
  openLoginModal();
}

if (window.shizenLayoutReady) {
  window.shizenLayoutReady.then(showLoginErrorFromQuery);
}

function toggleLoginModal() {
  if (loginModalOpen) closeLoginModal();
  else openLoginModal();
}

function openLoginModal() {
  document
    .getElementById("loginModal")
    .classList.add("open");
  document
    .getElementById("ingresoBtn")
    .classList.add("active");
  loginModalOpen = true;
}

function openAccessModal() {
  var registerModal =
    document.getElementById("registerModal");
  if (registerModal) registerModal.classList.remove("open");
  openLoginModal();
}

/* ── Carrito y checkout ────────────────────────────────────────────── */
var cart = JSON.parse(
  localStorage.getItem("shizenCart") || "[]",
);
var UI_TEXT = {
  emptyCart: "Tu carrito está vacío.",
  decrease: "Disminuir cantidad",
  increase: "Aumentar cantidad",
  offerTitle: "¡Aprovecha esta oferta!",
  offerDescription:
    "Crea tu cuenta gratis y aplica el descuento automáticamente en tu primer pedido.",
  accountTitle: "Crea tu cuenta gratis",
  accountDescription:
    "Para comprar en Shizen necesitas una cuenta. ¡Es gratis y rápido!",
};

function formatMoney(value) {
  return "$" + Number(value).toLocaleString("es-CO");
}

function saveCart() {
  localStorage.setItem("shizenCart", JSON.stringify(cart));
  updateCartCount();
  syncCartToDB();
}

function syncCartToDB() {
  var syncUrl =
    window.shizenSyncCartUrl || "php/sync_carrito.php";
  fetch(syncUrl, {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify(cart),
  }).catch(function (err) {
    console.warn("Cart DB sync error:", err);
  });
}

function loadCartFromDB() {
  var syncUrl =
    window.shizenSyncCartUrl || "php/sync_carrito.php";
  fetch(syncUrl)
    .then(function (res) {
      return res.json();
    })
    .then(function (data) {
      if (
        data &&
        data.logged_in &&
        Array.isArray(data.items) &&
        data.items.length > 0
      ) {
        cart = data.items;
        localStorage.setItem(
          "shizenCart",
          JSON.stringify(cart),
        );
        updateCartCount();
        renderCart();
      }
    })
    .catch(function (err) {
      console.warn("Cart DB load error:", err);
    });
}

function updateCartCount() {
  var count = document.getElementById("cartCount");
  if (count)
    count.textContent = cart.reduce(function (sum, item) {
      return sum + item.quantity;
    }, 0);
}

function groupCartByRestaurant() {
  var groupsMap = {};
  cart.forEach(function (item) {
    var key = String(
      item.businessId || item.restaurant || "general",
    );
    if (!groupsMap[key]) {
      groupsMap[key] = {
        businessId: item.businessId || 0,
        restaurantName:
          item.restaurant || "Restaurante Shizen",
        updatedAt: item.addedAt || 0,
        items: [],
      };
    }
    groupsMap[key].items.push(item);
    if ((item.addedAt || 0) > groupsMap[key].updatedAt) {
      groupsMap[key].updatedAt = item.addedAt || 0;
    }
  });

  var groupsArray = [];
  for (var k in groupsMap) {
    if (
      Object.prototype.hasOwnProperty.call(groupsMap, k)
    ) {
      groupsArray.push(groupsMap[k]);
    }
  }

  groupsArray.sort(function (a, b) {
    return b.updatedAt - a.updatedAt;
  });

  return groupsArray;
}

var activeCartBusinessId = null;

function showCartList() {
  activeCartBusinessId = null;
  selectedCheckoutBusinessId = null;
  renderCart();
}

function selectCartGroup(businessId) {
  activeCartBusinessId = businessId;
  selectedCheckoutBusinessId = businessId;
  renderCart();
}

function groupCartByRestaurant() {
  var groupsMap = {};
  cart.forEach(function (item) {
    var key = String(
      item.businessId || item.restaurant || "general",
    );
    if (!groupsMap[key]) {
      groupsMap[key] = {
        businessId: item.businessId || 0,
        restaurantName:
          item.restaurant || "Restaurante Shizen",
        updatedAt: item.addedAt || 0,
        items: [],
      };
    }
    groupsMap[key].items.push(item);
    if ((item.addedAt || 0) > groupsMap[key].updatedAt) {
      groupsMap[key].updatedAt = item.addedAt || 0;
    }
  });

  var groupsArray = [];
  for (var k in groupsMap) {
    if (
      Object.prototype.hasOwnProperty.call(groupsMap, k)
    ) {
      groupsArray.push(groupsMap[k]);
    }
  }

  groupsArray.sort(function (a, b) {
    return b.updatedAt - a.updatedAt;
  });

  return groupsArray;
}

function addToCart(product) {
  var busId = product.businessId || product.id_negocio || 0;
  var restName =
    product.restaurant ||
    (busId
      ? "Restaurante #" + busId
      : "Restaurante Shizen");
  var now = Date.now();

  var existing = cart.find(function (item) {
    return item.id === product.id;
  });

  if (existing) {
    existing.quantity += 1;
    existing.addedAt = now;
    if (!existing.businessId) existing.businessId = busId;
    if (!existing.restaurant)
      existing.restaurant = restName;
  } else {
    cart.push({
      id: product.id,
      name: product.name,
      price: product.price,
      restaurant: restName,
      businessId: busId,
      image: product.image || "assets/image-6.png",
      quantity: 1,
      addedAt: now,
    });
  }
  saveCart();
  renderCart();
  // El carrito no se abre automáticamente al agregar producto (según indicación)
}

function cartTotal() {
  return cart.reduce(function (sum, item) {
    return sum + item.price * item.quantity;
  }, 0);
}

function renderCart() {
  var itemsContainer = document.getElementById("cartItems");
  var total = document.getElementById("cartTotal");
  var totalRow = document.getElementById("cartTotalRow");
  var checkout = document.getElementById("checkoutButton");
  if (!itemsContainer || !total) return;

  if (!cart.length) {
    activeCartBusinessId = null;
    itemsContainer.innerHTML =
      '<p class="cart-empty">' + UI_TEXT.emptyCart + "</p>";
    total.textContent = formatMoney(0);
    if (totalRow) totalRow.style.display = "none";
    if (checkout) {
      checkout.disabled = true;
      checkout.style.display = "none";
    }
    return;
  }

  var groups = groupCartByRestaurant();
  var totalOverall = cartTotal();

  // Caso 1: Carrito específico seleccionado (Vista Detalle de platos)
  if (activeCartBusinessId !== null) {
    var targetGroup = groups.find(function (g) {
      return (
        String(g.businessId) ===
        String(activeCartBusinessId)
      );
    });

    if (!targetGroup) {
      activeCartBusinessId = null;
      renderCart();
      return;
    }

    var groupSubtotal = targetGroup.items.reduce(function (
      sum,
      item,
    ) {
      return sum + item.price * item.quantity;
    }, 0);

    var itemsHtml = targetGroup.items
      .map(function (item) {
        return (
          '<div class="cart-item">' +
          '<img class="cart-item-image" src="' +
          (item.image || "assets/image-6.png") +
          '" alt="' +
          item.name +
          '">' +
          '<div class="cart-item-info">' +
          "<strong>" +
          item.name +
          "</strong>" +
          "<small>" +
          formatMoney(item.price) +
          " / unidad</small>" +
          "<span>Subtotal: " +
          formatMoney(item.price * item.quantity) +
          "</span>" +
          "</div>" +
          '<div class="cart-quantity">' +
          '<button type="button" aria-label="' +
          UI_TEXT.decrease +
          '" title="' +
          UI_TEXT.decrease +
          '" onclick="changeCartQuantity(' +
          item.id +
          ',-1)">−</button>' +
          "<b>" +
          item.quantity +
          "</b>" +
          '<button type="button" aria-label="' +
          UI_TEXT.increase +
          '" title="' +
          UI_TEXT.increase +
          '" onclick="changeCartQuantity(' +
          item.id +
          ',1)">+</button>' +
          "</div>" +
          "</div>"
        );
      })
      .join("");

    itemsContainer.innerHTML =
      '<button type="button" class="btn-back-carts" onclick="showCartList()">← Volver a mis carritos</button>' +
      '<div class="cart-group-card" data-business-id="' +
      targetGroup.businessId +
      '">' +
      '<div class="cart-group-header">' +
      '<div class="cart-group-title">🏬 ' +
      targetGroup.restaurantName +
      "</div>" +
      "</div>" +
      '<div class="cart-group-items">' +
      itemsHtml +
      "</div>" +
      '<div class="cart-group-footer">' +
      '<div class="cart-group-subtotal">' +
      "<span>Subtotal Pedido:</span>" +
      "<strong>" +
      formatMoney(groupSubtotal) +
      "</strong>" +
      "</div>" +
      '<div class="cart-group-actions">' +
      '<button type="button" class="btn-cart-clear" title="Vaciar este carrito" onclick="clearBusinessCart(' +
      targetGroup.businessId +
      ')">🗑️ Vaciar</button>' +
      "</div>" +
      "</div>" +
      "</div>";

    total.textContent = formatMoney(groupSubtotal);
    if (totalRow) totalRow.style.display = "";
    if (checkout) {
      checkout.disabled = false;
      checkout.style.display = "";
    }
    return;
  }

  // Caso 2: Listado general de carritos por restaurante ("cajas")
  itemsContainer.innerHTML = groups
    .map(function (group, gIdx) {
      var isNewestGroup = gIdx === 0 && groups.length > 1;
      var groupSubtotal = group.items.reduce(function (
        sum,
        item,
      ) {
        return sum + item.price * item.quantity;
      }, 0);
      var totalItemsCount = group.items.reduce(function (
        sum,
        item,
      ) {
        return sum + item.quantity;
      }, 0);

      var cartNumber = groups.length - gIdx;

      return (
        '<div class="cart-selector-box" onclick="selectCartGroup(' +
        group.businessId +
        ')">' +
        '<div class="cart-box-header">' +
        '<span class="cart-box-name">🛒 Carrito #' +
        cartNumber +
        " — 🏬 " +
        group.restaurantName +
        "</span>" +
        (isNewestGroup
          ? '<span class="cart-group-badge">🔥 MÁS RECIENTE</span>'
          : "") +
        "</div>" +
        '<div class="cart-box-details">' +
        '<span class="cart-box-count">📦 ' +
        totalItemsCount +
        " plato(s)</span>" +
        '<span class="cart-box-total">Total: <strong>' +
        formatMoney(groupSubtotal) +
        "</strong></span>" +
        "</div>" +
        '<div class="cart-box-arrow">Ver platos y pagar ➜</div>' +
        "</div>"
      );
    })
    .join("");

  // Vista lista: ocultar total y checkout (debe elegirse un carrito primero)
  if (totalRow) totalRow.style.display = "none";
  if (checkout) {
    checkout.disabled = true;
    checkout.style.display = "none";
  }
}

function clearBusinessCart(businessId) {
  cart = cart.filter(function (item) {
    var bId = item.businessId || 0;
    return String(bId) !== String(businessId);
  });
  activeCartBusinessId = null;
  saveCart();
  renderCart();
}

var selectedCheckoutBusinessId = null;

function openCheckoutForBusiness(businessId) {
  selectedCheckoutBusinessId = businessId;
  openCheckout();
}

function changeCartQuantity(id, delta) {
  var item = cart.find(function (entry) {
    return entry.id === id;
  });
  if (!item) return;
  item.quantity += delta;
  item.addedAt = Date.now();
  if (item.quantity <= 0)
    cart = cart.filter(function (entry) {
      return entry.id !== id;
    });
  saveCart();
  renderCart();
}

function openCart() {
  renderCart();
  var modal = document.getElementById("cartModal");
  if (modal) modal.classList.add("open");
}

function closeCart() {
  var modal = document.getElementById("cartModal");
  if (modal) modal.classList.remove("open");
}

function handleCartOverlayClick(event) {
  if (event.target === document.getElementById("cartModal"))
    closeCart();
}

function openCheckout() {
  if (!cart.length) return;
  if (!window.shizenUser) {
    window.location.href =
      "php/login.php?redirect=index.php";
    return;
  }
  closeCart();
  document
    .getElementById("checkoutModal")
    .classList.add("open");
}

function closeCheckout() {
  document
    .getElementById("checkoutModal")
    .classList.remove("open");
}

function handleCheckoutOverlayClick(event) {
  if (
    event.target ===
    document.getElementById("checkoutModal")
  )
    closeCheckout();
}

function prepareCheckout(event) {
  if (!cart.length) {
    event.preventDefault();
    return;
  }
  var checkoutList = cart;
  if (selectedCheckoutBusinessId) {
    checkoutList = cart.filter(function (item) {
      return (
        String(item.businessId || 0) ===
        String(selectedCheckoutBusinessId)
      );
    });
  }
  if (!checkoutList.length) checkoutList = cart;

  document.getElementById("checkoutItems").value =
    JSON.stringify(
      checkoutList.map(function (item) {
        return { id: item.id, quantity: item.quantity };
      }),
    );
}

updateCartCount();

function closeLoginModal() {
  document
    .getElementById("loginModal")
    .classList.remove("open");
  document
    .getElementById("ingresoBtn")
    .classList.remove("active");
  loginModalOpen = false;
}

function handleLoginOverlayClick(e) {
  if (e.target === document.getElementById("loginModal"))
    closeLoginModal();
}

function togglePassword(button) {
  var input = button.parentElement.querySelector("input");
  if (!input) return;
  var showPassword = input.type === "password";
  input.type = showPassword ? "text" : "password";
  button.classList.toggle("is-visible", showPassword);
  button.setAttribute(
    "aria-label",
    showPassword
      ? "Ocultar contraseña"
      : "Mostrar contraseña",
  );
  button.setAttribute(
    "title",
    showPassword
      ? "Ocultar contraseña"
      : "Mostrar contraseña",
  );
}

function openAccountLogin() {
  openAccessModal();
}

function goToRegistration() {
  closeLoginModal();
  var registrationSection =
    document.getElementById("registro");
  if (!registrationSection) {
    window.location.assign("index.php#registro");
    return;
  }
  registrationSection.scrollIntoView({
    behavior: "smooth",
    block: "start",
  });
  registrationSection.focus({ preventScroll: true });
}

/* ── Register modal ────────────────────────────────────────────────── */
function openRegisterModal(isOffer) {
  closeLoginModal();
  var icon = document.getElementById("regModalIcon");
  var title = document.getElementById("regModalTitle");
  var sub = document.getElementById("regModalSub");
  var warning = document.getElementById("offerWarning");
  if (isOffer) {
    icon.textContent = "🏷️";
    title.textContent = UI_TEXT.offerTitle;
    sub.textContent = UI_TEXT.offerDescription;
    warning.classList.remove("hidden");
  } else {
    icon.textContent = "🌱";
    title.textContent = UI_TEXT.accountTitle;
    sub.textContent = UI_TEXT.accountDescription;
    warning.classList.add("hidden");
  }
  document
    .getElementById("registerModal")
    .classList.add("open");
}

function closeRegisterModal() {
  document
    .getElementById("registerModal")
    .classList.remove("open");
}

function handleRegisterOverlayClick(e) {
  if (e.target === document.getElementById("registerModal"))
    closeRegisterModal();
}

/* ── Join form steps ───────────────────────────────────────────────── */
function validateSection(sectionId) {
  var section = document.getElementById(sectionId);
  var fields = section.querySelectorAll(
    "input, select, textarea",
  );

  for (var i = 0; i < fields.length; i++) {
    var field = fields[i];
    if (
      field.type !== "checkbox" &&
      field.required &&
      !field.value.trim()
    ) {
      field.setCustomValidity("Este campo es obligatorio.");
    } else {
      field.setCustomValidity("");
    }
    if (!field.checkValidity()) {
      field.reportValidity();
      field.focus();
      return false;
    }
  }
  return true;
}

function nextStep(prefix) {
  if (!validateSection(prefix + "-step1")) return;
  document
    .getElementById(prefix + "-step1")
    .classList.add("hidden");
  document
    .getElementById(prefix + "-step2")
    .classList.remove("hidden");
  var dot1 = document.getElementById(prefix + "-dot-1");
  var dot2 = document.getElementById(prefix + "-dot-2");
  var line1 = document.getElementById(prefix + "-line-1");
  dot1.className = "step-dot done";
  dot1.textContent = "✓";
  line1.classList.add("done");
  dot2.classList.add("active");
}

function submitJoin(prefix) {
  if (!validateSection(prefix + "-step2")) return;
  document
    .getElementById(prefix + "-step2")
    .classList.add("hidden");
  document
    .getElementById(prefix + "-success")
    .classList.remove("hidden");
  var dot2 = document.getElementById(prefix + "-dot-2");
  dot2.className = "step-dot done";
  dot2.textContent = "✓";
}

function resetJoinForm(prefix) {
  document
    .getElementById(prefix + "-step1")
    .classList.remove("hidden");
  document
    .getElementById(prefix + "-step2")
    .classList.add("hidden");
  document
    .getElementById(prefix + "-success")
    .classList.add("hidden");
  var dot1 = document.getElementById(prefix + "-dot-1");
  var dot2 = document.getElementById(prefix + "-dot-2");
  var line1 = document.getElementById(prefix + "-line-1");
  dot1.className = "step-dot active";
  dot1.textContent = "1";
  dot2.className = "step-dot";
  dot2.textContent = "2";
  line1.className = "step-line";
}

/* ── Mobile nav ────────────────────────────────────────────────────── */
function toggleMobileMenu() {
  var menu = document.getElementById("mobileMenu");
  menu.classList.toggle("open");
  document.getElementById("hamburgerBtn").textContent =
    menu.classList.contains("open") ? "✕" : "☰";
}

function initializeRatingStars() {
  document
    .querySelectorAll(".rating-stars")
    .forEach(function (group) {
      var stars = Array.from(
        group.querySelectorAll(".rating-star"),
      );
      stars.forEach(function (star) {
        star
          .querySelector("input")
          .addEventListener("change", function () {
            var selected = Number(this.value);
            stars.forEach(function (entry) {
              entry.classList.toggle(
                "is-selected",
                Number(
                  entry.querySelector("input").value,
                ) <= selected,
              );
            });
          });
      });
    });
}

/* ── Init ──────────────────────────────────────────────────────────── */
function initializeApp() {
  renderCategoryCards();
  renderCategoryPage();
  renderPromoFilters();
  renderPromos();
  startCountdowns();
  initializeRatingStars();
  loadCartFromDB();

  if (window.location.hash === "#registro") {
    var registrationSection =
      document.getElementById("registro");
    if (registrationSection) {
      registrationSection.scrollIntoView({
        behavior: "smooth",
        block: "start",
      });
      registrationSection.focus({ preventScroll: true });
    }
  }
}

Promise.resolve(window.shizenLayoutReady).then(
  initializeApp,
);

document.addEventListener("keydown", function (e) {
  if (e.key === "Escape") closeLoginModal();
});
