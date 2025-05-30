 // Comprobamos que hay un token en localStorage
    const token = localStorage.getItem("token");
    if (!token) {
      alert("No existe token. Por favor, inicia sesión.");
      window.location.href = "index.html"; // Redirige al login si no hay token
    }

    const API_URL = "http://localhost/apiVentas/api/public/productos";

    // Función para mostrar mensajes en pantalla
    function showMessage(msg, isError = false) {
      const messageEl = document.getElementById("message");
      messageEl.textContent = msg;
      messageEl.style.color = isError ? "red" : "green";
    }

    // Función para realizar peticiones a la API
    async function sendRequest(method, data = {}) {
      const options = {
        method: method,
        headers: {
          "Content-Type": "application/json",
          "Authorization": "Bearer " + token
        },
        body: JSON.stringify(data)
      };

      // En el caso de método GET, no se envía body.
      if (method === "GET") {
        delete options.body;
      }
      try {
        const response = await fetch(API_URL, options);
        const result = await response.json();
        return { ok: response.ok, result };
      } catch (error) {
        console.error(error);
        return { ok: false, result: { message: "Error en la conexión con el servidor" } };
      }
    }

    // Evento para crear producto
    document.getElementById("btnCrear").addEventListener("click", async (e) => {
      e.preventDefault();
      const nombre = document.getElementById("nombreProducto").value.trim();
      const precio = document.getElementById("precioProducto").value.trim();

      if (nombre === "" || precio === "") {
        showMessage("Por favor complete todos los campos para crear producto.", true);
        return;
      }

      const { ok, result } = await sendRequest("POST", {
        nombreProducto: nombre,
        precioProducto: precio
      });

      if (ok) {
        showMessage(result.message || "Producto creado correctamente.");
      } else {
        showMessage(result.error || result.message || "Error al crear producto.", true);
      }
    });

    // Evento para actualizar producto
    document.getElementById("btnActualizar").addEventListener("click", async (e) => {
      e.preventDefault();
      const idProducto = document.getElementById("idProducto").value.trim();
      const nombre = document.getElementById("nombreProducto").value.trim();
      const precio = document.getElementById("precioProducto").value.trim();

      if (idProducto === "" || nombre === "" || precio === "") {
        showMessage("Debe ingresar ID, nombre y precio para actualizar.", true);
        return;
      }

      const { ok, result } = await sendRequest("PUT", {
        idProducto: idProducto,
        nombreProducto: nombre,
        precioProducto: precio
      });

      if (ok) {
        showMessage(result.message || "Producto actualizado correctamente.");
      } else {
        showMessage(result.error || result.message || "Error al actualizar el producto.", true);
      }
    });

    // Evento para eliminar producto
    document.getElementById("btnEliminar").addEventListener("click", async (e) => {
      e.preventDefault();
      const idProducto = document.getElementById("idProductoEliminar").value.trim();
      if (idProducto === "") {
        showMessage("Debes ingresar el ID del producto a eliminar.", true);
        return;
      }
      
      const { ok, result } = await sendRequest("DELETE", {
        idProducto: idProducto
      });
      
      if (ok) {
        showMessage(result.message || "Producto eliminado correctamente.");
      } else {
        showMessage(result.error || result.message || "Error al eliminar el producto.", true);
      }
    });