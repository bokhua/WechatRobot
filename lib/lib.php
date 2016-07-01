<?php

function url_request($url, $method = 'GET', $data = null){
	
	$response = null;

	if($method == 'GET'){
		if($data == null){
			$call = curl_init();
			curl_setopt_array($call, array(
					CURLOPT_RETURNTRANSFER => 1,
					CURLOPT_URL => $url, 
					CURLOPT_SSL_VERIFYPEER => false,
					CURLOPT_SSL_VERIFYHOST => false
				));
			$response = curl_exec($call);
			curl_close($call);		
		}
	}
	
	return $response;
}