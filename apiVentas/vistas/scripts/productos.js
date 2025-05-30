 // Esperamos a que el DOM esté cargado
    document.addEventListener('DOMContentLoaded', function() {
      // Primero, verificamos que exista un token en el localStorage.
      // Si no existe, redirigimos al login.
      const token = localStorage.getItem("token");
      if (!token) {
        alert("No se encontró token. Por favor inicia sesión.");
        window.location.href = "index.html"; // Asegúrate de que index.html sea tu login
        return;
      }

      // Realizamos la petición GET a la API para obtener los productos
      fetch("http://localhost/apiVentas/api/public/productos", {
        method: "GET",
        headers: {
          "Content-Type": "application/json",
          // Aunque para lecturas públicas puede no ser esencial, incluimos el token.
          "Authorization": "Bearer " + token
        }
      })
      .then(response => {
        if (!response.ok) {
          throw new Error("Error en la consulta de productos.");
        }
        return response.json();
      })
      .then(result => {
        const container = document.getElementById("productosContainer");
        let html = "";
        // Verificamos si se encontraron productos
        if (result.productos && result.productos.length > 0) {
          html += `<table>
                    <thead>
                      <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Precio</th>
                      </tr>
                    </thead>
                    <tbody>`;
          result.productos.forEach(prod => {
            html += `<tr>
                      <td>${prod.idProducto}</td>
                      <td>${prod.nombreProducto}</td>
                      <td>${prod.precioProducto}</td>
                    </tr>`;
          });
          html += `</tbody>
                   </table>`;
        } else {
          html = "<p>No se encontraron productos.</p>";
        }
        container.innerHTML = html;
      })
      .catch(error => {
        console.error("Error:", error);
        document.getElementById("message").textContent = "Error al cargar productos.";
      });
    });