
    <div class="wrap">
        <h1>Bienvenido a Mi Plugin</h1>
        <p>Esta es la página de configuración de tu plugin personalizado.</p>
        <form method="post" action="options.php">
            <?php
            settings_fields('faqer_options'); // Asegúrate de usar un nombre único para las opciones
            do_settings_sections('faqer-plugin'); // Registrar la sección de tu plugin si la necesitas
            ?>
            <input type="submit" value="Guardar Cambios" class="button-primary">
        </form>
    </div>
