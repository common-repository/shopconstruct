<?php
/**
 * Base class for displaying a list of items in an ajaxified HTML table.
 *
 * @since 1.0.0
 * @access private
 */
class Shop_CT_list_table {

	/**
	 * The current list of items.
	 * @var array
	 */
	public $items;

	/**
	 * All the texts to be shown on this page.
	 * @var array associative array of labels
	 */
	public $labels;

	public $level=0;

	/**
	 * The id of form tag
	 * @var string
	 */
	public $form_id;

	/**
	 * The ids for all labels tags (used to attach js images to buttons/inputs)
	 * @var array
	 */
	public $ids;

	/**
	 * The classes for all labels tags (used to attach js images to buttons/inputs)
	 * @var array
	 */
	public $classes;

	/**
	 * Classes for table tag
	 * @var array
	 */
	public $table_classes;

	/**
	 * The statuses that current post type handles
	 * @var array
	 */
	public $statuses;

	protected $current_status="any";

	/**
	 * Indicates wheter to show statuses or no
	 * @var boolean
	 */
	public $show_statuses = false;

	/**
	 * Indicates wheter to show search box or no
	 * @var boolean
	 */
	public $show_search_box = true;

	/**
	 * Array of bulk actions for current items list
	 * @var array
	 */
	public $bulk_actions;

	/**
	 * Indicates wheter to show bulk actions or no
	 * @var boolean
	 */
	public $show_bulk_actions = true;

	/**
	 * Array of filters for current page
	 * @var array
	 */
	public $filters;

	/**
	 * Indicates whether to show filters or no
	 * @var boolean
	 */
	public $show_filters = false;

	/**
	 * The current columns list.
	 * @var array associative array of columns
	 */
	public $columns;

	/**
	 * The actions to be displayed in the main columns.
	 * @var array
	 */
	public $row_actions;

	/**
	 * The object type of table items.
	 * @var string  for categories it is shop_ct_product_category
	 */
	public $items_object_type;

	/**
	 * The resource type of table items.
	 * @var string  for taxonomies ---> 'taxonomy' , for posts ---> 'post_type'
	 */
	public $items_resource_type;


	/**
	 * The query arguments for list table items.
	 * @var array an associative array of query arguments
	 */
	public $items_object_args;

	/**
	 * Indicates whether to show pagination or not
	 * @var boolean
	 */
	public $pagination = true;

	/**
	 * The number of items to be shown per page
	 * @var integer
	 */
	public $per_page = 10;

	/**
	 * The number of items to be shown per page
	 * @var integer
	 */
	public $paged = 1;

	/**
	 * The number of total pages
	 * @var integer
	 */
	public $total_pages = 1;

	/**
	 * Indicates whether to show items in hierarchial way or no, defaults to the object type's hierarchy
	 * @var boolean
	 */
	public $hierarchial;

	public $display_nav = true;

	public $_pagination_args;

	public function __construct( $args = array() ) {

		 $args = wp_parse_args( $args, array(
             'labels' => array(),
             'ids' => array(),
             'form_id' => "shop_ct_list_table",
             'classes' => array(),
             'table_classes' => array('shop_ct_list_table'),
             'statuses' => array(
                 'any' => __('All','shop_ct'),
                 'publish' => __('Published','shop_ct'),
                 'draft' => __('Draft','shop_ct'),
                 'pending' => __('Pending Review','shop_ct'),
                 'future' => __('Scheduled','shop_ct'),
                 'private' => __('Private','shop_ct'),
                 'trash' => __('Trash','shop_ct'),
             ),
             'show_statuses' => true,
             'show_search_box' => true,
             'bulk_actions' => array(
                 'bulk_actions' => __('Bulk Actions','shop_ct'),
                 'trash' => __('Move to trash','shop_ct'),
             ),
             'show_bulk_actions' => true,
             'show_filters' => true,
             'filters' => array('months','category'),
             'columns' => array(
                 'cb'=> array (
                     'name'=>'',
                     'sortable'=>false
                 ),
                 'title' => array (
                     'name'=>__('Title','shop_ct'),
                     'sortable'=>true,
                     'primary'=>true,
                 ),
                 'date' => array (
                     'name'=>__('Date','shop_ct'),
                     'sortable'=>true
                 ),
             ),
             'row_actions' => array(
                 'edit' => 'Edit',
                 'trash'=>'Trash',
                 'view'=>'View',
             ),
             'items_object_type' => 'shop_ct_product',
             'items_resource_type' => 'post_type',
             'items_object_args' => array(),
             'pagination' => true,
             'per_page' => 10,
             'total_pages' => 1,
             'hierarchial' => false,
		) );

        foreach($args as $key=>$value){
            if($value){
                $this->$key = $value;
            }
        }

		$this->_args = $args;
	}

	/**
	 * Whether the table has items to display or not
	 * @return bool
	 */
	public function has_items() {
		return !empty( $this->items );
	}

	/**
	 * Returns the labels for current page after parsing it
	 * @return array|bool List of labels for the page.
	 */
	public function get_labels(){
		if($this->labels){
			return wp_parse_args($this->labels,array(
				'page_title'=>'Title',
				'add_new_item'=>'Add new',
				'search'=>'Search',
			));
		}
	    return false;
	}

	/**
	 * Returns the ids for current page after parsing it
	 * @return array|bool List of ids for the page.
	 */

	public function get_ids(){
		if($this->ids){
			return wp_parse_args($this->ids,array(
				'add_new_item'=>'add_new',
				'search'=>'search_btn',
			));
		}
        return false;
	}

	public function get_classes(){
		if($this->classes){
			return wp_parse_args($this->classes,array(
				'add_new_item'=>'',
				'search'=>'',
			));
		}
		return false;
	}

	/**
	 * Helper to create links to edit.php with params.
	 * @param array  $args  URL parameters for the link.
	 * @param string $label Link text.
	 * @param string $class Optional. Class attribute. Default empty string.
	 * @return string The formatted link string.
	 */
	protected function get_edit_link( $args, $label, $class = '' ) {
		$url = add_query_arg( $args, 'admin.php?page=shop_ct_catalog' );

		$class_html = '';
		if ( ! empty( $class ) ) {
			 $class_html = sprintf(
				' class="%s"',
				esc_attr( $class )
			);
		}

		return sprintf(
			'<a href="%s" %s>%s</a>',
			esc_url( $url ),
			$class_html,
			$label
		);
	}

