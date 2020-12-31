<?php

namespace A3Rev\RIH;

class Main {

	public function __construct () {
		$this->init();
	}

	public function init () {
		add_action( 'init',array( $this,'customize_options'), 2 );
		add_action( 'widgets_init',array( $this,'customize_options'), 2 );
		add_action( 'responsi_after_setup_theme', array( $this,'responsi_build_css_theme_actived') );
		add_filter( 'responsi_google_webfonts', array( $this,'responsi_google_webfonts'));
		add_action( 'wp_enqueue_scripts',  array( $this, 'customize_preview_inline_style'), 11 );
		add_action( 'customize_save_after', array( $this, 'responsi_customize_save_options') );
		add_action( 'responsi_build_dynamic_css_success', array( $this,'_do_dynamic_css') );
		add_filter( 'responsi-animate', array( $this, 'responsi_animate'), 10 );

		add_action( 'responsi_wrapper_header_before', array( $this, 'ih_ob_start'), 12 );
		add_action( 'responsi_wrapper_header_after', array( $this, 'ih_ob_clean'), 99 );

		add_action( 'responsi_wrapper_nav_before', array( $this, 'ih_ob_start'), 9 );
		add_action( 'responsi_wrapper_nav_after', array( $this, 'ih_ob_clean'), 9 );

		add_action('customize_controls_enqueue_scripts', array(
            $this,
            'customize_controls_enqueue_scripts'
        ), 11);
	}

	public function responsi_animate( $responsi_animate ){
		
		if( true != $responsi_animate ){

			global $responsi_options_ih;
			
			$animateOpLists = array(
	        	'responsi_ih_animation',
		    );

		    if( is_array( $animateOpLists ) && count( $animateOpLists ) > 0 ){
		    	foreach ( $animateOpLists as $value) {
		    		if( isset( $responsi_options_ih[ $value ] ) && is_array( $responsi_options_ih[ $value ] ) ){
		    			if( isset( $responsi_options_ih[ $value ]['type'] ) && $responsi_options_ih[ $value ]['type'] != 'none' ){
		    				$responsi_animate = true;
		    			}
		    		}
		    	}
		    }
		}

		return $responsi_animate;
	}

	public function customize_controls_enqueue_scripts()
    {
        $suffix = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '' : '.min';
        wp_enqueue_script( 'responsi-ih-customize', RESPONSI_IH_URL . '/customize/js/customize.logic' . $suffix . '.js', array( 'jquery', 'customize-controls' ), '1.1.5', 1 );
    }

	public function _add_filter_default_settings_options(){
		return true;
	}

	public function _do_dynamic_css( $post_value ){
		global $responsi_options;
		$this->responsi_dynamic_css();
	}

	public function responsi_customize_save_options( $settings ) {

		$slug = 'ih';

	    global $wp_customize;

		$post_value = array();

		if( isset($_POST['customized']) ){
			$post_value = json_decode( wp_unslash( $_POST['customized'] ), true );
			$post_value = apply_filters( 'responsi_customized_post_value', $post_value );
		}else{
			$post_value = $settings->changeset_data();
			$post_value = apply_filters( 'responsi_customized_changeset_data_value', $post_value );
		}

		if( is_array( $GLOBALS['responsi_options_' . $slug] ) && count( $GLOBALS['responsi_options_' . $slug] ) > 0 && is_array( $post_value ) && count( $post_value ) > 0 ){
			
			add_filter( 'default_settings_' . $slug, array( $this, '_add_filter_default_settings_options' ) );
			
			$_default_options = responsi_default_options( $slug );

			if ( defined( str_replace("-","_", get_stylesheet() ) . '_' . $slug ) ) {
				if ( function_exists('default_option_child_theme') ){
					$_default_options = array_replace_recursive( $_default_options, default_option_child_theme() );
				}
			}

			if( is_array( $_default_options ) && count( $_default_options ) > 0 ){
				
				$new_options = get_option( $slug . '_'.get_stylesheet(), array() );

				$build_sass = false;

				if( is_array( $new_options ) ){
					
					if ( is_array($post_value) ) {
						
						if ( is_object( $post_value ) ){
			                $post_value = clone $post_value;
			            }

			            foreach( $post_value as $key => $value ){
							if( array_key_exists( $key, $_default_options ) ){

								if( is_array( $value ) && isset( $new_options[$key] ) && is_array( $new_options[$key] ) ){
									$new_options[$key] = array_merge( $new_options[$key], $value );
								}else{
									$new_options[$key] = $value;
								}
								$build_sass = true;
							}
						}

						$_customize_options = array_replace_recursive( $GLOBALS['responsi_options_' . $slug], $post_value );
						foreach( $_customize_options as $key => $value ){
							if( array_key_exists( $key, $_default_options )){
								if( isset( $new_options[$key] ) ){
									if( is_array( $new_options[$key] ) && is_array( $_default_options[$key] ) ){
										$new_opt = array_diff_assoc( $new_options[$key], $_default_options[$key] );
										if( is_array( $new_opt ) && count( $new_opt ) > 0 ){
											$new_options[$key] = $new_opt;
										}else{
											unset($new_options[$key]);
										}
									}else{
										if( !is_array( $new_options[$key] ) && !is_array($_default_options[$key]) && $new_options[$key] == $_default_options[$key] ){
											unset($new_options[$key]);
										}
									}
								}
								delete_option( $key );
							}
						}
					}
				}

				if( $wp_customize && !$wp_customize->is_theme_active()){
					$build_sass = true;
				}

				if( $build_sass ) {
					update_option( $slug . '_'.get_stylesheet(), $new_options );
					$this->responsi_dynamic_css();
					do_action( $slug . '_build_dynamic_css_success', $post_value );
				}
			}
		}
	}

