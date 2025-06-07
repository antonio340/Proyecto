<template>
  <header>
    <nav>
      <ul id="navList">
        <li><router-link to="/productos">Productos</router-link></li>
        <li><router-link to="/pedidos">Pedidos</router-link></li>
        <li><router-link to="/factura">Factura</router-link></li>
        <!-- Solo muestra el link de administración si el rol no es "cliente" -->
        <li v-if="rolUsuario.toLowerCase() !== 'cliente'">
          <router-link to="/admin-productos">Administrar Productos</router-link>
        </li>
        <li><a href="#" @click.prevent="logout">Salir</a></li>
      </ul>
    </nav>
  </header>
</template>

<script>
export default {
  name: 'HeaderComponent',
  computed: {
    rolUsuario() {
      // Intenta decodificar el token JWT para extraer el rol del usuario.
      // Si no existe, se asume que el rol es "Cliente".
      const token = localStorage.getItem("token");
      if (!token) return "Cliente";
      const parts = token.split(".");
      if (parts.length !== 3) return "Cliente";
      try {
        const payload = JSON.parse(atob(parts[1]));
        return payload.rolUsuario || "Cliente";
      } catch (error) {
        console.error("Error decodificando token: ", error);
        return "Cliente";
      }
    }
  },
  methods: {
    logout() {
      localStorage.removeItem("token");
      // Puedes limpiar también otros datos, si es necesario.
      this.$router.push("/");
    }
  }
}
</script>

<style scoped>
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