	/**
	 * Get an associative array ( id => link ) with the list
	 * of views available on this table.
	 *
	 * @return array
	 */
	protected function get_views(){
		if(isset($_REQUEST['post_status']) && !empty($_REQUEST['post_status'])){
			$this->current_status = $_REQUEST['post_status'];
		}
		if($this->show_statuses == true){

			$active_page = SHOP_CT()->admin->current_page;

			$status_links = array();
			$available_statuses = $this->statuses;
			$post_type = $this->items_object_type;
			foreach($available_statuses as $status => $name){
				$status_count_args = array(
					"posts_per_page"=>-1,
					"post_status" => $status,
					"post_type" => $post_type,
				);

				$classes="";

				if(isset($_REQUEST['post_status'])){
					if($_REQUEST['post_status'] == $status){

						$classes = ' class="current"';
					}

				}elseif($status=="any"){
					$classes = ' class="current"';
				}

				$url_args = array(
					"post_status" => $status,
				);

				$url = add_query_arg($url_args,SHOP_CT()->admin->menus->get_page_link( $active_page ));

				$status_count = count(get_posts($status_count_args));
				if($status_count>0){
					$label = $name . sprintf(' <span class="count">(%s)</span>', number_format_i18n($status_count));

					$status_links[$status] = sprintf('<a href="%s" %s >%s</a>', $url, $classes, $label);
				}
			}
			return $status_links;
		}else{
			return array();
		}


	}

	public function views(){
		$views = $this->get_views();

		if ( empty( $views ) )
			return;

		echo "<ul class='subsubsub shop-ct-post-status'>\n";
		foreach ( $views as $class => $view ) {
			$views[ $class ] = "\t<li class='$class'>$view";
		}
		echo implode( " |</li>\n", $views ) . "</li>\n";
		echo "</ul>";
	}

	protected function get_bulk_actions( $which = "" ){

		if($this->show_bulk_actions == false)
			return;

		if ( empty( $this->bulk_actions ) )
			return;

		if(isset($_GET['post_status'])){
			if($_GET['post_status'] == 'trash')
				$this->bulk_actions = array(
					'bulk_actions' => __('Bulk Actions','shop_ct'),
					'restore' => __('Restore','shop_ct'),
					'delete' => __('Delete Permanently','shop_ct'),
				);
				
		}

		echo '<label for="bulk-action-selector-' . esc_attr( $which ) . '" class="screen-reader-text">' . __( 'Select bulk action' ) . '</label>';
		echo '<select name="action" id="bulk-action-selector-' . esc_attr( $which ) . "\">\n";
		
		foreach ( $this->bulk_actions as $name => $title ) {
			$class = 'edit' === $name ? ' class="hide-if-no-js"' : '';

			echo "\t" . '<option value="' . $name . '"' . $class . '>' . $title . "</option>\n";
		}

		echo "</select>\n";

		submit_button( __( 'Apply' ), 'action', '', false, array( 'id' => "doaction-".$which ) );
		echo "\n";
		
	}

