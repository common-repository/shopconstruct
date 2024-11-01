<?php

class Shop_CT_Comment_Filter
{

    public function __construct()
    {
        add_action( 'preprocess_comment', array($this, 'preprocess_comment' ) );
        add_action( 'pre_comment_on_post', array($this, 'pre_comment_on_post' ) );
        add_action( 'comment_post', array($this, 'comment_post' ),10, 3 );
    }

    public function preprocess_comment($commentdata){
        if(isset($_REQUEST['shop_ct_product_review_required'])){
            $commentdata['comment_type'] = Shop_CT_Product_Review::COMMENT_TYPE;
        }
        return $commentdata;
    }

    public function pre_comment_on_post( $comment_id ){
        if( isset($_REQUEST['shop_ct_product_review_required']) && !isset($_REQUEST['comment_parent']))
            if(!isset( $_REQUEST['review_star'] ) && SHOP_CT()->product_settings->review_rating_required ==='yes')
                wp_die( sprintf(__( "Please Select the Rating %s","shop_ct" ),"</br><a href='javascript:history.back()'>« Back</a>" ));
    }

    public function comment_post($comment_id, $comment_approved, $commentdata){
        if(isset( $_REQUEST['review_star'] ) && !empty($_REQUEST['review_star'])){
            add_comment_meta( $comment_id, "rating", $_REQUEST['review_star'] , true );
            $product = new Shop_CT_Product($commentdata['comment_post_ID']);

            $product->calculate_rating();
        }
    }

    public static function show_single_review($comment, $args, $depth){
        \ShopCT\Core\TemplateLoader::get_template('frontend/product/review.php', compact( 'comment', 'args', 'depth' ));
    }

}
