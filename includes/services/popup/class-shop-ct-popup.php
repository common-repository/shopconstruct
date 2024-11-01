<?php
class Shop_CT_popup {

	/**
	* @access public
	*/
	public $form_classes = array();

	/**
	 * @access public
	 */
	public $sections = array();

	/**
	 * @access public
	 */
	public $controls = array();

	/**
	 * @access public
	 */
	public $registered_control_types = array();

	/**
	 * @access public
	 */
	public $two_column = true;

	/**
	 * @access public
	 */
	public $form_id = "";

	/**
	 * @access public
	 */
	public $display_form = true;

	/**
	 * @var string Custom Content, this is printed if display_form is false
	 */
	public $custom_content = "";


	/**
	 * Shop_CT_popup constructor.
	 * @param array $args
	 */
	public function __construct( $args = array() ) {
		add_action( 'wp_loaded', array( $this, 'wp_loaded' ) );
	}

	/**
	 * @access public
	 */
	public function wp_loaded() {
		do_action( 'shop_ct_popup_register', $this );
	}

	/**
	 * Hidden input control
	 * @param $id
	 * @param $control
	 */
	protected function _control_hidden( $id, $control ) {
		$default = ( isset( $control['default'] ) ? $control['default'] : "" );
		?>
		<input type="hidden" id="popup-control-<?php echo $id; ?>" name="<?php echo $id; ?>" class="popup-control-value"
			   value="<?php echo $default; ?>"/>
		<?php
	}

	/**
	 * @param $id
	 * @param $control
	 */
	protected function control_taxonomy_dropdown( $id, $control ) {
		$description     = isset( $control['description'] ) ? $control['description'] : "";
		$description_str = $description != "" ? '<p class="description">' . $description . '</p>' : '';
		$default   = ( isset( $control['default'] ) ? $control['default'] : 0 );
		$exclude   = ( isset( $control['exclude'] ) ? $control['exclude'] : array() );
		$label     = ( isset( $control['label'] ) ? $control['label'] : '' );
		$label_str = ( ! empty( $label ) ? '<span class="control_title" > ' . $label . ' </span>' : '' );
		$taxonomy  = ( isset( $control['taxonomy'] ) ? $control['taxonomy'] : Shop_CT_Product_Category::get_taxonomy() );
		$args      = ( isset( $control['args'] ) ? $control['args'] : array() );
		$classes   = array( "popup-control-value" );
		if ( isset( $control['search'] ) ) {
			$classes[] = "select2";
		}
		$args = wp_parse_args( $args, array(
			'show_option_all'   => '',
			'show_option_none'  => __( "Select Parent", "shop_ct" ),
			'option_none_value' => '-1',
			'orderby'           => 'ID',
			'exclude'           => $exclude,
			'order'             => 'ASC',
			'show_count'        => 0,
			'hide_empty'        => 0,
			'echo'              => 1,
			'selected'          => $default,
			'hierarchical'      => 1,
			'name'              => $id,
			'id'                => 'popup-control-' . $id,
			'class'             => implode( ' ', $classes ),
			'taxonomy'          => $taxonomy,
			'hide_if_empty'     => false,
			'value_field'       => 'term_id',
		) ); ?>
		<label>
			<?php echo $label_str; ?>
			<?php wp_dropdown_categories( $args ); ?>
			<?php echo $description_str; ?>
		</label>
		<?php
	}

    /**
     * @param $id
     * @param $control
     */
	protected function control_tinymce( $id, $control ) {
		if ( isset( $control['default'] ) ) {
			$default = $control['default'];
		} else {
			$default = "";
		}
		?>
		<div class="shop_ct-popup-tinymce-container" id="popup-tmce-<?php echo $id; ?>-container"
			 data-id="popup-control-<?php echo $id; ?>"></div>
		<textarea class="invisible" data-id="<?php echo $id ?>" title="tinyMCE"><?php echo wp_kses_post( $default ); ?></textarea>
		<?php
	}

    /**
     * @param $id
     * @param $control
     */
	protected function control_button( $id, $control ) {
		if ( isset( $control['label'] ) ) {
			$label = $control['label'];
		} else {
			$label = __("Save","shop_ct");
		}
		?>
		<input type="button" value="<?php echo $label ?>" class="button button-primary"
			   name="<?php echo 'popup-control-' . $id ?>" id="<?php echo 'popup-control-' . $id ?>">
		<?php
	}

