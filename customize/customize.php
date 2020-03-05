<?php

namespace A3Rev\RIH;

class Customizer
{
    public function __construct()
    {
        
        add_filter('responsi_default_options_ih', array(
            $this,
            'controls_settings'
        ));
        add_filter('responsi_customize_register_panels', array(
            $this,
            'panels'
        ));
        add_filter('responsi_customize_register_sections', array(
            $this,
            'sections'
        ));
        add_filter('responsi_customize_register_settings', array(
            $this,
            'controls_settings'
        ));
        add_action('customize_preview_init', array(
            $this,
            'customize_preview_init'
        ), 11);
        add_action('customize_controls_enqueue_scripts', array(
            $this,
            'customize_controls_enqueue_scripts'
        ), 11);

        add_action( 'customize_controls_print_styles',          array( $this, 'responsi_customize_controls_print_styles' ) );
    }

    public function customize_controls_enqueue_scripts()
    {
        $suffix = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '' : '.min';
    }

    public function customize_preview_init()
    {
        $suffix = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '' : '.min';
        wp_enqueue_script('customize-ih-preview', RESPONSI_IH_URL . '/customize/js/customize' . $suffix . '.js', array(
            'jquery',
            'customize-preview',
            'responsi-customize-function'
        ), '5.3.0', 1);
    }

    public function responsi_customize_controls_print_styles() {
        global $wp_version, $responsi_version;
        $suffix = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '' : '.min';
        $rtl = is_rtl() ? '.rtl' : '';
        wp_enqueue_style( 'ih-customize', RESPONSI_IH_URL . '/customize/css/customize' . $suffix . '.css', array(), null, 'screen' );
    }

    public function global_responsi_settings($options)
    {
        global $responsi_options_ih;
        $options = array_merge($options, $responsi_options_ih);
        return $options;
    }

    public function panels($panels)
    {
        $_panels                          = array();

        $_panels['ih_panel'] = array(
            'title' => __('Inline Header', 'responsi-ih'),
            'priority' => 2,
            'active_callback' => '_customize_menu_ih'
        );

        $panels                           = array_merge($panels, $_panels);
        return $panels;
    }

    public function sections($sections)
    {

        $_sections                       = array();
        $_sections['ih_settings'] = array(
            'title' => __('Header - Settings', 'responsi-ih'),
            'priority' => 9,
            'panel' => 'header_settings_panel',
        );
        $_sections['ih_styles'] = array(
            'title' => __('Header - Styles', 'responsi-ih'),
            'priority' => 11,
            'panel' => 'header_settings_panel',
        );
        $sections = array_merge($sections, $_sections);
        return $sections;
    }