	public function responsi_dynamic_css() {
	    $dynamic_css      = '';
	    $dynamic_css = $this->responsi_build_dynamic_css();
	    if ( '' !== $dynamic_css ) {
	        set_theme_mod( 'ih_custom_css', $dynamic_css );
	    }
	}

	public function customize_options(){

		$slug = 'ih';

	    global $wp_customize;

	    if( !function_exists('responsi_default_options') ){
	    	return;
	    }

	    $_childthemes = get_stylesheet();

	    $_default_options = responsi_default_options( $slug );

	    $_customize_options = $_default_options;

		if ( defined( str_replace("-","_", $_childthemes ) . '_' . $slug ) ) {
			if ( function_exists('default_option_child_theme') ){
				$_customize_options = array_replace_recursive( $_customize_options, default_option_child_theme() );
			}
		}

		$responsi_mods = get_option( $slug . '_'.$_childthemes, array() );

	    if( is_array( $responsi_mods ) ){
	        $_customize_options = array_replace_recursive( $_customize_options, $responsi_mods );
	    }
	    
	    if( 'responsi-blank-child' == $_childthemes ){

	    	if( function_exists('_blank_child_customize_options')){
	    		$_customize_options = _blank_child_customize_options( $slug, $_customize_options, $_default_options );
	    	}else{
	    		$responsi_mods = get_option( $slug .'_responsi', array() );
		        $responsi_blank_child =  get_option( $slug . '_responsi-blank-child', array() );
		        if( is_array($responsi_mods) ){
		            $_customize_options = array_replace_recursive( $_customize_options, $responsi_mods );
		        }
		        if( is_array($responsi_blank_child) ){
		            $_customize_options = array_replace_recursive( $_customize_options, $responsi_blank_child );
		        }
	    	}
	    }

	    if( is_customize_preview() && ( isset( $_REQUEST['changeset_uuid'] ) || isset( $_REQUEST['customize_changeset_uuid'] ) ) ){
	        $changeset_data = $wp_customize->changeset_data();
	        if ( is_array($changeset_data) ) {
	            if( count( $changeset_data ) > 0 ){
	                $_customize_options_preview = array();
	                foreach ( $changeset_data as $setting_id => $setting_params ){
	                    if ( ! array_key_exists( 'value', $setting_params ) ) {
	                        continue;
	                    }
	                    if ( isset( $setting_params['type'] ) && 'theme_mod' === $setting_params['type'] ) {
							$namespace_pattern = '/^(?P<stylesheet>.+?)::(?P<setting_id>.+)$/';
							if ( preg_match( $namespace_pattern, $setting_id, $matches ) && get_stylesheet() === $matches['stylesheet'] ) {
								$_customize_options_preview[ $matches['setting_id'] ] = $setting_params['value'];
							}
						} else {
							$_customize_options_preview[ $setting_id ] = $setting_params['value'];
						}
	                }
	                $_customize_options_preview = apply_filters( 'responsi_customized_post_value', $_customize_options_preview );
	                if ( is_array($_customize_options) && is_array( $_customize_options_preview ) && count( $_customize_options_preview ) > 0 ) {
	                    if ( is_object( $_customize_options_preview ) ){
	                        $_customize_options_preview = clone $_customize_options_preview;
	                    }
	                    $_customize_options = array_replace_recursive( $_customize_options, $_customize_options_preview );
	                }
	            }
	        }
	    }

	    if (isset($_POST['customized'])) {
	        
	        $post_value = json_decode(wp_unslash($_POST['customized']), true);
	        $post_value = apply_filters('responsi_customized_post_value', $post_value);
	        if ( is_array($_customize_options) && is_array($post_value) ) {
	            if ( is_object( $post_value ) ){
	                $post_value = clone $post_value;
	            }
	            $_customize_options = array_replace_recursive($_customize_options, $post_value);
	        }

	    }

	    $GLOBALS['responsi_options_' . $slug] = $_customize_options;

	    return $GLOBALS['responsi_options_' . $slug];
	}

