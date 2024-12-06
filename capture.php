<?
//CAPTURA O TRENDING TOPICS


//Captura url digitada
$url_raw = isset($_GET['url']) ? $gc5->anti_injection($_GET['url']) : null;
if (substr($url_raw,0,1) == "/") {
    $url_raw = substr($url_raw, 1);
}
$url = explode("/",$url_raw);


//URL de consulta (end point)
$twitter_url = "https://api.twitter.com/1.1/trends/place.json";

//Local
if (isset($url[1]) and $url[1] == "brazil") {
    //Brazil
    $woeid='23424768';
} else {
    //World
    $woeid='1';
}

//Parâmetros de autenticação (dados sensíveis omitidos)
$oauth_access_token = "xxxxx";
$oauth_access_token_secret ="xxxxx";
$consumer_key = "xxxxx";
$consumer_secret = "xxxxx";
$time = time();

//Procedimento de autenticação
$oauth = array('oauth_consumer_key' => $consumer_key, 
               'oauth_nonce' => $time,
               'id' => $woeid, 
               'oauth_signature_method' => 'HMAC-SHA1', 
               'oauth_nonce' => $time,
               'oauth_token' => $oauth_access_token, 
               'oauth_timestamp' => $time, 
               'oauth_version' => '1.0');

//Parâmetro da consulta
function buildBaseString($baseURI,$method,$params) {

    $r = array();
    ksort($params);

    foreach($params as $key=>$value) {
        $r[] = "$key=" . rawurlencode($value);
    }

    return $method."&".rawurlencode($baseURI).'&'.rawurlencode(implode('&',$r));

}

function buildAuthorizationHeader($oauth) {

    $r = 'Authorization: OAuth ';
    $values = array();

    foreach($oauth as $key => $value) {
        $values[] = "$key=\"".rawurlencode($value)."\"";
    }

    $r .= implode(', ', $values);
    return $r;

}

//Procedimento de consulta
$base_info = buildBaseString($twitter_url,'GET',$oauth);
$composite_key = rawurlencode($consumer_secret).'&' . rawurlencode($oauth_access_token_secret);
$oauth_signature = base64_encode(hash_hmac('sha1',$base_info,$composite_key,true));
$oauth['oauth_signature'] = $oauth_signature;

$header = array(buildAuthorizationHeader($oauth),'Expect:');
$options = array(CURLOPT_HTTPHEADER => $header,
                 CURLOPT_HEADER => false,
                 CURLOPT_URL => $twitter_url."?id=$woeid",
                 CURLOPT_RETURNTRANSFER => true,
                 CURLOPT_SSL_VERIFYPEER => false);

$feed = curl_init();
curl_setopt_array($feed, $options);
$json = curl_exec($feed);
curl_close($feed);
$trends = json_decode($json,true);\

//Retorno dos resultados em formato JSON
if (isset($trends[0]['trends'])) {

    $arr_trends = ["time" => $time,  
                "location" => $trends[0]['locations'][0]['name'],
                "trends" => []
                ];

    for($i=0; $i < count($trends[0]['trends']); $i++){

        $arr_trends["trends"][] = [
            "position" => $i + 1,
            "trending" => $trends[0]['trends'][$i]['name'],
            "volume" => $trends[0]['trends'][$i]['tweet_volume'],
            "url" => $trends[0]['trends'][$i]['url']
        ];

    }

    $json_trends_normalized = json_encode($arr_trends);
    echo $json_trends_normalized;

} else {
    echo $trends['errors'][0]["message"];
}