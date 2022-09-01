<?php
/**
 * 葫芦侠新版聚合API(带sign)计算
 *
 * 封装葫芦侠新版本API
 *
 * API更新、失效请提交issue
 *
 * @author：迷路的小孩
 *
 * 此API仅供学习交流，请下载后于24H内删除
**/
class Huluxia_Api {
    private $_key;
    private $userid;
    private $auto_sign;
    public function __construct($_key,$userid,$auto_sign){
        if($this->islogin($_key,$userid)){
            $this->key = $_key;
            $this->userid = $userid;
            $check_sign_url="http://floor.huluxia.com/user/signin/check/ANDROID/2.0?user_id=".$userid."&cat_id=43&platform=2&gkey=000000&app_version=4.1.0.9&versioncode=20141462&market_id=floor_web&_key=".$_key."&device_code=&phone_brand_type=MI";
            $check_sign_info = json_decode($this->get_link($check_sign_url),true);
            $signin=$check_sign_info["signin"];
            if($auto_sign==1 && $signin==0){
                $category = json_decode($this->get_link("http://floor.huluxia.com/category/list/ANDROID/2.0?platform=2&gkey=000000&app_version=4.1.0.9&versioncode=20141462&market_id=floor_web&_key=&device_code=&phone_brand_type=MI&is_hidden=0"),true);
                $list_data=$category["categories"];
                foreach($list_data as $list){
                    $categoryID = $list["categoryID"];
                    $msectime=$this->msectime();
                        if($categoryID!="0"){
                            $sign_url = "http://floor.huluxia.com/user/signin/ANDROID/4.1.8?platform=2&gkey=000000&app_version=4.2.0.1&versioncode=20141465&market_id=floor_web&_key=".$_key."&device_code=&phone_brand_type=MI&cat_id=".$categoryID."&time=".$msectime;
                            $sign_array = array();
                            $sign_array["cat_id"] = $categoryID;
                            $sign_array["time"] = $msectime;
                            $sign = $this->ToSign($sign_array);
                            $this->send_post($sign_url,array("sign" => $sign));
                        }
                }
                //完成自动签到
            }
        }else{
            return array('code'=>500,'msg'=>'未登录');
            exit;
        }
    }
    /**
     * 图片上传
     * @param {Object} $_key 用户_key (未完成)
     *
    **/
    public function upload_image(){
        $msectime=$this->msectime();
        $url="";
    }
     /**
     * 发布帖子
     * @param {Object} $title 标题, $detail 内容,$images 图片,$cat_id 版块ID,$tag_id 版块分区ID
     *
    **/
    public function create_post($title,$detail,$images,$cat_id,$tag_id){
        if(empty($title) || empty($detail)){
            return array('code'=>500,'msg'=>'必要参数不能为空');
        }else{
            $create_post_url="http://floor.huluxia.com/post/create/ANDROID/4.1.8?platform=2&gkey=000000&app_version=4.2.0.1&versioncode=20141465&market_id=floor_web&_key=".$this->key."&device_code=&phone_brand_type=MI";
            $sign_array = array();
            $sign_array["_key"] = $this->key;
            $sign_array["detail"] = $detail;
            $sign_array["device_code"] = "";
            $sign_array["images"] = $images;
            $sign_array["title"] = $title;
            $sign_array["voice"] = "";
            $sign = $this->ToSign($sign_array);
            $post_data = array(
                'cat_id' => $cat_id,
                'tag_id' => $tag_id,
                'type' => '0',
                'title' => $title,
                'detail' => $detail,
                'patcha' => '',
                'voice' => '',
                'lng' => '0.0',
                'lat' => '0.0',
                'images' => '',
                'user_ids' => '',
                'recommendTopics' => '',
                'sign' => $sign,
                'is_app_link' =>'4'
            );
            $info = json_decode($this->send_post($create_post_url,$post_data),true);
		    $msg=$info["msg"];
		    return array('code'=>200,'msg'=>$msg,'postID'=>$info["postID"]);
        }
    }
     /**
     * 发布评论
     * @param {Object} $post_id 帖子ID,$text 评论内容
     *
    **/
    public function create_comment($post_id,$text){
        if(empty($post_id) || empty($text)){
            return array('code'=>500,'msg'=>'必要参数不能为空');
        }else{
            $create_comment_url="http://floor.huluxia.com/comment/create/ANDROID/4.1.8?platform=2&gkey=000000&app_version=4.2.0.1&versioncode=20141465&market_id=floor_web&_key=".$this->key."&device_code=&phone_brand_type=MI";
            $sign_array = array();
            $sign_array["_key"] = $this->key;
            $sign_array["comment_id"] = 0;
            $sign_array["device_code"] = "";
            $sign_array["images"] = "";
            $sign_array["post_id"] = $post_id;
            $sign_array["text"] = $text;
            $sign = $this->ToSign($sign_array);
            $post_data = array(
                'post_id' => $post_id,
                'comment_id' => 0,
                'text' => $text,
                'patcha' => "",
                'images' => "",
                'remindUsers' => "",
                'sign' => $sign
            );
            $info = json_decode($this->send_post($create_comment_url,$post_data),true);
		    $msg=$info["msg"];
		    return array('code'=>200,'msg'=>$msg);
        }
    }
    /**
     * 是否登录(自动)
     * @param {Object} $_key 用户_key
     *
    **/
    public function islogin($_key,$userid){
        $url = "http://floor.huluxia.com/user/info/ANDROID/2.1?platform=2&gkey=000000&app_version=4.1.0.9&versioncode=20141462&market_id=floor_web&_key=".$_key."&device_code=&phone_brand_type=MI&user_id=".$userid;
		$info = json_decode($this->get_link($url),true);
		$islogin=$info["msg"];
		if($islogin=="未登录"){
		    return false;
		}else{
		    return true;
		}
    }
    
    
    
    
    
    
    
    public function get_link($url){
	    $ch = curl_init();
	    curl_setopt($ch, CURLOPT_URL,$url);
	    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
	    $httpheader[] = "Accept: */*";
	    $httpheader[] = "Accept-Encoding: gzip";
	    $httpheader[] = "Connection: close";
	    curl_setopt($ch, CURLOPT_TIMEOUT, 45);
    	curl_setopt($ch, CURLOPT_HTTPHEADER, $httpheader);
	    curl_setopt($ch, CURLOPT_USERAGENT,'okhttp/3.8.1');
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
	    curl_setopt($ch, CURLOPT_ENCODING, "gzip");
	    $return = curl_exec($ch);
	    curl_close($ch);
    	return $return;
    }
    public function send_post($url, $post_data) {
        ini_set('user_agent', 'okhttp/3.8.1');
        $postdata = http_build_query($post_data);
      $options = array(
        'http' => array(
        'method' => 'POST',
        'header' => 'Content-type:application/x-www-form-urlencoded',
        'content' => $postdata,
        'timeout' => 15 * 60 // 超时时间（单位:s）
        )
    );
    $context = stream_context_create($options);
    $result = file_get_contents($url, false, $context);
    return $result;
    }
    public function msectime() {
        list($msec, $sec) = explode(' ', microtime());
        $msectime = (float)sprintf('%.0f', (floatval($msec) + floatval($sec)) * 1000);
        return intval($msectime);
    }
    public function ToSign($args) {
        $sign="";
        foreach($args as $key=>$val){
          $sign=$sign.$key.$val;
        }
        $sign=strtoupper(md5($sign."fa1c28a5b62e79c3e63d9030b6142e4b"));
        return $sign;
    }
}