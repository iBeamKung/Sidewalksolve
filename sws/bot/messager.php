<?php

date_default_timezone_set('Asia/Bangkok');		//กำหนดค่าให้  timestamp ที่จะรับเข้ามาเป็นเวลาตามประเทศไทย
require('vendor/autoload.php');
use \PhpMqtt\Client\MqttClient;
$server   = ' YOUR MQTT BROKER ';			//กำหนดเลข server, port, clientId เพื่อทำ mqtt
$port     = 1883;
$clientId = 'Facebook Client';

$mqtt = new \PhpMqtt\Client\MqttClient($server, $port, $clientId);
$mqtt->connect();		//เชื่อมต่อ mqtt

$access_token = ' YOUR ACCESS TOKEN '; //access_token ของเพจ facebook
$verify_token = 'sidewalksolve';									

if (isset($_GET['hub_verify_token'])) { 
    if ($_GET['hub_verify_token'] === $verify_token) {		//ตั้ง verify token ของเพจ facebook
        echo $_GET['hub_challenge'];
        return;
    } else {
        echo 'Invalid Verify Token';
        return;
    }
}

/* receive and send messages */
$input = json_decode(file_get_contents('php://input'), true);		//รับข้อมูลที่ได้รับจากผู้ใช้เพจ โดยรับมาเป็น json
$text = $input['entry'][0]['messaging'][0]['message']['text'];		//เก็บข้อความที่ผู้ใช้เพจส่งมาในตัวเเปร $text
$report = "แจ้งปัญหา";
$ID = $input['entry'][0]['messaging'][0]['sender']['id'];		//เก็บ id ของผู้ใช้เพจในตัวเเปร $ID
$timestamp = $input['entry'][0]['messaging'][0]['timestamp'];		//เก็บ timestamp ในตัวเเปร  $timestamp
$urlimgvid = $input['entry'][0]['messaging'][0]['message']['attachments'][0]['payload']['url'];		//เก็บ urlรูปเเละวีดีโอ ในตัวเเปร $urlimgvid
$urlimgvid2 = $input['entry'][0]['messaging'][0]['message']['attachments'][1]['payload']['url'];
$urlimgvid3 = $input['entry'][0]['messaging'][0]['message']['attachments'][2]['payload']['url'];
$type = $input['entry'][0]['messaging'][0]['message']['attachments'][0]['type'];		//ถ้าข้อมูลถูกส่งมาเป็นรูปหรือวีดีโอ คำว่า image หรือ video จะถูกเก็บในตัวเเปร $type
$url = 'https://graph.facebook.com/v2.6/me/messages?access_token='. $access_token;
$url_sendpic = 'https://sidewalksolve.xyz/api/reqpic_FB.php/';		//ตำเเหน่งไฟล์ที่ต้องการส่ง url รูปไป
$url_sendvdo = 'https://sidewalksolve.xyz/api/reqvid_FB.php/';		//ตำเเหน่งไฟล์ที่ต้องการส่ง url วีดีโอไป
$s = explode(":", $text);		//ทำการเเยกข้อความจาก  :  โดยข้อความที่ถูกเเยกจะถูกเก็บเป็น array ใน ตัวเเปร $s
$c = count($s);		//นับจำนวน array ที่อยู่ใน ตัวเเปร $s -- ทำเพื่อตรวจสอบว่าผู้ใช้ส่งข้อความมาครบถ้วนหรือไม่
$expt = explode("\n", $text);		//ทำการเเยกข้อความจาก \n โดยข้อความที่ถูกเเยกจะถูกเก็บเป็น array ใน ตัวเเปร $expt
$c_expt = count($expt);		//นับจำนวน array ที่อยู่ใน ตัวเเปร $expt -- ทำเพื่อนับบรรทัดข้อความที่ผู้ใช้เพจส่งมา
$timesta = date('Y-m-d H:i:s',$timestamp/1000);		//timestamp ที่ได้รับมาจะอยู่ในรูปของ มิลลิวินาที จึงต้องทำการหาร 1000 เพื่อเเปลงให้เป็นหน่วยนาที จากนั้นจึงเเปลง timestamp ให้เป็น ปี-เดือน-วันที่

/*$jsonn = json_encode($input, JSON_UNESCAPED_UNICODE); //ทำการ encode เจสัน เเละใช้ JSON_UNESCAPED_UNICODE เพื่อให้ส่งข้อมูลเป็นภาษาไทยได้
$mqtt->publish('/sidewalksolve_data',$jsonn, 0);*/

