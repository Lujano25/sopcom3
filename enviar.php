<?php
// 1. Configuración Inicial
header('Content-Type: application/json'); // Le dice al navegador que responderemos datos, no web
error_reporting(0); // Ocultar errores técnicos en pantalla para no romper el JSON

// -----------------------------------------------------------------------------
// CONFIGURACIÓN DE CLAVES
// -----------------------------------------------------------------------------
// Tu clave SECRETA de Google (la que empieza con 6L... y es secreta)
$recaptcha_secret = '6Lc-m2AsAAAAADnrzBy7Dg9G4NkYGUAADPSHMPLu'; 

// Tu correo donde recibirás los leads
$mi_correo_sopcom = "informes@sopcom.com.mx, josue.ibarra@sopcom.com.mx, oscar.lujano@sopcom.com.mx"; 
// -----------------------------------------------------------------------------


// 2. Verificar si se envió el formulario|
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Validar que el usuario marcó el Captcha
    if (isset($_POST['g-recaptcha-response']) && !empty($_POST['g-recaptcha-response'])) {

        // 3. Verificar Captcha con Google usando cURL (Compatible con tu servidor)
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://www.google.com/recaptcha/api/siteverify");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(array(
            'secret' => $recaptcha_secret, 
            'response' => $_POST['g-recaptcha-response']
        )));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $verifyResponse = curl_exec($ch);
        curl_close($ch);

        $responseData = json_decode($verifyResponse);

        if ($responseData->success) {
            // ✅ CAPTCHA VÁLIDO: PROCESAMOS EL CORREO
            
            // Recibir datos del formulario (limpiando espacios)
            $nombre = trim($_POST['nombre']); 
            $email_cliente = trim($_POST['email']);
            $mensaje = trim($_POST['mensaje']);

            // --- CORREO 1: NOTIFICACIÓN PARA TI (SOPCOM) ---
            $asunto_interno = "Nuevo Lead Web - SOPCOM: $nombre";
            
            $mensaje_interno = "Has recibido un nuevo contacto desde la web:\n\n";
            $mensaje_interno .= "Nombre: $nombre\n";
            $mensaje_interno .= "Correo: $email_cliente\n";
            $mensaje_interno .= "Mensaje:\n$mensaje\n";
            
            $headers_interno = "From: no-reply@sopcom.com.mx\r\n";
            $headers_interno .= "Reply-To: $email_cliente\r\n";
            
            // Enviar correo a ti
            mail($mi_correo_sopcom, $asunto_interno, $mensaje_interno, $headers_interno);

            // --- CORREO 2: AUTORESPUESTA PARA EL CLIENTE ---
            $asunto_cliente = "Recibimos tu mensaje - SOPCOM Soluciones";
            
            $mensaje_cliente = "Hola $nombre,\n\n";
            $mensaje_cliente .= "Gracias por contactar a SOPCOM Soluciones Tecnológicas.\n\n";
            $mensaje_cliente .= "Hemos recibido tu solicitud correctamente. Nuestro equipo ya está revisando tu mensaje y nos pondremos en contacto contigo a la brevedad.\n\n";
            $mensaje_cliente .= "------------------------------------------------------\n";
            $mensaje_cliente .= "Tu mensaje original:\n$mensaje\n";
            $mensaje_cliente .= "------------------------------------------------------\n\n";
            $mensaje_cliente .= "Atentamente,\nEl Equipo SOPCOM\nwww.sopcom.com.mx";

            $headers_cliente = "From: no-reply@sopcom.com.mx\r\n";
            $headers_cliente .= "X-Mailer: PHP/" . phpversion();

            // Enviar confirmación al cliente
            mail($email_cliente, $asunto_cliente, $mensaje_cliente, $headers_cliente);

            // --- RESPUESTA FINAL AL NAVEGADOR (ÉXITO) ---
            echo json_encode([
                'status' => 'success', 
                'message' => '¡Mensaje enviado con éxito! Te hemos enviado una confirmación a tu correo.'
            ]);

        } else {
            // ❌ ERROR: Google dice que es un robot
            echo json_encode([
                'status' => 'error', 
                'message' => 'Error de seguridad: La verificación del robot falló.'
            ]);
        }

    } else {
        // ❌ ERROR: No marcó la casilla
        echo json_encode([
            'status' => 'error', 
            'message' => 'Por favor, marca la casilla "No soy un robot".'
        ]);
    }

} else {
    // ❌ ERROR: Intentaron entrar directo al archivo sin enviar formulario
    echo json_encode([
        'status' => 'error', 
        'message' => 'Acceso no permitido.'
    ]);
}
?>