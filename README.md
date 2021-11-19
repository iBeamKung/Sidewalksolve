# Sidewalksolve ✌

This project is prepared in partial fulfillment of the requirements for the Software Development Practice I subject 010123131 which is a part of the computer engineering curriculum in King Mongkut’s University of Technology North Bangkok — <a href="https://www.kmutnb.ac.th/">KMUTNB</a> — Thailand. **Sidewalksolve** is the place for people who encounter motocycle problems on the pavement and wish to make a complaint to let the authorities know and decide the problem. With Sidewalksolve, citizens can now submit photos of illegal activities on the pavements with any details via online system to the authorities for further legal action. Sidewalksolve aims to let all citizens have the opportunity to help check the orderliness of the country.

## Team Members
- "Netipat Suksai" s6301012610019@email.kmutnb.ac.th
- "Vasapol Rittideah" s6301012620171@email.kmutnb.ac.th
- "Nathanan Srijant" s6301012630095@email.kmutnb.ac.th
- "Taksaporn Nuangkaew" s6301012630052@email.kmutnb.ac.th
- "Prompaun thitipoomdeja" s6301012630133@email.kmutnb.ac.th

# Overview - ภาพรวมของระบบ

<p align="left">
  <img src="https://github.com/iBeamKung/Sidewalksolve/blob/main/image/overview2.png?raw=true">
</p>

There are 3 channels for making a complaint with Sidewalksolve: **Web page**, **LINE**, and **Facebook Messenger**.

## **Web page**
Our webpage are programmed with 3 programming languages: **HTML**, **CSS**, and **JavaScript**. HTML (HyperText Markup Language) is the primary language used to write web pages. It defines the meaning and structure of web content. CSS (Cascading Style Sheets) and JavaScript (JS) are other technologies besides HTML that are generally used to describe a web page's appearance/presentation (CSS) or functionality/behavior (JS). You can simply type <a href="https://sidewalksolve.xyz/">sidewalksolve.xyz</a> URL in any web brower in order to access to Sidewalksolve web page.

## **LINE**
There is Sidewalksolve LINE official account for making a complaint with LINE. It is created with messaging API for building a bot that provide personalized experiences for our users on LINE. This allows us to send messages or photos about your complaint easily by communicating using HTTP Request Methods in JSON format. If you would like to make a complaint with LINE, you can go through this <a href="https://page.line.me/?accountId=422phooi">link</a>.


## Facebook Messenger
There is Sidewalksolve Facebook official page where you can send messages or photos about your complaint with Facebook by integrating with Messenger API that supports to send messages in Facebook to our users. If you would like to make a complaint with Facebook, you can go through this <a href="https://www.facebook.com/Sidewalksolve/">link</a>.
### Backend ของเรานั้นจะประกอบไปด้วย 2 ภาษาด้วยกันคือ PHP,C

#### PHP
เราได้ใช้เป็นเวอชั่น 8.0 ซึ่งเป็นเวอร์ชั่นที่ค่อนข้างใหม่ในปัจจุบัน (17/11/21) โดยเราจะใช้ตัว Composer เป็นตัวจัดการ Library โดยไลบรารี่ที่เราใช้นั้นคือ PHP/MQTT และใช้ CURL ในการสื่อสารผ่าน HTTP Request

#### C
ตัวภาษา C นั้นเราใช้ในการรับข้อความมาจาก MQTT แล้วนำมาค่ามาใส่ลง MySQL Database โดย Library ที่เราใช้นั้นจะมี
- json-c เพื่อที่จะรับ JSON จาก MQTT มาประมวลผล
- mysql.h ใช้ในการจัดการเชื่อต่อกับ Database ในการเก็บข้อมูลของเรา
- mosquitto.h เป็นไลบรารี่ MQTT Client 

### Database
ใช้ MySQL เนื่องจากว่าการเก็บข้อมูลของเรานั้นคงที่ในเรื่องของประเภทข้อมูลไม่จำเป็นต้องขยายหัวข้อในการเก็บของข้อมูล


Process - การทำงาน
----

### Webpage
