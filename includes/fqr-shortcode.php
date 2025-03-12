<?php
// Función para mostrar el shortcode del FAQ
function frontend_shortcode() {
    ob_start();
    global $wpdb;
    $prefijo = $wpdb->prefix . 'fqr_';
    $tabla_faq = $prefijo . 'faq';

    $id_seleccionada = isset($_GET['id']) ? intval($_GET['id']) : 0;
    $ids_a_consultar = [1];

    if ($id_seleccionada > 0) {
        $ids_a_consultar[] = $id_seleccionada;
        $hijas = $wpdb->get_col($wpdb->prepare("SELECT id FROM $tabla_faq WHERE FK_idpadre = %d and borrado=0", $id_seleccionada));
        $ids_a_consultar = array_merge($ids_a_consultar, $hijas);
    }

    $ids_consulta = '(' . implode(',', $ids_a_consultar) . ')';
    $faq = $wpdb->get_results("SELECT id,pregunta,respuesta,FK_idpadre from $tabla_faq where FK_idpadre IN $ids_consulta and borrado=0");
    ?>
    <div class="faq-container">
        <?php if (!empty($faq)): ?>
            <ul class="faq-list">
                <?php foreach ($faq as $fila): ?>
                    <li class="faq-item" data-padre="<?php echo esc_attr($fila->FK_idpadre); ?>" data-estado="cerrado">
                        <strong class="faq-question" data-id="<?php echo esc_attr($fila->id); ?>">
                            <?php echo esc_html($fila->pregunta); ?>
                        </strong><br>
                        <div class="faq-answer" style="display:none">
                            <?php echo esc_html($fila->respuesta); ?><br>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
            <?php
                if (empty($hijas) && $id_seleccionada > 0) {
                    echo formulario_base($id_seleccionada);
                }
            ?>
        <?php else: ?>
            <p>No hay categorias.</p>
        <?php endif; ?>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('mi_shortcode', 'frontend_shortcode');

// Función para mostrar el formulario de contacto
function formulario_base($id_pregunta) {
    ob_start();
    global $wpdb;
    $prefijo = $wpdb->prefix . 'fqr_';
    $tabla_contacto = $prefijo . 'contacto';
    ?>
    <form method="post" class="formulario-base" data-padre-form="<?php echo esc_attr($id_pregunta); ?>">
        <!-- Campo oculto para almacenar la ID -->
        <input type="hidden" name="id_pregunta" value="<?php echo esc_attr($id_pregunta); ?>">
        
        <label for="nombre">Nombre:</label>
        <input type="text" id="nombre" name="nombre" required>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>

        <button type="submit" name="enviar_formulario">Enviar</button>
    </form>
    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["enviar_formulario"])) {
        $nombre = sanitize_text_field($_POST["nombre"]);
        $email = sanitize_email($_POST["email"]);
        $id_pregunta = isset($_POST["id_pregunta"]) ? intval($_POST["id_pregunta"]) : 0;
        
        // Insertar incluyendo la ID de la pregunta
        $wpdb->insert($tabla_contacto, [
            "nombre" => $nombre,
            "email" => $email,
            "FK_idfaq" => $id_pregunta  // Asegúrate que este campo existe en tu tabla
        ]);
        
        echo "<p>Gracias, <strong>" . esc_html($nombre) . "</strong>. Hemos recibido tu mensaje.</p>";
    }
    return ob_get_clean();
}
?>