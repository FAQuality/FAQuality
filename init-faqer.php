<?php
global $wpdb;
$prefijo = $wpdb->prefix . 'fqr_'; //
$tabla_faq = $prefijo . 'faq';
$tabla_categoria = $prefijo . 'categoria';
$tabla_contacto = $prefijo . 'contacto';
$PK_categoria = $tabla_categoria . '(id)';
$PK_faq = $tabla_faq . '(id)';
$PK_contacto = $tabla_contacto . '(id)';

// Pregunta, respuesta, categoria, padre, borrado?
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
                    borrado TINYINT(1) DEFAULT 0 NOT NULL,
                    CHECK (borrado = 0 OR borrado = 1),
                    FOREIGN KEY (FK_idcat) REFERENCES $PK_categoria,
                    FOREIGN KEY (FK_idpadre) REFERENCES $PK_faq 
                    ) DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
                    ;
    ";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql_query);
}

// Categoria, descripcion, borrado?
function crear_tabla_categoria() { // Aqui se guardan las categorias
    global $wpdb;
    global $tabla_categoria;

    $sql_query = " CREATE TABLE $tabla_categoria (
                    id INT PRIMARY KEY AUTO_INCREMENT,
                    categoria VARCHAR(255) NOT NULL,
                    descripcion TEXT,
                    borrado TINYINT(1) DEFAULT 0 NOT NULL,
                    CHECK (borrado = 0 OR borrado = 1)
                    ) DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
                    ;
    ";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql_query);
}

// Nombre, email, fecha, pregunta, atendido?, borrado?
function crear_tabla_contacto() { // Tabla en la que se guarda la información del formulario de contacto del final
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
                    CHECK (estado = 0 OR estado = 1),
                    FOREIGN KEY (FK_idfaq) REFERENCES $PK_faq 
                    ) DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
                    ;
    ";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql_query);
}

// Si se "elimina" algo, comprueba en contacto y faq que los que lo tuvieran como FK se marquen como borrados
function crear_trigger_al_marcar_borrado_pregunta() { // Crea el trigger para marcar como borrado las preguntas hijas cuando el padre es marcado
    global $wpdb;
    global $tabla_faq;
    global $tabla_contacto;

    $sql_query = "CREATE TRIGGER after_update_faq
                    AFTER UPDATE ON $tabla_faq -- Despues de actualizar cualquier registro de faq
                    FOR EACH ROW -- Cada fila ACTUALIZADA
                        BEGIN
                            IF NEW.borrado = 1 THEN -- Si se actualiza a borrado 1
                                UPDATE $tabla_faq -- Actualiza la tabla contacto
                                SET borrado = 1 -- Pone borrado 1
                                WHERE FK_idfaq = OLD.id;  -- Donde la pregunta padre sea el ID de la pregunta marcada
                            
                                UPDATE $tabla_contacto  -- Actualiza la tabla contacto
                                SET borrado = 1
                                WHERE FK_idfaq = OLD.id;
                            END IF;
                    END
                    ;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql_query);
}

// Al "eliminar" una categoria, las preguntas con esa categoría, se "eliminan"
function crear_trigger_al_marcar_borrado_categoria() {
    global $wpdb;
    global $tabla_categoria;
    global $tabla_faq;

    $sql_query = "CREATE TRIGGER after_update_faq
                    AFTER UPDATE ON $tabla_categoria -- Despues de actualizar cualquier registro de categoria
                    FOR EACH ROW -- Cada fila ACTUALIZADA
                        BEGIN
                            IF NEW.borrado = 1 THEN -- Si se actualizan los registros a borrado 1
                                UPDATE $tabla_faq -- Actualiza la tabla faq
                                SET borrado = 1 -- Pone borrado 1
                                WHERE FK_idcat = OLD.id; -- Donde la categoria de la pregunta sea la que sea ha marcado
                            END IF;
                    END
                    ;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql_query);
}


function activation() {
    crear_tabla_categoria();
    crear_tabla_contacto();
    crear_tabla_faq();
    crear_trigger_al_marcar_borrado_pregunta();
    crear_trigger_al_marcar_borrado_categoria();
}

register_activation_hook(__FILE__,"activation");
