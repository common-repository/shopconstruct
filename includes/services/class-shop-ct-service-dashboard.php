<?php

class Shop_CT_Dashboard
{
	public static function display_static_content()
    {
	    \ShopCT\Core\TemplateLoader::get_template('admin/dashboard/show.php');
    }
}
