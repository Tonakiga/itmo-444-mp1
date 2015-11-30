<?php
ini_set('display_errors',1); 
error_reporting(E_ALL);
// Start the session^M
require '/var/www/html/vendor/autoload.php';
$rds = new Aws\Rds\RdsClient([
    'version' => 'latest',
    'region'  => 'us-east-1',
	'credentials' => [
    'key'    => '',
    'secret' => '',
    ],
]);
// Create a table 
$result = $rds->describeDBInstances([
    'DBInstanceIdentifier' => 'jss-itmo444-db',
]);
$endpoint = $result['DBInstances'][0]['Endpoint']['Address'];
print "============\r\n". $endpoint . "================\r\n";
$link = new mysqli($endpoint,'controller','letmein1234','jssitmo444db');
// Check connection
if ($link->connect_error) {
    die("Connection failed: " . $link->connect_error);
} 
echo "Connected successfully\r\n";

#echo "Here is the result: " . $link;

$sql = "DROP TABLE IF EXISTS jssUserImages";
if(!mysqli_query($link, $sql)) {
   echo "Error : " . mysqli_error($link);
} 

$sql = "CREATE TABLE jssUserImages  
(
idTable INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
userNameTable VARCHAR(20),
userEmailTable VARCHAR(20),
userTelephoneTable VARCHAR(20),
rawS3URLTable VARCHAR(256),
finishedS3URLTable VARCHAR(256),
fileNameTable VARCHAR(256),
stateTable TINYINT(3),
dateTable timestamp 
)";

$linkValue = $link->query($sql);
if($linkValue === TRUE) {
print "Created jssUserImages!\r\n";
} else {
print "Table jssUserImages unable to be created!\r\n";
}
$link->close();
?>
