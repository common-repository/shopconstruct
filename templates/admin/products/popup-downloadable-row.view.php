<?php
/**
 * @var $file null|array
 */
if(!isset($file)) $file = array('name'=>'','url'=>'');
?>
<tr>
    <td class="sort" ><i class="fa fa-arrows"></i></td>
    <td class="file_name" >
        <input type="text" placeholder="<?php _e('File Name','shop_ct'); ?>" name="downloadable-file-names[]" value="<?php echo $file['name']; ?>" />
    </td>
    <td class="file_url" >
        <input type="url" class="input_text" placeholder="<?php _e('File URL','shop_ct'); ?>" name="downloadable-file-urls[]" value="<?php echo $file['url']; ?>" />
        <button class="upload_button"><?php _e('Choose file'); ?></button>
    </td>
    <td class="remove"><i class="remove_btn fa fa-times-circle-o"></i></td>
</tr>