    /**
     * @param $id
     * @param $control
     */
	protected function control_tabs( $id, $control ) {
		if ( isset( $control['tabs'] ) && ! empty( $control['tabs'] ) ) {
			$tabs = $control['tabs'];
		} else {
			return;
		}
		?>
		<div id="popup-control-<?php echo $id; ?>-tabs" class="shop_ct_popup_tabs">
			<div class="popup-tabs-menu">
				<ul>
					<?php
					reset( $tabs );
					$first_key = key( $tabs );
					foreach ( $tabs as $tab_id => $tab ) {
						$class = ( isset( $tab['class'] ) ? $tab['class'] : array() );
						$class[] = ( $tab_id == $first_key ? 'active' : '' );
						echo '<li class="' . implode( ' ', $class ) . '"><a class="shop-ct-tabs-menu-link" href="#' . $id . '-popup-tabs-' . $tab_id . '" >' . $tab['label'] . '</a></li>';
					}
					?>
				</ul>
			</div>
			<div class="popup-tabs-main">
				<?php
				foreach ( $tabs as $tab_id => $tab ) {
					$display = ( $tab_id == $first_key ? 'block' : 'none' );
					$class = ( isset( $tab['class'] ) ? $tab['class'] : array() );
					?>
					<div class="popup-tabs-tab <?php implode( ' ', $class ); ?>"
						 style="display:<?php echo $display; ?>;" id="<?php echo $id . '-popup-tabs-' . $tab_id; ?>">
						<?php
						if ( isset( $tab['controls'] ) && ! empty( $tab['controls'] ) ) {
							foreach ( $tab['controls'] as $control_id => $control ) {
								$this->single_control( $control_id, $control );
							}
						} elseif ( isset( $tab['content'] ) ) {
							echo $tab['content'];
						}
						?>
					</div>
					<?php
				}
				?>
			</div>
			<div class="clear"></div>
		</div>
		<?php
	}

    /**
     * @todo: make this work
     * @param $id
     * @param $control
     */
	protected function _control_media( $id, $control ) {
		if ( isset( $control['default'] ) ) {
			$default = $control['default'];
		} else {
			$default = 0;
		}

		echo $default.$id;
	}

    /**
     * @param $id
     * @param $control
     */
	protected function control_image_gallery( $id, $control ) {
		if ( isset( $control['add_new_text'] ) ) {
			$add_new_text = $control['add_new_text'];
		} else {
			$add_new_text = __( "Add New Image", "shop_ct" );
		}
		if ( isset( $control['default'] ) && is_array( $control['default'] ) ) {
			$default = $control['default'];
		} else {
			$default = array();
		} ?>
		<ul class="images_list popup_image_gallery_list">
			<?php if ( ! empty( $default ) ) {
				foreach ( $default as $gallery_image ) {
					$attachment = wp_get_attachment_image_src( $gallery_image );
					?>
					<li class="image" data-attachment_id="<?php echo $gallery_image; ?>">
						<img src="<?php echo $attachment[0]; ?>" alt=""/>
						<ul class="actions">
							<li>
								<a href="#" class="delete shop-ct-delete-img-gallery" title="Delete"><?php _e( 'Delete', 'shop_ct' ); ?></a>
							</li>
						</ul>
					</li>
				<?php }
			} ?>
		</ul>
		<p><a href="#" class="add_image_gallery shop-ct-add-img-gallery"><?php echo $add_new_text; ?></a></p>
		<input type="hidden" class="popup-control-value" id="popup-control-<?php echo $id; ?>"
			   name="popup-control-<?php echo $id; ?>" value="<?php echo json_encode( $default ); ?>"/>
		<?php
	}

