<?php

class Shop_CT_list_table_order extends Shop_CT_list_table
{

    protected function column_ship_to( $post )
    {
        if (Shop_CT()->order_meta->get($post->ID, 'shipping_address_1') || Shop_CT()->order_meta->get($post->ID, 'shipping_address_2')) {
            $ship_to = implode(', ', [Shop_CT()->order_meta->get($post->ID, 'shipping_address_1'), Shop_CT()->order_meta->get($post->ID, 'shipping_address_2')]);
        } else {
            $ship_to = '&#8212;';
        }

        echo $ship_to;
    }

    protected function column_status( $post )
    {
        $status = get_post_status( $post->ID );
        $all_statuses = get_post_stati( array() , "objects");
        $status = $all_statuses[$status]->label;


        if( !$status || $status == "" ) {
            $status = '&#8212;';
        }

        echo $status;
    }

    protected function column_purchased( $post )
    {
        $purchased = Shop_CT_Order::get_products_count($post->ID);


        if($purchased < 2) {
            $purchased_orders_quantity = '<a href="#">' . $purchased . ' Item</a>';
        } else {
            $purchased_orders_quantity = '<a href="#">' . $purchased . ' Items</a>';
        }

        echo $purchased_orders_quantity;
    }

    public function column_date( $post )
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'posts';
        $sql = "SELECT post_date FROM " . $table_name . " WHERE ID = '" . $post->ID . "'";
        $date_time = $wpdb->get_var($sql);
        $date = substr($date_time, 0, 10);

        echo $date;
    }

    protected function column_total( $post )
    {
        $order = new Shop_CT_Order($post->ID);
    	$total = $order->get_total() ?: 0;

        echo Shop_CT_Formatting::format_price($total);
    }

    protected function column_actions( $post ) {
        $status = $post->post_status;

        if ($status == 'shop-ct-pending' || $status == 'shop-ct-on-hold' || $status == 'shop-ct-processing') {
            echo '<i class="fa fa-check shop-ct-order-actions" data-status="shop-ct-completed" title="Mark complete"></i>';
        }

        if ($status == 'shop-ct-pending' || $status == 'shop-ct-on-hold') {
            echo '<i class="fa fa-ellipsis-h shop-ct-order-actions" data-status="shop-ct-processing" title="Mark processing"></i>';
        }

        echo '<i class="fa fa-pencil-square-o edit-view-order" title="Edit/View"></i>';
    }
}