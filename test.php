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
	$weather = (json_decode($result, true));
	
	
	
	if(isset($weather['weather']) && isset($weather['main'])){
		$description = $weather['weather'][0]['description'];
		$temp = $weather['main']['temp'];
		$dt = $weather['dt'];
		echo $description;
		echo $temp;
		echo $dt;
		
		
		$j_time = date("Ymd",$dt);                           // 20010310
		echo 'j_time:       '. $j_time ."\n";
	}
	
	return $result;
}

function checkWeatherTime(){
	$myfile = fopen("weather.json", "w") or die("Unable to open file!");
	$txt = "John Doe\n";
	fwrite($myfile, $txt);
	$txt = "Jane Doe\n";
	fwrite($myfile, $txt);
	fclose($myfile);
	
	
	
	$myfile = fopen("weather.json", "r") or die("Unable to open file!");
	echo fread($myfile,filesize("weather.json"));
	fclose($myfile);
	
}