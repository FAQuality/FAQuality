<?php
// Añadimos la clase wp_list_table de wordpress y pedimos que sea requerido ya que no es publico
// (se coge de otro enlace dentro del mismo wordpress).
if (!class_exists('WP_List_Table')) {
    require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php'; //ABSPATH es ruta absoluta
}

//Creamos la clase categoria_list_table que al extender de wp_list_table, cogemos lo que realiza la funcion
//wp_list_table y la personalizamos 
class Categoria_List_Table extends WP_List_Table {

//Creamos un constructor con la informacion principal (ajax desactivado por ahora)
    function __construct() {
        parent::__construct([
            'singular' => 'categoria',
            'plural'   => 'categorias',
            'ajax'     => false
        ]);
    }

//Creamos nuestras columnas (indicamos el tipo de columna que queremos y despues le ponemos nombre)    
    function get_columns() {
        return [
            'cb'      => '<input type="checkbox" />',
            'title'   => 'Título',
            'author'  => 'Autor',
            'date'    => 'Fecha',
            'description' => 'Descripcion'
        ];
    }

//Agregamos contenido a las columnas

//Generamos hueco para checkbox indicando que el valor de cada checbox es igual a su id
    function column_cb($item) {
        return sprintf('<input type="checkbox" name="registro[]" value="%s" />', $item['ID']);
    }

//Generamos hueco para titulo con enlace externo y le da el efecto cliqueable
    function column_title($item) {
        $edit_link = '?page=FAQ_Categoria&action=edit&id=' . $item['ID'];
        return sprintf('<strong><a href="%s">%s</a></strong>', $edit_link, esc_html($item['title']));
    }

//Generamos hueco para descripcion     
    function column_description($item) {
        return esc_html($item['description']);  
    }

//Generamos hueco para autor    
    function column_author($item) {
        return esc_html($item['author']);  
    }

//Generamos hueco para la fecha
    function column_date($item) {
        return esc_html($item['date']);  
    }
    
//Cargamos datos en las columnas
    function prepare_items() {
    $columns = $this->get_columns();  // Obtiene las columnas definidas antes
    $hidden  = [];                    // Columnas ocultas (vacío porque mostramos todas)
    $sortable = [];                    // Columnas ordenables (no usamos ordenamiento)
        $this->_column_headers = [$columns, $hidden, $sortable];

 // Ingresamos los datos que queramos
        $this->items = [
        ['ID' => 1, 'title' => 'Categoría 1', 'author' => 'Raul', 'date' => '2025-02-19', 'description'=> 'Prueba1'],
        ['ID' => 2, 'title' => 'Categoría 2', 'author' => 'Fernando', 'date' => '2025-02-18', 'description'=> 'Prueba2'],
        ];
    }
}

//Muestra la tabla en la pagina con los datos que agregamos anteriormente
function faqer_categoria_page() {
    echo '<div class="wrap"><h1>Categorías</h1>';
    $table = new Categoria_List_Table();
    $table->prepare_items();
    $table->display();
    echo '</div>';
}
