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
      try {
        const response = await fetch("http://localhost:3000/usuarios");
        const usuarios = await response.json();
        
        const usuarioValido = usuarios.find(
          user => user.nombreUsuario === this.nombreUsuario && 
                 user.claveUsuario === this.claveUsuario
        );

        if (usuarioValido) {
          localStorage.setItem("token", "simulated-token"); // Mock JWT
          localStorage.setItem("rolUsuario", usuarioValido.rolUsuario);
          this.$router.push('/productos'); // Redirigir tras login
        } else {
          this.errorMessage = "Usuario o contraseña incorrectos";
        }
      } catch (error) {
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