	public function months_dropdown($post_type){
		global $wpdb,$wp_locale;

		$extra_checks = "AND post_status != 'auto-draft'";
		if ( ! isset( $_GET['post_status'] ) || 'trash' !== $_GET['post_status'] ) {
			$extra_checks .= " AND post_status != 'trash'";
		} elseif ( isset( $_GET['post_status'] ) ) {
			$extra_checks = $wpdb->prepare( ' AND post_status = %s', $_GET['post_status'] );
		}

		$months = $wpdb->get_results( $wpdb->prepare( "
			SELECT DISTINCT YEAR( post_date ) AS year, MONTH( post_date ) AS month
			FROM $wpdb->posts
			WHERE post_type = %s
			$extra_checks
			ORDER BY post_date DESC
		", $post_type ) );

		$month_count = count( $months );

		if ( !$month_count || ( 1 == $month_count && 0 == $months[0]->month ) )
			return;

		$m = isset( $_GET['m'] ) ? (int) $_GET['m'] : 0;
		?>

		<label for="filter-by-date" class="screen-reader-text"><?php _e( 'Filter by date' ); ?></label>
		<select name="m" id="filter-by-date">
		<option<?php selected( $m, 0 ); ?> value="0"><?php _e( 'All dates' ); ?></option>
			<?php
				foreach ( $months as $arc_row ) {
					if ( 0 == $arc_row->year )
						continue;

					$month = zeroise( $arc_row->month, 2 );
					$year = $arc_row->year;

					printf( "<option %s value='%s'>%s</option>\n",
						selected( $m, $year . $month, false ),
						esc_attr( $arc_row->year . $month ),
						/* translators: 1: month name, 2: 4-digit year */
						sprintf( __( '%1$s %2$d' ), $wp_locale->get_month( $month ), $year )
					);
				}
			?>
		</select>
		<?php
	}

	/**
	 * Displays the filters
	 * @param string $which
	 */
	public function extra_actions($which){


		if(isset($_REQUEST['cat'])){
			$cat = $_REQUEST['cat'];
		}else{
			$cat = "";
		}


		if($this->show_filters){
			foreach($this->filters as $filter){
				if($filter == "months"){
					$this->months_dropdown($this->items_object_type);
				}elseif($filter == "category"){

					if ( is_object_in_taxonomy( $this->items_object_type, Shop_CT_Product_Category::get_taxonomy() ) ) {
						$dropdown_options = array(
							'show_option_all' => __( 'All categories' ),
							'hide_empty' => 0,
							'hierarchical' => 1,
							'show_count' => 0,
							'orderby' => 'name',
							'selected' => $cat,
							'taxonomy' => Shop_CT_Product_Category::get_taxonomy(),
						);

						echo '<label class="screen-reader-text" for="cat">' . __( 'Filter by category' ) . '</label>';
						wp_dropdown_categories( $dropdown_options );
					}
				}
			}

			submit_button( __( 'Filter' ), '', '', false, array( 'id' => "post-query-submit" ) );
		}
	}

	public function search_box( $text ) {


		if(isset($this->ids['search'])){
			$input_id = $this->ids['search'] . '-search-input';
		}else{
			$input_id = 'table-search-input';
		}

		$search_query =  isset($_REQUEST['s']) ? esc_attr( wp_unslash( $_REQUEST['s'] ) ) : '';


		if ( ! empty( $_REQUEST['orderby'] ) )
			echo '<input type="hidden" name="orderby" value="' . esc_attr( $_REQUEST['orderby'] ) . '" />';
		if ( ! empty( $_REQUEST['order'] ) )
			echo '<input type="hidden" name="order" value="' . esc_attr( $_REQUEST['order'] ) . '" />';
		if ( ! empty( $_REQUEST['post_mime_type'] ) )
			echo '<input type="hidden" name="post_mime_type" value="' . esc_attr( $_REQUEST['post_mime_type'] ) . '" />';
		if ( ! empty( $_REQUEST['detached'] ) )
			echo '<input type="hidden" name="detached" value="' . esc_attr( $_REQUEST['detached'] ) . '" />';
		?>
			<label class="screen-reader-text" for="<?php echo $input_id ?>"><?php echo $text; ?>:</label>
			<input class="shop-ct-search" type="search" id="<?php echo $input_id ?>" name="s" value="<?php echo $search_query; ?>" />
			<?php submit_button( $text, 'button shop-ct-search-submit', '', false, array( 'id' => 'search-submit' ) ); ?>
		<?php
	}

	/**
	 * Get the current page number
	 *
	 * @return int
	 */
	public function get_pagenum() {
		$pagenum = isset( $_REQUEST['paged'] ) ? absint( $_REQUEST['paged'] ) : 0;

		if ( isset( $this->_pagination_args['total_pages'] ) && $pagenum > $this->_pagination_args['total_pages'] )
			$pagenum = $this->_pagination_args['total_pages'];

		return max( 1, $pagenum );
	}

	protected function pagination( $which ) {

		$this->get_total_pages();

		$total_items = $this->get_total_items_count();
		$per_page = $this->per_page;

		if ($per_page > 0 )
			$total_pages = ceil( $total_items / $per_page );

		$args = array(
			'total_items'=>$total_items,
			'per_page'=>$per_page,
			'total_pages'=>$total_pages
		);

		$this->_pagination_args = $args;


		$infinite_scroll = false;

		$output = '<span class="displaying-num">' . sprintf( _n( '%s item', '%s items', $total_items ), number_format_i18n( $total_items ) ) . '</span>';

		$current = $this->get_pagenum();
		if(defined( 'DOING_AJAX' ) && DOING_AJAX) {
			$current_url = wp_get_referer();
		}else{
			$current_url = set_url_scheme( 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] );
		}

		$current_url = remove_query_arg( array( 'hotkeys_highlight_last', 'hotkeys_highlight_first' ), $current_url );

		$page_links = array();

		$total_pages_before = '<span class="paging-input">';
		$total_pages_after  = '</span>';

		$disable_first = $disable_last = $disable_prev = $disable_next = false;

 		if ( $current == 1 ) {
			$disable_first = true;
			$disable_prev = true;
 		}
		if ( $current == 2 ) {
			$disable_first = true;
		}
 		if ( $current == $total_pages ) {
			$disable_last = true;
			$disable_next = true;
 		}
		if ( $current == $total_pages - 1 ) {
			$disable_last = true;
		}

		if ( $disable_first ) {
			$page_links[] = '<span class="tablenav-pages-navspan" aria-hidden="true">&laquo;</span>';
		} else {
			$page_links[] = sprintf( "<a class='first-page' href='%s'><span class='screen-reader-text'>%s</span><span aria-hidden='true'>%s</span></a>",
				esc_url( remove_query_arg( 'paged', $current_url ) ),
				__( 'First page' ),
				'&laquo;'
			);
		}

		if ( $disable_prev ) {
			$page_links[] = '<span class="tablenav-pages-navspan" aria-hidden="true">&lsaquo;</span>';
		} else {
			$page_links[] = sprintf( "<a class='prev-page shop-ct-link' href='%s'><span class='screen-reader-text'>%s</span><span aria-hidden='true'>%s</span></a>",
				esc_url( add_query_arg( 'paged', max( 1, $current-1 ), $current_url ) ),
				__( 'Previous page' ),
				'&lsaquo;'
			);
		}

		if ( 'bottom' === $which ) {
			$html_current_page  = $current;
			$total_pages_before = '<span class="screen-reader-text">' . __( 'Current Page' ) . '</span><span id="table-paging" class="paging-input">';
		} else {
			$html_current_page = sprintf( "%s<input class='current-page' id='current-page-selector' type='text' name='paged' value='%s' size='%d' aria-describedby='table-paging' />",
				'<label for="current-page-selector" class="screen-reader-text">' . __( 'Current Page' ) . '</label>',
				$current,
				strlen( $total_pages )
			);
		}
		$html_total_pages = sprintf( "<span class='total-pages'>%s</span>", number_format_i18n( $total_pages ) );
		$page_links[] = $total_pages_before . sprintf( _x( '%1$s of %2$s', 'paging' ), $html_current_page, $html_total_pages ) . $total_pages_after;

		if ( $disable_next ) {
			$page_links[] = '<span class="tablenav-pages-navspan" aria-hidden="true">&rsaquo;</span>';
		} else {
			$page_links[] = sprintf( "<a class='next-page shop-ct-link' href='%s'><span class='screen-reader-text'>%s</span><span aria-hidden='true'>%s</span></a>",
				esc_url( add_query_arg( 'paged', min( $total_pages, $current+1 ), $current_url ) ),
				__( 'Next page' ),
				'&rsaquo;'
			);
		}

		if ( $disable_last ) {
			$page_links[] = '<span class="tablenav-pages-navspan" aria-hidden="true">&raquo;</span>';
		} else {
			$page_links[] = sprintf( "<a class='last-page shop-ct-link' href='%s'><span class='screen-reader-text'>%s</span><span aria-hidden='true'>%s</span></a>",
				esc_url( add_query_arg( 'paged', $total_pages, $current_url ) ),
				__( 'Last page' ),
				'&raquo;'
			);
		}

		$pagination_links_class = 'pagination-links';
		if ( ! empty( $infinite_scroll ) ) {
			$pagination_links_class = ' hide-if-js';
		}
		$output .= "\n<span class='$pagination_links_class'>" . join( "\n", $page_links ) . '</span>';

		if ( $total_pages ) {
			$page_class = $total_pages < 2 ? ' one-page' : '';
		} else {
			$page_class = ' no-pages';
		}
		$this->_pagination = "<div class='tablenav-pages{$page_class}'>$output</div>";

		echo $this->_pagination;
	}

	public function display_tablenav($which){
		if($which == "top"){
			?>
			<div class="tablenav <?php echo esc_attr( $which ); ?>" >
				<?php
				if($this->show_bulk_actions){
					?>
					<div class="alignleft actions bulkactions">
						<?php $this->get_bulk_actions( $which ); ?>
					</div>
					<?php
				}
				if($this->show_filters){
					?>
					<div class="alignleft actions">
						<?php $this->extra_actions( $which ); ?>
					</div>
					<?php
				}
				if($this->show_search_box == true){
					?>
					<div class="alignleft actions">
						<?php $this->search_box($this->labels['search']); ?>
					</div>
					<?php
				}
				$this->pagination( $which );
				?>
			</div>
			<?php
		}elseif($which == "bottom"){
			?>
			<div class="tablenav <?php echo esc_attr( $which ); ?>" >
				<?php
				if($this->show_bulk_actions){
					?>
					<div class="alignleft actions bulkactions">
						<?php $this->get_bulk_actions( $which ); ?>
					</div>
					<?php
				}
				$this->pagination( $which );
				?>
			</div>
			<?php
		}else{
			return null;
		}
	}

	public function page_nav(){
		$labels = $this->get_labels();
		$ids = $this->get_ids();
		$classes = $this->get_classes();
		?>
		<h1><?php echo $labels['page_title']; ?> <a href="#" id="<?php echo $ids['add_new_item']; ?>" class="mat-button mat-button--primary <?php echo $classes['add_new_item']; ?>"><?php echo $labels['add_new_item']; ?></a></h1>
		<?php
		$this->views();
	}

	public function print_column_headers($with_id = true){
		if(!empty($this->columns)){
			$columns = $this->columns;

			$hidden = get_option("shop_ct_hidden_columns_".$this->items_object_type);

			if(defined( 'DOING_AJAX' ) && DOING_AJAX) {
				$current_url = wp_get_referer();
			}else{
				$current_url = set_url_scheme( 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] );
			}

			$current_url = remove_query_arg( array('paged','slug','action','task','nonce'), $current_url );

			if(!$hidden){
				$hidden=array();
			}

			if ( isset( $_GET['orderby'] ) ) {
				$current_orderby = $_GET['orderby'];
			} else {
				$current_orderby = '';
			}

			if ( isset( $_GET['order'] ) && 'desc' === $_GET['order'] ) {
				$current_order = 'desc';
			} else {
				$current_order = 'asc';
			}

			if ( ! empty( $columns['cb'] ) ) {
				static $cb_counter = 1;
				$columns['cb']['name'] = '<label class="screen-reader-text" for="cb-select-all-' . $cb_counter . '">' . __( 'Select All' ) . '</label>'
					. '<input class="shop-ct-col-checkbox" id="cb-select-all-' . $cb_counter . '" type="checkbox" />';
				$cb_counter++;
			}

			foreach($columns as $column_key => $column){

				$column_display_name = $column['name'];
				$column_sortable = $column['sortable'];
				if(isset($column['primary'])){
					$primary = $column['primary'];
				} else {
					$primary = false;
				}

				$class = array( 'manage-column', "column-$column_key" );

				if ( in_array( $column_key, $hidden ) ) {
					$class[] = 'hidden';
				}

				if ( 'cb' === $column_key ){
					$class[] = 'shop-ct-check-column';
					$class[] = 'check-column';
				} elseif ( in_array( $column_key, array( 'posts', 'comments', 'links' ) ) ){
					$class[] = 'num';
				}

				if ( true === $primary ) {
					$class[] = 'column-primary';
				}

				if ( $column_sortable == true ) {
					$orderby = $column_key;
					if ( $current_orderby === $orderby ) {
						$order = 'asc' === $current_order ? 'desc' : 'asc';
						$class[] = 'sorted';
						$class[] = $current_order;
					} else {
						$order = 'desc' ;
						$class[] = 'sortable';
						$class[] =  'asc' ;
					}
					$column_display_name = '<a href="' . esc_url( add_query_arg( compact( 'orderby', 'order' ), $current_url ) ) . '"><span>' . $column_display_name . '</span><span class="sorting-indicator"></span></a>';
				}

				$tag = ( 'cb' === $column_key ) ? 'td' : 'th';
				$colspan = ( 'title' === $column_key ) ? 'colspan="2"' : '';
				$scope = ( 'th' === $tag ) ? 'scope="col"' : '';
				$id = $with_id ? "id='$column_key'" : '';

				if ( !empty( $class ) )
					$class = "class='" . join( ' ', $class ) . "'";

				echo "<$tag $scope $id $class $colspan >$column_display_name</$tag>";
			}
		}
	}