    public function controls_settings($controls_settings)
    {
        $_default = apply_filters( 'default_settings_ih', false );
        
        if( $_default ){
            $responsi_options_ih = array();
        }else{
            global $responsi_options_ih;
        }

        $_controls_settings = array();

        $_controls_settings['ih_settings_lb'] = array(
            'control' => array(
                'label'      => __('Layout', 'responsi-ih'),
                'section'    => 'ih_settings',
                'type'       => 'ilabel'
            ),
            'setting' => array(
                'type' => 'option',
            )
        );

        $_controls_settings['responsi_ih_column'] = array(
            'control' => array(
                'section'    => 'ih_settings',
                'settings'   => 'multiple',
                'type'       => 'column',
                'choices' => array(
                    '1' => get_template_directory_uri() . '/functions/images/header-widgets-1.png',
                    '2' => get_template_directory_uri() . '/functions/images/header-widgets-2.png',
                    '3' => get_template_directory_uri() . '/functions/images/header-widgets-3.png',
                    '4' => get_template_directory_uri() . '/functions/images/header-widgets-4.png',
                ),
                'input_attrs' => array(
                    'validate' => true,
                )
            ),
            'setting' => array(
                'default'       => isset($responsi_options_ih['responsi_ih_column']) ? $responsi_options_ih['responsi_ih_column'] : array( 'col' => 4, 'col1' => '22', 'col2' => '58', 'col3' => '15', 'col4' => '5' ) ,
                'sanitize_callback' => 'responsi_sanitize_columns',
            )
        );

        $_controls_settings['responsi_ih_position'] = array(
            'control' => array(
                'label'      => __('Position', 'responsi-ih'),
                'section'    => 'ih_settings',
                'settings'   => 'responsi_ih_position',
                'type'       => 'iswitcher',
                'choices' => array(
                    'checked_value' => 'true',
                    'checked_label' => 'Fixed',
                    'unchecked_value' => 'false',
                    'unchecked_label' => 'Scroll',
                    'container_width' => 110,
                )
            ),
            'setting' => array(
                'default'       => isset($responsi_options_ih['responsi_ih_position']) ? $responsi_options_ih['responsi_ih_position'] : 'false',
                'sanitize_callback' => 'responsi_sanitize_checkboxs',
            )
        );

        $_controls_settings['responsi_ih_custom_width'] = array(
            'control' => array(
                'label'      => __('Maximum Content Width', 'responsi-ih'),
                'section'    => 'ih_settings',
                'settings'   => 'responsi_ih_custom_width',
                'type'       => 'icheckbox',
                'input_attrs' => array(
                    'class' => 'collapsed'
                )
            ),
            'setting' => array(
                'default'       => isset($responsi_options_ih['responsi_ih_custom_width']) ? $responsi_options_ih['responsi_ih_custom_width'] : 'false',
                'sanitize_callback' => 'responsi_sanitize_checkboxs',
                'transport' => 'postMessage'
            )
        );

        $_controls_settings['responsi_ih_width'] = array(
            'control' => array(
                //'label'      => __('Maximum Content Width', 'responsi-ih'),
                'description' => __( 'Maximum content width in pixels in large screens.', 'responsi-ih' ),
                'section'    => 'ih_settings',
                'settings'    => 'responsi_ih_width',
                'type'       => 'slider',
                'input_attrs'  => array(
                    'min' => '600',
                    'max' => '3000',
                    'step' => '1',
                    'class' => 'hide last'
                )
            ),
            'setting' => array(
                'default'       => isset($responsi_options_ih['responsi_ih_width']) ? $responsi_options_ih['responsi_ih_width'] : 1024,
                'sanitize_callback' => 'responsi_sanitize_slider',
                'transport' => 'postMessage'
            )
        );

        $_controls_settings['responsi_ih_animation'] = array(
            'control' => array(
                'label' => __('Animation', 'responsi-ih'),
                'section'    => 'ih_settings',
                'settings'   => 'multiple',
                'type'       => 'animation',
                'input_attrs' => array(
                )
            ),
            'setting' => array(
                'default'       => isset($responsi_options_ih['responsi_ih_animation']) ? $responsi_options_ih['responsi_ih_animation'] : array('type' => 'none', 'direction' => '', 'duration' => '1','delay' => '1'),
                'sanitize_callback' => 'responsi_sanitize_animation',
                'transport' => 'postMessage'
            )
        );

        $_controls_settings['responsi_ih_max'] = array(
            'control' => array(
                'label' => __('Logo Height', 'responsi-ih'),
                'section' => 'ih_settings',
                'settings' => 'responsi_ih_max',
                'type' => 'slider',
                'input_attrs' => array(
                    'min' => '20',
                    'max' => '200',
                    'step' => '1',
                    'class' => ''
                )
            ),
            'setting' => array(
                'type' => 'option',
                'sanitize_callback' => 'responsi_sanitize_slider',
                'default' => isset($responsi_options_ih['responsi_ih_max']) ? $responsi_options_ih['responsi_ih_max'] : 70,
                'transport'   => 'postMessage'
            )
        );

        $_controls_settings['responsi_ih_min'] = array(
            'control' => array(
                'label' => __('Logo Min Height', 'responsi-ih'),
                'section' => 'ih_settings',
                'settings' => 'responsi_ih_min',
                'type' => 'slider',
                'input_attrs' => array(
                    'min' => '20',
                    'max' => '200',
                    'step' => '1',
                    'class' => ''
                )
            ),
            'setting' => array(
                'type' => 'option',
                'sanitize_callback' => 'responsi_sanitize_slider',
                'default' => isset($responsi_options_ih['responsi_ih_min']) ? $responsi_options_ih['responsi_ih_min'] : 40,
                'transport'   => 'postMessage'
            )
        );

        $_controls_settings['responsi_ih_animation_speed'] = array(
            'control' => array(
                'label' => __('Animation Speed', 'responsi-ih'),
                'section' => 'ih_settings',
                'settings' => 'responsi_ih_animation_speed',
                'type' => 'slider',
                'input_attrs' => array(
                    'min' => '0',
                    'max' => '10',
                    'step' => '1',
                    'class' => ''
                )
            ),
            'setting' => array(
                'type' => 'option',
                'sanitize_callback' => 'responsi_sanitize_slider',
                'default' => isset($responsi_options_ih['responsi_ih_animation_speed']) ? $responsi_options_ih['responsi_ih_animation_speed'] : 3,
                'transport'   => 'postMessage'
            )
        );

        $_controls_settings['ih_settings_lb_tablet'] = array(
            'control' => array(
                'label'      => __('Tablet', 'responsi-ih'),
                'section'    => 'ih_settings',
                'type'       => 'ilabel'
            ),
            'setting' => array(
                'type' => 'option',
            )
        );

        $_controls_settings['responsi_ih_column_tablet'] = array(
            'control' => array(
                'section'    => 'ih_settings',
                'settings'   => 'multiple',
                'type'       => 'column',
                'choices' => array(
                    '1' => get_template_directory_uri() . '/functions/images/header-widgets-1.png',
                    '2' => get_template_directory_uri() . '/functions/images/header-widgets-2.png',
                    '3' => get_template_directory_uri() . '/functions/images/header-widgets-3.png',
                    '4' => get_template_directory_uri() . '/functions/images/header-widgets-4.png',
                ),
                'input_attrs' => array(
                    'validate' => true,
                )
            ),
            'setting' => array(
                'default'       => isset($responsi_options_ih['responsi_ih_column_tablet']) ? $responsi_options_ih['responsi_ih_column_tablet'] : array( 'col' => 4, 'col1' => '30', 'col2' => '10', 'col3' => '50', 'col4' => '10' ) ,
                'sanitize_callback' => 'responsi_sanitize_columns',
                'transport' => 'postMessage'
            )
        );

        $_controls_settings['responsi_ih_max_tablet'] = array(
            'control' => array(
                'label' => __('Logo Height', 'responsi-ih'),
                'section' => 'ih_settings',
                'settings' => 'responsi_ih_max_tablet',
                'type' => 'slider',
                'input_attrs' => array(
                    'min' => '20',
                    'max' => '200',
                    'step' => '1',
                    'class' => ''
                )
            ),
            'setting' => array(
                'type' => 'option',
                'sanitize_callback' => 'responsi_sanitize_slider',
                'default' => isset($responsi_options_ih['responsi_ih_max_tablet']) ? $responsi_options_ih['responsi_ih_max_tablet'] : 60,
                'transport'   => 'postMessage'
            )
        );

        $_controls_settings['responsi_ih_min_tablet'] = array(
            'control' => array(
                'label' => __('Logo Min Height', 'responsi-ih'),
                'section' => 'ih_settings',
                'settings' => 'responsi_ih_min_tablet',
                'type' => 'slider',
                'input_attrs' => array(
                    'min' => '20',
                    'max' => '200',
                    'step' => '1',
                    'class' => ''
                )
            ),
            'setting' => array(
                'type' => 'option',
                'sanitize_callback' => 'responsi_sanitize_slider',
                'default' => isset($responsi_options_ih['responsi_ih_min_tablet']) ? $responsi_options_ih['responsi_ih_min_tablet'] : 30,
                'transport'   => 'postMessage'
            )
        );

        $_controls_settings['responsi_ih_tablet_scroll'] = array(
            'control' => array(
                'label' => __('Header non Sticky', 'responsi-ih'),
                'section' => 'ih_settings',
                'settings' => 'responsi_ih_tablet_scroll',
                'type' => 'icheckbox',
            ),
            'setting' => array(
                'type' => 'option',
                'sanitize_callback' => 'responsi_sanitize_checkboxs',
                'default' => isset($responsi_options_ih['responsi_ih_tablet_scroll']) ? $responsi_options_ih['responsi_ih_tablet_scroll'] : 'true',
                'transport'   => 'postMessage'
            )
        );

        $_controls_settings['ih_settings_lb_mobile'] = array(
            'control' => array(
                'label'      => __('Mobile', 'responsi-ih'),
                'section'    => 'ih_settings',
                'type'       => 'ilabel'
            ),
            'setting' => array(
                'type' => 'option',
            )
        );

        $_controls_settings['responsi_ih_column_mobile'] = array(
            'control' => array(
                'section'    => 'ih_settings',
                'settings'   => 'multiple',
                'type'       => 'column',
                'choices' => array(
                    '1' => get_template_directory_uri() . '/functions/images/header-widgets-1.png',
                    '2' => get_template_directory_uri() . '/functions/images/header-widgets-2.png',
                    '3' => get_template_directory_uri() . '/functions/images/header-widgets-3.png',
                    '4' => get_template_directory_uri() . '/functions/images/header-widgets-4.png',
                ),
                'input_attrs' => array(
                    'validate' => true,
                )
            ),
            'setting' => array(
                'default'       => isset($responsi_options_ih['responsi_ih_column_mobile']) ? $responsi_options_ih['responsi_ih_column_mobile'] : array( 'col' => 4, 'col1' => '25', 'col2' => '10', 'col3' => '53', 'col4' => '12' ) ,
                'sanitize_callback' => 'responsi_sanitize_columns',
                'transport' => 'postMessage'
            )
        );

        $_controls_settings['responsi_ih_max_mobile'] = array(
            'control' => array(
                'label' => __('Logo Height', 'responsi-ih'),
                'section' => 'ih_settings',
                'settings' => 'responsi_ih_max_mobile',
                'type' => 'slider',
                'input_attrs' => array(
                    'min' => '20',
                    'max' => '200',
                    'step' => '1',
                    'class' => ''
                )
            ),
            'setting' => array(
                'type' => 'option',
                'sanitize_callback' => 'responsi_sanitize_slider',
                'default' => isset($responsi_options_ih['responsi_ih_max_mobile']) ? $responsi_options_ih['responsi_ih_max_mobile'] : 50,
                'transport'   => 'postMessage'
            )
        );

        $_controls_settings['responsi_ih_min_mobile'] = array(
            'control' => array(
                'label' => __('Logo Min Height', 'responsi-ih'),
                'section' => 'ih_settings',
                'settings' => 'responsi_ih_min_mobile',
                'type' => 'slider',
                'input_attrs' => array(
                    'min' => '20',
                    'max' => '200',
                    'step' => '1',
                    'class' => ''
                )
            ),
            'setting' => array(
                'type' => 'option',
                'sanitize_callback' => 'responsi_sanitize_slider',
                'default' => isset($responsi_options_ih['responsi_ih_min_mobile']) ? $responsi_options_ih['responsi_ih_min_mobile'] : 25,
                'transport'   => 'postMessage'
            )
        );

        $_controls_settings['responsi_ih_sitetitle_mobile_font'] = array(
            'control' => array(
                'label' => __('Site Title Font', 'responsi'),
                'section'    => 'ih_settings',
                'settings'   => 'multiple',
                'type'       => 'typography',
            ),
            'setting' => array(
                'default'       => isset($responsi_options_ih['responsi_ih_sitetitle_mobile_font']) ? $responsi_options_ih['responsi_ih_sitetitle_mobile_font'] : array('size' => '16','line_height' => '1.5','face' => 'Open Sans','style' => 'normal','color' => '#ffffff'),
                'sanitize_callback' => 'responsi_sanitize_typography',
                'transport' => 'postMessage'
            )
        );

        $_controls_settings['responsi_ih_mobile_scroll'] = array(
            'control' => array(
                'label' => __('Header non Sticky', 'responsi-ih'),
                'section' => 'ih_settings',
                'settings' => 'responsi_ih_mobile_scroll',
                'type' => 'icheckbox',
            ),
            'setting' => array(
                'type' => 'option',
                'sanitize_callback' => 'responsi_sanitize_checkboxs',
                'default' => isset($responsi_options_ih['responsi_ih_mobile_scroll']) ? $responsi_options_ih['responsi_ih_mobile_scroll'] : 'true',
                'transport'   => 'postMessage'
            )
        );

        $_controls_settings['ih_widget1_lb'] = array(
            'control' => array(
                'label'      => __('Widgets #1', 'responsi-ih'),
                'section'    => 'header_widgets',
                'type'       => 'ilabel'
            ),
            'setting' => array(
                'type' => 'option',
            )
        );

        $_controls_settings['responsi_ih_widget1_alignment'] = array(
            'control' => array(
                'label' => __('Alignment', 'responsi-ih'),
                'section' => 'header_widgets',
                'settings' => 'responsi_ih_widget1_alignment',
                'type' => 'iradio',
                'input_attrs' => array(
                    'checked_label' => 'ON',
                    'unchecked_label' => 'OFF',
                    'container_width' => 80,
                    'class' => ''
                ),
                'choices' => array(
                    'left' => 'Left',
                    'right' => 'Right',
                    'center' => 'Center'
                )
            ),
            'setting' => array(
                'type' => 'option',
                'sanitize_callback' => 'responsi_sanitize_choices',
                'default' => isset($responsi_options_ih['responsi_ih_widget1_alignment']) ? $responsi_options_ih['responsi_ih_widget1_alignment'] : 'right',
                'transport'   => 'postMessage'
            )
        );

        $_controls_settings['responsi_ih_widget1_alignment_tablet'] = array(
            'control' => array(
                'label' => __('Alignment in Tablet', 'responsi-ih'),
                'section' => 'header_widgets',
                'settings' => 'responsi_ih_widget1_alignment_tablet',
                'type' => 'iradio',
                'input_attrs' => array(
                    'checked_label' => 'ON',
                    'unchecked_label' => 'OFF',
                    'container_width' => 80,
                    'class' => ''
                ),
                'choices' => array(
                    'left' => 'Left',
                    'right' => 'Right',
                    'center' => 'Center'
                )
            ),
            'setting' => array(
                'type' => 'option',
                'sanitize_callback' => 'responsi_sanitize_choices',
                'default' => isset($responsi_options_ih['responsi_ih_widget1_alignment_tablet']) ? $responsi_options_ih['responsi_ih_widget1_alignment_tablet'] : 'right',
                'transport'   => 'postMessage'
            )
        );

        $_controls_settings['responsi_ih_widget1_alignment_mobile'] = array(
            'control' => array(
                'label' => __('Alignment in Mobile', 'responsi-ih'),
                'section' => 'header_widgets',
                'settings' => 'responsi_ih_widget1_alignment_mobile',
                'type' => 'iradio',
                'input_attrs' => array(
                    'checked_label' => 'ON',
                    'unchecked_label' => 'OFF',
                    'container_width' => 80,
                    'class' => ''
                ),
                'choices' => array(
                    'left' => 'Left',
                    'right' => 'Right',
                    'center' => 'Center'
                )
            ),
            'setting' => array(
                'type' => 'option',
                'sanitize_callback' => 'responsi_sanitize_choices',
                'default' => isset($responsi_options_ih['responsi_ih_widget1_alignment_mobile']) ? $responsi_options_ih['responsi_ih_widget1_alignment_mobile'] : 'right',
                'transport'   => 'postMessage'
            )
        );

        $_controls_settings['ih_widget2_lb'] = array(
            'control' => array(
                'label'      => __('Widgets #2', 'responsi-ih'),
                'section'    => 'header_widgets',
                'type'       => 'ilabel'
            ),
            'setting' => array(
                'type' => 'option',
            )
        );

        $_controls_settings['responsi_ih_widget2_alignment'] = array(
            'control' => array(
                'label' => __('Alignment', 'responsi-ih'),
                'section' => 'header_widgets',
                'settings' => 'responsi_ih_widget2_alignment',
                'type' => 'iradio',
                'input_attrs' => array(
                    'checked_label' => 'ON',
                    'unchecked_label' => 'OFF',
                    'container_width' => 80,
                    'class' => ''
                ),
                'choices' => array(
                    'left' => 'Left',
                    'right' => 'Right',
                    'center' => 'Center'
                )
            ),
            'setting' => array(
                'type' => 'option',
                'sanitize_callback' => 'responsi_sanitize_choices',
                'default' => isset($responsi_options_ih['responsi_ih_widget2_alignment']) ? $responsi_options_ih['responsi_ih_widget2_alignment'] : 'right',
                'transport'   => 'postMessage'
            )
        );

        $_controls_settings['responsi_ih_widget2_alignment_tablet'] = array(
            'control' => array(
                'label' => __('Alignment in Tablet', 'responsi-ih'),
                'section' => 'header_widgets',
                'settings' => 'responsi_ih_widget2_alignment_tablet',
                'type' => 'iradio',
                'input_attrs' => array(
                    'checked_label' => 'ON',
                    'unchecked_label' => 'OFF',
                    'container_width' => 80,
                    'class' => ''
                ),
                'choices' => array(
                    'left' => 'Left',
                    'right' => 'Right',
                    'center' => 'Center'
                )
            ),
            'setting' => array(
                'type' => 'option',
                'sanitize_callback' => 'responsi_sanitize_choices',
                'default' => isset($responsi_options_ih['responsi_ih_widget2_alignment_tablet']) ? $responsi_options_ih['responsi_ih_widget2_alignment_tablet'] : 'right',
                'transport'   => 'postMessage'
            )
        );

        $_controls_settings['responsi_ih_widget2_alignment_mobile'] = array(
            'control' => array(
                'label' => __('Alignment in Mobile', 'responsi-ih'),
                'section' => 'header_widgets',
                'settings' => 'responsi_ih_widget2_alignment_mobile',
                'type' => 'iradio',
                'input_attrs' => array(
                    'checked_label' => 'ON',
                    'unchecked_label' => 'OFF',
                    'container_width' => 80,
                    'class' => ''
                ),
                'choices' => array(
                    'left' => 'Left',
                    'right' => 'Right',
                    'center' => 'Center'
                )
            ),
            'setting' => array(
                'type' => 'option',
                'sanitize_callback' => 'responsi_sanitize_choices',
                'default' => isset($responsi_options_ih['responsi_ih_widget2_alignment_mobile']) ? $responsi_options_ih['responsi_ih_widget2_alignment_mobile'] : 'right',
                'transport'   => 'postMessage'
            )
        );

        $_controls_settings = apply_filters('_ih_controls_settings', $_controls_settings);
        $controls_settings  = array_merge($controls_settings, $_controls_settings);
        return $controls_settings;
    }
}
?>
