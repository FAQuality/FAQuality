<?php
// Función para mostrar el shortcode del FAQ
function frontend_shortcode($atts) {
    ob_start();
    session_start(); // Inicia la sesión para el CAPTCHA
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

    // Atributos del shortcode para filtrar por categorías
    $atts = shortcode_atts([
        'categorias' => '' // IDs de las categorías separados por comas
    ], $atts);

    if (!empty($atts['categorias'])) {
        $categoria_ids = array_map('intval', explode(',', $atts['categorias']));

        if (!empty($categoria_ids) && $categoria_ids[0] != 0) {
            $placeholders = implode(',', array_fill(0, count($categoria_ids), '%d'));

            $query = $wpdb->prepare(
                "SELECT id, pregunta, respuesta, FK_idpadre FROM $tabla_faq WHERE (FK_idpadre IN (" . implode(',', array_fill(0, count($ids_a_consultar), '%d')) . ") OR FK_idcat IN ($placeholders)) AND borrado = 0",
                array_merge($ids_a_consultar, $categoria_ids)
            );

            $faq = $wpdb->get_results($query);
        } else {
            $faq = []; // No se encontraron categorías válidas
        }
    } else {
        $ids_consulta = '(' . implode(',', $ids_a_consultar) . ')';
        $faq = $wpdb->get_results("SELECT id,pregunta,respuesta,FK_idpadre from $tabla_faq where FK_idpadre IN $ids_consulta and borrado=0");
    }
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
        <?php else: ?>
            <p>No hay preguntas en estas categorías.</p>
        <?php endif; ?>
    </div>
    <?php
    formulario_base();
    return ob_get_clean();
}
add_shortcode('mi_shortcode', 'frontend_shortcode');

// Función para mostrar el formulario de contacto
function formulario_base() {
    ob_start();
    session_start(); // Inicia la sesión para el CAPTCHA
    global $wpdb;
    $prefijo = $wpdb->prefix . 'fqr_';
    $tabla_contacto = $prefijo . 'contacto';

    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["enviar_formulario"])) {
        if (isset($_POST['captcha']) && $_POST['captcha'] == $_SESSION['captcha']) {
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
    <form method="post">
        <label for="nombre">Nombre:</label>
        <input type="text" id="nombre" name="nombre" required>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>

        <label for="captcha">Introduce el texto de la imagen:</label>
        <img src="<?php echo plugin_dir_url(__FILE__) . 'captcha.php'; ?>" alt="CAPTCHA" id="captcha-img">
        <input type="text" name="captcha" required>

        <button type="button" onclick="document.getElementById('captcha-img').src='<?php echo plugin_dir_url(__FILE__) . 'captcha.php'; ?>?' + Math.random();">
            Recargar CAPTCHA 
        </button>
        <button type="submit" name="enviar_formulario">Enviar</button>
    </form>
    <?php
    return ob_get_clean();
}
?>