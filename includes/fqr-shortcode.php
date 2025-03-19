<?php
function frontend_shortcode($atts) {
    ob_start();
    session_start();
    global $wpdb;
    $prefijo = $wpdb->prefix . 'fqr_';
    $tabla_faq = $prefijo . 'faq';

    // Procesamiento de atributos del shortcode
    $atts = shortcode_atts([
        'categorias' => '' // IDs de las categorías separados por comas
    ], $atts);

    if (empty($atts['categorias'])) {
        return '<p>No se especificaron categorías.</p>';
    }

    $categoria_ids = array_map('intval', explode(',', $atts['categorias']));

    if (empty($categoria_ids) || $categoria_ids[0] == 0) {
        return '<p>No se encontraron categorías seleccionadas.</p>';
    }

    $placeholders = implode(',', array_fill(0, count($categoria_ids), '%d'));
    $query = $wpdb->prepare(
        "SELECT id, pregunta, respuesta, FK_idpadre FROM $tabla_faq WHERE FK_idcat IN ($placeholders) AND FK_idpadre=1 AND borrado = 0 order by prioridad desc",
        ...$categoria_ids
    );

    $faq = $wpdb->get_results($query);

    ?>
    <div class="faq-container">
        <?php if (!empty($faq)): ?>
            <ul class="faq-list">
                <?php foreach ($faq as $fila): ?>
                    <li class="faq-item" data-padre="<?php echo esc_attr($fila->FK_idpadre); ?>" data-estado="cerrado">
                        <strong class="faq-question" style="cursor: pointer;" data-id="<?php echo esc_attr($fila->id); ?>">
                            <?php echo esc_html($fila->pregunta); ?>
                        </strong><br>
                        <div class="faq-answer" style="display:none">
                            <?php echo esc_html($fila->respuesta); ?><br>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>No hay preguntas en estas categorías.</p>
        <?php endif; ?>
    </div>
    <script> 
        document.addEventListener('DOMContentLoaded', function() {
        function aplicarSangria() {
            const items = document.querySelectorAll('.faq-item');
            const itemMap = new Map();

            // Crear un mapa de todos los items
            items.forEach(item => {
                const id = item.querySelector('.faq-question').getAttribute('data-id');
                itemMap.set(id, item);
            });

            // Calcular y aplicar la sangría
            items.forEach(item => {
                let nivel = 0;
                let currentItem = item;
                while (currentItem) {
                    const padreId = currentItem.getAttribute('data-padre');
                    if (padreId === '1') break; // Detener en el nivel raíz
                    currentItem = itemMap.get(padreId);
                    if (currentItem) nivel++;
                }
                item.style.marginLeft = `${nivel * 20}px`;
            });

            // Aplicar sangría al formulario
            const formulario = document.querySelector('.formulario-base');
            if (formulario) {
                const padreFormId = formulario.getAttribute('data-padre-form');
                if (padreFormId) {
                    const padrePregunta = itemMap.get(padreFormId);
                    if (padrePregunta) {
                        const nivelPadre = calcularNivel(padrePregunta, itemMap);
                        formulario.style.marginLeft = `${(nivelPadre) * 20}px`;
                    }
                }
            }
        }

        function calcularNivel(item, itemMap) {
            let nivel = 0;
            let currentItem = item;
            while (currentItem) {
                const padreId = currentItem.getAttribute('data-padre');
                if (padreId === '1') break; // Detener en el nivel raíz
                currentItem = itemMap.get(padreId);
                if (currentItem) nivel++;
            }
            return nivel;
        }

            // Aplicar sangría inicial
            aplicarSangria();

            // Observar cambios en el DOM para aplicar sangría a nuevos elementos
            const observer = new MutationObserver(aplicarSangria);
            observer.observe(document.querySelector('.faq-container'), { childList: true, subtree: true });

            // Manejar la apertura de preguntas y actualización del formulario
            document.querySelector('.faq-list').addEventListener('click', function(e) {
                if (e.target.classList.contains('faq-question')) {
                    const preguntaId = e.target.getAttribute('data-id');
                    const formulario = document.querySelector('.formulario-base');
                    if (formulario) {
                        formulario.setAttribute('data-padre-form', preguntaId);
                        aplicarSangria();
                    }
                }
            });
        });
    </script>
    <?php

    formulario_base();
    return ob_get_clean();
}
add_shortcode('FAQer', 'frontend_shortcode');


function procesar_formulario() {
    session_start();
    global $wpdb;
    $prefijo = $wpdb->prefix . 'fqr_';
    $tabla_contacto = $prefijo . 'contacto';

    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["enviar_formulario"])) {
        if ($_POST['captcha'] == $_SESSION['captcha']) {
            $nombre = sanitize_text_field($_POST["nombre"]);
            $email = sanitize_email($_POST["email"]);
            $mensaje = sanitize_text_field($_POST["mensaje"]);
            $id_pregunta = isset($_POST["id_pregunta"]) ? intval($_POST["id_pregunta"]) : 0;

            $wpdb->insert($tabla_contacto, [
                "nombre" => $nombre,
                "email" => $email,
                "mensaje" => $mensaje,
                "FK_idfaq" => $id_pregunta
            ]);

            wp_safe_redirect(add_query_arg('form_status', 'success', wp_get_referer()));
            exit;
        } else {
            wp_safe_redirect(add_query_arg('form_status', 'error_captcha', wp_get_referer()));
            exit;
        }
    }
}

add_action('admin_post_nopriv_fqr_form_submit', 'procesar_formulario');
add_action('admin_post_fqr_form_submit', 'procesar_formulario');

function formulario_base() {
    ob_start();
    session_start();
    global $wpdb;
    $prefijo = $wpdb->prefix . 'fqr_';
    $tabla_contacto = $prefijo . 'contacto';

    if (isset($_GET['form_status'])) {
        switch ($_GET['form_status']) {
            case 'success':
                echo '<p style="color: green;">Mensaje enviado con éxito ✅</p>';
                break;
            case 'error_captcha':
                echo '<p style="color: red;">Error en el CAPTCHA ❌</p>';
                break;
        }
    }
    return ob_get_clean();
}
?>