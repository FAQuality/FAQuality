<?php
/*


Plugin Name: FAQer Apocalipsis Prime 2
Description: Miau. Versión mejorada del FAQer Prime Apocalipsis
Author: El gato más bonito de España

*/

require_once plugin_dir_path(__FILE__) . 'includes/fqr-functions.php'; // Importar los menús del plugin
require_once plugin_dir_path(__FILE__) . 'init-faqer.php'; // Importar las funciones que se deben de ejecutar al iniciarse el plugin
require_once plugin_dir_path(__FILE__) . 'includes/fqr-shortcode.php';
register_activation_hook(__FILE__,"activation"); // Función que se ejecuta al activar el plugin

add_shortcode('mi_shortcode', 'frontend_shortcode');

// Evita el acceso directo
if (!defined('ABSPATH')) {
    exit;
}
 

// Arreglar Nuevo FAQ

