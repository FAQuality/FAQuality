<?php
if (!class_exists('WP_List_Table')) {
    require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

class Categoria_List_Table extends WP_List_Table {
    function __construct() {
        parent::__construct([
            'singular' => 'categoria',
            'plural'   => 'categorias',
            'ajax'     => false
        ]);
    }

    function get_columns() {
        return [
            'cb'      => '<input type="checkbox" />',
            'title'   => 'Título',
            'author'  => 'Autor',
            'date'    => 'Fecha'
        ];
    }

    function column_cb($item) {
        return sprintf('<input type="checkbox" name="registro[]" value="%s" />', $item['ID']);
    }

    function column_title($item) {
        $edit_link = '?page=FAQ_Categoria&action=edit&id=' . $item['ID'];
        return sprintf('<strong><a href="%s">%s</a></strong>', $edit_link, esc_html($item['title']));
    }

    function prepare_items() {
        $columns = $this->get_columns();
        $hidden  = [];
        $sortable = [];
        $this->_column_headers = [$columns, $hidden, $sortable];

        // Datos de ejemplo
        $this->items = [
            ['ID' => 1, 'title' => 'Categoría 1', 'author' => 'admin', 'date' => '2025-02-19'],
            ['ID' => 2, 'title' => 'Categoría 2', 'author' => 'admin', 'date' => '2025-02-18']
        ];
    }
}

function faqer_categoria_page() {
    echo '<div class="wrap"><h1>Lista de Categorías</h1>';
    $table = new Categoria_List_Table();
    $table->prepare_items();
    $table->display();
    echo '</div>';
}
