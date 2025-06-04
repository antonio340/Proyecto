<template>
  <header>
    <nav>
      <ul id="navList">
        <li><router-link to="/productos">Productos</router-link></li>
        <li><router-link to="/pedidos">Pedidos</router-link></li>
        <li><router-link to="/factura">Factura</router-link></li>
        <li v-if="rolUsuario !== 'Cliente'">
          <router-link to="/admin-productos">Admin Productos</router-link>
        </li>
        <li><a href="#" @click="logout">Salir</a></li>
      </ul>
    </nav>
  </header>
</template>

<script>
export default {
  name: 'HeaderComponent',
  data() {
    return {
      rolUsuario: localStorage.getItem('rolUsuario') || 'Cliente'
    }
  },
  methods: {
    logout() {
      localStorage.removeItem('token');
      localStorage.removeItem('rolUsuario');
      this.$router.push('/');
    }
  }
}
</script>

<style scoped>
/* Copia tus estilos CSS originales de header.js aquí */
nav ul {
  list-style-type: none;
  display: flex;
  gap: 15px;
  justify-content: center;
  padding: 10px;
  background: #007BFF;
  color: #fff;
}
nav a {
  color: #fff;
  text-decoration: none;
}
</style>