	public function responsi_build_dynamic_css( $preview = false ) {

		global $responsi_options, $responsi_options_ih;

	    if( !function_exists('responsi_default_options') ){
	    	return;
	    }

	    /*if ( !$preview ) {
	        $responsi_options_ih = $this->customize_options();
	    } else {
	        global $responsi_options_ih;
	    }*/
	    
	    if (!is_array($responsi_options_ih)) {
            return '';
        }

        /* Header */
	    $header_bg                              = isset( $responsi_options['responsi_header_bg'] ) ? $responsi_options['responsi_header_bg'] : array( 'onoff' => 'false', 'color' => '#ffffff' );
	    $enable_header_bg_image                 = isset( $responsi_options['responsi_enable_header_bg_image'] ) ? esc_attr( $responsi_options['responsi_enable_header_bg_image'] ) : 'false';
	    $header_bg_image                        = isset( $responsi_options['responsi_header_bg_image'] ) ? str_replace( array( 'https:', 'http:' ), '', esc_url( $responsi_options['responsi_header_bg_image'] ) ) : '';
	    $header_bg_image_repeat                 = isset( $responsi_options['responsi_header_bg_image_repeat'] ) ? esc_attr( $responsi_options['responsi_header_bg_image_repeat'] ) : 'repeat';
	    $header_border_top                      = isset( $responsi_options['responsi_header_border_top'] ) ? $responsi_options['responsi_header_border_top'] : array('width' => '0','style' => 'solid','color' => '#DBDBDB');
	    $header_border_bottom                   = isset( $responsi_options['responsi_header_border_bottom'] ) ? $responsi_options['responsi_header_border_bottom'] : array('width' => '0','style' => 'solid','color' => '#DBDBDB');
	    $header_border_lr                       = isset( $responsi_options['responsi_header_border_lr'] ) ? $responsi_options['responsi_header_border_lr'] : array('width' => '0','style' => 'solid','color' => '#DBDBDB');
	    $header_box_shadow_option               = isset( $responsi_options['responsi_header_box_shadow'] ) ? $responsi_options['responsi_header_box_shadow'] : array( 'onoff' => 'false' , 'h_shadow' => '0px' , 'v_shadow' => '0px', 'blur' => '5px' , 'spread' => '0px', 'color' => '#DBDBDB', 'inset' => '' );
	    $header_box_shadow                      = responsi_generate_box_shadow( $header_box_shadow_option );
	    $header_padding_top                     = isset( $responsi_options['responsi_header_padding_top'] ) ? esc_attr( $responsi_options['responsi_header_padding_top'] ) : 0;
	    $header_padding_bottom                  = isset( $responsi_options['responsi_header_padding_bottom'] ) ? esc_attr( $responsi_options['responsi_header_padding_bottom'] ) : 0;
	    $header_padding_left                    = isset( $responsi_options['responsi_header_padding_left'] ) ? esc_attr( $responsi_options['responsi_header_padding_left'] ) : 0;
	    $header_padding_right                   = isset( $responsi_options['responsi_header_padding_right'] ) ? esc_attr( $responsi_options['responsi_header_padding_right'] ) : 0;
	    $header_margin_top                      = isset( $responsi_options['responsi_header_margin_top'] ) ? esc_attr( $responsi_options['responsi_header_margin_top'] ) : 0;
	    $header_margin_bottom                   = isset( $responsi_options['responsi_header_margin_bottom'] ) ? esc_attr( $responsi_options['responsi_header_margin_bottom'] ) : 0;
	    $header_margin_left                     = isset( $responsi_options['responsi_header_margin_left'] ) ? esc_attr( $responsi_options['responsi_header_margin_left'] ) : 0;
	    $header_margin_right                    = isset( $responsi_options['responsi_header_margin_right'] ) ? esc_attr( $responsi_options['responsi_header_margin_right'] ) : 0;
	    $font_logo                              = isset( $responsi_options['responsi_font_logo'] ) ? $responsi_options['responsi_font_logo'] : array('size' => '36','line_height' => '1.5','face' => 'Open Sans','style' => 'normal','color' => '#FFFFFF');
	    $font_desc                              = isset( $responsi_options['responsi_font_desc'] ) ? $responsi_options['responsi_font_desc'] : array('size' => '13','line_height' => '1.5','face' => 'PT Sans','style' => 'normal','color' => '#7c7c7c');
	    $font_header_widget_title               = isset( $responsi_options['responsi_font_header_widget_title'] ) ? $responsi_options['responsi_font_header_widget_title'] : array('size' => '14','line_height' => '1.5','face' => 'Open Sans','style' => 'normal','color' => '#ffffff');
	    $responsi_font_header_widget_text       = isset( $responsi_options['responsi_font_header_widget_text'] ) ? $responsi_options['responsi_font_header_widget_text'] : array('size' => '13','line_height' => '1.5','face' => 'Open Sans','style' => 'normal','color' => '#7c7c7c');
	    $responsi_font_header_widget_link       = isset( $responsi_options['responsi_font_header_widget_link'] ) ? $responsi_options['responsi_font_header_widget_link'] : array('size' => '13','line_height' => '1.5','face' => 'Open Sans','style' => 'normal','color' => '#7c7c7c');
	    $responsi_font_header_widget_link_hover = isset( $responsi_options['responsi_font_header_widget_link_hover'] ) ? esc_attr( $responsi_options['responsi_font_header_widget_link_hover'] ) : '#ff6868';
	    $responsi_bg_header_position_vertical   = isset( $responsi_options['responsi_bg_header_position_vertical'] ) ? esc_attr( $responsi_options['responsi_bg_header_position_vertical'] ) : 'center';
	    $responsi_bg_header_position_horizontal = isset( $responsi_options['responsi_bg_header_position_horizontal'] ) ? esc_attr( $responsi_options['responsi_bg_header_position_horizontal'] ) : 'center';
	    $responsi_header_widget_text_alignment  = isset( $responsi_options['responsi_font_header_widget_text_alignment'] ) ? esc_attr( $responsi_options['responsi_font_header_widget_text_alignment'] ) : 'left';

	    $header_inner_bg                              = isset( $responsi_options['responsi_header_inner_bg'] ) ? $responsi_options['responsi_header_inner_bg'] : array( 'onoff' => 'false', 'color' => '#ffffff' );
	    $enable_header_inner_bg_image                 = isset( $responsi_options['responsi_enable_header_inner_bg_image'] ) ? esc_attr( $responsi_options['responsi_enable_header_inner_bg_image'] ) : 'false';
	    $header_inner_bg_image                        = isset( $responsi_options['responsi_header_inner_bg_image'] ) ? str_replace( array( 'https:', 'http:' ), '', esc_url( $responsi_options['responsi_header_inner_bg_image'] ) ) : '';
	    $header_inner_bg_image_repeat                 = isset( $responsi_options['responsi_header_inner_bg_image_repeat'] ) ? esc_attr( $responsi_options['responsi_header_inner_bg_image_repeat'] ) : 'repeat';
	    $responsi_bg_header_inner_position_vertical   = isset( $responsi_options['responsi_bg_header_inner_position_vertical'] ) ? esc_attr( $responsi_options['responsi_bg_header_inner_position_vertical'] ) : 'center';
	    $responsi_bg_header_inner_position_horizontal = isset( $responsi_options['responsi_bg_header_inner_position_horizontal'] ) ? esc_attr( $responsi_options['responsi_bg_header_inner_position_horizontal'] ) : 'center';
	    $header_inner_border_top                      = isset( $responsi_options['responsi_header_inner_border_top'] ) ? $responsi_options['responsi_header_inner_border_top'] : array('width' => '0','style' => 'solid','color' => '#DBDBDB');
	    $header_inner_border_bottom                   = isset( $responsi_options['responsi_header_inner_border_bottom'] ) ? $responsi_options['responsi_header_inner_border_bottom'] : array('width' => '0','style' => 'solid','color' => '#DBDBDB');
	    $header_inner_border_lr                       = isset( $responsi_options['responsi_header_inner_border_lr'] ) ? $responsi_options['responsi_header_inner_border_lr'] : array('width' => '0','style' => 'solid','color' => '#DBDBDB');
	    $header_inner_box_shadow_option               = isset( $responsi_options['responsi_header_inner_box_shadow'] ) ? $responsi_options['responsi_header_inner_box_shadow'] : array( 'onoff' => 'false' , 'h_shadow' => '0px' , 'v_shadow' => '0px', 'blur' => '5px' , 'spread' => '0px', 'color' => '#DBDBDB', 'inset' => '' );
	    $header_inner_box_shadow                      = responsi_generate_box_shadow( $header_inner_box_shadow_option );
	    $header_inner_padding_top                     = isset( $responsi_options['responsi_header_inner_padding_top'] ) ? esc_attr( $responsi_options['responsi_header_inner_padding_top'] ) : 0;
	    $header_inner_padding_bottom                  = isset( $responsi_options['responsi_header_inner_padding_bottom'] ) ? esc_attr( $responsi_options['responsi_header_inner_padding_bottom'] ) : 0;
	    $header_inner_padding_left                    = isset( $responsi_options['responsi_header_inner_padding_left'] ) ? esc_attr( $responsi_options['responsi_header_inner_padding_left'] ) : 0;
	    $header_inner_padding_right                   = isset( $responsi_options['responsi_header_inner_padding_right'] ) ? esc_attr( $responsi_options['responsi_header_inner_padding_right'] ) : 0;
	    $header_inner_margin_top                      = isset( $responsi_options['responsi_header_inner_margin_top'] ) ? esc_attr( $responsi_options['responsi_header_inner_margin_top'] ) : 0;
	    $header_inner_margin_bottom                   = isset( $responsi_options['responsi_header_inner_margin_bottom'] ) ? esc_attr( $responsi_options['responsi_header_inner_margin_bottom'] ) : 0;
	    $header_inner_margin_left                     = isset( $responsi_options['responsi_header_inner_margin_left'] ) ? esc_attr( $responsi_options['responsi_header_inner_margin_left'] ) : 0;
	    $header_inner_margin_right                    = isset( $responsi_options['responsi_header_inner_margin_right'] ) ? esc_attr( $responsi_options['responsi_header_inner_margin_right'] ) : 0;

	    $font_header_widget_title               = isset( $responsi_options['responsi_font_header_widget_title'] ) ? $responsi_options['responsi_font_header_widget_title'] : array('size' => '14','line_height' => '1.5','face' => 'Open Sans','style' => 'normal','color' => '#ffffff');
	    $responsi_font_header_widget_text       = isset( $responsi_options['responsi_font_header_widget_text'] ) ? $responsi_options['responsi_font_header_widget_text'] : array('size' => '13','line_height' => '1.5','face' => 'Open Sans','style' => 'normal','color' => '#7c7c7c');
	    $responsi_font_header_widget_link       = isset( $responsi_options['responsi_font_header_widget_link'] ) ? $responsi_options['responsi_font_header_widget_link'] : array('size' => '13','line_height' => '1.5','face' => 'Open Sans','style' => 'normal','color' => '#7c7c7c');
	    $responsi_font_header_widget_link_hover = isset( $responsi_options['responsi_font_header_widget_link_hover'] ) ? esc_attr( $responsi_options['responsi_font_header_widget_link_hover'] ) : '#ff6868';
	    $responsi_bg_header_position_vertical   = isset( $responsi_options['responsi_bg_header_position_vertical'] ) ? esc_attr( $responsi_options['responsi_bg_header_position_vertical'] ) : 'center';
	    $responsi_bg_header_position_horizontal = isset( $responsi_options['responsi_bg_header_position_horizontal'] ) ? esc_attr( $responsi_options['responsi_bg_header_position_horizontal'] ) : 'center';
	    $responsi_header_widget_text_alignment  = isset( $responsi_options['responsi_font_header_widget_text_alignment'] ) ? esc_attr( $responsi_options['responsi_font_header_widget_text_alignment'] ) : 'left';
	    
	   
	    $header_padding_css = '';
	    $header_padding_css .= 'padding-top:' . $header_padding_top . 'px;padding-bottom:' . $header_padding_bottom . 'px;';
	    $header_padding_css .= 'padding-left:' . $header_padding_left . 'px;padding-right:' . $header_padding_right . 'px;';
	    
	    $header_css = '';
	    $header_css .= responsi_generate_background_color( $header_bg );
	    if ( 'true' === $enable_header_bg_image && '' !== trim( $header_bg_image ) ) {
	        $header_css .= 'background-image:url("' . $header_bg_image . '");background-position:' . strtolower($responsi_bg_header_position_horizontal) . ' ' . strtolower($responsi_bg_header_position_vertical) . ';background-repeat:' . $header_bg_image_repeat . ';';
	    }
	    
	    $header_inner_css = '';
	    $header_inner_css .= responsi_generate_background_color( $header_inner_bg );
	    if ( 'true' === $enable_header_inner_bg_image && '' !== trim( $header_inner_bg_image ) ) {
	        $header_inner_css = 'background-image:url("' . $header_inner_bg_image . '");background-position:' . strtolower($responsi_bg_header_inner_position_horizontal) . ' ' . strtolower($responsi_bg_header_inner_position_vertical) . ';background-repeat:' . $header_inner_bg_image_repeat . ';';
	    }
	    $header_inner_css .= 'margin-top:' . $header_inner_margin_top . 'px;margin-bottom:' . $header_inner_margin_bottom . 'px;';
	    $header_inner_css .= 'margin-left:' . $header_inner_margin_left . 'px;margin-right:' . $header_inner_margin_right . 'px;';
	    $header_inner_css .= 'padding-top:' . $header_inner_padding_top . 'px;padding-bottom:' . $header_inner_padding_bottom . 'px;';
	    $header_inner_css .= 'padding-left:' . $header_inner_padding_left . 'px;padding-right:' . $header_inner_padding_right . 'px;';
	    $header_inner_css .= responsi_generate_border($header_inner_border_top, 'border-top');
	    $header_inner_css .= responsi_generate_border($header_inner_border_bottom, 'border-bottom');
	    $header_inner_css .= responsi_generate_border($header_inner_border_lr, 'border-left');
	    $header_inner_css .= responsi_generate_border($header_inner_border_lr, 'border-right');
	    $header_inner_css .= $header_inner_box_shadow;

	    $out_plus 		= ( $header_inner_margin_top + $header_inner_margin_bottom + $header_inner_padding_top + $header_inner_padding_bottom + $header_inner_border_top['width'] + $header_inner_border_bottom['width'] );
	    $in_plus  		= ( $header_padding_top + $header_padding_bottom );

	    $corner_top 	= ( $header_inner_margin_top + $header_inner_padding_top + $header_inner_border_top['width'] + $header_padding_top );
	    $corner_right	= ( $header_inner_margin_right + $header_inner_padding_right + $header_inner_border_lr['width'] + $header_padding_right );

        $responsi_ih_width 						= $responsi_options_ih['responsi_ih_width'];
        $responsi_ih_max 						= $responsi_options_ih['responsi_ih_max'];
        $responsi_ih_min 						= $responsi_options_ih['responsi_ih_min'];

        $responsi_ih_animation_speed 			= $responsi_options_ih['responsi_ih_animation_speed'];
		$animation_speed = 0;
		if( 0 == $responsi_ih_animation_speed ){
			$animation_speed = 0;
		}elseif( 10 == $responsi_ih_animation_speed ){
			$animation_speed = 1;
		}else{
			$animation_speed = '0.'.$responsi_ih_animation_speed;
		}

		$responsi_ih_widget1_alignment 			= isset( $responsi_options_ih['responsi_ih_widget1_alignment'] ) ? $responsi_options_ih['responsi_ih_widget1_alignment'] : 'right';
		$responsi_ih_widget2_alignment 			= isset( $responsi_options_ih['responsi_ih_widget2_alignment'] ) ? $responsi_options_ih['responsi_ih_widget2_alignment'] : 'right';

		/*Tablet*/
		$responsi_ih_column_tablet 				= isset( $responsi_options_ih['responsi_ih_column_tablet'] ) ? $responsi_options_ih['responsi_ih_column_tablet'] : array( 'col' => 2, 'col1' => '50', 'col2' => '50', 'col3' => '25', 'col4' => '25' );
		$_ih_tablet_col 						= $responsi_ih_column_tablet['col'];
		$responsi_ih_max_tablet 				= $responsi_options_ih['responsi_ih_max_tablet'];
        $responsi_ih_min_tablet 				= $responsi_options_ih['responsi_ih_min_tablet'];
        $responsi_ih_widget1_alignment_tablet 	= isset( $responsi_options_ih['responsi_ih_widget1_alignment_tablet'] ) ? $responsi_options_ih['responsi_ih_widget1_alignment_tablet'] : 'right';
		$responsi_ih_widget2_alignment_tablet 	= isset( $responsi_options_ih['responsi_ih_widget2_alignment_tablet'] ) ? $responsi_options_ih['responsi_ih_widget2_alignment_tablet'] : 'right';
		
		$tablet_css = '';

		if( $_ih_tablet_col == 4 ){
			$_ih_tablet_col1 						= $responsi_ih_column_tablet['col1'];
	        $_ih_tablet_col2 						= $responsi_ih_column_tablet['col2'];
	        $_ih_tablet_col3 						= $responsi_ih_column_tablet['col3'];
	        $_ih_tablet_col4 						= $responsi_ih_column_tablet['col4'];

	        $tablet_css = '.ih-tablet-0 #ih-area-1{
                width:'.$_ih_tablet_col1.'% !important;
            }

            .ih-tablet-0 #ih-area-2{
                width:'.$_ih_tablet_col2.'% !important;
            }

