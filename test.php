<?php echo "test/n";

echo checkWeatherTime();

function getWeather() {
	
	$today = date("Ymd");                           // 20010310
	echo 'Now:       '. $today ."\n";
	
	
	
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
	//$weather = readWeather($result);
	$weather = (json_decode($result, true));
	if(isset($weather['weather']) && isset($weather['main'])){
		$description = $weather['weather'][0]['description'];
		$temp = $weather['main']['temp'];
		$dt = $weather['dt'];
		$j_time = date("Ymd",$dt); 
		//$result = $description." - ".$temp." - ".$temp." - ".$j_time;
		
		//echo $temp;
		//echo $dt;
		
		
		                          // 20010310
		//echo 'j_time:       '. $j_time ."\n";
	}
	
	
	return $result;
}

function readWeather($json){
	$weather = (json_decode($json, true));
	if(isset($weather['weather']) && isset($weather['main'])){
		$description = $weather['weather'][0]['description'];
		$temp = $weather['main']['temp'];
		$dt = $weather['dt'];
		$j_time = date("Ymd",$dt); 
		//$result = $description." - ".$temp." - ".$temp." - ".$j_time;
		
		//echo $temp;
		//echo $dt;
		
		
		                          // 20010310
		//echo 'j_time:       '. $j_time ."\n";
	}
	return $weather;
}
function writeWeather($jsonFile){
	$myfile = fopen("weather.json", "w");
	$txt = $jsonFile;
	fwrite($myfile, $txt);
	fclose($myfile);	
}

function checkWeatherTime(){	
	$myfile = fopen("weather.json", "r");
	if(isset($myfile)) {
		//while(!feof($myfile)) {
		//  echo fgets($myfile) . "<br>";
		//}
		$jsonFile = fread($myfile,filesize("weather.json"));
		echo $jsonFile;
		//echo fread($myfile,filesize("weather.json"));
		
	}
	fclose($myfile);
	
	
}