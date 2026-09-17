<div class="view document-page">
  <div class="join-page">
    <div class="join-left" style="background:linear-gradient(135deg,#bf360c,#f57c00)">
      <div class="join-left-bg" style="background-image:url('https://images.unsplash.com/photo-1611068562065-994ba66501ba?w=800&fit=crop&auto=format')"></div>
      <div class="join-left-content">
        <img src="../assets/logo_repartidor.png" alt="Shizen" class="join-left-logo" />
        <span class="join-left-badge">Para repartidores</span>
        <h2>Gana dinero recorriendo la ciudad</h2>
        <p>Sé repartidor Shizen: horarios flexibles, mejores tarifas y una comunidad que cuida el planeta.</p>
        <div class="benefit-grid">
          <div class="benefit-card">
            <div class="b-icon">💰</div>
            <div class="b-title">Ganancias top</div>
            <div class="b-desc">Las mejores tarifas del mercado</div>
          </div>
          <div class="benefit-card">
            <div class="b-icon">⌛</div>
            <div class="b-title">Horarios libres</div>
            <div class="b-desc">Trabaja cuando quieras</div>
          </div>
          <div class="benefit-card">
            <div class="b-icon">🏥</div>
            <div class="b-title">Seguro incluido</div>
            <div class="b-desc">Accidentes cubiertos en cada domicilio</div>
          </div>
          <div class="benefit-card">
            <div class="b-icon">📱</div>
            <div class="b-title">App intuitiva</div>
            <div class="b-desc">Gestiona todo desde tu celular</div>
          </div>
        </div>
      </div>
    </div>
    <div class="join-right">
      <div class="join-form-wrap document-form-wrap">
        <a class="join-back" href="registro_repartidor.php">← Volver a tus datos</a>
        <div class="progress-steps">
          <div class="step-dot done">✓</div>
          <div class="step-line done"></div>
          <div class="step-dot active">2</div>
        </div>
        <form method="post" action="documentos_repartidor.php" enctype="multipart/form-data">
          <h2>Sube tus documentos</h2>
          <p class="form-sub">Aceptamos archivos PDF. La foto es solo para identificarte.</p>
          <div class="form-group">
            <label for="cedula">Número de cédula</label>
            <input class="form-input" id="cedula" type="text" name="cedula" inputmode="numeric" pattern="[0-9]{6,12}" placeholder="Número de identificación" required />
          </div>
          <div class="photo-upload-box">
            <input class="upload-hidden-input" id="foto-repartidor" type="file" name="foto_repartidor" accept="image/*" required />
            <div class="photo-upload-wrapper">
              <label for="foto-repartidor" class="photo-upload-button" id="foto-repartidor-btn">
                <img id="foto-repartidor-preview" class="photo-preview-img" style="display: none;" alt="Vista previa de tu foto" />
                <div class="photo-placeholder" id="foto-repartidor-placeholder">
                  <span class="photo-icon">📷</span>
                  <span>Agregar foto</span>
                </div>
              </label>
              <div class="photo-filename" id="foto-repartidor-filename">Ningún archivo seleccionado</div>
            </div>
          </div>
          <div class="document-upload-grid">
            <div class="document-upload-item">
              <span>Cédula</span>
              <input class="upload-hidden-input" id="documento-cedula" type="file" name="documento_cedula" accept="application/pdf" required />
              <label class="upload-pdf-button" for="documento-cedula" id="documento-cedula-label">📄 Cédula</label>
            </div>

            <?php if ($esMotorizado): ?>
            <div class="document-upload-item">
              <span>Licencia de conducción</span>
              <input class="upload-hidden-input" id="licencia" type="file" name="licencia_conduccion" accept="application/pdf" required />
              <label class="upload-pdf-button" for="licencia" id="licencia-label">📄 Licencia</label>
            </div>
            <div class="document-upload-item">
              <span>Tarjeta de propiedad</span>
              <input class="upload-hidden-input" id="tarjeta" type="file" name="tarjeta_propiedad" accept="application/pdf" required />
              <label class="upload-pdf-button" for="tarjeta" id="tarjeta-label">📄 Tarjeta</label>
            </div>
            <div class="document-upload-item">
              <span>SOAT vigente</span>
              <input class="upload-hidden-input" id="soat" type="file" name="soat" accept="application/pdf" required />
              <label class="upload-pdf-button" for="soat" id="soat-label">📄 SOAT</label>
            </div>
            <?php endif; ?>
          </div>
          <button class="btn-primary-full" type="submit">Finalizar solicitud</button>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
  (function() {
    var inputFoto = document.getElementById("foto-repartidor");
    var preview = document.getElementById("foto-repartidor-preview");
    var placeholder = document.getElementById("foto-repartidor-placeholder");
    var filename = document.getElementById("foto-repartidor-filename");

    if (inputFoto) {
      inputFoto.addEventListener("change", function () {
        if (this.files && this.files[0]) {
          var file = this.files[0];
          preview.src = URL.createObjectURL(file);
          preview.style.display = "block";
          if (placeholder) placeholder.style.display = "none";
          if (filename) {
            filename.textContent = file.name;
            filename.classList.add("selected");
          }
        }
      });
    }

    // PDF files listener to show filename on button
    var pdfInputs = document.querySelectorAll(".document-upload-item input[type='file']");
    pdfInputs.forEach(function(input) {
      input.addEventListener("change", function() {
        var label = this.parentElement.querySelector(".upload-pdf-button");
        if (label && this.files && this.files[0]) {
          label.textContent = "✓ " + this.files[0].name;
          label.classList.add("uploaded");
        }
      });
    });
  })();
</script>
