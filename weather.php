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
		echo $lastTime;
		echo "    ";
		echo $lastTime;
		echo "    ";
		echo $today;
		
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
			$weather['last_update'] = $today;
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
		
		//$j_time = date("d-m-Y H:00",$dt);
		
		$name = $weather['name'];	
		$wind = $weather['wind']['speed'];
		$result  = "Meteo di ".$name."\n";
		$result .= "Aggiornato alle ".$j_time." \n\n";
		$result .= "Temperatura ".$temp."\u02DA \n";
		$result .= $description." \n";
		$result .= "Vento ".$wind." Km/h \n";
		$result .= "Umidit\u00E0 ".$humidity."% \n";
	}
	return $result;	
}