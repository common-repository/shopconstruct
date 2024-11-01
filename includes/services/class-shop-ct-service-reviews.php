<?php

class Shop_CT_Service_Reviews extends Shop_CT_Settings {

    public function __construct(){
        $this->init();
    }

    public function init(){

    }

    public function display(){
        $table = new Shop_CT_list_table_reviews();

        $table->labels = array(
            'page_title' => 'Reviews',
//        'add_new_item' => __('Add Order','shop_ct'),
            'search'     => __( 'Search Review', 'shop_ct' ),
        );

        $table->ids = array(
            'add_new_item' => 'shop_ct_add_order_btn',
        );

        $table->classes = array();

        $table->items_resource_type = 'comment_type';

        $table->items_object_type = 'shop_ct_review';

        $table->items_object_args = array(
            'type'    => 'shop_ct_review',
            'orderby' => 'comment_date',
            'status'  => array( 'all', 'spam' ),
        );

        $table->table_classes = array( 'shop_ct_review_table', 'shop_ct_list_table' );

        $table->form_id = 'shop_ct_review_form';

        $table->show_search_box = true;

        $table->bulk_actions = array(
            'bulk_actions' => __( 'Bulk Actions', 'shop_ct' ),
            'approve'      => __( 'Approve', 'shop_ct' ),
            'hold'         => __( 'Unapprove', 'shop_ct' ),
            'spam'         => __( 'Spam', 'shop_ct' ),
            'trash'        => __( 'Delete', 'shop_ct' ),
        );

        $table->show_bulk_actions = true;

        $table->show_filters = false;

        $table->filters = array( 'months', 'category' );

        $table->columns = array();

        $table->columns['cb'] = array(
            'name'     => '',
            'sortable' => false,
        );

        $table->columns['author'] = array(
            'name'     => __( 'Author', 'shop_ct' ),
            'sortable' => true,
        );

        $table->columns['review'] = array(
            'name'     => __( 'Review', 'shop_ct' ),
            'sortable' => false,
            'primary'  => true,
        );

        $table->columns['in_response_to'] = array(
            'name'     => __( 'In Response To', 'shop_ct' ),
            'sortable' => true,
        );

        $table->columns['rating']       = array(
            'name'     => __( 'Rating', 'shop_ct' ),
            'sortable' => true,
        );
        $table->columns['submitted_on'] = array(
            'name'     => __( 'Submitted On', 'shop_ct' ),
            'sortable' => true,
        );

        $table->columns['table_actions'] = array(
            'name'     => __( 'Actions', 'shop_ct' ),
            'sortable' => false,
        );

        $table->columns['cb'] = array(
            'name'     => '',
            'sortable' => false,
        );

        $table->pagination = true;

        $table->per_page = 10;

        $table->total_pages = 1;

        $table->hierarchial = false;

        $table->row_actions = array(
            'approve'   => __( 'Approve', 'shop_ct' ),
            'unapprove' => __( 'Unapprove', 'shop_ct' ),
            'edit'      => __( 'Edit', 'shop_ct' ),
            'spam'      => __( 'Spam', 'shop_ct' ),
            'trash'     => __( 'Trash', 'shop_ct' ),
        );

        ob_start();

        $table->display();

        $return = ob_get_clean();

        return $return;
    }
}