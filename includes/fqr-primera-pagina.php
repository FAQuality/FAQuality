<?php
function faqer_page()
{
    ?>
    <div class="wrap">
        <h2>Guía del Plugin FAQer para WordPress</h2>
        
        <div class="administracion">
            <h3>Administración</h3>
            <p>Acceda a las siguientes funciones desde el panel de administración:</p>
            <a href="admin.php?page=Nuevo_FAQ" class="button button-primary">Crear FAQ</a>
            <a href="admin.php?page=FAQ_New_Categoria" class="button button-secondary">Crear categoría</a>
            <a href="admin.php?page=Dudas" class="button button-secondary">Usuarios que rellenaron el formulario</a>
        </div>
        <div class="fqr-uso-shortcode">
            <?php
            global $wpdb;
            $tabla_categoria = $wpdb->prefix . 'fqr_' . 'categoria';
            $categorias = $wpdb->get_results("SELECT id, categoria FROM $tabla_categoria WHERE borrado=0");
            ?>
            <!-- Creamos la lista con las categorias que queremos seleccionar -->
            <div>
                <h2>Generar shortcode</h2>
                <!-- Lista dinamica -->
                <label for="id_cat"><strong>Añadir categoria al shortcode:</strong> </label><br>
                <select name="id_cat" id="id_cat">
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
                </select><br>
                <!-- Contenedor de etiquetas, actualmente vacio ya que no se han agregado ninguna -->
                <div id="tagContainer" style="margin-top: 10px;"></div>

                <!-- Shortcode dinámico -->
                <!-- Contenedor del shortcode y botón -->
                <div style="display: flex; align-items: center; gap: 10px;">
                    <p><strong>Shortcode final: </strong><span id="shortcode">[FAQer
                            categorias="1"]</span></p>
                    <button onclick="copiarAlPortapapeles()" class="button nuevo">Copiar</button>
                    <!-- Este span se mostrará después de copiar el texto -->
                    <span id="copiadoMensaje" style="display: none; color: green;">¡Copiado!</span>
                </div>
                <script>
                    function copiarAlPortapapeles() {
                        actualizarShortcode(); // Asegura que el shortcode esté actualizado antes de copiar

                        let texto = document.getElementById("shortcode").innerText; // Obtiene el shortcode dinámico
                        navigator.clipboard.writeText(texto) // Copia al portapapeles
                            .then(() => { //El metodo writeText manda un promise (una funcion) que si se realiza ejecuta .then
                                // Mostrar el mensaje de "Copiado" al lado del botón
                                let mensaje = document.getElementById("copiadoMensaje");
                                mensaje.style.display = 'inline'; // Muestra el mensaje
                                setTimeout(() => {
                                    mensaje.style.display = 'none'; // Oculta el mensaje después de 2 segundos
                                }, 2000);
                            }) //Si ocurre cualquier error, manda mensaje de error
                            .catch(err => console.error("Error al copiar: ", err)); // Manejo de errores
                    }
                </script>
            </div>
            <script>
                let categoriasSeleccionadas = []; // Array que almacena los IDs de las categorías seleccionadas

                function actualizarShortcode() { //Coge las categorias seleccionadas y las inserta en la base del shortcode
                    document.getElementById("shortcode").innerText = '[FAQer categorias="' + categoriasSeleccionadas.join(",") + '"]';
                }

                function agregarCategoria() { //Llamamos a la funcion select para usarla
                    let select = document.getElementById("id_cat");
                    let categoriaID = select.value;
                    let categoriaTexto = select.options[select.selectedIndex].text;

                    // Evitar agregar duplicados o una opción vacía
                    if (categoriaID && !categoriasSeleccionadas.includes(categoriaID)) {
                        categoriasSeleccionadas.push(categoriaID);

                        // Crear etiqueta visual con css escrito en la misma linea
                        let tagContainer = document.getElementById("tagContainer");
                        let tag = document.createElement("span");
                        tag.className = "tag";
                        tag.style.cssText = "display: inline-block; background: #0073aa; color: white; padding: 5px 10px; margin: 5px; border-radius: 5px;";
                        tag.innerHTML = categoriaTexto + ' <button onclick="eliminarCategoria(\'' + categoriaID + '\')" style="background: red; border: none; color: white; padding: 2px 5px; cursor: pointer;">X</button>';
                        tag.setAttribute("data-id", categoriaID);
                        tagContainer.appendChild(tag);

                        // Actualizar shortcode
                        actualizarShortcode();
                    }
                }

                function eliminarCategoria(id) {
                    // Remover la categoría del array
                    categoriasSeleccionadas = categoriasSeleccionadas.filter(categoria => categoria !== id);

                    // Eliminar la etiqueta visual
                    let tagContainer = document.getElementById("tagContainer");
                    let tags = tagContainer.getElementsByClassName("tag");
                    for (let tag of tags) {
                        if (tag.getAttribute("data-id") === id) {
                            tag.remove();
                            break;
                        }
                    }

                    // Actualizar shortcode
                    actualizarShortcode();
                }
                // Evento para detectar cambios en el <select>
                document.getElementById("id_cat").addEventListener("change", agregarCategoria);
            </script>
            <div class="fqr-funcionamiento">
                <h3>Funcionamiento</h3>
                <ul>
                    <li>Las preguntas se muestran de forma jerárquica, con preguntas padre e hijas.</li>
                    <li>El orden de las preguntas se puede personalizar mediante un campo de prioridad. La que más prioridad
                        tenga, será la que más arriba aparecerá. Si tiene el valor predeterminado (0), se ordenará por orden
                        de
                        creación (ID)</li>
                    <li>Si una pregunta abierta no tiene preguntas hijas, aparecerá un formulario de contacto para que el
                        usuario
                        envíe su duda.</li>
                </ul>
            </div>
            <div class="fqr-caracteristicas">
                <h3>Características principales</h3>
                <ul>
                    <li>FAQs jerárquicas con preguntas padre personalizadas</li>
                    <li>Orden personalizable de preguntas mediante campo de prioridad</li>
                    <li>Mostrar preguntas según su categoría</li>
                    <li>Formulario de contacto integrado para preguntas que tenga el usuario y no encuentre en el FAQ</li>
                </ul>
            </div>
            <?php

}
?>