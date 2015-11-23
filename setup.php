<?php
ini_set('display_errors',1); 
 error_reporting(E_ALL);
// Start the session^M
require '/var/www/html/vendor/autoload.php';
$rds = new Aws\Rds\RdsClient([
    'version' => 'latest',
    'region'  => 'us-east-1',
    'credentials' => [
        'key'    => 'AKIAI37TXF3MPNO7NLWQ',
        'secret' => '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',
    ],
]);
$result = $rds->createDBInstance([
    'AllocatedStorage' =>10,
    #'AutoMinorVersionUpgrade' => true || false,
    #'AvailabilityZone' => '<string>',
    #'BackupRetentionPeriod' => <integer>,
	#'CharacterSetName' => '<string>',
	#'CopyTagsToSnapshot' => true || false,
	#'DBClusterIdentifier' => '<string>',
    'DBInstanceClass' => 'db.t1.micro', // REQUIRED
    'DBInstanceIdentifier' => 'jss-itmo444-db', // REQUIRED
    'DBName' => 'jsstestdb',
    #'DBParameterGroupName' => '<string>',
    #'DBSecurityGroups' => ['<string>', ...],
    #'DBSubnetGroupName' => '<string>',
    'Engine' => 'MySQL', // REQUIRED
	#'EngineVersion' => '5.5.41',
    #'Iops' => <integer>,
    #'KmsKeyId' => '<string>',
	#'LicenseModel' => '<string>',
  'MasterUserPassword' => 'letmein1234',
    'MasterUsername' => 'controller',
    #'MultiAZ' => true || false,
    #'OptionGroupName' => '<string>',
    #'Port' => <integer>,
    #'PreferredBackupWindow' => '<string>',
    #'PreferredMaintenanceWindow' => '<string>',
 'PubliclyAccessible' => true,
    #'StorageEncrypted' => true || false,
    #'StorageType' => '<string>',
	#'Tags' => [
	#     [
	#         'Key' => '<string>',
	#         'Value' => '<string>',
	#     ],
        // ...
	# ],
    #'TdeCredentialArn' => '<string>',
    #'TdeCredentialPassword' => '<string>',
	#'VpcSecurityGroupIds' => ['<string>', ...],
]);
print "Create RDS DB results: \n";
# print_r($rds);
$result = $rds->waitUntil('DBInstanceAvailable',['DBInstanceIdentifier' => 'jss-itmo444-db',
]);
// Create a table 
$result = $rds->describeDBInstances([
    'DBInstanceIdentifier' => 'jss-itmo444-db',
]);
$endpoint = $result['DBInstances'][0]['Endpoint']['Address'];
print "============\n". $endpoint . "================\n";
$link = mysqli_connect($endpoint,"controller","letmein1234","jss-itmo444-db") or die("Error " . mysqli_error($link));
#echo "Here is the result: " . $link;

$sql = "DROP TABLE IF EXISTS jssUserImages";
if(!mysqli_query($link, $sql)) {
   echo "Error : " . mysqli_error($link);
} 

$sql = "CREATE TABLE jssUserImages  
(
ID INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
username VARCHAR(20),
useremail VARCHAR(20),
telephone VARCHAR(20),
raws3url VARCHAR(256),
finisheds3url VARCHAR(256),
filename VARCHAR(256),
state TINYINT(3),
datetime timestamp 
)";
?>
