<?php
/**
 * @var $cart Shop_CT_Cart
 * @var $isButton bool
 */
?>
<a href="#" class="shop-ct-open-cart<?php if($isButton === 'yes') { echo " shop-ct-button"; } ?>"><i class="fa fa-shopping-cart" aria-hidden="true"></i>&nbsp;&nbsp;(<span class="shop-ct-cart-count"><?php echo $cart->get_count(); ?></span>)</a>