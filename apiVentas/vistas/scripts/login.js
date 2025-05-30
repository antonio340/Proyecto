document.getElementById("loginForm").addEventListener("submit", async function(event) {
    event.preventDefault(); // Evitar recarga de página

    const nombreUsuario = document.getElementById("nombreUsuario").value;
    const claveUsuario = document.getElementById("claveUsuario").value;
    
    // Datos a enviar en JSON
    const datos = {
        nombreUsuario: nombreUsuario,
        claveUsuario: claveUsuario
    };

    try {
        const response = await fetch("http://localhost/apiVentas/api/public/login", {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify(datos)
        });

        const result = await response.json();

        if (response.ok) {
            // Guardamos el token en localStorage
            localStorage.setItem("token", result.token);
            alert("Login exitoso!");
            window.location.href = "productos.html"; // Redirigir al usuario
        } else {
            document.getElementById("error-message").textContent = result.error;
        }
    } catch (error) {
        console.error("Error al conectar con la API:", error);
    }
});
