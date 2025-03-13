<?php
// Este archivo contiene la lógica AJAX para el plugin.

// Función AJAX para cargar las preguntas hijas
function fqr_cargar_hijas_callback() {
    global $wpdb;
    $prefijo = $wpdb->prefix . 'fqr_';
    $tabla_faq = $prefijo . 'faq';
    
    $id_padre = intval($_POST['id_padre']);
    $hijas = $wpdb->get_results($wpdb->prepare("SELECT id, pregunta, respuesta FROM $tabla_faq WHERE FK_idpadre = %d AND borrado = 0", $id_padre));
    
    $respuesta = '';
    
    if (!empty($hijas)) {
        foreach ($hijas as $hija) {
            $respuesta .= '<li class="faq-item" data-padre="' . esc_attr($id_padre) . '">';
            $respuesta .= '<strong class="faq-question" style="cursor:pointer;" data-id="' . esc_attr($hija->id) . '">' . esc_html($hija->pregunta) . '</strong><br>';
            $respuesta .= '<div class="faq-answer" style="display:none">' . esc_html($hija->respuesta) . '<br></div>';
            $respuesta .= '</li>';
        }
    } else {
        // Insertar formulario directamente sin verificar BD
        $plugin_url = plugin_dir_url(__FILE__) .'';
        $respuesta .= <<<EOD
        <div class="formulario-base" data-padre-form="{$id_padre}">
            <form method="post">
                <input type="hidden" name="id_pregunta" value="{$id_padre}">
                <label>Nombre: <input type="text" name="nombre" required></label>
                <label>Email: <input type="email" name="email" required></label>
                <button type="submit" name="enviar_formulario">Enviar</button>
                <label for="captcha">Introduce el texto de la imagen:</label>
                <img src="{$plugin_url}captcha.php" alt="CAPTCHA" id="captcha-img">
                <input type="text" name="captcha" required>
                <button type="button" onclick="document.getElementById('captcha-img').src='{$plugin_url}captcha.php?' + Math.random();">
                    Recargar CAPTCHA 🔄
                </button>
            </form>
        </div>
EOD;
    }
    
    echo $respuesta;
    wp_die();
}

add_action('wp_ajax_actualizar_prioridad_faq', function() {
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
?>