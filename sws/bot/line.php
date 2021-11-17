<?php

date_default_timezone_set('Asia/Bangkok'); //กำหนดค่าให้  timestamp ที่จะรับเข้ามาเป็นเวลาตามประเทศไทย
require('vendor/autoload.php');
use \PhpMqtt\Client\MqttClient;
$server   = '203.146.252.179'; //กำหนดเลข server, port, clientId เพื่อทำ mqtt
$port     = 1883;
$clientId = 'Line Client';

$mqtt = new \PhpMqtt\Client\MqttClient($server, $port, $clientId);
$mqtt->connect(); //เชื่อมต่อ mqtt


$Linedata = file_get_contents('php://input'); //รับข้อมูลที่ได้รับจากผู้ใช้เพจ โดยรับมาเป็น json
$jsonData = json_decode($Linedata, true);
$replyToken = $jsonData["events"][0]["replyToken"]; //เก็บ replyToken ของผู้ใช้เพจในตัวเเปร $replyToken
$userID = $jsonData["events"][0]["source"]["userId"]; //เก็บ userId ของผู้ใช้เพจในตัวเเปร $userId
$timestamp = $jsonData["events"][0]["timestamp"]; //เก็บ timestamp ในตัวเเปร  $timestamp
$type = $jsonData["events"][0]["message"]["type"]; //เก็บ type ของข้อมูลในตัวเเปร $type
$text = $jsonData["events"][0]["message"]["text"]; //เก็บข้อความที่ผู้ใช้ส่งมาในตัวเเปร $message
$id = $jsonData["events"][0]["message"]["id"]; //เป็นไอดีรูปเเละวีดีโอ
$s = explode(":", $text); //ทำการเเยกข้อความจาก  :  โดยข้อความที่ถูกเเยกจะถูกเก็บเป็น array ใน ตัวเเปร $s
$c = count($s); //นับจำนวน array ที่อยู่ใน ตัวเเปร $s -- ทำเพื่อตรวจสอบว่าผู้ใช้ส่งข้อความมาครบถ้วนหรือไม่
$expt = explode("\n", $text); //ทำการเเยกข้อความจาก \n โดยข้อความที่ถูกเเยกจะถูกเก็บเป็น array ใน ตัวเเปร $expt
$c_expt = count($expt); //นับจำนวน array ที่อยู่ใน ตัวเเปร $expt -- ทำเพื่อนับบรรทัดข้อความที่ผู้ใช้เพจส่งมา

$timesta = date('Y-m-d H:i:s',$timestamp/1000); //timestamp ที่ได้รับมาจะอยู่ในรูปของ มิลลิวินาที จึงต้องทำการหาร 1000 เพื่อเเปลงให้เป็นหน่วยนาที จากนั้นจึงเเปลง timestamp ให้เป็น ปี-เดือน-วันที่
$url_sendpic = 'https://sidewalksolve.xyz/api/reqpic_LINE.php/'; //ตำเเหน่งไฟล์ที่ต้องการส่ง url รูปไป
$url_sendvdo = 'https://sidewalksolve.xyz/api/reqvid_LINE.php/'; //ตำเเหน่งไฟล์ที่ต้องการส่ง url วีดีโอไป
//$pmmyk3 = 'https://sidewalksolve.herokuapp.com/savepic.php/';

$report = "แจ้งปัญหา";
$cancel = "ยกเลิก"; 
$example = "ตัวอย่างการกรอกแบบฟอร์ม";

/*$jsonn = json_encode($jsonData, JSON_UNESCAPED_UNICODE); //ทำการ encode เจสัน เเละใช้ JSON_UNESCAPED_UNICODE เพื่อให้ส่งข้อมูลเป็นภาษาไทยได้
$mqtt->publish('/sidewalksolve_data',$jsonn, 0);*/

function sendMessage($replyJson, $token){
         $ch = curl_init($token["URL"]);
         curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
         curl_setopt($ch, CURLINFO_HEADER_OUT, true);
         curl_setopt($ch, CURLOPT_POST, true);
         curl_setopt($ch, CURLOPT_HTTPHEADER, array(
             'Content-Type: application/json',
             'Authorization: Bearer ' . $token["AccessToken"])
             );
         curl_setopt($ch, CURLOPT_POSTFIELDS, $replyJson);
         $result = curl_exec($ch);
         curl_close($ch);
	return $result;
 }
 
