<?php
// Añadimos la clase wp_list_table de wordpress y pedimos que sea requerido ya que no es publico
// (se coge de otro enlace dentro del mismo wordpress).
if (!class_exists('WP_List_Table')) {
    require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php'; //ABSPATH es ruta absoluta
}

//Creamos la clase categoria_list_table que al extender de wp_list_table, cogemos lo que realiza la funcion
//wp_list_table y la personalizamos 
class FAQ_List_Table extends WP_List_Table {

//Creamos un constructor con la informacion principal (ajax desactivado por ahora)
    function __construct() {
        parent::__construct([
            'singular' => 'FAQ',
            'plural'   => 'FAQs',
            'ajax'     => false
        ]);
    }

//Creamos nuestras columnas (indicamos el tipo de columna que queremos y despues le ponemos nombre)    
    function get_columns() {
        return [
        'cb'         => '<input type="checkbox" />', // Checkbox para seleccionar filas
        'id_nuevo'   => 'ID Nuevo',                 // Nueva columna para el ID
        'nombre'     => 'Nombre',                   // Nombre de la persona o categoría
        'pregunta'   => 'Pregunta',                 // Resumen de la pregunta
        'respuesta'  => 'Respuesta',                // Respuesta relacionada
        
    ];
    }

//Agregamos contenido a las columnas

//Generamos hueco para checkbox indicando que el valor de cada checbox es igual a su id
    function column_cb($item) {
        return sprintf('<input type="checkbox" name="registro[]" value="%s" />', $item['ID']);
    }

//Generamos hueco para titulo (nombre) con enlace externo y le da el efecto cliqueable
    function column_ID_nuevo($item) {
        $edit_link = '?page=FAQ&action=edit&id=' . $item['ID'];
        return sprintf('<strong><a href="%s">%s</a></strong>', $edit_link, esc_html($item['id_nuevo']));
    }

//Generamos hueco para el nuevo ID   
    function column_nombre($item) {
        return esc_html($item['nombre']);  
    }

//Generamos hueco para pregunta    
    function column_pregunta($item) {
        return esc_html($item['pregunta']);  
    }

//Generamos hueco para respuesta 
    function column_respuesta($item) {
        return esc_html($item['respuesta']);  
    }
   
    
//Cargamos datos en las columnas
    function prepare_items() {
    $columns = $this->get_columns();  // Obtiene las columnas definidas antes
    $hidden  = [];                    // Columnas ocultas (vacío porque mostramos todas)
    $sortable = [];                    // Columnas ordenables (no usamos ordenamiento)
        $this->_column_headers = [$columns, $hidden, $sortable];

 // Ingresamos los datos que queramos
        $this->items = [
        ['ID' => 1, 'id_nuevo' => 'Pregunta 1', 'nombre' => 'Raul', 'pregunta' => '¿?',
         'respuesta'=> 'Si'],
         ['ID' => 2, 'id_nuevo' => 'Pregunta 2', 'nombre' => 'Fernando', 'pregunta' => '¿?',
         'respuesta'=> 'No'],
        ];
    }
}

//Muestra la tabla en la pagina con los datos que agregamos anteriormente
function faqer_FAQ_page() {
    echo '<div class="wrap"><h1>Frequetly Answered Questionss</h1>';
    $table = new FAQ_List_Table();
    $table->prepare_items();
    $table->display();
    echo '</div>';
}
