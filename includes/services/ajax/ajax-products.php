<?php
add_action('shop_ct_ajax_delete_product', 'shop_ct_ajax_delete_product_callback' );

/**
 * @return bool
 */
function shop_ct_ajax_delete_product_callback(){
	if(isset($_REQUEST['id'])){
		$id = $_REQUEST['id'];
		
		$force_delete = (isset($_REQUEST['force_delete']) ? $_REQUEST['force_delete'] : true);
		
		/* if we are completely deleting the product we need to delete the postmeta too */	
		
		if(is_array($id)){
			foreach($id as $i){
				if($force_delete){
					$post_meta = get_post_meta($i);
					if(is_array($post_meta) && !empty($post_meta)){
						foreach($post_meta as $meta_key => $meta_value){
							delete_post_meta($i,$meta_key);
						}
					}
				}
				wp_delete_post($i,$force_delete);
			}
			$deleted = true;
		}else{
			if($force_delete){
				$post_meta = get_post_meta($id);
				if(is_array($post_meta) && !empty($post_meta)){
					foreach($post_meta as $meta_key => $meta_value){
						delete_post_meta($id,$meta_key);
					}
				}
			}
			$deleted = wp_delete_post($id,$force_delete);
		}
		
		
		if($deleted != false){
			echo json_encode(array('success'=>1,'deleted_post'=>$deleted));
			die();
		}
		
		
	}else{
		return false;
	}
}

add_action('shop_ct_ajax_generate_product_permalink', 'shop_ct_ajax_generate_product_permalink_callback' );
function shop_ct_ajax_generate_product_permalink_callback() {
	$title = $_GET['title'];
	$id = $_GET['id'];

	$html = get_sample_permalink_html($id, $title, sanitize_title($title));

	echo json_encode(array(
		'html' => $html,
		'success' => $html ? 1 : 0,
	));

	wp_die();
}