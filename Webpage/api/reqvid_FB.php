<?php
$id_data = json_decode(file_get_contents('php://input'), true);		//รับข้อมูล
$url = 'https://sidewalksolve.xyz/uploads/FB/vdo';
$uid_data = $id_data["uID"];		//ไอดีผู้ใช้เพจ
$img_vdo = $id_data["id"];		//ลิงก์วีดีโอ
$times_tamp = $id_data["Timestamp"];

function file_get_contents_curl($urll) {
    $curl_resource = curl_init();
  
    curl_setopt($curl_resource, CURLOPT_HEADER, 0);
    curl_setopt($curl_resource, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl_resource, CURLOPT_URL, $urll);
  
    $dataa = curl_exec($curl_resource);
    curl_close($curl_resource);
    return $dataa;
}

//เซฟ path วีดีโอลงใน hostinger
define('UPLOAD_DIR', '../uploads/FB/vdo/');
file_put_contents($img_vdo, file_get_contents('php://input') . PHP_EOL, FILE_APPEND);
$file = UPLOAD_DIR . uniqid() . '.mp4';
$success = file_put_contents($file, file_get_contents_curl($img_vdo));

function isnull($conn,$new_path_vid,$coun,$num_path_pic)		//ในกรณีที่ผู้ใช้เพจยังไม่ได้ส่งวีดีโอมา
{
	$sql = "UPDATE sws_data SET video_src='$new_path_vid' WHERE id=$coun";		//อัพเดต column video_src โดยใส่ path วีดีโอลงไป
	if ($conn->query($sql) === TRUE) 
	{
		echo "Record updated successfully"  ."<br>";
	}
	
	else
	{
		echo "Error updating record: " . $conn->error;
	}
	
	if($num_path_pic >= 2)		//ถ้ามี path ภาพอย่างน้อย 2 รูปถูกเก็บเอาไว้
	{
		$sql2 = "UPDATE sws_data SET report='1' WHERE id=$coun";		//อัพเดต column report ให้มีค่า 1
		
		if ($conn->query($sql2) === TRUE) 
		{
			echo "Record updated successfully2." ."<br>";
		}

		else
		{
			echo "Error updating record2: " . "<br>". $conn->error;
		}
	}
}

function one_pic($conn,$new_path_vid,$coun,$old_path_vid)		//ถ้าผู้ใช้ได้ส่งวีดีโอมาเเล้วอย่างน้อย 1 วีดีโอ
{
	$merge = $old_path_vid. ',' .$new_path_vid;		//ทำการรวม string -- นำ path วีดีโอใหม่ไปต่อกับ path เดิมที่อยู่ใน database โดยคั่นด้วยจุลภาค
	$sql = "UPDATE sws_data SET video_src='$merge' WHERE id=$coun";		//อัพเดต column video_src โดยใส่ $merge ลงไป
	
	if ($conn->query($sql) === TRUE) 
	{
		echo "Record updated successfully2." ."<br>";
	}

	else
	{
		echo "Error updating record2: " . "<br>". $conn->error;
	}
}

$severname = "sidewalksolve.xyz";
$username = "u722950798_admin";
$password = "Rsp010123131";
$dbname = "u722950798_sidewalksolve";

// Create connection
$mysql = mysqli_connect($severname,$username,$password,$dbname);

// Check connection
if (!$mysql)
{
  die("Connection failed: " . mysqli_connect_error());
}

echo "Connected successfully" . "<br>";
mysqli_set_charset($mysql, "utf8");

$sql = "SELECT from_where, userID, photo_src, video_src, timestamp FROM sws_data"; //เลือกข้อมูลจาก column ต่างๆ
$result = $mysql->query($sql);
$name = strval($file);		//เเปลง path วีดีโอ เป็น string
$c = 0;
$int_timesta = (int)$times_tamp;		//ทำให้ timestamp เป็นตัวเลข
$time_timestamp = date('Y-m-d H:i:s', $int_timesta/1000);		//แปลง timestamp เป็น  ปี-เดือน-วันที่
$convert_to_string = strval($time_timestamp);		//เเปลง  ปี-เดือน-วันที่ ให้เป็น string

