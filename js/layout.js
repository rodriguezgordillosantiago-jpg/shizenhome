/* Carga los bloques de interfaz para mantener cada vista editable de forma aislada.
   En index.php la navegación ya viene incluida; index.html la carga aquí. */
var layoutSections = [
  ["navigation", "forms/navegacion.php"],
  ["overlays", "forms/modales.php"],
];

var appContent = document.getElementById("app-content");
if (appContent && !appContent.innerHTML.trim()) {
  layoutSections.splice(0, 0, [
    "app-content",
    "forms/home.php",
  ]);
}

window.shizenLayoutReady = Promise.all(
  layoutSections
    .filter(function (section) {
      var el = document.getElementById(section[0]);
      return el && !el.innerHTML.trim();
    })
    .map(function (section) {
      return fetch(section[1] + "?v=20260905-1", {
        cache: "no-store",
      })
        .then(function (response) {
          if (!response.ok)
            throw new Error(
              "No se pudo cargar " + section[1],
            );
          return response.text();
        })
        .then(function (html) {
          var targetEl = document.getElementById(
            section[0],
          );
          if (targetEl) {
            targetEl.insertAdjacentHTML("beforeend", html);
          }
        });
    }),
).catch(function (error) {
  console.error(error);
  var targetApp = document.getElementById("app-content");
  if (targetApp && !targetApp.innerHTML.trim()) {
    targetApp.innerHTML =
      '<p class="load-error">No pudimos cargar la aplicacion. Actualiza la pagina e intentalo de nuevo.</p>';
  }
});
