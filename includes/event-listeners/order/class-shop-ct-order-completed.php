<?php

class Shop_CT_Order_Completed
{

    public function __construct()
    {
        add_action('shop_ct_order_completed',array($this,'add_download_permissions'));
    }

    public function add_download_permissions(Shop_CT_Order $order)
    {
        $result = false;
        $products = $order->get_products();
        if(!empty($products)):
            foreach($order->get_products() as $p){
                /** @var Shop_CT_Product $product */
                $product = $p['object'];
                if($product->is_downloadable()){
                    if(!$product->has_download_permission($order->get_id(),$order->get_billing_email())){
                        $product->add_download_permission($order->get_id(),$order->get_billing_email());
                        $product->save();
                        $result = true;
                    }
                }
            }
            if($result){
                do_action('shop_ct_added_download_permissions',array( 'order_data' => array('order' => $order)));
            }
        endif;
    }

}