//$time_timestamp = '2021-02-21 21:30:16';

//timestamp ขณะที่ผู้ใช้เพจส่งวีดีโอมา
list($datel, $timel) = explode(" ", $convert_to_string);
list($yearl, $monthl, $dayl) = explode("-", $datel);
list($hrl, $minutel, $secondl) = explode(":", $timel);

if ($result->num_rows > 0)
{
	//output data of each row
	while($row = $result->fetch_assoc())
	{
		echo "from_where: ". $row["from_where"] . " userID: ". $row["userID"]. " video_src: " . $row["video_src"]. "timestamp: ". $row["timestamp"] ."<br>";		

		$from = $row["from_where"];
		$user_id = $row["userID"];
		$data_pic = $row["photo_src"];
		$data_vid = $row["video_src"];
		$time_stamp = $row["timestamp"];
		
		$explod_data_vid = explode(",", $data_vid);		//ข้อมูลใน column video_src
		$num_of_path_vid = count($explod_data_vid);		//นับว่ามี path วีดีโอถูกเก็บไว้กี่ path
		$c = $c + 1;		//เมื่อพบข้อมูลในตาราง 1 เเถว จะทำการ + เพิ่มทีละ 1
		
		$explod_data_pic = explode(",", $data_pic);		//ข้อมูลใน column photo_src
		$num_of_path_pic = count($explod_data_pic);		//นับว่ามี path ภาพถูกเก็บไว้กี่ path

		list($date, $time) = explode(" ", $time_stamp);
		list($year, $month, $day) = explode("-", $date);
		list($hr, $minute, $second) = explode(":", $time);
		$minu = (int)$minute + 10;
			
		//ทำการใส่ path วีดีโอลงไปใน database ซึ่งวีดีโอนี้จะต้องส่งภายใน 10นาที หลังจากส่งข้อมูล
		if ($user_id == $uid_data && $from == 'FB')	//เลือกเเถวที่ตรงกับ uid ของผู้ส่งข้อมูล เเละเป็นข้อมูลที่มาจาก FB จากนั้นจะทำการใส่ path วีดีโอลงไป ณ ช่องใส่ path วีดีโอ
		{
			echo 'Hi'. "<br>";
			if ( (int)$minu >= 60 )		//เกิน 60นาที ขึ้นชั่วโมงใหม่
			{
				$hrcheck = (int)$hr + 1;
				$minu = (int)$minu - 60;
				echo  'yerrrr' . "<br>";
			}
			
			if( (int)$minute < 50 )		//ส่งครั้งแรกก่อน 50 บวกนาทีปกติ
			{
				if ( $date == $datel && (int)$hrl == (int)$hr && (int)$minutel <= (int)$minu )		//ต้องชั่วโมงเดียวกัน
				{
					echo '45' ."<br>";
					//echo "from_where: ". $row["from_where"] . " userID: ". $row["userID"]. " video_src: " . $row["video_src"]."<br>";
					if (is_null($data_vid))		//ในกรณีที่ผู้ใช้เพจยังไม่ได้ส่งวีดีโอมา
					{
						$complete = isnull($mysql,$name,$c,$num_of_path_pic);
					}
					
					else if($num_of_path_vid >= 1)		//ในกรณีที่ผู้ใช้เคยส่งวีดีโอเเล้ว
					{
						$complete = one_pic($mysql,$name,$c,$data_vid);
					}
				}
			}
			else
			{
				echo 'sleepy' ."<br>";
				if( (int)$hr == 23 )		//ส่งครั้งแรกมาตอน 5 ทุ่ม 45 - 59 นาที ถ้านับไปอีก 15 นาที จะเกินวัน
				{
					echo '23 pass' . "<br>";
					if($date == $datel)		//ถ้าส่งครั้งที่สองวันเดียวกันให้ผ่าน
					{
						echo 'date pass' . "<br>";
						if (is_null($data_vid))		//ในกรณีที่ผู้ใช้เพจยังไม่ได้ส่งวีดีโอมา
						{
							$complete = isnull($mysql,$name,$c,$num_of_path_pic);
						}
						
						else if($num_of_path_vid >= 1)		//ในกรณีที่ผู้ใช้เคยส่งวีดีโอเเล้ว
						{
							$complete = one_pic($mysql,$name,$c,$data_vid);
						}
					}
					else		//ถ้าคนละวัน
					{
						echo 'diff date pass' . "<br>";
						if ( (int)$month == 4 || (int)$month == 6 || (int)$month == 9 || (int)$month == 11 )		//เดือนจบที่30วัน
						{
							echo '6th month' . "<br>";
							if ( (int)$yearl == (int)$year )		//ต้องปีเดียวกัน (เพราะไม่มีเดือน 12)
							{
								echo 'same year' . "<br>";
								if ( (int)$day == 30 )		//ถ้าส่งครั้งแรกวันที่ 30
								{
									echo 'date = 30th' . "<br>";
									if ( (int)$monthl == (int)$month + 1 && (int)$dayl == 1 && (int)$hrl == 0 && (int)$minutel <= (int)$minu )		//ส่งครั้งที่สองจะต้องขึ้นวันที่ 1 ของเดือนถัดไป
									{
										echo 'date+1 = 1st' . "<br>";
										if (is_null($data_vid))		//ในกรณีที่ผู้ใช้เพจยังไม่ได้ส่งวีดีโอมา
										{
											$complete = isnull($mysql,$name,$c,$num_of_path_pic);
										}
										
										else if($num_of_path_vid >= 1)		//ในกรณีที่ผู้ใช้เคยส่งวีดีโอเเล้ว
										{
											$complete = one_pic($mysql,$name,$c,$data_vid);
										}
									}
								}
								else		//วันที่ไม่ใช่ 30ของเดือน 4 6 9 11
								{
									echo 'not 30' . "<br>";
									if ( (int)$monthl == (int)$month && (int)$dayl == (int)$day+1 && (int)$hrl == 0 && (int)$minutel <= (int)$minu )		//จะต้องวันถัดไปของเดือนเดียวกัน
									{
										echo 'date+1' . "<br>";
										if (is_null($data_vid))		//ในกรณีที่ผู้ใช้เพจยังไม่ได้ส่งวีดีโอมา
										{
											$complete = isnull($mysql,$name,$c,$num_of_path_pic);
										}
										
										else if($num_of_path_vid >= 1)		//ในกรณีที่ผู้ใช้เคยส่งวีดีโอเเล้ว
										{
											$complete = one_pic($mysql,$name,$c,$data_vid);
										}
									}
								}
							}
						}
						else if ( (int)$month == 1 || (int)$month == 3 || (int)$month == 5 || (int)$month == 7 || (int)$month == 8 || (int)$month == 10 )		//เดือนจบที่31วัน
						{
							echo '1st month' . "<br>";
							if ( (int)$yearl == (int)$year )		//ต้องปีเดียวกัน (เพราะไม่มีเดือน 12)
							{
								echo 'same year yarr' . "<br>";
								if ( (int)$day == 31 )		//ถ้าส่งครั้งแรกวันที่ 31
								{
									echo 'date = 31th' . "<br>";
									if ( (int)$monthl == (int)$month + 1 && (int)$dayl == 1 && (int)$hrl == 0 && (int)$minutel <= (int)$minu )		//ส่งครั้งที่สองจะต้องขึ้นวันที่ 1 ของเดือนถัดไป
									{
										echo 'date+1 = 1st yarr' . "<br>";
										if (is_null($data_vid))		//ในกรณีที่ผู้ใช้เพจยังไม่ได้ส่งวีดีโอมา
										{
											$complete = isnull($mysql,$name,$c,$num_of_path_pic);
										}
										
										else if($num_of_path_vid >= 1) 		//ในกรณีที่ผู้ใช้เคยส่งวีดีโอเเล้ว
										{
											$complete = one_pic($mysql,$name,$c,$data_vid);
										}
									}
								}
								else 		//วันที่ไม่ใช่ 31
								{
									if ( (int)$monthl == (int)$month && (int)$dayl == (int)$day+1 && (int)$hrl == 0 && (int)$minutel <= (int)$minu ) 		//จะต้องเป็นวันถัดไปของเดือนเดียวกัน
									{
										echo 'date+1 yarr' . "<br>";
										if (is_null($data_vid))		//ในกรณีที่ผู้ใช้เพจยังไม่ได้ส่งวีดีโอมา
										{
											$complete = isnull($mysql,$name,$c,$num_of_path_pic);
										}
										
										else if($num_of_path_vid >= 1)		//ในกรณีที่ผู้ใช้เคยส่งวีดีโอเเล้ว
										{
											$complete = one_pic($mysql,$name,$c,$data_vid);
										}
									}
								}
							}
						}
						else if ( (int)$month == 12 )
						{
							echo '12nd month' . "<br>";
							if ( (int)$day < 31 ) 		//ถ้าส่งครั้งแรกวันอื่นที่ไม่ใช่ 31
							{
								echo '12.20' . "<br>";
								if ( (int)$monthl == (int)$month && (int)$dayl == (int)$day+1 && (int)$hrl == 0 && (int)$minutel <= (int)$minu )		//จะต้องเป็นวันถัดไปของเดือนเดียวกัน
								{
									echo 'date+1 12.' . "<br>";
									if (is_null($data_vid)) 		//ในกรณีที่ผู้ใช้เพจยังไม่ได้ส่งวีดีโอมา
									{
										$complete = isnull($mysql,$name,$c,$num_of_path_pic);
									}
									
									else if($num_of_path_vid >= 1) 		//ในกรณีที่ผู้ใช้เคยส่งวีดีโอเเล้ว
									{
										$complete = one_pic($mysql,$name,$c,$data_vid);
									}
								}
							}
							else if( (int)$day == 31 ) 		//ถ้าส่งครั้งแรกวันที่ 31
							{
								echo '12.31' . "<br>";
								if ( (int)$yearl == (int)$year+1 && (int)$monthl == 1 && (int)$dayl == 1 && (int)$hrl == 0 && (int)$minutel <= (int)$minu )		//จะต้องเป็นวันที่ 1มค
								{
									echo 'to 1 jan' . "<br>";
									if (is_null($data_vid))		//ในกรณีที่ผู้ใช้เพจยังไม่ได้ส่งวีดีโอมา
									{
										$complete = isnull($mysql,$name,$c,$num_of_path_pic);
									}
									
									else if($num_of_path_vid >= 1)		//ในกรณีที่ผู้ใช้เคยส่งวีดีโอเเล้ว
									{
										$complete = one_pic($mysql,$name,$c,$data_vid);
									}
								}
							}
						}
						else		//เดือน กพ
						{
							if ( (int)$year%4 == 0 )		//ปีที่มีเดือนกุมพาลงท้ายด้วย 29 วัน
							{
								echo '%4' . "<br>";
								if ( (int)$day == 29)		//เปลี่ยนเดือน
								{
									echo '29' . "<br>";
									if ((int)$yearl == (int)$year && (int)$monthl == 3 && (int)$dayl == 1 && (int)$hrl == 0 && (int)$minutel <= (int)$minu )		//จะต้องเป็นวันที่ 1มีนา
									{
										echo 'feb 29' . "<br>";		//pass
										if (is_null($data_vid))		//ในกรณีที่ผู้ใช้เพจยังไม่ได้ส่งวีดีโอมา
										{
											$complete = isnull($mysql,$name,$c,$num_of_path_pic);
										}
										
										else if($num_of_path_vid >= 1)		//ในกรณีที่ผู้ใช้เคยส่งวีดีโอเเล้ว
										{
											$complete = one_pic($mysql,$name,$c,$data_vid);
										}
									}
								}
								else		//ไม่เปลี่ยนเดือน
								{
									if ( (int)$yearl == (int)$year && (int)$monthl == (int)$month && (int)$dayl == (int)$day+1 && (int)$hrl == 0 && (int)$minutel <= (int)$minu ) //จะต้องเป็นวันที่ 1มีนา
									{
										//pass
										echo 'feb not 29' . "<br>";
										if (is_null($data_vid))		//ในกรณีที่ผู้ใช้เพจยังไม่ได้ส่งวีดีโอมา
										{
											$complete = isnull($mysql,$name,$c,$num_of_path_pic);
										}
										
										else if($num_of_path_vid >= 1)		//ในกรณีที่ผู้ใช้เคยส่งวีดีโอเเล้ว
										{
											$complete = one_pic($mysql,$name,$c,$data_vid);
										}
									}
								}
							}
							else		//ปีที่เดือนกุมพาลงด้วยวันที่ 28
							{
								if ( (int)$day == 28)		//เปลี่ยนเดือน
								{
									echo '28' . "<br>";
									if ((int)$yearl == (int)$year && (int)$monthl == 3 && (int)$dayl == 1 && (int)$hrl == 0 && (int)$minutel <= (int)$minu )		//จะต้องเป็นวันที่ 1มีนา
									{
										//pass
										echo 'feb 28' . "<br>";
										if (is_null($data_vid))		//ในกรณีที่ผู้ใช้เพจยังไม่ได้ส่งวีดีโอมา
										{
											$complete = isnull($mysql,$name,$c,$num_of_path_pic);
										}
										
										else if($num_of_path_vid >= 1)		//ในกรณีที่ผู้ใช้เคยส่งวีดีโอเเล้ว
										{
											$complete = one_pic($mysql,$name,$c,$data_vid);
										}
									}
								}
								else		//ไม่เปลี่ยนเดือน
								{
									if ( (int)$yearl == (int)$year && (int)$monthl == (int)$month && (int)$dayl == (int)$day+1 && (int)$hrl == 0 && (int)$minutel <= (int)$minu )		//จะต้องเป็นวันที่ 1มีนา
									{
										//pass
										echo 'feb not 28' . "<br>";
										if (is_null($data_vid))		//ในกรณีที่ผู้ใช้เพจยังไม่ได้ส่งวีดีโอมา
										{
											$complete = isnull($mysql,$name,$c,$num_of_path_pic);
										}
										
										else if($num_of_path_vid >= 1)		//ในกรณีที่ผู้ใช้เคยส่งวีดีโอเเล้ว
										{
											$complete = one_pic($mysql,$name,$c,$data_vid);
										}
									}
								}
							}
							
						}
					}
				}
				else //ส่งตอนก่อน 5 ทุ่ม ไม่เปลี่ยนวัน
				{
					echo 'before 23' . "<br>";
					if ( (int)$hrl == (int)$hr )		//ชมเท่ากันผ่าน
					{
						echo 'same hr' . "<br>";
						if (is_null($data_vid))		//ในกรณีที่ผู้ใช้เพจยังไม่ได้ส่งวีดีโอมา
						{
							$complete = isnull($mysql,$name,$c,$num_of_path_pic);
						}
						
						else if($num_of_path_vid >= 1)		//ในกรณีที่ผู้ใช้เคยส่งวีดีโอเเล้ว
						{
							$complete = one_pic($mysql,$name,$c,$data_vid);
						}
					}
					else if ( (int)$hrl == (int)$hrcheck && (int)$minutel <= (int)$minu )		//เช็คเวลา
					{
						echo 'change hr' . "<br>";
						if (is_null($data_vid))		//ในกรณีที่ผู้ใช้เพจยังไม่ได้ส่งวีดีโอมา
						{
							$complete = isnull($mysql,$name,$c,$num_of_path_pic);
						}
						
						else if($num_of_path_vid >= 1)		//ในกรณีที่ผู้ใช้เคยส่งวีดีโอเเล้ว
						{
							$complete = one_pic($mysql,$name,$c,$data_vid);
						}
					}
				}
			}
		}
	}
}
else
{
  echo "0 results";
}

$mysql->close();

?>