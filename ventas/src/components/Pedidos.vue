<template>
  <div class="pedidos-container">
    <h1>Gestión de Pedidos</h1>

    <!-- Formulario para crear pedido -->
    <div class="form-container">
      <h2>Crear Pedido</h2>
      <form @submit.prevent="crearPedido">
        <input 
          v-model="nuevoPedido.idProducto" 
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
            <th>Nombre Producto</th>
            <th>Precio</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="pedido in pedidosConProductos" :key="pedido.idPedido">
            <td>{{ pedido.idPedido }}</td>
            <td>{{ pedido.idUsuario }}</td>
            <td>{{ pedido.idProducto }}</td>
            <td>{{ pedido.nombreProducto || 'N/A' }}</td>
            <td>${{ pedido.precioProducto || 'N/A' }}</td>
          </tr>
        </tbody>
      </table>
      <p v-else>No hay pedidos registrados</p>
    </div>

    <!-- Eliminar pedido -->
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
      productos: [], // Añadimos array para productos
      nuevoPedido: { idProducto: '' },
      idPedidoEliminar: '',
      mensaje: '',
      mensajeError: false
    }
  },
  computed: {
    pedidosConProductos() {
      return this.pedidos.map(pedido => {
        const producto = this.productos.find(p => p.idProducto == pedido.idProducto);
        return {
          ...pedido,
          nombreProducto: producto?.nombreProducto,
          precioProducto: producto?.precioProducto
        };
      });
    }
  },
  methods: {
    async cargarProductos() {
      try {
        const response = await fetch('http://localhost:3000/productos');
        this.productos = await response.json();
      } catch (error) {
        console.error('Error al cargar productos:', error);
      }
    },
    async cargarPedidos() {
      try {
        const token = localStorage.getItem('token');
        const response = await fetch('http://localhost:3000/pedidos', {
          headers: {
            'Authorization': `Bearer ${token}`
          }
        });
        this.pedidos = await response.json();
        await this.cargarProductos(); // Cargar productos después de los pedidos
        this.mostrarMensaje('Pedidos cargados', false);
      } catch (error) {
        this.mostrarMensaje('Error al cargar pedidos', true);
      }
    },
    async crearPedido() {
      try {
        const token = localStorage.getItem('token');
        const response = await fetch('http://localhost:3000/pedidos', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Authorization': `Bearer ${token}`
          },
          body: JSON.stringify({ idProducto: this.nuevoPedido.idProducto })
        });
        
        if (response.ok) {
          this.mostrarMensaje('Pedido creado', false);
          this.nuevoPedido.idProducto = '';
          this.cargarPedidos();
        } else {
          throw new Error('Error en la respuesta');
        }
      } catch (error) {
        this.mostrarMensaje('Error al crear pedido', true);
      }
    },
    async eliminarPedido() {
      if (!this.idPedidoEliminar) {
        this.mostrarMensaje('Ingresa un ID válido', true);
        return;
      }

      try {
        const response = await fetch(`http://localhost:3000/pedidos/${this.idPedidoEliminar}`, {
          method: 'DELETE'
        });

        if (!response.ok) {
          const error = await response.json();
          throw new Error(error.message || 'Error al eliminar');
        }

        this.mostrarMensaje('Pedido eliminado correctamente', false);
        this.idPedidoEliminar = '';
        this.cargarPedidos(); // Actualiza la lista
      } catch (error) {
        this.mostrarMensaje(error.message, true);
        console.error("Error completo:", error);
      }
    },
    mostrarMensaje(texto, esError) {
      this.mensaje = texto;
      this.mensajeError = esError;
      setTimeout(() => this.mensaje = '', 3000);
    }
  },
  mounted() {
    this.cargarPedidos(); // Carga automática al entrar
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
    .container {
      max-width: 600px;
      margin: 0 auto 20px auto;
      background: #fff;
      padding: 20px;
      border: 1px solid #ddd;
      border-radius: 5px;
    }
    input, button {
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
    button.delete {
      background: #c82333;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 10px;
    }
    table, th, td {
      border: 1px solid #ddd;
    }
    th, td {
      padding: 10px;
      text-align: center;
    }
    th {
      background: #007BFF;
      color: #fff;
    }
    #message {
      text-align: center;
      font-weight: bold;
      margin-top: 15px;
    }
</style>