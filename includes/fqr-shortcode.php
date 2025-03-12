<?php
function frontend_shortcode($atts) {
    ob_start(); // Inicia el almacenamiento en búfer de salida
    session_start();
    global $wpdb;    
    $prefijo = $wpdb->prefix . 'fqr_';
    $tabla_faq = $prefijo . 'faq';    

    //Usamo las herramientas de shortcode pata obtener el parámetro categorias del shortcode
    $atts = shortcode_atts([
        'categorias' => '' // IDs de las categorías separados por comas
    ], $atts);
    //Si no insertamos categorias mandamos mensaje
    if (empty($atts['categorias'])) {
        return '<p>No se especificaron categorías.</p>';
    }

    // Pasamos las ids de arrays a strings con array_map con la funcion intval que transforma de string
    // a int (numeor entero) y con explode indicamos los separadores del array (em ese caso la , )
    $categoria_ids = array_map('intval', explode(',', $atts['categorias']));

    // Si escribimos categoria inexistente mandamos mensaje
    if (empty($categoria_ids) || $categoria_ids[0] == 0) {
        return '<p>No se encontraron categorías seleccionadas.</p>';
    }

    // Crear placeholders para la consulta SQL pasando categoria_ids de array a una lista de marcadores de
    // posicion (de [1,2,3] a %d,%d,%d)
    $placeholders = implode(',', array_fill(0, count($categoria_ids), '%d'));
   
    // Seleccionamos los id de la tabla faq que sus id padres coincidad con los id insertados en el placeholder
    $query = $wpdb->prepare(
        "SELECT id FROM $tabla_faq WHERE FK_idcat IN ($placeholders) AND borrado = 0",
        ...$categoria_ids
    );

    // Ejecuta la consulta y guardamos en la variable $faq los resultados
    $faq = $wpdb->get_results($query);

    ?>
    <div>
        <?php if (!empty($faq)): ?> <!-- Comrpobamos que no este vacio la consulta -->
            <ul>       
                <?php foreach ($faq as $fila): ?>  <!-- Bucle para ir printeando las preguntas -->
                    <li>
                        <strong><?php echo esc_html($fila->pregunta); ?></strong><br>
                        <?php echo esc_html($fila->respuesta); ?><br>                        
                    </li>
                <?php endforeach; ?>
                <?php echo formulario_base(); ?>                
            </ul>
        <?php else: ?>
            <p>No hay preguntas en estas categorías.</p>
        <?php endif; ?>         
    </div>
    <?php

    return ob_get_clean(); // Devuelve el contenido almacenado en el búfer
}add_shortcode('FAQer', 'frontend_shortcode');


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
