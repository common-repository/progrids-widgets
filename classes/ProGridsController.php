<?php

/**
 * Class ProGridsController
 *
 * This class contains all data loading and view rendering as well as enqueuing the scripts required by each page.
 */
class ProGridsController
{
    public $action;

    public function __construct()
    {
        $this->action = isset($_GET['page']) ? $_GET['page'] : '';
    }


    /////////////////////////////////////////////////////////////////////////////////////////////////////////
    //
    //  UTIL FUNCTIONS
    //
    /////////////////////////////////////////////////////////////////////////////////////////////////////////

    public static function render($view = '', $args = array())
    {
        if ($view) {
            extract($args);
            require(PROGRIDS_VIEW_DIR . '/' . $view . '.php');
        }
    }

    /////////////////////////////////////////////////////////////////////////////////////////////////////////
    //
    //  CONTROLLER ACTIONS
    //
    /////////////////////////////////////////////////////////////////////////////////////////////////////////

    public function widget($content = '')
    {
        $check = is_single(); // in future: possibly allow different checks based on options
        if ($check && !is_preview() && (!function_exists('is_main_query') || is_main_query())) {
            $widgetId = intval(get_option('progrids_widgetid'));
            if ($widgetId) {
                ob_start();
                self::render('installationCode', array('widgetId'=>$widgetId));
                $content .= ob_get_clean();
            }
        }
        return $content;
    }

    public function main()
    {
        self::render('main');
    }
}