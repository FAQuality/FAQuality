<?php

require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

global $wpdb;
$prefijo = $wpdb->prefix . 'fqr_';
$tabla_faq = $prefijo . 'faq';
$tabla_categoria = $prefijo . 'categoria';
$tabla_contacto = $prefijo . 'contacto';
$PK_categoria = $tabla_categoria . '(id)';
$PK_faq = $tabla_faq . '(id)';
$PK_contacto = $tabla_contacto . '(id)';

// Pregunta, respuesta, categoria, padre, borrado?
function crear_tabla_faq() {
    global $wpdb;
    $prefijo = $wpdb->prefix . 'fqr_';
    $tabla_faq = $prefijo . 'faq';
    $PK_faq = $tabla_faq . '(id)';
    $tabla_categoria = $prefijo . 'categoria';
    $PK_categoria = $tabla_categoria . '(id)';

    $sql_query = " CREATE TABLE $tabla_faq (
        id INT PRIMARY KEY AUTO_INCREMENT,
        pregunta VARCHAR(255) NOT NULL,
        respuesta TEXT NOT NULL,
        FK_idcat INT,
        FK_idpadre INT,
        borrado TINYINT(1) DEFAULT 0 NOT NULL CHECK (borrado = 0 OR borrado = 1),
        FOREIGN KEY (FK_idcat) REFERENCES $PK_categoria,
        FOREIGN KEY (FK_idpadre) REFERENCES $PK_faq
        );";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    $resultadodbdelta = dbDelta($sql_query);
}

// Categoria, descripcion, borrado?
function crear_tabla_categoria() {
    global $wpdb;
    $prefijo = $wpdb->prefix . 'fqr_';
    $tabla_categoria = $prefijo . 'categoria';

    $sql_query = "CREATE TABLE $tabla_categoria (
        id INT PRIMARY KEY AUTO_INCREMENT,
        categoria VARCHAR(255) NOT NULL,
        descripcion TEXT,
        borrado TINYINT(1) DEFAULT 0 NOT NULL CHECK (borrado = 0 OR borrado = 1)
        );";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    $resultadodbdelta = dbDelta($sql_query);

    
}

// Nombre, email, fecha, pregunta, atendido?, borrado?
function crear_tabla_contacto() {
    global $wpdb;
    $prefijo = $wpdb->prefix . 'fqr_';
    $tabla_contacto = $prefijo . 'contacto';
    $tabla_faq = $prefijo . 'faq';
    $PK_faq = $tabla_faq . '(id)';


    $sql_query = " CREATE TABLE $tabla_contacto (
        id INT PRIMARY KEY AUTO_INCREMENT,
        fecha DATE DEFAULT CURDATE(),
        nombre VARCHAR(255) NOT NULL,
        email VARCHAR(255),
        FK_idfaq INT,
        estado_atendido TINYINT(1) DEFAULT 0 NOT NULL CHECK (estado_atendido = 0 OR estado_atendido = 1),
        borrado TINYINT(1) DEFAULT 0 NOT NULL CHECK (borrado = 0 OR borrado = 1),
        FOREIGN KEY (FK_idfaq) REFERENCES $PK_faq 
        );";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    $resultadodbdelta = dbDelta($sql_query);
}

// Si se "elimina" algo, comprueba en contacto y faq que los que lo tuvieran como FK se marquen como borrados
function crear_trigger_al_marcar_borrado_pregunta() {
    global $wpdb;
    $prefijo = $wpdb->prefix . 'fqr_';
    $tabla_contacto = $prefijo . 'contacto';
    $tabla_faq = $prefijo . 'faq';

    $sql_query = "CREATE TRIGGER after_update_faq_trigger
        AFTER UPDATE ON $tabla_faq
        FOR EACH ROW
        BEGIN
            IF @disable_trigger = 0 AND NEW.borrado = 1 THEN
                SET @disable_trigger = 1;

                UPDATE $tabla_faq
                SET borrado = 1
                WHERE FK_idfaq = OLD.id;

                UPDATE $tabla_contacto
                SET borrado = 1
                WHERE FK_idfaq = OLD.id;

                SET @disable_trigger = 0;
            END IF;
        END;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    $resultadoquery = $wpdb->query($sql_query);
}

// Al "eliminar" una categoria, las preguntas con esa categoría, se "eliminan"
function crear_trigger_al_marcar_borrado_categoria() {
    global $wpdb;

    $prefijo = $wpdb->prefix . 'fqr_';
    $tabla_categoria = $prefijo . 'categoria';
    $tabla_faq = $prefijo . 'faq';

    $sql_query = "CREATE TRIGGER after_update_categoria_trigger
        AFTER UPDATE ON nphT7_fqr_categoria
        FOR EACH ROW
        BEGIN
            IF @disable_trigger = 0 AND NEW.borrado = 1 THEN
                SET @disable_trigger = 1;
        
                UPDATE nphT7_fqr_faq
                SET borrado = 1
                WHERE FK_idcat = OLD.id;
        
                SET @disable_trigger = 0;
            END IF;
        END;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    $resultadoquery = $wpdb->query($sql_query);

}

function activation() {
    crear_tabla_categoria();
    crear_tabla_faq();
    crear_tabla_contacto();
    crear_trigger_al_marcar_borrado_pregunta();
    crear_trigger_al_marcar_borrado_categoria();
}
