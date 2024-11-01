<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}


/**
 * Shop_CT_Admin_Menus class.
 */
class Shop_CT_Admin_Menus
{

    /**
     * Admin menu items
     */
    public $items = array();

    /**
     * The number of toplevel pages in Shop_CT
     */
    public $toplevel_pages_count = 0;

    /**
     * Count of none-toplevel page in Shop_CT
     */
    public $pages_count = 0;


    /**
     * Array of toplevel pages in Shop_CT
     */
    public $toplevel_pages = array();


    /**
     * Array of none-toplevel pages in Shop_CT
     */
    public $pages = array();


    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->init_items();
        add_action('admin_menu', array($this, 'admin_menus'));
    }

    /**
     * Init the items array which applies filters to let admin menu be managable from extensions
     */
    public function init_items()
    {
        $menuItems = array(
            (object)array(
                'priority' => 1,
                'title' => __('Products | ShopConstruct', 'shop_ct'),
                'menu_title' => __('ShopConstruct', 'shop_ct'),
                'submenu_title' => 'Products',
                'capability' => 'manage_options',
                'menu_slug' => 'shop_ct_catalog',
                'function_name' => array('Shop_CT_Service_Products', 'init_admin'),
                'parent_slug' => '',
                'icon_image' => untrailingslashit(SHOP_CT()->plugin_url()) . '/assets/images/shop-menu-icon.png',
            ),
            (object)array(
                'priority' => 2,
                'title' => __('Categories | ShopConstruct', 'shop_ct'),
                'menu_title' => __('Categories', 'shop_ct'),
                'capability' => 'manage_options',
                'menu_slug' => 'shop_ct_categories',
                'function_name' => array('Shop_CT_Service_Product_Categories', 'init_admin'),
                'parent_slug' => 'shop_ct_catalog',
                'icon_image' => 'dashicons-category',
            ),

            (object)array(
                'priority' => 2,
                'title' => __('Tags | ShopConstruct', 'shop_ct'),
                'menu_title' => __('Tags', 'shop_ct'),
                'capability' => 'manage_options',
                'menu_slug' => 'shop_ct_tags',
                'function_name' => array('Shop_CT_Service_Tags', 'init_admin'),
                'parent_slug' => 'shop_ct_catalog',
                'icon_image' => 'dashicons-tag',
            ),
            (object)array(
                'priority' => 2,
                'title' => __('Reviews | ShopConstruct', 'shop_ct'),
                'menu_title' => __('Reviews', 'shop_ct'),
                'capability' => 'manage_options',
                'menu_slug' => 'shop_ct_reviews',
                'function_name' => array('Shop_CT_Service_Reviews', 'init_admin'),
                'parent_slug' => 'shop_ct_catalog',
                'icon_image' => 'dashicons-star-half',
            ),
            (object)array(
                'priority' => 2,
                'title' => __('Attributes | ShopConstruct', 'shop_ct'),
                'menu_title' => __('Attributes', 'shop_ct'),
                'capability' => 'manage_options',
                'menu_slug' => 'shop_ct_attributes',
                'function_name' => array('Shop_CT_Service_Attributes', 'init_admin'),
                'parent_slug' => 'shop_ct_catalog',
                'icon_image' => 'dashicons-media-spreadsheet',
            ),
            (object)array(
                'priority' => 2,
                'title' => __('Orders | ShopConstruct', 'shop_ct'),
                'menu_title' => __('Orders', 'shop_ct'),
                'submenu_title' => 'Orders',
                'capability' => 'manage_options',
                'menu_slug' => 'shop_ct_orders',
                'function_name' => array('Shop_CT_Service_Orders', 'init_admin'),
                'parent_slug' => 'shop_ct_catalog',
                'icon_image' => 'dashicons-money',
            ),
            (object)array(
                'priority' => 2,
                'title' => __('Checkout | ShopConstruct', 'shop_ct'),
                'menu_title' => __('Checkout', 'shop_ct'),
                'capability' => 'manage_options',
                'submenu_title' => 'Checkout options',
                'menu_slug' => 'shop_ct_checkout',
                'function_name' => array('Shop_CT_Checkout_Settings', 'init_admin'),
                'parent_slug' => 'shop_ct_catalog',
                'icon_image' => 'dashicons-money',
            ),
            (object)array(
                'priority' => 2,
                'title' => __('Settings | ShopConstruct', 'shop_ct'),
                'menu_title' => __('Settings', 'shop_ct'),
                'capability' => 'manage_options',
                'menu_slug' => 'shop_ct_settings',
                'function_name' => array('Shop_CT_Service_Settings', 'init_admin'),
                'parent_slug' => 'shop_ct_catalog',
                'icon_image' => '',
            ),
            (object)array(
                'priority' => 3,
                'title' => __('PayPal options | ShopConstruct', 'shop_ct'),
                'menu_title' => __('PayPal', 'shop_ct'),
                'capability' => 'manage_options',
                'menu_slug' => 'shop_ct_paypal',
                'function_name' => array($this, 'checkout_paypal_page'),
                'parent_slug' => 'shop_ct_checkout',
                'icon_image' => '',
            ),
            (object)array(
                'priority' => 3,
                'title' => __('BACS options | ShopConstruct', 'shop_ct'),
                'menu_title' => __('BACS', 'shop_ct'),
                'capability' => 'manage_options',
                'menu_slug' => 'shop_ct_bacs',
                'function_name' => array($this, 'checkout_bacs_page'),
                'parent_slug' => 'shop_ct_checkout',
                'icon_image' => '',
            ),
            (object)array(
                'priority' => 3,
                'title' => __('Cheque options | ShopConstruct', 'shop_ct'),
                'menu_title' => __('Cheque', 'shop_ct'),
                'capability' => 'manage_options',
                'menu_slug' => 'shop_ct_cheque',
                'function_name' => array($this, 'checkout_cheque_page'),
                'parent_slug' => 'shop_ct_checkout',
                'icon_image' => '',
            ),
            (object)array(
                'priority' => 3,
                'title' => __('Cash on Delivery options | ShopConstruct', 'shop_ct'),
                'menu_title' => __('Cash on Delivery', 'shop_ct'),
                'capability' => 'manage_options',
                'menu_slug' => 'shop_ct_cash',
                'function_name' => array($this, 'checkout_cash_page'),
                'parent_slug' => 'shop_ct_checkout',
                'icon_image' => '',
            ),


            (object)array(
                'priority' => 3,
                'title' => 'Products | ShopConstruct',
                'menu_title' => 'Products',
                'capability' => 'manage_options',
                'menu_slug' => 'shop_ct_products_settings',
                'function_name' => array('Shop_CT_Product_Settings', 'init_admin'),
                'parent_slug' => 'shop_ct_settings',
                'icon_image' => '',
            ),
            (object)array(
                'priority' => 3,
                'title' => 'Emails | ShopConstruct',
                'menu_title' => 'Emails',
                'capability' => 'manage_options',
                'menu_slug' => 'shop_ct_emails_settings',
                'function_name' => array('Shop_CT_Email', 'init_admin'),
                'parent_slug' => 'shop_ct_settings',
                'icon_image' => '',
            ),
            (object)array(
                'priority' => 3,
                'title' => 'Shipping Zones | ShopConstruct',
                'menu_title' => 'Shipping Zones',
                'capability' => 'manage_options',
                'menu_slug' => 'shop_ct_shipping zones_settings',
                'function_name' => array('Shop_CT_Service_Shipping_Zones', 'init_admin'),
                'parent_slug' => 'shop_ct_settings',
                'icon_image' => '',
            ),
        );


        if (!SHOP_CT()->isAdvanced()) {
	        $menuItems[] = (object) array(
                'priority' => 2,
                'title' => 'Style Settings | ShopConstruct',
                'menu_title' => 'Style Settings (Pro)',
                'capability' => 'manage_options',
                'menu_slug' => 'shop_ct_style_settings',
                'function_name' => array($this, 'style_settings_pro_page'),
                'parent_slug' => 'shop_ct_catalog',
                'icon_image' => 'dashicons-admin-appearance',
            );
        }

        $this->items = apply_filters("shop_ct_admin_menu_items", $menuItems);
    }

    /**
     * get admin menu item
     * @param int $slug
     * @return stdClass|bool
     */
    public function get_item($slug)
    {
        $item_info = new stdClass();
        foreach ($this->items as $it) {
            if ($it->menu_slug == $slug) {
                $item_info = $it;
                $item_info->page_title = $it->title;
                $item_info->priority = $it->priority;
            }
        }
        return !empty($item_info) ? $item_info : false;
    }

    /**
     * Add admin menu pages
     */
    public function admin_menus()
    {
        foreach ($this->items as $menu_item) {
            $priority = $menu_item->priority;
            $title = $menu_item->title;
            $menu_title = $menu_item->menu_title;
            $capability = $menu_item->capability;
            $menu_slug = $menu_item->menu_slug;
            $parent_slug = $menu_item->parent_slug;
            $icon_image = $menu_item->icon_image;
            if ($priority == 1) {
                $this->toplevel_pages[$this->toplevel_pages_count] = add_menu_page($title, $menu_title, $capability, $menu_slug, array($this, 'init_pages'), $icon_image);
                add_action('load-' . $this->toplevel_pages[$this->toplevel_pages_count], array($this, 'admin_init'));
                $this->toplevel_pages_count++;
            } elseif ($priority == 2) {
                $this->pages[$this->pages_count] = add_submenu_page($parent_slug, $title, $menu_title, $capability, $menu_slug, array($this, 'init_pages'));
                add_action('load-' . $this->pages[$this->pages_count], array($this, 'admin_init'));
                $this->pages_count++;
            }
            /* pages with lower priority than 2 are not shown in admin menu */
        }

        global $submenu;

        if (isset($submenu['shop_ct_catalog'])) {
            if ($submenu['shop_ct_catalog'][0][0] == "ShopConstruct") {
                $submenu['shop_ct_catalog'][0][0] = "Products";
            }
        }
    }

    /**
     *
     */
    public function admin_init()
    {
        SHOP_CT()->admin->current_page = $this->get_current_page_slug();
    }

    public function cmp($a, $b)
    {
        if ($a->priority == $b->priority) {
            return 0;
        } else {
            return $a->priority < $b->priority ? 1 : -1; // reverse order
        }
    }

    /**
     * Return Current page's slug
     * @return string
     */
    public function get_current_page_slug()
    {
        if (isset($_REQUEST['page'])) $page_name = $_REQUEST['page'];
        else return false;

        if (isset($_REQUEST['shop_ct_path'])) {
            $path = $_REQUEST['shop_ct_path'];
            $path_array = explode("/", $path);
            $subpage_name = end($path_array);
        }
        $shop_ct_main_menu_custom = $this->items;

        usort($shop_ct_main_menu_custom, array($this, 'cmp'));
        foreach ($shop_ct_main_menu_custom as $item) {
            if ($page_name == $item->menu_slug || in_array($page_name, $this->toplevel_pages) || (isset($subpage_name) && $subpage_name == $item->menu_slug)) {
                $active_page = $item->menu_slug;
                break;
            }
        }
        return isset($active_page) ? $active_page : false;
    }

    /**
     * Prints out a single adminbar item
     *
     * @param $item
     */
    protected function adminbar_item($item)
    {
        echo '<a class="shop-ct-link" href="' . $item->link . '" data-slug="' . $item->menu_slug . '">' . $item->link_html . '</a> ';
        if (isset($item->children)) {
            echo '<ul class="shop_ct_submenu">';
            if ($item->priority == 2) {
                echo '<li class="' . $item->sub_item_classes_str . ' first_sub_item">';
                echo '<a class="shop-ct-link" href="' . $item->link . '" data-slug="' . $item->menu_slug . '">' . $item->sub_title . '</a> ';
                echo '</li>';
            }
            foreach ($item->children as $sub_item) {
                echo '<li class="' . $sub_item->item_classes_str . '">';
                $this->adminbar_item($sub_item);
                echo '</li>';
            }
            echo '</ul>';
        }
    }

    /**
     * @param string $slug
     * @return bool
     */
    public function has_child_item($slug)
    {
        $t = false;
        foreach ($this->items as $shop_ct_nav_item) {
            if ($shop_ct_nav_item->parent_slug == $slug) {
                $t = true;
            }
        }
        return $t;
    }

    public function get_parent_listing($slug)
    {
        $menu_item = $this->get_item($slug);
        $listing = $slug;
        $pr = $menu_item->priority;
        $parent_slug = $menu_item->parent_slug;
        while ($pr > 3) {
            foreach ($this->items as $row) {
                if ($row->menu_slug == $parent_slug) {
                    $parent_slug = $row->parent_slug;
                    $pr = $row->priority;
                    $listing = $row->menu_slug . "/" . $listing;
                }
            }
        }
        return array($listing, $parent_slug);
    }

    public function get_page_link($slug)
    {
        $menu_item = $this->get_item($slug);
        $priority = $menu_item->priority;
        if ($priority == 1 || $priority == 2) {
            $link = "admin.php?page=" . $slug;
        } else {
            $listing = $this->get_parent_listing($slug);
            $link = "admin.php?page=" . $listing[1] . "&shop_ct_path=" . $listing[0];
        }
        return $link;
    }

    /**
     * Return hierarchial admin menu
     * @return array
     */
    protected function get_hierarchial_menu()
    {
        $i = 0;
        $menu = $this->items;
        foreach ($menu as $item) {
            $slug = $item->menu_slug;
            $this_item = $this->get_item($slug);
            $title = $this_item->menu_title;
            $priority = $this_item->priority;
            $icon_image = $this_item->icon_image;
            $item_classes = array();
            array_push($item_classes, "menu_item");
            $has_children = $this->has_child_item($slug);
            if ($priority > 2) array_push($item_classes, "sub_item");
            /* if the function rerturns true assign 'has-child' class */
            if ($has_children == true || $priority == 2) array_push($item_classes, "has_children");

            if (SHOP_CT()->admin->current_page == $slug) array_push($item_classes, "current");

            if ($priority == 1) array_push($item_classes, "main_page");

            if ($priority == 2) array_push($item_classes, "sub_page");

            if ($title == "ShopConstruct") $title = "Products";


            if ($priority == 1 || $priority == 2) {
                $link = "admin.php?page=" . $slug;
            } else {
                $listing = $this->get_parent_listing($slug);
                $link = "admin.php?page=" . $listing[1] . "&shop_ct_path=" . $listing[0];
            }

            if ($priority == 1 || $priority == 2) {
                if (!empty($icon_image)) {
                    $dashicon_test = explode("dashicon", $icon_image);
                    if (isset($dashicon_test[1])) {
                        $image_html = '<span class="shop_ct_ab_icon shop_ct_dashicons dashicons ' . $icon_image . '"></span>';
                    } elseif (strstr($icon_image, '<')) {
                        $image_html = '<span class="shop_ct_ab_icon">' . $icon_image . '</span>';
                    } else {
                        $image_html = '<span class="shop_ct_ab_icon"><img src="' . $icon_image . '" width="20" height="20" alt="" /></span>';
                    }
                } else {
                    $image_html = '<span class="shop_ct_ab_icon shop_ct_dashicons dashicons dashicons-admin-generic"></span>';
                }

                $title_html = '<span class="shop_ct_ab_title">' . $title . '</span>';
                if ($has_children == true) $title_html .= '<span class="shop_ct_ab_after dashicons dashicons-arrow-down-alt2"></span>';
            } else {
                $image_html = "";
                $title_html = $title;
            }

            $sub_title = "";
            if ($priority == 2) {
                if (isset($this_item->submenu_title) && !empty($this_item->submenu_title)) {
                    $sub_title = $this_item->submenu_title;
                } else {
                    $sub_title = $title;
                }
            }

            $item_classes_str = implode(" ", $item_classes);

            $sub_item_classes_str = str_replace(array("has_children", "sub_page"), " ", $item_classes_str);
            $menu[$i]->link = $link;
            $menu[$i]->item_classes_str = $item_classes_str;
            $menu[$i]->link_html = $image_html . $title_html;
            $menu[$i]->sub_title = $sub_title;
            $menu[$i]->sub_item_classes_str = $sub_item_classes_str;
            $i++;
        }
        $new = array();
        foreach ($menu as $a) {
            $new[$a->parent_slug][] = $a;
        }
        $tree = $this->createTree($new, array($menu[0]));

        return $tree;
    }

    public function createTree(&$list, $parent)
    {
        $tree = array();
        foreach ($parent as $k => $l) {
            if (isset($list[$l->menu_slug])) {
                $l->children = $this->createTree($list, $list[$l->menu_slug]);
            }
            $tree[] = $l;
        }
        return $tree;
    }

    /**
     * Prints out adminbar
     */
    protected function adminbar()
    {
        $menu_array = $this->get_hierarchial_menu();

        $screen = get_current_screen();

        $user = wp_get_current_user();

        echo '<ul is="shop_ct-navigation-list" class="shop_ct_adminbar_list">';
        foreach ($menu_array as $item) {
            if ((in_array($screen->id, $this->toplevel_pages) || $screen->parent_base == $item->menu_slug) && $screen->parent_base == $item->menu_slug) {
                echo '<li class="' . $item->item_classes_str . '">';
                echo '<a class="shop-ct-link" href="' . $item->link . '" data-slug="' . $item->menu_slug . '" >' . $item->link_html . '</a>';
                echo '</li>';
                foreach ($item->children as $children) {
                    echo '<li class="' . $children->item_classes_str . '">';
                    $this->adminbar_item($children);
                    echo '</li>';
                }
            }
        }
        echo '</ul>';
        ?>
        <div class="clear"></div>

        <div class="shop_ct_adminbar_footer">
            <a href="https://wordpress.org/support/plugin/shopconstruct/reviews" class="shop_ct_feedback_btn"
               target="_blank"><?php _e('Leave a Feedback', 'shop_ct'); ?></a>
            <a class="shop_ct_help_btn" href="https://wordpress.org/support/plugin/shopconstruct/"
               target="_blank"><?php _e('Help', 'shop_ct'); ?></a>
        </div>
        <?php
    }

    /**
     * Load and print out the HTML of the current page
     */
    public function load_admin_page()
    {
        $cur = SHOP_CT()->admin->current_page;
        $item = $this->get_item($cur);
        if (isset($item->function_name)) {
            if (is_array($item->function_name) && method_exists($item->function_name[0], $item->function_name[1])) {
                echo call_user_func($item->function_name);
            } elseif (function_exists($item->function_name)) {
                echo call_user_func($item->function_name);
            } else {
                _e('Page not found', 'shop_ct');
            }
        } else {
            _e('Define function for displaying this page', 'shop_ct');
        }
    }

    /**
     * Prints out admin pages
     */
    public function init_pages()
    {

        $url = SHOP_CT()->plugin_url();
        $cur = SHOP_CT()->admin->current_page;
        if (isset($_SESSION['shop_ct_adminbar']) && !empty($_SESSION['shop_ct_adminbar'])) {
            $adminbar_class = $_SESSION['shop_ct_adminbar'];
        } else {
            $adminbar_class = 'vertical';
        }
        $symbol = Shop_CT_Currencies::get_currency_symbol() ?: '&#36;';
        $position = SHOP_CT()->settings->currency_pos ?: 'left';
        ?>
        <div id="shop_ct_wrapper" data-current-page="<?php echo $cur; ?>"
             data-shop-ct-currency-symbol="<?php echo $symbol; ?>"
             data-shop-ct-currency-position="<?php echo $position; ?>">
            <div class="shop_ct_adminbar  <?php echo $adminbar_class; ?>">
                <?php $this->adminbar(); ?>
            </div>
            <div class="shop_ct_body">
                <div class="shop_ct_ajax_bind_html">
                    <?php $this->load_admin_page(); ?>
                </div>
            </div>
            <div class="shop_ct_loading_cover">
                <div class="shop_ct_loading_img">
                    <img src="<?php echo $url . "/assets/images/loading_dark_large.gif" ?>" alt="loading page"/>
                </div>
            </div>
            <div id="shop_ct_error_dialog_wrapper"></div>
            <div id="shop_ct_popup_block"></div>
        </div>
        <?php
    }


    public function design_page()
    {
        return "<h2 class='vertical_menu_sometext'>In Development stage</h2>";
    }

    public function localization_page()
    {
        return "<h2 class='vertical_menu_sometext'>In Development stage</h2>";
    }

    public function checkout_paypal_page()
    {
        SHOP_CT()->payment_gateways->output_admin_page('paypal');
    }

    public function checkout_bacs_page()
    {
        SHOP_CT()->payment_gateways->output_admin_page('bacs');
    }

    public function checkout_cheque_page()
    {
        SHOP_CT()->payment_gateways->output_admin_page('cheque');
    }

    public function checkout_cash_page()
    {
        SHOP_CT()->payment_gateways->output_admin_page('cod');
    }

    public function style_settings_pro_page()
    {
        ?>
        <div class="shop-ct-admin-pro">
            <img src="<?php echo SHOP_CT()->plugin_url() . '/assets/images/admin/style-settings-pro.png'; ?>" alt="style settings" />
            <div class="shop-ct-admin-pro-overlay">
                <h2>Style Settings are available only in advanced version of the plugin</h2>
                <p>
                    <a class="mat-button mat-button--primary" href="https://shopconstruct.com/pricing" target="_blank">Get advanced version for only $7</a>
                </p>
            </div>
        </div>
        <?php
    }
}