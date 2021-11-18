# SideWalkSolve - ระบบแก้ปัญหามอเตอร์ไซค์บนทางเท้า

<p align="left">
  <img src="https://github.com/iBeamKung/Sidewalksolve/blob/main/image/logo-1500x1100.png?raw=true" width="250">
</p>

โปรเจคนี้เป็นส่วนหนึ่งของรายวิชา 010123131 Software Development Practice I (1/2564)<br />
คณะวิศวะกรรมคอมพิวเตอร์  มหาวิทยาลัยพระจอมเกล้าพระนครเหนือ

This project is prepared in partial fulfillment of the requirements for the Software Development Practice I subject 010123131 which is a part of the computer engineering curriculum in King Mongkut’s University of Technology North Bangkok — <a href="https://www.kmutnb.ac.th/">KMUTNB</a> — Thailand. **Sidewalksolve** is the place for people who encounter motocycle problems on the pavement and wish to make a complaint to let the authorities know and decide the problem. Now critizens can submit photos of illegal acttivities on the pavements with any details to the authorities for further legal action. Sidewalksolve wish that all critizens will have the opportunity to help checking the orderliness of the country.

Overview - ภาพรวมของระบบ
----
<p align="left">
  <img src="https://github.com/iBeamKung/Sidewalksolve/blob/main/image/overview2.png?raw=true">
</p>

### ระบบนี้จะ UI ช่องทางในการรับปัญหาอยู่ 3 รูปแบบ คือ
- Webpage
- Line
- Facebook Messager

#### Webpage
จะใช้ภาษา HTML,CSS,JS เป็น UI ในการแสดงผล UI ในเว็ปเพราะง่ายต่อการเรียนรู้และการออกแบบ

#### Line
โดยตัวระบบ Line นั้นได้มีระบบห้องแชทที่เรียกว่า Line Official Account ซึ่งได้มีระบบ Messaging API ซึ่งทำให้เราสามารถส่งข้อความได้โดยการสื่อสารกันแบบ HTTP Request ในรูปแบบ JSON

#### Facebook Messager
ระบบของ Facebook นั้นจะเรียกว่า Messager โดยเราสามารถสร้างหน้าเพจ Facebook แล้วให้เชื่อมต่อกับ Messager ได้โดยที่มี API รองรับในการส่งข้อความไปใน Messager ให้บุคคลได้

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
