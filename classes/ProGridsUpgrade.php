<?php
/**
 * Class ProGridsUpgrade
 *
 * This class contains all upgrade logic
 *
 * Don't remove upgrade scripts, just add new ones
 */
class ProGridsUpgrade
{

    public static function upgrade() {
        $currVersion = proGrids_currentVersion();
        $version = ProGrids::savedVersion();

        if (version_compare($version, $currVersion, '<')) {
            if (version_compare($version, '3.0.0', '<')) {
                // only upgrade to version 3 if less then 3
                self::upgradeToVersion3();
            }
            ProGrids::savedVersion($currVersion);

            wp_remote_post(PROGRIDS_BASE_URL.'/widget/wordpress', array(
                'body' => array(
                    'siteurl' => proGrids_site_url(),
                    'active' => 1,
                    'pluginVersion' => $currVersion
                )
            ));
        }
    }

    /**
     * Migrate function to update old settings format to new settings format for v3.0.0
     */
    private static function upgradeToVersion3() {
        $op2 = get_option('proGrids_options2');
        $widgetId = false;

        if ($op2 && gettype($op2) === 'object') {
            foreach($op2 as $key=>$val) {
                if ($key === 'widgetId') {
                    $widgetId = $val;
                    break;
                }
            }
        }

        if (!$widgetId) {
            // dont do anything
            return;
        }

        ob_start();
        ProGridsController::render('upgradeCode', array('widgetId'=>$widgetId));
        $installationCode = ob_get_clean();

        update_option('progrids_code', $installationCode);
    }
}