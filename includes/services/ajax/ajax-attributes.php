<?php

add_action('shop_ct_ajax_attributes_popup', 'shop_ct_ajax_attributes_popup_callback');
function shop_ct_ajax_attributes_popup_callback()
{
    $submit_btn_value = isset($_REQUEST['submit_btn_value']) ? $_REQUEST['submit_btn_value'] : __("Save Attribute", "shop_ct");

    if (isset($_REQUEST['id']) && $_REQUEST['id'] !== 'new') {
        $popup_action = "edit";

        global $wpdb;
        $attr_id = absint($_REQUEST['id']);

        $attribute = new Shop_CT_Product_Attribute($attr_id);

        $defaults['name'] = $attribute->get_name();
        $defaults['slug'] = $attribute->get_slug();
        $defaults['type'] = $attribute->get_type();
        $defaults['order_by'] = $attribute->get_order_by();
        $defaults['public'] = $attribute->get_public();

        $defaults['terms'] = $attribute->get_terms();

    } else {
        $popup_action = "add";
    }

    $popup = new Shop_CT_popup_attribute_taxonomies();

    $popup->two_column = false;

    $popup->form_id = 'shop_ct_popup_attributes_form';

    $popup->sections['error_section'] = array(
        'priority' => 1,
        'type' => 'default',
        'class' => 'invisible'
    );

    $popup->controls['errors'] = array(
        'section' => 'error_section',
        'type' => 'div',
        'class' => 'error'
    );

    $popup->sections['control_section'] = array(
        'priority' => 2,
    );

    $popup->controls['name'] = array(
        'label' => __('Name', 'shop_ct'),
        'type' => 'text',
        'default' => $defaults['name'],
        'section' => 'control_section',
        'description' => __('Name for the attribute (shown on the front-end).', 'shop_ct'),
    );

    $popup->controls['slug'] = array(
        'label' => __('Slug', 'shop_ct'),
        'type' => 'text',
        'default' => $defaults['slug'],
        'section' => 'control_section',
        'description' => __('Unique slug/reference for the attribute; must be shorter than 28 characters.', 'shop_ct'),
    );

    /*$popup->controls['type'] = array(
        'label' => __('Type', 'shop_ct'),
        'type' => 'select',
        'default' => $defaults['type'],
        'section' => 'control_section',
        'choices' => array(
            'select' => __('Select', 'shop_ct'),
            'text' => __('Text', 'shop_ct'),
        ),
        'description' => __('Determines how you select attributes for products. Text allows manual entry whereas select allows pre-configured terms in a drop-down list.', 'shop_ct'),
    );*/

    /* $popup->controls['ordering'] = array(
         'label' => __('Default sort order', 'shop_ct'),
         'type' => 'select',
         'default' => $defaults['order_by'],
         'section' => 'control_section',
         'choices' => array(
             'name' => __('Name', 'shop_ct'),
             'name_num' => __('Name (numeric)', 'shop_ct'),
             'term_id' => __('Term ID', 'shop_ct')
         ),
         'description' => __('Determines the sort order of the terms on the frontend shop product pages', 'shop_ct')
     );*/

    $popup->controls['attribute_values'] = array(
        'label' => __('Attribute Terms', 'shop_ct'),
        'type' => 'attribute_values',
        'section' => 'control_section',
        'default' => $defaults['terms']
    );

    $popup->controls['id'] = array(
        'default' => $_REQUEST['id'],
        'type' => 'hidden',
        'section' => 'control_section',
    );

    $popup->controls['hidden_action'] = array(
        'default' => $popup_action,
        'type' => 'hidden',
        'section' => 'control_section',
    );

    $popup->controls['submit_attribute'] = array(
        'label' => $submit_btn_value,
        'type' => 'submit',
        'section' => 'control_section',
    );

    if (is_numeric($_REQUEST['id'])) {
        $popup->controls['name']['attrs'] = array(
            'readonly' => 'readonly'
        );

        $popup->controls['slug']['attrs'] = array(
            'readonly' => 'readonly'
        );
    }

    ob_start();
    $popup->display();
    $return = ob_get_clean();

    echo json_encode(array('success' => 1, 'return_html' => $return));
    die();
}

add_action('shop_ct_ajax_add_new_attribute_term', 'shop_ct_ajax_add_new_attribute_term_callback');
function shop_ct_ajax_add_new_attribute_term_callback()
{
    $term = new Shop_CT_popup_attribute_taxonomies();

    echo json_encode(array('html' => $term->add_row()));

    wp_die();
}

add_action('shop_ct_ajax_save_attribute', 'shop_ct_ajax_save_attribute_callback');
function shop_ct_ajax_save_attribute_callback()
{

    if ($_REQUEST['id'] === 'new') {
        $attribute = new Shop_CT_Product_Attribute();
    } elseif (is_numeric($_REQUEST['id']) && $_REQUEST['id'] > 0) {
        $id = absint($_REQUEST['id']);

        $attribute = new Shop_CT_Product_Attribute($id);
    } else {
        return false;
    }

    $attribute_data = $_POST['data']['attribute'];
    
    if (!isset($attribute_data['slug']) || empty($attribute_data['slug'])) {
        $attribute_data['slug'] = sanitize_title($attribute_data['name']);
    }

    /**
     * TODO: add support for 'select' type
     */
    $attribute
        ->set_name($attribute_data['name'])
        ->set_slug($attribute_data['slug'])
        ->set_type('text')
        ->set_order_by('term_id');
    $attribute->save();
    Shop_CT_Product_Attribute::register();

    if (isset($_POST['data']['terms'])) {
        $terms = $_POST['data']['terms'];

        foreach ($terms as $term_data) {
            $term = isset($term_data['id']) && is_numeric($term_data['id']) ? new Shop_CT_Product_Attribute_Term($term_data['id']) : new Shop_CT_Product_Attribute_Term(NULL, $attribute->get_slug());
            $term
                ->set_name($term_data['name'])
                ->set_slug(sanitize_title($term_data['name']));

            $attribute->add_term($term);
        }
    }

    $result['result'] = $attribute->save();
    $result['success'] = $result['result'] !== false;

    echo json_encode($result);
    wp_die();
}

add_action('shop_ct_ajax_delete_attribute', 'shop_ct_ajax_delete_attribute_callback');
function shop_ct_ajax_delete_attribute_callback()
{
    if (isset($_POST['id']) && !isset($_POST['ids'])) {
        $r['result'] = Shop_CT_Product_Attribute::delete($_POST['id']);
    } elseif (!isset($_POST['id']) && isset($_POST['ids'])) {
        foreach ($_POST['ids'] as $id) {
            $r['result'][$id] = Shop_CT_Product_Attribute::delete($id);
        }
    }

    echo json_encode($r);
    wp_die();
}

add_action('wp_ajax_shop_ct_get_product_attribute_item', 'shop_ct_get_product_attribute_item_callback');
function shop_ct_get_product_attribute_item_callback()
{
    if(!isset($_GET['nonce']) || !wp_verify_nonce($_GET['nonce'],'shop_ct_nonce')){
        wp_die('are_you sure you want to do this?');
    }

    $product = new Shop_CT_Product($_GET['product_id']);
    $attribute = $_GET['attribute_taxonomy'] === 'custom' ? null : new Shop_CT_Product_Attribute( $_GET['attribute_taxonomy']);


    \ShopCT\Core\TemplateLoader::get_template('admin/products/popup-attribute-row.view.php', compact('product', 'attribute'));
    die;
}
