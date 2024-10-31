<?php

/**
 * Plugin Name: ProGrids Widget Plugin
 * Description: ProGrids.com widget management plugin
 * Version:     3.0.1
 * Author:      Sazze, Inc.
 * Author URI: http://progrids.com/
 * Year:        2014
 * License:     GPLv2
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 */


define('PROGRIDS_REQUIRED_WP_VERSION', '3.2');
define('PROGRIDS_REQUIRED_PHP_VERSION', '5.2.4');

if (!defined('PROGRIDS_DEV_ENVIRON')) {
    define('PROGRIDS_DEV_ENVIRON', file_exists(dirname(__FILE__) . '/development'));
}

define('PROGRIDS_PLUGIN_DIR', dirname(__FILE__));
define('PROGRIDS_PLUGIN_URL', plugins_url('', __FILE__));
define('PROGRIDS_VIEW_DIR', dirname(__FILE__) . '/views');

define('PROGRIDS_PROTOCOL', PROGRIDS_DEV_ENVIRON ? 'http' : 'https');
define('PROGRIDS_API_PROTOCOL', PROGRIDS_DEV_ENVIRON ? 'http' : 'https');

define('PROGRIDS_HOST', (PROGRIDS_DEV_ENVIRON ? 'local.' : '') . 'progrids.com');
define('PROGRIDS_API_HOST', '' . (PROGRIDS_DEV_ENVIRON ? 'api.local.' : 'api.') . 'progrids.com');
define('PROGRIDS_WIDGETS_HOST', '' . (PROGRIDS_DEV_ENVIRON ? 'widgets.local.' : 'widgets.') . 'progrids.com');

define('PROGRIDS_BASE_URL', PROGRIDS_PROTOCOL . '://' . PROGRIDS_HOST);
define('PROGRIDS_API_BASE_URL', PROGRIDS_API_PROTOCOL . '://' . PROGRIDS_API_HOST);
define('PROGRIDS_WIDGETS_BASE_URL', PROGRIDS_API_PROTOCOL . '://' . PROGRIDS_WIDGETS_HOST);


/**
 * Plugin Activation hook function to check for Minimum PHP and WordPress versions
 */
function proGrids_activate() {
    global $wp_version;
    if (version_compare(PHP_VERSION, PROGRIDS_REQUIRED_PHP_VERSION, '<')) {
        $flag = 'PHP';
    } else if (version_compare($wp_version, PROGRIDS_REQUIRED_WP_VERSION, '<')) {
        $flag = 'WordPress';
    } else {
        //required version met
        wp_remote_post(PROGRIDS_BASE_URL.'/widget/wordpress', array(
            'body' => array(
                'siteurl' => proGrids_site_url(true),
                'active' => 1,
                'pluginVersion' => proGrids_currentVersion()
            )
        ));
        return;
    }
    $version = 'PHP' == $flag ? PROGRIDS_REQUIRED_PHP_VERSION : PROGRIDS_REQUIRED_WP_VERSION;
    deactivate_plugins(basename(__FILE__));
    wp_die(
        '<p>The <strong>ProGrids</strong> plugin requires ' . $flag . '  version ' . $version . ' or greater.</p>' ,
        'Plugin Activation Error',
        array('response' => 200, 'back_link' => true ));
}

/**
 * Plugin hook to delete all options when the plugin is deleted
 */
function proGrids_cleanup() {
    foreach (ProGrids::$optionNames as $name) {
        delete_option($name);
    }
}

/**
 * Plugin Deactivation hook function to inform Progrids that plugin was deactivated
 */
function proGrids_deactivate() {
    wp_remote_post(PROGRIDS_BASE_URL.'/widget/wordpress', array(
        'body' => array(
            'siteurl' => proGrids_site_url(),
            'active' => 0,
            'pluginVersion' => proGrids_currentVersion()
        )
    ));
}

/**
 * A class autoloader
 *
 * @param $class string name of the class to load
 * @return bool
 */
function proGrids_autoload($class) {
    $f = dirname(__FILE__) . '/classes/' . $class . '.php';

    if (file_exists($f)) {
        require_once($f);
        return true;
    }
    return false;
}

/**
 * Get the current version of the plugin
 *
 * @return string
 */
function proGrids_currentVersion() {
    $pluginData = get_plugin_data(__FILE__);
    return $pluginData['Version'];
}

/**
 * Get the site url saved when plugin was activated
 *
 * @param bool $forceNew Force to get site url from WP
 * @return string Site URL
 */
function proGrids_site_url($forceNew = false) {
    $pg_siteurl = get_option('proGrids_siteurl');
    if ($forceNew || !$pg_siteurl) {
        update_option('proGrids_siteurl', site_url());
        return site_url();
    } else {
        return $pg_siteurl;
    }
}


register_activation_hook(__FILE__, 'proGrids_activate');
register_deactivation_hook(__FILE__, 'proGrids_deactivate');
register_uninstall_hook(__FILE__, 'proGrids_cleanup' );
spl_autoload_register('proGrids_autoload');

$pg = new ProGrids();
$pg->initialize();