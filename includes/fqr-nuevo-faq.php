<?php
//Limpiamos buffer para la redireccion
ob_start();

//Funcion que pasamos al archivo fqr-function con todo el html y la inserccion de datos
function faqer_new_faq_page()
{
    global $wpdb;

    //Asignamos nombre y prefijo a la tabla
    $prefijo = $wpdb->prefix . 'fqr_';
    $tabla_faq = $prefijo . 'faq';
    $tabla_categoria = $prefijo . 'categoria';

    //Si mandamos un request(enviar) limpiamos codigo con sanitize y wp_kses_post
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // $FK_idpadre=isset($_POST['id_padre']) == false ? 'NULL' : $_POST['id_padre'];        
        $pregunta = sanitize_text_field($_POST['pregunta']);
        $respuesta = wp_kses_post($_POST['respuesta']);
        $FK_idpadre = sanitize_text_field($_POST['id_padre']);
        $FK_idcat = sanitize_text_field($_POST['id_cat']);

        //Insertamos en la tabla los datos y hacemos redirect a la lista principal
        $wpdb->insert($tabla_faq, [
            'pregunta' => $pregunta,
            'respuesta' => $respuesta,
            'FK_idpadre' => $FK_idpadre,
            'FK_idcat' => $FK_idcat
        ]);
        wp_safe_redirect(admin_url('admin.php?page=FAQ'));
        exit;
    }

    $categorias = $wpdb->get_results("SELECT id, categoria FROM $tabla_categoria WHERE borrado=0");
    $preguntas = $wpdb->get_results("
    SELECT id, pregunta, FK_idpadre 
    FROM $tabla_faq 
    WHERE borrado = 0 
    ORDER BY FK_idpadre, id
");

    function mostrar_opciones_jerarquicas($preguntas, $padre_id = 1, $nivel = 0)
    {
        $html = '';
        foreach ($preguntas as $pregunta) {
            if ($pregunta->FK_idpadre == $padre_id) {
                $sangria = str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;', $nivel);
                $html .= sprintf(
                    '<option value="%s">%s%s</option>',
                    esc_attr($pregunta->id),
                    $sangria . ($nivel > 0 ? '&#8627; ' : ''),
                    esc_html($pregunta->pregunta)
                );
                $html .= mostrar_opciones_jerarquicas($preguntas, $pregunta->id, $nivel + 1);
            }
        }
        return $html;
    }


    //HTML para la base de pagina web con herramientas de wordpress
?>
    <div class="wrap">
        <div class="boton-justify">
            <h1>Crear Nuevo FAQ</h1>
            <form method="post" action="" id="nuevo-faq-form" onsubmit="return validarFormulario();">
                <input name="Guardar_Pregunta" type="submit" value="Guardar Pregunta" class="button button-primary">
        </div> <!-- Campo para el título -->
        <!-- Insertamos los datos con el nombre   -->
        <label for="titulo_faq"><strong>Pregunta:</strong></label><br>
        <input type="text" id="pregunta" name="pregunta" style="width: 100%; font-size: 18px; padding: 10px; margin-bottom: 10px;" placeholder="Escribe la pregunta aquí">
        <p id="error-pregunta" style="color: red; display: none;">Por favor, ingrese una pregunta.</p>
        <div class="editable-justify"> <!-- Lista dinamica -->
            <label for="id_cat" style="margin-top: 30px;"><Strong>Selecciona una categoria:</Strong></label>
            <select name="id_cat" id="id_cat" style="margin-bottom: 10px;">
                <?php
                //Comprueba si existe categoria alguna
                if ($categorias) {
                    //Reproduce en bucle las categorias existentes
                    foreach ($categorias as $categoria) {
                        echo '<option value="' . esc_attr($categoria->id) . '">' . esc_html($categoria->categoria) . '</option>';
                    }
                } else {
                    echo '<option value="">No hay categorías disponibles</option>';
                }
                ?>
            </select>
            <!-- Id pregunta padre -->
            <label for="id_padre" style="margin-top: 30px !important;"><strong>Pregunta Padre:</strong></label>
            <select name="id_padre" id="id_padre" style="margin-bottom: 10px;">
                <option value="1">Sin padre</option>
                <?php
                if ($preguntas) {
                    echo mostrar_opciones_jerarquicas($preguntas);
                } else {
                    echo '<option value="">No hay preguntas disponibles</option>';
                }
                ?>
            </select>
        </div>
        <!-- Respuesta -->
        <label for="respuesta"><strong>Respuesta:</strong></label><br>
        <?php
        //Se empieza uso de php en el html       
        $contenido_por_defecto = ''; //Contenido que aparecera en el formulario ya escrito (vacio)
        $editor_id = 'respuesta'; //Base e identificador del editor de wp 

        //Configuracion del editor
        $configuracion_editor = array(
            'textarea_name' => 'respuesta', //Define el contenido del campo y se manda a la BD
            'media_buttons' => true, // Habilita el botón "Añadir medios"
            'teeny' => false, // Usa la versión completa del editor
            'quicktags' => true // Habilita etiquetas rápidas (negrita, cursiva, etc.)
        );

        // Muestra el editor en el formulario
        wp_editor($contenido_por_defecto, $editor_id, $configuracion_editor);
        ?>
        <br>
        <script>
            function validarFormulario() {
                const preguntaInput = document.getElementById('pregunta');
                const errorPregunta = document.getElementById('error-pregunta');

                // Elimina espacios al inicio y al final
                const pregunta = preguntaInput.value.trim();

                if (pregunta === '') {
                    // Muestra el mensaje de error
                    errorPregunta.style.display = 'block';
                    errorPregunta.textContent = 'Por favor, ingrese una pregunta válida.';

                    // Evita que se envíe el formulario
                    return false;
                }

                // Oculta el mensaje de error si todo está bien
                errorPregunta.style.display = 'none';
                return true;
            }
        </script>
        <!-- Boton de enviar -->
        <input name="Guardar_Pregunta" type="submit" value="Guardar Pregunta" class="button button-primary">
        </form>
    </div>
<?php
    // if(isset($_POST['Guardar_Pregunta'])) {
    //     $FK_idpadre=isset($_POST['id_padre']) == false ? 'NULL' : $_POST['id_padre'];
    //     wp_die(''. $FK_idpadre .'');
    // }
}
