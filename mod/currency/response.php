<?php

class mod_currency
{
	protected $postObj;
	
	function __construct($PostObj){
		$this->postObj = $PostObj;
	}

	function reply(){
		
		$fromUsername = $this->postObj->FromUserName;
		$toUsername = $this->postObj->ToUserName;
		$keyword = trim($postObj->Content);

		$pairs = array('SGDCNY', 'CNYSGD', 'SGDJPY', 'JPYSGD');

		$content = $this->checkCurrency($pairs);

		echo WechatReponse::renderText($fromUsername, $toUsername, $content);
	}

	private function checkCurrency($pairs = array()){
		
		$result = '';

		if(count($pairs) == 0){
			return 'invalid request.';
		}
		foreach ($pairs as $pair) {
			$pair = '"'.$pair.'"';
		}
		$keyword = urlencode(implode(',', $pair));
		
		$url = 'https://query.yahooapis.com/v1/public/yql?q=select%20*%20from%20yahoo.finance.xchange%20where%20pair%20in%20('.$keyword.')&format=json&diagnostics=true&env=store%3A%2F%2Fdatatables.org%2Falltableswithkeys&callback=';
		
		$response = url_request($url);

		if(empty($response)){
			$result = '服务不可用';
		}else{
			$rates = json_decode($response)->query->results->rate;
			foreach ($rates as $rate) {
				$result .= $rate->Name.': '.$rate->Rate.PHP_EOL.'Ask: '.$rate->Ask.' Bid: '.$rate->Bid.PHP_EOL.PHP_EOL;
			}
		}
		date_default_timezone_set('Asia/Singapore');
 		$date = date('Y-M-d H:i:s');
 		$result .= $date;
		return $result;
	}
}