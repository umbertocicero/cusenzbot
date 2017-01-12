<?php echo "test";

echo getMeteo();

function getMeteo() {
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
}