<?php
//Globalizamos el uso de los prefijos para sql("fqr_" "categorias")
$prefijo = $wpdb->prefix ."fqr_";
$tabla_categoria = $prefijo."categoria";

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
        'Miau Primera Pagina', // Title of the page
        'FAQer_raul', // Text to show on the menu link
        'manage_options', // Capability requirement to see the link
        'FAQer', // Slug del menú (URL amigable)
        'faqer_page', // Función que mostrará el contenido de la página
        'dashicons-format-status', // Icono del menú (puedes usar uno de los iconos predeterminados de WordPress)
        8 // Posición en el menú de administración
    );

    add_submenu_page( //Menu categoria
        'FAQer',           // El slug del menú principal al que pertenece
        'Categoria', // Título de la página del submenú
        'Categoria',              // Nombre del submenú que aparecerá en el menú
        'manage_options',       // Permiso requerido
        'FAQ_Categoria',   // Slug único para la página del submenú
        'faqer_categoria_page' // Función que renderiza la página del submenú
    );
    add_submenu_page( //Menu crear categorias
        'FAQer',           // El slug del menú principal al que pertenece
        'Nueva_Categoria', // Título de la página del submenú
        'Nueva Categoria',  // Nombre del submenú que aparecerá en el menú
        'manage_options',       // Permiso requerido
        'FAQ_New_Categoria',   // Slug único para la página del submenú
        'faqer_new_categoria_page' // Función que renderiza la página del submenú
    );
    add_submenu_page( //Menu FAQ
        'FAQer',   // El slug del menú principal al que pertenece
        'FAQ', // Título de la página del submenú
        'FAQ',  // Nombre del submenú que aparecerá en el menú
        'manage_options',  // Permiso requerido
        'FAQ',   // Slug único para la página del submenú
        'faqer_faq_page' // Función que renderiza la página del submenú
    );
    add_submenu_page( //Menu crear faqs
        'FAQer',  // El slug del menú principal al que pertenece
        'Nuevo FAQ', // Título de la página del submenú
        'Nuevo FAQ',  // Nombre del submenú que aparecerá en el menú
        'manage_options',       // Permiso requerido
        'Nuevo_FAQ',   // Slug único para la página del submenú
        'faqer_new_faq_page' // Función que renderiza la página del submenú
    );
    add_submenu_page( //Menu contacto
        'FAQer',  // El slug del menú principal al que pertenece
        'Contacto', // Título de la página del submenú
        'Contato',  // Nombre del submenú que aparecerá en el menú
        'manage_options',       // Permiso requerido
        'Contacto',   // Slug único para la página del submenú
        'faqer_contact_page' // Función que renderiza la página del submenú
    );
    add_submenu_page( //Menu ABOUT US
        'FAQer',  // El slug del menú principal al que pertenece
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

//Funcion para crear tabla en sql
function faq_create_table(){    
    global $wpdb;
    global $prefijo;
    global $tabla_categoria;
//Crreamos la tabla con comando SQL y sus caracteristicas
    $sql = "CREATE TABLE $tabla_categoria (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        nombre varchar(255) NOT NULL,
        descripcion text NOT NULL,
        PRIMARY KEY (id)
    ) $charset_collate;";

    //Buscamos la herramienta dbdelta para evitar el duplicado de tablas.
     require_once ABSPATH . 'wp-admin/includes/upgrade.php';
     dbDelta($sql);
 }
//Con un hook obligamos a que cuando se abra este archivo cree la tabla, es decir, al inicio del plugin
register_activation_hook(__FILE__, 'faq_create_table');

