<?php
/**
 * Original script source
 * 
 * http://playground.zaplabs.com/sandbox/qa/stackexchange/stackoverflow/17269448/index.php
 * http://playground.zaplabs.com/sandbox/qa/stackexchange/stackoverflow/17269448/index.php.source
 */
require (dirname(dirname(dirname(dirname(__FILE__))))). '/qa-include/qa-base.php';    //include question2answer environment
require (dirname(dirname(dirname(dirname(__FILE__))))). '/qa-include/app/users.php';    //include question2answer environment
include_once (dirname(dirname(dirname(__FILE__)))). ('/hw-imgur/lib/hw-imgur-api.php');
include_once (dirname(dirname(dirname(__FILE__)))). ('/hw-imgur/functions.php');

//require logged in user
if(!qa_get_logged_in_userid()) exit('unauthorize');

$nonce = isset($_POST['_nonce'])? $_POST['_nonce'] : '';    //nonce upload security
$return_json = array();

//valid action token
if(! NonceUtil::check('valid_token_upload' , $nonce) ) {
    $return_json['error'] = 1;
    $return_json['message'] = '[invalid token] Vui lòng load lại trang web.';
    hw_print_json_ajax($return_json);
    exit();
}

if(isset($_FILES['file'])) {
	$file = $_FILES['file'];	//single file
	$filename = $file['tmp_name'];
    $name = basename($file["name"]);    //file name

	$handle = fopen($filename, 'r');
	$data = fread($handle, filesize($filename));
	fclose($handle);
	$pvars   = array('image' => base64_encode($data));
	
	//$image = file_get_contents($_FILES['file']['tmp_name']);
    $args = array(
        'name' => $name,
        'title' => $name,
        #'description' => ''
    );
    $reply = HW_IMGUR::upload_image2album(base64_encode($data), HW_IMGUR::main_album, $args);
	/*
	$client_id = "9b5122a3d34e478";
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, 'https://api.imgur.com/3/image.json');
	curl_setopt($ch, CURLOPT_POST, TRUE);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array( 'Authorization: Client-ID ' . $client_id ));
	curl_setopt($ch, CURLOPT_POSTFIELDS, $pvars );
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
	curl_setopt($ch, CURLOPT_TIMEOUT, 100);

	$reply = curl_exec($ch);
	curl_close($ch);

	$reply = json_decode($reply);*/
    if(empty($reply->error)) {
        $return_json['result'] = 'success';
        $return_json['link'] = $reply->data->link;
        $return_json['title'] = $name;

    }
    else {
        $return_json = $reply;
    }
    hw_print_json_ajax($return_json);
	//printf('<img height="180" src="%s" >', $newimgurl);
	exit();
}
?>