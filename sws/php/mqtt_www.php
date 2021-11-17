<?php

require('vendor/autoload.php');

use \PhpMqtt\Client\MqttClient;


$server   = ' You Broker MQTT ';
$port     = 1883;
$clientId = 'WWW Client';

$photo = json_decode($_POST["photo_src"], true);
$photoall = '';

foreach ($photo as $value) {
  $photoall =  $photoall . ",../uploads/WWW/image/$value";
}
$photoall = ltrim($photoall, $photoall[0]);

$video = json_decode($_POST["video_src"], true);
$videoall = '';

foreach ($video as $value) {
  $videoall =  $videoall . ",../uploads/WWW/vdo/$value";
}
$videoall = ltrim($videoall, $videoall[0]);

$send = array('from' => 'WWW',
		'timestamp' => date('Y-m-d H:i:s'),
		'first_name' => $_POST["first_name"],
		'last_name' => $_POST["last_name"],
		'tel' => $_POST["tel"],
		'id_number' => $_POST["id_number"],
		'address' => $_POST["address"],
		'date' => $_POST["date"],
		'description' => $_POST["description"],
		'photo_src' => $photoall,
		'video_src' => $videoall
	);

$mqtt = new \PhpMqtt\Client\MqttClient($server, $port, $clientId);
$mqtt->connect();
$mqtt->publish('/sidewalksolve_data', json_encode($send, JSON_UNESCAPED_UNICODE), 0);
$mqtt->disconnect();

echo "Success MQTT Publish ";

?>