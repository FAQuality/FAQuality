<?php
// Añadimos la clase wp_list_table de wordpress y pedimos que sea requerido ya que no es publico
// (se recoge de otro enlace dentro del mismo wordpress).

if (!class_exists('WP_List_Table')) {
    require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php'; //ABSPATH es ruta absoluta
}

include_once './categoria.act.php';

//Creamos la clase categoria_list_table que al extender de wp_list_table, cogemos lo que realiza la funcion
//wp_list_table y la personalizamos 
class Categoria_List_Table_F extends WP_List_Table {

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

    $prefijo = $wpdb->prefix . 'fqr_'; // Prefijo para todas las tablas
    $tabla_categoria = $prefijo . 'categoria';
    return $wpdb->get_results("SELECT id, categoria, descripcion FROM $tabla_categoria WHERE borrado = 0", ARRAY_A);
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
            'id' => 'ID',           
            'categoria'   => 'Categoría',
            'descripcion'  => 'Descripción',
            'shortcode' => 'Shortcode',
            'acciones' => 'Acciones'
        ];
    }

//Agregamos contenido a las columnas
//Generamos hueco para checkbox indicando que el valor de cada checbox es igual a su id
    function column_cb($item) {
        return sprintf('<input type="checkbox" name="registro[]" value="%s" />', $item['ID']);
    }

//Generamos hueco para nombre con enlace externo y le da el efecto cliqueable
    function column_categoria($item) {
        $edit_link = '?page=FAQ_Categoria&action=edit&id=' . $item['id'];
        return sprintf('<strong><a href="%s">%s</a></strong>', $edit_link, esc_html($item['categoria']));
    }

//Generamos hueco para descripcion     
    function column_descripcion($item) {
        return esc_html($item['descripcion']);  
    }  

    // Agrega botones de acción en la columna "Acciones"
    function column_acciones($item) {
        $edit_link = '?page=FAQ_Categoria&action=edit&id=' . $item['id'];
        $delete_link = '?page=FAQ_Categoria&action=delete&id=' . $item['id'];

        return sprintf( // Esta f no es mia, es del nombre de la funcion de php
            '<a href="%s">✏️ Editar</a> | <a href="%s" onclick="return confirm(\'¿Estás seguro?\')">❌ Eliminar</a>',
            esc_url($edit_link),
            esc_url($delete_link)
        );
    }

//Generamos hueco para la id
    function column_id($item) {
        return esc_html($item['id']);  
    }  

    function column_shortcode($item){
        return esc_html('[shortcode categoria="'. $item['categoria'] . '"]');
    }
} 

//Muestra la tabla en la pagina con los datos que agregamos anteriormente
function faqer_categoria_page() {
    function faqer_selection_categoria_page() {
        require_once 'categoria.act.php';
        require_once 'bbdd.actions.php';

        if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['id'])) {
            faqer_edit_categoria_page();

        } else if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
            dbMarkAsDeletedCategoria($_GET['id']);
        }
    }

    faqer_selection_categoria_page();

    echo '<div class="wrap"><h1>Categorías</h1>';
    $categoria_table = new Categoria_List_Table_F(); // FIXME: Externalizar o algo para que no se cree uno nuevo cada vez que se llama a la funcion
    $categoria_table->prepare_items();
    $categoria_table->display();
    echo '</div>';
}


