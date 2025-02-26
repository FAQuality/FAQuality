<?php
/*
Plugin Name: FAQer
Description: This is my first plugin! It makes a new admin menu link!
Author: yo
*/

error_log("Llega el codigo al main");

require_once plugin_dir_path(__FILE__) . 'includes/fqr-functions.php';
require_once plugin_dir_path(__FILE__) . 'init-faqer-2.php';


register_activation_hook(__FILE__,"crear_tabla_faq"); // no funciona