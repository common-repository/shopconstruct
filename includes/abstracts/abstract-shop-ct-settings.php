<?php

/**
 * @class Shop_CT_Settings
 */
class Shop_CT_Settings {

	/**
	 * The plugin ID. Used for option names.
	 * @var string
	 */
	public $plugin_id = 'shop_ct';

	/** @var string ID of form to handle settings */
	public $form_id = 'shop_ct_settings_form';

	/** @var string method for form ( get/post ) */
	public $method = 'post';

	/** @var string ID of settings */
	public $id = null;

	/** @var  array Array of controls to display */
	public $controls = array();

	/** @var string Determine if the page has a quick navigation for sections */
	public $navigation = 'no';

	/** @var  array Sections of controls */
	public $sections = array();

	/** @var  array default values for controls */
	public $defaults;

	/**
	 * @var array
	 */
	protected static $_instance;


	/**
	 * Возвращает экземпляр класса, из которого вызван
	 *
	 * @return object|array
	 */
	public static function instance() {
		$className = static::getClassName();
		if ( ! ( static::$_instance instanceof $className ) ) {
            static::$_instance = new $className();
		}

		return static::$_instance;
	}

	final protected static function getClassName() {
		return get_called_class();
	}


	/**
	 * Initialize admin and display the page
	 */
	public static function init_admin() {
		$instance = static::instance();
		return $instance->display();
	}

	/** @var array Array of panels */
	public $panels = array();

	public function __get( $key ) {
		if ( $this->id ) {
			$this->$key = get_option( $this->plugin_id . "_" . $this->id . "_" . $key, false );
		} else {
			$this->$key = get_option( $this->plugin_id . "_" . $key, false );
		}

		return $this->$key;
	}

	/**
	 * @param $key
	 * @param bool $default
	 * @param bool $concat
	 *
	 * @return mixed
	 */
	public function get_option( $key, $default = false, $concat = true ) {
		if ( ! $concat ) {
            return get_option( $key, $default );
		} else {
            if ( $this->id ) {
                return get_option( $this->plugin_id . "_" . $this->id . "_" . $key, $default );
            } else {
                return get_option( $this->plugin_id . "_" . $key, $default );
            }
		}
	}

	public function control_color( $id, $control ){
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
            $label_str = '';
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
        <input <?php echo implode( ' ', $attrs ); ?> type="text" class="shop_ct_setting control-value jscolor" id="shop_ct_<?php echo $id; ?>" name="<?php echo $id; ?>" <?php echo $placeholder; ?> value="<?php echo $default; ?>" />
        <label for="shop_ct_<?php echo $id; ?>" >
            <?php echo $label_str; ?>
        </label>
        <?php
        echo $description_str;
    }

	public function control_page_select( $id, $control ) {

		$pages = get_pages(
			array(
				'posts_per_page' => - 1,
			)
		);

		$pages_array = array();

		foreach ( $pages as $page ) {
			$pages_array[ $page->ID ] = $page->post_title;
		}

		$select_control = array(
			'search'  => 'yes',
			'choices' => $pages_array,
		);

		$this->control_select( $id, array_merge( $control, $select_control ) );
	}

	/**
	 * @param $id
	 * @param $control
	 */
	protected function control_radio( $id, $control ) {
		$default = isset( $control['default'] ) ? $control['default'] : '';

		$description     = isset( $control['description'] ) ? $control['description'] : "";
		$description_str = $description != "" ? '<p class="description">' . $description . '</p>' : '';
		$label           = isset( $control['label'] ) ? $control['label'] : '';


		echo '<div class="radio-block"><ul>';
		if ( isset( $control['choices'] ) && ! empty( $control['choices'] ) ) {
			foreach ( $control['choices'] as $key => $choice ) {
			    ?>
                <li>
                    <input class="control-value" type="radio" id="shop_ct_<?php echo $id; ?>_<?php echo $key; ?>" name="<?php echo $id; ?>" value="<?php echo $key; ?>" <?php checked( $key, $default, true ); ?> />
                    <label for="shop_ct_<?php echo $id; ?>_<?php echo $key; ?>"><span class="radicon"></span><?php echo $choice; ?></label>
                </li>
                <?php
			}
		}
		echo '</ul></div>';
        echo '<span class="control_title">' . $label . '</span>';
		echo $description_str;
	}

