<?php
//conection: 
$link = mysqli_connect("jss-itmo-444-db.cl1a5ekfqhis.us-east-1.rds.amazonaws.com","jssdbuser","superpassword","3306") or die("Error " . mysqli_error($link)); 

echo "Here is the result: " . $link;


$sql = "CREATE TABLE phpdata 
(
ID INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
ID INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
uname VARCHAR(20),
email VARCHAR(20),
phone VARCHAR(20),
raws3 VARCHAR(256),
finisheds3 VARCHAR(256),
jpgname VARCHAR(256),
state TinyInt(3)
)";

$con->query($sql);

?>
