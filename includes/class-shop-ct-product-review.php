<?php

class Shop_CT_Product_Review {
	/**
	 * @var int
	 */
	private $id;

	/**
	 * @var int
	 */
	private $product_id;

	/**
	 * @var string
	 */
	private $author = '';

	/**
	 * @var string
	 */
	private $author_email = '';

	/**
	 * @var string
	 */
	private $author_url = '';

	/**
	 * @var int
	 */
	private $approved = 1;

	/**
	 * @var int
	 */
	private $parent = 0;

	/**
	 * @var int
	 */
	private $rating = 0;

	/**
	 * @var string
	 */
	private $content = '';

	/**
	 * @var string $date
	 */
	private $date;

	/**
	 * @var WP_Comment
	 */
	public $comment;

	const COMMENT_TYPE = 'shop_ct_review';

	/**
	 * Shop_CT_Product_Review constructor.
	 *
	 * @param int|WP_Comment $id
	 */
	public function __construct( $id = NULL ) {
		if (NULL !== $id && (is_numeric($id) || $id instanceof WP_Comment)) {
			if (is_numeric($id)) {
				$id = absint( $id );

				$comment = get_comment( $id );
			} else {
				$comment = $id;
				$id = $comment->comment_ID;
				$this->comment = $comment;
			}

			if ( NULL !== $comment ) {
				$this->id           = $id;
				$this->product_id   = $comment->comment_post_ID;
				$this->author       = $comment->comment_author;
				$this->author_email = $comment->comment_author_email;
				$this->author_url   = $comment->comment_author_url;
				$this->content      = $comment->comment_content;
				$this->parent       = $comment->comment_parent;
				$this->approved     = $comment->comment_approved;
				$this->date         = $comment->comment_date;
				$this->comment      = $comment;


				if( isset(get_comment_meta($comment->comment_ID)['rating']) ){
                    $this->rating = (int) get_comment_meta($comment->comment_ID, 'rating', true);
                }
			}
		}
	}

	/**
	 * @return int
	 */
	public function get_id() {
		return $this->id;
	}

	/**
	 * @return int
	 */
	public function get_product_id() {
		return $this->product_id;
	}

	/**
	 * @param int $product_id
	 *
	 * @return Shop_CT_Product_Review
	 */
	public function set_product_id( $product_id ) {
		$this->product_id = absint($product_id);

		return $this;
	}

	/**
	 * @return string
	 */
	public function get_author() {
		return $this->author;
	}

	/**
	 * @param string $author
	 *
	 * @return Shop_CT_Product_Review
	 */
	public function set_author( $author ) {
		$this->author = sanitize_text_field($author);

		return $this;
	}

	/**
	 * @return string
	 */
	public function get_author_email() {
		return $this->author_email;
	}

	/**
	 * @param string $author_email
	 *
	 * @return Shop_CT_Product_Review
	 */
	public function set_author_email( $author_email ) {
		$this->author_email = sanitize_email($author_email);

		return $this;
	}

	/**
	 * @return string
	 */
	public function get_author_url() {
		return $this->author_url;
	}

	/**
	 * @param string $author_url
	 *
	 * @return Shop_CT_Product_Review
	 */
	public function set_author_url( $author_url ) {
		$this->author_url = esc_url($author_url);

		return $this;
	}

	/**
	 * @return int|string
	 */
	public function get_approved() {
		return $this->approved;
	}

	/**
	 * @param int|string $approved
	 *
	 * @return Shop_CT_Product_Review
	 */
	public function set_approved( $approved ) {
		if ( 'approve' === $approved || 1 === $approved || '1' === $approved ) {
			$this->approved = 1;
		} elseif (in_array($approved, ['hold', 'trash', 'spam'])) {
			$this->approved = $approved;
		} elseif ( 0 === $approved && '0' === $approved ) {
			$this->approved = $approved;
		}

		return $this;
	}

	/**
	 * @return int
	 */
	public function get_parent() {
		return $this->parent;
	}

	/**
	 * @param int $parent
	 *
	 * @return Shop_CT_Product_Review
	 */
	public function set_parent( $parent ) {
		$this->parent = absint($parent);

		return $this;
	}

	/**
	 * @return int
	 */
	public function get_rating() {
		return $this->rating;
	}

	/**
	 * @param int $rating
	 *
	 * @return Shop_CT_Product_Review
	 */
	public function set_rating( $rating ) {
		$rating = (int)$rating;

		if ($rating > 5) {
			$rating = 5;
		} elseif ($rating < 0) {
			$rating = 0;
		}

		$this->rating = $rating;

		return $this;
	}

	/**
	 * @return string
	 */
	public function get_date() {
		return $this->date;
	}

	/**
	 * @param string $date
	 *
	 * @return Shop_CT_Product_Review
	 */
	public function set_date( $date ) {
		$this->date = $date;

		return $this;
	}

	/**
	 * @return string
	 */
	public function get_content() {
		return $this->content;
	}

	/**
	 * @param string $content
	 *
	 * @return Shop_CT_Product_Review
	 */
	public function set_content( $content ) {
		$this->content = wp_kses_data($content);

		return $this;
	}

	/**
	 * @return WP_Comment
	 */
	public function get_comment() {
		return $this->comment;
	}

	private function can_be_saved() {
		return isset($this->product_id, $this->author, $this->author_email, $this->content);
	}

	public function save() {
		if ($this->parent && NULL === $this->product_id) {
			$this->product_id = self::get_product_id_by_parent_id($this->parent);
		}

		if (!$this->can_be_saved()) {
			return false;
		}

		$args = array(
			'comment_post_ID' => $this->product_id,
			'comment_author' => $this->author,
			'comment_author_email' => $this->author_email,
			'comment_author_url' => $this->author_url,
			'comment_approved' => $this->approved,
			'comment_content' => $this->content,
			'comment_date' => $this->date,
			'comment_parent' => $this->parent,
			'comment_type' => self::COMMENT_TYPE,
		);

		if (NULL === $this->id) {
			$result = wp_insert_comment($args);

			if ($result) {
				$this->id = $result;
			} else {
				return false;
			}
		} else {
			$args['comment_ID'] = $this->id;
			$result = wp_update_comment($args);
		}

		if ($result) {
			update_comment_meta($this->id, 'rating', $this->rating);
		}

		return $result;
	}

	private static function get_product_id_by_parent_id($parent_id) {
		$parent = get_comment($parent_id);

		return $parent->comment_post_ID;
	}

	public static function get_all() {
		$wp_comments = get_comments(['type' => self::COMMENT_TYPE]);
		$comments = array();

		foreach ( $wp_comments as $wp_comment ) {
			$comments[] = new self($wp_comment);
		}

		return $comments;
	}

	public static function delete($id) {
		return wp_delete_comment($id, true);
	}

	public static function change_status($id, $status) {
		$review = new self($id);

		return $review->set_approved($status)->save();
	}
}