	/**
	 * @param $id
	 * @param $control
	 */
	protected function control_submit( $id, $control ) {
		$label = isset( $control['label'] ) ? $control['label'] : __( 'Save', 'shop_ct' );
		submit_button( $label, array(
			'shop_ct_save_settings',
			'primary'
		), 'shop_ct_' . $id, false, array( 'id' => 'shop_ct_' . $id, 'class' => '' ) );
	}

	/**
	 * @param $id
	 * @param $control
	 */
	protected function control_textarea( $id, $control ) {
		$default         = isset( $control['default'] ) ? $control['default'] : '';
		$description     = isset( $control['description'] ) ? $control['description'] : "";
		$description_str = $description != "" ? '<p class="desciprttion">' . $description . '</p>' : '';
		$label_str       = isset( $control['label'] ) ? '<span class="control_title" > ' . $control['label'] . ' </span>' : '';
		$attrs           = array();
		if ( isset( $control['attrs'] ) && ! empty( $control['attrs'] ) ) {
			foreach ( $control['attrs'] as $k => $attr ) {
				$attrs[] = $k . '=' . $attr;
			}
		}
		?>
        <textarea <?php echo implode( ' ', $attrs ); ?> id="shop_ct_<?php echo $id; ?>"
                                                        class="shop_ct_setting control-value"
                                                        name="<?php echo $id; ?>"><?php echo wp_unslash( $default ); ?></textarea>
        <label for="shop_ct_<?php echo $id; ?>"><?php echo $label_str; ?></label>
        <?php echo $description_str; ?>
		<div class="clear"></div>
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
				array_merge( $classes, explode( $control['class'] ) );
			} elseif ( is_array( $control['class'] ) ) {
				array_merge( $classes, $control['class'] );
			}
		}

		if ( isset( $control['search'] ) && in_array( $control['search'], array( true, 'yes' ) ) ) {
			$classes[] = 'select2';
		}
		$select_attrs = array();
        $select_attrs['multiple'] = 'no';
		if ( isset( $control['multiple'] ) && in_array( $control['multiple'], array( true, 'yes' ) ) ) {
			$select_attrs['multiple'] = 'yes';
			if ( ! in_array( 'select2', $classes ) ) {
				$classes[] = 'select2';
			}
		}
		if ( isset( $control['attrs'] ) && ! empty( $control['attrs'] ) ) {
			foreach ( $control['attrs'] as $k => $attr ) {
				$select_attrs[] = $k . '=' . $attr;
			}
		}
		?>
		<div class="shop_ct_mui_select">
			<label class="shop_ct_mui_label_block">
				<?php echo $label_str; ?>
				<?php
				if ( isset( $control['countries'] ) && $control['countries'] == 'yes' ) {
					echo SHOP_CT()->locations->get_all_countries_dropdown( array(
						"selected" => $default,
						"id"       => "shop_ct_" . $id,
						"name"     => $id,
						"multiple" => $select_attrs['multiple'],
						'class'    => 'shop_ct_setting'
					) );
					echo '<div class="shop_ct_mui_select_bar"></div><div class="cb"></div>';
					echo $description_str;
					echo "</label></div>";
					return;
				}
				?>
				<select <?php echo implode( ' ', $select_attrs ); ?>
					class="shop_ct_setting control-value <?php echo implode( ' ', $classes ); ?>" id="shop_ct_<?php echo $id; ?>"
					name="<?php echo $id . ( $select_attrs['multiple'] === 'yes' ? '[]' : '' ); ?>">
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
							if ( is_array( $default ) ) {
								$selected = in_array( $key, $default ) ? 'selected="selected"' : '';
							} else {
								$selected = selected( $key, $default, false );
							}
							echo '<option ' . implode( ' ', $attrs ) . ' value="' . esc_attr( $key ) . '" ' . $selected . ' >' . $html . '</option>';
						}
					}
					?>
				</select>
				<div class="shop_ct_mui_select_bar"></div><div class="cb"></div>
				<?php echo $description_str; ?>
			</label>
		</div>
		<?php
	}

	/**
	 * @param $id
	 * @param $control
	 */
	protected function control_checkbox( $id, $control ) {
		$default         = isset( $control['default'] ) ? $control['default'] : "";
		$description     = isset( $control['description'] ) ? $control['description'] : "";
		$description_str = $description != "" ? '<p class="description">' . $description . '</p>' : '';
		$label           = isset( $control['label'] ) ? $control['label'] : "";
		$label_str       = $label != "" ? '<span class="control_title" > ' . $label . ' </span>' : '';
		if ( is_string( $default ) ) {
			if ( $default === "yes" || $default === "no" ) {
				$checked = checked(  'yes', $default, false );
				$def_val = 'yes';
                $false_val = 'no';
			} else {
				$checked = checked( 'true', $default, false );
				$def_val = 'true';
                $false_val = 'false';
			}
		} else {
			$checked = checked( $default, true, false );
			$def_val = 'true';
            $false_val = 'false';
		}
		$attrs = array();
		if ( isset( $control['attrs'] ) && ! empty( $control['attrs'] ) ) {
			foreach ( $control['attrs'] as $k => $attr ) {
				$attrs[] = $k . '=' . $attr;
			}
		}
		if ( isset( $control['grouped'] ) && $control['grouped'] == 'yes' && is_array( $control['choices'] ) && ! empty( $control['choices'] ) ): ?>
			<div class="checkbox-block">
                <ul>
				<?php
				foreach ( $control['choices'] as $key => $val ) {
					$default         = isset( $val['default'] ) ? $val['default'] : "";
					$label           = isset( $val['label'] ) ? $val['label'] : "";
					$description     = isset( $val['description'] ) ? $val['description'] : "";
					$description_str = $description != "" ? '<span class="description">' . $description . '</span>' : '';
					if ( is_string( $default ) ) {
						if ( $default === "yes" || $default === "no" ) {
							$checked = checked( 'yes', $default, false );
							$def_val = 'yes';
							$false_val = 'no';
						} else {
							$checked = checked( 'true', $default, false );
							$def_val = 'true';
                            $false_val = 'false';
						}
					} else {
						$checked = checked( $default, true, false );
						$def_val = 'true';
                        $false_val = 'false';
					}
					?>
					<li>
                        <input type="hidden" name="<?php echo $key; ?>" value="<?php echo $false_val; ?>" />
						<input <?php echo implode( '', $attrs ); ?> type="checkbox" name="<?php echo $key; ?>"
						                                            id="shop_ct_<?php echo $key; ?>"
						                                            value="<?php echo $def_val; ?>" <?php echo $checked; ?> />
						<label for="shop_ct_<?php echo $key; ?>">
							<span class="icon"></span>
							<?php echo $label; ?>
						</label>
						<?php echo $description_str; ?>
					</li>
					<?php
				}
				?>
                </ul>
			</div>
		<?php else: ?>
            <div class="checkbox-block">
                <input type="hidden" name="<?php echo $id; ?>" value="<?php echo $false_val; ?>" />
                    <input <?php echo implode( ' ', $attrs ); ?> type="checkbox" class="shop_ct_setting control-value"
                                                                 id="shop_ct_<?php echo $id; ?>" name="<?php echo $id; ?>"
                                                                 value="<?php echo $def_val; ?>" <?php echo $checked; ?> />
                    <label for="shop_ct_<?php echo $id; ?>">
                        <span class="icon"></span>
                    </label>
	                <?php echo $description_str; ?>
            </div>
			<?php
		endif;
		echo $label_str;
		echo '<span class="clear"></span>';
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
			$label_str = '';
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
        <input <?php echo implode( ' ', $attrs ); ?> type="text" class="shop_ct_setting control-value" id="shop_ct_<?php echo $id; ?>" name="<?php echo $id; ?>" <?php echo $placeholder; ?> value="<?php echo wp_unslash( $default ); ?>" />
        <label for="shop_ct_<?php echo $id; ?>" >
            <?php echo $label_str; ?>
        </label>
        <?php
        echo $description_str;
	}

	/**
	 * @param $id
	 * @param $control
	 */
	public function single_control( $id, $control ) {
		$type = ( isset( $control['type'] ) ? $control['type'] : "text" );

		$default = ( isset( $control['default'] ) ? $control['default'] : "" );

		if ( isset( $control['placeholder'] ) ) {
			$placeholder = 'placeholder="' . $control['placeholder'] . '"';
		} else {
			$placeholder = '';
		}

		$html_class = isset( $control['html_class'] ) ? $control['html_class'] : array();

		if ( is_string( $html_class ) ) {
			explode( ' ', $html_class );
		}
		$html_class_str  = implode( ' ', $html_class );
		$label_str       = ( isset( $control['label'] ) ? '<span class="control_title"> ' . $control['label'] . ' </span>' : '' );
		$description     = isset( $control['description'] ) ? $control['description'] : "";
		$description_str = $description != "" ? '<p class="description">' . $description . '</p>' : '';

		$help = false;

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
			?>
			<div id="control-container-<?php echo $id; ?>" class="control_container control-container-<?php echo $type.' '.implode( ' ', $html_class ); ?>">

                <input <?php echo implode( ' ', $attrs ); ?> type="<?php echo $type; ?>" class="shop_ct_setting control-value" id="shop_ct_<?php echo $id; ?>" name="<?php echo $id; ?>" <?php echo $placeholder; ?> value="<?php echo $default; ?>" />
                <label class="shop_ct_mui_text <?php echo ( $help ? 'shop_ct_mui_help_icon' : '' ); ?>" >
	                <?php echo $label_str; ?>
                </label>
				<?php echo $description_str; ?>
			</div>
			<?php
		}
	}

	/**
	 * The Panels navigation
	 */
	public function navigation() {
		echo '<div>';
		echo '<ul id="' . $this->id . '_navigation" class="shop_ct_settings_navigation flat subsubsub shop_ct_section_navigation">';
		foreach ( $this->panels as $panel_id => $panel ) {
			echo '<li rel="' . $panel_id . '" ' . ( $panel == reset( $this->panels ) ? 'class="active"' : '' ) . '>';
			echo '<span class="shop_ct_mui_nav_sub">' . $panel['title'] . '</span>';
			echo '</li>';
		}
		echo '</ul>';
		echo '<div class="clear"></div>';
		echo '</div>';
	}

	/**
	 * Main function to display settings page
	 */
	public function display() {

		if ( ! empty( $this->controls ) ) {
			?>
			<div class="shop_ct_settings">
				<form class="shop_ct_mui_form shop_ct_settings_form" id="<?php echo $this->form_id; ?>" action=""
				      method="post">
					<?php
					if ( empty( $this->panels ) ) {
						$this->panels = array(
							'main' => array(
								'priority' => 1
							),
						);
					}
					if ( $this->navigation == 'yes' ) {
						$this->navigation();
					}
					foreach ( $this->panels as $panel_id => $panel ) {
						echo '<section id="' . $panel_id . '" class="shop_ct_wrapper shop_ct_section shop_ct' . ( $this->navigation == 'yes' ? '_hidden' : '' ) . '_section ' . ( $this->navigation == 'yes' && $panel == reset( $this->panels ) ? 'active' : '' ) . '"" >';
						foreach ( $this->sections as $section_id => $section ) {
							if ( isset( $section['panel'] ) && $section['panel'] != $panel_id ) {
								continue;
							}
							if ( isset( $section['title'] ) && ! empty( $section['title'] ) ) {
								echo '<h2>' . $section['title'] . '</h2>';
							}
							if ( isset( $section['description'] ) && ! empty( $section['description'] ) ) {
								echo '<p>' . $section['description'] . '</p>';
							}
							foreach ( $this->controls as $control_id => $control ) {
								if ( $control['section'] == $section_id ) {
									$this->single_control( $control_id, $control );
								}
							}
						}
						echo '</section>';
					}
					?>
					<div class="submit_block">
                        <button type="submit" class="mat-button mat-button--primary shop_ct_save_settings"><?php _e( 'Save', 'shop_ct' ); ?></button>
					</div>
				</form>
			</div>
			<?php
		}
	}

}