            .ih-tablet-0 #ih-area-3{
                width:'.$_ih_tablet_col3.'% !important;
            }

            .ih-tablet-0 #ih-area-4{
                width:'.$_ih_tablet_col4.'% !important;
            }';

	    }elseif( $_ih_tablet_col == 3 ){
	    	$_ih_tablet_col1 						= $responsi_ih_column_tablet['col1'];
	        $_ih_tablet_col2 						= $responsi_ih_column_tablet['col2'];
	        $_ih_tablet_col3 						= $responsi_ih_column_tablet['col3'];
	        $_ih_tablet_col4 						= 0;

	        $tablet_css = '.ih-tablet-0 #ih-area-1{
                width:'.$_ih_tablet_col1.'% !important;
            }

            .ih-tablet-0 #ih-area-2{
                width:'.$_ih_tablet_col2.'% !important;
            }

            .ih-tablet-0 #ih-area-3{
                width:'.$_ih_tablet_col3.'% !important;
            }

            .ih-tablet-0 #ih-area-4{
                width:'.$_ih_tablet_col4.'% !important;
                display:none !important;
            }';

	    }elseif( $_ih_tablet_col == 2 ){
	    	$_ih_tablet_col1 						= $responsi_ih_column_tablet['col1'];
	        $_ih_tablet_col2 						= $responsi_ih_column_tablet['col2'];
	        $_ih_tablet_col3 						= 0;
	        $_ih_tablet_col4 						= 0;

	        $tablet_css = '.ih-tablet-0 #ih-area-1{
                width:'.$_ih_tablet_col1.'% !important;
            }

            .ih-tablet-0 #ih-area-2{
                width:'.$_ih_tablet_col2.'% !important;
            }

            .ih-tablet-0 #ih-area-3{
                width:'.$_ih_tablet_col3.'% !important;
                display:none !important;
            }

            .ih-tablet-0 #ih-area-4{
                width:'.$_ih_tablet_col4.'% !important;
                display:none !important;
            }';

	    }elseif( $_ih_tablet_col == 1 ){
	    	$_ih_tablet_col1 						= $responsi_ih_column_tablet['col1'];
	        $_ih_tablet_col2 						= 15;
	        $_ih_tablet_col3 						= 0;
	        $_ih_tablet_col4 						= 0;

	        $tablet_css = '.ih-tablet-0 #ih-area-1{
                width:85% !important;
            }

            .ih-tablet-0 #ih-area-2{
                width:'.$_ih_tablet_col2.'% !important;
            }

            .ih-tablet-0 #ih-area-3{
                width:'.$_ih_tablet_col3.'% !important;
                display:none !important;
            }

            .ih-tablet-0 #ih-area-4{
                width:'.$_ih_tablet_col4.'% !important;
                display:none !important;
            }';
	    }

        /*Mobile*/
        $responsi_ih_column_mobile 				= isset( $responsi_options_ih['responsi_ih_column_mobile'] ) ? $responsi_options_ih['responsi_ih_column_mobile'] : array( 'col' => 2, 'col1' => '50', 'col2' => '50', 'col3' => '25', 'col4' => '25' );
		$_ih_mobile_col 						= $responsi_ih_column_mobile['col'];
		$responsi_ih_max_mobile 				= $responsi_options_ih['responsi_ih_max_mobile'];
        $responsi_ih_min_mobile 				= $responsi_options_ih['responsi_ih_min_mobile'];
        $responsi_ih_widget1_alignment_mobile 	= isset( $responsi_options_ih['responsi_ih_widget1_alignment_mobile'] ) ? $responsi_options_ih['responsi_ih_widget1_alignment_mobile'] : 'right';
		$responsi_ih_widget2_alignment_mobile 	= isset( $responsi_options_ih['responsi_ih_widget2_alignment_mobile'] ) ? $responsi_options_ih['responsi_ih_widget2_alignment_mobile'] : 'right';
		
		$mobile_css = '';

		if( $_ih_mobile_col == 4 ){
			$_ih_mobile_col1 						= $responsi_ih_column_mobile['col1'];
	        $_ih_mobile_col2 						= $responsi_ih_column_mobile['col2'];
	        $_ih_mobile_col3 						= $responsi_ih_column_mobile['col3'];
	        $_ih_mobile_col4 						= $responsi_ih_column_mobile['col4'];

	        $mobile_css = '.ih-mobile-0 #ih-area-1{
                width:'.$_ih_mobile_col1.'% !important;
            }

            .ih-mobile-0 #ih-area-2{
                width:'.$_ih_mobile_col2.'% !important;
            }

            .ih-mobile-0 #ih-area-3{
                width:'.$_ih_mobile_col3.'% !important;
            }

            .ih-mobile-0 #ih-area-4{
                width:'.$_ih_mobile_col4.'% !important;
            }';

	    }elseif( $_ih_mobile_col == 3 ){
	    	$_ih_mobile_col1 						= $responsi_ih_column_mobile['col1'];
	        $_ih_mobile_col2 						= $responsi_ih_column_mobile['col2'];
	        $_ih_mobile_col3 						= $responsi_ih_column_mobile['col3'];
	        $_ih_mobile_col4 						= 0;

	        $mobile_css = '.ih-mobile-0 #ih-area-1{
                width:'.$_ih_mobile_col1.'% !important;
            }

            .ih-mobile-0 #ih-area-2{
                width:'.$_ih_mobile_col2.'% !important;
            }

            .ih-mobile-0 #ih-area-3{
                width:'.$_ih_mobile_col3.'% !important;
            }

            .ih-mobile-0 #ih-area-4{
                width:'.$_ih_mobile_col4.'% !important;
                display:none !important;
            }';

	    }elseif( $_ih_mobile_col == 2 ){
	    	$_ih_mobile_col1 						= $responsi_ih_column_mobile['col1'];
	        $_ih_mobile_col2 						= $responsi_ih_column_mobile['col2'];
	        $_ih_mobile_col3 						= 0;
	        $_ih_mobile_col4 						= 0;

	        $mobile_css = '.ih-mobile-0 #ih-area-1{
                width:'.$_ih_mobile_col1.'% !important;
            }

            .ih-mobile-0 #ih-area-2{
                width:'.$_ih_mobile_col2.'% !important;
            }

            .ih-mobile-0 #ih-area-3{
                width:'.$_ih_mobile_col3.'% !important;
                display:none !important;
            }

            .ih-mobile-0 #ih-area-4{
                width:'.$_ih_mobile_col4.'% !important;
                display:none !important;
            }';

	    }elseif( $_ih_mobile_col == 1 ){
	    	$_ih_mobile_col1 						= $responsi_ih_column_mobile['col1'];
	        $_ih_mobile_col2 						= 15;
	        $_ih_mobile_col3 						= 0;
	        $_ih_mobile_col4 						= 0;

	        $mobile_css = '.ih-mobile-0 #ih-area-1{
                width:85% !important;
            }

            .ih-mobile-0 #ih-area-2{
                width:'.$_ih_mobile_col2.'% !important;
            }

            .ih-mobile-0 #ih-area-3{
                width:'.$_ih_mobile_col3.'% !important;
                display:none !important;
            }

            .ih-mobile-0 #ih-area-4{
                width:'.$_ih_mobile_col4.'% !important;
                display:none !important;
            }';
	    }

	    $widget_css = '';

	    $widget_css .= '.ih-area-widget .widget-title h3 {' . responsi_generate_fonts($font_header_widget_title) . '}';
	    $widget_css .= '.ih-area-widget .widget .textwidget, .ih-area-widget .widget:not(div), .ih-area-widget .widget p,.ih-area-widget .widget label,.ih-area-widget .widget .textwidget,.ih-area-widget .login-username label, .ih-area-widget .login-password label, .ih-area-widget .widget .textwidget .tel, .ih-area-widget .widget .textwidget .tel a, .ih-area-widget .widget .textwidget a[href^=tel], .ih-area-widget .widget * a[href^=tel], .ih-area-widget .widget a[href^=tel]{' . responsi_generate_fonts($responsi_font_header_widget_text) . ' text-decoration: none;}';
	    $widget_css .= '.ih-area-widget .widget a,.ih-area-widget .widget ul li a,.ih-area-widget .widget ul li{' . responsi_generate_fonts($responsi_font_header_widget_link) . '}';
	    $widget_css .= '.ih-area-widget .widget a:hover{color:' . $responsi_font_header_widget_link_hover . ';}';
	    $widget_css .= '.ih-area-widget .widget{text-align:inherit;}';
	    
	    $header_widget_alignment_mobile               = isset( $responsi_options['responsi_font_header_widget_text_alignment_mobile'] ) ? esc_attr( $responsi_options['responsi_font_header_widget_text_alignment_mobile'] ) : 'true';
	    $header_widget_mobile_margin                  = isset( $responsi_options['responsi_header_widget_mobile_margin'] ) ? esc_attr( $responsi_options['responsi_header_widget_mobile_margin'] ) : 'true';
	    $header_widget_mobile_margin_between          = isset( $responsi_options['responsi_header_widget_mobile_margin_between'] ) ? esc_attr( $responsi_options['responsi_header_widget_mobile_margin_between'] ) : 0;
	    
	    $header_widget_mobile_css = '';

	    if ( 'true' === $header_widget_alignment_mobile ) {
	        $header_widget_mobile_css .= '.ih-area-widget .widget, .ih-area-widget * .widget, .ih-area-widget .widget *, .ih-area-widget .widget .widget-title h3, .header-widget-1 .widget .logo-ctn {text-align:center !important;}';
	        $header_widget_mobile_css .= '.logo.site-logo,.logo-ctn,.desc-ctn{margin:auto;}';
	    }

	    if ( 'true' === $header_widget_mobile_margin && $header_widget_mobile_margin_between >= 0) {
	        $header_widget_mobile_css .= '.ih-area-widget .widget{margin-bottom:' . $header_widget_mobile_margin_between . 'px !important;}';
	    }else{
	        $header_widget_mobile_css .= '.ih-area-widget .widget{margin-bottom:0px !important;}';
	    }

	    $responsi_ih_sitetitle_mobile_font               = isset( $responsi_options_ih['responsi_ih_sitetitle_mobile_font'] ) ? $responsi_options_ih['responsi_ih_sitetitle_mobile_font'] : array('size' => '16','line_height' => '1.5','face' => 'Open Sans','style' => 'normal','color' => '#ffffff');

		$output = '

		.ih-layout{
			' . $header_css . '
		}

		.ih-ctn{
			' . $header_css . '
			' . $header_padding_css . '
		}

		.ih-content-wrap{
			' . $header_inner_css . '
		}

		.responsi-ih-wide{
			max-width:'.$responsi_ih_width.'px;
		}
		
		.ih-ctn .logo-ctn img{
			transition: '.$animation_speed.'s;
		}

		.ih-content{
			transition: height '.$animation_speed.'s;
		}

		'.$widget_css.'
		
		@media (min-width:783px) {
		  	.ih-ctn .logo-ctn img{
			  	max-height: '.$responsi_ih_max.'px;
			  	/*line-height: '.$responsi_ih_max.'px;
			  	height: '.$responsi_ih_max.'px;
			  	font-size: '.$responsi_ih_max.'px;*/
			  	width: auto;
			  	max-width: none;
			}
			.ih-sticky .logo-ctn img{
			  	max-height: '.$responsi_ih_min.'px;
			  	/*line-height: '.$responsi_ih_min.'px;
			  	height: '.$responsi_ih_min.'px;
			  	font-size: '.$responsi_ih_min.'px;*/
			  	width: auto;
			}
			.ih-content{
				height: '.$responsi_ih_max.'px;
			}
			.ih-sticky .ih-content{
				height: '.$responsi_ih_min.'px;
			}

			#ih-layout.ihSticky{
				min-height:'.( $responsi_ih_max + $in_plus + $out_plus ).'px;
			}

			.ih-area-widget1{
				text-align:'.$responsi_ih_widget1_alignment.';
			}
			
			.ih-area-widget2{
				text-align:'.$responsi_ih_widget2_alignment.';
			}
		}
		@media only screen and (min-width:600px) and (max-width:782px) {
		 	.ih-ctn .logo-ctn img{
			  	max-height: '.$responsi_ih_max_tablet.'px;
			  	/*line-height: '.$responsi_ih_max_tablet.'px;
			  	height: '.$responsi_ih_max_tablet.'px;
			  	font-size: '.$responsi_ih_max_tablet.'px;*/
			  	width: auto;
			  	max-width: none;
			}
			.ih-sticky .logo-ctn img{
			  	max-height: '.$responsi_ih_min_tablet.'px;
			  	/*line-height: '.$responsi_ih_min_tablet.'px;
			  	height: '.$responsi_ih_min_tablet.'px;
			  	font-size: '.$responsi_ih_min_tablet.'px;*/
			  	width: auto;
			}
			.ih-content{
				height: '.$responsi_ih_max_tablet.'px;
			}
			.ih-sticky .ih-content{
				height: '.$responsi_ih_min_tablet.'px;
			}

			#ih-layout:not(.ih-tablet-nonsticky).ihSticky{
				min-height:'.( $responsi_ih_max_tablet + $in_plus + $out_plus ).'px;
			}
			'.$tablet_css.'

			.ih-layout:not(.ihSticky) .stickyMenu{
			    margin-top: '.$corner_top.'px !important;
			    right: '.$corner_right.'px !important;
			}

			.ih-layout.ihSticky .ih-tablet-nonsticky .stickyMenu{
				margin-top: '.$corner_top.'px !important;
			    right: '.$corner_right.'px !important;
			}

			.ih-area-widget1{
				text-align:'.$responsi_ih_widget1_alignment_tablet.';
			}
			
			.ih-area-widget2{
				text-align:'.$responsi_ih_widget2_alignment_tablet.';
			}
			
		}
		@media (max-width:600px) {
			
			.ih-area .logo-ctn a.site-title, .ih-area .logo-ctn a.site-title:hover, .ih-area .logo-ctn a:link:hover, .ih-area .site-title, .ih-area a.site-title:link, .ih-area a.site-title:hover, .ih-area a.site-title:link:hover{
				'. responsi_generate_fonts($responsi_ih_sitetitle_mobile_font, true) .'
			}

		 	.ih-ctn .logo-ctn img{
                max-height: '.$responsi_ih_max_mobile.'px;
                /*line-height: '.$responsi_ih_max_mobile.'px;
                height: '.$responsi_ih_max_mobile.'px;
                font-size: '.$responsi_ih_max_mobile.'px;*/
                width: auto;
                max-width: none;
            }
            .ih-sticky .logo-ctn img{
                max-height: '.$responsi_ih_min_mobile.'px;
                /*line-height: '.$responsi_ih_min_mobile.'px;
                height: '.$responsi_ih_min_mobile.'px;
                font-size: '.$responsi_ih_min_mobile.'px;*/
                width: auto;
            }
            .ih-content{
                height: '.$responsi_ih_max_mobile.'px;
            }
            .ih-sticky .ih-content{
                height: '.$responsi_ih_min_mobile.'px;
            }

            #ih-layout:not(.ih-mobile-nonsticky).ihSticky{
				min-height:'.( $responsi_ih_max_mobile + $in_plus + $out_plus ).'px;
			}
            '.$mobile_css.'
            
			.ih-layout:not(.ihSticky) .stickyMenu{
			    margin-top: '.$corner_top.'px !important;
			    right: '.$corner_right.'px !important;
			}

			.ih-layout.ihSticky .ih-mobile-nonsticky .stickyMenu{
			    margin-top: '.$corner_top.'px !important;
			    right: '.$corner_right.'px !important;
			}

			.ih-area-widget1{
				text-align:'.$responsi_ih_widget1_alignment_mobile.';
			}
			
			.ih-area-widget2{
				text-align:'.$responsi_ih_widget2_alignment_mobile.';
			}
		}

		';

	    if( function_exists('responsi_minify_css') ){
        	$output = responsi_minify_css( $output );
        }

		return $output;
	}

	public function responsi_build_css_theme_actived(){
		$this->responsi_dynamic_css();
	}

	public function build_css_after_updated(){
		$this->responsi_dynamic_css();
	}

	public function customize_preview_inline_style(){

		if ( is_customize_preview() ) {
			if( is_child_theme() ){
				wp_add_inline_style( 'responsi-theme', $this->responsi_build_dynamic_css( true ) );
			}else{
				wp_add_inline_style( 'responsi-framework', $this->responsi_build_dynamic_css( true ) );
			}
		} else {
			$ih_custom_css = get_theme_mod( 'ih_custom_css' );
			if ( false === $ih_custom_css ) {
				$this->responsi_dynamic_css();
				if( is_child_theme() ){
					wp_add_inline_style( 'responsi-theme', $this->responsi_build_dynamic_css( true ) );
				}else{
					wp_add_inline_style( 'responsi-framework', $this->responsi_build_dynamic_css( true ) );
				}
			}else{
				if( is_child_theme() ){
					wp_add_inline_style( 'responsi-theme', get_theme_mod( 'ih_custom_css' ) );
				}else{
					wp_add_inline_style( 'responsi-framework', get_theme_mod( 'ih_custom_css' ) );
				}
			}
		}

	}

	public function responsi_google_webfonts( $options ){
		global $responsi_options_ih;
		$new_options = array();
		if( is_array( $options ) && is_array( $responsi_options_ih ) )
			$new_options  = array_merge( $options, $responsi_options_ih );
		return $new_options;
	}

	public function ih_ob_start () {
		remove_action( 'responsi_wrapper_nav_content', 'responsi_navigation', 10 );
		ob_start();
	}
	public function ih_ob_clean () {
		ob_get_clean();
	}

}
?>
