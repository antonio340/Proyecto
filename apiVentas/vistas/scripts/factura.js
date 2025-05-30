    // Verificar que existe token en localStorage; de lo contrario, redirigir al login
    const token = localStorage.getItem("token");
    if (!token) {
      alert("No existe token. Por favor, inicia sesión.");
      window.location.href = "index.html"; // Suponiendo que este es el login
    }

    // URL del endpoint de factura. Ajusta según tu ruta
    const API_URL = "http://localhost/apiVentas/api/public/factura";

    // Función para mostrar mensajes (éxito o error)
    function displayMessage(msg, isError = false) {
      const messageEl = document.getElementById("message");
      messageEl.textContent = msg;
      messageEl.style.color = isError ? "red" : "green";
      setTimeout(() => { messageEl.textContent = "" }, 5000);
    }

    // Función para consultar (o generar/actualizar) la factura
    async function consultarFactura() {
      try {
        const response = await fetch(API_URL, {
          method: "GET",
          headers: {
            "Content-Type": "application/json",
            "Authorization": "Bearer " + token
          }
          // No se envía body ya que la lógica se basa en el token
        });

        const result = await response.json();

        if (!response.ok) {
          displayMessage(result.error || result.message || "Error al procesar la factura.", true);
          document.getElementById("facturaInfo").innerHTML = "";
        } else {
          // Mostrar la factura recibida
          // Se espera que el resultado tenga una estructura similar a:
          // { message: "...", factura: { idFactura, idUsuario, subtotalFactura, ivaFactura, totalFactura } }
          const factura = result.factura;
          let html = `
            <table>
              <thead>
                <tr>
                  <th>ID Factura</th>
                  <th>ID Usuario</th>
                  <th>Subtotal</th>
                  <th>IVA</th>
                  <th>Total</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>${factura.idFactura}</td>
                  <td>${factura.idUsuario}</td>
                  <td>${factura.subtotalFactura}</td>
                  <td>${factura.ivaFactura}</td>
                  <td>${factura.totalFactura}</td>
                </tr>
              </tbody>
            </table>
          `;
          document.getElementById("facturaInfo").innerHTML = html;
          displayMessage(result.message || "Factura procesada correctamente.");
        }
      } catch (error) {
        console.error("Error:", error);
        displayMessage("Error de conexión con el servidor.", true);
      }
    }

    // Asignar evento al botón para consultar la factura
    document.getElementById("btnConsultarFactura").addEventListener("click", function(e) {
      e.preventDefault();
      consultarFactura();
    });

    // Puedes llamar a consultarFactura() al cargar la página si deseas que se consulte automáticamente
    // consultarFactura();