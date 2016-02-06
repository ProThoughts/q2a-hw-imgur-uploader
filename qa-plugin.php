<?php
/**
Plugin Name: Hoangweb
Plugin URI: http://hoangweb.com/
Plugin Description: Imgur API integration
Plugin Version: 1.0
Plugin Date: 2013-12-16
Plugin Author: Mr.Hoang
Plugin Author URI: http://hoangweb.com
Plugin License: GPLv2
Plugin Minimum Question2Answer Version: 1.6
 */

if (!defined('QA_VERSION')) { // don't allow this page to be requested directly from browser
    header('Location: ../../');
    exit;
}
define('HWIMGUR_PLUGIN_DIR', dirname(__FILE__));

//include PHP Hooks
include_once (dirname(__FILE__). '/lib/PHP-Hooks/php-hooks.php');
global $hooks;

include_once ('functions.php'); //function ultilities

$hooks->add_action('before_render_page','hw_register_iflychat');
function hw_register_iflychat() {
    //  Make sure to use the above code before printing any content via PHP,
    if(qa_opt('hw_enable_iflychat')) {
        global $iflychat_userinfo;
        require_once(QA_BASE_DIR. 'hw-libs/iflychat/iflychat-php/iflychatsettings.php');
        require_once(QA_BASE_DIR. 'hw-libs/iflychat/iflychat-php/iflychatuserdetails.php');
        require_once(QA_BASE_DIR. 'hw-libs/iflychat/iflychat-php/iflychat.php');

        //init user
        hw_init_current_userlogin();

    }
}

qa_register_plugin_phrases('qa-imgur-lang.php', 'hw_lang');
qa_register_plugin_module('page', 'qa-imgur-page.php', 'hw_admin_page', 'Hoangweb Plugin');
qa_register_plugin_layer('qa-imgur-layer.php', 'Hoangweb layer');

