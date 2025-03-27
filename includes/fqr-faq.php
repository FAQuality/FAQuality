<?php
// Añadimos la clase wp_list_table de wordpress y pedimos que sea requerido ya que no es publico
// (se coge de otro enlace dentro del mismo wordpress).
if (!class_exists('WP_List_Table')) {
    require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php'; //ABSPATH es ruta absoluta
}

include_once 'faq.act.php';

//Creamos la clase categoria_list_table que al extender de wp_list_table, cogemos lo que realiza la funcion
//wp_list_table y la personalizamos 
class FAQ_List_Table extends WP_List_Table
{

    //Creamos un constructor con la informacion principal (ajax desactivado por ahora)
    function __construct()
    {
        parent::__construct([
            'singular' => 'FAQ',
            'plural' => 'FAQs',
            'ajax' => true
        ]);
    }

    //Creamos nuestras columnas (indicamos el tipo de columna que queremos y despues le ponemos nombre)    
    function get_columns()
    {
        return [
            'cb' => '<input type="checkbox" />', // Checkbox para seleccionar filas
            'id' => 'ID',                        // Nueva columna para el ID                
            'pregunta' => 'Pregunta',                 // Pregunta
            'FK_idcat' => 'Categoria',                // Categoria de la pregunta
            'FK_idpadre' => 'ID Pregunta padre',         // ID de la pregunta padre
            'prioridad' => 'Prioridad',
            'acciones' => 'Acciones'
        ];
    }

    function get_total_items()
    {
        global $wpdb;
        $prefijo = $wpdb->prefix . 'fqr_';
        $tabla_faq = $prefijo . 'faq';
        return $wpdb->get_var("SELECT COUNT(*) FROM $tabla_faq WHERE borrado = 0");
    }

    //Obtiene los datos de la base de datos 
    function get_preguntas($per_page, $page_number)
    {
        global $wpdb;
        $prefijo = $wpdb->prefix . 'fqr_';
        $tabla_faq = $prefijo . 'faq';
        $offset = ($page_number - 1) * $per_page;

        return $wpdb->get_results(
            $wpdb->prepare(
                "SELECT id, pregunta, respuesta, prioridad, FK_idcat, FK_idpadre 
                 FROM $tabla_faq 
                 WHERE borrado = 0 
                 LIMIT %d OFFSET %d",
                $per_page,
                $offset
            ),
            ARRAY_A
        );
    }

    //Generamos hueco para checkbox indicando que el valor de cada checbox es igual a su id
    function column_cb($item)
    {
        return sprintf('<input type="checkbox" name="registro[]" value="%s" />', $item['id']);
    }

    //Generamos hueco para ID que al clickear el texto se pueda editar la pregunta
    function column_id($item)
    {
        return esc_html($item['id']);
    }

    //Generamos hueco para la nueva pregunta  
    function column_pregunta($item)
    {
        $edit_link = '?page=FAQ&action=edit&id=' . $item['id'];
        $pregunta = sprintf('<strong><a href="%s">%s</a></strong>', $edit_link, esc_html($item['pregunta']));
        return $pregunta;
    }

    function column_prioridad($item)
    {
        return sprintf(
            '<div class="editable-priority" data-id="%d"><a>%s</a></div>',
            $item['id'],
            esc_html($item['prioridad'])
        );
    }

    //Generamos hueco para categoria 
    function column_FK_idcat($item)
    {
        global $wpdb;
        $prefijo = $wpdb->prefix . 'fqr_';   // Asegúrate de que este prefijo es correcto      
        $tabla_categoria = $prefijo . 'categoria'; // Nombre de la tabla que contiene las preguntas
        $fkidcat = $item['FK_idcat']; // Obtenemos el valor de la ID de la pregunta

        // Hacemos la consulta para obtener la pregunta relacionada con esta ID
        $pregunta = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT categoria FROM $tabla_categoria WHERE id = %d",
                $fkidcat
            )
        );
        // Verificamos si hemos obtenido una pregunta
        if ($pregunta) {
            return esc_html($pregunta);  // Devolvemos la pregunta con seguridad (escapada para evitar XSS)
        } else {
            return 'Categoria no encontrada';  // En caso de que no haya una pregunta asociada
        }
    }

    function column_FK_idpadre($item)
    {
        global $wpdb;
        $prefijo = $wpdb->prefix . 'fqr_';  // Asegúrate de que este prefijo es correcto
        $tabla_faq = $prefijo . 'faq';  // Nombre de la tabla que contiene las preguntas
        $fkidfaq = $item['FK_idpadre'];  // Obtenemos el valor de la ID de la pregunta

        // Hacemos la consulta para obtener la pregunta relacionada con esta ID
        $pregunta = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT pregunta FROM $tabla_faq WHERE id = %d",
                $fkidfaq
            )
        );

        // Verificamos si hemos obtenido una pregunta
        if ($pregunta) {
            return esc_html($pregunta);  // Devolvemos la pregunta con seguridad (escapada para evitar XSS)
        } else {
            return 'Pregunta no encontrada';  // En caso de que no haya una pregunta asociada
        }

    }

    function column_acciones($item)
    {
        $edit_link = '?page=FAQ&action=edit&id=' . $item['id'];
        $delete_link = '?page=FAQ&action=delete&id=' . $item['id'];

        return sprintf( // Esta f no es mia, es del nombre de la funcion de php
            '<a href="%s">✏️ Editar</a> | <a href="%s" onclick="return confirm(\'¿Estás seguro?\')">❌ Eliminar</a>',
            esc_url($edit_link),
            esc_url($delete_link)
        );
    }

    //Cargamos datos en las columnas
    function prepare_items()
    {
        $per_page = 15;
        $current_page = $this->get_pagenum();
        $total_items = $this->get_total_items();

        $this->set_pagination_args([
            'total_items' => $total_items,
            'per_page' => $per_page,
        ]);

        $this->items = $this->get_preguntas($per_page, $current_page);

        $columns = $this->get_columns();
        $hidden = [];
        $sortable = [];
        $this->_column_headers = [$columns, $hidden, $sortable];
    }
}

//Muestra la tabla en la pagina con los datos que agregamos anteriormente
function FAQuality_FAQ_page()
{
    function FAQuality_selection_faq_page()
    {
        require_once 'faq.act.php';
        require_once 'bbdd.actions.php';

        if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['id'])) {
            FAQuality_edit_faq_page();

            if(isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['paged'])) {
                wp_redirect(admin_url('admin.php?page=FAQ&paged=2'));
            } 

        } else if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
            dbMarkAsDeletedFAQ($_GET['id']);
        }
    }

    FAQuality_selection_faq_page();

    echo '<div class="wrap"><div class="title-container"><h1 style="width: min-content;">FAQs</h1>';
    echo '<a class="button nuevo" href="?page=Nuevo_FAQ">Nuevo FAQ</a></div>';
    $table = new FAQ_List_Table();
    $table->prepare_items();
    $table->display();
    echo '</div>';
}
