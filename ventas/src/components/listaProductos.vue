<template>
  <div>
    <h1>Lista de Productos</h1>
    <!-- Aquí irá la tabla de productos -->
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
    <p v-else>Cargando productos...</p>
  </div>
</template>

<script>
export default {
  name: 'ListaProductos',
  data() {
    return {
      productos: [] // Aquí se almacenarán los productos desde JSON Server
    }
  },
  async mounted() {
    // Llamada a la API (ajusta la URL según tu JSON Server)
    const response = await fetch('http://localhost:3000/productos');
    this.productos = await response.json();
  }
}
</script>

<style scoped>
/* Copia los estilos de tu CSS original (productos.css) aquí */

    body {
      font-family: Arial, sans-serif;
      margin: 20px;
      background-color: #f4f4f4;
    }
    h1 {
      text-align: center;
    }
    #productosContainer {
      margin-top: 20px;
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
    .message {
      text-align: center;
      margin-top: 15px;
      font-weight: bold;
    }
</style>