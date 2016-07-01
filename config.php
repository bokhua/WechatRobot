<?php

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

$CFG->dirroot = 'C:/public/http/wechat';
$CFG->wwwroot = 'http://54.148.245.231/wechat';

$CFG->dbtype = 'mysql';
$CFG->dbhost = '127.0.0.1';
$CFG->dbport = '1433';
$CFG->dbuser = '';
$CFG->dbpass = '';

require_once(dirname(__FILE__) . '/lib/setup.php');