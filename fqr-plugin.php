<?php
/*
Plugin Name: FAQer
Description: The best FAQ plugin! It allows you to create hierarchical questions and answers
Author: Los mejores programadores del mundo
*/


require_once plugin_dir_path(__FILE__) . 'includes/fqr-functions.php'; // Importar los menús del plugin
require_once plugin_dir_path(__FILE__) . 'init-faqer.php'; // Importar las funciones que se deben de ejecutar al iniciarse el plugin

register_activation_hook(__FILE__,"activation"); // Función que se ejecuta al activar el plugin