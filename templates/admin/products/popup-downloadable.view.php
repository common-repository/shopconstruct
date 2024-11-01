<?php
/**
 * @var $product Shop_CT_Product
 */

?>
<div class="shop-ct-grid-item mat-card show_if_downloadable">
    <span class="mat-card-title"><?php _e('Downloadable Files','shop_ct'); ?></span>
    <div class="product-downloadable-files-block">
        <table class="widefat">
            <tbody class="ui-sortable">
                <?php
                $files = $product->get_downloadable_files();
                if(!empty($files)):
                    foreach($files as $file):
                        \ShopCT\Core\TemplateLoader::get_template('admin/products/popup-downloadable-row.view.php', compact('file'));
                    endforeach;
                else:
                    echo '<tr class="no-items"><td colspan="7">' . __( 'No Files', 'shop_ct' ) . '</td></tr>';
                endif;
                ?>
            </tbody>
            <tfoot>
            <tr>
                <td colspan="5">
                    <button class="button-secondary product-add-downloadable-file"
                            data-row="<?php echo htmlspecialchars( \ShopCT\Core\TemplateLoader::get_template_buffer('admin/products/popup-downloadable-row.view.php') ); ?>"><?php _e( 'Add File', 'shop_ct' ); ?></button>
                </td>
            </tr>
            </tfoot>
        </table>
    </div>
    <div class="shop-ct-field mat-input-text full-width">
        <input type="number" name="post_meta[download_limit]" id="post_meta[download_limit]" value="<?= $product->get_download_limit(); ?>"/>
        <label for="post_meta[download_limit]"><?php _e('Download Limit', 'shop_ct'); ?></label>
        <span></span>
    </div>
    <div class="shop-ct-field mat-input-text full-width">
        <input type="number" name="post_meta[download_expiry]" id="post_meta[download_expiry]" value="<?= $product->get_download_expiry(); ?>"/>
        <label for="post_meta[download_expiry]"><?php _e('Download Expiry (in days)', 'shop_ct'); ?></label>
        <span></span>
    </div>
</div>
