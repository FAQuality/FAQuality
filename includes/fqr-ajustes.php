<?php
function ajustes_page() {
    // Cargar la configuración actual desde el archivo
    $config = fqr_get_config();

    // Manejar el envío del formulario
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $nuevo_asunto = sanitize_text_field($_POST['asunto']);
        $nuevo_mensaje = wp_kses_post($_POST['mensaje']);

        $nueva_config = [
            'email_asunto' => $nuevo_asunto,
            'email_cuerpo' => $nuevo_mensaje
        ];

        // Guardar los nuevos ajustes en el archivo de configuración
        fqr_update_config($nueva_config);

        // Actualizar la configuración cargada con los nuevos valores
        $config = $nueva_config;

        echo '<div class="updated"><p><strong>Ajustes guardados correctamente.</strong></p></div>';
    }

    // Obtener los valores actuales para mostrar en el formulario
    $asunto_actual = isset($config['email_asunto']) ? esc_attr($config['email_asunto']) : 'Gracias por contactar con nosotros';
    $mensaje_actual = isset($config['email_cuerpo']) ? esc_textarea($config['email_cuerpo']) : 'Hemos recibido tu mensaje y contactaremos contigo en breve.';
    ?>

    <div class="wrap">
        <h1><strong>Ajustes</strong></h1>
        <h2>Configuración del email por defecto</h2>
        <form method="post" action="">
            <label for="asunto"><strong>Asunto:</strong></label>
            <div style="display: flex; align-items:center; gap:8px; margin:6px 0;">
                <input type="text" id="asunto" name="asunto" style="width: 40%; font-size: 16px; 
                min-height:2rem;" placeholder="Indica el asunto" value="<?php echo $asunto_actual; ?>">
            </div>
            <label for="mensaje_email"><strong>Mensaje por defecto:</strong></label>
            <div style="margin:6px 0;">
                <?php
                // Mostrar el editor avanzado de WordPress (TinyMCE)
                wp_editor(
                    $mensaje_actual, // Contenido actual del mensaje
                    'mensaje_email', // ID del editor
                    [
                        'textarea_name' => 'mensaje', // Nombre del campo en el formulario
                        'textarea_rows' => 10, // Número de filas visibles en el editor
                        'teeny' => false, // Editor completo (no simplificado)
                        'media_buttons' => true, // Ocultar botones para agregar medios
                    ]
                );
                ?>
            </div>
            <p>Para incluir el mensaje o el nombre del usuario en el CUERPO del correo, incluye <span class="llaves">{</span>nombre<span class="llaves">}</span> o <span class="llaves">{</span>mensaje<span class="llaves">}</span></p>
            <input type="submit" value="Guardar ajustes" style="margin-top: 10px;" class="button button-primary">
        </form>
    </div>


    <?php
}

// Función para obtener la configuración desde el archivo
function fqr_get_config() {
    $config_file = plugin_dir_path(__FILE__) . 'fqr-config.php';
    return file_exists($config_file) ? include $config_file : [];
}

// Función para guardar la configuración en el archivo
function fqr_update_config($new_config) {
    $config_file = plugin_dir_path(__FILE__) . 'fqr-config.php';
    $config_content = "<?php\nreturn " . var_export($new_config, true) . ";\n";
    return file_put_contents($config_file, $config_content);
}

