<template>
  <div class="admin-productos">
    <h1>Administrar Productos</h1>

    <!-- Formulario para Crear/Actualizar -->
    <div class="form-container">
      <h2>Crear / Actualizar Producto</h2>
      <!-- El campo ID es opcional para actualizacion-->
      <input v-model="producto.idProducto" type="number" placeholder="ID (para actualizar)">
      <input v-model="producto.nombreProducto" type="text" placeholder="Nombre" required>
      <input v-model="producto.precioProducto" type="number" step="0.01" placeholder="Precio" required>
      <button @click="crearProducto">Crear</button>
      <button @click="actualizarProducto">Actualizar</button>
    </div>

    <!-- Formulario para Eliminar -->
    <div class="form-container">
      <h2>Eliminar Producto</h2>
      <input v-model="idEliminar" type="number" placeholder="ID del producto">
      <button class="delete-btn" @click="eliminarProducto">Eliminar</button>
    </div>

    <!-- Mensaje de estado -->
    <p :class="{ 'error': mensajeError, 'success': !mensajeError }">{{ mensaje }}</p>

    <!-- Lista de productos -->
    <div class="productos-list">
      <h2>Productos Existentes</h2>
      <button @click="cargarProductos">Refrescar Lista</button>
      <table v-if="productos.length">
        <thead>
          <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Precio</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="prod in productos" :key="prod.idProducto">
            <td>{{ prod.idProducto }}</td>
            <td>{{ prod.nombreProducto }}</td>
            <td>{{ prod.precioProducto }}</td>
          </tr>
        </tbody>
      </table>
      <p v-else>No hay productos registrados</p>
    </div>
  </div>
</template>

<script>
export default {
  name: 'AdminProductos',
  data() {
    return {
      productos: [],
      producto: {
        idProducto: '',
        nombreProducto: '',
        precioProducto: ''
      },
      idEliminar: '',
      mensaje: '',
      mensajeError: false
    }
  },
  methods: {
    async cargarProductos() {
      try {
        // No se requiere token para obtener la lista de productos (a menos que tu API lo pida)
        const response = await fetch('http://localhost/apiVentas/api/public/productos', {
          method: 'GET',
          headers: { 'Content-Type': 'application/json' }
        });
        const data = await response.json();
        // Suponemos que el controlador retorna un objeto { "productos": [ ... ] }
        if (data.productos) {
          this.productos = data.productos;
          this.mostrarMensaje('Productos cargados', false);
        } else {
          this.productos = [];
          this.mostrarMensaje('No se encontraron productos', true);
        }
      } catch (error) {
        console.error("Error al cargar productos:", error);
        this.mostrarMensaje('Error al cargar productos', true);
      }
    },
    async crearProducto() {
      if (!this.producto.nombreProducto || !this.producto.precioProducto) {
        this.mostrarMensaje('Nombre y precio son requeridos', true);
        return;
      }
      try {
        const token = localStorage.getItem('token');
        const response = await fetch('http://localhost/apiVentas/api/public/productos', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Authorization': `Bearer ${token}`
          },
          body: JSON.stringify({
            nombreProducto: this.producto.nombreProducto,
            precioProducto: parseFloat(this.producto.precioProducto)
          })
        });
        if (response.ok) {
          this.mostrarMensaje('Producto creado correctamente', false);
          this.resetForm();
          this.cargarProductos();
        } else {
          const errorResp = await response.json();
          throw new Error(errorResp.message || 'Error al crear el producto');
        }
      } catch (error) {
        console.error("Error al crear producto:", error);
        this.mostrarMensaje('Error al crear producto', true);
      }
    },
    async actualizarProducto() {
      // Verifica que se envíen los tres campos
      if (
        !this.producto.idProducto ||
        !this.producto.nombreProducto ||
        !this.producto.precioProducto
      ) {
        this.mostrarMensaje('ID, nombre y precio son requeridos', true);
        return;
      }
      try {
        const token = localStorage.getItem('token');
        const response = await fetch('http://localhost/apiVentas/api/public/productos', {
          method: 'PUT',
          headers: {
            'Content-Type': 'application/json',
            'Authorization': `Bearer ${token}`
          },
          body: JSON.stringify({
            idProducto: this.producto.idProducto,
            nombreProducto: this.producto.nombreProducto,
            precioProducto: parseFloat(this.producto.precioProducto)
          })
        });
        if (response.ok) {
          this.mostrarMensaje('Producto actualizado correctamente', false);
          this.resetForm();
          this.cargarProductos();
        } else {
          const errorResp = await response.json();
          throw new Error(errorResp.message || 'Error al actualizar el producto');
        }
      } catch (error) {
        console.error("Error al actualizar producto:", error);
        this.mostrarMensaje('Error al actualizar producto', true);
      }
    },
    async eliminarProducto() {
      if (!this.idEliminar) {
        this.mostrarMensaje('Ingresa un ID válido', true);
        return;
      }
      try {
        const token = localStorage.getItem('token');
        const response = await fetch('http://localhost/apiVentas/api/public/productos', {
          method: 'DELETE',
          headers: {
            'Content-Type': 'application/json',
            'Authorization': `Bearer ${token}`
          },
          body: JSON.stringify({ idProducto: this.idEliminar })
        });
        if (response.ok) {
          this.mostrarMensaje('Producto eliminado correctamente', false);
          this.idEliminar = '';
          this.cargarProductos();
        } else {
          const errorResp = await response.json();
          throw new Error(errorResp.message || 'Error al eliminar el producto');
        }
      } catch (error) {
        console.error("Error al eliminar producto:", error);
        this.mostrarMensaje('Error al eliminar producto', true);
      }
    },
    resetForm() {
      this.producto = {
        idProducto: '',
        nombreProducto: '',
        precioProducto: ''
      };
    },
    mostrarMensaje(texto, esError) {
      this.mensaje = texto;
      this.mensajeError = esError;
      setTimeout(() => this.mensaje = '', 3000);
    }
  },
  mounted() {
    this.cargarProductos();
  }
}
</script>

<style scoped>
/* Estilos adaptados de adminProductos.css */
body {
  font-family: Arial, sans-serif;
  margin: 20px;
  background: #f4f4f4;
}
h1 {
  text-align: center;
  margin-bottom: 20px;
}
.form-container {
  width: 400px;
  margin: 0 auto 20px auto;
  padding: 20px;
  background: #fff;
  border: 1px solid #ddd;
  border-radius: 5px;
}
.form-container input,
.form-container button {
  width: 100%;
  padding: 10px;
  margin-top: 10px;
  box-sizing: border-box;
}
.form-container button {
  cursor: pointer;
  background: #007BFF;
  border: none;
  color: #fff;
}
.form-container button.delete {
  background: #C82333;
}
.message {
  text-align: center;
  font-weight: bold;
  margin-top: 15px;
}
</style>
