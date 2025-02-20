<?php
function faqer_new_faq_page() {
    ?> 
   <div class="wrap">
    <h1>Crear Nuevo FAQs</h1>
    <form method="post" action="">
        <!-- Campo para el título -->
        <label for="titulo_faq"><strong>Título de la FAQ:</strong></label><br>
        <input type="text" id="titulo_faq" name="titulo_faq" style="width: 100%; font-size: 18px; padding: 10px; margin-bottom: 10px;" placeholder="Escribe el título aquí">
        <?php
        // Configuración del editor
        $contenido_por_defecto = ''; 
        $editor_id = 'descripcion_FAQ';

        $configuracion_editor = array(
            'textarea_name' => 'descripcion_FAQ', // Nombre del campo en el formulario
            'media_buttons' => true, // Habilita el botón "Añadir medios"
            'teeny' => false, // Usa la versión completa del editor
            'quicktags' => true // Habilita etiquetas rápidas (negrita, cursiva, etc.)
        );

        // Muestra el editor en el formularios
        wp_editor($contenido_por_defecto, $editor_id, $configuracion_editor);
        ?>
        <br>
        <input type="submit" value="Guardar FAQ" class="button button-primary">
    </form>
</div>
<?php
}