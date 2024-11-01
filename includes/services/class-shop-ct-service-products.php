<?php

class Shop_CT_Service_Products extends Shop_CT_Settings {

    /**
     * @return string
     */
    public function display(){
        $args = array(
            'labels' => array(
                'page_title' => 'Products',
                'add_new_item' => '<span class="fa fa-plus"></span>',
                'search' => __('Search Products','shop_ct')
            ),
            'ids' => array(
                'add_new_item' => 'shop_ct_add_product_btn',
            ),
            'form_id' => "shop_ct_products_list_table",
            'classes' => array(),
            'table_classes' => array('shop_ct_product_table','shop_ct_list_table'),
            'statuses' => array(
                'any' => __('All','shop_ct'),
                'publish' => __('Published','shop_ct'),
                'draft' => __('Draft','shop_ct'),
                'pending' => __('Pending Review','shop_ct'),
                'future' => __('Scheduled','shop_ct'),
                'private' => __('Private','shop_ct'),
                'trash' => __('Trash','shop_ct'),
            ),
            'show_statuses' => true,
            'show_search_box' => true,
            'bulk_actions' => array(
                'bulk_actions' => __('Bulk Actions','shop_ct'),
                'trash' => __('Move to trash','shop_ct'),
            ),
            'show_bulk_actions' => true,
            'show_filters' => true,
            'filters' => array('months','category'),
            'columns' => array(
                'cb'=> array (
                    'name'=>'',
                    'sortable'=>false
                ),
                /*'ID' => array(
                    'name' => 'ID',
                    'sortable' => true,
                ),*/
                'title' => array (
                    'name'=>__('Title','shop_ct'),
                    'sortable'=>true,
                    'primary'=>true,
                ),
                'featured_img' => array(
                    'name'=>'<i class="fa fa-picture-o"></i>',
                    'sortable'=>false
                ),
                /*'shop_ct_product_rating' => array (
                    'name'=>__('Rating','shop_ct'),
                    'sortable'=>true
                ),*/
                'sku' => array (
                    'name'=>__('SKU','shop_ct'),
                    'sortable'=>true
                ),
                'stock_status' => array (
                    'name'=>__('Stock','shop_ct'),
                    'sortable'=>false
                ),
                'price' => array (
                    'name'=>__('Price','shop_ct'),
                    'sortable'=>true
                ),
                /*'categories' => array (
                    'name'=>__('Categories','shop_ct'),
                    'sortable'=>false
                ),*/
                /*'tags' => array (
                    'name'=>__('Tags','shop_ct'),
                    'sortable'=>false
                ),*/
                /*'date' => array (
                    'name'=>__('Date','shop_ct'),
                    'sortable'=>true
                ),*/
                'shortcode' => array(
                    'name'     => __( 'Shortcode', 'shop_ct' ),
                    'sortable' => false,
                )
            ),
            'row_actions' => array(
                'edit' => 'Edit',
                'trash'=>'Trash',
                'view'=>'View',
            ),
            'items_object_type' => 'shop_ct_product',
            'items_resource_type' => 'post_type',
            'items_object_args' => array(),
            'pagination' => true,
            'per_page' => 10,
            'total_pages' => 1,
            'hierarchial' => false,


        );


        $table = new Shop_CT_list_table_Products( $args );

        ob_start();

        $table->display();

        $return = ob_get_clean();

        $return .= '<div class="shop_ct_product_page_overlay"></div>';

        return $return;
    }
}