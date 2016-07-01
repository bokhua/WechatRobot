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
				$newslist = '';	
				
				$cnt = 0;

				foreach ($response->list as $news) {
					$title = $news->article;
					$source = $news->source;
					$icon = $news->icon;
					$url = $news->url;
					if(empty($title))
						continue;
					$newslist .= '<item>
								<Title><![CDATA['.$title.']]></Title> 
								<Description><![CDATA['.$source.']]></Description>
								<PicUrl><![CDATA['.$icon.']]></PicUrl>
								<Url><![CDATA['.$url.']]></Url>
								</item>';
					$cnt++;
				}

				$content = '<xml>
							<ToUserName><![CDATA['.$fromUsername.']]></ToUserName>
							<FromUserName><![CDATA['.$toUsername.']]></FromUserName>
							<CreateTime>'.time().'</CreateTime>
							<MsgType><![CDATA[news]]></MsgType>
							<ArticleCount>'.$cnt.'</ArticleCount>
							<Articles>'.$newslist.'</Articles>
							</xml> ';
				echo $content;
				exit;
			}		
		}

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