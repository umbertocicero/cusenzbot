<?php 
require('weather.php'); 
include("Telegram.php");

define("BOT_TOKEN", "326665840:AAGd8Y7ReODVEtKZ8DffNkwv0CvuWxLIcmE");
define("BOT_USERNAME", "CusenzBot");
/*
$content = file_get_contents("php://input");
$update = json_decode($content, true);

if(!$update)
{
  exit;
}
*/
//GitHub: https://github.com/Eleirbag89/TelegramBotPHP
$telegram = new Telegram(BOT_TOKEN);
$result = $telegram->getData();

$message = isset($result['message']) ? $result['message'] : "";
$messageId = isset($message['message_id']) ? $message['message_id'] : "";
$chatId = isset($message['chat']['id']) ? $message['chat']['id'] : "";
$firstname = isset($message['chat']['first_name']) ? $message['chat']['first_name'] : "";
$lastname = isset($message['chat']['last_name']) ? $message['chat']['last_name'] : "";
$username = isset($message['chat']['username']) ? $message['chat']['username'] : "";
$date = isset($message['date']) ? $message['date'] : "";
$text = isset($message['text']) ? $message['text'] : "";

$new_chat_participant = isset($message['new_chat_participant']) ? $message['new_chat_participant']['username'] : "";
if($new_chat_participant == BOT_USERNAME){
	$text = "/hello";
}

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
		$resultText .= "Questo bot é perfetto per tutti i cosentini (e non solo). \n";
		$resultText .= "Ideale per dare un po di vita al vostro gruppo si amici su Telegram\n\n";
		$resultText .= "Lista comandi per ogni categoria:\n";
		$resultText .= "/testo\n";
		$resultText .= "/meteo\n";
		$resultText .= "/foto\n";
		$resultText .= "\n";
		sendMsg($resultText);
		exit;
	case "/testo":
		$resultText  = "1. Ciao\n";
		$resultText .= "2. Insulta {nome persona} - Es: Insulta Mario\n";
		$resultText .= "3. Minaccia {nome persona} - Es: Minaccia Mario\n";
		$resultText .= "4. Proverbio | Nonno\n";
		$resultText .= "5. Poesia\n";
		sendMsg($resultText);
        break;
	case "/foto":
		$resultText = "1. Foto | Immagine\n";
		sendMsg($resultText);
        break;
	case "/meteo":
		$resultText  = "1. /meteo_oggi\n";
		$resultText .= "2. /meteo_settimana\n";
		sendMsg($resultText);
        break;
	case "/meteo_oggi":
		$resultText = getWeatherToday();
		sendMsg($resultText);
        break;
	case "/meteo_settimana":
		$resultText = getWeatherWeek();
		sendMsg($resultText);
        break;
	case "/hello":
		$resultText  = "Ciao a tutti :)\n";
		$resultText .= "scrivi /start per maggiori informazioni\n";
		sendMsg($resultText);
        break;
		/*
    case "/foto":
		sendPhoto($chatId,$text);
        break;
		*/
	default:
		//sendMsg($chatId,$update);
		$resultText = $_SERVER['REMOTE_ADDR'];
		sendMsg($resultText);
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
				case "@weatherToday":
					$resultText = getWeatherToday();
					break;
				case "@weatherWeek":
					$resultText = getWeatherWeek();
					break;
				case "@photo":
				
					$pictures_a = json_decode(file_get_contents(realpath("pictures.json")), true);
					foreach ($pictures_a as $k => $v) {	
						if($resultText==$k){
							if(is_array($v)) {
							   $random = rand(0, count($v)-1);
							   $picture = $v[$random]; 
							} else {
							   $picture = $v;
							}
							sendPhoto($picture);
							exit;
						}
					}
					exit;
			}
			
			$resultText = str_replace("%s", $secondText, $resultText);
			sendMsg($resultText);
			$found = true;
			exit;
		}
	}
	
}

function sendMsg($text) {
	/*
	header("Content-Type: application/json");
	$parameters = array('chat_id' => $chat_id, "text" => $text);
	$parameters["method"] = "sendMessage";
	
	// imposto la keyboard
	//$parameters["reply_markup"] = '{ "keyboard": [["uno"], ["due"], ["tre"], ["quattro"]], "one_time_keyboard": false}';
	
	echo json_encode($parameters);
	
	*/
	
	$telegram = new Telegram(BOT_TOKEN);
	$chat_id = $telegram->ChatID();
	$content = array('chat_id' => $chat_id, 'text' => $text);
	$telegram->sendMessage($content);
}

function sendPhoto($id) {
	/*
	$botUrl = "https://api.telegram.org/bot" . BOT_TOKEN . "/sendPhoto";
    // change image name and path
	$postFields = array('chat_id' => $c, 'photo' => new CURLFile(realpath("images/".$id)), 'caption' => $text);
	$ch = curl_init(); 
	curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: multipart/form-data"));
	curl_setopt($ch, CURLOPT_URL, $botUrl); 
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
	curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
	// read curl response
	$output = curl_exec($ch);
	*/
	$telegram = new Telegram(BOT_TOKEN);
	$chat_id = $telegram->ChatID();
	$img = new CURLFile(realpath("images/".$id));
	$content = array('chat_id' => $chat_id, 'photo' => $img);
	$telegram->sendPhoto($content);
}

function checkJSON($chatID,$update){
	$myFile = "log.txt";
	$updateArray = print_r($update,TRUE);
	$fh = fopen($myFile, 'a') or die("can't open file");
	fwrite($fh, $chatID ."\n\n");
	fwrite($fh, $updateArray."\n\n");
	fclose($fh);
}