    /**
     * @param $id
     * @param $control
     */
	protected function control_image( $id, $control ) {
		if ( isset( $control['add_new_text'] ) ) {
			$add_new_text = $control['add_new_text'];
		} else {
			$add_new_text = __( "Set Image", "shop_ct" );
		}

		if ( isset( $control['remove_text'] ) ) {
			$remove_text = $control['remove_text'];
		} else {
			$remove_text = __( "Remove image", "shop_ct" );
		}

        $default_image = '';
		if ( isset( $control['default'] ) && ! empty( $control['default'] ) ) {
			$default = $control['default'];
			if ( is_numeric( $default ) && $default > 0 ) {
				$default_image = wp_get_attachment_url( $default );
			}
			$class = "class='remove_control_image shop-ct-img-control'";
			$text       = $remove_text;
		} else {
			$default       = "";
			$class         = "class='add_image_control shop-ct-img-control'";
			$text          = $add_new_text;
		}

		if ( isset( $control['label'] ) ) {
			$label_str = '<span class="control_title" > ' . $control['label'] . ' </span>';
		} else {
			$label_str = $label = '';
		}
		?>
		<label>
			<?php
			echo $label_str;
			?>
			<div class="image_control_image_container">
				<?php if ( ! empty( $default ) ) { ?>
					<p><a href="#" class="add_image_control shop-ct-img-control"><img src="<?php echo $default_image; ?>" alt=""/></a></p>
				<?php } ?>
				<p><a href="#" <?php echo $class ?> data-remove_text="<?php echo $remove_text; ?>"
					  data-add_new_text="<?php echo $add_new_text; ?>"><?php echo $text; ?></a></p>
				<input type="hidden" class="popup-control-value" id="popup-control-<?php echo $id; ?>"
					   name="popup-control-<?php echo $id; ?>" value="<?php echo $default ?>"/>
			</div>
		</label>
		<?php
	}

    /**
     * @param $id
     * @param $control
     */
	protected function control_submit( $id, $control ) {
		if ( isset( $control['label'] ) ) {
			$label = $control['label'];
		} else {
			$label = "Save";
		}
		?>
        <input type="submit" name="<?php echo 'popup-control-' . $id;  ?>" id="<?php echo 'popup-control-' . $id;  ?>" class="mat-button mat-button--primary" value="<?php echo $label; ?>">
        <?php
	}

    /**
     * @param $id
     * @param $control
     */
	protected function control_date( $id, $control ) {
		$description     = isset( $control['description'] ) ? $control['description'] : "";
		$description_str = $description != "" ? '<p class="description">' . $description . '</p>' : '';

		if ( isset( $control['label'] ) ) {
			$label_str = '<span class="control_title" > ' . $control['label'] . ' </span>';
		} else {
			$label_str = $label = '';
		}

		if ( isset( $control['placeholder'] ) ) {
			$placeholder = $control['placeholder'];
		} else {
			$placeholder = "";
		}

		if ( isset( $control['default'] ) && ! empty( $control['default'] ) && $control['default'] !== 'NULL' ) {
			$default_date    = date_i18n( 'Y-m-d', strtotime( $control['default'] ) );
			$default_hour    = date_i18n( 'H', strtotime( $control['default'] ) );
			$default_minutes = date_i18n( 'i', strtotime( $control['default'] ) );
		} elseif ( isset( $control['default'] ) && $control['default'] === 'NULL' ) {
			$default_date    = '';
			$default_hour    = '';
			$default_minutes = '';
			$placeholder     = isset( $control['placeholder'] ) ? $control['placeholder'] : '';
		} else {
			$default_date    = "";
			$default_hour    = "00";
			$default_minutes = "00";
		}

		if ( isset($control['product']) && $control['product'] ) {
			$class = "shop_ct_product_datepicker";
		} else {
			$class = "shop_ct_datepicker";
		}
		?>
		<label class="datepicker_control">
			<?php echo $label_str; ?>
			<input placeholder="<?php echo $placeholder; ?>" type="text" id="popup-control-date-<?php echo $id; ?>" value="<?php echo $default_date; ?>" class="<?php echo $class; ?> shop_ct_popup_date_date" />
			<?php if ( isset( $control['show_time'] ) && $control['show_time'] == true ) :
				echo "&#64;"; ?>
				<input size="2" type="text" id="popup-control-hour-<?php echo $id; ?>" class="shop_ct_popup_date_hour" value="<?php echo $default_hour; ?>" />
				<?php echo "&#58;"; ?>
				<input size="2" type="text" id="popup-control-minute-<?php echo $id; ?>" class="shop_ct_popup_date_minute" value="<?php echo $default_minutes; ?>" />
			<?php endif;
			echo $description_str; ?>
		</label>
		<?php
	}

    /**
     * @param $id
     * @param $control
     */
	protected function control_textarea( $id, $control ) {
		if ( isset( $control['default'] ) ) {
			$default = $control['default'];
		} else {
			$default = "";
		}
		$description     = isset( $control['description'] ) ? $control['description'] : "";
		$description_str = $description != "" ? '<p class="description">' . $description . '</p>' : '';
		if ( isset( $control['label'] ) ) {
			$label_str = '<span class="control_title" > ' . $control['label'] . ' </span>';
		} else {
			$label_str = $label = '';
		}
		$attrs = array();
		if ( isset( $control['attrs'] ) && ! empty( $control['attrs'] ) ) {
			foreach ( $control['attrs'] as $k => $attr ) {
				$attrs[] = $k . '=' . $attr;
			}
		}
		?>
		<label>
			<?php echo $label_str; ?>
			<textarea <?php echo implode( ' ', $attrs ); ?> id="popup-control-<?php echo $id; ?>"
															class="popup-control-value"
															name="<?php echo $id; ?>"><?php echo $default; ?></textarea>
			<?php echo $description_str; ?>
		</label>
		<?php
	}

