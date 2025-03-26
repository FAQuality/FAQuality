<?php
// Este archivo contiene la lógica AJAX para el plugin.

// Función AJAX para cargar las preguntas hijas
function fqr_cargar_hijas_callback()
{
    global $wpdb;
    $prefijo = $wpdb->prefix . 'fqr_';
    $tabla_faq = $prefijo . 'faq';

    $id_padre = intval($_POST['id_padre']);
    $hijas = $wpdb->get_results($wpdb->prepare("SELECT id, pregunta, respuesta FROM $tabla_faq WHERE FK_idpadre = %d AND borrado = 0 ORDER BY prioridad desc", $id_padre));

    $respuesta = '';


    if (!empty($hijas)) {
        foreach ($hijas as $hija) {
            $respuesta_html = apply_filters('the_content', $hija->respuesta);
            $respuesta .= '<li class="faq-item" data-padre="' . esc_attr($id_padre) . '">';
            $respuesta .= '<strong class="faq-question" style="cursor:pointer;" data-id="' . esc_attr($hija->id) . '">' . esc_html($hija->pregunta) . '</strong><br>';
            $respuesta .= '<div class="faq-answer" style="display:none">' . $respuesta_html . '</div>';
            $respuesta .= '</li>';
        }
    } else {
        // Insertar formulario directamente sin verificar BD
        $nonce = wp_create_nonce('fqr_form_nonce');
        $plugin_url = plugin_dir_url(__FILE__) . '';
        $respuesta .= <<<EOD
        <div class="formulario-base show" data-padre-form="{$id_padre}">
            <form method="post" class="fqr-form" action="#">
                <input type="hidden" name="action" value="fqr_submit_form">
                <input type="hidden" name="id_pregunta" value="{$id_padre}">
                <input type="hidden" name="fqr_nonce" value="{$nonce}">
                <div class="row">
                    <label>Nombre: <input type="text" name="nombre" class="faq-input" required></label>
                    <label>Email: <input type="email" name="email" class="faq-input" required></label>
                </div>
                <label>Mensaje: <textarea type="text" name="mensaje" class="faq-input mensaje"></textarea></label>
                <label for="captcha">Introduce el texto de la imagen:</label>
                <div class="captcha">
                    <img src="{$plugin_url}captcha.php" alt="CAPTCHA" id="captcha-img">
                    <button class="captcha-button" type="button" onclick="document.getElementById('captcha-img').src='{$plugin_url}captcha.php?' + Math.random();">Recargar captcha</button>
                </div>
                <input type="text" name="captcha" class="faq-input" required>
                <button type="submit" name="enviar_formulario">Enviar</button>
            </form>
        </div>
    EOD;
    }

    echo $respuesta;
    wp_die();
}

add_action('wp_ajax_actualizar_prioridad_faq', function () {
    check_ajax_referer('faq_priority_nonce', 'security');

    global $wpdb;
    $table = $wpdb->prefix . 'fqr_faq';

    $wpdb->update(
        $table,
        ['prioridad' => $_POST['prioridad']],
        ['id' => $_POST['id']],
        ['%d'],
        ['%d']
    );

    wp_die();
});

add_action('wp_ajax_fqr_cargar_hijas', 'fqr_cargar_hijas_callback');
add_action('wp_ajax_nopriv_fqr_cargar_hijas', 'fqr_cargar_hijas_callback');


function fqr_submit_form_callback()
{
    check_ajax_referer('fqr_form_nonce', 'fqr_nonce'); // Verificar nonce para seguridad

    session_start();
    global $wpdb;
    $prefijo = $wpdb->prefix . 'fqr_';
    $tabla_contacto = $prefijo . 'contacto';

    $response = ['success' => false, 'message' => ''];

    // Validar CAPTCHA
    if (!isset($_SESSION['captcha']) || $_POST['captcha'] != $_SESSION['captcha']) {
        $response['message'] = "Captcha incorrecto ❌, intenta de nuevo.";
        wp_send_json($response);
    }

    // Validar campos requeridos
    if (empty($_POST['nombre']) || empty($_POST['email']) || empty($_POST['mensaje'])) {
        $response['message'] = "Todos los campos son obligatorios.";
        wp_send_json($response);
    }

    // Insertar datos en la base de datos
    $nombre = sanitize_text_field($_POST["nombre"]);
    $email = sanitize_email($_POST["email"]);
    $mensaje = sanitize_textarea_field($_POST["mensaje"]);
    $id_pregunta = intval($_POST["id_pregunta"]);

    $result = $wpdb->insert($tabla_contacto, [
        "nombre" => $nombre,
        "email" => $email,
        "mensaje" => $mensaje,
        "FK_idfaq" => $id_pregunta
    ]);

    if ($result) {
        // Respuesta exitosa
        $response['success'] = true;
        $response['message'] = "Gracias, <strong>" . esc_html($nombre) . "</strong>. Hemos recibido tu mensaje ✅";

        $asunto = 'Muchas gracias por enviar su mensaje ' . $nombre;
        $cuerpo = 'Hemos recibido exitosamente su mensaje: <br>' . $mensaje . '<br><br>Gracias por su interés. Le responderemos lo antes posible';

        enviar_correo_personalizado($email, ['nombre' => $nombre, 'mensaje' => $mensaje ]);
    } else {
        // Error al insertar en la base de datos
        $response['message'] = "Ocurrió un error al enviar tu mensaje. Intenta nuevamente.";
    }

    wp_send_json($response);
}

function enviar_correo_personalizado($email, $datos_adicionales = [])
{
    // Obtener la configuración
    $config = fqr_get_config();

    // Obtener el correo del administrador de WordPress
    $correo_remitente = get_option('admin_email');

    // Usar el asunto de la configuración o un valor por defecto
    $asunto = isset($config['email_asunto']) ? $config['email_asunto'] : 'Gracias por contactar con nosotros';

    // Usar el mensaje de la configuración o un valor por defecto
    $mensaje = isset($config['email_cuerpo']) ? htmlspecialchars_decode($config['email_cuerpo']) : 'Le responderemos en breve';

    // Reemplazar placeholders en el mensaje
    foreach ($datos_adicionales as $key => $value) {
        $mensaje = str_replace("{{$key}}", $value, $mensaje);
    }

    // Encabezados del correo (asegurándonos de que sea contenido HTML)
    $headers = array(
        'Content-Type: text/html; charset=UTF-8', // Permite contenido HTML
        'From: ' . get_bloginfo('name') . ' <' . $correo_remitente . '>',
    );

    // Enviar el correo
    $enviado = wp_mail($email, $asunto, $mensaje, $headers);

    // Verificar si el correo se envió correctamente
    return $enviado ? "Correo enviado con éxito" : "Error al enviar el correo";
}


add_action('wp_ajax_fqr_submit_form', 'fqr_submit_form_callback');
add_action('wp_ajax_nopriv_fqr_submit_form', 'fqr_submit_form_callback');
