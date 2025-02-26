<?php
function faqer_page() {
    ?> 
    <div class="wrap">
        <h1>Bienvenido a Mi Plugin</h1>
        <p>Esta es la página de configuración de tu plugin personalizado.</p>
        <form method="post" action="procesar_formulario.php">
            <p>Ahora no hace nada, solo testing</p>
            <input type="submit" value="Guardar Cambios" class="button-primary">
        </form>
    </div>
    <?php
}