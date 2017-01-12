<?php 

$urlWeek = "http://api.openweathermap.org/data/2.5/forecast/daily?id=2524907&appid=65afaf2b63bbcca892a620603b4bba7b&lang=it&units=metric";
$urlToday = "http://api.openweathermap.org/data/2.5/weather?id=2524907&appid=65afaf2b63bbcca892a620603b4bba7b&lang=it&units=metric";

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
	$file_name = "weather_today.json";
	$url = $urlToday;
	switch ($type) {
    case "today":
        $file_name = "weather_today.json";
		break;
	case "week":
		 $file_name = "weather_week.json";
        break;
	}
	$myfile = fopen($file_name, "r");
	$jsonFile = fread($myfile,filesize($file_name));
	$weather = (json_decode($jsonFile, true));
	fclose($myfile);
	if(isset($weather)) {		
		$lastTime = isset($weather['last_update']) ? $weather['last_update'] : 0;
		$today = gmdate("YmdH00");
		
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
			$weather['last_update'] = gmdate("YmdH00");
			$jsonFile = json_encode($weather);
		}
		writeWeather($jsonFile, $file_name);
	}
	return $jsonFile;
}

function getWeatherToday(){	
	$weather = (json_decode(getWeather("today"), true));
	$result = "Meteo momentaneamente non disponibile";
	if(isset($weather['weather']) && isset($weather['main']) && $weather['cod'] == 200){
		$description = $weather['weather'][0]['description'];
		$temp = $weather['main']['temp'];
		$humidity = $weather['main']['humidity'];
		$dt = $weather['dt'];
		
		$datetime = new DateTime();
		$datetime->setTimestamp($dt);
		$la_time = new DateTimeZone('Europe/Rome');
		$datetime->setTimezone($la_time);
		$j_time = $datetime->format('d-m-Y H:i');
		
		$name = $weather['name'];	
		$wind = $weather['wind']['speed'];
		$result  = "Meteo ".$name."\n\n";
		//$result .= "Aggiornato alle ".$j_time." \n\n";
		$result .= "Temperatura ".$temp."° \n";
		$result .= ucfirst($description)." \n";
		$result .= "Vento ".$wind." Km/h \n";
		$result .= "Umidità ".$humidity."% \n";
	}
	$encoded = utf8_encode($result);
	return $encoded;	
}