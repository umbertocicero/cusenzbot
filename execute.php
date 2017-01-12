<?php
define("BOT_TOKEN", "326665840:AAGd8Y7ReODVEtKZ8DffNkwv0CvuWxLIcmE");
$content = file_get_contents("php://input");
$update = json_decode($content, true);

if(!$update)
{
  exit;
}

$message = isset($update['message']) ? $update['message'] : "";
$messageId = isset($message['message_id']) ? $message['message_id'] : "";
$chatId = isset($message['chat']['id']) ? $message['chat']['id'] : "";
$firstname = isset($message['chat']['first_name']) ? $message['chat']['first_name'] : "";
$lastname = isset($message['chat']['last_name']) ? $message['chat']['last_name'] : "";
$username = isset($message['chat']['username']) ? $message['chat']['username'] : "";
$date = isset($message['date']) ? $message['date'] : "";
$text = isset($message['text']) ? $message['text'] : "";

$resultText = trim($text);
$textSplit = explode(" ", $resultText);

$firstText = $textSplit[0];
$secondText = "";

if(isset($textSplit[1])){
	for ($i=1; $i < count($textSplit); $i++)
	{
	  $tempText = trim($textSplit[$i]);
	  if($tempText!=""){
		  $secondText = $tempText;
		  break;
	  }
	}
}

$firstText = strtolower($firstText);
$firstText = trim($firstText);

switch ($firstText) {
    case "/start":
        $resultText = "Ciao sono CusenzBot!\n\n";
		$resultText .= "Questo bot é perfetto per tutti i cosentini (e non solo)\n\n";
		$resultText .= "lista comandi per ogni categoria:\n";
		$resultText .= "/testo\n";
		$resultText .= "/foto\n";
		sendMsg($chatId,$resultText);
		exit;
	case "/testo":
		$resultText  = "1. Ciao\n";
		$resultText .= "2. Insulta {nome persona} - Es: Insulta Mario\n";
		$resultText .= "3. Minaccia {nome persona} - Es: Minaccia Mario\n";
		$resultText .= "4. Proverbio | Nonno\n";
		$resultText .= "5. Poesia\n";
		sendMsg($chatId,$resultText);
        break;
	case "/foto":
		$resultText = "1. Foto [al momento non disponibile]\n";
		sendMsg($chatId,$resultText);
        break;
	case "meteo":
		$resultText = getWeather();
		sendMsg($chatId,$resultText);
        break;
		/*
    case "/foto":
		sendPhoto($chatId,$text);
        break;
		*/
	default:
		//sendMsg($chatId,$update);
       //sendMsg($chatId,$resultText );
}

$json_a = json_decode(file_get_contents(realpath("response.json")), true);


$found = false;
foreach ($json_a as $k => $v) {	
	if($found) break;
		
	$keySplit = explode("/", $k);	
	foreach ($keySplit as $value) {	
		if($value == $firstText){
			
			if(is_array($v)) {
			   $random = rand(0, count($v)-1);
			   $t = $v[$random]; 
			} else {
			   $t = $v;
			}
			
			$resultText = $t;
			$resultText = str_replace("%s", $secondText, $t);
			sendMsg($chatId, $resultText);
			sendMsg($chatId, $resultText);
			$found = true;
			break;
		}
	}
	
}


function sendMsg($c,$t) {
	header("Content-Type: application/json");
	$parameters = array('chat_id' => $c, "text" => $t);
	$parameters["method"] = "sendMessage";
	echo json_encode($parameters);
}
function sendPhoto($c,$t) {
	$botUrl = "https://api.telegram.org/bot" . BOT_TOKEN . "/sendPhoto";
    // change image name and path
	$postFields = array('chat_id' => $c, 'photo' => new CURLFile(realpath("images/image.png")), 'caption' => $text);
	$ch = curl_init(); 
	curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type:multipart/form-data"));
	curl_setopt($ch, CURLOPT_URL, $botUrl); 
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
	curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
	// read curl response
	$output = curl_exec($ch);
}



function getWeather() {
	/*
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
	
	*/
	//PREVISIONI GIORNATA
	//http://api.openweathermap.org/data/2.5/forecast/daily?id=524901&lang=it&appid=65afaf2b63bbcca892a620603b4bba7b
	
	
	/*
	header('Content-Type: text/plain; charset=utf-8;'); 
	$file = file_get_contents("http://api.openweathermap.org/data/2.5/weather?id=2524907&appid=65afaf2b63bbcca892a620603b4bba7b&lang=it&units=metric");
	$weather = json_decode($file);
	*/
	
	$url = "http://api.openweathermap.org/data/2.5/weather?id=2524907&appid=65afaf2b63bbcca892a620603b4bba7b&lang=it&units=metric";
	//  Initiate curl
	$ch = curl_init();
	// Disable SSL verification
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	// Will return the response, if false it print the response
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	// Set the url
	curl_setopt($ch, CURLOPT_URL,$url);
	// Execute
	$result=curl_exec($ch);
	// Closing
	curl_close($ch);

	// Will dump a beauty json :3
	$weather = (json_decode($result, true));
	
	
	
	if(isset($weather['weather']) && isset($weather['main'])){
		$description = $weather['weather'][0]['description'];
		$temp = $weather['main'][0]['temp'];
		echo $description;
		echo $temp;
	}
	
	return $result;


//print_r(json_decode($file));
}







