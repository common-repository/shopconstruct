<?php

class Shop_CT_popup_review extends Shop_CT_popup
{
    protected function control_div($id, $control) {

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

    public function get_status_section_choices() {
        $choices = array(
            'approve' => __('Approved', 'shop_ct'),
            'hold' => __('Pending', 'shop_ct'),
            'spam' => __('Spam', 'shop_ct'),
        );

        return $choices;
    }

    public function get_submitted_on_html($id) {

        if( $id !== 'reply' ) {
            $comment = get_comment($id);
            $date = $comment->comment_date;

            $html = '<i class="fa fa-calendar"></i><p>Submitted on: ' . $date . '</p>';
        } else {
            $html = '';
        }

        return $html;
    }

    public function get_in_response_to_html($id, $parent_id = NULL) {

        $comment = is_null($parent_id) ? get_comment($id) : get_comment($parent_id);

        $comment_post_id = $comment->comment_post_ID;

        $post = get_post($comment_post_id);

        $post_title = $post->post_title;

        $html = '<p>In response to: <a class="review_popup_link" href="#">' . $post_title . '</a></p>';

        return $html;
    }

    public function get_move_to_trash_html($id) {
        $html = '<a id="move_review_to_trash" href="#" data-comment-id="' . $id . '">Move to Trash</a>';

        return $html;
    }

    public function get_title($str) {
        return '<h2 class="review_section_title">' . ucfirst(__($str, 'shop_ct')) . '</h2>';
    }
}