function file_get_contents_curl($urll) {
    $curl_resource = curl_init();
  
    curl_setopt($curl_resource, CURLOPT_HEADER, 0);
    curl_setopt($curl_resource, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl_resource, CURLOPT_URL, $urll);
  
    $dataa = curl_exec($curl_resource);
    curl_close($curl_resource);
    return $dataa;
}

if ($text == $report)	//ถ้าผู้ใช้พิมพ์คำว่า 'แจ้งปัญหา'มา จะทำการส่งข้อความกลับไปตามข้อความที่เก็บอยู่ใน $replymessage
{
    /*initialize curl*/
    $ch = curl_init($url);
    $replymessage[1] = "กรุณากรอกข้อมูลตามเเบบฟอร์มต่อไปนี้ ";
	$replymessage[2] = "ชื่อ : \nนามสกุล :  \nเบอร์โทร  : \nเลขประจำตัวประชาชน :  \nที่อยู่ :  \nวันที่เกิดเหตุ :  \nเวลา :  \nสถานที่เกิดเหตุ : ";
	$replymessage[3] = "ตัวอย่าง\nชื่อ : ซื่อสัตย์\nนามสกุล : จริงใจ\nเบอร์โทร  : 08XXXXXXXX\nเลขประจำตัวประชาชน  : 1234567891234\nที่อยู่ : 00/0 ซอยxxxx ถนนxxxx เเขวง/ตำบล เขต/อำเภอ รหัสไปรษณีย์\nวันที่เกิดเหตุ : 2021-10-2\nเวลาเกิดเหตุ : 19:00\nสถานที่เกิดเหตุ : ซอยxxxx ถนนxxxx เเขวง/ตำบล เขต/อำเภอ";
	$replymessage[4] = "หากท่านไม่ประสงค์จะออกนาม ท่านสามารถเว้นว่างได้  และหากต้องการแจ้งปัญหามากกว่า 1 ปัญหา กรุณาแจ้งห่างกัน 10 นาที ขอบคุณค่ะ";
	
	for($i = 1; $i < 5;$i++)	//ทำการส่งข้อความทั้งหมด  4 ข้อความไปยังผู้ใช้ เพจโดยทำการส่งทีละ 1 ข้อความ
	{	
		$resp = array(
		  'recipient' => array(
			'id' => $ID
		  ),
		  'message' => array(
			'text' => $replymessage[$i]
		  )
		);
		
    $jsonData = json_encode($resp, JSON_UNESCAPED_UNICODE);
    /* curl setting to send a json post data */
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
	$result = curl_exec($ch); //ใช้คำสั่งนี้เพื่อส่งข้อความดังกล่าวไปยังผู้ใช้เพจ
	}
	curl_close($ch);
	return $result;
}

