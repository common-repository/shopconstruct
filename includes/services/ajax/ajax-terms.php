<?php

add_action('shop_ct_save_product_category', 'shop_ct_save_product_category_callback');
function shop_ct_save_product_category_callback($category_data) {
	$category = $category_data['hidden_action'] !== 'add' && isset($category_data['id']) ? new Shop_CT_Product_Category($category_data['id']) : new Shop_CT_Product_Category();

	foreach ( $category_data as $property => $value ) {
		$method_name = 'set_' . $property;

		if (method_exists($category, $method_name)) {
			call_user_func(array($category, $method_name), $value);
		}
	}

	$GLOBALS['shop_ct_save_product_category_result'] = $category->save();
}

add_action('shop_ct_save_product_tag', 'shop_ct_save_product_tag_callback');
function shop_ct_save_product_tag_callback($tag_data) {
	$tag = $tag_data['hidden_action'] !== 'add' && isset($tag_data['id']) ? new Shop_CT_Product_Tag($tag_data['id']) : new Shop_CT_Product_Tag();

	foreach ( $tag_data as $property => $value ) {
		$method_name = 'set_' . $property;

		if (method_exists($tag, $method_name)) {
			call_user_func(array($tag, $method_name), $value);
		}
	}

	$GLOBALS['shop_ct_save_product_tag_result'] = $tag->save();
}

add_action("shop_ct_ajax_popup_save", "shop_ct_ajax_popup_save_callback" );
function shop_ct_ajax_popup_save_callback() {

	$shop_ct_taxonomies = array(
		Shop_CT_Product_Category::get_taxonomy(),
		Shop_CT_Product_Tag::get_taxonomy(),
	);

	if ( isset( $_POST['taxonomy'] ) ) {
		$taxonomy = $_POST['taxonomy'];
	} else {
		$taxonomy = "";
	}

	$unprefixed_taxonomy = str_replace( 'shop_ct_', '', $taxonomy );

	if (in_array($taxonomy, $shop_ct_taxonomies)) {
		$action_name = strpos( $taxonomy, 'shop_ct_' ) === 0 ? 'shop_ct_save_' . $unprefixed_taxonomy : 'shop_ct_save_' . $taxonomy;

		do_action($action_name, $_POST['term_args']);

		$key = 'shop_ct_save_' . $unprefixed_taxonomy . '_result';
		$result = $GLOBALS[$key];

		unset($GLOBALS[$key]);

		$success = (int)(NULL !== $result && false !== $result);

		echo json_encode(array('success' => $success, 'data' => $result));
		wp_die();
	}

	$term_args = wp_parse_args( $_POST['term_args'], array(
		"name"        => "",
		"description" => "",
		"slug"        => "",
		"parent"      => "",
	) );


	if ( isset( $term_args['hidden_action'] ) ) {
		$hidden_action = $term_args['hidden_action'];
	} else {
		$hidden_action = "";
	}

	if ( $hidden_action == "add" ) {
		$a = wp_insert_term( $term_args['name'], $taxonomy, $term_args );
		if ( ! is_wp_error( $a ) ) {
			$new_id = $a['term_id'];
			if ( isset( $term_args['thumbnail'] ) ) {
				add_term_meta( $new_id, "thumbnail_id", $term_args['thumbnail'] );
			};
		}

	} else {
		$a = wp_update_term( $term_args['id'], $taxonomy, $term_args );
		if ( isset( $term_args['thumbnail'] ) ) {
			update_term_meta( $term_args['id'], "thumbnail_id", $term_args['thumbnail'] );
		};
	};
	if ( ! is_wp_error( $a ) ) {
		echo json_encode( array( 'success' => 1 ) );
		die();
	} else {
		echo json_encode( array( 'error' => $a->get_error_message() ) );
		die();
	}

}



add_action("shop_ct_ajax_terms_popup", "shop_ct_ajax_terms_popup_callback" );

