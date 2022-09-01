<?php
/**
 * 葫芦侠新版聚合API(带sign)计算
 *
 * 封装葫芦侠新版本API
 *
 * API更新、失效请提交issue
 *
 * 返回格式：JSON
 *
 * @author：迷路的小孩
 *
 * 此API仅供学习交流，请下载后于24H内删除
**/
header('Content-Type: text/html;charset=utf-8');
include('api.class.php');
/**
* 引入类
* @param {Object} $_key 用户_key , $userid 用户ID , $auto_sign 是否自动签到(1:是 0:否)
*
**/
$_key="";
$userid="";
$auto_sign=0;
$api= new Huluxia_Api($_key,$userid,$auto_sign);
//获取操作
$action=$_REQUEST['action'];

if($action=='create_comment'){
    $post_id=$_REQUEST['post_id'];
    $text=$_REQUEST['text'];
	$res=$api->create_comment($post_id,$text);
	ajaxReturn($res);
}
if($action=='create_post'){
    $title=$_REQUEST['title'];
    $detail=$_REQUEST['detail'];
    $images=$_REQUEST['images'];
    $cat_id=$_REQUEST['cat_id'];
    $tag_id=$_REQUEST['tag_id'];
	$res=$api->create_post($title,$detail,$images,$cat_id,$tag_id);
	ajaxReturn($res);
}

function ajaxReturn($array){
    $content=json_encode($array,JSON_UNESCAPED_UNICODE);
    echo $content;
	exit;
}