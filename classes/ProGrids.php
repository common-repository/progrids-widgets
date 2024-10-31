<?php

class ProGrids
{
    const OPTION_GROUP_NAME = 'progrids_options';

    public static $optionNames = array(
        'progrids_code',
        'progrids_widgetid',
        'proGrids_version',
        'proGrids_siteurl'
    );

    public function __construct() {
        $this->controller = new ProGridsController();
    }

    /**
     * Initialization function unrelated to WP hook API.
     * Adds plugin universal actions/filters
     */
    public function initialize() {
        add_action('admin_menu', array($this, 'adminMenu'));
        add_action('admin_init', array($this, 'adminInit'));

        // widget controller/action to append to content
        add_filter('the_content', array($this->controller, 'widget'));

        add_filter('pre_update_option_progrids_code', array($this, 'preUpdateCodeFilter'), 10, 2);
        add_action('update_option_progrids_code', array($this, 'postUpdateCodeAction'), 10, 2);
    }

    /**
     * Implementation of WP 'admin_menu' action hook
     */
    public function adminMenu() {
        add_menu_page('ProGrids.com Widget Settings', 'ProGrids', 'administrator', 'proGridsPlugin',
            array($this->controller, 'main'), PROGRIDS_PLUGIN_URL . '/images/proGridsFav.png');
    }

    /**
     * Implementation of WP 'admin_init' action hook
     */
    public function adminInit() {
        // enqueue admin scripts and styles
        wp_enqueue_style('proGrids-adminCss', PROGRIDS_PLUGIN_URL . '/css/admin.css');

        register_setting(self::OPTION_GROUP_NAME, 'progrids_code', 'htmlentities');

        if (current_user_can('administrator')) {
            ProGridsUpgrade::upgrade();
        }
    }

    /**
     * Implementation of filter pre_update_option_progrids_code fired before the option is updated
     *
     * @param $value
     * @param $old_value
     * @return mixed Must return the new value
     */
    public function preUpdateCodeFilter( $value, $old_value ) {
        $matches = array();
        preg_match('/progrids\d+/i', $value, $matches);
        if (count($matches)>0) {
            $widgetId = intval(substr($matches[0], 8));
            update_option('progrids_widgetid', $widgetId);
            wp_remote_post(PROGRIDS_BASE_URL.'/widget/wordpress', array(
                'body' => array(
                    'siteurl' => proGrids_site_url(),
                    'widgetId' => $widgetId,
                    'pluginVersion' => proGrids_currentVersion()
                )
            ));
        }

        return $value;
    }

    /**
     * Implementation of action update_option_progrids_code fired after the option is updated
     * @param $value
     * @param $old_value
     */
    public function postUpdateCodeAction( $value, $old_value ) {

    }

    /**
     * Returns the latest saved plugin version. Also can be used to save current version
     * @param string $newVersion
     * @return bool|mixed|void
     */
    public static function savedVersion($newVersion = '') {
        if ($newVersion) {
            return update_option('proGrids_version', $newVersion);
        } else {
            return get_option('proGrids_version', '');
        }
    }

}