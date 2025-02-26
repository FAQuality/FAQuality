<?php
/*
Plugin Name: FAQer_raul
Description: Que vuelva la mili
Author: Alguien muy guapo
*/

// Evita el acceso directo
if (!defined('ABSPATH')) {
    exit;
}

require_once plugin_dir_path(__FILE__) . 'includes/fqr-functions.php';

//Obligamos a que cuando se abra plugin cree las tablas necesarias
register_activation_hook(__FILE__, 'crear_tabla_categorias');

//Funcion que va a crear el plugin
function crear_tabla_categorias(){    
    global $wpdb;

//Asingamos el prefijo categoria
    $tabla_categoria = $wpdb->prefix . 'categoria';   
   
    
//Creamos la tabla con comando SQL y sus caracteristicas
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