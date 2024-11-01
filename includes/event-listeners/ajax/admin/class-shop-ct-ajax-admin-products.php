<?php

class Shop_CT_Ajax_Admin_Products
{
    public function __construct()
    {
        add_action("shop_ct_ajax_product_popup", array($this, 'popup'));
        add_action('shop_ct_ajax_untrash_product', array($this, 'untrash'));
        add_action('shop_ct_ajax_trash_product', array($this, 'trash'));
        add_action('wp_ajax_shop_ct_update_product', array($this, 'update_product'));
        add_action('wp_ajax_shop_ct_get_product_cat_checklist', array($this, 'get_product_cat_checklist'));
    }

    public function get_product_cat_checklist()
    {
        $term_ids = array();
        $post_id = 0;
        if (isset($_REQUEST['product_id']) && !empty($_REQUEST['product_id']) && is_numeric($_REQUEST['product_id'])) {
            $post_id = $_REQUEST['product_id'];
            $terms = get_the_terms($post_id, Shop_CT_Product_Category::get_taxonomy());

            if (is_array($terms)) {
                foreach ($terms as $term) {
                    $term_ids[] = $term->term_id;
                }
            }
        }
        wp_terms_checklist($post_id, array(
            'taxonomy' => Shop_CT_Product_Category::get_taxonomy(),
            'descendants_and_self' => false,
            'popular_cats' => true,
            'walker' => false,
            'checked_ontop' => 1,
            'selected_cats' => $term_ids,
        ));
        die;
    }

    /**
     * Update product info
     *
     * @throws Exception
     */
    public function update_product()
    {
        if (isset($_REQUEST['product_id'])) $id = $_REQUEST['product_id'];
        elseif (isset($_REQUEST['post_data']['ID'])) $id = $_REQUEST['post_data']['ID'];
        else return;

        if (absint($id) != $id) {
            throw new Exception('Invalid value passed for "ID" field');
        }

        $id = absint($id);

        $product = new Shop_CT_Product($id);
        $post_data = $product->get_post_data();

        if (isset($_REQUEST['post_data'])) {
            foreach ($_REQUEST['post_data'] as $key => $value) {
                $post_data->$key = $value;
            }
        }

        if (isset($_REQUEST['post_data']['post_date']))
            $post_data->post_date = date("Y-m-d H:i:s", strtotime($_REQUEST['post_data']['post_date']));
        if (isset($_REQUEST['post_data']['post_modified']))
            $post_data->post_modified = date("Y-m-d H:i:s", strtotime($_REQUEST['post_data']['post_modified']));
        if (isset($_REQUEST['post_data']['post_date_gmt']))
            $post_data->post_date_gmt = date("Y-m-d H:i:s", strtotime($_REQUEST['post_data']['post_date_gmt']));
        if (isset($_REQUEST['post_data']['post_modified_gmt']))
            $post_data->post_modified_gmt = date("Y-m-d H:i:s", strtotime($_REQUEST['post_data']['post_modified_gmt']));


        if (isset($_REQUEST['product_sale_price_dates_from']) && !empty($_REQUEST['product_sale_price_dates_from'])) {
            $sale_from = $_REQUEST['product_sale_price_dates_from']
                . ' ' . (!empty($_REQUEST['product_sale_price_dates_from_hours']) && strlen($_REQUEST['product_sale_price_dates_from_hours']) ==2 ? $_REQUEST['product_sale_price_dates_from_hours'] : '00')
                . ':' . (!empty($_REQUEST['product_sale_price_dates_from_minutes']) && strlen($_REQUEST['product_sale_price_dates_from_minutes']) ==2 ? $_REQUEST['product_sale_price_dates_from_minutes'] : '00');
        } else {
            $sale_from = '';
        }


        if (isset($_REQUEST['product_sale_price_dates_to']) && !empty($_REQUEST['product_sale_price_dates_to'])) {
            $sale_to = $_REQUEST['product_sale_price_dates_to']
                . ' ' . (!empty($_REQUEST['product_sale_price_dates_to_hours']) && strlen($_REQUEST['product_sale_price_dates_to_hours']) <=2 ? $_REQUEST['product_sale_price_dates_to_hours'] : '00')
                . ':' . (!empty($_REQUEST['product_sale_price_dates_to_minutes']) && strlen($_REQUEST['product_sale_price_dates_to_minutes']) <=2 ? $_REQUEST['product_sale_price_dates_to_minutes'] : '00');
        } else {
            $sale_to = '';
        }

        if ((empty($sale_from) || Shop_CT_Dates::validate($sale_from)) && (empty($sale_to) || strtotime($sale_from) < strtotime($sale_to))) {
            $_REQUEST['post_meta']['sale_price_dates_from'] = $sale_from;
        }

        if ((empty($sale_from) || Shop_CT_Dates::validate($sale_to)) && (empty($sale_from) || strtotime($sale_from) < strtotime($sale_to))) {
            $_REQUEST['post_meta']['sale_price_dates_to'] = $sale_to;
        }


        if (isset($_REQUEST['post_meta']['product_image_gallery']) && !empty($_REQUEST['post_meta']['product_image_gallery'])) {
            $featured_image = $_REQUEST['post_meta']['product_image_gallery'][0];
            $product->set_image_id((int)$featured_image);
        }

        if (isset($_REQUEST['downloadable-file-urls']) && !empty($_REQUEST['downloadable-file-urls'])):
            foreach ($_REQUEST['downloadable-file-urls'] as $key => $url):
                $_REQUEST['post_meta']['downloadable_files'][] = array(
                    'url' => $url,
                    'name' => isset($_REQUEST['downloadable-file-names'][$key]) ? $_REQUEST['downloadable-file-names'][$key] : '',
                );
            endforeach;
        endif;

        if (isset($_REQUEST['post_meta']['regular_price'])
            && !empty($_REQUEST['post_meta']['regular_price'])
            && isset($_REQUEST['post_meta']['sale_price'])
            && !empty($_REQUEST['post_meta']['sale_price'])
            && $_REQUEST['post_meta']['sale_price'] >= $_REQUEST['post_meta']['regular_price']) {

            $_REQUEST['post_meta']['sale_price'] = '';
            $_REQUEST['post_meta']['sale_price_dates_to'] = '';
            $_REQUEST['post_meta']['sale_price_dates_from'] = '';
        }


        if (isset($_REQUEST['post_meta'])) {
            foreach ($_REQUEST['post_meta'] as $key => $value) {
                if (method_exists($product, 'set_' . $key)) {
                    call_user_func(array($product, 'set_' . $key), $value);
                } else {
                    throw new Exception('field "' . $key . '" does not exist for product.');
                }
            }
        }


        if (isset($_REQUEST['tax_input']['shop_ct_product_category'])) {
            $cats = array_map('intval', $_REQUEST['tax_input']['shop_ct_product_category']);
            wp_set_object_terms($id, $cats, Shop_CT_Product_Category::get_taxonomy(), false);
        }

        if (isset($_REQUEST['product_tags'])) {
            wp_set_object_terms($id, $_REQUEST['product_tags'], Shop_CT_Product_Tag::get_taxonomy(), false);
        }

        if (isset($_REQUEST['product-attribute-taxonomies']) && !empty($_REQUEST['product-attribute-taxonomies'])):
            $attributes = $_REQUEST['product-attribute-taxonomies'];
            $attributes_terms = $_REQUEST['product-attribute-terms'];

            foreach ($attributes as $key => $attribute):
                $current_terms = isset($attributes_terms[$key]) ? $attributes_terms[$key] : null;

                // either the id of existing attribute is passed or the name of attribute which needs to be saved
                if (is_numeric($attribute)):

                    $attr = new Shop_CT_Product_Attribute($attribute);

                else:

                    $attr = new Shop_CT_Product_Attribute();
                    $attr->set_name($attribute);
                    $attr->set_slug(sanitize_title($attribute));
                    $attr->save();
                    // register is required for taxonomy_exists() checks to be true
                    Shop_CT_Product_Attribute::register();

                endif;


                if (!empty($current_terms)):

                    $current_terms = array_map('trim', explode(',', $current_terms));
                    wp_set_object_terms($id, $current_terms, $attr->get_slug(), false);

                endif;
            endforeach;

        endif;

        $product->save();

        echo json_encode(array('success' => 1));
        die();
    }

