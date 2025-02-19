<?php 
/**
 * Plugin Name: FAQer 
 * Description: This plugin is a demo for a FAQ 
 * Version: 1.0.0
 * Author: Raul
 * License: GPL2
 */

// Hook para agregar el menú en la administración
add_action('admin_menu', 'mi_plugin_menu');

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
// Función para mostrar el contenido de la página del plugin
function mi_plugin_pagina() {
    ?>
    <div class="wrap">
        <h1>Bienvenido a Mi Plugin</h1>
        <p>Esta es la página de configuración de tu plugin personalizado.</p>
        <form method="post" action="options.php">
            <?php
            settings_fields('mi_plugin_opciones'); // Asegúrate de usar un nombre único para las opciones
            do_settings_sections('mi-plugin'); // Registrar la sección de tu plugin si la necesitas
            ?>
            <input type="submit" value="Guardar Cambios" class="button-primary">
        </form>
    </div>
    <?php
}
