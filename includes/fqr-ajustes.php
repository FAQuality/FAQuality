<?php

function ajustes_page()
{
?>
    <div class="wrap">
        <h1>Ajustes</h1>
        <form method="post" action="">
            <label for="email_emisor"><strong>Email como emisor:</strong></label>
            <div style="display: flex; align-items:center; gap:8px; margin:6px 0px;">
                <input type="text" id="email_emisor" name="email" style="width: 40%; font-size: 16px;"
                    placeholder="Escribe el email aquí">
            </div>
            <label for="mensaje_email"><strong>Mensaje por defecto:</strong></label>
            <div style="display: flex; align-items:center; gap:8px; margin-top:6px;">
                <textarea type="text" id="mensaje_email" name="mensaje" style="width: 40%; font-size: 16px; 
                height:6rem; min-height:2rem;"
                    placeholder="Escribe el mensaje aquí"></textarea>
            </div>
            <input type="submit" value="Guardar" style="margin-top: 10px;" class="button button-primary">
            <p style="opacity: 0.8;">Más ajustes proximamente...</p>
        </form>
    <?php
}
