<template>
  <div class="login-container">
    <h2>Iniciar Sesión</h2>
    <form @submit.prevent="handleLogin">
      <label for="nombreUsuario">Usuario:</label>
      <input v-model="nombreUsuario" type="text" id="nombreUsuario" required>
      
      <label for="claveUsuario">Contraseña:</label>
      <input v-model="claveUsuario" type="password" id="claveUsuario" required>
      
      <button type="submit">Ingresar</button>
    </form>
    <p v-if="errorMessage" class="error">{{ errorMessage }}</p>
  </div>
</template>

<script>
export default {
  name: 'inicioLogin',
  data() {
    return {
      nombreUsuario: '',
      claveUsuario: '',
      errorMessage: ''
    }
  },
  methods: {
    async handleLogin() {
      // Preparamos los datos que enviaremos a la API
      const datos = {
        nombreUsuario: this.nombreUsuario,
        claveUsuario: this.claveUsuario
      };

      try {
        // Hacemos la petición POST a tu API real
        const response = await fetch("http://localhost/apiVentas/api/public/login", {
          method: "POST",
          headers: {
            "Content-Type": "application/json"
          },
          body: JSON.stringify(datos)
        });

        const result = await response.json();

        if (response.ok) {
          // Suponiendo que el API retorna 'token' y 'rolUsuario'
          localStorage.setItem("token", result.token);
          localStorage.setItem("rolUsuario", result.rolUsuario);
          // Redirigimos al usuario tras un login exitoso
          this.$router.push('/productos');
        } else {
          // Mostramos el error que retorna la API o un mensaje por defecto
          this.errorMessage = result.error || "Error en el login";
        }
      } catch (error) {
        console.error("Error al conectar con el servidor:", error);
        this.errorMessage = "Error al conectar con el servidor";
      }
    }
  }
}
</script>

<style scoped>
body {
  font-family: Arial, sans-serif;
  display: flex;
  justify-content: center;
  align-items: center;
  height: 100vh;
  background: #f4f4f4;
}

.login-container {
  background: white;
  padding: 20px;
  border-radius: 5px;
  box-shadow: 0px 0px 10px rgba(0,0,0,0.1);
}

h2 {
  text-align: center;
}

input {
  width: 100%;
  padding: 10px;
  margin-top: 5px;
}

button {
  width: 100%;
  padding: 10px;
  background: #007BFF;
  color: white;
  border: none;
  cursor: pointer;
}

.error {
  color: red;
  text-align: center;
}
</style>