	public function get_total_pages(){
		$resource_type = $this->items_resource_type;

		$per_page = $this->per_page;
		$total_items = $this->get_total_items_count();

		$total_pages = ceil( $total_items / $per_page );
		$this->total_pages = $total_pages;
		return $total_pages;


	}

	public function filter_items_args($args,$resource_type){
		$total_pages = $this->total_pages;
		if ( $resource_type == "post_type" ) {
			if ( ! isset( $args['posts_per_page'] ) ) {
				$per_page = $this->per_page;
				if ( $per_page ) {
					$args['posts_per_page'] = $per_page;
				} else {
					$args['posts_per_page'] = 20;
				}

			}

			if ( ! isset( $args['offset'] ) ) {
				if ( isset( $_GET['paged'] ) ) {
					if ( $_GET['paged'] > 0 ) {
						if ( $_GET['paged'] < $total_pages ) {
							$args['paged'] = $_GET['paged'];
						} else {
							if ( $total_pages == 1 ) {
								$args['paged'] = "";
							} else {
								$args['paged'] = $total_pages;
							}
						}
					} else {
						$args['paged'] = "";
					}

				}
			}

			if(!isset($args['category']) && isset($_GET['cat'])){
                $args['tax_query'] = array(
                  array(
                          'taxonomy' => Shop_CT_Product_Category::get_taxonomy(),
                      'field' => 'term_id',
                      'terms' => $_GET['cat']
                  )
                );
			}

			if(!isset($args['shop_ct_product_tag']) && isset($_GET['tag'])){
				$args['shop_ct_product_tag'] = $_GET['tag'];
			}

			if(!isset($args['m']) && isset($_GET['m'])){
				$args['m'] = (int)$_GET['m'];
			}

			if(!isset($args['orderby']) && isset($_GET['orderby'])){
				if($_GET['orderby'] != 'shop_ct_product_rating'){
					$args['orderby'] = strtoupper($_GET['orderby']);
				}else{
					$args['orderby'] = 'meta_value';
					$args['meta_key'] = 'shop_ct_product_rating';
				}
				$args['orderby'] = $_GET['orderby'];
			}elseif(!isset($args['orderby'])){
				$args['orderby'] = 'date';
			}

			if(!isset($args['order']) && isset($_GET['order'])){
				$args['order'] = strtoupper($_GET['order']);
			}elseif(!isset($args['order'])){
				$args['order'] = 'DESC';
			}

			if(!isset($args['post_type'])){
				$args['post_type'] = $this->items_object_type;
			}

			if(!isset($args['post_status'])){
				if(isset($_GET['post_status'])){
					$args['post_status'] = $_GET['post_status'];
				}else{
					$args['post_status'] = "any";
				}


			}

			if(!isset($args['s'])){
				if(isset($_GET['s'])){
					$args['s'] = $_GET['s'];
				}else{
					$args['s'] = "";
				}


			}
			return $args;
		}elseif($resource_type == "taxonomy"){
			if(!isset($args['number'])){
				$per_page = $this->per_page;
				if($per_page){
					$args['number'] = $per_page;
				}else{
					$args['number'] = 20;
				}

			}

			if ( ! isset( $args['offset'] ) ) {
				if ( isset( $_GET['paged'] ) ) {
					if ( $_GET['paged'] > 0 ) {
						if ( $_GET['paged'] < $total_pages ) {
							$args['offset'] = $_GET['paged'];
						} else {
							$args['offset'] = $total_pages;
						}
					} else {
						$args['offset'] = 1;
					}
				}
			}

			if(!isset($args['orderby']) && isset($_GET['orderby'])){
				$args['orderby'] = $_GET['orderby'];
			}

			if(!isset($args['order']) && isset($_GET['order'])){
				$args['order'] = strtoupper($_GET['order']);
			}

			if(!isset($args['hide_empty'])){
				$args['hide_empty'] = false;
			}
			if(!isset($args['search'])){
				if(isset($_GET['s'])){
					$args['search'] = $_GET['s'];
				}else{
					$args['search'] = "";
				}
			}
			return $args;
		}elseif( $resource_type == 'custom' ){
			if( method_exists( $this, "filter_items_args_" . $this->items_object_type ) ){
				return call_user_func(
					array( $this, 'filter_items_args_' . $this->items_object_type ),
					$args,
					$resource_type
				);
			}
		}else{
			return $args;
		}
	}

