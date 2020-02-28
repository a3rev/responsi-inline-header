<?php

if ( ! function_exists( 'responsi_ih_register_styles' ) ){
    function responsi_ih_register_styles( $styles ){
        global $responsi_version;
        $suffix = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '' : '.min';
        $styles->add( 'responsi-ih', RESPONSI_IH_CSS_URL . '/responsi-ih'.$suffix.'.css', array(), $responsi_version, 'screen' );    
    }
}
add_action( 'wp_default_styles', 'responsi_ih_register_styles' );


if ( ! function_exists( 'responsi_ih_load_styles' ) ){
    function responsi_ih_load_styles(){
        wp_enqueue_style( 'responsi-ih' );
    }
}
add_action( 'wp_enqueue_scripts', 'responsi_ih_load_styles' );

if ( ! function_exists( 'responsi_ih_register_scripts' ) ){
    function responsi_ih_register_scripts( &$scripts ){
        global $responsi_version;
        $suffix = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '' : '.min';

        //$scripts->add( 'fontawesome', RESPONSI_IH_JS_URL . '/all.js', array('jquery' ), '5.9.0', true );

        $scripts->add( 'responsi-ih', RESPONSI_IH_JS_URL . '/responsi-ih'.$suffix.'.js', array('jquery' ), $responsi_version, true );
        
    }
}
add_action( 'wp_default_scripts', 'responsi_ih_register_scripts', 12 );

if ( ! function_exists( 'responsi_ih_load_javascript' ) ){
    function responsi_ih_load_javascript(){
        global $responsi_options_ih;
        $responsi_ih_paramaters =  array(
            '_position'         => isset( $responsi_options_ih['responsi_ih_position'] ) ? $responsi_options_ih['responsi_ih_position'] : 'false',
        );
        did_action( 'init' ) && wp_localize_script( 'responsi-ih', 'responsi_ih_paramaters', $responsi_ih_paramaters );
        wp_enqueue_script( 'responsi-ih' );
    }
}
add_action( 'wp_footer', 'responsi_ih_load_javascript' );

function responsi_ih_focus_sections( $_sectionIds ) {

        $focusSection =  array( 
            //'sidebar-widgets-rih-1' => '#ih-area-1',
            //'sidebar-widgets-rih-2' => '#ih-area-2',
            'sidebar-widgets-rih-3' => '#ih-area-3',
            'sidebar-widgets-rih-4' => '#ih-area-4',
            //'sidebar-widgets-rih-5' => '#ih-area-5',
            //'sidebar-widgets-rih-6' => '#ih-area-6',
        );

        if( is_array ($focusSection) && count($focusSection) > 0 ){
            foreach( $focusSection as $settings => $selector ){
                $_sectionIds[] = array(
                    'selector' => $selector,
                    'settings' => $settings,
                );
            }
        }

        return $_sectionIds;

    }

add_filter( 'responsi_focus_sections', 'responsi_ih_focus_sections' );

function responsi_ih_register_widget(){
    if ( !function_exists('register_sidebars') ){
        return;
    }
    global $responsi_options_ih;

    do_action( 'the_widgets_responsi_ih_before' );
   
    $total = 2;
    if ( !$total ){
        $total = 2;
    }

    $i = 0;
    while ( $i < $total ) : $i++;
        register_sidebar( array( 'name' => __( 'Inline Header', 'responsi-ih' )." {$i}", 'id' => 'rih-'.$i, 'description' => __( 'Widgetized header', 'responsi-ih' ), 'before_widget' => '<div id="%1$s" class="ih_widget"><div class="widget %2$s"><div class="ih_widget_content clearfix">', 'after_widget' => '</div></div></div>', 'before_title' => '</div><div class="ih_widget_title clearfix">', 'after_title' => '</div><div class="ih_widget_content clearfix">' ) );
    endwhile;

    do_action( 'the_widgets_responsi_ih_after' );
}

add_action( 'widgets_init', 'responsi_ih_register_widget' );

if( !function_exists('responsi_ih_customize') ){
    function responsi_ih_customize(){
        global $wp_customize;
        if ( $wp_customize ) {
            $wp_customize->get_setting( 'responsi_ih_column[col1]' )->transport = 'postMessage';
            $wp_customize->get_setting( 'responsi_ih_column[col2]' )->transport = 'postMessage';
            $wp_customize->get_setting( 'responsi_ih_column[col3]' )->transport = 'postMessage';
            $wp_customize->get_setting( 'responsi_ih_column[col4]' )->transport = 'postMessage';
        }
    }
}

add_action( 'responsi_customize_selective_refresh_after', 'responsi_ih_customize' );

