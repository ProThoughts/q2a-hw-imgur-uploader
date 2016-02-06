<?php
//nonce library
include_once ('lib/NonceUtil.php');
/**
 * return imgur token from my account authorized
 * @return mixed
 */
function hw_get_imgur_token () {
    $token = qa_opt('hw_imgur_token');
    if($token) {
        return unserialize(base64_decode($token));
    }
}

/**
 * refresh new token
 * @param $token
 */
function hw_update_imgur_token($token = array()){
    $token['saved_time'] = strtotime(date('Y-m-d H:i:s')); //set current time to save token
    $token = base64_encode(serialize($token));
    qa_opt('hw_imgur_token', $token);
}
function _print($txt){
    echo '<textarea>';
    print_r($txt);
    echo '</textarea>';
}

/**
 * generate token (use Nonce php library instead)
 */
function hw_create_token() {
    @session_start();
    $token = md5(rand(1000,9999)); //you can use any encryption
    $_SESSION['token'] = $token; //store it as session variable
}

/**
 * check for allowing referer
 * @param $host host name
 * @return bool
 */
function hw_allow_referer($host = '') {
    $allow = array('localhost','hoangweb.net','vietcodex.com');
    if($host) array_push($allow, $host);

    if(isset($_SERVER['HTTP_REFERER'])) {
        $url = parse_url($_SERVER['HTTP_REFERER']);
        return in_array($url['host'], $allow);
    }
    return false;
}

/**
 * print json ajax result
 * @param $data
 */
function hw_print_json_ajax($data) {
    echo json_encode($data);
}

/**
 * send mail via google script
 */
function hw_send_mail() {

}

/**
 * init current user logined
 */
function hw_init_current_userlogin() {
    global $iflychat_userinfo;
    //init user
    if(qa_is_logged_in()) {
        $handle= qa_get_logged_in_handle(); //user name
        $userid = qa_get_logged_in_userid();    //user id

        $user=qa_db_select_with_pending(
            qa_db_user_account_selectspec($handle, false)
        );
        //get user avatar src
        $avatar_src = hw_get_user_avatar_src($user['flags'], $user['email'], $user['avatarblobid']);
        if(empty($avatar_src)) {
            $avatar_src = 'https://iflychat.com/sites/all/modules/drupalchat/themes/light/images/default_avatar.png';
        }
        //set detail current user to chat
        $iflychat_userinfo = new iFlyChatUserDetails($handle, $userid);
        $iflychat_userinfo->setIsAdmin(TRUE);
        $iflychat_userinfo->setAvatarUrl($avatar_src);
        $iflychat_userinfo->setProfileLink(qa_opt('site_url'). 'user/'. $handle);
        $iflychat_userinfo->setRoomRoles(array());
        $iflychat_userinfo->setRelationshipSet(FALSE);
        //$iflychat_userinfo->setAllRoles(array('1'=>'admin'));
    }
}

/**
 * @param $flags
 * @param $email
 * @param $blobid
 * @param $size
 * @return mixed|string
 */
function hw_get_user_avatar_src($flags, $email, $blobid) {
    $size = qa_opt('avatar_users_size');
    $img_src = '';

    if (qa_opt('avatar_allow_gravatar') && ($flags & QA_USER_FLAGS_SHOW_GRAVATAR)){
        $img_src = (qa_is_https_probably() ? 'https' : 'http').
            '://www.gravatar.com/avatar/'.md5(strtolower(trim($email))).'?s='.(int)$size;
    }
    elseif (qa_opt('avatar_allow_upload') && (($flags & QA_USER_FLAGS_SHOW_AVATAR)) && isset($blobid)){
        $img_src = qa_path_html('image', array('qa_blobid' => $blobid  , 'qa_size' =>  $size ), null, QA_URL_FORMAT_PARAMS);
    }
    elseif ( (qa_opt('avatar_allow_gravatar')||qa_opt('avatar_allow_upload')) && qa_opt('avatar_default_show') && strlen(qa_opt('avatar_default_blobid')) )
        $img_src = qa_path_html('image', array('qa_blobid' => $blobid  , 'qa_size' => $size  ), null, QA_URL_FORMAT_PARAMS);

    return $img_src;
}