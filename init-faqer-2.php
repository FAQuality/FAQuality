<?php
global $wpdb;
$prefijo = $wpdb->prefix . 'fqr_';
$tabla_faq = $prefijo . 'faq';
$tabla_categoria = $prefijo . 'categoria';
$tabla_contacto = $prefijo . 'contacto';
$PK_categoria = $tabla_categoria . '(id)';
$PK_faq = $tabla_faq . '(id)';
$PK_contacto = $tabla_contacto . '(id)';

error_log("Llega el codigo al init");
// Pregunta, respuesta, categoria, padre, borrado?
function crear_tabla_faq() {
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
        borrado TINYINT(1) DEFAULT 0 NOT NULL,
        CHECK (borrado = 0 OR borrado = 1),
        FOREIGN KEY (FK_idcat) REFERENCES $PK_categoria,
        FOREIGN KEY (FK_idpadre) REFERENCES $PK_faq 
        ) DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql_query);

    $resultado = $wpdb->query("SELECT id FROM $tabla_faq LIMIT 1");
    if ($resultado === false) {
        error_log("Error al crear la tabla faq: " . $wpdb->last_error);
    } else {
        error_log("Tabla faq creada correctamente.");
    }
}

// Categoria, descripcion, borrado?
function crear_tabla_categoria() {
    global $wpdb;
    global $tabla_categoria;

    $sql_query = " CREATE TABLE $tabla_categoria (
        id INT PRIMARY KEY AUTO_INCREMENT,
        categoria VARCHAR(255) NOT NULL,
        descripcion TEXT,
        borrado TINYINT(1) DEFAULT 0 NOT NULL,
        CHECK (borrado = 0 OR borrado = 1)
        ) DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql_query);

    $resultado = $wpdb->query("SELECT id FROM $tabla_categoria LIMIT 1");
    if ($resultado === false) {
        error_log('Error al crear la tabla categoria: ' . $wpdb->last_error);
    } else {
        error_log('Tabla categoria creada correctamente.');
    }
}

// Nombre, email, fecha, pregunta, atendido?, borrado?
function crear_tabla_contacto() {
    global $wpdb;
    global $tabla_contacto;
    global $PK_faq;


    $sql_query = " CREATE TABLE $tabla_contacto (
        id INT PRIMARY KEY AUTO_INCREMENT,
        fecha DATE DEFAULT CURDATE(),
        nombre VARCHAR(255) NOT NULL,
        email VARCHAR(255),
        FK_idfaq INT,
        estado_atendido TINYINT(1) DEFAULT 0 NOT NULL,
        borrado TINYINT(1) DEFAULT 0 NOT NULL,
        CHECK (borrado = 0 OR borrado = 1),
        CHECK (estado_atendido = 0 OR estado_atendido = 1),
        FOREIGN KEY (FK_idfaq) REFERENCES $PK_faq 
        ) DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql_query);

    $resultado = $wpdb->query("SELECT id FROM $tabla_contacto LIMIT 1");
    if ($resultado === false) {
        error_log('Error al crear la tabla contacto: ' . $wpdb->last_error);
    } else {
        error_log('Tabla contacto creada correctamente.');
    }
}

// Si se "elimina" algo, comprueba en contacto y faq que los que lo tuvieran como FK se marquen como borrados
function crear_trigger_al_marcar_borrado_pregunta() {
    global $wpdb;
    global $tabla_faq;
    global $tabla_contacto;

    $sql_query = "CREATE TRIGGER after_update_faq_trigger
        AFTER UPDATE ON $tabla_faq
        FOR EACH ROW
        BEGIN
            IF NEW.borrado = 1 THEN
                UPDATE $tabla_faq
                SET borrado = 1
                WHERE FK_idfaq = OLD.id;

                UPDATE $tabla_contacto
                SET borrado = 1
                WHERE FK_idfaq = OLD.id;
            END IF;
        END;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    $wpdb->query($sql_query);

    $resultado = $wpdb->query('SHOW TRIGGERS LIKE after_update_faq_trigger');
    if ($resultado === false) {
        error_log('Error al crear el trigger after_update_faq_trigger: ' . $wpdb->last_error);
    } else {
        error_log('Trigger after_update_faq_trigger creado correctamente.');
    }
}

// Al "eliminar" una categoria, las preguntas con esa categoría, se "eliminan"
function crear_trigger_al_marcar_borrado_categoria() {
    global $wpdb;
    global $tabla_categoria;
    global $tabla_faq;

    $sql_query = "CREATE TRIGGER after_update_categoria_trigger
        AFTER UPDATE ON $tabla_categoria
        FOR EACH ROW
        BEGIN
            IF NEW.borrado = 1 THEN
                UPDATE $tabla_faq
                SET borrado = 1
                WHERE FK_idcat = OLD.id;
            END IF;
        END;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    $wpdb->query($sql_query);

    $resultado = $wpdb->query("SHOW TRIGGERS LIKE 'after_update_categoria_trigger'");
    if ($resultado === false) {
        error_log('Error al crear el trigger after_update_categoria_trigger: ' . $wpdb->last_error);
    } else {
        error_log('Trigger after_update_categoria_trigger creado correctamente.');
    }
}

function activation() {
    error_log("Se activa el plugin");
    crear_tabla_categoria();
    crear_tabla_contacto();
    crear_tabla_faq();
    crear_trigger_al_marcar_borrado_pregunta();
    crear_trigger_al_marcar_borrado_categoria();
}