	public function column_cb( $post ) {
		if($this->items_resource_type == "post_type"){
		?>
			<input class="shop-ct-col-checkbox" type="checkbox" id="cb-select-<?php echo $post->ID; ?>" name="post[]" value="<?php echo $post->ID; ?>" />
			<div class="locked-indicator"></div>
		<?php
	}elseif($this->items_resource_type == "taxonomy"){
		?>
			<input class="shop-ct-col-checkbox" type="checkbox" id="cb-select-<?php echo $post->term_id; ?>" name="term[]" value="<?php echo $post->term_id; ?>" />
			<div class="locked-indicator"></div>
		<?php
		} elseif($this->items_resource_type == 'comment_type') {
	    /** @var Shop_CT_Product_Review $post */
		?>
			<input class="shop-ct-col-checkbox" type="checkbox" id="cb-select-<?php echo $post->get_id(); ?>" name="comment[]" value="<?php echo $post->get_id(); ?>" />
			<div class="locked-indicator"></div>
		<?php
		}
	}

	/**
	 * Handles the post date column output.
	 *
	 * @param WP_Post $post The current WP_Post object.
	 */
	public function column_date( $post ) {
		$mode = 'list';
		if ( '0000-00-00 00:00:00' === $post->post_date ) {
			$t_time = $h_time = __( 'Unpublished' );
			$time_diff = 0;
		} else {
			$t_time = get_the_time( __( 'Y/m/d g:i:s a' ) );
			$m_time = $post->post_date;
			$time = get_post_time( 'G', true, $post );

			$time_diff = time() - $time;

			if ( $time_diff > 0 && $time_diff < DAY_IN_SECONDS ) {
				$h_time = sprintf( __( '%s ago' ), human_time_diff( $time ) );
			} else {
				$h_time = mysql2date( __( 'Y/m/d' ), $m_time );
			}
		}

		if ( 'publish' === $post->post_status ) {
			_e( 'Published' );
		} elseif ( 'future' === $post->post_status ) {
			if ( $time_diff > 0 ) {
				echo '<strong class="error-message">' . __( 'Missed schedule' ) . '</strong>';
			} else {
				_e( 'Scheduled' );
			}
		} else {
			_e( 'Last Modified' );
		}
		echo '<br />';
		if ( 'excerpt' === $mode ) {

			echo apply_filters( 'post_date_column_time', $t_time, $post, 'date', $mode );
		} else {
			echo '<abbr title="' . $t_time . '">' . apply_filters( 'post_date_column_time', $h_time, $post, 'date', $mode ) . '</abbr>';
		}
	}



	public function column_default( $post, $column_name ) {
		if ( 'categories' === $column_name ) {
			$taxonomy = Shop_CT_Product_Category::get_taxonomy();
		} elseif ( 'tags' === $column_name ) {
			$taxonomy = Shop_CT_Product_Tag::get_taxonomy();
		} elseif ( 0 === strpos( $column_name, 'taxonomy-' ) ) {
			$taxonomy = substr( $column_name, 9 );
		} else {
			$taxonomy = false;
		}
		if ( $taxonomy ) {
			$taxonomy_object = get_taxonomy( $taxonomy );
			$terms = get_the_terms( $post->ID, $taxonomy );
			if ( is_array( $terms ) ) {
				$out = array();
				foreach ( $terms as $t ) {
					$posts_in_term_qv = array();
					if ( $taxonomy_object->query_var ) {
						if ($taxonomy_object->query_var == Shop_CT_Product_Category::get_taxonomy()) $posts_in_term_qv[ 'cat' ] = $t->slug;
						elseif ($taxonomy_object->query_var == Shop_CT_Product_Tag::get_taxonomy()) $posts_in_term_qv[ 'tag' ] = $t->slug;
						else $posts_in_term_qv[ $taxonomy_object->query_var ] = $t->slug;
					} else {
						$posts_in_term_qv['taxonomy'] = $taxonomy;
						$posts_in_term_qv['term'] = $t->slug;
					}

					$label = esc_html( sanitize_term_field( 'name', $t->name, $t->term_id, $taxonomy, 'display' ) );
					$out[] = $this->get_edit_link( $posts_in_term_qv, $label );
				}
				echo join( __( ', ' ), $out );
			} else {
				echo '<span aria-hidden="true">&#8212;</span><span class="screen-reader-text">' . $taxonomy_object->labels->no_terms . '</span>';
			}
			return;
		}

		if ( is_post_type_hierarchical( $post->post_type ) ) {

			do_action( 'shop_ct_manage_pages_custom_column', $column_name, $post->ID );
		} else {

			do_action( 'shop_ct_manage_posts_custom_column', $column_name, $post->ID );
		}
		do_action( "shop_ct_manage_{$post->post_type}_posts_custom_column", $column_name, $post->ID );
	}

    public function column_shortcode($post)
    {
        if ($post instanceof WP_Term) {
            switch ($post->taxonomy) {
                case Shop_CT_Product_Category::get_taxonomy():
                    $category = new Shop_CT_Product_Category($post->term_id);
                    return '<input type="text" class="shop_ct_shortcode_input" value="'.esc_attr($category->get_shortcode()).'" readonly />';
                    break;
            }
        }
	}

