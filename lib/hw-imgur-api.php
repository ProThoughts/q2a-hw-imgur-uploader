<?php
class HW_IMGUR {
    const client_id = '9b5122a3d34e478';    //client id
    const client_secret = '20b9d214b181d01e2014e4f5eacc991af358add9';   //client secret

    const upload_image = 'https://api.imgur.com/3/image.json';
    static $imgur_authorize = 'https://api.imgur.com/oauth2/authorize?response_type=token&client_id={client_id}';
    const refresh_token = 'https://api.imgur.com/oauth2/token';
    const get_album_images= 'https://api.imgur.com/3/album/{album}/images';
    const main_album = 'bld18';

    /**
     * store token
     * @var string
     */
    public static $token = '';

    public static function init() {
        self::$imgur_authorize = str_replace('{client_id}', self::client_id, self::$imgur_authorize);
    }
    /**
     * @param $url
     * @param array $opts
     * @param array $data
     * @param bool $use_cookie
     * @param null $callback
     * @return mixed
     */
    public static function curl_post($url, $opts = array(),$data = array(),$use_cookie = true, $callback = null){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        #curl_setopt($ch, CURLOPT_HTTPHEADER, array( 'Authorization: Client-ID ' . client_id ));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        #curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT ,0);
        curl_setopt($ch, CURLOPT_TIMEOUT, 100);

        //cookie
        if($use_cookie){
            curl_setopt($ch, CURLOPT_COOKIEJAR, "cookie.txt");
            curl_setopt($ch, CURLOPT_COOKIEFILE, "cookie.txt");          #reading
        }
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);

        if(is_array($opts) && count($opts)) curl_setopt_array($ch, $opts);
        $resp = curl_exec($ch);
        //callback
        if(is_callable($callback)) call_user_func($callback, array($ch,$resp) );

