<?php

class Shop_CT_popup_attribute_taxonomies extends Shop_CT_popup
{

    public function control_attribute_values($id, $control)
    {
        $label = (isset($control['label']) ? $control['label'] : '');
        $label_str = (!empty($label) ? '<span class="control_title" > ' . $label . ' </span>' : '');

        $attribute_id = isset($control['attribute_id']) ? $control['attribute_id'] : false;
        $terms = isset($control['default']) ? $control['default'] : array();
        $attr_name = "term";

        ?>
        <div class="wrap">
            <?php echo $label_str; ?>
            <div class="table_wrap">
                <div class="add_table_item">
                    <input type="hidden" value="<?php _e('New Term', 'shop_ct'); ?>"
                           id="shop_ct_attributes_new_term_text"/>
                    <button id="add_attribute_term_button" class="page-title-action"><?php _e('Add new', 'shop_ct'); ?>
                        <span class="taxonomy_label"><?php echo $attr_name; ?></span></button>
                </div>
                <table class="shop_ct_attr_terms_table shop_ct_list_table striped widefat">
                    <thead>
                    <tr>
                        <th class="name"><?php _e('Term', 'shop_ct'); ?></th>
                        <th class="action"><?php _e('Actions', 'shop_ct'); ?></th>
                    </tr>
                    </thead>
                    <tfoot>
                    <tr>
                        <td><?php _e('Term', 'shop_ct'); ?></td>
                        <td><?php _e('Actions', 'shop_ct'); ?></td>
                    </tr>
                    </tfoot>

                    <tbody>
                    <?php

                    if (!empty($terms)) {
                        foreach ($terms as $term) {
                            echo $this->add_row($term);
                        }
                    } else {
                        echo '<tr class="no-items"><td colspan="2">' . __('No items found.') . '</td></tr>';
                    }

                    ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php


    }

    /**
     * @param null|Shop_CT_Product_Attribute_Term $term
     *
     * @return string
     */
    public function add_row($term = NULL)
    {
        ob_start();

        ?>

        <tr class="shop_ct_attr_term_item shop_ct_term">

            <td class="shop_ct_new_attribute_term column-primary name">
                <input class="shop_ct_attribute_term_name" type="text" name="term_name"
                       data-id="<?php echo $this->not_null_and_is_instance_of_term($term, 'get_id') ? $term->get_id() : '' ?>"
                       value="<?php echo $this->not_null_and_is_instance_of_term($term, 'get_name') ? $term->get_name() : '' ?>"
                       placeholder="<?php _e('Name', 'shop_ct') ?>"/>
            </td>

            <td class="action">
                <button class="remove-term button button-primary"><?php _e('Remove', 'shop_ct') ?></button>
            </td>

        </tr>

        <?php

        return ob_get_clean();
    }

    private function not_null_and_is_instance_of_term($term, $function_name)
    {
        return $term instanceof Shop_CT_Product_Attribute_Term && method_exists($term, $function_name) && NULL !== $term->{$function_name}();
    }

    protected function control_div($id, $control)
    {

        if (isset($control['label'])) {
            $label = $control['label'];
            $label_str = '<span class="control_title" >' . $control['label'] . '</span>';
        } else {
            $label_str = $label = '';
        }

        $class = 'control-div ';
        isset($control['class']) ? $class .= $control['class'] : $class .= '';

        isset($control['default']) ? $default = $control['default'] : $default = '';

        ?>
        <div id="<?php echo $id ?>" class="<?php echo $class ?>"><?php echo $default ?></div>
        <?php
    }
}