else if($c_expt == 8)
{	
	if ($c == 9 || $c == 10) //ใช้เงื่อนไขนี้เพื่อตรวจสอบว่าผู้ใช้ส่งข้อมูลมาครบถ้วนหรือไม่
	{
		$data = explode("\n", $text);	//ทำการเเยกข้อความจาก \n โดยข้อความที่ถูกเเยกจะถูกเก็บเป็น array ใน ตัวเเปร $data
		$dict["from"] = "FB";			//บรรทัดที่ 91-94, ทำการใส่ข้อมูลลงใน $dict เพื่อทำเป็น json
		$dict["userID"] = $ID;
		$dict["timestamp"] = $timesta;
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
				$ch = curl_init($url);
				$replymessage[1] = "กรุณากรอกวันที่และเวลาที่เกิดเหตุด้วยค่ะ";
				$resp = array(
				  'recipient' => array(
					'id' => $ID
				  ),
				  'message' => array(
					'text' => $replymessage[1]
				  )
				);
				
				/*$jsonData = json_encode($resp, JSON_UNESCAPED_UNICODE);
				curl_setopt($ch, CURLOPT_POST, 1);
				curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
				curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
				$result = curl_exec($ch);	//ทำการส่งข้อความกลับไปหาผู้ใช้เพจว่า "กรุณากรอกวันที่และเวลาที่เกิดเหตุด้วยค่ะ"
				curl_close($ch);
				return $result;*/
			}
			
			else if($new_str_hour == '' && $new_str_minute == '') //ถ้าผู้ใช้เพจไม่ใส่เวลา
			{
				if($new_str_year == '' || $new_str_month == '' || $new_str_day == '')//ในกรณีที่ใส่วันที่ไม่ครบ
				{
					$ch = curl_init($url);
					$replymessage[1] = "กรุณากรอกเวลาเเละวันที่ให้ถูกต้องด้วยค่ะ";
					$resp = array(
					  'recipient' => array(
						'id' => $ID
					  ),
					  'message' => array(
						'text' => $replymessage[1]
					  )
					);
					
					/*$jsonData = json_encode($resp, JSON_UNESCAPED_UNICODE);
					curl_setopt($ch, CURLOPT_POST, 1);
					curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
					curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
					$result = curl_exec($ch); //ทำการส่งข้อความกลับไปหาผู้ใช้เพจว่า "กรุณากรอกเวลาเเละวันที่ให้ถูกต้องด้วยค่ะ"
					curl_close($ch);
					return $result;*/
				}
				
				else if(!is_null($new_str_year) || !is_null($new_str_month) || !is_null($new_str_day))//ในกรณีที่ใส่วันที่ครบ
				{
					$ch = curl_init($url);
					$replymessage[1] = "กรุณากรอกเวลาที่พบเหตุการณ์ดังกล่าวด้วยค่ะ";
					$resp = array(
					  'recipient' => array(
						'id' => $ID
					  ),
					  'message' => array(
						'text' => $replymessage[1]
					  )
					);
					
					/*$jsonData = json_encode($resp, JSON_UNESCAPED_UNICODE);
					curl_setopt($ch, CURLOPT_POST, 1);
					curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
					curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
					$result = curl_exec($ch); //ทำการส่งข้อความกลับไปหาผู้ใช้เพจว่า "กรุณากรอกเวลาที่พบเหตุการณ์ดังกล่าวด้วยค่ะ"
					curl_close($ch);
					return $result;*/
				}
			}
			
			else if($new_str_year == '' && $new_str_month == '' && $new_str_day == '') //ถ้าผู้ใช้เพจไม่ใส่วันที่
			{
				if($new_str_hour == '' || $new_str_minute == '') //ถ้าผู้ใช้เพจใส่เวลามาไม่ครบ
				{
					$ch = curl_init($url);
					$replymessage[1] = "กรุณากรอกวันที่เเละเวลาให้ถูกต้องด้วยค่ะ";
					$resp = array(
					  'recipient' => array(
						'id' => $ID
					  ),
					  'message' => array(
						'text' => $replymessage[1]
					  )
					);
					
					/*$jsonData = json_encode($resp, JSON_UNESCAPED_UNICODE);
					curl_setopt($ch, CURLOPT_POST, 1);
					curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
					curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
					$result = curl_exec($ch); //ทำการส่งข้อความกลับไปหาผู้ใช้เพจว่า "กรุณากรอกวันที่เเละเวลาให้ถูกต้องด้วยค่ะ"
					curl_close($ch);
					return $result;*/
				}
				
				else if(!is_null($new_str_hour) || !is_null($new_str_minute)) //ถ้าผู้ใช้เพจใส่เวลามาครบ
				{
					$ch = curl_init($url);
					$replymessage[1] = "กรุณากรอกวันที่ที่พบเหตุการณ์ดังกล่าวด้วยค่ะ";
					$resp = array(
					  'recipient' => array(
						'id' => $ID
					  ),
					  'message' => array(
						'text' => $replymessage[1]
					  )
					);
					
					/*$jsonData = json_encode($resp, JSON_UNESCAPED_UNICODE);
					curl_setopt($ch, CURLOPT_POST, 1);
					curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
					curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
					$result = curl_exec($ch); //ทำการส่งข้อความกลับไปหาผู้ใช้เพจว่า "กรุณากรอกวันที่ที่พบเหตุการณ์ดังกล่าวด้วยค่ะ"
					curl_close($ch);
					return $result;*/
				}
			}
			
			else if($new_str_year == '' || $new_str_month == '' || $new_str_day == '') //ถ้าผู้ใช้เพจใส่วันที่ไม่ครบ
			{
				if($new_str_hour == '' || $new_str_minute == '') //ถ้าผู้ใช้เพจใส่เวลาไม่ครบ
				{
					$ch = curl_init($url);
					$replymessage[1] = "กรุณากรอกวันที่เเละเวลาที่พบเหตุการณ์ให้ครบถ้วนด้วยค่ะ";
					$resp = array(
					  'recipient' => array(
						'id' => $ID
					  ),
					  'message' => array(
						'text' => $replymessage[1]
					  )
					);
					
					/*$jsonData = json_encode($resp, JSON_UNESCAPED_UNICODE);
					curl_setopt($ch, CURLOPT_POST, 1);
					curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
					curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
					$result = curl_exec($ch); //ทำการส่งข้อความกลับไปหาผู้ใช้เพจว่า "กรุณากรอกวันที่เเละเวลาที่พบเหตุการณ์ให้ครบถ้วนด้วยค่ะ"
					curl_close($ch);
					return $result;*/
				}
				
				else //ถ้าผู้ใช้เพจไม่ใส่วันที่เพียงอย่างเดียว
				{
					$ch = curl_init($url);
					$replymessage[1] = "กรุณากรอกวันที่เกิดเหตุให้ถูกต้องด้วยค่ะ";
					$resp = array(
					  'recipient' => array(
						'id' => $ID
					  ),
					  'message' => array(
						'text' => $replymessage[1]
					  )
					);
					
					/*$jsonData = json_encode($resp, JSON_UNESCAPED_UNICODE);
					curl_setopt($ch, CURLOPT_POST, 1);
					curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
					curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
					$result = curl_exec($ch); //ทำการส่งข้อความกลับไปหาผู้ใช้เพจว่า "กรุณากรอกวันที่เกิดเหตุให้ถูกต้องด้วยค่ะ"
					curl_close($ch);
					return $result;*/
				}
			}
			
			else if($new_str_hour == '' || $new_str_minute == '') //ถ้าผู้ใช้เพจใส่เวลาไม่ครบ
			{
				if($new_str_year == '' || $new_str_month == '' || $new_str_day == '') //ถ้าผู้ใช้เพจใส่วันที่ไม่ครบ
				{
					$ch = curl_init($url);
					$replymessage[1] = "กรุณากรอกวันที่เเละเวลาที่พบเหตุการณ์ให้ครบถ้วนด้วยค่ะ";
					$resp = array(
					  'recipient' => array(
						'id' => $ID
					  ),
					  'message' => array(
						'text' => $replymessage[1]
					  )
					);
					
					/*$jsonData = json_encode($resp, JSON_UNESCAPED_UNICODE);
					curl_setopt($ch, CURLOPT_POST, 1);
					curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
					curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
					$result = curl_exec($ch); //ทำการส่งข้อความกลับไปหาผู้ใช้เพจว่า "กรุณากรอกวันที่เเละเวลาที่พบเหตุการณ์ให้ครบถ้วนด้วยค่ะ"
					curl_close($ch);
					return $result;*/
				}
				
				else //ถ้าผู้ใช้เพจไม่ใส่เวลาเพียงอย่างเดียว
				{
					$ch = curl_init($url);
					$replymessage[1] = "กรุณากรอกเวลาที่เกิดเหตุให้ถูกต้องด้วยค่ะ";
					$resp = array(
					  'recipient' => array(
						'id' => $ID
					  ),
					  'message' => array(
						'text' => $replymessage[1]
					  )
					);
					
					/*$jsonData = json_encode($resp, JSON_UNESCAPED_UNICODE);
					curl_setopt($ch, CURLOPT_POST, 1);
					curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
					curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
					$result = curl_exec($ch); //ทำการส่งข้อความกลับไปหาผู้ใช้เพจว่า "กรุณากรอกเวลาที่เกิดเหตุให้ถูกต้องด้วยค่ะ"
					curl_close($ch);
					return $result;*/
				}
			}
			$jsonData = json_encode($resp, JSON_UNESCAPED_UNICODE);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
			$result = curl_exec($ch); //ทำการส่งข้อความกลับไปหาผู้ใช้เพจว่า "กรุณากรอกเวลาที่เกิดเหตุให้ถูกต้องด้วยค่ะ"
			curl_close($ch);
			return $result;
		}
		else //ถ้าผู้ใช้เพจส่งข้อมูลมาครบถ้วน
		{
	 
			list($word_address, $address_arr4) = explode(":", $data[4]); //ทำการเเยกข้อความจาก :
			$dict1[$listkey[4]] = $address_arr4;	
			
			$dict1[$listkey[5]] = $new_str_date;
			$dict1[$listkey[6]] = $new_str_hour.':'.$new_str_minute; //รวม string
			
			list($word_description, $description_arr7) = explode(":", $data[7]); //ทำการเเยกข้อความจาก :
			$dict1[$listkey[7]] = $description_arr7;				//บรรทัดที่ 189-192 เเละ 195 ทำการใส่ข้อมูลลงใน $dict1 เพื่อทำเป็น json
			
			for($i = 0; $i < 4;$i++)
			{
				list($data_key, $data_value) = explode(":", $data[$i]);	//ทำการเเยกข้อความจาก :
				$new_str = str_replace(' ','',$data_value); //ทำการลบช่องว่าง ด้วยการเเทนที่ ' ' ด้วย ''
				$dict1[$listkey[$i]] = $new_str;
			}
			
			$dict["text"] = $dict1; //นำ $dict1 ไปใส่ใน $dict โดยมีค่า key เป็น "text"
			$jsonn = json_encode($dict, JSON_UNESCAPED_UNICODE); //ทำการ encode เจสัน เเละใช้ JSON_UNESCAPED_UNICODE เพื่อให้ส่งข้อมูลเป็นภาษาไทยได้
			
			$mqtt->publish('/sidewalksolve_data',$jsonn, 0); //ส่ง mqtt ไปยัง topic /sidewalksolve_data
			$mqtt->disconnect(); //หยุดเชื่อมต่อกับ mqtt
			
			$ch = curl_init($url);
			$replymessage[1] = "เราได้รับข้อมูลของท่านเเล้ว";
			$replymessage[2] = "กรุณาส่งรูปหรือวีดีโอหลักฐานของท่านภายใน 10 นาที";
			for($i = 1; $i < 3;$i++)
			{
				$resp = array(
				  'recipient' => array(
					'id' => $ID
				  ),
				  'message' => array(
					'text' => $replymessage[$i]
				  )
				);
				
				$jsonData = json_encode($resp, JSON_UNESCAPED_UNICODE);
				curl_setopt($ch, CURLOPT_POST, 1);
				curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
				curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
				$result = curl_exec($ch); //ส่งข้อความหาผู้ใช้เพจตามข้อความที่อยู่ใน $replymessage[i]
				curl_close($ch);
			}
			return $result;
		}
	}
}

