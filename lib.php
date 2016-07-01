<?php
class WechatRequest
{
	function getRefreshToken(){
		
		global $CFG;

		$expired = false;

		if(!empty($CFG->token) && time() - $CFG->token_time <= $CFG->token_expire/2){
			return $CFG->token;
		}elseif(empty($CFG->token)){

			$rawtoken = file_get_contents(TOKEN_FILE);
			
			if($rawtoken != false){
				$token = json_decode($rawtoken);
				if(time() - $token->timestamp <= $token->expires_in/2){
					
					$CFG->token = $token->access_token;
					$CFG->token_expire = $token->expires_in;
					$CFG->token_time = $token->timestamp;

					return $token->access_token;
				}
			}
		}

		$url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.APP_ID.'&secret='.APP_SECRET;
		$response = Helper::GetUrl($url);

		$token = json_decode($response);
		if(!empty($token) && array_key_exists('access_token', $token)){
			$token->timestamp = time();
			$CFG->token = $token->access_token;
			$CFG->token_expire = $token->expires_in;
			$CFG->token_time = $token->timestamp;
			file_put_contents(TOKEN_FILE, json_encode($token));
			return $token->access_token;
		}
		return null;
	}

	function getServerIpList(){
		
		global $CFG;

		if(count($CFG->server_ip) > 0){
			return $CFG->server_ip;
		}else{
			$iplist = file_get_contents(IP_FILE);
			if($iplist != false && !empty($iplist)){
				$iplist = json_decode($iplist)->ip_list;
				if(!empty($iplist) && count($iplist) > 0){
					$CFG->server_ip = $iplist;
					return $iplist;
				}
			}
		}

		$url = 'https://api.weixin.qq.com/cgi-bin/getcallbackip?access_token='.$this->getRefreshToken();
		$response = Helper::GetUrl($url);

		$iplist = json_decode($response);
		if(!empty($iplist) && array_key_exists('ip_list', $iplist)){
			$CFG->server_ip = $iplist->ip_list;
			file_put_contents(IP_FILE, $response);
			return $iplist->ip_list;
		}
	}

	function validCallBack(){
		$ip = $_SERVER['REMOTE_ADDR'];
		$iplist = $this->getServerIpList();
		
		return in_array($ip, $iplist);
	}
}

class WechatCallback
{
	public function valid()
    {
        $echoStr = $_GET["echostr"];

        //valid signature , option
        if($this->checkSignature()){
        	echo $echoStr;
        	exit;
        }
    }

    public function responseMsg()
    {
		//get post data, May be due to the different environments
		$postStr = $GLOBALS["HTTP_RAW_POST_DATA"];

      	//extract post data
		if (!empty($postStr)){
                /* libxml_disable_entity_loader is to prevent XML eXternal Entity Injection,
                   the best way is to check the validity of xml by yourself */
                libxml_disable_entity_loader(true);
              	$postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
                $fromUsername = $postObj->FromUserName;
                $toUsername = $postObj->ToUserName;
                $keyword = trim($postObj->Content);
                $time = time();
                          
				if(!empty( $keyword ))
                {
                	$textTpl = "<xml>
								<ToUserName><![CDATA[%s]]></ToUserName>
								<FromUserName><![CDATA[%s]]></FromUserName>
								<CreateTime>%s</CreateTime>
								<MsgType><![CDATA[%s]]></MsgType>
								<Content><![CDATA[%s]]></Content>
								<FuncFlag>0</FuncFlag>
								</xml>";

                	if($keyword == '汇率' || strtolower($keyword) == 'currency'){
                		$contentStr = Helper::CheckCurrency();
                	}else{	              		
	                	$contentStr = "你好!";	         
                	}
                	$msgType = "text";
                	$resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
                	echo $resultStr;
                }else{
                	echo "Input something...";
                }

        }else {
        	echo "";
        	exit;
        }
    }
		
	private function checkSignature()
	{
        // you must define TOKEN by yourself
        if (!defined("TOKEN")) {
            throw new Exception('TOKEN is not defined!');
        }
        
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];
        		
		$token = TOKEN;
		$tmpArr = array($token, $timestamp, $nonce);
        // use SORT_STRING rule
		sort($tmpArr, SORT_STRING);
		$tmpStr = implode( $tmpArr );
		$tmpStr = sha1( $tmpStr );
		
		if( $tmpStr == $signature ){
			return true;
		}else{
			return false;
		}
	}
}

class Helper
{
	static function GetUrl($url){
		$call = curl_init();
		curl_setopt_array($call, array(
				CURLOPT_RETURNTRANSFER => 1,
				CURLOPT_URL => $url, 
				CURLOPT_SSL_VERIFYPEER => false,
				CURLOPT_SSL_VERIFYHOST => false
			));
		$response = curl_exec($call);
		curl_close($call);
		return $response;
	}
	static function CheckCurrency(){
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