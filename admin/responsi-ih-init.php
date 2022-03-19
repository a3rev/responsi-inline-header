<?php
global $cart_icons_lists;

$cart_icons_lists = array(
    'fab fa-opencart' => __('Opencart', 'responsi-ih'),
    'fas fa-cart-plus' => __('Plus', 'responsi-ih'),
    //'fal fa-cart-plus' => __( 'Plus Pro', 'responsi-ih' ),
    'fas fa-cart-arrow-down' => __('Arrow down', 'responsi-ih'),
    //'fal fa-cart-arrow-down' => 'Arrow down Pro', 'responsi-ih' ),
    'fas fa-shopping-cart' => __('Shopping', 'responsi-ih'),
    //'fal fa-shopping-cart' => __( 'Shopping cart', 'responsi-ih' ),
    'fas fa-luggage-cart' => __('Luggage', 'responsi-ih'),
    //'fal fa-luggage-cart' => __( 'Luggage Cart', 'responsi-ih' ),
    'fas fa-shopping-basket' => __('Basket', 'responsi-ih'),
    'fas fa-shopping-bag' => __('Bag', 'responsi-ih'),
);

function _customize_menu_ih()
{
    $_permisstion = true;

    if (function_exists('framework_a3rev_super_user_menu_permission')) {
        if (framework_a3rev_super_user_menu_permission('framework_a3rev_permissions_ih_roles')) {
            return true;
        } else {
            return false;
        }
    } else {
        return true;
    }
    return true;
}

function _check_customize_wc_plugin_installed()
{
    if (!in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {
        return false;
    } else {
        return true;
    }
    return true;
}

/**
 * Register Activation Hook
 */
function responsi_ih_install()
{
    global $responsi_ih;
    $responsi_ih->responsi_build_css_theme_actived();
}

/**
* Load Localisation files.
*
* Note: the first-loaded translation file overrides any following ones if the same translation is present.
*
* Locales found in:
*         - WP_LANG_DIR/responsi-above-header-widgets/responsi-ih-LOCALE.mo
*          - /wp-content/plugins/responsi-above-header-widgets/languages/responsi-ih-LOCALE.mo (which if not found falls back to)
*          - WP_LANG_DIR/plugins/responsi-ih-LOCALE.mo
*/
function ih_load_plugin_textdomain()
{
    $locale = apply_filters('plugin_locale', get_locale(), 'responsi-ih');
    load_textdomain('responsi-ih', WP_LANG_DIR . '/responsi-above-header-widgets/responsi-ih-' . $locale . '.mo');
    load_plugin_textdomain('responsi-ih', false, RESPONSI_IH_FOLDER . '/languages/');
}

/**
 * Load languages file
 */
function load_plugin_textdomain_responsi_ih()
{
    if (get_option('responsi_ih_installed')) {
        delete_option('responsi_ih_installed');
        responsi_ih_install();
    }
    ih_load_plugin_textdomain();
}
// Add language
add_action('init', 'load_plugin_textdomain_responsi_ih');

function responsi_ih_settings_link($links)
{
    $customize_url =  ( ( is_ssl() || force_ssl_admin() ) ? str_replace('http:', 'https:', admin_url('customize.php')) : str_replace('https:', 'http:', admin_url('customize.php')) ) ;
    $customize_url .= '?autofocus[panel]=header_settings_panel';
    $settings_link = '<a href="'.$customize_url.'">'.__('Settings', 'responsi-ih').'</a>';
    array_unshift($links, $settings_link);
    return $links;
}
 
add_filter("plugin_action_links_".RESPONSI_IH_NAME, 'responsi_ih_settings_link');
