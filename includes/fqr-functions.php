<?php
//Insertamos los contenidos de los archivos
add_action( 'admin_menu', 'fqr_Add_My_Admin_Link' );
include 'fqr-primera-pagina.php';
include 'fqr-categoria.php';
include 'fqr-new-categoria.php';
include 'fqr-faq.php';
include 'fqr-nuevo-faq.php';
include 'fqr-contacto.php';
include 'fqr-aboutus.php';

// Add a new top level menu link to the ACP
function fqr_Add_My_Admin_Link()
{
    add_menu_page( //Menu principal
        'Mi plugin a caraperro', // Title of the page
        'FAQer Raul Edicion Juego del Año', // Text to show on the menu link
        'manage_options', // Capability requirement to see the link
        'FAQerF', // Slug del menú (URL amigable)
        'faqer_page', // Función que mostrará el contenido de la página
        'dashicons-format-status', // Icono del menú (puedes usar uno de los iconos predeterminados de WordPress)
        8 // Posición en el menú de administración
    );

    add_submenu_page( //Menu categoria
        'FAQerF',           // El slug del menú principal al que pertenece
        'Categoria', // Título de la página del submenú
        'Categoria',              // Nombre del submenú que aparecerá en el menú
        'manage_options',       // Permiso requerido
        'FAQ_Categoria',   // Slug único para la página del submenú
        'faqer_categoria_page' // Función que renderiza la página del submenú
    );
    function faqer_selection_new_categoria_page() {
        if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['id'])) {
            faqer_edit_categoria_page();
        } else {
            faqer_new_categoria_page();
        }
    }
    add_submenu_page( //Menu crear categorias
        'FAQerF',           // El slug del menú principal al que pertenece
        'Nueva_Categoria', // Título de la página del submenú
        'Nueva Categoria',  // Nombre del submenú que aparecerá en el menú
        'manage_options',       // Permiso requerido
        'FAQ_New_Categoria',   // Slug único para la página del submenú
        'faqer_selection_new_categoria_page' // Función que renderiza la página del submenú
    );
    add_submenu_page( //Menu FAQ
        'FAQerF',   // El slug del menú principal al que pertenece
        'FAQ', // Título de la página del submenú
        'FAQ',  // Nombre del submenú que aparecerá en el menú
        'manage_options',  // Permiso requerido
        'FAQ',   // Slug único para la página del submenú
        'faqer_faq_page' // Función que renderiza la página del submenú
    );
    add_submenu_page( //Menu crear faqs
        'FAQerF',  // El slug del menú principal al que pertenece
        'Nuevo FAQ', // Título de la página del submenú
        'Nuevo FAQ',  // Nombre del submenú que aparecerá en el menú
        'manage_options',       // Permiso requerido
        'Nuevo_FAQ',   // Slug único para la página del submenú
        'faqer_new_faq_page' // Función que renderiza la página del submenú
    );
    add_submenu_page( //Menu contacto
        'FAQerF',  // El slug del menú principal al que pertenece
        'Contacto', // Título de la página del submenú
        'Contato',  // Nombre del submenú que aparecerá en el menú
        'manage_options',       // Permiso requerido
        'Contacto',   // Slug único para la página del submenú
        'faqer_contact_page' // Función que renderiza la página del submenú
    );
    add_submenu_page( //Menu ABOUT US
        'FAQerF',  // El slug del menú principal al que pertenece
        'About_Us', // Título de la página del submenú
        'About Us',  // Nombre del submenú que aparecerá en el menú
        'manage_options',       // Permiso requerido
        'aboutus',   // Slug único para la página del submenú
        'faqer_aboutus_page' // Función que renderiza la página del submenú
    );
}

// En fqr-functions.php
add_shortcode('fqr_shortcode', 'fqr_shortcode_function');

function fqr_shortcode_function() {
    return '<p>Este es un shortcode de FQR Plugin.</p>';
}

