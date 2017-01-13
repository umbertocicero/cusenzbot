<?php 

function callWeather($url) {
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
	return $result;
}

function writeWeather($jsonFile,$file_name){
	$myfile = fopen($file_name, "w");
	$txt = $jsonFile;
	fwrite($myfile, $txt);
	fclose($myfile);	
}

function getWeather($type){
	switch ($type) {
    case "today":
        $file_name = "weather_today.json";
		$url = "http://api.openweathermap.org/data/2.5/weather?id=2524907&appid=65afaf2b63bbcca892a620603b4bba7b&lang=it&units=metric";
		$dayParser = "YmdH00";
		break;
	case "week":
		 $file_name = "weather_week.json";
		 $url = "http://api.openweathermap.org/data/2.5/forecast/daily?id=2524907&appid=65afaf2b63bbcca892a620603b4bba7b&lang=it&units=metric";
		 $dayParser = "Ymd";
        break;
	default:
		exit;
	}
	$myfile = fopen($file_name, "r");
	$jsonFile = fread($myfile,filesize($file_name));
	$weather = (json_decode($jsonFile, true));
	fclose($myfile);
	if(isset($weather)) {		
		$lastTime = isset($weather['last_update']) ? $weather['last_update'] : 0;
		$today = gmdate($dayParser);
		
		if($lastTime < $today || $weather['cod'] != 200){
			$jsonFile  = callWeather($url);
			$weather = json_decode($jsonFile, true);
			$weather['last_update'] = $today;
			$jsonFile = json_encode($weather);
			writeWeather($jsonFile, $file_name);
		}
		
	} else {
		$jsonFile = callWeather($url);	
		$weather = json_decode($jsonFile, true);
		if(isset($weather)) {
			$weather['last_update'] = gmdate($dayParser);
			$jsonFile = json_encode($weather);
		}
		writeWeather($jsonFile, $file_name);
	}
	
	return $jsonFile;
}

function getWeatherToday(){	
	$weather = json_decode(getWeather("today"), true);
	$result = "Meteo momentaneamente non disponibile";
	if(isset($weather['weather']) && isset($weather['main']) && $weather['cod'] == 200){
		$description = $weather['weather'][0]['description'];
		$icon = getWeatherIco($weather['weather'][0]['main']);
		$temp = $weather['main']['temp'];
		$humidity = $weather['main']['humidity'];
		$dt = $weather['dt'];
		
		$datetime = new DateTime();
		$datetime->setTimestamp($dt);
		$la_time = new DateTimeZone('Europe/Rome');
		$datetime->setTimezone($la_time);
		
		/*
		$oldLocale = setlocale(LC_TIME, 'it_IT');
		echo utf8_encode( strftime("%a %d %b %Y", $row['eventtime']) );
		setlocale(LC_TIME, $oldLocale);
		*/
		
		$j_time = $datetime->format('d-m-Y H:i');
		
		
		
		
		$name = $weather['name'];	
		$wind = $weather['wind']['speed'];
		
		$result  = "Meteo ".$name."\n\n";
		//$result .= "Aggiornato alle ".$j_time." \n\n";
		$result .= utf8_encode("Temperatura ".$temp."°  \n");
		$result .= ucfirst($description)." ".$icon." \n";
		$result .= "Vento ".$wind." Km/h \n";
		$result .= utf8_encode("Umidità ".$humidity."% \n");
	}
	return ($result);
	
	
	//return utf8_encode($result);	
}
function getWeatherWeek(){	
	$weather = json_decode(getWeather("week"), true);
	$result = "Meteo momentaneamente non disponibile";
	if(isset($weather['city']) && isset($weather['list']) && $weather['cod'] == 200){
		$name = $weather['city']['name'];	
		$result  = "Meteo ".$name."\n\n";
		
		$list = $weather['list'];		
		foreach ($list as $weatherValue){
			$dt = $weatherValue['dt'];
			$datetime = new DateTime();
			$datetime->setTimestamp($dt);
			
			$la_time = new DateTimeZone('Europe/Rome');
			$datetime->setTimezone($la_time);
			$j_time = $datetime->format('d-m-Y');
			
			setlocale(LC_ALL, 'ita', 'it_IT'); 
		    $day = strftime("Giorno %m-%d", $dt);
			$result .= $day." \n";
			
			//$result .= $day." ".$j_time." \n";
			
			$temp = $weatherValue['temp']['day'];
			$description = $weatherValue['weather'][0]['description'];
			$mainType = $weatherValue['weather'][0]['main'];
			$icon = getWeatherIco($mainType);
			
			$result .= utf8_encode("Temperatura ".$temp."° \n");
			$result .= ucfirst($description)." ".$icon." \n";
			$result .= "\n";
			
		}
	
	}
	return $result;		
}

function getWeatherIco($type){	
	$icon = ""; 
	$type = strtolower($type);
	switch ($type) {
		case "clear":
			$icon = '"\u2600"'; 
			break;
		case "clouds":
			$icon = '"\u2601"'; 
			break;
		case "rain":
			$icon = '"\uD83C\uDF27"'; 
			break;
		case "drizzle":
			$icon = '"\uD83C\uDF27"'; 
			break;
		case "thunderstorm":
			$icon = '"\u26C8"'; 
			break;
		case "snow":
			$icon = '"\u2744"'; 
			break;
		case "mist":
			$icon = '"\uD83C\uDF01"'; 
			break;
		case "fog":
			$icon = '"\u1F32B"'; 
			break;
		case "atmosphere":
			$icon = '"\u1F32B"'; 
			break;
	}
	return json_decode($icon);
}