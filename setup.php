<?php
// Start the session^M
require '/var/www/html/vendor/autoload.php';
$rds = new Aws\Rds\RdsClient([
    'version' => 'latest',
    'region'  => 'us-east-1'
]);
$result = $rds->createDBInstance([
    'AllocatedStorage' => 10,
    #'AutoMinorVersionUpgrade' => true || false,
    #'AvailabilityZone' => '<string>',
    #'BackupRetentionPeriod' => <integer>,
	#'CharacterSetName' => '<string>',
	#'CopyTagsToSnapshot' => true || false,
	#'DBClusterIdentifier' => '<string>',
    'DBInstanceClass' => 'db.t1.micro', // REQUIRED
    'DBInstanceIdentifier' => 'jss-itmo444-mp1', // REQUIRED
    'DBName' => 'jss_itmo444_mp1',
    #'DBParameterGroupName' => '<string>',
    #'DBSecurityGroups' => ['<string>', ...],
    #'DBSubnetGroupName' => '<string>',
    'Engine' => 'MySQL', // REQUIRED
    'EngineVersion' => '5.5.41',
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
	#6'VpcSecurityGroupIds' => ['<string>', ...],
]);
print "Create RDS DB results: \n";
# print_r($rds);
$result = $rds->waitUntil('DBInstanceAvailable',['DBInstanceIdentifier' => 'jss_itmo444_mp1',
]);
// Create a table 
$result = $rds->describeDBInstances([
    'DBInstanceIdentifier' => 'jss_itmo444_mp1',
]);
$endpoint = $result['DBInstances'][0]['Endpoint']['Address'];
print "============\n". $endpoint . "================\n";
$link = mysqli_connect($endpoint,"controller","letmein1234","3306") or die("Error " . mysqli_error($link)); 
echo "Here is the result: " . $link;
$sql = "CREATE TABLE images
(
ID INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
PosterName VARCHAR(32),
Title VARCHAR(32),
Content VARCHAR(500)
)";
$con->query($sql);
?>
