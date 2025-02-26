<?php
/*
Plugin Name: FAQer Fenrando
Description: Ooo
Author: yo
*/

require_once plugin_dir_path(__FILE__) . 'includes/fqr-functions.php';
require_once plugin_dir_path(__FILE__) . 'init-faqer-2.php';

register_activation_hook(__FILE__,"activation"); // no funciona