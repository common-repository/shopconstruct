<?php

/**
 * Class Shop_CT_list_table_attribute_taxonomies
 * todo: arguments do not work
 */
class Shop_CT_list_table_attribute_taxonomies extends Shop_CT_list_table {

    /**
     * @return Shop_CT_Product_Attribute[]
     */
	public function get_all_items(){

        return Shop_CT_Product_Attribute::get_all();
	}

    /**
     * @return Shop_CT_Product_Attribute[]
     */
	public function get_items(){

		return Shop_CT_Product_Attribute::get_all();
	}

    /**
     * @param $post Shop_CT_Product_Attribute
     */
	public function column_cb( $post ){
		?>
		<input id="cb-select-<?php echo $post->get_id(); ?>" type="checkbox" name="comment[]" value="<?php echo $post->get_id(); ?>" title="cb" />
		<div class="locked-indicator"></div>
		<?php
	}

    /**
     * @param $post Shop_CT_Product_Attribute
     * @return string
     */
	public function column_name( $post ){
		$pad ="";
		$title = $post->get_name();
		
		echo '<strong>';
		$can_edit_post = true;
		if ( $can_edit_post ) {
			$edit_link = "#";
			echo '<a class="row-title" href="' . $edit_link . '" title="' . esc_attr( sprintf( __( 'Edit &#8220;%s&#8221;' ), $title ) ) . '">' . $pad . $title . '</a>';
		} else {
			echo $pad . $title;
		}
		echo '</strong>';
	}

    /**
     * @param $post Shop_CT_Product_Attribute
     */
	public function column_slug( $post ){
		echo $post->get_slug();
	}

    /**
     * @param $post Shop_CT_Product_Attribute
     */
	public function column_ordering( $post ){
		switch( $post->get_order_by() ){
			case "name":
				_e( 'Name', 'shop_ct' );
				break;
			case "name_num":
				_e( 'Name (numeric)', 'shop_ct' );
				break;
			case "term_id":
				_e( 'Term ID', 'shop_ct' );
				break;
			default:
				_e( 'Term ID', 'shop_ct' );
				break;
		}
	}

    /**
     * @param $post Shop_CT_Product_Attribute
     */
	public function column_attribute_values( $post ){

		foreach ( $post->get_terms() as $term ) {
            $out[] = $term->get_name();
        }

        echo !empty($out) ? join( __( ', ' ), $out ) : "&#8212;";
	}
}