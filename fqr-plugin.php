<?php
/*

Plugin Name: FAQer Beta 0.1
Description: Beta 01
Author: Los betas

*/

require_once plugin_dir_path(__FILE__) . 'includes/fqr-functions.php'; // Importar los menús del plugin
require_once plugin_dir_path(__FILE__) . 'init-faqer.php'; // Importar las funciones que se deben de ejecutar al iniciarse el plugin

register_activation_hook(__FILE__,"activation"); // Función que se ejecuta al activar el plugin

function fqr_shortcode_function() {
    return "Fuistes troliado PUTOOO!!!";    
}add_shortcode('mi_shortcode', 'fqr_shortcode_function');


// Evita el acceso directo
if (!defined('ABSPATH')) {
    exit;
}