function shop_ct_ajax_terms_popup_callback(){
	$name = "";
	$slug = "";
	$parent = "";
	$description = "";
	$id = "";

	if(isset($_REQUEST['taxonomy'])){
		$taxonomy = $_REQUEST['taxonomy'];
	};
	if(isset($_REQUEST['submit_btn_value'])){
		$submit_btn_value = $_REQUEST['submit_btn_value'];
	};
	if(isset($_REQUEST['id']) && $_REQUEST['id']!= 'new'){
		if(is_numeric($_REQUEST['id']) && $_REQUEST['id']>0){
			$id = $_REQUEST['id'];
			$popup_action = "edit";
			if (Shop_CT_Product_Category::get_taxonomy() === $taxonomy) {
				$category = new Shop_CT_Product_Category($id);

				$name = $category->get_name();
				$slug = $category->get_slug();
				$parent = $category->get_parent();
				$description = $category->get_description();
				$thumbnail = $category->get_thumbnail_id();
			} elseif (Shop_CT_Product_Tag::get_taxonomy() === $taxonomy) {
				$tag = new Shop_CT_Product_Tag($id);

				$name = $tag->get_name();
				$slug = $tag->get_slug();
				$parent = $tag->get_parent();
				$description = $tag->get_description();
			} else {
				$term = get_term($id);

				$name = $term->name;
				$slug = $term->slug;
				$parent = $term->parent;
				$description = $term->description;
				$thumbnail = get_term_meta( $id, "shop_ct_thumbnail_id", true );
			}

		}
	}else {
		$popup_action = "add";
	}

	switch ( $taxonomy ) {
		default :
			$obj = new Shop_CT_popup();
	}

	$obj->two_column = false;

	$obj->sections['control_section'] = array(
		'priority'=>1,
	);

	$obj->controls['name'] = array(
		'label'=>__('Name','shop_ct'),
		'type'=>'text',
		'default'=>$name,
		'section'=>'control_section',
		'description'	=> __( 'The name is how it appears on your site.', 'shop_ct' ),
	);

	$obj->controls['slug'] = array(
		'label'=>__('Slug','shop_ct'),
		'type'=>'text',
		'default'=>$slug,
		'section'=>'control_section',
		'description' => __( 'The “slug” is the URL-friendly version of the name. It is usually all lowercase and contains only letters, numbers, and hyphens.', 'shop_ct' ),
	);

	if($taxonomy == "shop_ct_product_category"){
		$obj->controls['parent'] = array(
			'label'=>__('Parent','shop_ct'),
			'type'=>'taxonomy_dropdown',
			'search'=>true,
			'taxonomy'=> $taxonomy,
			'default'=> $parent,
			'section'=>'control_section',
			'exclude'=>array($id),
		);
	};

	$obj->controls['description'] = array(
		'label'=>__('Description','shop_ct'),
		'type'=>'textarea',
		'default'=>$description,
		'section'=>'control_section',
		'description'	=> __( 'The description is not prominent by default; however, some themes may show it.', 'shop_ct' ),
	);

	if (Shop_CT_Product_Tag::get_taxonomy() !== $taxonomy) {
		$obj->controls['thumbnail_id'] = array(
			'label'=>__('Featured Image','shop_ct'),
			'type'=>'image',
			'section'=>'control_section',
			'add_new_text' => __('Set featured image','shop_ct'),
			'remove_text' => __('Remove featured image','shop_ct'),
			'default'		=> intval( $thumbnail ),
		);
	}

	$obj->controls['id'] = array(
		'default' => $id,
		'type'=>'hidden',
		'section'=>'control_section',
	);

	$obj->controls['hidden_action'] = array(
		'default' => $popup_action,
		'type'=>'hidden',
		'section'=>'control_section',
	);

	switch ( $taxonomy ) {
		case "shop_ct_product_category":
			$obj->controls['submit_category'] = array(
				'label'   => $submit_btn_value,
				'type'    => 'submit',
				'section' => 'control_section',
			);
			break;
		case "shop_ct_product_tag" :
			$obj->controls['submit_tag'] = array(
				'label'   => $submit_btn_value,
				'type'    => 'submit',
				'section' => 'control_section',
			);

			$obj->controls['taxonomy'] = array(
				'type' => 'hidden',
				'section' => 'control_section',
				'default' => Shop_CT_Product_Tag::get_taxonomy(),
			);
			break;
		default:
			$obj->controls['submit_term'] = array(
				'label'   => $submit_btn_value,
				'type'    => 'submit',
				'section' => 'control_section',
			);
			break;
	}

	ob_start();
	$obj->display();
	$return = ob_get_clean();

	echo json_encode(array('success'=>1,'return_html'=>$return));
	die();
}



add_action("shop_ct_ajax_delete_term", "shop_ct_ajax_delete_term_callback" );

function shop_ct_ajax_delete_term_callback(){

	if(!isset($_GET['id']) || !isset($_GET['taxonomy'])){

        die(0);
	}

    $id = $_GET['id'];
    $taxonomy = $_GET['taxonomy'];

	if(is_array($id)){
		foreach($id as $key=>$single_id){
            wp_delete_term($single_id,$taxonomy);
		}
		echo json_encode(array("success"=>1));
		die;
	}else{
		if (wp_delete_term($id,$taxonomy)){
			echo json_encode(array("success"=>1));
			die;
		}else{
			echo json_encode(array("error"=>"Error while deleting term:"));
			die;
		}
	}
}

add_action('shop_ct_ajax_add_new_attribute', 'shop_ct_ajax_add_new_attribute_callback' );
function shop_ct_ajax_add_new_attribute_callback() {
	$title = $_POST['title'];
	$parent = intval($_POST['parent']);
	$post_id = $_POST['post_id'];

	$terms = get_the_terms( $post_id, 'shop_ct_product_category' );
	$term_ids = array();

	if ( is_array( $terms ) ) {
		foreach ( $terms as $term ) {
			$term_ids[] = $term->term_id;
		}
	}
	$new_category = new Shop_CT_Product_Category();
	$new_category
		->set_name($title)
		->set_parent($parent);

	$result = $new_category->save();

	$html = wp_terms_checklist( $post_id, array(
		'taxonomy'             => 'shop_ct_product_category',
		'descendants_and_self' => false,
		'popular_cats'         => true,
		'walker'               => false,
		'checked_ontop'        => 1,
		'selected_cats'        => $term_ids,
		'echo'                 => false,
	) );

	echo json_encode(array(
		'success' => (int)(false !== $result),
		'result' => $result,
		'html' => $html
	));

	wp_die();
}