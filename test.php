<?php echo "test";

echo getMeteo();

function getMeteo() {
	header('Content-Type: text/plain; charset=utf-8;'); 
	$file = file_get_contents("http://api.openweathermap.org/data/2.5/weather?id=2524907&appid=65afaf2b63bbcca892a620603b4bba7b&lang=it&units=metric");
	$weather = json_decode($file);
	
	if(isset($weather['weather']) && isset($weather['main'])){
		$description = $weather['weather'][0]['description'];
		$temp = $weather['main'][0]['temp'];
		echo $description;
		echo $temp;
	}
   // return $phpObj;
	return $file;
}