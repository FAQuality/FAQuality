<?php
function deleteCategoria() {
    global $wpdb;
    $prefijo = $wpdb->prefix . 'fqr_';
    $tabla_categoria = $prefijo . 'categoria';

    $sql_query = "SET @disable_trigger = 0;
        UPDATE $tabla_categoria 
        SET borrado = 1 
        WHERE id = 1;";
}

function deleteFAQ() {
    global $wpdb;
    $prefijo = $wpdb->prefix . 'fqr_';
    $tabla_faq = $prefijo . 'faq';

    $sql_query = "SET @disable_trigger = 0;
        UPDATE $tabla_faq
        SET borrado = 1
        WHERE id = 2 OR FK_idpadre = 2;";
}

function mostrarTabla($nombretabla) {
    


}