<template>
  <div class="factura-container">
    <h1>Tu Factura</h1>
    <button @click="generarFactura">Generar Factura</button>
    
    <div v-if="factura" class="factura-info">
      <table>
        <thead>
          <tr>
            <th>ID Factura</th>
            <th>Subtotal</th>
            <th>IVA</th>
            <th>Total</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>{{ factura.idFactura }}</td>
            <td>{{ factura.subtotalFactura }}</td>
            <td>{{ factura.ivaFactura }}</td>
            <td>{{ factura.totalFactura }}</td>
          </tr>
        </tbody>
      </table>
    </div>
    <p v-else class="message">{{ mensaje }}</p>
  </div>
</template>

<script>
export default {
  name: 'Factura',
  data() {
    return {
      factura: null,
      mensaje: 'Presiona "Generar Factura" para ver tus datos.'
    }
  },
  methods: {
    async generarFactura() {
      try {
        const token = localStorage.getItem('token');
        const response = await fetch('http://localhost:3000/facturas?userId=1'); // Ajusta el userId
        const data = await response.json();
        
        if (data.length > 0) {
          this.factura = data[0]; // Toma la primera factura (ajusta según tu lógica)
          this.mensaje = '';
        } else {
          this.mensaje = 'No se encontró factura.';
        }
      } catch (error) {
        this.mensaje = 'Error al generar la factura';
        console.error(error);
      }
    }
  }
}
</script>

<style scoped>
        body {
      font-family: Arial, sans-serif;
      margin: 20px;
      background: #f4f4f4;
    }
    h1 {
      text-align: center;
    }
    .container {
      max-width: 600px;
      margin: 0 auto;
      background: #fff;
      padding: 20px;
      border: 1px solid #ddd;
      border-radius: 5px;
    }
    button {
      padding: 10px 20px;
      margin: 10px 0;
      background: #007BFF;
      color: #fff;
      border: none;
      cursor: pointer;
      border-radius: 3px;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 15px;
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