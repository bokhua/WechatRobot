<?php
/**
  * wechat php test
  */
require_once('config.php');
require_once('lib.php');
//define your token

$mode = 'listen';
$wechatObj = new WechatCallback();
if($mode == 'auth'){
    $wechatObj->valid();
}elseif($mode == 'listen'){
	$wechatObj->responseMsg();
}