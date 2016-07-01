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

function get_request_mod($keyword){
	
	global $CFG;

	$mods = $CFG->settings->mods;
	
	foreach ($mods as $mod) {
		$keywords = $mod->keywords;
		foreach ($keywords as $key) {
			if(substr( strtolower($keyword), 0, strlen($key) ) == $key)
				return $mod->name;
		}
	}

	return null;
}

function get_request_param($modname, $keyword){
	
	global $CFG;

	$mods = $CFG->settings->mods;
	
	foreach ($mods as $mod) {
		if($mod->name == $modname){

			$keywords = $mod->keywords;

			foreach ($keywords as $key) {
				if(substr( strtolower($keyword), 0, strlen($key) ) == $key)
					return substr($keyword, strlen($key));
			}
		}
	}

	return null;
}