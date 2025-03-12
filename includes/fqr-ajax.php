<?php
// Este archivo contiene la lógica AJAX para el plugin.

// Función AJAX para cargar las preguntas hijas
function fqr_cargar_hijas_callback() {
    global $wpdb;
    $prefijo = $wpdb->prefix . 'fqr_';
    $tabla_faq = $prefijo . 'faq';

    $id_padre = intval($_POST['id_padre']);

    $hijas = $wpdb->get_results($wpdb->prepare("SELECT id, pregunta, respuesta FROM $tabla_faq WHERE FK_idpadre = %d AND borrado = 0", $id_padre));

    $formulario_mostrado = false;
    if (!empty($hijas)) {
        $respuesta = '';
        foreach ($hijas as $hija) {
            $respuesta .= '<li class="faq-item" data-padre="' . esc_attr($id_padre) . '">';
            $respuesta .= '<strong class="faq-question" data-id="' . esc_attr($hija->id) . '">' . esc_html($hija->pregunta) . '</strong><br>';
            $respuesta .= '<div class="faq-answer" style="display:none">' . esc_html($hija->respuesta) . '<br></div>';
            $respuesta .= '</li>';
        }
        echo $respuesta;
    } else {
        if (!$formulario_mostrado) {
            echo formulario_base();
            $formulario_mostrado = true;
        }
    }

    wp_die();
}
add_action('wp_ajax_fqr_cargar_hijas', 'fqr_cargar_hijas_callback');
add_action('wp_ajax_nopriv_fqr_cargar_hijas', 'fqr_cargar_hijas_callback');
?>