        curl_close($ch);
        return $resp;
    }

    /**
     * @param $url
     * @param array $opts
     * @param bool $refresh_cookie
     * @return mixed
     */
    public static function curl_get($url, $opts = array() ,$refresh_cookie = false){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        //curl_setopt($ch, CURLOPT_HTTPHEADER, array( 'Authorization: Client-ID ' . client_id ));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        if(is_array($opts) && count($opts)) curl_setopt_array($ch, $opts);
        //cookie
        if($refresh_cookie) {
            curl_setopt($ch, CURLOPT_COOKIESESSION, true);
        }

        $resp = curl_exec($ch);
        curl_close($ch);
        return $resp;
    }
    static function _print($txt){
        echo '<textarea>';
        print_r($txt);
        echo '</textarea>';
    }

    /**
     * get imgur token
     * @param $force force to refresh token
     * @return mixed
     */
    public static function get_token($force = false) {
        #if(isset($_SESSION['hw_imgur_token'])) return unserialize($_SESSION['hw_imgur_token']);
        $token = hw_get_imgur_token();
        if($token && isset($token['access_token']) && isset($token['refresh_token']) ) {
            $current_time  = strtotime(date('Y-m-d H:i:s'));
            //echo $current_time,'-',$token['saved_time'], '=>',($current_time-$token['saved_time']);
            //token expired need to refresh new one
            if( (isset($token['saved_time']) && $current_time - $token['saved_time'] >= 1500) || $force == true) {    //3600-1000
                self::refresh_token($token['refresh_token']);
            }
        }
        else {  //first generate token
            self::fetch_token();
        }
        return hw_get_imgur_token();
    }

    /**
     * set token
     * @param $token
     */
    public static function set_token($token) {
        @session_start();
        #$_SESSION['hw_imgur_token'] = serialize($token);
        if(is_string($token)) $token = json_decode($token);
        hw_update_imgur_token((array)$token);
    }
    /**
     * fetch token
     */
    public static function fetch_token() {

        $result = self::curl_get(self::$imgur_authorize, null,true);
        $output = preg_match_all('/<button.+value=[\'"]([^\'"]+)[\'"].*>/i', $result, $matches);
        $accept_code = $matches [1][0];

        //_print($result);exit();

        $result = self::curl_post(self::$imgur_authorize . '', array(
                CURLOPT_HEADER => 1
            ),array(
                'username' => 'hoangsoft90@gmail.com',
                'password' => 'code837939',
                'allow' => $accept_code
            ), true, function($data){
                @session_start();
                $ch = $data[0];		//curl instance
                $http_data = $data[1];	//curl_exec response

                //get header info
                $curl_info = curl_getinfo($ch);
                $headers = substr($http_data, 0, $curl_info["header_size"]); //split out header

                //Parse the headers to get the new URL
                preg_match("!\r\n(?:Location|URI): *(.*?) *\r\n!", $headers, $matches);
                $url = $matches[1];

                //extract token from redirect url
                $parse = parse_url($url);
                parse_str($parse['fragment'], $token);
                #$token = json_encode($token);
                self::set_token( $token);

            });
    }

    /**
     * regenerate new token base refresh token string
     * @param $refresh_token
     */
    public static function refresh_token($refresh_token) {
        $data = array(
            'refresh_token' => $refresh_token,
            'client_id' => self::client_id,
            'client_secret' => self::client_secret,
            'grant_type' => 'refresh_token'
        );
        $token = self::curl_post(self::refresh_token, array(
                //CURLOPT_HEADER => 1
            ), $data, false );
        self::set_token( $token);
    }

    /**
     * give string is valid url ?
     * @param $url
     * @return bool
     */
    public static function valid_url($url) {
        if (!filter_var($url, FILTER_VALIDATE_URL) === false) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * check a string is base64 format
     * @param $s
     * @return bool
     */
    public static function is_base64($s)
    {
        return (bool) preg_match('/^[a-zA-Z0-9\/\r\n+]*={0,2}$/', $s);
    }

    /**
     * detect image type
     * @param $image
     * @return string
     */
    public static function get_image_type($image) {
        if(self::valid_url($image)) $type = 'url';
        elseif(self::is_base64($image)) $type = 'base64';

        if(isset($type)) return $type;
    }
    /**
     * upload image to imgur by anonymous
     * @param $image
     * @param $param (optional)
     * @return mixed
     */
    public static function anonymous_upload_image($image, $param = array()) {
        $opts = array(
            CURLOPT_HTTPHEADER => array( 'Authorization: Client-ID ' . self::client_id )
        );
        //if(self::get_image_type($image))
            $type = self::get_image_type($image);

        $data = array(
            'image' => $image,
            'type' => $type
        );
        if(is_array($param)) $data = array_merge($data, $param);

        $reply = self::curl_post(self::upload_image, $opts, $data,false);
        $reply = json_decode($reply);
        return $reply->data->link;
    }

    /**
     * valid api error
     * @param $result
     */
    public static function check_api_error($result, $msg = array()) {
        if(isset($result->data->error)) {
            //refresh token
            self::get_token(true);  //force to refresh imgur token
            //valid
            if(!is_array($msg) ) {
                $msg = array('message' => is_string($msg)? $msg : '');
            }
            $msg['error'] = 1;
            $msg = array_merge((array)$result->data->error, $msg) ;
            return (object) $msg;
        }
    }

    /**
     * upload image to album
     * @param $image
     * @param $album album id
     * @param $param (optional)
     */
    public static function upload_image2album($image, $album = '', $param = array()) {
        $token = self::get_token();
        $opts = array(
            CURLOPT_HTTPHEADER => array( 'Authorization: Bearer ' . $token['access_token'] ),
        );
        $type = self::get_image_type($image);
        //get default album from setting
        if(!$album) $album = qa_opt('hw_imgur_album');

        $data = array(
            'image' => $image,
            'type' => $type,
            'album' => $album
        );
        if(is_array($param)) $data = array_merge($data, $param);

        $reply = self::curl_post(self::upload_image, $opts, $data,false);
        $reply = json_decode($reply);

        //return if api error
        $error = self::check_api_error($reply, array(
            'message' => 'Làm lại thao tác này để khởi động trình upload.',
            #'script' => 'window.location.reload()'
        ));
        if($error) return $error;

        return $reply;
    }

    /**
     * get images in album
     * @param $album album id
     */
    public static function get_images_album($album) {
        $token = self::get_token(); //GET access token
        $url = str_replace('{album}', $album, self::get_album_images);

        $opts = array(
            CURLOPT_HTTPHEADER => array( 'Authorization: Bearer ' . $token['access_token'] ),
        );
        $resp = self::curl_get($url, $opts);
        $resp = json_decode($resp);

        //return if api error
        $error = self::check_api_error($resp, 'Làm lại thao tác này để khởi động lại gallery.');
        if($error) return $error;

        return $resp->data;
    }
}
//test
#HW_IMGUR::init();
#HW_IMGUR::get_token();
//$r = HW_IMGUR::anonymous_upload_image('http://s0.2mdn.net/viewad/3130388/fotolia-GetCreative-300x250-us-fish.gif');
#HW_IMGUR::upload_image2album('http://s0.2mdn.net/viewad/3130388/fotolia-GetCreative-300x250-us-fish.gif', 'bld18');
#$t=HW_IMGUR::get_images_album('bld18');
#print_r($t);