else if($c_expt > 1 && $c_expt != 8) //นับบรรทัดว่าผู้ใช้เพจส่งข้อมูลมาครบตรมฟอร์มหรือไม่ หากไม่ครบจะทำเงื่อนไขนี้
{
	$ch = curl_init($url);
	$replymessage[1] = "ท่านกรอกข้อมูลไม่ตรงแบบฟอร์ม กรุณากรอกข้อมูลใหม่อีกครั้งค่ะ";
		$resp = array(
		  'recipient' => array(
			'id' => $ID
		  ),
		  'message' => array(
			'text' => $replymessage[1]
		  )
		);
			
	$jsonData = json_encode($resp, JSON_UNESCAPED_UNICODE);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
	$result = curl_exec($ch); //ส่งข้อความหาผู้ใช้เพจตามข้อความที่อยู่ใน $replymessage[1]
	curl_close($ch);
	return $result;
}

else if ($type == "image") //ถ้าผู้ใช้ส่งรูปมา
{	
	$num_pic = $input['entry'][0]['messaging'][0]['message']['attachments'];
	for($i = 0; $i < sizeof($num_pic);$i++) //จำนวน array ใน $num_pic
	{
		$urlimgvid = $input['entry'][0]['messaging'][0]['message']['attachments'][$i]['payload']['url']; //ทำการเก็บลิงก์รูปใน $id_data
		$id_data[$i] = $urlimgvid;
	}
		$id_data['len'] = sizeof($num_pic); //เก็บจำนวนภาพที่ผู้ใช้เพจส่งมาไว้ใน dict เพื่อที่จะนำค่านี้ไปใช้ต่อไป
		$ch = curl_init($url);
		$replymessage[1] = "เราได้รับไฟล์รูปของท่านเเล้ว ขอขอบคุณ";
		//$replymessage[2] = "ลิงก์รูปของท่านคือ";
		/*$replymessage[3] = $urlimgvid2;
		$replymessage[4] = $urlimgvid3;*/
		//$replymessage[4] = $ID;
		for($i = 1; $i < 2;$i++){
		$resp = array(
		  'recipient' => array(
			'id' => $ID
		  ),
		  'message' => array(
			'text' => $replymessage[$i]
		  )
		);
		
		$jsonData = json_encode($resp, JSON_UNESCAPED_UNICODE);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
		$result = curl_exec($ch); //ส่งข้อความหาผู้ใช้เพจว่า "เราได้รับไฟล์รูปของท่านเเล้ว ขอขอบคุณ"
		}
	
	curl_close($ch);
	//return $result;
	
	$id_data["uID"] = $ID; //นำ id ของผู้ใช้เพจเก็บไว้ใน dict id_data
	$id_data["Timestamp"] = $timestamp; //นำ timestamp ของผู้ใช้เพจเก็บไว้ใน  dict id_data
	
	$curl_resource = curl_init($url_sendpic);
	$jsonData = json_encode($id_data, JSON_UNESCAPED_UNICODE);
	curl_setopt($curl_resource, CURLOPT_POST, 1);
    curl_setopt($curl_resource, CURLOPT_POSTFIELDS, $jsonData);
    curl_setopt($curl_resource, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
	$post = curl_exec($curl_resource); //ทำการส่ง json $id_data ไปยังไฟล์ php ที่อยู่บน hostinger ซึ่งมีที่อยู่ตามลิงก์ที่เก็บไว้ในตัวเเปร $url_sendpic
	curl_close($curl_resource);
	return $post;
}
else if ($type == "video") //ถ้าผู้ใช้ส่งวีดีโอมา
{
	$ch = curl_init($url);
	$replymessage[1] = "เราได้รับวีดีโอของท่านเเล้ว ขอขอบคุณ";
	//$replymessage[2] = "ลิงก์วีดีโอของท่านคือ";
	//$replymessage[3] = $urlimgvid;
	for($i = 1; $i < 2;$i++){
	$resp = array(
      'recipient' => array(
        'id' => $ID
      ),
      'message' => array(
        'text' => $replymessage[$i]
      )
    );
	$jsonData = json_encode($resp, JSON_UNESCAPED_UNICODE);
	curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
	$result = curl_exec($ch); //ส่งข้อความหาผู้ใช้เพจว่า "เราได้รับวีดีโอของท่านเเล้ว ขอขอบคุณ"
	}
	curl_close($ch);
	//return $result;
	
	$id_data["uID"] = $ID; //นำ id ของผู้ใช้เพจเก็บไว้ใน dict id_data
	$id_data["id"] = $urlimgvid;  //นำ url รูปที่ได้รับจากผู้ใช้เพจเก็บไว้ใน dict id_data
	$id_data["Timestamp"] = $timestamp; //นำ timestamp ของผู้ใช้เพจเก็บไว้ใน dict id_data
	
	$curl_resource = curl_init($url_sendvdo);
	$jsonData = json_encode($id_data, JSON_UNESCAPED_UNICODE);
	curl_setopt($curl_resource, CURLOPT_POST, 1);
    curl_setopt($curl_resource, CURLOPT_POSTFIELDS, $jsonData);
    curl_setopt($curl_resource, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
	$post = curl_exec($curl_resource); //ทำการส่ง json $id_data ไปยังไฟล์ php ที่อยู่บน hostinger ซึ่งมีที่อยู่ตามลิงก์ที่เก็บไว้ในตัวเเปร $url_sendvdo
	curl_close($curl_resource);
	return $post;
}
/*
else if ($type == "audio")
{
	$ch = curl_init($url);
	$replymessage = "เราได้รับคลิปเสียงของท่านเเล้ว ขอขอบคุณ";
	$resp = array(
      'recipient' => array(
        'id' => $ID
      ),
      'message' => array(
        'text' => $replymessage
      )
    );
	$jsonData = json_encode($resp, JSON_UNESCAPED_UNICODE);
	curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
	$result = curl_exec($ch);
	curl_close($ch);
	return $result;
}
else if ($type == "file")
{
	$ch = curl_init($url);
	$replymessage = "เราได้รับไฟล์ของท่านเเล้ว ขอขอบคุณ";
	$resp = array(
      'recipient' => array(
        'id' => $ID
      ),
      'message' => array(
        'text' => $replymessage
      )
    );
	$jsonData = json_encode($resp, JSON_UNESCAPED_UNICODE);
	curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
	$result = curl_exec($ch);
	curl_close($ch);
	return $result;
}*/
?>