<?php
function frontend_shortcode() {
    ob_start(); // Inicia el almacenamiento en búfer de salida
    session_start();
    global $wpdb;    
    $prefijo = $wpdb->prefix . 'fqr_';
    $tabla_faq = $prefijo . 'faq';    
   
    
    // Consulta para obtener preguntas y categorías    
    $faq = $wpdb->get_results("SELECT id,pregunta,respuesta from $tabla_faq where FK_idpadre=1 AND borrado=0");   
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
}add_shortcode('shortcode', 'frontend_shortcode');

function formulario_base() {
    ob_start();
    session_start(); //Empezamos sesion para mandar el resultado del captcha
    global $wpdb;
    $prefijo = $wpdb->prefix . 'fqr_';
    $tabla_contacto = $prefijo . 'contacto';

    // Mensaje de validación
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["enviar_formulario"])) {
        if ($_POST['captcha'] == $_SESSION['captcha']) { //Comprobamos si en el campo captcha es igual al captcha original con la sesion
            $nombre = sanitize_text_field($_POST["nombre"]);
            $email = sanitize_email($_POST["email"]);
            $wpdb->insert($tabla_contacto, ["nombre" => $nombre, "email" => $email]);

            echo "<p style='color: green;'>Gracias, <strong>" . esc_html($nombre) . "</strong>. Hemos recibido tu mensaje ✅</p>";
        } else {
            echo "<p style='color: red;'>Captcha incorrecto ❌, intenta de nuevo.</p>";
        }
    }
    ?>
    <!-- Insertamos el formulario -->
    <form method="post">
        <label for="nombre">Nombre:</label>
        <input type="text" id="nombre" name="nombre" required>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>

        <label for="captcha">Introduce el texto de la imagen:</label>
        <img src="<?php echo plugin_dir_url(__FILE__) . 'captcha.php'; ?>" alt="CAPTCHA" id="captcha-img">
        <input type="text" name="captcha" required>

        <!-- Boton para generar de forma aleatoria el captcha -->
        <button type="button" onclick="document.getElementById('captcha-img').
                src='<?php echo plugin_dir_url(__FILE__) . 'captcha.php'; ?>?' + Math.random();
                //Recarga de forma aleatorio el documento captcha para que genere mas captcha aleatorios">
            Recargar CAPTCHA 🔄
        </button>
        <button type="submit" name="enviar_formulario">Enviar</button>
    </form>
    <?php
    return ob_get_clean();
}
?>





