<?php
//Begin PHP session
session_start();
require '/vendor/autoload.php';
print $_POST['userEmail'];
$uploaddir = '/tmp/';
$uploadfile = $uploaddir . basename($_FILES['userfile']['name']);
echo '<pre>';
if (move_uploaded_file($_FILES['userfile']['tmp_name'], $uploadfile)) {
    echo "File is valid, and was successfully uploaded.\n";
} else {
    echo "Possible file upload attack!\n";
}
echo 'Here is some more debugging info:';
print_r($_FILES);
echo "</pre>";
use Aws\S3\S3Client;

$s3 = new Aws\S3\S3Client([
    'version' => 'latest',
    'region'  => 'us-east-1'
]);
$bucket = 
uniqid("JSS_ITMO444_PHPFiles",false);
$result = $s3->createBucket([
    'ACL' => 'public-read',
    'Bucket' => $bucket
]);
#Print bucket completion details.
print_r($result);

$client->waitUntilBucketExists(array('Bucket' => $bucket));
$result = $s3->putObject([
    'ACL' => 'public-read',
    'Bucket' => $bucket,
    'Key' => $uploadfile
    'SourceFile' => $uploadfile,
]);  
$url = $result['ObjectURL'];
echo $url;
$rds = new Aws\Rds\RdsClient([
    'version' => 'latest',
    'region'  => 'us-east-1'
]);
$result = $rds->describeDBInstances([
    'DBInstanceIdentifier' => 'JSS_ITMO444_DBMP1',
]);
$endpoint = $result['DBInstances'][0]['Endpoint']['Address']
    print "o0o\n". $endpoint . "o0o";^M
$link = mysqli_connect($endpoint,"controller","letmein","jss_ITMO444_DB"") or die("Error " . mysqli_error($link));
/* check connection */
if (mysqli_connect_errno()) {
    printf("Connect failed: %s\n", mysqli_connect_error());
    exit();
}
/* Prepared statement, stage 1: prepare */
if (!($stmt = $link->prepare("INSERT INTO items (ID, userEmail,userPhone,userFileName,rawS3URL,completeS3URL,completeS3URL,subscriptionStatus) VALUES (NULL,?,?,?,?,?,?,?)"))) {
    echo "Prepare failed: (" . $link->errno . ") " . $link->error;
}
$userEmail = $_POST['userEmail']; //this is from the post at the very top of the code
$userPhone = $_POST['userPhone'];
$rawS3URL = $url; 
$userFileName = basename($_FILES['userFileName']['userName']);
$completeS3URL = "none";
$currentState = 0;
$subscriptionStatus = 0;
mysqli_query($link, 'INSERT INTO userFileDatabase (ID, userName, userPhone, userPhone, raws3URL, completeS3,userFileName, currentState, currentDate) VALUES (NULL, '$userName', '$userEmail', '$userPhone', '$rawS3URL', '$s3finishedURL', '$userFileName', '$s3finishedURL', '$currentState', NULL)");
 
$stmt->bind_param("sssssii",$userPhone,$userPhone,$userFileName,$rawS3URL,$completeS3URL,$currentState,$subscriptionStatus);
if (!$stmt->execute()) {
    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
}
printf("%d Row inserted.\n", $stmt->affected_rows);
$stmt->close();
$link->real_query("SELECT * FROM userFileDatabase");
$res = $link->use_result();
print "Result set order...\n";
while ($row = $res->fetch_assoc()) {
    print $row['ID'] . " " . $row['userEmail']. " " . $row['userPhone'];
}
$link->close();
//add code to detect if subscribed to SNS topic 
//if not subscribed then subscribe the user and UPDATE the column in the database with a new value 0 to 1 so that then each time you dont have to resubscribe them
// add code to generate SQS Message with a value of the ID returned from the most recent inserted piece of work
//  Add code to update database to UPDATE currentState column to 1 (in progress)
header('Location: gallery.php');
?>
