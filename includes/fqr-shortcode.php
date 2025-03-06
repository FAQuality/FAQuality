<?php
function frontend_shortcode() {
    ob_start(); // Inicia el almacenamiento en búfer de salida
    global $wpdb;
    
    $prefijo = $wpdb->prefix . 'fqr_';
    $tabla_faq = $prefijo . 'faq';
    $tabla_categoria = $prefijo . 'categoria';

    // Consulta para obtener preguntas y categorías    
    $faq = $wpdb->get_results("SELECT id,pregunta,respuesta from $tabla_faq");

    ?>
    <div>
        <?php if (!empty($faq)): ?>
            <ul>
                <?php foreach ($faq as $fila): ?>
                    <li>
                        <strong><?php echo esc_html($fila->pregunta); ?></strong><br>
                        <?php echo esc_html($fila->respuesta); ?><br>                        
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>No hay categorias.</p>
        <?php endif; ?>
    </div>
    <?php

    return ob_get_clean(); // Devuelve el contenido almacenado en el búfer
}

add_shortcode('mi_shortcode', 'frontend_shortcode');
?>
