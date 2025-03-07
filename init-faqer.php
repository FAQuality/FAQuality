<?php

require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

global $wpdb;

// Variables principales
$prefijo = $wpdb->prefix . 'fqr_'; // Prefijo para todas las tablas
$tabla_categoria = $prefijo . 'categoria'; // Nombre de la tabla categoria
$tabla_faq = $prefijo . 'faq'; // Nombre de la tabla faq
$tabla_contacto = $prefijo . 'contacto'; // Nombre de la tabla contacto
$PK_categoria = $tabla_categoria . '(id)'; // Clave primaria de la tabla categoria
$PK_faq = $tabla_faq . '(id)'; // Clave primaria de la tabla faq
$PK_contacto = $tabla_contacto . '(id)'; // Clave primaria de la tabla contacto

// Funcion que crea la tabla FAQ
function crear_tabla_faq() {
    global $wpdb;
    // FIXME: Se redeclaran las variables porque al hacer las consultas usando global no se leen con el contenido
    $prefijo = $wpdb->prefix . 'fqr_';
    $tabla_faq = $prefijo . 'faq';
    $PK_faq = $tabla_faq . '(id)';
    $tabla_categoria = $prefijo . 'categoria';
    $PK_categoria = $tabla_categoria . '(id)';

    // Consulta para crear la tabla 
    $sql_query = " CREATE TABLE $tabla_faq (
        id INT PRIMARY KEY AUTO_INCREMENT,
        pregunta VARCHAR(255) NOT NULL,
        respuesta TEXT NOT NULL,
        FK_idcat INT, -- ID de categoría
        FK_idpadre INT DEFAULT 1, -- ID de pregunta padre
        borrado TINYINT(1) DEFAULT 0 NOT NULL CHECK (borrado = 0 OR borrado = 1),
        FOREIGN KEY (FK_idcat) REFERENCES $PK_categoria,
        FOREIGN KEY (FK_idpadre) REFERENCES $PK_faq
        );";

    // Manda la consulta contra la base de datos
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    $resultadodbdelta = dbDelta($sql_query);
}

// Funcion que crea la tabla categoria
function crear_tabla_categoria() {
    global $wpdb;

    // FIXME: Se redeclaran las variables porque al hacer las consultas usando global no se leen con el contenido
    $prefijo = $wpdb->prefix . 'fqr_';
    $tabla_categoria = $prefijo . 'categoria';

    // Consulta para crear la tabla 
    $sql_query = "CREATE TABLE $tabla_categoria (
        id INT PRIMARY KEY AUTO_INCREMENT,
        categoria VARCHAR(255) NOT NULL,
        descripcion TEXT,
        borrado TINYINT(1) DEFAULT 0 NOT NULL CHECK (borrado = 0 OR borrado = 1)
        );";

    // Manda la consulta contra la base de datos
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    $resultadodbdelta = dbDelta($sql_query);

    
}

// Funcion que crea la tabla contacto
function crear_tabla_contacto() {
    global $wpdb;

    // FIXME: Se redeclaran las variables porque al hacer las consultas usando global no se leen con el contenido
    $prefijo = $wpdb->prefix . 'fqr_';
    $tabla_contacto = $prefijo . 'contacto';
    $tabla_faq = $prefijo . 'faq';
    $PK_faq = $tabla_faq . '(id)';

    // Consulta para crear la tabla 
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

    // Manda la consulta contra la base de datos
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    $resultadodbdelta = dbDelta($sql_query);
}

// Si se "elimina" una pregunta, comprueba en contacto que los que la tuvieran como 
// clave foránea se marquen como borrados
function crear_trigger_al_marcar_borrado_pregunta() {
    global $wpdb;

    // FIXME: Se redeclaran las variables porque al hacer las consultas usando global no se leen con el contenido
    $prefijo = $wpdb->prefix . 'fqr_';
    $tabla_contacto = $prefijo . 'contacto';
    $tabla_faq = $prefijo . 'faq';

    // Consulta para crear el trigger  
    $sql_query = "CREATE TRIGGER after_update_faq_trigger -- Crea un auto-actualizador de datos
        AFTER UPDATE ON $tabla_faq -- se actualizan cuando haya una actualización en esta tabla
        FOR EACH ROW -- lee cada fila
        BEGIN
            IF @disable_trigger = 0 AND NEW.borrado = 1 THEN -- si en esa fila se cambia el dato de borrado a 1
                SET @disable_trigger = 1; -- desactiva que se pueda reactivar el trigger

                UPDATE $tabla_contacto
                SET borrado = 1 -- cambia a borrado el contacto 
                WHERE FK_idfaq = OLD.id; -- de los usuarios que hayan rellenado el formulario en una pregunta eliminada

                SET @disable_trigger = 0; -- activa que se pueda reactivar el trigger
            END IF;
        END;";

    // Manda la consulta contra la base de datos
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    $resultadoquery = $wpdb->query($sql_query);
}

// Al "eliminar" una categoria, las preguntas con esa categoría, se "eliminan"
function crear_trigger_al_marcar_borrado_categoria() {
    global $wpdb;

    // FIXME: Se redeclaran las variables porque al hacer las consultas usando global no se leen con el contenido
    $prefijo = $wpdb->prefix . 'fqr_';
    $tabla_categoria = $prefijo . 'categoria';
    $tabla_faq = $prefijo . 'faq';

    // Consulta para crear el trigger 
    $sql_query = "CREATE TRIGGER after_update_categoria_trigger -- Crea un auto-actualizador de datos
        AFTER UPDATE ON $tabla_categoria -- se actualizan cuando haya una actualización en esta tabla
        FOR EACH ROW -- lee cada fila
        BEGIN
            IF @disable_trigger = 0 AND NEW.borrado = 1 THEN -- si en esa fila se cambia el dato de borrado a 1
                SET @disable_trigger = 1; -- desactiva que se pueda reactivar el trigger
        
                UPDATE $tabla_faq
                SET borrado = 1 -- cambia a borrado la pregunta 
                WHERE FK_idcat = OLD.id; -- en las preguntas cuya categoría sea borrada
        
                SET @disable_trigger = 0; -- activa que se pueda reactivar el trigger
            END IF;
        END;";

    // Manda la consulta contra la base de datos
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    $resultadoquery = $wpdb->query($sql_query);

}

//Introduce una categoria vacia siempre que se ejecuta el plugin
function categoria_none() {
    global $wpdb;
    $prefijo = $wpdb->prefix . 'fqr_';
    
    $tabla_categoria = $prefijo . 'categoria';

    $sql_query = "INSERT INTO $tabla_categoria (id,categoria) VALUES ('1','Sin categoria)";
    
    $wpdb->query($sql_query);
}

//Introduce una pregunta FAQ vacia siempre que se ejecuta el plugin
function faq_none() {
    global $wpdb;
    $prefijo = $wpdb->prefix . 'fqr_';
    $tabla_faq = $prefijo . 'faq';

    $sql_query = "INSERT INTO $tabla_faq (id,pregunta,respuesta,borrado) VALUES (1,'Sin padre','Sin Madre',1)";
    
    $wpdb->query($sql_query);
}


// Funcion que se ejecuta al iniciar el plugin
function activation() {
    crear_tabla_categoria();
    crear_tabla_faq();
    crear_tabla_contacto();
    crear_trigger_al_marcar_borrado_pregunta();
    crear_trigger_al_marcar_borrado_categoria();
    categoria_none();
    faq_none();
}


