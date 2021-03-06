<?php
require_once($CFG->dirroot.'/mod/translate/lib.php');
class mod_translate
{
	protected $postObj;
	
	function __construct($PostObj){
		$this->postObj = $PostObj;
	}

	function reply(){
		$fromUsername = $this->postObj->FromUserName;
		$toUsername = $this->postObj->ToUserName;
		$keyword = trim($this->postObj->Content);

		$query = trim(get_request_param('translate', $keyword));

		$en = translate($query, 'auto', 'en');
		$zh = translate($query, 'auto', 'zh');

		$content = 'not result matched for '.$query;

		if($en['trans_result'][0]['src'] != $en['trans_result'][0]['dst'])
			$content = $en['trans_result'][0]['dst'];
		if($zh['trans_result'][0]['src'] != $zh['trans_result'][0]['dst'])
			$content = $zh['trans_result'][0]['dst'];

		echo WechatReponse::renderText($fromUsername, $toUsername, $content);
	}
}