<?php

class mod_translate
{
	protected $postObj;
	
	function __construct($PostObj){
		$this->postObj = $PostObj;
	}

	function reply(){
		
		require_once(dir(__FILE__).'/lib.php');
		
		$fromUsername = $this->postObj->FromUserName;
		$toUsername = $this->postObj->ToUserName;
		$keyword = trim($postObj->Content);

		$query = trim(get_request_param('translate', $keyword));

		$en = translate($query, 'auto', 'en');
		$zh = translate($query, 'auto', 'zh');

		if($en['trans_result'][0]['src'] != $en['trans_result'][0]['dst'])
			$content = $en['trans_result'][0]['dst'];
		if($zh['trans_result'][0]['src'] != $zh['trans_result'][0]['dst'])
			$content = $zh['trans_result'][0]['dst'];

    	$template = "<xml>
			<ToUserName><![CDATA[%s]]></ToUserName>
			<FromUserName><![CDATA[%s]]></FromUserName>
			<CreateTime>%s</CreateTime>
			<MsgType><![CDATA[%s]]></MsgType>
			<Content><![CDATA[%s]]></Content>
			<FuncFlag>0</FuncFlag>
			</xml>";
		$msgType = 'text';

		$out = sprintf($template, $fromUsername, $toUsername, time(), $msgType, $content);

		echo $out; 
	}
}