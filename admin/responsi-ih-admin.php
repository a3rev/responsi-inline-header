<?php

namespace A3Rev\RIH;

class Admin
{
    var $admin_page;

    public function __construct()
    {
        $this->init();
    }

    public function init()
    {
        add_filter('responsi_expanded_support', array( $this, 'responsi_expanded_support' ));
        add_filter('filter_responsi_template_developer', array( &$this, '_add_filter_responsi_template_developer'), 11);
    }

    public function responsi_expanded_support($arrays)
    {
        $arrays['responsithemes_ih'] = 'ih';
        return $arrays;
    }

    public static function _add_filter_responsi_template_developer($framework_options)
    {
        $shortname = 'framework_a3rev';

        global $wp_roles;
        if (! isset($wp_roles)) {
            $wp_roles = new \WP_Roles();
        }
        $roles = $wp_roles->get_names();

        $add_menu_place = 'Developer Settings Menu';

        $new_options = array();

        foreach ($framework_options as $option) {
            $new_options[] = $option;

            if ($option['name'] == $add_menu_place) {
                $new_options[] = array(     'name' => __('Inline Header Customize Menu', 'responsi-ih'),
                    "class" => "visible",
                    'id' => $shortname . '_permissions_ih_roles',
                    'std'=> array('administrator'),
                    "options" => $roles,
                    "placeholder" => 'Select Roles who can access',
                    'type' => 'chosen-multicheck' );
            }
        }

        return $new_options;
    }
}