if ($type == "text" ) //ถ้าข้อมูลที่ส่งมามี type เป็น text
{
	if ($text == $report) //ถ้าผู้ใช้ส่งคำว่า 'แจ้งปัญหา'มา
	{
		$replymessage1["type"] = "text";
		$replymessage1["text"] = "กรุณากรอกข้อมูลตามเเบบฟอร์มต่อไปนี้";
				
		$replymessage2["type"] = "text";
		$replymessage2["text"] = "ชื่อ :\nนามสกุล :\nเบอร์โทร  :\nเลขประจำตัวประชาชน :\nที่อยู่ :\nวันที่เกิดเหตุ( ปี-เดือน-วัน ) :\nเวลา :\nสถานที่เกิดเหตุ :";
		
		$replymessage3["type"] = "text";
		$replymessage3["text"] = "ตัวอย่าง\nชื่อ : ซื่อสัตย์\nนามสกุล : จริงใจ\nเบอร์โทร  : 08XXXXXXXX\nเลขประจำตัวประชาชน  : 1234567891234\nที่อยู่ : 00/0 ซอยxxxx ถนนxxxx เเขวง/ตำบล เขต/อำเภอ รหัสไปรษณีย์\nวันที่เกิดเหตุ : 2021-10-2\nเวลาเกิดเหตุ : 19:00\nสถานที่เกิดเหตุ : ซอยxxxx ถนนxxxx เเขวง/ตำบล เขต/อำเภอ";
		
		$replymessage4["type"] = "text";
		$replymessage4["text"] = "หากท่านไม่ประสงค์จะออกนาม ท่านสามารถเว้นว่างได้  และหากต้องการแจ้งปัญหามากกว่า 1 ปัญหา กรุณาแจ้งห่างกัน 10 นาที ขอบคุณค่ะ";
		
		$replyJson["replyToken"] = $replyToken; //ทำการเก็บข้อความทั้งหมดที่ต้องการส่งกลับไปหาผู้ใช้เพจไว้ใน  $replyJson
		$replyJson["messages"][0] = $replymessage1;
		$replyJson["messages"][1] = $replymessage2;
		$replyJson["messages"][2] = $replymessage3;
		$replyJson["messages"][3] = $replymessage4;
	}
	
	else if ($c == 9 || $c == 10) //ใช้เงื่อนไขนี้เพื่อตรวจสอบว่าผู้ใช้ส่งข้อมูลมาครบถ้วนหรือไม่
	{
		$data = explode("\n", $text); //ทำการเเยกข้อความจาก \n โดยข้อความที่ถูกเเยกจะถูกเก็บเป็น array ใน ตัวเเปร $data
		$dict["from"] = "LINE"; //บรรทัดที่ 80-84, ทำการใส่ข้อมูลลงใน $dict เพื่อทำเป็น json
		$dict["userID"] = $userID;
		$dict["timestamp"] = $timesta;
		//$dict["timestamp"] = $timestamp;
		$dict["type"] = "text";
		$listkey = array("first_name","last_name","tel","id_number","address","date","time","description");
		
		list($word_date, $date_arr5) = explode(":", $data[5]); //เเยกข้อความจาก : โดยข้อความที่อยู่หน้า : จะถูกเก็บในตัวเเปรชื่อ $word_date เเละข้อความที่อยู่หลัง : จะถูกเก็บในตัวเเปรชื่อ $date_arr5
		list($year, $month, $day) = explode("-", $date_arr5);
		$new_str_date = str_replace(' ','',$date_arr5); //ทำการลบช่องว่าง ด้วยการเเทนที่ ' ' ด้วย ''
		$new_str_year = str_replace(' ','',$year);
		$new_str_month = str_replace(' ','',$month);
		$new_str_day = str_replace(' ','',$day);
		
		$timedata = explode(":", $data[6]); //ทำการเเยกข้อความของ array ตัวที่ 6 ( เวลา) ใน $data จาก  :  โดยข้อความที่ถูกเเยกจะถูกเก็บเป็น array ใน ตัวเเปร $timedata
		$count_timedata = count($timedata); //นับจำนวน array ใน $timedata เพื่อตรวจสอบว่าผู้ใช้เพจได้ส่งข้อมูลมาตามฟอร์ม เวลา:00:00 หรือไม่

		if($count_timedata == 3) //ถ้าผู้ใช้เพจใส่เวลามาในฟอร์ม
		{
			list($word_time, $hour_arr6, $minute_arr6) = $timedata; //เเยกข้อความจาก : ซึ่งจะได้ array 3ตัว โดย $hour_arr6 = $timedata[1], $minute_arr6 = $timedata[2]
		}
		
		else if($count_timedata == 2) //ถ้าผู้ใช้เพจไม่ใส่เวลามาในฟอร์ม
		{
			list($word_time, $hour_arr6) = $timedata;
			$minute_arr6 = '';
		}
		
		$new_str_hour = str_replace(' ','',$hour_arr6);		//ทำการลบช่องว่าง ด้วยการเเทนที่ ' ' ด้วย ''
		$new_str_minute = str_replace(' ','',$minute_arr6);	//ทำการลบช่องว่าง ด้วยการเเทนที่ ' ' ด้วย ''
		
		if( $new_str_hour == '' || $new_str_minute == '' || $new_str_year == '' || $new_str_month == '' || $new_str_day == '') //ถ้าผู้ใช้เพจไม่ใส่เวลา หรือ วันที่
		{
			if($new_str_hour == '' && $new_str_minute == '' && $new_str_year == '' && $new_str_month == '' && $new_str_day == '') //ถ้าผู้ใช้เพจไม่ใส่ทั้งเวลา เเละ วันที่
			{
				$replymessage["type"] = "text";
				$replymessage["text"] = "กรุณากรอกวันที่และเวลาที่เกิดเหตุด้วยค่ะ";
				$replyJson["replyToken"] = $replyToken;
				$replyJson["messages"][0] = $replymessage;
			}
			
			else if($new_str_hour == '' && $new_str_minute == '') //ถ้าผู้ใช้เพจไม่ใส่เวลา
			{
				if($new_str_year == '' || $new_str_month == '' || $new_str_day == '')//ในกรณีที่ใส่วันที่ไม่ครบ
				{
					$replymessage["type"] = "text";
					$replymessage["text"] = "กรุณากรอกเวลาเเละวันที่ให้ถูกต้องด้วยค่ะ ";
					$replyJson["replyToken"] = $replyToken;
					$replyJson["messages"][0] = $replymessage;
				}
				
				else if(!is_null($new_str_year) || !is_null($new_str_month) || !is_null($new_str_day))//ในกรณีที่ใส่วันที่ครบ
				{
					$replymessage["type"] = "text";
					$replymessage["text"] = "กรุณากรอกเวลาที่พบเหตุการณ์ดังกล่าวด้วยค่ะ";
					$replyJson["replyToken"] = $replyToken;
					$replyJson["messages"][0] = $replymessage;
				}
			}
			
			else if($new_str_year == '' && $new_str_month == '' && $new_str_day == '') //ถ้าผู้ใช้เพจไม่ใส่วันที่
			{
				if($new_str_hour == '' || $new_str_minute == '') //ถ้าผู้ใช้เพจใส่เวลามาไม่ครบ
				{
					$replymessage["type"] = "text";
					$replymessage["text"] = "กรุณากรอกวันที่เเละเวลาให้ถูกต้องด้วยค่ะ ";
					$replyJson["replyToken"] = $replyToken;
					$replyJson["messages"][0] = $replymessage;
				}
				
				else if(!is_null($new_str_hour) || !is_null($new_str_minute)) //ถ้าผู้ใช้เพจใส่เวลามาครบ
				{
					$replymessage["type"] = "text";
					$replymessage["text"] = "กรุณากรอกวันที่ที่พบเหตุการณ์ดังกล่าวด้วยค่ะ";
					$replyJson["replyToken"] = $replyToken;
					$replyJson["messages"][0] = $replymessage;
				}
			}
			
			else if($new_str_year == '' || $new_str_month == '' || $new_str_day == '') //ถ้าผู้ใช้เพจใส่วันที่ไม่ครบ
			{
				if($new_str_hour == '' || $new_str_minute == '') //ถ้าผู้ใช้เพจใส่เวลาไม่ครบ
				{
					$replymessage["type"] = "text";
					$replymessage["text"] = "กรุณากรอกวันที่เเละเวลาที่พบเหตุการณ์ให้ครบถ้วนด้วยค่ะ";
					$replyJson["replyToken"] = $replyToken;
					$replyJson["messages"][0] = $replymessage;
				}
				
				else //ถ้าผู้ใช้เพจไม่ใส่วันที่เพียงอย่างเดียว
				{
					$replymessage["type"] = "text";
					$replymessage["text"] = "กรุณากรอกวันที่เกิดเหตุให้ถูกต้องด้วยค่ะ";
					$replyJson["replyToken"] = $replyToken;
					$replyJson["messages"][0] = $replymessage;
				}
			}
			
			else if($new_str_hour == '' || $new_str_minute == '') //ถ้าผู้ใช้เพจใส่เวลาไม่ครบ
			{
				if($new_str_year == '' || $new_str_month == '' || $new_str_day == '') //ถ้าผู้ใช้เพจใส่วันที่ไม่ครบ
				{
					$replymessage["type"] = "text";
					$replymessage["text"] = "กรุณากรอกวันที่เเละเวลาที่พบเหตุการณ์ให้ครบถ้วนด้วยค่ะ";
					$replyJson["replyToken"] = $replyToken;
					$replyJson["messages"][0] = $replymessage;
				}
				
				else //ถ้าผู้ใช้เพจไม่ใส่เวลาเพียงอย่างเดียว
				{
					$replymessage["type"] = "text";
					$replymessage["text"] = "กรุณากรอกเวลาที่เกิดเหตุให้ถูกต้องด้วยค่ะ";
					$replyJson["replyToken"] = $replyToken;
					$replyJson["messages"][0] = $replymessage;
					//$dict1[$listkey[$i]] =  '1999-9-9';
				}
			}
			
			else
			{
				$replymessage["type"] = "text";
				$replymessage["text"] = "ไม่รู้โว๊ย";
				$replyJson["replyToken"] = $replyToken;
				$replyJson["messages"][0] = $replymessage;
			}
		}
		else //ถ้าผู้ใช้เพจส่งข้อมูลมาครบถ้วน
		{
			list($word_address, $address_arr4) = explode(":", $data[4]); //ทำการเเยกข้อความจาก :
			$dict1[$listkey[4]] = $address_arr4;	
			
			$dict1[$listkey[5]] = $new_str_date;
			$dict1[$listkey[6]] = $new_str_hour.':'.$new_str_minute; //รวม string
			
			list($word_description, $description_arr7) = explode(":", $data[7]); //ทำการเเยกข้อความจาก :
			$dict1[$listkey[7]] = $description_arr7; //บรรทัดที่ 143-146 เเละ 149 ทำการใส่ข้อมูลลงใน $dict1 เพื่อทำเป็น json
		
			
			for($i = 0; $i < 4;$i++)
			{
				list($data_key, $data_value) = explode(":", $data[$i]);	//ทำการเเยกข้อความจาก :
				$new_str = str_replace(' ','',$data_value); //ทำการลบช่องว่าง ด้วยการเเทนที่ ' ' ด้วย ''
				$dict1[$listkey[$i]] = $new_str;
			}
			
			$replymessage1["type"] = "text";
			$replymessage1["text"] = "เราได้รับข้อมูลของท่านเเล้ว";
			
			$replymessage2["type"] = "text";
			$replymessage2["text"] = "กรุณาส่งรูปหรือวีดีโอหลักฐานของท่านภายใน 10 นาที และส่งครั้งละไม่เกิน2รูป";
			
			$replyJson["replyToken"] = $replyToken;
			$replyJson["messages"][0] = $replymessage1;
			$replyJson["messages"][1] = $replymessage2;
			
			$dict["text"] = $dict1; //นำ $dict1 ไปใส่ใน $dict โดยมีค่า key เป็น "text"
			$jsonn = json_encode($dict, JSON_UNESCAPED_UNICODE); //ทำการ encode เจสัน เเละใช้ JSON_UNESCAPED_UNICODE เพื่อให้ส่งข้อมูลเป็นภาษาไทยได้
		
			$mqtt->publish('/sidewalksolve_data',$jsonn, 0); //ส่ง mqtt ไปยัง topic /sidewalksolve_data
			$mqtt->disconnect(); //หยุดเชื่อมต่อกับ mqtt
		}
	}
	
	if($c_expt > 1 && $c_expt != 8) //นับบรรทัดว่าผู้ใช้เพจส่งข้อมูลมาครบตรมฟอร์มหรือไม่ หากไม่ครบจะทำเงื่อนไขนี้
	{
		$replymessage["type"] = "text";
		$replymessage["text"] = "ท่านกรอกข้อมูลไม่ตรงแบบฟอร์ม กรุณากรอกข้อมูลใหม่อีกครั้งค่ะ";
		$replyJson["replyToken"] = $replyToken;
		$replyJson["messages"][0] = $replymessage;
	}
}
else if($type == "image") //ถ้าผู้ใช้ส่งรูปมา
{
	$replymessage1["type"] = "text";
	$replymessage1["text"] = "เราได้รับไฟล์รูปของท่านเเล้ว ขอขอบคุณ ";
	$replyJson["replyToken"] = $replyToken;
	$replyJson["messages"][0] = $replymessage1;
	
	$dict["from"] = "LINE";
    $dict["userID"] = $userID;
	$dict["timestamp"] = $timesta;
	$dict["type"] = "image";
	$dict["imageid"] = $id;
	$jsonn = json_encode($dict);

	/*$data = explode("\n", $text);
	list($data2, $data3) = explode(":", $data[8]);
	$id_data["num_pic"] = $data3;*/
	
	$id_data["uID"] = $userID; //นำ userID ของผู้ใช้เพจเก็บไว้ใน dict id_data
	$id_data["id"] = $id; //นำ id รูปที่ได้รับจากผู้ใช้เพจเก็บไว้ใน dict id_data
	$id_data["ts"] = $timesta; //timestamp ที่เเปลงเป็น เป็น ปี-เดือน-วันที่
	$id_data["Timestamp"] = $timestamp; //นำ timestamp ของผู้ใช้เพจเก็บไว้ใน dict id_data
	//$id_data = $userID. ':' .$id;
	
	/*$mqtt->publish('/sidewalksolve_data',$jsonn, 0);
	$mqtt->disconnect();*/
	
	$ch = curl_init($url_sendpic);
	$jsonData = json_encode($id_data, JSON_UNESCAPED_UNICODE);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
	$result = curl_exec($ch); //ทำการส่ง json $id_data ไปยังไฟล์ php ที่อยู่บน hostinger ซึ่งมีที่อยู่ตามลิงก์ที่เก็บไว้ในตัวเเปร $url_sendpic
	curl_close($ch);
	
	/*$ch = curl_init($pmmyk3);
	$jsonData = json_encode($id_data, JSON_UNESCAPED_UNICODE);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
	$result = curl_exec($ch);
	curl_close($ch);*/
}
else if($type == "video") //ถ้าผู้ใช้ส่งวีดีโอมา
{
	$replymessage1["type"] = "text";
	$replymessage1["text"] = "เราได้รับวีดีโอของท่านเเล้ว ขอขอบคุณ";
	$replyJson["replyToken"] = $replyToken;
	$replyJson["messages"][0] = $replymessage1;
	
	$dict["from"] = "LINE";
    $dict["userID"] = $userID;
	$dict["timestamp"] = $timestamp;
	$dict["type"] = "video";
	$dict["videoid"] = $id;
	$jsonn = json_encode($dict);
	
	$id_data["uID"] = $userID; //นำ userID ของผู้ใช้เพจเก็บไว้ใน dict id_data
	$id_data["id"] = $id; //นำ id รูปที่ได้รับจากผู้ใช้เพจเก็บไว้ใน dict id_data
	$id_data["ts"] = $timesta; //timestamp ที่เเปลงเป็น เป็น ปี-เดือน-วันที่
	$id_data["Timestamp"] = $timestamp; //นำ timestamp ของผู้ใช้เพจเก็บไว้ใน dict id_data
	
	$ch = curl_init($url_sendvdo);
	$jsonData = json_encode($id_data, JSON_UNESCAPED_UNICODE);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
	$result = curl_exec($ch); //ทำการส่ง json $id_data ไปยังไฟล์ php ที่อยู่บน hostinger ซึ่งมีที่อยู่ตามลิงก์ที่เก็บไว้ในตัวเเปร $url_sendvdo
	curl_close($ch);
}
else //ถ้าเป็น type อื่น
{
  $replymessage1["type"] = "text";
  $replymessage1["text"] = "ไม่มีข้อมูลที่ต้องการ";
  $replyJson["replyToken"] = $replyToken;
  $replyJson["messages"][0] = $replymessage1;
}

$lineData['URL'] = "https://api.line.me/v2/bot/message/reply";
$lineData['AccessToken'] = "(ufXK/2GNqBYv3nLxa3UUnnYz6NzoTO3GsBI+9z5lzvbrjqoTZOva+IjBlxaDKazf6zEexNTk0Wo4sCXczZCqCHLfux/817VkeX6BkcZR0nSeo1ps3V6cZHC9c+rrPsX2PWOTQs0VuhGlAYtQ3EdXWQdB04t89/1O/w1cDnyilFU=)";
$encodeJson = json_encode($replyJson);
$results = sendMessage($encodeJson,$lineData); //ส่งข้อมูลที่อยู่ใน replyJson กลับไปหาผู้ใช้เพจ
?>