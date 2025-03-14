<?php
function frontend_shortcode($atts) {
    ob_start();
    session_start();
    global $wpdb;
    $prefijo = $wpdb->prefix . 'fqr_';
    $tabla_faq = $prefijo . 'faq';

    // Procesamiento de atributos del shortcode
    $atts = shortcode_atts([
        'categorias' => '' // IDs de las categorías separados por comas
    ], $atts);

    if (empty($atts['categorias'])) {
        return '<p>No se especificaron categorías.</p>';
    }

    $categoria_ids = array_map('intval', explode(',', $atts['categorias']));

    if (empty($categoria_ids) || $categoria_ids[0] == 0) {
        return '<p>No se encontraron categorías seleccionadas.</p>';
    }

    $placeholders = implode(',', array_fill(0, count($categoria_ids), '%d'));
    $query = $wpdb->prepare(
        "SELECT id, pregunta, respuesta, FK_idpadre FROM $tabla_faq WHERE FK_idcat IN ($placeholders) AND FK_idpadre=1 AND borrado = 0 order by prioridad desc",
        ...$categoria_ids
    );

    $faq = $wpdb->get_results($query);

    ?>
    <div class="faq-container">
        <?php if (!empty($faq)): ?>
            <ul class="faq-list">
                <?php foreach ($faq as $fila): ?>
                    <li class="faq-item" data-padre="<?php echo esc_attr($fila->FK_idpadre); ?>" data-estado="cerrado">
                        <strong class="faq-question" style="cursor: pointer;" data-id="<?php echo esc_attr($fila->id); ?>">
                            <?php echo esc_html($fila->pregunta); ?>
                        </strong><br>
                        <div class="faq-answer" style="display:none">
                            <?php echo esc_html($fila->respuesta); ?><br>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>No hay preguntas en estas categorías.</p>
        <?php endif; ?>
    </div>
    <?php

    formulario_base();
    return ob_get_clean();
}
add_shortcode('FAQer', 'frontend_shortcode');

function formulario_base() {
    ob_start();
    session_start();
    global $wpdb;
    $prefijo = $wpdb->prefix . 'fqr_';
    $tabla_contacto = $prefijo . 'contacto';

    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["enviar_formulario"])) {
        if ($_POST['captcha'] == $_SESSION['captcha']) {
            $nombre = sanitize_text_field($_POST["nombre"]);
            $email = sanitize_email($_POST["email"]);
            $id_pregunta = isset($_POST["id_pregunta"]) ? intval($_POST["id_pregunta"]) : 0;

            $wpdb->insert($tabla_contacto, [
                "nombre" => $nombre,
                "email" => $email,
                "FK_idfaq" => $id_pregunta
            ]);

            echo "<p style='color: green;'>Gracias, <strong>" . esc_html($nombre) . "</strong>. Hemos recibido tu mensaje ✅</p>";
        } else {
            echo "<p style='color: red;'>Captcha incorrecto ❌, intenta de nuevo.</p>";
        }
    }
    ?>
    <?php
    return ob_get_clean();
}
?>