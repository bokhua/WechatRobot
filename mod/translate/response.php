<?php

class mod_translate
{
	protected $postObj;
	
	function __construct($PostObj){
		$this->postObj = $PostObj;
	}

	function reply(){
		
		$fromUsername = $this->postObj->FromUserName;
		$toUsername = $this->postObj->ToUserName;
		$keyword = trim($postObj->Content);
		


		$content = '';

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