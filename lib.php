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