    /**
     * @param $id
     * @param $control
     */
	protected function control_text( $id, $control ) {
		if ( isset( $control['default'] ) ) {
			$default = $control['default'];
		} else {
			$default = "";
		}
		$description     = isset( $control['description'] ) ? $control['description'] : "";
		$description_str = $description != "" ? '<p class="description">' . $description . '</p>' : '';
		if ( isset( $control['label'] ) ) {
			$label_str = '<span class="control_title" > ' . $control['label'] . ' </span>';
		} else {
			$label_str = $label = '';
		}
		if ( isset( $control['placeholder'] ) ) {
			$placeholder = 'placeholder="' . $control['placeholder'] . '"';
		} else {
			$placeholder = '';
		}
		$attrs = array();
		if ( isset( $control['attrs'] ) && ! empty( $control['attrs'] ) ) {
			foreach ( $control['attrs'] as $k => $attr ) {
				$attrs[] = $k . '=' . $attr;
			}
		}
		?>
		<label>
			<?php
			echo $label_str;
			echo '<input ' . implode( ' ', $attrs ) . ' type="text" class="popup-control-value" id="popup-control-' . $id . '" name="' . $id . '" value="' . $default . '" ' . $placeholder . ' />';
			echo $description_str;
			?>
		</label>
		<?php
	}

    /**
     * @param $id
     * @param $control
     */
	protected function control_select( $id, $control ) {
		$default = isset( $control['default'] ) ? $control['default'] : "";
		$description     = isset( $control['description'] ) ? $control['description'] : "";
		$description_str = $description != "" ? '<p class="description">' . $description . '</p>' : '';
		$label           = isset( $control['label'] ) ? $control['label'] : '';
		$label_str       = $label != '' ? '<span class="control_title">' . $label . '</span>' : '';
		$classes         = array();
		if ( isset( $control['class'] ) ) {
			if ( is_string( $control['class'] ) ) {
				array_merge( $classes, explode( $control['class'], ' ' ) );
			} elseif ( is_array( $control['class'] ) ) {
				array_merge( $classes, $control['class'] );
			}
		}
		if ( isset( $control['search'] ) && $control['search'] == true ) {
			$classes[] = 'select2';
		}
		$select_attrs = array();
		if ( isset( $control['attrs'] ) && ! empty( $control['attrs'] ) ) {
			foreach ( $control['attrs'] as $k => $attr ) {
				$select_attrs[] = $k . '=' . $attr;
			}
		}
		?>
		<label>
			<?php echo $label_str; ?>
			<?php
			if ( isset( $control['countries'] ) && $control['countries'] == 'yes' ) {
				echo SHOP_CT()->locations->get_all_countries_dropdown( array(
					"selected" => $default,
					"id"       => "shop_ct-control-" . $id,
					"name"     => $id,
					"multiple" => 'yes'
				) );
				echo "</label>";
				return;
			}
			?>
			<select <?php echo implode( ' ', $select_attrs ); ?>
				class="popup-control-value <?php echo implode( ' ', $classes ); ?>" id="shop-ct-control-<?php echo $id; ?>"
				name="<?php echo $id; ?>">
				<?php
				if ( isset( $control['choices'] ) && ! empty( $control['choices'] ) ) {
					foreach ( $control['choices'] as $key => $choice ) {
						$attrs = array();
						if ( is_array( $choice ) ) {
							$html = $choice['html'];
							if ( isset( $choice['attrs'] ) ) {
								foreach ( $choice['attrs'] as $k => $attr ) {
									$attrs[] = $k . '=' . $attr;
								}
							}
						} else {
							$html = $choice;
						}

						$selected = $default == $key || (is_array($default) && in_array($key, $default)) ? 'selected="selected"' : '';

						echo '<option ' . implode( ' ', $attrs ) . ' value="' . $key . '" ' . $selected . ' >' . $html . '</option>';
					}
				}
				?>
			</select>
			<?php echo $description_str; ?>
		</label>
		<?php
	}