    public function trash($id = null)
    {
        $trash_id = null;
        if (null !== $id && absint($id) == $id) {
            $trash_id = absint($id);
        }

        if (!$trash_id && isset($_REQUEST['id'])) {
            if (is_array($_REQUEST['id'])) {
                $trash_id = array_map('absint', $_REQUEST['id']);
            } elseif (absint($_REQUEST['id']) == $_REQUEST['id']) {
                $trash_id = absint($_REQUEST['id']);
            }
        }

        if (!$trash_id) {
            throw new Exception('invalid value passed for field "id", while trashing product');
        }

        if (is_array($trash_id)) {
            foreach ($trash_id as $cur_id) {
                wp_trash_post((int)$cur_id);
            }
        } else {
            wp_trash_post((int)$trash_id);
        }

        echo json_encode(array('success' => 1));
        die();
    }

    /**
     * @param int|int[] $id
     * @throws Exception
     */
    public function untrash($id = null)
    {
        if (!isset($_REQUEST['nonce']) || !wp_verify_nonce($_REQUEST['nonce'], 'shop_ct_nonce') || !isset($_REQUEST['id'])) {
            die('invalid parameters');
        }

        $untrash_id = $_REQUEST['id'];

        if (absint($untrash_id) != $untrash_id) {
            die('invalid id parameter');
        }

        $untrash_id = absint($untrash_id);

        if (!$untrash_id && isset($_REQUEST['id'])) {
            if (is_array($_REQUEST['id'])) {
                $untrash_id = array_map('absint', $_REQUEST['id']);
            } elseif (absint($id) == $id) {
                $untrash_id = absint($id);
            }
        }

        if (!$untrash_id) {
            throw new Exception('invalid value passed for field "id", while untrashing product');
        }

        if (is_array($untrash_id)) {
            foreach ($untrash_id as $cur_id) {
                wp_untrash_post((int)$cur_id);
            }
        } else {
            wp_untrash_post((int)$untrash_id);
        }

        echo json_encode(array('success' => 1));
        die();
    }

    public function popup()
    {
        /** Check if we are editing a product or adding new product */
        if (isset($_REQUEST['id']) && is_numeric($_REQUEST['id']) && $_REQUEST['id'] > 0) {
            /** if editing get the post object from database */

            $id = absint($_REQUEST['id']);

            $autoDraft = false;

            $product = new Shop_CT_Product($id);

        } else {
            $autoDraft = true;
            /** if adding new product create an auto-draft to work with */
            $post = get_default_post_to_edit(Shop_CT_Product::get_post_type(), true);

            $product = new Shop_CT_Product($post->ID);
            $product->set_post_data('post_title', '');
        }

        echo json_encode(array('success' => 1,
            'return_html' => \ShopCT\Core\TemplateLoader::get_template_buffer('admin/products/popup.view.php', compact('product', 'autoDraft')),
            'title' => $autoDraft ? __('Add Product', 'shop_ct') : __($product->get_title(), 'shop_ct'),
        ));
        die();
    }


}
