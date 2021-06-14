<?php
error_reporting(0);

$email = "INI BUAT NARO EMAIL SOB";
$password = "PASSWORDNYA DISINI YA SOB";
$headers = [
            'Authority: api.coinmarketcap.com',
            'Sec-Ch-Ua: \" Not;A Brand\";v=\"99\", \"Google Chrome\";v=\"91\", \"Chromium\";v=\"91\"',
            'Accept: application/json, text/plain, */*',
            'Sec-Ch-Ua-Mobile: ?0',
            'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.101 Safari/537.36',
            'Content-Type: application/json;charset=UTF-8',
            'Origin: https://coinmarketcap.com',
            'Sec-Fetch-Site: same-site',
            'Sec-Fetch-Mode: cors',
            'Sec-Fetch-Dest: empty',
            'Referer: https://coinmarketcap.com/',
            'Accept-Language: en-US,en;q=0.9',
			];

print("[!] Checking session... \r\n");
$token = trim(file_get_contents('cmc_session.txt'));
upHere:
if (file_exists('cmc_session.txt')):
    print("__[>] Trying to signin with `".substr($token, 7, 16)."********`\r\n");
    if(filesize('cmc_session.txt')):
        goto loginToken;
    elseif(!filesize('cmc_session.txt')):
        @unlink('cmc_session.txt');
        print("__[?] `Token` is empty\r\n\n");
        sleep(2);
        goto upHere;
    endif;
elseif (!file_exists('cmc_session.txt')):
    print("[?] `Token` file does not exist\r\n");
    print("__[>] Trying to signin with `****".substr($email, 4)."`\r\n");
endif;

$page = curl("https://coinmarketcap.com/airdrop/participated/", false, $headers, "cmc.cook");
loginUser:
$login = curl("https://api.coinmarketcap.com/auth/v4/user/login", '{"email":"'.$email.'","password":"'.$password.'","platform":"web"}', $headers, "cmc.cook");
$Dlogin = json_decode($login, true);
if($Dlogin['status']['error_code']==10011):
    exit("[!] ".$Dlogin['status']['error_message'].", check your details again sir");
elseif($Dlogin['status']['error_code']==0):
    saveFile('cmc_session.txt',$Dlogin['data']['token']);
    print("[!] ".$Dlogin['status']['error_message'].", `Token` saved in 'cmc_session.txt'\r\n");
endif;

/* refreshing */
$token = trim(file_get_contents('cmc_session.txt'));
/* endof */

loginToken:
echo PHP_EOL;
$data = data($token);
$Ddata = json_decode($data, true);
if($Ddata['status']['error_code']==100000):
    @unlink('cmc_session.txt');
    print("[#] Session ended, trying to get new `Token`\r\n");
    goto loginUser;
elseif($Ddata['status']['error_code']==100002):
    exit("[!] ".$Ddata['status']['error_message']."\r\n");
elseif($Ddata['status']['error_code']==0):
    $cnt = 0;
    foreach($Ddata['data']['projects'] as $key=>$value){
        $cnts = $cnt+1;
        $projectName = $value['projectName'];
        $awardingStatus = $value['awardingStatus'];
        if($awardingStatus=="WIN"):
            $status = coloredStr("Congratulations","green");
        elseif($awardingStatus=="NO_WIN"):
            $status = coloredStr("You didn`t win this time","red");
        elseif($awardingStatus=="WAITING"):
            $status = coloredStr("Awaiting results!","yellow");
        endif;
        print("[$cnts] $projectName - $status\r\n");
        $cnt++;
    }
endif;

/* Function * -/- * Don't you try to change everything below here */

function getStr($string, $start, $end){
	$str = explode($start, $string);
	$str = explode($end, $str[1]);
	return $str[0];
}

function saveFile($filename,$string)
{
	$handle = fopen($filename, 'a');
	fwrite($handle, $string);
	fclose($handle);
	return 1;
}

function coloredStr($string,$color){
    $array = array(
		"green" => "1;32",
		"red" => "1;31",
		"yellow" => "1;33",
		"purple" => "1;35"
  	);
    $text = "\033[".$array[$color]."m".$string."\033[0m";
	return $text;
}

function data($bearer){
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, 'https://api.coinmarketcap.com/data-api/v3/airdrop/participated/history');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate');

    $headers = array();
    $headers[] = 'Authority: api.coinmarketcap.com';
    $headers[] = 'Content-Length: 0';
    $headers[] = 'Sec-Ch-Ua: \" Not;A Brand\";v=\"99\", \"Google Chrome\";v=\"91\", \"Chromium\";v=\"91\"';
    $headers[] = 'Accept: application/json, text/plain, */*';
    $headers[] = 'Authorization: '.$bearer;
    $headers[] = 'Sec-Ch-Ua-Mobile: ?0';
    $headers[] = 'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.101 Safari/537.36';
    $headers[] = 'Origin: https://coinmarketcap.com';
    $headers[] = 'Sec-Fetch-Site: same-site';
    $headers[] = 'Sec-Fetch-Mode: cors';
    $headers[] = 'Sec-Fetch-Dest: empty';
    $headers[] = 'Referer: https://coinmarketcap.com/';
    $headers[] = 'Accept-Language: en-US,en;q=0.9,id;q=0.8';
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $result = curl_exec($ch);
    curl_close($ch);
	return $result;
}

function curl($url, $body = null, $headers = null, $cookies = null, $isHttpHeader = false){
	
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	if ($body && !empty($body)) {
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
    }else{
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
	}
	
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	if ($isHttpHeader==1 OR $isHttpHeader==true)
	{
		curl_setopt($ch, CURLOPT_HEADER, 1);
	}
	//curl_setopt($ch, CURLOPT_VERBOSE, 1);
    //curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);

	if ($headers && !empty($headers)) {
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    }
	
	if ($cookies && !empty($cookies)) {
		curl_setopt($ch, CURLOPT_COOKIEJAR, $cookies);
		curl_setopt($ch, CURLOPT_COOKIEFILE, $cookies);
	}
	
	$result = curl_exec($ch);
	//if (curl_errno($ch)) echo 'Error:' . curl_error($ch);
	curl_close($ch);
	return $result;
}