	/**
	 * @param $post
	 * @param $column_name
	 * @param $primary
	 * @return string
     */
	protected function handle_row_actions($post, $column_name, $primary){
		if($this->items_resource_type == "post_type"){
			if($primary != true){
				return '';
			}

			$post_type_object = get_post_type_object( $post->post_type );
			$can_edit_post = current_user_can( 'edit_posts' );
			$actions = array();

			if ( $can_edit_post && 'trash' != $post->post_status ) {
				$actions['edit'] = '<a href="' . get_edit_post_link( $post->ID ) . '" title="' . esc_attr__( 'Edit this item' ) . '">' . __( 'Edit' ) . '</a>';
			}

			if ( current_user_can( 'edit_posts' ) ) {
				if ( 'trash' === $post->post_status )
					$actions['untrash'] = "<a title='" . esc_attr__( 'Restore this item from the Trash' ) . "' href='" . wp_nonce_url( admin_url( sprintf( $post_type_object->_edit_link . '&amp;action=untrash', $post->ID ) ), 'untrash-post_' . $post->ID ) . "'>" . __( 'Restore' ) . "</a>";
				elseif ( EMPTY_TRASH_DAYS )
					$actions['trash'] = "<a class='submitdelete' title='" . esc_attr__( 'Move this item to the Trash' ) . "' href='" . get_delete_post_link( $post->ID ) . "'>" . __( 'Trash' ) . "</a>";
				if ( 'trash' === $post->post_status || !EMPTY_TRASH_DAYS )
					$actions['delete'] = "<a class='submitdelete' title='" . esc_attr__( 'Delete this item permanently' ) . "' href='" . get_delete_post_link( $post->ID, '', true ) . "'>" . __( 'Delete Permanently' ) . "</a>";
			}

			if ( is_post_type_viewable( $post_type_object ) ) {
				$title = _draft_or_post_title();
				if ( in_array( $post->post_status, array( 'pending', 'draft', 'future' ) ) ) {
					if ( $can_edit_post ) {
						$unpublished_link = set_url_scheme( get_permalink( $post ) );
						$preview_link = get_preview_post_link( $post, array(), $unpublished_link );
						$actions['view'] = '<a target="_blank" href="' . esc_url( $preview_link ) . '" title="' . esc_attr( sprintf( __( 'Preview &#8220;%s&#8221;' ), $title ) ) . '" rel="permalink">' . __( 'Preview' ) . '</a>';
					}
				} elseif ( 'trash' != $post->post_status ) {
					$actions['view'] = '<a target="_blank" href="' . get_permalink( $post->ID ) . '" title="' . esc_attr( sprintf( __( 'View &#8220;%s&#8221;' ), $title ) ) . '" rel="permalink">' . __( 'View' ) . '</a>';
				}
			}

			if ( is_post_type_hierarchical( $post->post_type ) ) {

				$actions = apply_filters( 'shop_ct_page_row_actions', $actions, $post );
			} else {

				$actions = apply_filters( 'shop_ct_post_row_actions', $actions, $post );
			}

			return $this->row_actions( $actions );

		} elseif($this->items_resource_type == "comment_type") {

			if($primary != true){
				return '';
			}

		} elseif( $this->items_resource_type == "custom" ){
			if( is_array( $this->row_actions ) && !empty( $this->row_actions ) ){
				$actions = array();
				foreach( $this->row_actions as $action => $name ){
					$actions[$action] = "<a class='" . $action . "' title='" . $name . "' href='#' >" . $name . "</a>";
				}
				return $this->row_actions( $actions );
			}
		} else{
			$tag = $post;
			$taxonomy = $this->items_object_type;
			$tax = get_taxonomy( $taxonomy );
			$default_term = get_option( 'default_' . $taxonomy );

			$uri = ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ? wp_get_referer() : $_SERVER['REQUEST_URI'];

			$edit_link = add_query_arg(
				'wp_http_referer',
				urlencode( wp_unslash( $uri ) ),
				get_edit_term_link( $tag->term_id, $taxonomy, 'shop_ct_product' )
			);

			$actions = array();
			if ( current_user_can( $tax->cap->edit_terms ) ) {
				if(isset($this->row_actions['edit'])){
					$actions['edit'] = '<a href="' . esc_url( $edit_link ) . '">' . __( 'Edit' ) . '</a>';
				}
				if(isset($this->row_actions['inline'])){
					$actions['inline hide-if-no-js'] = '<a href="#" class="editinline">' . __( 'Quick&nbsp;Edit' ) . '</a>';
				}
			}
			if ( current_user_can( $tax->cap->delete_terms ) && $tag->term_id != $default_term ){
				if(isset($this->row_actions['delete'])){
					$actions['delete'] = "<a class='delete-tag' href='" . wp_nonce_url( "edit-tags.php?action=delete&amp;taxonomy=$taxonomy&amp;tag_ID=$tag->term_id", 'delete-tag_' . $tag->term_id ) . "'>" . __( 'Delete' ) . "</a>";
				}
			}
			if ( $tax->public ){
				if(isset($this->row_actions['view'])){
					$actions['view'] = '<a target="_blank" href="' . get_term_link( $tag ) . '">' . __( 'View' ) . '</a>';
				}

			}


			$actions = apply_filters( 'shop_ct_tag_row_actions', $actions, $tag );

			$actions = apply_filters( "shop_ct_{$taxonomy}_row_actions", $actions, $tag );

			return $this->row_actions( $actions );
		}
	}

	protected function row_actions( $actions, $always_visible = false ) {
		$action_count = count( $actions );
		$i = 0;

		if ( !$action_count )
			return '';

		$out = '<div class="' . ( $always_visible ? 'row-actions visible' : 'row-actions' ) . '">';
		foreach ( $actions as $action => $link ) {
			++$i;
			( $i == $action_count ) ? $sep = '' : $sep = ' | ';
			$out .= "<span class='$action'>$link$sep</span>";
		}
		$out .= '</div>';

		$out .= '<button type="button" class="toggle-row"><span class="screen-reader-text">' . __( 'Show more details' ) . '</span></button>';

		return $out;
	}

	public function column_title($post){
		if($this->items_resource_type == "post_type"){

			$pad ="";

			$status = $post->post_status;

			$title = _draft_or_post_title($post->ID);

			echo '<strong>';

			$can_edit_post = current_user_can( 'edit_posts' );
			$title = _draft_or_post_title($post->ID);

			if ( $can_edit_post && $post->post_status != 'trash' ) {
				$edit_link = get_edit_post_link( $post->ID );
				echo '<a class="row-title" href="' . $edit_link . '" title="' . esc_attr( sprintf( __( 'Edit &#8220;%s&#8221;' ), $title ) ) . '">' . $pad . $title . '</a>';
			} else {
				echo $pad . $title;
			}

			_post_states( $post );

			echo '</strong>';
			echo '<div class="locked-info"></div>';

		}
	}

	protected function _column_title( $post, $classes, $data, $primary ) {
		echo '<td class="' . $classes . ' page-title" ', $data, ' colspan="2" >';
		echo $this->column_title( $post );
		echo $this->handle_row_actions( $post, 'title', $primary );
		echo '</td>';
	}

	protected function column_featured_image($post){
		if($this->items_resource_type == "post_type"){
		    if ($this->items_object_type === 'shop_ct_product') {
		        $product = new Shop_CT_Product($post->ID);
		        $thumbnail_url = $product->get_image_url();
            }else{
                $thumbnail_url = wp_get_attachment_url( get_post_thumbnail_id($post->ID) );

                if(!is_string($thumbnail_url)){
                    $thumbnail_url = trailingslashit( SHOP_CT()->plugin_url() ) . "assets/images/placeholder.png";
                }
            }


			$alt = $post->post_title;

		}else{
			$term = $post;
			$thumbnail_id = get_term_meta($term->term_id,'thumbnail_id',true);

			if($thumbnail_id){
				$thumbnail_url = wp_get_attachment_url($thumbnail_id);
			}else{
				$thumbnail_url = trailingslashit( SHOP_CT()->plugin_url() ) . "assets/images/placeholder.png";
			}

			$alt = $term->name;

		}
		echo '<img src="'. $thumbnail_url .'" width="50" alt="'. $alt .'" />';
	}

