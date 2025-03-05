<?php
function faqer_page() {
    ?> 
    <div class="wrap">
        <h1>Bienvenido a FAQer</h1>
        <h2>El plugin de Frequently Answered Question más extenso de EUROPA!!!</h2>       
        
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <!-- Sección Guardar FAQ -->
            <div style="text-align: center; width: 45%;">
                <h3>Para crear un FAQ nuevo, pulse en Crear FAQ.</h3>                
                <br>
                <a href="admin.php?page=Nuevo_FAQ" class="button">Crear FAQ</a>
            </div>

            <!-- Sección Guardar Categoría -->
            <div style="text-align: center; width: 45%;">
                <h3>Para crear una nueva categoría de FAQ, pulse en Crear Categoría.</h3>               
                <br>
                <a href="admin.php?page=FAQ_New_Categoria" class="button">Crear Categoría</a>
            </div>
        </div>
    </div>
    <?php
}
?>
