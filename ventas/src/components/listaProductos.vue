<template>
  <div>
    <h1>Lista de Productos</h1>
    <!-- Mostramos la tabla cuando hay productos -->
    <table v-if="productos.length">
      <thead>
        <tr>
          <th>ID</th>
          <th>Nombre</th>
          <th>Precio</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="producto in productos" :key="producto.idProducto">
          <td>{{ producto.idProducto }}</td>
          <td>{{ producto.nombreProducto }}</td>
          <td>{{ producto.precioProducto }}</td>
        </tr>
      </tbody>
    </table>
    <!-- Si hay un error, se muestra el mensaje -->
    <p v-if="errorMessage" class="error">{{ errorMessage }}</p>
    <!-- Mientras se cargan los productos, muestra un mensaje de carga -->
    <p v-else-if="!productos.length">Cargando productos...</p>
  </div>
</template>

<script>
export default {
  name: 'ListaProductos',
  data() {
    return {
      productos: [],       // Se almacenarán los productos obtenidos de la API
      errorMessage: ''     // Se asignará un mensaje de error en caso de fallo
    }
  },
  async mounted() {
    // Verificamos la existencia del token
    const token = localStorage.getItem("token");
    if (!token) {
      alert("No se encontró token. Por favor inicia sesión.");
      this.$router.push('/'); // Redirige al login (ajusta la ruta si es necesario)
      return;
    }
    
    try {
      // Realizamos la petición GET a la API real con el token en el header
      const response = await fetch("http://localhost/apiVentas/api/public/productos", {
        method: "GET",
        headers: {
          "Content-Type": "application/json",
          "Authorization": "Bearer " + token
        }
      });

      if (!response.ok) {
        throw new Error("Error en la consulta de productos.");
      }
      
      const result = await response.json();
      
      // Se asume que la API retorna un objeto con una propiedad 'productos'
      if (result.productos && Array.isArray(result.productos) && result.productos.length > 0) {
        this.productos = result.productos;
      } else {
        this.errorMessage = "No se encontraron productos.";
      }
    } catch (error) {
      console.error("Error:", error);
      this.errorMessage = "Error al cargar productos.";
    }
  }
}
</script>

<style scoped>
body {
  font-family: Arial, sans-serif;
  margin: 20px;
  background-color: #f4f4f4;
}
h1 {
  text-align: center;
}
table {
  width: 100%;
  border-collapse: collapse;
  background: #fff;
}
table, th, td {
  border: 1px solid #ddd;
}
th, td {
  padding: 10px;
  text-align: center;
}
th {
  background-color: #007BFF;
  color: #fff;
}
.error {
  color: red;
  text-align: center;
  margin-top: 15px;
}
</style>
