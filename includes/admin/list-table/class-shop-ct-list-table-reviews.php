<?php

/**
 * Class Shop_CT_list_table_reviews
 */
class Shop_CT_list_table_reviews extends Shop_CT_list_table
{
    public function column_author ( $comment ) {
        /** @var Shop_CT_Product_Review $comment */
        echo $comment->get_author() ? $comment->get_author() . '<br /><a href="mailto:' . $comment->get_author_email() . '">' . $comment->get_author_email() . '</a>' : 'error';
    }

    public function column_review( $comment ) {
        /** @var Shop_CT_Product_Review $comment */

        if( $comment->get_content() ) {
            echo $this->get_in_reply_to($comment->get_id(), $comment->get_product_id());
            echo '<br /><a href="#" class="shop_ct_review_content">' . strip_tags($comment->get_content()) . '</a>';

        } else {
            echo 'oops';
        }
    }

    public function column_in_response_to( $comment ) {
        /** @var Shop_CT_Product_Review $comment */
        echo $this->get_post_title_by_id($comment->get_product_id()) ? $this->get_post_title_by_id($comment->get_product_id()) : 'oops';
    }

    public function column_rating( $comment ) {
        /** @var Shop_CT_Product_Review $comment */
//        echo $this->rating( $comment->get_product_id() );
        echo $this->get_single_rating($comment->get_id());
    }

    public function column_submitted_on( $comment ) {
        /** @var Shop_CT_Product_Review $comment */
        echo $comment->get_date() ? $comment->get_date() : 'error';
    }

    public function column_table_actions( $comment ) {
        /** @var Shop_CT_Product_Review $comment */

        if( $this->get_status($comment->get_id()) == 'approved' ) {
            the_tags('Tags: ', ',', '');
            echo '<i class="fa fa-spinner reviews_table_action" title="Hold" data-action="hold"></i>';
        } elseif ( $this->get_status($comment->get_id()) == 'spam' ) {
            echo '<i class="fa fa-shield reviews_table_action spam" title="Not Spam" data-action="hold"></i>';
        } else {
            echo '<i class="fa fa-check reviews_table_action" title="Approve" data-action="approve"></i>';
        }

        echo '<i class="fa fa-reply reviews_table_action" title="Reply" data-action="reply"></i>
            <i class="fa fa-pencil-square-o reviews_table_action" title="Edit/View" data-action="edit_view"></i>
            <i class="fa fa-trash reviews_table_action" title="Delete" data-action="trash"></i>';
    }

    public function page_nav() {
        $labels = $this->get_labels();
        $ids = $this->get_ids();
        $classes = $this->get_classes();
        ?>
        <h1><?php echo $labels['page_title']; ?></h1>
        <?php
        $this->views();
    }

    private function get_post_title_by_id($id) {
        $post = get_post($id);
        $text = '<a target="_blank" href="' . @get_post_permalink($id) . '">' . $post->post_title . '</a>';
        return $text;
    }

    private function get_status($id) {
         return wp_get_comment_status($id);
    }

    private function get_in_reply_to($comment_id, $comment_post_id) {
        $comment = new Shop_CT_Product_Review($comment_id);
        $text = '';

        if($comment->get_parent()) {
            $parent = new Shop_CT_Product_Review($comment->get_parent());
            $comment_author = $parent->get_author();
            $comment_author_url = $comment->get_author_url();
            $text = 'In reply to: <a href="' . get_comments_link($comment_post_id) . '">' . $comment_author . '</a>';
        }

        return $text;
    }

    private function rating( $comment_post_id ) {
        $reviews = get_comments( array('post_id' => $comment_post_id, 'status' => 'approve') );
        $ratings = array();

        foreach($reviews as $review) {
            if( isset( get_comment_meta($review->comment_ID)['rating'] ) ) {

                $single_rating = get_comment_meta($review->comment_ID)['rating'][0];

                array_push($ratings, $single_rating);
            }
        }

        $rating = array_sum($ratings) / count($ratings);


        return $rating;
    }

    private function get_single_rating( $comment_id ) {
        $rating = get_comment_meta( $comment_id)['rating'][0];

        return !empty($rating) ? $rating : '&#151;';
    }
}