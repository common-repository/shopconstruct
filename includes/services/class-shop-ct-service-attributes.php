<?php

class Shop_CT_Service_Attributes extends Shop_CT_Settings {

    public function __construct(){
        $this->init();
    }

    public function init(){

    }

    public function display(){
        ob_start();
        $table = new Shop_CT_list_table_attribute_taxonomies();

        $table->labels = array(
            'page_title' => __('Attributes', 'shop_ct'),
            'add_new_item' => '<span class="fa fa-plus"></span>',
            'search' => __('Search Attribute','shop_ct')
        );

        $table->ids = array(
            'add_new_item' => 'shop_ct_add_attr_btn',
        );

        $table->classes = array();

        $table->items_resource_type = 'custom';

        $table->items_object_type = 'shop_ct_attribute_taxonomy';

        $table->table_classes = array('shop_ct_attr_table', 'shop_ct_list_table');

        $table->form_id = 'shop_ct_attribute_form';

        $table->show_search_box = true;

        $table->bulk_actions = array(
            'bulk_actions'  => __('Bulk Actions','shop_ct'),
            'delete'        => __('Delete', 'shop_ct'),
        );

        $table->show_bulk_actions = true;

        $table->show_filters = false;

        $table->filters = array('months','category');

        $table->columns = array();

        $table->columns['cb'] = array (
            'name'     => '',
            'sortable' =>false
        );

        $table->columns['name'] = array (
            'name'     => __('Name', 'shop_ct'),
            'sortable' => false,
            'primary'  =>true,
        );

        $table->columns['slug'] = array (
            'name'     => __('Slug', 'shop_ct'),
            'sortable' => false,
        );

        $table->columns['ordering'] = array (
            'name'     => __('Order by', 'shop_ct'),
            'sortable' => false
        );

        $table->columns['attribute_values'] = array (
            'name'     => __('Terms', 'shop_ct'),
            'sortable' => false
        );

        $table->row_actios = array(
            'edit'   => __('Edit', 'shop_ct' ),
            'delete' => __( 'Delete', 'shop_ct' )
        );

        $table->pagination = true;

        $table->per_page = 10;

        $table->total_pages = 1;

        $table->hierarchial = false;

        $table->row_actions = array(
            'edit'   => __('Edit', 'shop_ct'),
            'delete' => __('Delete', 'shop_ct'),
        );

        $table->display();

        return ob_get_clean();
    }
}