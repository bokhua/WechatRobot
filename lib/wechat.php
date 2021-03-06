<?php

class WechatReponse{

	static function renderText($touser, $fromuser, $content = null){
		return '<xml>
				<ToUserName><![CDATA['.$touser.']]></ToUserName>
				<FromUserName><![CDATA['.$fromuser.']]></FromUserName> 
				<CreateTime>'.time().'</CreateTime>
				<MsgType><![CDATA[text]]></MsgType>
				<Content><![CDATA['.$content.']]></Content>
				</xml>';//<MsgId>1234567890123456</MsgId>
	}

	static function renderLink($touser, $fromuser, $title, $desc, $url){
		
		return '<xml>
				<ToUserName><![CDATA['.$touser.']]></ToUserName>
				<FromUserName><![CDATA['.$fromuser.']]></FromUserName> 
				<CreateTime>'.time().'</CreateTime>
				<MsgType><![CDATA[link]]></MsgType>
				<Title><![CDATA['.$title.']]></Title>
				<Description><![CDATA['.$desc.']]></Description>
				<Url><![CDATA['.$url.']]></Url>
				</xml>';//<MsgId>1234567890123456</MsgId>
	}

	static function renderNews($touser, $fromuser, $newslist = array()){

		$articles = '';
		$cnt = 0;
		foreach ($newslist as $news) {
			if(!empty($news->title) && !empty($news->url)){
				$articles .=   '<item>
								<Title><![CDATA['.$news->title.']]></Title> 
								<Description><![CDATA['.$news->desc.']]></Description>
								<PicUrl><![CDATA['.$news->icon.']]></PicUrl>
								<Url><![CDATA['.$news->url.']]></U->rl>
								</item>';
				$cnt++;
			}
		}
		return '<xml>
				<ToUserName><![CDATA['.$touser.']]></ToUserName>
				<FromUserName><![CDATA['.$fromuser.']]></FromUserName>
				<CreateTime>'.time().'</CreateTime>
				<MsgType><![CDATA[news]]></MsgType>
				<ArticleCount>'.$cnt.'</ArticleCount>
				<Articles>'.$articles.'</Articles>
				</xml>';//<MsgId>1234567890123456</MsgId>
	}
}

class WechatReponseNews{
    public $title, $desc, $icon, $url;
    function __construct($Title, $Desc, $Icon, $Url){
        $this->title = $Title;
        $this->desc = $Desc;
        $this->icon = $Icon;
        $this->url = $Url;
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
    	global $CFG;
		//get post data, May be due to the different environments
		$postStr = array_key_exists('HTTP_RAW_POST_DATA', $GLOBALS) ? $GLOBALS['HTTP_RAW_POST_DATA'] : null;

      	//extract post data
		if (!empty($postStr)){
            /* libxml_disable_entity_loader is to prevent XML eXternal Entity Injection,
               the best way is to check the validity of xml by yourself */
            libxml_disable_entity_loader(true);
          	$postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
            $keyword = trim($postObj->Content);

			if(!empty( $keyword ))
            {
            	$modname = get_request_mod($keyword);
            	$modname = empty($modname) ? 'turing' : $modname;
            	$modfile = $CFG->dirroot.'/mod/'.$modname.'/response.php';
            	$modclassname = 'mod_'.$modname;

            	if(!empty($modname) && file_exists($modfile)){
            		require_once($modfile);
            		$mod = new $modclassname($postObj);
            		$mod->reply();
            		exit;
            	}
            	
            }
        }

        $this->defaultReply($postObj);
        exit;
    }

    private function defaultReply($postObj){

    	$fromUsername = $postObj->FromUserName;
		$toUsername = $postObj->ToUserName;
		$keyword = trim($postObj->Content);
    	$template = "<xml>
			<ToUserName><![CDATA[%s]]></ToUserName>
			<FromUserName><![CDATA[%s]]></FromUserName>
			<CreateTime>%s</CreateTime>
			<MsgType><![CDATA[%s]]></MsgType>
			<Content><![CDATA[%s]]></Content>
			<FuncFlag>0</FuncFlag>
			</xml>";

		$msgType = 'text';

		$content = '你好！';

		$out = sprintf($template, $fromUsername, $toUsername, time(), $msgType, $content);

		echo $out; 
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