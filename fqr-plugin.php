<?php
/*
Plugin Name: FAQer_raul
Description: This is my first plugin! It makes a new admin menu link!
Author: yo
*/

// Evita el acceso directo
if (!defined('ABSPATH')) {
    exit;
}

require_once plugin_dir_path(__FILE__) . 'includes/fqr-functions.php';

register_activation_hook(__FILE__, 'crear_tabla_categorias');

function crear_tabla_categorias(){    
    global $wpdb;

    $tabla_categoria = $wpdb->prefix . 'categoria'; 
    $charset_collate = $wpdb->get_charset_collate();
   
    
//Crreamos la tabla con comando SQL y sus caracteristicas
    $sql = "CREATE TABLE $tabla_categoria (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        nombre varchar(255) NOT NULL,
        descripcion text NOT NULL,
        PRIMARY KEY (id)
    ) ";

    //Buscamos la herramienta dbdelta y la importa para evitar el duplicado de tablas.
     require_once ABSPATH . 'wp-admin/includes/upgrade.php';
     dbDelta($sql);
 }