if( !function_exists('responsi_ih_frontend') ){
    function responsi_ih_frontend(){
        global $responsi_options_ih, $shiftclick;

        /*$custom_width_class = '';
        $animation_class = '';
        $animation_data = '';
        $animation_style = '';
        $responsi_ih_tablet = '';
        $responsi_ih_mobile = '';
        $_ih_column = '';*/

        $responsi_ih_column = isset( $responsi_options_ih['responsi_ih_column'] ) ? $responsi_options_ih['responsi_ih_column'] : array( 'col' => 2, 'col1' => '50', 'col2' => '50', 'col3' => '25', 'col4' => '25' );
        $responsi_ih_tablet = 0;
        $responsi_ih_mobile = 0;
        $responsi_ih_custom_width = isset( $responsi_options_ih['responsi_ih_custom_width'] ) ? $responsi_options_ih['responsi_ih_custom_width'] : 0;
        $custom_width_class = '';
        if( $responsi_ih_custom_width == 'true' ){
            $custom_width_class = ' responsi-ih-wide';
        }

        $_ih_column = $responsi_ih_column['col'];
        $_ih_col1 = $responsi_ih_column['col1'];
        $_ih_col2 = $responsi_ih_column['col2'];
        $_ih_col3 = $responsi_ih_column['col3'];
        $_ih_col4 = $responsi_ih_column['col4'];

        $responsi_ih_animation = isset( $responsi_options_ih['responsi_ih_animation'] ) ? responsi_generate_animation($responsi_options_ih['responsi_ih_animation']) : false;

        $animation_class = '';
        $animation_data = '';
        $animation_style = '';

        if( false !== $responsi_ih_animation ){
            $animation_class = ' '.$responsi_ih_animation['class'];
            $animation_data = ' data-animation="'.$responsi_ih_animation['data'].'"';
            $animation_style = ' style="'.$responsi_ih_animation['style'].'"';
        }

        $responsi_ih_position = isset( $responsi_options_ih['responsi_ih_position'] ) ? $responsi_options_ih['responsi_ih_position'] : 'false';
        $ih_sticky = ( $responsi_ih_position == 'true' ) ? ' ihSticky' : '';

        $responsi_ih_mobile_scroll = isset( $responsi_options_ih['responsi_ih_mobile_scroll'] ) ? $responsi_options_ih['responsi_ih_mobile_scroll'] : 'true';
        $_ih_mobile_scroll = ( 'true' == $responsi_ih_mobile_scroll ) ? ' ih-mobile-nonsticky' : '';

        $responsi_ih_tablet_scroll = isset( $responsi_options_ih['responsi_ih_tablet_scroll'] ) ? $responsi_options_ih['responsi_ih_tablet_scroll'] : 'true';
        $_ih_tablet_scroll = ( 'true' == $responsi_ih_tablet_scroll ) ? ' ih-tablet-nonsticky' : '';
        
        ?>
        <div id="ih-layout" class="ih-layout ih-clearfix<?php echo $ih_sticky;?><?php echo $_ih_mobile_scroll.$_ih_tablet_scroll;?>">
            <div id="ih-ctn" class="ih-ctn ih-clearfix ih-header<?php echo $_ih_mobile_scroll.$_ih_tablet_scroll;?>">
                <div id="ih-wrap" class="ih-clearfix ih-animation <?php echo $animation_class;?>"<?php echo $animation_data . $animation_style;?>>
                    <div id="ih-wide" class="ih-wide ih-clearfix<?php echo $custom_width_class;?>">
                        <div id="ih-content-wrap" class="ih-content-wrap ih-clearfix ih-column-<?php echo $_ih_column;?>">
                            <div id="ih-content" class="ih-content ih-tablet-<?php echo $responsi_ih_tablet;?> ih-mobile-<?php echo $responsi_ih_mobile;?>">
                                <?php

                                if( $_ih_column >= 1 ){
                                    $width = $_ih_col1;
                                    if( $_ih_column == 1 ){
                                        $width = 100;
                                    }
                                    ?>
                                    <div id="ih-area-1" class="ih-area ih-area-logo" style="width:<?php echo $width;?>%">
            
                                        <div class="logo-ctn">
                                            <?php responsi_site_logo();?>
                                        </div>
                                        <div class="desc-ctn">
                                            <?php responsi_site_description();?>
                                        </div>
                                        
                                    </div>
                                    <?php
                                }

                                if( $_ih_column >= 2 ){
                                    $width = $_ih_col2;
                                    ?>
                                    <div id="ih-area-2" class="ih-area ih-area-menu" style="width:<?php echo $width;?>%">
                                        <?php echo $shiftclick; ?>
                                        <?php responsi_navigation(); ?>
                                    </div>
                                    <?php
                                }

                                if( $_ih_column >= 3 ){
                                    $width = $_ih_col3;
                                    ?>
                                    <div id="ih-area-3" class="ih-area ih-area-widget ih-area-widget1" style="width:<?php echo $width;?>%">
                            
                                        <?php echo $shiftclick; ?>

                                        <?php
                                        if ( responsi_active_sidebar( 'rih-1' ) ) {
                                                responsi_dynamic_sidebar('rih-1' );
                                        }elseif( is_customize_preview() ){
                                            ?>
                                            <div class="widget ih-none-widget">
                                                <a href="tel:+008433337777"><i class="fa fa-phone"></i> <span class="phonenumber">+00 84 3333 7777</span></a>
                                            </div>
                                            <?php
                                        }
                                        ?>
                                        
                                    </div>
                                    <?php
                                }

                                if( $_ih_column >= 4 ){
                                    $width = $_ih_col4;
                                    ?>
                                    <div id="ih-area-4" class="ih-area ih-area-widget ih-area-widget2" style="width:<?php echo $width;?>%">
                            
                                        <?php echo $shiftclick; ?>

                                        <?php
                                        if ( responsi_active_sidebar( 'rih-2' ) ) {
                                                responsi_dynamic_sidebar('rih-2' );
                                        }elseif( is_customize_preview() ){
                                            ?>
                                            <div class="widget ih-none-widget">
                                                <a href="#"><i class="fa fa-linkedin"></i></a>
                                                <a href="#"><i class="fa fa-facebook"></i></a>
                                                <a href="#"><i class="fa fa-youtube"></i></a>
                                            </div>
                                            <?php
                                        }
                                        ?>
                                        
                                    </div>
                                    <?php
                                }

                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>                        
        </div>
        <?php
    }
}

add_action( 'responsi_wrapper_center_before', 'responsi_ih_frontend', 10 );
?>