  // Función para decodificar el payload del token (JWT)
  function getRoleFromToken() {
    const token = localStorage.getItem("token");
    if (!token) return null;

    const parts = token.split(".");
    if (parts.length !== 3) return null;

    try {
      const payload = JSON.parse(atob(parts[1]));
      return payload.rolUsuario;
    } catch (e) {
      console.error("Error decodificando token: ", e);
      return null;
    }
  }

  // Función para cargar el header de navegación
  function loadHeader() {
    const role = getRoleFromToken();
    let navHtml = `
      <li><a href="productos.html" style="color: #fff; text-decoration: none;">Productos</a></li>
      <li><a href="pedidos.html" style="color: #fff; text-decoration: none;">Pedidos</a></li>
      <li><a href="factura.html" style="color: #fff; text-decoration: none;">Factura</a></li>  
    `;

    // Si el rol no es "cliente", mostramos la opción de "Administrar Productos"
    if (role && role.toLowerCase() !== "cliente") {
      navHtml += `<li><a href="adminProductos.html" style="color: #fff; text-decoration: none;">Administrar Productos</a></li>`;
    }

    // Siempre se muestra el enlace para salir
    navHtml += `<li><a href="#" id="logoutLink" style="color: #fff; text-decoration: none;">Salir</a></li>`;
  
    document.getElementById("navList").innerHTML = navHtml;

    // Evento para cerrar sesión: borra el token y redirige al login
    document.getElementById("logoutLink").addEventListener("click", function (e) {
      e.preventDefault();
      localStorage.removeItem("token");
      window.location.href = "index.html"; // Asume que index.html es la página de login
    });
  }

  // Ejecutar la función al cargar la página
  document.addEventListener("DOMContentLoaded", loadHeader);