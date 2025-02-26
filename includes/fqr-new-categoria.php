<?php
//Limpiamos buffer para la redireccion
ob_start();

//Funcion que pasamos al archivo fqr-function con todo el html y la inserccion de datos
function faqer_new_categoria_page() {
global $wpdb;

//Asignamos nombre y prefijo a la tabla
$tabla_categoria = $wpdb->prefix . 'categoria'; 

//Si mandamos un request(enviar) limpiamos codigo con sanitize y wp_kses_post
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = sanitize_text_field($_POST['nombre']);
    $descripcion = wp_kses_post($_POST['descripcion']);
    
    //Insertamos en la tabla los datos y hacemos redirect a la lista principal
    $wpdb->insert($tabla_categoria, ['nombre' => $nombre, 'descripcion' => $descripcion]);    
     wp_safe_redirect(admin_url('admin.php?page=FAQ_Categoria'));
    exit;
}

//HTML para la base de pagina web con herramientas de wordpress
    ?> 
   <div class="wrap">
    <h1>Crear Nueva Categoría</h1>
    <form method="post" action="">
        <!-- Campo para el título -->      
        <!-- Insertamos los datos con el nombre   -->
        <label for="titulo_categoria"><strong>Título de la Categoría:</strong></label><br>
        <input type="text" id="nombre" name="nombre" style="width: 100%; font-size: 18px; padding: 10px; margin-bottom: 10px;" placeholder="Escribe el título aquí">
        <?php
        //Se empieza uso de php en el html
        // Configuración del editor
        $contenido_por_defecto = ''; //Contenido que aparecera en el formulario ya escrito (vacio)
        $editor_id = 'descripcion'; //Base e identificador del editor de wp 

        //Configuracion del editor
        $configuracion_editor = array(
            'textarea_name' => 'descripcion', //Define el contenido del campo y se manda a la BD
            'media_buttons' => true, // Habilita el botón "Añadir medios"
            'teeny' => false, // Usa la versión completa del editor
            'quicktags' => true // Habilita etiquetas rápidas (negrita, cursiva, etc.)
        );

        // Muestra el editor en el formulario
        wp_editor($contenido_por_defecto, $editor_id, $configuracion_editor);
        ?>
        <br>
        <!-- Boton de enviar -->
        <input type="submit" value="Guardar Categoría" class="button button-primary">
    </form>
</div>
<?php
}
