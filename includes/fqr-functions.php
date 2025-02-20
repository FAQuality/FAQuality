<?php
include 'fqr-primera-pagina.php';

add_action( 'admin_menu', 'fqr_Add_My_Admin_Link' );

// Add a new top level menu link to the ACP
function fqr_Add_My_Admin_Link()
{
    add_menu_page(
        'Miau Primera Pagina', // Title of the page
        'FAQer plugin', // Text to show on the menu link
        'manage_options', // Capability requirement to see the link
        'FAQer', // Slug del menú (URL amigable)
        'faqer_page()', // Función que mostrará el contenido de la página
        'dashicons-admin-plugins', // Icono del menú (puedes usar uno de los iconos predeterminados de WordPress)
        4 // Posición en el menú de administración
    );
}

// En fqr-functions.php
add_shortcode('fqr_shortcode', 'fqr_shortcode_function');

function fqr_shortcode_function() {
    return '<p>Este es un shortcode de FQR Plugin.</p>';
}

