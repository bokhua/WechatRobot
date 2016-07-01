<?php

class mod_currency
{
	function __construct(){

	}

	function display(){
		
	}
	
	private function CheckCurrency(){
		$result = '';
		$pair = array('"CNYSGD"', '"SGDCNY"');
		$keyword = urlencode(implode(',', $pair));
		$url = 'https://query.yahooapis.com/v1/public/yql?q=select%20*%20from%20yahoo.finance.xchange%20where%20pair%20in%20('.$keyword.')&format=json&diagnostics=true&env=store%3A%2F%2Fdatatables.org%2Falltableswithkeys&callback=';
		$response = Helper::GetUrl($url);
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