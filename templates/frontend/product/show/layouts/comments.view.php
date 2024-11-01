<?php
/**
 * @var $product Shop_CT_Product
 */
if ( post_password_required() ) {
    return;
}

?>
<div id="comments" class="comments-area">
    <?php if ( wp_count_comments($product->get_id())->approved ) : ?>
        <h2 class="comments-title">
            <?php
            $comments_number = intval(wp_count_comments($product->get_id())->approved);
            if ( 1 === $comments_number ) {
                /* translators: %s: post title */
                printf( _x( 'One Review on &ldquo;%s&rdquo;', 'comments title', 'shop_ct' ), $product->get_post_data()->post_title );
            } else {
                printf(
                /* translators: 1: number of comments, 2: post title */
                    _x( '%1$s reviews on &ldquo;%2$s&rdquo;', $comments_number,	'comments title', 'shop_ct' ),
                    number_format_i18n( $comments_number ),
                    $product->get_post_data()->post_title
                );
            }
            ?>
        </h2>

        <ol class="comment-list">

            <?php
            //Gather comments for a specific page/post
            $comments = get_comments(array(
                'post_id' => $product->get_id(),
                'status' => 'approve' //Change this to the type of comments to be displayed
            ));

            //Display the list of comments
            wp_list_comments(array(
                'per_page' => 10, //Allow comment pagination
                'reverse_top_level' => false, //Show the latest comments at the top of the list
                'callback' => array( 'Shop_CT_Comment_Filter', 'show_single_review' )
            ), $comments);

            ?>
        </ol><!-- .comment-list -->


    <?php endif; // Check for have_comments(). ?>

    <?php
    // If comments are closed and there are comments, let's leave a little note, shall we?
    if ( ! comments_open() && get_comments_number() && post_type_supports( get_post_type(), 'comments' ) ) :
        ?>
        <p class="no-comments"><?php _e( 'Comments are closed.', 'shop_ct' ); ?></p>
    <?php endif; ?>

    <?php

    comment_form( array(
        'title_reply_before' => '<h2 id="reply-title" class="comment-reply-title">',
        'title_reply_after'  => '</h2>',
    ), $product->get_id() );
    ?>

</div><!-- .comments-area -->