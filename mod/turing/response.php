<?php

class mod_turing{

	protected $postObj;

	function __construct($PostObj){
		$this->postObj = $PostObj;
	}

	function reply(){
		$fromUsername = $this->postObj->FromUserName;
		$toUsername = $this->postObj->ToUserName;
		$keyword = trim($this->postObj->Content);

		$url = 'http://www.tuling123.com/openapi/api';

		$appkey = 'db57fd447f1e949f061fdadba6a1ad4d';

		$response = url_request($url, 'POST', array('key' => $appkey, 'info' => $keyword,'userid' => $fromUsername));

		$content = null;

		if(!empty($response)){
			$response = json_decode($response);


			if($response->code == 302000){

				$newslist = array();
				foreach ($response->list as $news) {
					$title = $news->article;
					$desc = $news->source;
					$icon = $news->icon;
					$url = $news->detailurl;
					$newslist[] = new WechatReponseNews($title, $desc, $icon, $url);
				}

				$content = WechatReponse::renderNews($fromUsername, $toUsername, $newslist);
			}

			if($response->code == 100000){
				$content = WechatReponse::renderText($fromUsername, $toUsername, $response->text);
			}

			if($response->code == 200000){
				$content = WechatReponse::renderLink($fromUsername, $toUsername, $response->text, 'link', $response->url);
				$content = WechatReponse::renderText($fromUsername, $toUsername, $response->text.PHP_EOL.$response->url);
			}

			if($response->code == 308000){
				
				$newslist = array();

				foreach ($response->list as $value) {
					$newslist[] = new WechatReponseNews($value->name, $value->info, $vaue->icon, $value->detailurl);
				}
				$content = $response->text.PHP_EOL;
				$content .= WechatReponse::renderNews($fromUsername, $toUsername, $newslist);
				$content = $response->text.PHP_EOL.WechatReponse::renderText($fromUsername, $toUsername, $value->name.PHP_EOL.$value->info.PHP_EOL.$value->detailurl);
			}
		}
		echo $content;
	}
}