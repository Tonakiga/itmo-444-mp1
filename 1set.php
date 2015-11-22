<?php
//Begin PHP session
require 'vendor/autoload.php';
$rds = new Aws\Rds\RdsClient([
    'version' => 'latest',
    'region'  => 'us-east-1'
]);
$result = $rds->createDBInstance([
    'AllocatedStorage' => 10,
    'DBInstanceClass' => 'db.t1.micro',
    'DBInstanceIdentifier' => 'JSS_ITMO444_DBMP1',
    'DBName' => 'JSS-ITMO444-DB',
    'Engine' => 'MySQL',
    'EngineVersion' => '5.5.41',
	'MasterUserPassword' => 'letmein',
    'MasterUsername' => 'controller',
    'PubliclyAccessible' => true,
]);
print "Echo of AWS RDS DB creation: \n";
$result = $rds->waitUntil('DBInstanceAvailable',['DBInstanceIdentifier' => 'JSS-ITMO444-DB',
]);
//Make the table to hold the files.
$result = $rds->describeDBInstances([
    'DBInstanceIdentifier' => 'JSS_ITMO444_DBMP1',
]);
$endpoint = $result['DBInstances'][0]['Endpoint']['Address'];
print "o0o\n". $endpoint . "o0o\n";
$link = mysqli_connect($endpoint,"controller","letmein","jss_ITMO444_DB") or die("Error " . mysqli_error($link)); 
echo "DB Created: " . $link;
$sql = "CREATE TABLE userFileDatabase 
(
ID INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
userName VARCHAR(32),
userEmail VARCHAR(32),
userPhone VARCHAR(32),
rawS3URL VARCHAR(256),
completeS3URL VARCHAR(256),
fileName VARCHAR(256),
jpgFile VARCHAR(256),
currentState TINYINT(3),
currentDate TIMESTAMP);
)";
$con->query($sql);
mysqli_close($link);
?>
