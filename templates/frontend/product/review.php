<?php
/**
 * @var $comment WP_Comment
 * @var $args array
 * @var $depth
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
$rating   = intval( get_comment_meta( $comment->comment_ID, 'rating', true ) );

?>
<li itemprop="review" itemscope itemtype="http://schema.org/Review" <?php comment_class("shop_ct_review"); ?> id="li-comment-<?php comment_ID() ?>">
	<article id="div-comment-<?php comment_ID(); ?>" class="comment-body">
		<footer class="comment-meta">
			<div class="comment-author vcard">
				<?php if ( 0 != $args['avatar_size'] ) echo get_avatar( $comment, $args['avatar_size'] ); ?>
				<?php printf( __( '%s <span class="says">says:</span>' ), sprintf( '<b class="fn">%s</b>', get_comment_author_link( $comment ) ) ); ?>
			</div><!-- .comment-author -->
			<div class="comment-metadata">
				<div itemprop="reviewRating" itemscope itemtype="http://schema.org/Rating" class="rating_stars" title="<?php echo sprintf( __( 'Rated %d out of 5', 'shop_ct' ), $rating ) ?>">
					<?php
					//todo: dzel taki get_optioni default@

					if (SHOP_CT()->product_settings->enable_review_rating === 'yes') {
						for ( $n = 5; $n > 0; $n-- ) {
							$class = $rating == $n ? "active" : "";
							if ( $rating > 0 ) {
								echo "<span class='rating_star ". $class ." fa fa-star'></span>";
							}
						}
					}
					?>
				</div>
				<a href="<?php echo esc_url( get_comment_link( $comment, $args ) ); ?>">
					<time datetime="<?php comment_time( 'c' ); ?>">
						<?php
							/* translators: 1: comment date, 2: comment time */
							printf( __( '%1$s at %2$s' ), get_comment_date( '', $comment ), get_comment_time() );
						?>
					</time>
				</a>
				<?php edit_comment_link( __( 'Edit' ), '<span class="edit-link">', '</span>' ); ?>
			</div><!-- .comment-metadata -->

			<?php if ( '0' == $comment->comment_approved ) : ?>
			<p class="comment-awaiting-moderation"><?php _e( 'Your comment is awaiting moderation.' ); ?></p>
			<?php endif; ?>
		</footer><!-- .comment-meta -->

		<div class="comment-content">
			<?php comment_text(); ?>
		</div><!-- .comment-content -->
		<div class="comment-reply"><?php _e('Reply', 'shop_ct'); ?></div>
	</article><!-- .comment-body -->