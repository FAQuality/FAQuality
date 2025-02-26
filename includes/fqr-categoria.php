<?php
// Añadimos la clase wp_list_table de wordpress y pedimos que sea requerido ya que no es publico
// (se recoge de otro enlace dentro del mismo wordpress).

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
    
   //Obtiene los datos de la base de datos 
   function get_categorias() {
    global $wpdb;
    $prefijo = $wpdb->prefix . 'fqr_';    
    $tabla_categoria = $prefijo . 'categoria'; // Usa el prefijo correcto
    return $wpdb->get_results("SELECT id, nombre, descripcion FROM $tabla_categoria", ARRAY_A);
} 

//Cargamos datos en las columnas
    function prepare_items() {
    $this->items = $this->get_categorias(); //Insertamos los datos de la tabla de sql
    $columns = $this->get_columns();  // Obtiene las columnas definidas antes
    $hidden  = [];                    // Columnas ocultas (vacío porque mostramos todas)
    $sortable = [];                    // Columnas ordenables (no usamos ordenamiento)
        $this->_column_headers = [$columns, $hidden, $sortable];   
     }

//Creamos nuestras columnas (indicamos el tipo de columna que queremos y despues le ponemos nombre)    
    function get_columns() {
        return [
            'cb' => '<input type="checkbox" />',            
            'nombre'   => 'Nombre',
            'descripcion'  => 'Descripción',
            'acciones' => 'Acciones'
            
        ];
    }

//Agregamos contenido a las columnas
//Generamos hueco para checkbox indicando que el valor de cada checbox es igual a su id
    function column_cb($item) {
        return sprintf('<input type="checkbox" name="registro[]" value="%s" />', $item['ID']);
    }

//Generamos hueco para nombre con enlace externo y le da el efecto cliqueable
    function column_nombre($item) {
        $edit_link = '?page=FAQ_Categoria&action=edit&id=' . $item['ID'];
        return sprintf('<strong><a href="%s">%s</a></strong>', $edit_link, esc_html($item['nombre']));
    }

//Generamos hueco para descripcion     
    function column_descripcion($item) {
        return esc_html($item['descripcion']);  
    }  

//Generamos hueco para la id
    function column_id($item) {
        return esc_html($item['id']);  
    }  

    /** Agrega botones de acción en la columna "Acciones" */
    function column_acciones($item) {
        $edit_link = '?page=FAQ_New_Categoria&action=edit&id=' . $item['id'];
        $delete_link = '?page=FAQ_New_Categoria&action=delete&id=' . $item['id'];

        return sprintf(
            '<a href="%s">✏️ Editar</a> | <a href="%s" onclick="return confirm(\'¿Estás seguro?\')">❌ Eliminar</a>',
            esc_url($edit_link),
            esc_url($delete_link)
        );
    }

} 

//Muestra la tabla en la pagina con los datos que agregamos anteriormente
function faqer_categoria_page() {
    echo '<div class="wrap"><h1>Categorías</h1>';
    $categoria_table = new Categoria_List_Table();
    $categoria_table->prepare_items();
    $categoria_table->display();
    echo '</div>';
}


