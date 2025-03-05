<?php
/*
Plugin Name: FAQer_Raul_elMamasLokas
Description: Que vuelva la mili
Author: Alguien no muy del betis
*/

require_once plugin_dir_path(__FILE__) . 'includes/fqr-functions.php'; // Importar los menús del plugin
require_once plugin_dir_path(__FILE__) . 'init-faqer.php'; // Importar las funciones que se deben de ejecutar al iniciarse el plugin

register_activation_hook(__FILE__,"activation"); // Función que se ejecuta al activar el plugin
// Evita el acceso directo
if (!defined('ABSPATH')) {
    exit;
}


