<?php
session_start();
var_dump($_POST);
if(!empty($_POST)){
echo $_POST['useremail'];
echo $_POST['telephone'];
echo $_POST['username'];
$_SESSION['username']=$_POST['username'];
$_SESSION['telephone']=$_POST['telephone'];
$_SESSION['useremail']=$_POST['useremail'];
}
else
{
echo "post empty";
}
date_default_timezone_set('America/Chicago');
$uploaddir = '/tmp/';
$uploadfile = $uploaddir . basename($_FILES['userfile']['name']);
print '<pre>';
if (move_uploaded_file($_FILES['userfile']['tmp_name'], $uploadfile)) {
  echo "File is valid, and was successfully uploaded.\n";
}
else {
    echo "Possible file upload attack!\n";
}
echo 'Here is some more debugging info:';
print_r($_FILES);
print "</pre>";
  
require '/var/www/html/vendor/autoload.php';
$s3 = new Aws\S3\S3Client([
    'version' => 'latest',
    'region'  => 'us-east-1'
]);
$bucket = uniqid("jss-php",false);
# AWS PHP SDK version 3 create bucket
$result = $s3->createBucket([
    'ACL' => 'public-read',
    'Bucket' => $bucket
]);
$s3->waitUntil('BucketExists',[
	'Bucket' => $bucket
]);
$result = $s3->putObject([
    'ACL' => 'public-read',
    'Bucket' => $bucket,
   'Key' => $uploadfile,
   'ContentType' => $FILES['userfile']['type'],
   'Body' =>fopen($uploadfile)
]);
$url = $result['ObjectURL'];
echo $url;
$rds = new Aws\Rds\RdsClient([
    'version' => 'latest',
    'region'  => 'us-east-1'
]);
$result = $rds->describeDBInstances(array(
    'DBInstanceIdentifier' => 'jss-db'
   
));
$endpoint = $result['DBInstances'][0]['Endpoint']['Address'];
    echo "============\n". $endpoint . "================";
$link = mysqli_connect($endpoint,"controller","letmein1234","jssdb") or die("Error" . mysql_error($link));
if (mysqli_connect_errno()) { 
    printf("Connect failed: %s\n", mysqli_connect_error()); 
    exit(); 
} 
else { 
echo "Success"; 
} 
if (!($stmt = $link->prepare("INSERT INTO userImages (username,useremail,telephone,raws3url,finisheds3url,filename,state) VALUES (?,?,?,?,?,?,?)"))) {
    echo "Prepare failed: (" . $link->errno . ") " . $link->error;
}
$username=$_POST['username'];
$useremail = $_POST['useremail'];
$telephone = $_POST['telephone'];
$raws3url = $url; 
$filename = basename($_FILES['userfile']['name']);
$finisheds3url = "none";
$state=0;
$stmt->bind_param("ssssssi",$username,$useremail,$telephone,$raws3url,$finisheds3url,$filename,$state);
if (!$stmt->execute()) {
    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
}
printf("%d Row inserted.\n", $stmt->affected_rows);
$stmt->close();
$publish = $result->publish(array(
    'TopicArn' => $topicARN,
    // Message is required
    'Subject' => 'Test',
    'Message' => 'Image Uploaded',
    
    
));
$link->real_query("SELECT * FROM userImages");
$res = $link->use_result();
echo "Result set order...\n";
while ($row = $res->fetch_assoc()) {
    echo $row['id'] . " " . $row['username'] . " " . $row['useremail']. " " . $row['telephone'];
}
$link->close();
header("Location: gallery.php");
?> 
