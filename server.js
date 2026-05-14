require('dotenv').config();
const express = require('express');
const cors = require('cors');


const app = express();
const PORT = process.env.PORT || 3000;

app.use(cors()); 
app.use(express.json()); 
const nodemailer = require('nodemailer');

// Ruta GET: Para comprobar que funciona la raíz
app.get('/', (req, res) => {
    res.json({ 
        estatus: 'Éxito',
        mensaje: '¡El backend de SOP&COM está en línea y operativo!' 
    });
});

// --- NUEVA RUTA POST: Para recibir los datos del contacto ---
// --- NUEVA RUTA POST: Para recibir los datos del contacto ---
app.post('/api/contacto', (req, res) => {
    // Extraemos los datos que nos enviará el frontend
    const { nombre, empresa, correo, mensaje } = req.body;

    console.log("🔔 ¡Nuevo prospecto recibido en SOP&COM!");
    console.log(`Nombre: ${nombre}`);
    console.log(`Empresa: ${empresa}`);
    
   // 1. Configuramos el "cartero" robot con tu servidor de SOPCOM
    const transporter = nodemailer.createTransport({
        host: 'mail.sopcom.com.mx', 
        port: 2525,             // Cambiamos el puerto al 2525
        secure: false,         // Debe ser false cuando usamos el puerto 2525
        auth: {
            user: 'oscar.lujano@sopcom.com.mx', 
            pass: process.env.EMAIL_PASS
        },
        tls: {
            rejectUnauthorized: false // Evita bloqueos por certificados SSL del hosting
        }
    });

    // 2. Armamos el diseño del correo que te va a llegar
    const mailOptions = {
        from: 'oscar.lujano@sopcom.com.mx', // Quién lo envía (tu robot)
        to: 'oscar.lujano@sopcom.com.mx',   // A quién le llega (tú mismo para la alerta)
        subject: `🚨 Nuevo prospecto SOP&COM: ${empresa}`,
        text: `¡Hola Óscar!\n\nTienes un nuevo prospecto interesado en SOP&COM.\n\nDatos del cliente:\n- Nombre: ${nombre}\n- Empresa: ${empresa}\n- Correo: ${correo}\n\nMensaje:\n${mensaje}`
    };

    // 3. Disparamos el correo
    transporter.sendMail(mailOptions, (error, info) => {
        if (error) {
            console.log("❌ Error al enviar la alerta por correo: ", error);
            // Le decimos a Postman que hubo un error
            return res.status(500).json({ estatus: 'Error', mensaje: 'Fallo al enviar correo' });
        } 
        
        console.log("✅ Alerta por correo enviada exitosamente!");
        // Si todo sale bien, le respondemos el "200 OK" a la página web
        res.json({
            estatus: 'Éxito',
            mensaje: 'Tu mensaje ha sido recibido. Un consultor se pondrá en contacto pronto.'
        });
    });
});

app.listen(PORT, () => {
    console.log(`🚀 Servidor de SOP&COM corriendo exitosamente en http://localhost:${PORT}`);
});



// 1. Configuramos el "cartero" robot con tu servidor de SOPCOM
    const transporter = nodemailer.createTransport({
        host: 'mail.sopcom.com.mx', // Normalmente es mail.tudominio.com
        port: 465,                  // Puerto seguro estándar para SMTP
        secure: true,               // true para el puerto 465
        auth: {
            user: 'oscar.lujano@sopcom.com.mx', // Tu correo electrónico de SOPCOM
            pass: '*sopcom2025*'              // Tu contraseña de correo electrónico
        }
    });