    /**
     * @param $id
     * @param $control
     */
	protected function control_checkbox( $id, $control ) {
		$default         = isset( $control['default'] ) ? $control['default'] : "";
		$description     = isset( $control['description'] ) ? $control['description'] : "";
		$description_str = $description != "" ? '<span class="control_description">' . $description . '</span>' : '';
		$label           = isset( $control['label'] ) ? $control['label'] : "";
		$label_str       = $label != "" ? '<span class="control_title" > ' . $label . ' </span>' : '';
        $checked = '';
		if ( is_string( $default ) ) {
			if ( $default === "true" || $default === "false" ) {
				$checked = checked( 'true', $default, false );
			} elseif ( $default === "yes" || $default === "no" ) {
				$checked = checked( 'yes', $default,  false );
			}
		} else {
            $checked = $default ? 'checked="checked"' : '';
        }

		$attrs = array();
		if ( isset( $control['attrs'] ) && ! empty( $control['attrs'] ) ) {
			foreach ( $control['attrs'] as $k => $attr ) {
				$attrs[] = $k . '=' . $attr;
			}
		}
		?>
		<label>
			<?php echo $label_str; ?>
			<input <?php echo implode( ' ', $attrs ); ?> type="checkbox" class="popup-control-value"
														 id="popup-control-<?php echo $id; ?>" name="<?php echo $id; ?>"
														 value="true" <?php echo $checked; ?> />
			<?php echo $description_str; ?>
		</label>
		<?php
	}

    /**
     * @param $id
     * @param $control
     */
	protected function control_radio( $id, $control ) {
		if ( isset( $control['default'] ) ) {
			$default = $control['default'];
		} else {
			$default = "";
		}
		$description     = isset( $control['description'] ) ? $control['description'] : "";
		$description_str = $description != "" ? '<p class="description">' . $description . '</p>' : '';
		$label           = isset( $control['label'] ) ? $control['label'] : '';
		echo "<div>";
		echo '<span class="control_title">' . $label . '</span>';
		if ( isset( $control['choices'] ) && ! empty( $control['choices'] ) ) {
			foreach ( $control['choices'] as $key => $choice ) {
				echo '<label>';
				echo '<input class="popup-control-value" type="radio" id="popup-control-' . $id . '" name="' . $id . '" value="' . $key . '" ' . checked( $key, $default, false ) . ' />' . $choice;
				echo '</label>';
			}
		}
		echo $description_str;
		echo "</div>";
	}

    /**
     * @param $id
     * @param $control
     */
	public function single_control( $id, $control ) {
		$type = ( isset( $control['type'] ) ? $control['type'] : "text" );
		$default = ( isset( $control['default'] ) ? $control['default'] : "" );
		$placeholder = ( isset( $control['placeholder'] ) ? $control['placeholder'] : '' );
		$html_class = isset( $control['html_class'] ) ? $control['html_class'] : array();
		if ( is_string( $html_class ) ) {
			explode( ' ', $html_class );
		}
		$html_class_str = implode( ' ', $html_class );
		$label_str      = ( isset( $control['label'] ) ? '<span class="control_title"> ' . $control['label'] . ' </span>' : '' );
		if ( method_exists( $this, '_control_' . $type ) ) {
			echo call_user_func(
				array( $this, '_control_' . $type ),
				$id,
				$control
			);
		} elseif ( method_exists( $this, 'control_' . $type ) ) {
			echo '<div id="control-container-' . $id . '" class="control_container control-container-' . $type . ' ' . $html_class_str . '"    >';
			echo call_user_func(
				array( $this, 'control_' . $type ),
				$id,
				$control
			);
			echo '</div>';
		} else {
			$attrs = array();
			if ( isset( $control['attrs'] ) && ! empty( $control['attrs'] ) ) {
				foreach ( $control['attrs'] as $k => $attr ) {
					$attrs[] = $k . '=' . $attr;
				}
			}
			echo '<div id="control-container-' . $id . '" class="control_container control-container-' . $type . ' ' . implode( ' ', $html_class ) . '">';
			echo '<label>';
			echo $label_str;
			echo '<input ' . implode( ' ', $attrs ) . ' type="' . $type . '" class="popup-control-value" id="popup-control-' . $id . '" name="' . $id . '" value="' . $default . '" ' . $placeholder . ' />';
			echo '</label>';
			echo '</div>';
		}
	}