	protected function _column_featured_img($post, $classes, $data, $primary){
		echo '<td class="' . $classes . ' page-title" '. $data. ' >';
		$this->column_featured_image( $post );
		echo '</td>';
	}

	protected function column_description($item)
    {
        $description = $item->description;
        $words_array = explode(' ', $description);
        if(count($words_array)>10){
            $description = implode(' ', array_slice($words_array, 0, 10));
            $description .= '...';
        }

        echo $description;
    }

	protected function column_name($tag){
		if($this->items_resource_type == "taxonomy"){
			$taxonomy = $this->items_object_type;
			$pad = str_repeat( '&#8212; ', max( 0, $this->level ) );
			$name = apply_filters( 'shop_ct_term_name', $pad . ' ' . $tag->name, $tag );

			$qe_data = get_term( $tag->term_id, $taxonomy, OBJECT, 'edit' );

			$uri = ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ? wp_get_referer() : $_SERVER['REQUEST_URI'];
			$edit_link = add_query_arg(
				'wp_http_referer',
				urlencode( wp_unslash( $uri ) ),
				get_edit_term_link( $tag->term_id, $taxonomy, "shop_ct_product")
			);

			$out = '<strong><a class="row-title" href="' . esc_url( $edit_link ) . '" title="' . esc_attr( sprintf( __( 'Edit &#8220;%s&#8221;' ), $name ) ) . '">' . $name . '</a></strong><br />';

			$out .= '<div class="hidden" id="inline_' . $qe_data->term_id . '">';
			$out .= '<div class="name">' . $qe_data->name . '</div>';

			/** This filter is documented in wp-admin/edit-tag-form.php */
			$out .= '<div class="slug">' . apply_filters( 'editable_slug', $qe_data->slug, $qe_data ) . '</div>';
			$out .= '<div class="parent">' . $qe_data->parent . '</div></div>';

			return $out;
		}
	}

	protected function _column_name($post, $classes, $data, $primary){
		echo '<td class="' . $classes . ' page-title" ', $data, '>';
		echo $this->column_name( $post );
		echo $this->handle_row_actions( $post, 'title', $primary );
		echo '</td>';
	}

	public function single_post_row_columns($item,$resource_type){
		$columns = $this->columns;
		$hidden = get_option("shop_ct_hidden_columns_".$this->items_resource_type);

		foreach($columns as $column_name => $column){
			$column_display_name = $column['name'];
			$column_sortable = $column['sortable'];
			if(isset($column['primary'])){
				$primary = $column['primary'];
			}else{
				$primary = false;
			}

			$classes = "$column_name column-$column_name";
			if ( $primary === true ) {
				$classes .= ' has-row-actions column-primary';
			}

			if (!empty($hidden) && in_array( $column_name, $hidden ) ) {
				$classes .= ' hidden';
			}

			$data = 'data-colname="' . wp_strip_all_tags( $column_display_name ) . '"';

			if( $column_name == 'featured_img' ){
				$data = 'data-colname="' . __('Featured Image','shop_ct') . '"';
			}

			$attributes = "class='$classes' $data";


			if($this->items_resource_type == "post_type"){
				$default_columns = array('ID');
			}elseif($this->items_resource_type == "taxonomy"){
				$default_columns = array('term_id','id','slug','count');
			} elseif ( $this->items_resource_type == "comment_type" ) {
				$default_columns = array();
			}elseif ( $this->items_resource_type == "custom" ){
				$default_columns = array();
			}

			if ( 'cb' == $column_name ) {
				echo '<th scope="row" class="shop-ct-check-column check-column">';
				$this->column_cb( $item );
				echo '</th>';
			}elseif(in_array($column_name,$default_columns)){
				echo '<td class="' . $classes . ' page-title" ', $data, '>';
				echo $item->$column_name;
				echo '</td>';
			} elseif ( method_exists( $this, '_column_' . $column_name ) ) {
				echo call_user_func(
					array( $this, '_column_' . $column_name ),
					$item,
					$classes,
					$data,
					$primary
				);
			} elseif ( method_exists( $this, 'column_' . $column_name ) ) {
				echo "<td $attributes>";
				echo call_user_func( array( $this, 'column_' . $column_name ), $item );
				echo "</td>";
			}else{
				echo "<td $attributes>";
				$this->column_default( $item, $column_name );
				echo "</td>";
			}
		}
	}

	public function get_column_count(){
		return count($this->columns);
	}

	public function get_all_items(){
		$resource_type = $this->items_resource_type;
		$args = $this->items_object_args;
		$args = $this->filter_items_args($args,$resource_type);

		if($resource_type == "post_type"){
			if(isset($args['offset'])){
				$args['offset']=0;
			}
			if(isset($args['category'])){
				$args['category']='';
			}

			if(isset($args['posts_per_page'])){
				$args['posts_per_page']=-1;
			}

			$items = get_posts($args);
		}elseif($resource_type == "taxonomy"){
			$taxonomy = $this->items_object_type;

			if(isset($args['hide_empty'])){
				$args['hide_empty']=false;
			}
			if(isset($args['number'])){
				$args['number']='';
			}

			if(isset($args['offset'])){
				$args['offset']=0;
			}

			if(isset($args['parent'])){
				$args['parent']=0;
			}
			$items = get_terms($taxonomy,$args);
		} elseif ( $resource_type == 'comment_type' ) {
			$items = get_comments($args);
		} elseif ( $resource_type == "custom" ){
			$items = array();
		}

		return $items;
	}

	public function get_total_items_count(){
		return count($this->get_all_items());
	}

	public function get_items(){
		$resource_type = $this->items_resource_type;
		$args = $this->items_object_args;
		$args = $this->filter_items_args($args,$resource_type);
		if($resource_type == 'post_type'){
			$items = get_posts($args);
		}elseif($resource_type == "taxonomy"){
			$taxonomy = $this->items_object_type;
			$items=get_terms($taxonomy,$args);
		} elseif($resource_type == 'comment_type') {
			$items = Shop_CT_Product_Review::get_all();//get_comments($this->items_object_args);
		}elseif ( $resource_type == "custom" ){
			$items = array();
		}

		return $items;
	}

