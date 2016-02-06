<?php
//nonce library
include_once (QA_HTML_THEME_LAYER_DIRECTORY. '/lib/NonceUtil.php');

//include PHP Hooks lib
#include_once (QA_HTML_THEME_LAYER_DIRECTORY. '/lib/PHP-Hooks/php-hooks.php');
#global $hooks;

#@session_start();$_SESSION['qah']='layer';
/**
 * Created by PhpStorm.
 * User: Hoang
 * Date: 20/07/2015
 * Time: 13:12
 */
class qa_html_theme_layer extends qa_html_theme_base
{
    /**
     * @var
     */
    private $ifly_html_code;

    public function initialize() {
        if( qa_opt('hw_enable_iflychat')) {  //enable iflychat
            //moved to 'before_render_page' hook
            global $iflychat_userinfo;
            global $hw_iflychat;    //iflychat instance

            if(!empty($iflychat_userinfo)) {
                $userdedetail = $iflychat_userinfo->getUserDetails() ;
                $iflychat_settings = new iFlyChatSettings();
                $hw_iflychat = new iFlyChat($iflychat_settings->iflychat_settings, $userdedetail);
                $this->ifly_html_code = $hw_iflychat->getHtmlCode();
            }

        }
    }
    function head_custom()
    {

        parent::head_custom();

        //create upload token
        $session_token =  NonceUtil::generate( 'valid_session', 1800);
        $upload_token =  NonceUtil::generate( 'valid_token_upload', 1800);
        $gallery_token = NonceUtil::generate( 'load_images',1800 );

        $this->output_raw(
            "<script>\n" .
            "var hw_imgur = {
                ajaxHandler: qa_root + 'qa-plugin/hw-imgur/ajax.php' ,
                session_token : '" .$session_token. "',
                upload_token : '" .$upload_token. "',
                gallery_token : '" .$gallery_token. "'
            };
                \n" .
            "</script>\n\n"
        );
        $this->output_raw("<script src=\"" .QA_HTML_THEME_LAYER_URLTOROOT. "/js/script.js\"></script>");
        $this->output_raw("<script src=\"" .QA_HTML_THEME_LAYER_URLTOROOT. "/js/hw-jquery-plugin.js\"></script>");
        $this->output_raw("<script src=\"" .QA_HTML_THEME_LAYER_URLTOROOT. "/js/jquery-ui.js\"></script>");
        $this->output_raw("<link rel=\"stylesheet\" type=\"text/css\" href=\"" .QA_HTML_THEME_LAYER_URLTOROOT. "style.css\"/>");
        //print iflychat
        global $hw_iflychat;
        //if(!empty($hw_iflychat)) echo $hw_iflychat->getHtmlCode();
        if(!empty($this->ifly_html_code) ) echo $this->ifly_html_code;
    }
}