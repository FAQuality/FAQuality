<?php

$prefijo = 'fqr_';
$tabla_faq = $prefijo . 'faq';
$tabla_categoria = $prefijo . 'categoria';
$tabla_contacto = $prefijo . 'contacto';
$PK_categoria = $tabla_categoria . '(id)';
$PK_faq = $tabla_faq . '(id)';
$PK_contacto = $tabla_contacto . '(id)';

function crear_tabla_faq() { // Aqui se guardan las preguntas con sus respuestas
    global $wpdb;
    global $tabla_faq;
    global $PK_faq;
    global $PK_categoria;

    $sql_query = " CREATE TABLE $tabla_faq (
                    id INT PRIMARY KEY AUTO_INCREMENT,
                    pregunta VARCHAR(255) NOT NULL,
                    respuesta TEXT NOT NULL,
                    FK_idcat INT,
                    FK_idpadre INT,
                    FOREIGN KEY (FK_idcat) REFERENCES $PK_categoria,
                    FOREIGN KEY (FK_idpadre) REFERENCES $PK_faq 
                    ) DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
                    ;
    ";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql_query);
}

function crear_tabla_categoria() { // Aqui se guardan las categorias
    global $wpdb;
    global $tabla_categoria;

    $sql_query = " CREATE TABLE $tabla_categoria (
                    id INT PRIMARY KEY AUTO_INCREMENT,
                    categoria VARCHAR(255) NOT NULL,
                    descripcion TEXT,
                    ) DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
                    ;
    ";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql_query);
}

function crear_tabla_contacto() { // Tabla en la que se guarda la información del formulario de contacto del final
    global $wpdb;
    global $tabla_contacto;
    global $PK_faq;
    

    $sql_query = " CREATE TABLE $tabla_contacto (
                    id INT PRIMARY KEY AUTO_INCREMENT,
                    fecha DATE DEFAULT CURDATE(),
                    nombre TEXT NOT NULL,
                    email VARCHAR(255),
                    FK_idfaq INT,
                    estado TINYINT(1),
                    FOREIGN KEY (FK_idfaq) REFERENCES $PK_faq
                    ) DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
                    ;
    ";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql_query);
}

function activation() {
    crear_tabla_categoria();
    crear_tabla_contacto();
    crear_tabla_faq();
}

register_activation_hook(__FILE__,"activation");
