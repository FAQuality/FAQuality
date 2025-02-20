<?php
add_action( 'admin_menu', 'fqr_Add_My_Admin_Link' );
include 'fqr-primera-pagina.php';
// Add a new top level menu link to the ACP
function fqr_Add_My_Admin_Link()
{
    add_menu_page(
        'Miau Primera Pagina', // Title of the page
        'FAQer plugin_raul', // Text to show on the menu link
        'manage_options', // Capability requirement to see the link
        'FAQer', // Slug del menú (URL amigable)
        'faqer_page', // Función que mostrará el contenido de la página
        'dashicons-admin-plugins', // Icono del menú (puedes usar uno de los iconos predeterminados de WordPress)
        4 // Posición en el menú de administración
    );

    add_submenu_page(
        'fqr-plugin',           // El slug del menú principal al que pertenece
        'Submenú FAQer', // Título de la página del submenú
        'Submenú',              // Nombre del submenú que aparecerá en el menú
        'manage_options',       // Permiso requerido
        'fqr-plugin-submenu',   // Slug único para la página del submenú
        'fqr_plugin_submenu_page' // Función que renderiza la página del submenú
    );
}

// En fqr-functions.php
add_shortcode('fqr_shortcode', 'fqr_shortcode_function');

function fqr_shortcode_function() {
    return '<p>Este es un shortcode de FQR Plugin.</p>';
}
function fqr_plugin_submenu_page() {
    echo '<div class="wrap">';
    echo '<h1>Bienvenido al Submenú de Mi Plugin</h1>';
    echo '<p>Aquí va el contenido de la página del submenú.</p>';
    echo '</div>';
}
