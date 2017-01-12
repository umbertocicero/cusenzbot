<?php 

function callWeather() {
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
	return $result;
}

function writeWeather($jsonFile){
	$myfile = fopen("weather.json", "w");
	$txt = $jsonFile;
	fwrite($myfile, $txt);
	fclose($myfile);	
}

function getWeather(){	
	$myfile = fopen("weather.json", "r");
	$jsonFile = fread($myfile,filesize("weather.json"));
	$weather = (json_decode($jsonFile, true));
	fclose($myfile);
	if(isset($weather)) {		
		$lastTime = isset($weather['last_update']) ? $weather['last_update'] : 0;
		$today = gmdate("YmdH00");
		
		if($lastTime < $today || $weather['cod'] != 200){
			$jsonFile  = callWeather();
			$weather = json_decode($jsonFile, true);
			$weather['last_update'] = $today;
			$jsonFile = json_encode($weather);
			writeWeather($jsonFile);
		}
		
	} else {
		$jsonFile = callWeather();	
		$weather = json_decode($jsonFile, true);
		if(isset($weather)) {
			$weather['last_update'] = gmdate("YmdH00");
			$jsonFile = json_encode($weather);
		}
		writeWeather($jsonFile);
	}
	return $jsonFile;
}

function getWeatherMsg(){	
	$weather = (json_decode(getWeather(), true));
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
		$result  = "Meteo ".$name."\n";
		//$result .= "Aggiornato alle ".$j_time." \n\n";
		$result .= "Temperatura ".$temp."\u{00B0} \n";
		$result .= ucfirst($description)." \n";
		$result .= "Vento ".$wind." Km/h \n";
		$result .= "Umidit\u00E0 \U00E0".$humidity."% \n";
	}
	$encoded = json_encode($result, JSON_UNESCAPED_UNICODE);
	$decoded = json_decode($encoded, true);
	return $decoded;	
}