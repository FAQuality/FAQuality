<?php
add_action( 'admin_menu', 'fqr_Add_My_Admin_Link' );

// Add a new top level menu link to the ACP
function fqr_Add_My_Admin_Link()
{
    add_menu_page(
        'Miau Primera Pagina', // Title of the page
        'FAQer plugin', // Text to show on the menu link
        'manage_options', // Capability requirement to see the link
        'FAQer', // Slug del menú (URL amigable)
        'FAQer_pagina', // Función que mostrará el contenido de la página
        'dashicons-admin-plugins', // Icono del menú (puedes usar uno de los iconos predeterminados de WordPress)
        4 // Posición en el menú de administración
    );
}

// En fqr-functions.php
add_shortcode('fqr_shortcode', 'fqr_shortcode_function');

function fqr_shortcode_function() {
    return '<p>Este es un shortcode de FQR Plugin.</p>';
}
function mi_plugin_pagina() {
    ?> 
    <div class="wrap">
        <h1>Bienvenido a Mi Plugin</h1>
        <p>Esta es la página de configuración de tu plugin personalizado.</p>
        <form method="post" action="options.php">
            <?php
            settings_fields('mi_plugin_opciones'); // Asegúrate de usar un nombre único para las opciones
            do_settings_sections('mi-plugin'); // Registrar la sección de tu plugin si la necesitas
            ?>
            <input type="submit" value="Guardar Cambios" class="button-primary">
        </form>
    </div>
    <?php
}
