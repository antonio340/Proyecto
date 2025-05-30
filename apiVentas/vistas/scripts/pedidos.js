   // Verificar que existe un token en localStorage. Si no, redirigir al login
    const token = localStorage.getItem("token");
    if (!token) {
      alert("No se encontró token. Por favor, inicia sesión.");
      window.location.href = "index.html";
    }

    // API URL para pedidos
    const API_URL = "http://localhost/apiVentas/api/public/pedidos";

    // Función para enviar peticiones a la API
    async function sendRequest(method, data = {}) {
      const options = {
        method: method,
        headers: {
          "Content-Type": "application/json",
          "Authorization": "Bearer " + token
        },
        body: JSON.stringify(data)
      };
      if (method === "GET") delete options.body;
      try {
        const response = await fetch(API_URL, options);
        const result = await response.json();
        return { ok: response.ok, result };
      } catch (error) {
        console.error("Error en la conexión:", error);
        return { ok: false, result: { message: "Error en la conexión con el servidor" } };
      }
    }

    // Función para mostrar mensajes en pantalla
    function displayMessage(msg, isError = false) {
      const messageEl = document.getElementById("message");
      messageEl.textContent = msg;
      messageEl.style.color = isError ? "red" : "green";
      setTimeout(() => { messageEl.textContent = "" }, 3000);
    }

    // Crear Pedido
    document.getElementById("crearPedidoForm").addEventListener("submit", async function (e) {
      e.preventDefault();
      const idProducto = document.getElementById("pedidoIdProducto").value.trim();
      if (idProducto === "") {
        displayMessage("Ingrese el ID del producto", true);
        return;
      }

      // Enviamos la solicitud para crear el pedido (POST)
      const { ok, result } = await sendRequest("POST", { idProducto });
      if (ok) {
        displayMessage(result.message || "Pedido creado correctamente.");
        loadPedidos();
      } else {
        displayMessage(result.error || result.message || "Error al crear el pedido.", true);
      }
    });

    // Cargar Pedidos
    document.getElementById("cargarPedidos").addEventListener("click", function () {
      loadPedidos();
    });

    async function loadPedidos() {
      const { ok, result } = await sendRequest("GET");
      const pedidosListDiv = document.getElementById("pedidosList");
      if (ok) {
        if (result.pedidos && result.pedidos.length > 0) {
          let html = "<table><thead><tr><th>ID Pedido</th><th>ID Usuario</th><th>ID Producto</th><th>Nombre Producto</th><th>Precio Producto</th></tr></thead><tbody>";
          result.pedidos.forEach(pedido => {
            html += `<tr>
                        <td>${pedido.idPedido}</td>
                        <td>${pedido.idUsuario}</td>
                        <td>${pedido.idProducto}</td>
                        <td>${pedido.nombreProducto}</td>
                        <td>${pedido.precioProducto}</td>
                      </tr>`;
          });
          html += "</tbody></table>";
          pedidosListDiv.innerHTML = html;
        } else {
          pedidosListDiv.innerHTML = "<p>No se encontraron pedidos.</p>";
        }
      } else {
        pedidosListDiv.innerHTML = "<p>Error al cargar pedidos.</p>";
      }
    }

    // Eliminar Pedido
    document.getElementById("eliminarPedidoForm").addEventListener("submit", async function (e) {
      e.preventDefault();
      const idPedido = document.getElementById("pedidoIdEliminar").value.trim();
      if (idPedido === "") {
        displayMessage("Ingrese el ID del pedido a eliminar.", true);
        return;
      }

      const { ok, result } = await sendRequest("DELETE", { idPedido });
      if (ok) {
        displayMessage(result.message || "Pedido eliminado correctamente.");
        loadPedidos();
      } else {
        displayMessage(result.error || result.message || "Error al eliminar el pedido.", true);
      }
    });

    // Al cargar la página, se puede intentar cargar los pedidos automáticamente
    loadPedidos();