<?php
require dirname(dirname(dirname(__FILE__))). '/qa-include/qa-base.php';    //include question2answer environment
require dirname(dirname(dirname(__FILE__))). '/qa-include/app/users.php';    //include users
include_once ('lib/hw-imgur-api.php');
include_once ('functions.php');

//valid
$userid = qa_get_logged_in_userid();
if(!$userid) exit('Unauthorize');   //user must to login

$action = isset($_GET['action'])? $_GET['action'] : ''; //action
$nonce = isset($_POST['_nonce'])? $_POST['_nonce'] : '';  //nonce string
//return json data
$return = array();

//valid action token
if(! NonceUtil::check( $action,$nonce) ) {
    $return['error'] = '1';
    $return['result'] = 'invalid token';
    $return['message'] = '[invalid token] Phiên làm việc đã hết, lưu bài viết và nạp lại trang để có thể sử dụng tính năng.';
    hw_print_json_ajax($return);
    exit();
}


if($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {
    if(hw_allow_referer())
    {
        //authorize to get token
        HW_IMGUR::init();

        /**
         * load images in album
         */
        if($action == 'load_images') {
            $album = isset($_POST['al'])? $_POST['al'] : 'bld18'; //or get default album
            $images = HW_IMGUR::get_images_album($album);

            $result = array();
            //$html = '<div class="album-images">';
            //catch error if imgur token expire
            if(isset($images->error)) {
                $return['result'] = 'error';
                $return['error'] = 1;
                $return['message'] = $images->message;
            }
            elseif(is_array($images)) {
                foreach ($images as $img) {
                    //$html .= '<div class="image-item" id="'. $img->id .'"><img class="thumb" src="'.$img->link. '"/></div>';
                    if(!is_object($img) && !is_array($img)) continue;
                    $result[] = array(
                        'link' => $img->link,
                        'id' => $img->id,
                        'title' => $img->title,
                        'description' => $img->description
                    );
                }
                $return['result'] = 'success';
                $return['data'] = $result;
            }
            //$html .= '</div>';

            hw_print_json_ajax($return);
        }
        /**
         * check valid token
         */
        elseif($action == 'valid_session') {
            //valid token if expire then create new one
            HW_IMGUR::get_token();
            hw_print_json_ajax(array(
                'result' => 'done',
                'error' => 0,
                'message' => ''
            ));
        }
    }
}

?>