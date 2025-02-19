<?php 
/**
 * Plugin Name: FAQer 
 * Description: This plugin is a demo for a FAQ 
 * Version: 1.0.0
 * Author: Raul
 * License: GPL2
 */


function mi_plugin_menu() {
    add_menu_page(
        'FAQer', // Título de la página
        'FAQer', // Nombre del menú
        'manage_options', // Capacidad requerida para ver el menú
        'mi-plugin', // Slug del menú (URL amigable)
        'mi_plugin_pagina', // Función que mostrará el contenido de la página
        'dashicons-admin-plugins', // Icono del menú (puedes usar uno de los iconos predeterminados de WordPress)
        4 // Posición en el menú de administración
    );
}


?>