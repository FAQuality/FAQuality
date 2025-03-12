<?php
/*


Plugin Name: FAQer Max Apocalipsis 7000
Description: FAQ para felinos. Si no eres felino deja de usar el plugin.
Author: Gato apocalíptico

*/

require_once plugin_dir_path(__FILE__) . 'includes/fqr-functions.php'; // Importar los menús del plugin
require_once plugin_dir_path(__FILE__) . 'init-faqer.php'; // Importar las funciones que se deben de ejecutar al iniciarse el plugin
require_once plugin_dir_path(__FILE__) . 'includes/fqr-shortcode.php';
include_once plugin_dir_path(__FILE__) . 'includes/fqr-ajax.php';
register_activation_hook(__FILE__,"activation"); // Función que se ejecuta al activar el plugin

add_shortcode('mi_shortcode', 'frontend_shortcode');

// Evita el acceso por ruta relativa 
if (!defined('ABSPATH')) {
    exit;
}

function fqr_enqueue_scripts() {
    wp_enqueue_script('fqr-js', plugin_dir_url(__FILE__) . 'assets/fqr-faq-query.js', array('jquery'), '1.0', true); // Añade el js antes de </body> con su dependencia (jquery) e indica la version de mi script para que los navegadores, al actualizar el plugin, vuelvan a descargarlo
    wp_localize_script('fqr-js', 'ajax_object', array('ajax_url' => admin_url('admin-ajax.php')));
}

add_action('wp_enqueue_scripts', 'fqr_enqueue_scripts');

// https://github.com/orgs/ValentinaSystem-FAQ/projects/1/views/1
// Problema de que el formulario se muestra varias veces. Eliminar formulario al cerrar pregunta.