	public function display_rows_or_placeholder(){

		if($this->items_resource_type == "post_type"){
			$items = $this->get_items();
			if(!empty($items)){
				foreach ( $items as $item ){
					echo '<tr>';
					$this->single_post_row_columns( $item, $this->items_resource_type );
					echo '</tr>';
				}
			}else{
				echo '<tr class="no-items"><td class="colspanchange" colspan="' . $this->get_column_count() . '">';
				_e( 'No items found.' );
				echo '</td></tr>';
			}
		} elseif($this->items_resource_type == "comment_type") {
			$items = $this->get_items();

			if( !empty($items) ) {

				foreach ( $items as $item ){
					echo '<tr>';
					$this->single_post_row_columns( $item, $this->items_resource_type );
					echo '</tr>';
				}

			} else {

				echo '<tr class="no-items"><td class="colspanchange" colspan="' . $this->get_column_count() . '">';
				_e( 'No items found.' );
				echo '</td></tr>';

			}
		}elseif( $this->items_resource_type == 'custom' ) {
			$items = $this->get_items();

			if( !empty($items) ) {

				foreach ( $items as $item ) {
					echo '<tr>';
					$this->single_post_row_columns( $item, $this->items_resource_type );
					echo '</tr>';
				}

			}else{
				echo '<tr class="no-items"><td class="colspanchange" colspan="' . $this->get_column_count() . '">';
				_e( 'No items found.' );
				echo '</td></tr>';
			}
		} else {
			$taxonomy = $this->items_object_type;

			$args = $this->items_object_args;
			$args = $this->filter_items_args($args,$this->items_resource_type);

			$args = wp_parse_args( $args, array(
				'page' => 1,
				'number' => 20,
				'search' => '',
				'hide_empty' => 0
			) );

			$page = $args['page'];

			// Set variable because $args['number'] can be subsequently overridden.
			$number = $args['number'];

			$args['offset'] = $offset = ( $page - 1 ) * $number;

			// Convert it to table rows.
			$count = 0;

			if ( is_taxonomy_hierarchical( $taxonomy ) && ! isset( $args['orderby'] ) ) {
				// We'll need the full set of terms then.
				$args['number'] = $args['offset'] = 0;
			}
			$terms = get_terms( $taxonomy, $args );

			if ( empty( $terms ) || ! is_array( $terms ) ) {
				echo '<tr class="no-items"><td class="colspanchange" colspan="' . $this->get_column_count() . '">';
				_e( 'No items found.' );
				echo '</td></tr>';
				return;
			}

			if ( is_taxonomy_hierarchical( $taxonomy ) && ! isset( $args['orderby'] ) ) {
				if ( ! empty( $args['search'] ) ) {// Ignore children on searches.
					$children = array();
				} else {
					$children = _get_term_hierarchy( $taxonomy );
				}
				// Some funky recursion to get the job done( Paging & parents mainly ) is contained within, Skip it for non-hierarchical taxonomies for performance sake
				$this->terms_rows( $taxonomy, $terms, $children, $offset, $number, $count );
			} else {
				foreach ( $terms as $term ) {
					$this->single_term_row($term);
				}
			}
		}
	}

	public function single_term_row($term,$level = 0){
		$tag = sanitize_term( $term, $this->items_object_type);
		$this->level = $level;
		echo '<tr id="tag-' . $term->term_id . '">';
		$this->single_post_row_columns( $term,$this->items_resource_type );
		echo '</tr>';
	}


	protected function terms_rows( $taxonomy, $terms, &$children, $start, $per_page, &$count, $parent = 0, $level = 0 ) {

		$end = $start + $per_page;

		foreach ( $terms as $key => $term ) {

			if ( $count >= $end )
				break;

			if ( $term->parent != $parent && empty( $_REQUEST['s'] ) )
				continue;

			// If the page starts in a subtree, print the parents.
			if ( $count == $start && $term->parent > 0 && empty( $_REQUEST['s'] ) ) {
				$my_parents = $parent_ids = array();
				$p = $term->parent;
				while ( $p ) {
					$my_parent = get_term( $p, $taxonomy );
					$my_parents[] = $my_parent;
					$p = $my_parent->parent;
					if ( in_array( $p, $parent_ids ) ) // Prevent parent loops.
						break;
					$parent_ids[] = $p;
				}
				unset( $parent_ids );

				$num_parents = count( $my_parents );
				while ( $my_parent = array_pop( $my_parents ) ) {
					echo "\t";
					$this->single_term_row( $my_parent, $level - $num_parents );
					$num_parents--;
				}
			}

			if ( $count >= $start ) {
				echo "\t";
				$this->single_term_row( $term, $level );
			}

			++$count;

			unset( $terms[$key] );
			if ( isset( $children[$term->term_id] ) && empty( $_REQUEST['s'] ) )
				$this->terms_rows( $taxonomy, $terms, $children, $start, $per_page, $count, $term->term_id, $level + 1 );
		}
	}

	/**
	 * @access public
	 */

	public function display(){
		if($this->form_id){
			$form_id=$this->form_id;
		}else{
			$form_id="posts-filter";
		}
		if($this->display_nav):
		?>
		<div class="wrap">
			<?php
			$this->page_nav();
			?>
			<form id="<?php echo $form_id; ?>" class="shop_ct_list_table_form" method="get">
				<?php
				if(isset($_GET['page'])){
					?>
					<input type="hidden" name="page" id="shop_ct_nav_page" value="<?php echo $_GET['page']; ?>" />
					<?php
				}
				if(isset($_GET['shop_ct_path'])){
					?>
					<input type="hidden" name="shop_ct_path" id="shop_ct_nav_path" value="<?php echo $_GET['shop_ct_path']; ?>" />
					<?php
				}


				$this->display_tablenav('top');
				?>
				<table id="inline-table" class="wp-list-table widefat fixed striped <?php echo implode( ' ', $this->table_classes );echo implode(' ',$this->get_table_classes()); ?>">

					<thead>
						<tr>
							<?php $this->print_column_headers(); ?>
						</tr>
					</thead>
					<tbody id="the-list">
						<?php $this->display_rows_or_placeholder(); ?>
					</tbody>
					<tfoot>
						<tr>
							<?php $this->print_column_headers( false ); ?>
						</tr>
					</tfoot>
				</table>
				<?php $this->display_tablenav('bottom'); ?>
			</form>
		</div>
		<?php
		else:
		?>
		<table class="wp-list-table widefat striped <?php echo implode( ' ', $this->table_classes );echo implode(' ',$this->get_table_classes()); ?>">
			<thead>
				<tr>
					<?php $this->print_column_headers(); ?>
				</tr>
			</thead>
			<tbody id="the-list">
				<?php $this->display_rows_or_placeholder(); ?>
			</tbody>
			<tfoot>
				<tr>
					<?php $this->print_column_headers( false ); ?>
				</tr>
			</tfoot>
		</table>
		<?php 
		endif;

	}

	/**
	 * Get a list of CSS classes for the list table table tag.
	 *
	 * @since 1.0.0
	 * @access protected
	 *
	 * @return array List of CSS classes for the table tag.
	 */
	protected function get_table_classes() {
		return array( 'widefat', 'fixed', 'striped');
	}
}