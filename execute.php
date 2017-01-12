<?php require('weather.php');
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
		$resultText .= "Questo bot é perfetto per tutti i cosentini (e non solo)\n";
		$resultText .= "Ideale per dare un po di vita al vostro gruppo si amici su Telegram\n\n";
		$resultText .= "lista comandi per ogni categoria:\n";
		$resultText .= "/testo\n";
		$resultText .= "/meteo\n";
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
	case "/meteo":
		$resultText = "1. Meteo\n";
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
			switch ($resultText) {
				case "@meteo":
					$resultText = getWeatherMsg();
					break;
				case "@foto":
					sendPhoto($chatId,$t);
					exit;
			}
			
			$resultText = str_replace("%s", $secondText, $resultText);
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
function sendPhoto($c,$id) {
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






