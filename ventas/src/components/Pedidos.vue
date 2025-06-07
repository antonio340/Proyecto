<template>
  <div class="pedidos-container">
    <h1>Gestión de Pedidos</h1>

    <!-- Formulario para crear pedido -->
    <div class="form-container">
      <h2>Crear Pedido</h2>
      <form @submit.prevent="crearPedido">
        <input 
          v-model="idProducto" 
          type="number" 
          placeholder="ID del Producto" 
          required
        >
        <button type="submit">Crear Pedido</button>
      </form>
    </div>

    <!-- Lista de pedidos -->
    <div class="list-container">
      <h2>Listado de Pedidos</h2>
      <button @click="cargarPedidos">Cargar Pedidos</button>
      <table v-if="pedidos.length">
        <thead>
          <tr>
            <th>ID Pedido</th>
            <th>ID Usuario</th>
            <th>ID Producto</th>
            <th>Nombre</th>
            <th>Precio</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="pedido in pedidos" :key="pedido.idPedido"> <!-- Usamos idPedido como clave -->
            <td>{{ pedido.idPedido }}</td>
            <td>{{ pedido.idUsuario }}</td>
            <td>{{ pedido.idProducto }}</td>
            <td>{{ pedido.nombreProducto }}</td>
            <td>{{ pedido.precioProducto }}</td>
          </tr>
        </tbody>
      </table>
      <p v-else>No hay pedidos registrados</p>
    </div>

    <!-- Formulario para eliminar pedido -->
    <div class="form-container">
      <h2>Eliminar Pedido</h2>
      <form @submit.prevent="eliminarPedido">
        <input 
          v-model="idPedidoEliminar" 
          type="number" 
          placeholder="ID del Pedido" 
          required
        >
        <button type="submit" class="delete-btn">Eliminar Pedido</button>
      </form>
    </div>

    <!-- Mensajes de estado -->
    <p :class="{'error': mensajeError, 'success': !mensajeError}">
      {{ mensaje }}
    </p>
  </div>
</template>

<script>
export default {
  name: 'Pedidos',
  data() {
    return {
      pedidos: [],
      idProducto: '',
      idPedidoEliminar: '',
      mensaje: '',
      mensajeError: false
    }
  },
  methods: {
    async cargarPedidos() {
      try {
        const token = localStorage.getItem('token');
        // Cambiamos la URL para usar la API real
        const response = await fetch('http://localhost/apiVentas/api/public/pedidos', {
          headers: {
            'Content-Type': 'application/json',
            'Authorization': `Bearer ${token}`
          }
        });
        const result = await response.json();
        // Suponemos que la API retorna directamente un array o un objeto con la propiedad "pedidos"
        this.pedidos = Array.isArray(result) ? result : (result.pedidos || []);
        this.mostrarMensaje('Pedidos cargados', false);
      } catch (error) {
        console.error("Error al cargar pedidos:", error);
        this.mostrarMensaje('Error al cargar pedidos', true);
      }
    },
    async crearPedido() {
      try {
        const token = localStorage.getItem('token');
        const response = await fetch('http://localhost/apiVentas/api/public/pedidos', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Authorization': `Bearer ${token}`
          },
          body: JSON.stringify({ idProducto: this.idProducto })
        });
        
        if (response.ok) {
          this.mostrarMensaje('Pedido creado', false);
          this.idProducto = '';
          this.cargarPedidos();
        } else {
          const errorResp = await response.json();
          throw new Error(errorResp.message || 'Error al crear pedido');
        }
      } catch (error) {
        console.error("Error al crear pedido:", error);
        this.mostrarMensaje('Error al crear pedido', true);
      }
    },
    async eliminarPedido() {
    if (!this.idPedidoEliminar) {
      this.mostrarMensaje('Ingresa un ID válido', true);
      return;
    }
    try {
      const token = localStorage.getItem('token');
      const response = await fetch('http://localhost/apiVentas/api/public/pedidos', { // Asegúrate de usar la URL de tu API real
        method: 'DELETE',
        headers: {
          'Content-Type': 'application/json',
          'Authorization': `Bearer ${token}`
        },
        body: JSON.stringify({ idPedido: this.idPedidoEliminar })
      });
      
      if (!response.ok) {
        const error = await response.json();
        throw new Error(error.message || 'Error al eliminar el pedido');
      }
      
      this.mostrarMensaje('Pedido eliminado correctamente', false);
      this.idPedidoEliminar = '';
      this.cargarPedidos();
    } catch (error) {
      this.mostrarMensaje(error.message, true);
      console.error("Error completo:", error);
    }
  },
    mostrarMensaje(texto, esError) {
      this.mensaje = texto;
      this.mensajeError = esError;
      setTimeout(() => {
        this.mensaje = '';
      }, 3000);
    }
  },
  mounted() {
    this.cargarPedidos();
  }
}
</script>

<style scoped>
body {
  font-family: Arial, sans-serif;
  margin: 20px;
  background: #f4f4f4;
}
h1, h2 {
  text-align: center;
}
.container,
.form-container,
.list-container {
  max-width: 600px;
  margin: 0 auto 20px auto;
  background: #fff;
  padding: 20px;
  border: 1px solid #ddd;
  border-radius: 5px;
}
input,
button {
  width: 100%;
  padding: 10px;
  margin-top: 10px;
  box-sizing: border-box;
}
button {
  cursor: pointer;
  background: #007BFF;
  border: none;
  color: #fff;
}
button.delete-btn {
  background: #c82333;
}
table {
  width: 100%;
  border-collapse: collapse;
  margin-top: 10px;
}
table,
th,
td {
  border: 1px solid #ddd;
}
th,
td {
  padding: 10px;
  text-align: center;
}
th {
  background: #007BFF;
  color: #fff;
}
#error-message,
.error,
.success {
  text-align: center;
  font-weight: bold;
  margin-top: 15px;
}
.error {
  color: red;
}
.success {
  color: green;
}
</style>
