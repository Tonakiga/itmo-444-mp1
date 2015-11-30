<?php
session_start();
ini_set('display_errors',1); 
$uploaddir = '/tmp/';
$uploadfile = $uploaddir . basename($_FILES['userFileForm']['name']);

if (move_uploaded_file($_FILES['userFileForm']['tmp_name'], $uploadfile)) {
  echo "File is valid, and was successfully uploaded.\n";
}
else {
    echo "Possible file upload attack!\n";
}
echo 'Here is some more debugging info:';
print_r($_FILES);
  
require '/var/www/html/vendor/autoload.php';
$userS3 = new Aws\S3\S3Client([
    'version' => 'latest',
    'region'  => 'us-east-1',
	'credentials' => [
    'key'    => '',
    'secret' => '',
    ],
]);
$bucket = uniqid("jss-userImages",false);
# AWS PHP SDK version 3 create bucket
$result = $userS3->createBucket([
    'ACL' => 'public-read',
    'Bucket' => $bucket,
]);
$result = $userS3->putObject([
    'ACL' => 'public-read',
    'Bucket' => $bucket,
	'Key' => $bucket,
	'SourceFile' => $uploadfile,
]);
$rawurl = $bucket;
$url = $result['ObjectURL'];
echo $url;
$rds = new Aws\Rds\RdsClient([
    'version' => 'latest',
    'region'  => 'us-east-1',
	'credentials' => [
    'key'    => '',
    'secret' => '',
    ],
]);
$result = $rds->describeDBInstances(array(
    'DBInstanceIdentifier' => 'jss-itmo444-db',
));
$endpoint = $result['DBInstances'][0]['Endpoint']['Address'];
    echo "============\n". $endpoint . "================";
$link = mysqli_connect($endpoint,"controller","letmein1234","jssitmo444db") or die("Error" . mysql_error($link));
/*check connection*/
if (mysqli_connect_errno()) {
    printf("Connect failed: %s\n", mysqli_connect_error());
    exit();
}

/*Prepared statement, stage 1: prepare*/
if (!($stmt = $link->prepare("INSERT INTO jssUserImages (userNameTable,userEmailTable,userTelephoneTable,rawS3URLTable,finishedS3URLTable,fileNameTable,stateTable,dateTable) 
	VALUES ('?','?','?','?','?','?','?','?')"))) {
    echo "Prepare failed: (" . $link->errno . ") " . $link->error;
}
else {
	echo "Prepare complete!\n";
}

$name = $_POST['userNameForm'];
$email = $_POST['userEmailForm'];
$phone = $_POST['userTelephoneForm'];
$userS3rawurl = $rawurl;
$filename = basename($_FILES['userFileForm']['name']);
$userS3finishedurl = $url;
$status = 0;
$issubscribed = 0;
mysqli_query($link, "INSERT INTO jssUserImages (idTable,userNameTable,userEmailTable,userTelephoneTable,rawS3URLTable,finishedS3URLTable,fileNameTable,stateTable,dateTable) 
VALUES (NULL,'$name','$email','$phone','$userS3rawurl','$userS3finishedurl','$filename','$status','NULL')");

$stmt->bind_param("sssssii",$name,$email,$phone,$userS3rawurl,$userS3finishedurl,$filename,$status,$issubscribed);
//execution of SQL insert
if (!$stmt->execute()) {
   echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
}
printf("%d Row inserted.\n", $stmt->affected_rows);
/* explicit close recommended */
$stmt->close();
$link->real_query("SELECT * FROM jssUserImages");
$res = $link->use_result();
echo "Result set order...\n";
while ($row = $res->fetch_assoc()) {
    print $row['idTable'] . " " . $row['userEmailTable'] . " " . $row['userTelephoneTable'];
}

//using aws sns
use Aws\Sns\SnsClient;
$sns = new Aws\Sns\SnsClient
([
	'version' => 'latest',
	'region' => 'us-east-1',
	'credentials' => [
    'key'    => '',
    'secret' => '',
    ],
]);

//creating the sns topic
$result = $sns->createTopic([
    'Name' => 'JSSSNS',
]);

$snsARN = $result['TopicArn'];

//subscribing user to sns
$result = $sns->subscribe ([
    'Endpoint' => $email,
    'Protocol' => 'email',
    'TopicArn' => $snsARN,
]);

//push out the subscription
$result = $sns->publish
([
	'Message' => 'Hello! This is an automated message informing you that your file has been uploaded!',
	'Subject' => 'File Uploaded to AWS S3 bucket',    
	'TopicArn' => $snsARN,
]);

//this is for the gallery session page
$_SESSION['gallerySession'] = TRUE;
$link->close();
?>
