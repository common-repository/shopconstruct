<?php

add_action("shop_ct_ajax_review_popup", "shop_ct_ajax_review_popup_callback" );
function shop_ct_ajax_review_popup_callback() {
    $obj = new Shop_CT_popup_review();

    $obj->two_column = true;

    $obj->form_id = 'shop_ct_review_popup_form';

    $defaults = array();

    $comment_id = isset($_REQUEST['id']) ? $_REQUEST['id'] : 'reply';

    $parent_id =  isset($_REQUEST['parent_id']) ? $_REQUEST['parent_id'] : NULL;

    $is_reply = false;

    if( $comment_id !== 'reply' ) {

        $comment_status = wp_get_comment_status( $comment_id );

	    $review = new Shop_CT_Product_Review($comment_id);
	    $rating = $review->get_rating();

        $defaults['name'] = $review->get_author();
        $defaults['email'] = $review->get_author_email();

        $defaults['url'] = NULL !== $review->get_author_url() ? $review->get_author_url() : '';

        $defaults['content'] = $review->get_content();

        $defaults['rating'] = !empty( $rating ) ? $rating : 0;

        if ( $comment_status == 'unapproved' ) {
            $defaults['status'] = 'hold';
        } elseif ( $comment_status == 'approved' ) {
            $defaults['status'] = 'approve';
        } else {
            $defaults['status'] = $comment_status;
        }
    } else {
        $defaults['name'] = wp_get_current_user()->data->user_nicename;
        $defaults['email'] = wp_get_current_user()->data->user_email;
        $defaults['url'] = wp_get_current_user()->data->user_url;
        $defaults['content'] = '';
        $defaults['status'] = 'approve';
        $is_reply = true;
    }


    /**
     * Author information section.
     */
    $obj->sections['author_section'] = array(
        'title'     => __('Author', 'shop_ct'),
        'column'    => 'left',
        'priority'  => 2,
        'type'      => 'default',
    );

        $obj->controls['author_section_title'] = array(
            'type' => 'div',
            'section'   => 'author_section',
            'default' => $obj->get_title('Author'),
        );

        $obj->controls['author_name'] = array(
            'label'     => __('Name', 'shop_ct'),
            'type'      => 'text',
            'section'   => 'author_section',
            'default'   => $defaults['name'],
        );

        $obj->controls['author_email'] = array(
            'label'     => __('Email', 'shop_ct'),
            'type'      => 'email',
            'section'   => 'author_section',
            'default'   => $defaults['email'],
        );

        $obj->controls['author_url'] = array(
            'label'     => __('URL', 'shop_ct'),
            'type'      => 'url',
            'section'   => 'author_section',
            'default'   => $defaults['url'],
        );

	$obj->controls['content'] = array(
		'label' => __('Content', 'shop_ct'),
		'type' => 'textarea',
		'section' => 'author_section',
		'default' => $defaults['content'],
	);

    /**
     * Status section.
     */
    $obj->sections['status_section'] = array(
        'title'     => __('Status', 'shop_ct'),
        'column'    => 'right',
        'priority'  => 1,
        'type'      => 'default'
    );

        $obj->controls['status_section_title'] = array(
            'type'      => 'div',
            'section'   => 'status_section',
            'default'   => $obj->get_title('Status'),
        );

        $obj->controls['status_radio'] = array(
    //        'label'     => __('', 'shop_ct'),
            'type'      => 'radio',
            'section'   => 'status_section',
            'choices'   => $obj->get_status_section_choices(),
            'default'   => $defaults['status'],
        );

        $obj->controls['submitted_on'] = array(
            'section'   => 'status_section',
            'type'      => 'div',
            'default'   => $obj->get_submitted_on_html($comment_id)
        );

        $obj->controls['in_response_to'] = array(
            'section'   => 'status_section',
            'type'      => 'div',
            'default'   => $obj->get_in_response_to_html($comment_id, $parent_id)
        );

    /**
     * Trash and In Response To section.
     */
    $obj->sections['trash_update_button'] = array(
        'type'      => 'default',
        'column'    => 'right',
        'priority'  => 2,
    );

        $obj->controls['move_to_trash'] = array(
            'section'   => 'trash_update_button',
            'type'      => 'div',
            'default'   => $obj->get_move_to_trash_html($comment_id),
        );

        $obj->controls['update_button'] = array(
            'section'   => 'trash_update_button',
            'type'      => 'button',
            'label'     => __('Update', 'shop_ct'),
            'class'     => 'update_review_button',
        );

    if (!$is_reply) {

        $obj->sections['rating_section'] = array(
            'type' => 'default',
            'column' => 'right',
            'priority' => 3,
        );

            $obj->controls['rating_section_tit;e'] = array(
                'section' => 'rating_section',
                'type' => 'div',
                'default' => $obj->get_title('rating'),
            );

            $obj->controls['rating_dropdown'] = array(
                'section' => 'rating_section',
                'type' => 'select',
                'choices' => range(0, 5),
                'default' => $defaults['rating'],
             );
    }



    ob_start();

    $obj->display();

    $html = ob_get_clean();

    echo json_encode(array(
        'return_html' => $html,
        'success' => true,
    ));

    wp_die();
}

add_action('shop_ct_ajax_update_review', 'shop_ct_ajax_update_review_callback' );
function shop_ct_ajax_update_review_callback() {
	$review = new Shop_CT_Product_Review(absint($_POST['comment_id']));

    $form_data = $_POST['form_data'];
    $result = array();
    $rating = absint($form_data['rating']);

    if( !isset($_REQUEST['parent_id']) ) {
        $review
	        ->set_author($form_data['author_name'])
	        ->set_author_email($form_data['author_email'])
	        ->set_author_url($form_data['author_url'])
	        ->set_content($form_data['content'])
	        ->set_rating($rating)
            ->set_approved($form_data['status']);

        $review->save();

    } elseif ( isset($_REQUEST['parent_id']) ) {

	    $reply = new Shop_CT_Product_Review();
	    $reply
		    ->set_author(wp_get_current_user()->user_login)
		    ->set_author_email(wp_get_current_user()->user_email)
		    ->set_author_url(wp_get_current_user()->user_url)
		    ->set_content($form_data['content'])
		    ->set_parent($_REQUEST['parent_id'])
		    ->set_approved(1);

        $result['reply'] = $reply->save();
    }

    echo json_encode($result);
    wp_die();
}

add_action('shop_ct_ajax_change_review_status', 'shop_ct_ajax_change_review_status_callback' );
function shop_ct_ajax_change_review_status_callback() {

    $result = array();

    if( isset($_POST['id']) && !isset($_POST['ids']) ) {
        $id = $_POST['id'];
        $status = $_POST['status'];

        $result['status'] = $status !== 'trash' ? Shop_CT_Product_Review::change_status($id, $status) : Shop_CT_Product_Review::delete($id);

    } elseif ( !isset($_POST['id']) && isset($_POST['ids']) ) {
        $ids = $_POST['ids'];

        if( isset($_POST['status']) ) {
            $status = $_POST['status'];


            foreach($ids as $key => $id) {

                $result['comment' . $id] = Shop_CT_Product_Review::change_status($id, $status);
            }
        } else {
            foreach($ids as $key => $id) {

                $result['comment' . $id] = Shop_CT_Product_Review::delete($id);

            }
        }
    }



    echo json_encode($result);
    wp_die();
}