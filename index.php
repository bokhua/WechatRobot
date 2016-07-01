<?php
/**
  * wechat php test
  */
require_once('lib.php');
//define your token
define("TOKEN", "weixin");

define('TOKEN_FILE', 'C:\public\http\wechat\refresh_token.txt');

define('IP_FILE', 'C:\public\http\wechat\server_ip.txt');

define('APP_ID', 'wxc55dd5bdfa4ae1f1');
define('APP_SECRET', 'd7cdc4746b7a9dbb172ba913b305a4c7');

global $CFG;
$CFG = new stdClass();

$CFG->token = '';
$CFG->token_expire = 3600;
$CFG->token_time = 0;
$CFG->server_ip = array();

$mode = 'listen';
$wechatObj = new WechatCallback();
if($mode == 'auth'){
    $wechatObj->valid();
}elseif($mode == 'listen'){
	$wechatObj->responseMsg();
}