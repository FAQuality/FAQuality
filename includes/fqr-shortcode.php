<?php
function frontend_shortcode() {
    ob_start(); // Inicia el almacenamiento en búfer de salida
    global $wpdb;    
    $prefijo = $wpdb->prefix . 'fqr_';
    $tabla_faq = $prefijo . 'faq';    

    // Consulta para obtener preguntas y categorías    
    $faq = $wpdb->get_results("SELECT id,pregunta,respuesta from $tabla_faq");
    ?>
    <div>
        <?php if (!empty($faq)): ?>
            <ul>
                <?php foreach ($faq as $fila): ?>
                    <li>
                        <strong><?php echo esc_html($fila->pregunta); ?></strong><br>
                        <?php echo esc_html($fila->respuesta); ?><br>                        
                    </li>
                <?php endforeach; ?>
                <?php echo formulario_base(); ?>
            </ul>
        <?php else: ?>
            <p>No hay categorias.</p>
        <?php endif; ?>        
    </div>
    <?php
    return ob_get_clean(); // Devuelve el contenido almacenado en el búfer
}add_shortcode('mi_shortcode', 'frontend_shortcode');

function formulario_base() {
    ob_start(); // Inicia el almacenamiento en búfer de salida
    global $wpdb;
    $prefijo = $wpdb->prefix . 'fqr_';
    $tabla_contacto = $prefijo . 'contacto';
    ?>
    <form method="post">
        <label for="nombre">Nombre:</label>
        <input type="text" id="nombre" name="nombre" required>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>
       
        <button type="submit" name="enviar_formulario">Enviar</button>
    </form>
    <?php    
    // Procesar el formulario si se ha enviado
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["enviar_formulario"])) {
        $nombre = sanitize_text_field($_POST["nombre"]);
        $email = sanitize_email($_POST["email"]);
        $wpdb->insert($tabla_contacto, ["nombre"=> $nombre,"email"=> $email]);

        echo "<p>Gracias, <strong>" . esc_html($nombre) . "</strong>. Hemos recibido tu mensaje.</p>";
    }
    return ob_get_clean(); // Devuelve el contenido almacenado en el búfer
}
?>





