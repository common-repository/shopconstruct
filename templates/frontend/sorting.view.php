<div class="shop-ct-sorting">
    <form action="#" method="get">
        <h3 class="shop-ct-subtitle">Sort by</h3>
        <label class="shop-ct-select-wrap">
            <select class="shop_ct_sorting shop-ct-select" name="products_orderby">
                <option value="date,desc" data-location="<?php echo add_query_arg(array('products_orderby' => 'date', 'products_ordering'=>'desc'), $_SERVER['REQUEST_URI']); ?>" data-ordering="desc" <?php if(!isset($_GET['products_orderby']) || ($_GET['products_orderby'] === 'date' && $_GET['products_ordering'] === 'desc'))  echo 'selected="selected"'; ?>>New to Old</option>
                <option value="date,asc" data-location="<?php echo add_query_arg(array('products_orderby' => 'date', 'products_ordering'=>'asc'), $_SERVER['REQUEST_URI']); ?>" data-ordering="asc" <?php if(isset($_GET['products_orderby']) && $_GET['products_orderby'] === 'date' && $_GET['products_ordering'] === 'asc')  echo 'selected="selected"'; ?>>Old to New</option>
                <option value="price,desc" data-location="<?php echo add_query_arg(array('products_orderby' => 'price', 'products_ordering'=>'desc'), $_SERVER['REQUEST_URI']); ?>" data-ordering="desc" <?php if(isset($_GET['products_orderby']) && $_GET['products_orderby'] === 'price' && $_GET['products_ordering'] === 'desc')  echo 'selected="selected"'; ?>>Price: high to low</option>
                <option value="price,asc" data-location="<?php echo add_query_arg(array('products_orderby' => 'price', 'products_ordering'=>'asc'), $_SERVER['REQUEST_URI']); ?>" data-ordering="asc" <?php if(isset($_GET['products_orderby']) && $_GET['products_orderby'] === 'price' && $_GET['products_ordering'] === 'asc')  echo 'selected="selected"'; ?>>Price: low to high</option>
                <option value="review,desc" data-location="<?php echo add_query_arg(array('products_orderby' => 'review', 'products_ordering'=>'desc'), $_SERVER['REQUEST_URI']); ?>" data-ordering="desc" <?php if(isset($_GET['products_orderby']) && $_GET['products_orderby'] === 'review' && $_GET['products_ordering'] === 'desc')  echo 'selected="selected"'; ?>>Review: high to low</option>
                <option value="review,asc" data-location="<?php echo add_query_arg(array('products_orderby' => 'review', 'products_ordering'=>'asc'), $_SERVER['REQUEST_URI']); ?>" data-ordering="asc" <?php if(isset($_GET['products_orderby']) && $_GET['products_orderby'] === 'review' && $_GET['products_ordering'] === 'asc')  echo 'selected="selected"'; ?>>Review: low to high</option>
            </select>
            <img src="<?php echo SHOP_CT()->plugin_url() . '/assets/images/admin/arrow-down.svg'; ?>" width="22" alt="select" />
            <input type="hidden" name="products_ordering" value="desc" />
        </label>
    </form>

</div>