<?php

/**
 * Class Shop_CT_List_Table_Products
 */
class Shop_CT_List_Table_Products extends Shop_CT_list_table {

    public function column_shop_ct_product_rating($post){
        $product = new Shop_CT_Product($post);
        $rating = $product->get_rating();
        echo '<span class="product_rating_'.$rating.'">'.$rating.'</span>';
    }

    public function column_sku($post){
        $product = new Shop_CT_Product($post);
        $sku = $product->get_sku();

        if(empty($sku)){
            $sku='-';
        }

        echo $sku;
    }

    public function _column_sku($post, $classes, $data, $primary){
        echo '<td class="' . $classes . ' page-title" ', $data, '>';
        $this->column_sku( $post );
        echo '</td>';
    }

    public function column_price($post){
        $product = new Shop_CT_Product($post->ID);

        if($product->is_on_sale()){
            echo '<del><span class="amount">'.Shop_CT_Formatting::format_price ($product->get_regular_price()).'</span></del>&nbsp;<ins><span class="amount">'.Shop_CT_Formatting::format_price ($product->get_price()).'</span></ins>
            ';
        }else{
            echo '<span class="amount">'.Shop_CT_Formatting::format_price ($product->get_price()).'</span>';
        }
    }

    public function _column_price($post, $classes, $data){
        echo '<td class="' . $classes . ' page-title" ', $data, '>';
        $this->column_price( $post );
        echo '</td>';
    }

    public function column_stock_status($post){
        $product = new Shop_CT_Product($post->ID);
        $availabilty = $product->get_availability();
        $class = $availabilty['class'];
        $text = $availabilty['availability'];

        echo '<span class="'. $class .'">'. $text .'</span>';
    }

    public function _column_stock_status($post, $classes, $data, $primary){
        echo '<td class="' . $classes . ' page-title" ', $data, '>';
        $this->column_stock_status( $post );
        echo '</td>';
    }

    public function column_shortcode( $post ) {
        $product = new Shop_CT_Product($post->ID);
        ?>
        <input type="text" class="shop_ct_shortcode_input" value='<?php echo method_exists($product, 'get_shortcode') ? $product->get_shortcode() : ""; ?>' readonly />
        <?php
    }
}