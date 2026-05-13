const express = require('express');
const cors = require('cors');

const app = express();
const PORT = process.env.PORT || 3000;

app.use(cors()); 
app.use(express.json()); 

// Ruta GET: Para comprobar que funciona la raíz
app.get('/', (req, res) => {
    res.json({ 
        estatus: 'Éxito',
        mensaje: '¡El backend de SOP&COM está en línea y operativo!' 
    });
});

// --- NUEVA RUTA POST: Para recibir los datos del contacto ---
app.post('/api/contacto', (req, res) => {
    // Extraemos los datos que nos enviará el frontend
    const { nombre, empresa, correo, mensaje } = req.body;

    // Imprimimos en la consola del servidor para verificar que llegaron
    console.log("🔔 ¡Nuevo prospecto recibido en SOP&COM!");
    console.log(`Nombre: ${nombre}`);
    console.log(`Empresa: ${empresa}`);
    console.log(`Correo: ${correo}`);
    console.log(`Mensaje: ${mensaje}`);
    console.log("-----------------------------------------");

    // Le respondemos a la página web (o a Postman) que todo salió bien
    res.json({ 
        estatus: 'Éxito', 
        mensaje: 'Tu mensaje ha sido recibido. Un consultor se pondrá en contacto pronto.' 
    });
});

app.listen(PORT, () => {
    console.log(`🚀 Servidor de SOP&COM corriendo exitosamente en http://localhost:${PORT}`);
});