    /**
     * @param $section_id
     * @param $section
     */
	public function single_section( $section_id, $section ) {
		$class = array( "shop_ct_popup_section" );
		$type  = "default";
		if ( isset( $section['type'] ) ) {
			$type = $section['type'];
			if ( $section['type'] == 'dropdown' ) {
				$class[] = "dropdown_section active";
			}
		}
		$has_tabs   = false;
		$has_submit = false;
		foreach ( $this->controls as $id => $control ) {
			if ( isset( $control['section'] ) && $control['section'] == $section_id ) {
				if ( isset( $control['type'] ) ) {
					if ( $control['type'] == 'tabs' ) {
						$has_tabs = true;
					}
					if ( $control['type'] == 'submit' || $control['type'] == 'post_submit' ) {
						$has_submit = true;
					}
				}
			}
		}
		if ( $has_submit ) {
			$class[] = "popup_section_submit";
		}
		if ( $has_tabs ) {
			$class[] = "popup_section_tabs";
		}
		$title = "";
		if ( isset( $section['title'] ) ) {
			$title = $section['title'];
		}
		if ( isset( $section['class'] ) ) {
			if ( is_array( $section['class'] ) ) {
				array_merge( $class, $section['class'] );
			} else {
				$class[] = $section['class'];
			}
		}
		?>
		<div class="<?php echo implode( ' ', $class ); ?>" id="shop-ct-popup-section-<?php echo $section_id; ?>">
			<?php
			$controls = array();
			if ( $type == "dropdown" ) : ?>

            <div type="button" class="shop_ct_popup_section_toggle_btn shop-ct-toggle-button">
                <span class="screen-reader-text">Toggle panel: Custom Fields</span>
                <span class="toggle_indicator"></span>
            </div>
            <h2 class="section_title"><?php echo $title; ?></h2>
            <div class="inside">

            <?php endif;

            foreach ( $this->controls as $id => $control ) {
                if ( isset( $control['section'] ) && $control['section'] == $section_id ) {
                    $this->single_control( $id, $control );
                    $controls[ $id ] = $control;
                }
            }
            if ( $type == "dropdown" ) : ?>
				<div class="clear"></div>
			</div>
		    <?php endif; ?>
		</div>
		<?php
	}

	/**
	 * Displays a single column
	 *
	 * @access public
	 *
	 * @param $which
	 */
	public function column( $which ) {
		$sections = $this->sections;
		if ( $which != "single" ) {
			$priorities = array();
			foreach ( $sections as $id => $section ) {
				$priorities[ $id ] = $section['priority'];
			}
			array_multisort( $priorities, SORT_ASC, $sections );
			foreach ( $sections as $id => $section ) {
				if ( $section['column'] == $which ) {
					$this->single_section( $id, $section );
				}
			}
			/*
			*/
		} else {
			$priorities = array();
			foreach ( $sections as $id => $section ) {
				$priorities[ $id ] = $section['priority'];
			}
			array_multisort( $priorities, SORT_ASC, $sections );
			foreach ( $sections as $id => $section ) {
				$this->single_section( $id, $section );
			}
		}
	}

	/**
	 * Displays columns in the form
	 *
	 * @access public
	 */
	public function columns() {
		if ( $this->two_column ) {
			?>
			<div class="form_column left_column">
				<?php $this->column( "left" ); ?>
			</div>
			<div class="form_column right_column">
				<?php $this->column( "right" ); ?>
			</div>
			<?php
		} else {
			?>
			<div class="form_column single_column">
				<?php $this->column( "single" ); ?>
			</div>
			<?php
		}
	}

	/**
	 * @access public
	 */
	public function content() {
		echo "custom content";
	}

	/**
	 * Main function called to display the popup content
	 *
	 * @access public
	 */
	public function display() {
		if ( $this->display_form ) {
			?>
			<form class=" <?php echo implode( ' ', $this->get_form_classes() ); ?>" id="<?php echo $this->form_id; ?>">
				<div class="shop_ct_popup_inner_body">
					<?php $this->columns(); ?>
				</div>
			</form>
			<?php
		} else {
			?>
			<div class="shop_ct_popup_inner_body">
				<?php $this->content(); ?>
			</div>
			<?php
		}
	}

	/**
	 * @access public
	 */
	public function get_form_classes() {
		$classes = array( 'shop_ct_popup_form' );
		if ( $this->two_column ) {
			$classes[] = "columns-2";
		}
		$classes = array_merge( $classes, $this->form_classes );
		return $classes;
	}
}