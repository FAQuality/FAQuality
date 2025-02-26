<?php
//Limpiamos buffer para la redireccion
ob_start();

//Funcion que pasamos al archivo fqr-function con todo el html y la inserccion de datos
function faqer_new_categoria_page() {
global $wpdb;

//Asignamos nombre y prefijo a la tabla
$tabla_categoria = $wpdb->prefix . 'categoria'; 

// Verifica si la acción es "delete" y si el ID de la categoría está definido
  if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
     $id = intval($_GET['id']); // Asegúrate de que el ID sea un número válido

     // Eliminar la categoría de la base de datos
     $wpdb->delete($tabla_categoria, ['id' => $id]);

     // Redirigir para evitar que la acción se repita al recargar la página
     wp_safe_redirect(admin_url('admin.php?page=FAQ_Categoria'));
     exit;
 }

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

function faqer_edit_categoria_page() {
    global $wpdb;
    
    //Asignamos nombre y prefijo a la tabla
    $tabla_categoria = $wpdb->prefix . 'categoria';       
    
      // Si la acción es editar, mostramos el formulario con los datos actuales
      if (isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['id'])) {
        $id = intval($_GET['id']);
        $categoria = $wpdb->get_row($wpdb->prepare("SELECT * FROM $tabla_categoria WHERE id = %d", $id));
    
        if ($categoria) {
            // Mostrar formulario de edición con los datos actuales
            ?>
           <div class="wrap">
            <h1>Editar Categoría</h1>
            <form method="post" action="">
                <!-- Campo oculto para la ID -->
                <input type="hidden" name="id" value="<?php echo esc_attr($id); ?>">
                <!-- Campo para el título -->      
                <!-- Insertamos los datos con el nombre   -->
                <label for="titulo_categoria"><strong>Título de la Categoría:</strong></label><br>
                <input type="text" id="nombre" name="nombre" value="<?php echo esc_attr($categoria->nombre); ?>"
                style="width: 100%; font-size: 18px; padding: 10px; margin-bottom: 10px;" placeholder="Escribe el título aquí">
                <?php
                //Se empieza uso de php en el html
                // Configuración del editor
                $contenido_por_defecto = $categoria->descripcion; //Contenido que aparecera en el formulario ya escrito (vacio)
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
                <input type="submit" name="update_categoria" value="Actualizar" class="button button-primary">

            </form>            
            </div>
         <?php
        }
    }
    
    // Verificar si se ha enviado el formulario de actualización
    if (isset($_POST['update_categoria'])) {
        $id = intval($_POST['id']);
        $nombre = sanitize_text_field($_POST['nombre']);
        $descripcion = wp_kses_post($_POST['descripcion']);      
    
        // Actualizar la categoría en la base de datos
        $wpdb->update(
            $tabla_categoria,
            ['nombre' => $nombre, 'descripcion' => $descripcion],
            ['id' => $id]
        );
        
        $resultado = $wpdb->update(
            $tabla_categoria,
            ['nombre' => $nombre, 'descripcion' => $descripcion],
            ['id' => $id]
        );
        
        // Forzar la redirección aunque no haya cambios
        if ($resultado === false) {
            die("Error en la actualización: " . $wpdb->last_error);
        } else {
            wp_safe_redirect(admin_url('admin.php?page=FAQ_Categoria'));
            exit;
        }
        // Redirigir después de la actualización
       
        
    }
}