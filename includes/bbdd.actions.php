<?php
function deleteCategoria() {
    $sql_query = "SET @disable_trigger = 0;
        UPDATE nphT7_fqr_categoria 
        SET borrado = 1 
        WHERE id = 1;";
}