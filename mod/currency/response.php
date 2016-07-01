<?php

class mod_currency
{
	protected $postObj;
	
	function __construct($PostObj){
		$this->postObj = $PostObj;
	}

	function reply(){
		
		$fromUsername = $this->postObj->FromUserName;
		$toUsername = $this->$postObj->ToUserName;

		$pair = array('"CNYSGD"', '"SGDCNY"');

		$content = $this->checkCurrency($pair);

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

	private function checkCurrency($pair = array()){
		
		$result = '';

		if(count($pair) == 0){
			return 'invalid request.';
		}

		$keyword = urlencode(implode(',', $pair));
		
		$url = 'https://query.yahooapis.com/v1/public/yql?q=select%20*%20from%20yahoo.finance.xchange%20where%20pair%20in%20('.$keyword.')&format=json&diagnostics=true&env=store%3A%2F%2Fdatatables.org%2Falltableswithkeys&callback=';
		
		$response = url_request($url);

		if(empty($response)){
			$result = '服务不可用';
		}else{
			$rates = json_decode($response)->query->results->rate;
			foreach ($rates as $rate) {
				$result .= $rate->Name.': '.$rate->Rate.PHP_EOL;
			}
		}
		date_default_timezone_set('Asia/Singapore');
 		$date = date('Y-M-d H:i:s');
 		$result .= $date;
		return $result;
	}
}