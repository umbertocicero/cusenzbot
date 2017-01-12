<?php echo "test";

echo getMeteo();

function getMeteo() {
	$BASE_URL = "http://query.yahooapis.com/v1/public/yql";
    $yql_query = 'select item.condition from weather.forecast where woeid=714748 and u = "c"';
    $yql_query_url = $BASE_URL . "?q=" . urlencode($yql_query) . "&format=json";
    // Make call with cURL
    $session = curl_init($yql_query_url);
    curl_setopt($session, CURLOPT_RETURNTRANSFER,true);
    $json = curl_exec($session);
    // Convert JSON to PHP object
    $phpObj =  json_decode($json);
   // return $phpObj;
	return $json;
}