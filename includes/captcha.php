<?php
session_start(); //Iniciamos sesion en php para guardar los datos de la imagen y poder verificarla luego
header("Content-type: image/png"); //Asignamos que este archivo devuelva una imagen png y no un html

// Generar texto aleatorio
$captcha_text = substr(md5(rand()), 0, 6); //Genera el texto aleatorio
$_SESSION['captcha'] = $captcha_text; //Guarda este texto generado en la sesison para verificar

// Crear imagen
$img = imagecreate(120, 40); //crea lienzo de la imagen
$bg = imagecolorallocate($img, 255, 255, 255); //Genera color fondo
$text_color = imagecolorallocate($img, 0, 0, 0); //Genera color letras
// Insertamos el texto generado en la imagen, le ponemos un tamaño y un color
imagestring($img, 5, 20, 10, $captcha_text, $text_color);

//Guardamos la imagen en png (que es lo que va a recoger este archivo por el header)
imagepng($img);
imagedestroy($img); //Borra la imagen del servidor tras mandarla para ahorrar espacio
?>