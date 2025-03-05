<?php
function frontend_shortcode() {
    ob_start(); // Inicia el almacenamiento en búfer de salida
    ?>
    <form method="post" action="">
        <label for="nombre">Nombre:</label>
        <input type="text" id="nombre" name="nombre" required>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>

        <input type="submit" name="enviar_formulario" value="Enviar">
    </form>
    
    <?php
    // Procesar el formulario si se envía
    if (isset($_POST['enviar_formulario'])) {
        $nombre = sanitize_text_field($_POST['nombre']);
        $email = sanitize_email($_POST['email']);

        echo "<p>¡Gracias, $nombre! Hemos recibido tu correo: $email</p>";
    }

    return ob_get_clean(); // Devuelve el contenido almacenado en el búfer
}add_shortcode('mi_shortcode', 'frontend_shortcode');
?>
