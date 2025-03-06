<?php
function faqer_edit_faq_page() {
    global $wpdb;
    
    //Asignamos nombre y prefijo a la tabla
    $prefijo = $wpdb->prefix . 'fqr_'; // Prefijo para todas las tablas
    $tabla_faq = $prefijo . 'faq';
    
      // Si la acción es editar, mostramos el formulario con los datos actuales
      if (isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['id'])) {
        $id = intval($_GET['id']);
        $faq = $wpdb->get_row($wpdb->prepare("SELECT * FROM $tabla_faq WHERE id = $id AND borrado = 0"));
        $id_padre_actual = $wpdb->get_var($wpdb->prepare("SELECT FK_idpadre FROM $tabla_faq WHERE id = $id"));
        $id_categoria_actual = $wpdb->get_var($wpdb->prepare("SELECT FK_idcat FROM $tabla_faq WHERE id = $id"));

        if ($faq) {
            $tabla_categoria = $prefijo . 'categoria';
            $categorias = $wpdb->get_results("SELECT id, categoria FROM $tabla_categoria WHERE borrado=0");
            $id_padre = $wpdb->get_results("SELECT id, pregunta FROM $tabla_faq WHERE borrado=0 OR id=1");

            // Mostrar formulario de edición con los datos actuales
            ?>
           <div class="wrap">
            <h1>Editar Categoría</h1>
            <form method="post" action="">
                <!-- Campo oculto para la ID -->
                <input type="hidden" name="id" value="<?php echo esc_attr($id); ?>">
                <!-- Campo para el título -->      
                <!-- Insertamos los datos con el nombre   -->
                <label for="pregunta"><strong>Pregunta:</strong></label><br>
                <input type="text" id="pregunta" name="pregunta" value="<?php echo esc_attr($faq->pregunta); ?>"
                style="width: 100%; font-size: 18px; padding: 10px; margin-bottom: 10px;" placeholder="Escribe la pregunta aquí">
                <!-- Lista dinamica -->
                <label for="id_cat"><strong>Selecciona una categoria:</strong></label>        
                <select name="id_cat" id="id_cat">
                    <?php
                    //Comprueba si existe categoria alguna
                    if ($categorias) {
                        //Reproduce en bucle las categorias existentes
                        foreach ($categorias as $categoria) {
                            $cat_selected = ($categoria->id == $id_categoria_actual) ? 'selected="selected"' : '';
                            echo '<option value="' . esc_attr($categoria->id) . '"' . $cat_selected . '>' . esc_html($categoria->categoria) . '</option>';
                        }
                    } else {
                        echo '<option value="">No hay ninguna categoria disponible</option>';
                    }
                    ?>
                </select><br>
        <!-- Id pregunta padre -->
        <label for="id_padre"><strong>Pregunta Padre:</strong></label>
        <select name="id_padre" id="id_padre">
                <?php
                //Comprueba si existe categoria alguna
                if ($id_padre) {
                    //Reproduce en bucle las categorias existentes
                    foreach ($id_padre as $id_pregunta) {
                        $selected = ($id_pregunta->id == $id_padre_actual) ? 'selected="selected"' : '';
                        echo '<option value="' . esc_attr($id_pregunta->id) . '"' . $selected . '>' . esc_html($id_pregunta->pregunta) . '</option>';
                    }
                } else {
                    echo '<option value="">No hay preguntas padres todavia</option>';
                }
                ?>
            </select><br>
                <?php
                //Se empieza uso de php en el html
                // Configuración del editor
                $contenido_por_defecto = $faq->respuesta; //Contenido que aparecera en el formulario ya escrito (vacio)
                $editor_id = 'respuesta'; //Base e identificador del editor de wp 

                //Configuracion del editor
                $configuracion_editor = array(
                    'textarea_name' => 'respuesta', //Define el contenido del campo y se manda a la BD
                    'media_buttons' => false, // Habilita el botón "Añadir medios"
                    'teeny' => false, // Usa la versión completa del editor
                    'quicktags' => true // Habilita etiquetas rápidas (negrita, cursiva, etc.)
                );

                // Muestra el editor en el formulario
                wp_editor($contenido_por_defecto, $editor_id, $configuracion_editor);
                ?>
                <br>
                <!-- Boton de enviar -->
                <input type="submit" name="update_faq" value="Actualizar" class="button button-primary">

            </form>            
            </div>
         <?php
        } 
    } else {
        $action = $_GET['action'];
        $id = $_GET['id'];
        $faq = $wpdb->get_row($wpdb->prepare("SELECT * FROM $tabla_faq WHERE id = $id AND borrado = 0"));
        wp_die("No se ha podido editar. Action -> $action. ID -> $id. Consulta-> $faq");
    }

    
    
    // Verificar si se ha enviado el formulario de actualización
    if (isset($_POST['update_faq'])) {
        $id = intval($_POST['id']);
        $faq = sanitize_text_field($_POST['pregunta']);
        $respuesta = wp_kses_post($_POST['respuesta']);
        $categoria = intval($_POST['id_cat']);
        $id_padre = intval($_POST['id_padre']);
        
    
        // Actualizar la categoría en la base de datos
        $resultado = $wpdb->update(
            $tabla_faq,
            ['pregunta' => $faq, 'respuesta' => $respuesta, 'FK_idcat' => $categoria, 'FK_idpadre' => $id_padre],
            ['id' => $id]
        );
        
        // Forzar la redirección aunque no haya cambios
        if ($resultado === false) {
            die("Error en la actualización: " . $wpdb->last_error);
            wp_safe_redirect(admin_url('admin.php?page=FAQ'));
        } else {
            wp_safe_redirect(admin_url('admin.php?page=FAQ'));
            exit;
        }
    }
}
