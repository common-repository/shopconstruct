<?php

class Shop_CT_Service_Orders extends Shop_CT_Settings {


    public function __construct() {
        $this->init();
    }

    public function init(){

    }

    public function display(){
        $table = new Shop_CT_list_table_order();
        /* set labels for texts */

        $table->labels = array(
            'page_title'   => 'Orders',
            'add_new_item' => '<span class="fa fa-plus"></span>',
            'search'       => __( 'Search Orders', 'shop_ct' )
        );

        $table->ids = array(
            'add_new_item' => 'shop_ct_add_order_btn',
        );

        $table->classes = array();

        $table->table_classes = array( 'shop_ct_order_table', 'shop_ct_list_table' );

        $table->statuses = array(
            'shop_ct-any'        => __( 'All', 'shop_ct' ),
            'shop-ct-pending'    => __( 'Pending Payment', 'shop_ct' ),
            'shop-ct-processing' => __( 'Processing', 'shop_ct' ),
            'shop-ct-on-hold'    => __( 'On Hold', 'shop_ct' ),
            'shop-ct-completed'  => __( 'Completed', 'shop_ct' ),
            'shop-ct-cancelled'  => __( 'Cancelled', 'shop_ct' ),
            'shop-ct-refunded'   => __( 'Refunded', 'shop_ct' ),
            'shop-ct-failed'     => __( 'Failed', 'shop_ct' ),
        );

        $table->show_statuses = true;

        $table->form_id = 'shop_ct_order_form';

        /**
         * @todo
         */
        $table->show_search_box = false;

        $table->bulk_actions = array(
            'bulk_actions' => __( 'Bulk Actions', 'shop_ct' ),
            'complete'     => __( 'Mark complete', 'shop_ct' ),
            'processing'   => __( 'Mark processing', 'shop_ct' ),
            'on_hold'      => __( 'Mark on-hold', 'shop_ct' ),
            'delete'       => __( 'Delete', 'shop_ct' ),
        );

        $table->show_bulk_actions = true;

        $table->show_filters = false;

        $table->filters = array( 'months', 'category' );

        $table->columns = array();

        $table->columns['cb'] = array(
            'name'     => '',
            'sortable' => false
        );

        $table->columns['status'] = array(
            'name'     => __( 'Status', 'shop_ct' ),
            'sortable' => false,
        );

        $table->columns['title'] = array(
            'name'     => __( 'Order', 'shop_ct' ),
            'sortable' => true,
        );

        $table->columns['purchased'] = array(
            'name'     => __( 'Purchased', 'shop_ct' ),
            'sortable' => true,
        );

        $table->columns['ship_to'] = array(
            'name'     => __( 'Ship To', 'shop_ct' ),
            'sortable' => false,
        );

        $table->columns['date'] = array(
            'name'     => __( 'Date', 'shop_ct' ),
            'sortable' => true
        );

        $table->columns['total'] = array(
            'name'     => __( 'Total', 'shop_ct' ),
            'sortable' => true,
        );

        $table->columns['actions'] = array(
            'name'     => __( 'Actions', 'shop_ct' ),
            'sortable' => false,
        );

        $table->row_actions = array(
            'edit'  => 'Edit',
            'trash' => 'Trash',
            'view'  => 'View',
        );

        $table->items_object_type = 'shop_ct_order';

        $table->items_resource_type = 'post_type';


        $table->items_object_args = array();

        $table->pagination = true;

        $table->per_page = 10;

        $table->total_pages = 1;

        $table->hierarchial = false;

        ob_start();

        $table->display();

        $return = ob_get_clean();

        return $return;
    }
}