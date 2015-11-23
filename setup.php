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
   # 'CharacterSetName' => '<string>',
   # 'CopyTagsToSnapshot' => true || false,
   # 'DBClusterIdentifier' => '<string>',
    'DBInstanceClass' => 'db.t1.micro', // REQUIRED
    'DBInstanceIdentifier' => 'jss-test-db', // REQUIRED
    'DBName' => 'jsstestdb',
    #'DBParameterGroupName' => '<string>',
    #'DBSecurityGroups' => ['<string>', ...],
    #'DBSubnetGroupName' => '<string>',
    'Engine' => 'MySQL', // REQUIRED
   # 'EngineVersion' => '5.5.41',
    #'Iops' => <integer>,
    #'KmsKeyId' => '<string>',
   # 'LicenseModel' => '<string>',
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
   # 'Tags' => [
   #     [
   #         'Key' => '<string>',
   #         'Value' => '<string>',
   #     ],
        // ...
   # ],
    #'TdeCredentialArn' => '<string>',
    #'TdeCredentialPassword' => '<string>',
   # 'VpcSecurityGroupIds' => ['<string>', ...],
]);
print "Create RDS DB results: \n";
# print_r($rds);
$result = $rds->waitUntil('DBInstanceAvailable',['DBInstanceIdentifier' => 'jss-test-db',
]);
// Create a table 
$result = $rds->describeDBInstances([
    'DBInstanceIdentifier' => 'jss-test-db',
]);
$endpoint = $result['DBInstances'][0]['Endpoint']['Address'];
print "============\n". $endpoint . "================\n";
$link = mysqli_connect($endpoint,"controller","letmein1234","jsstestdb") or die("Error " . mysqli_error($link));
#echo "Here is the result: " . $link;
$sql = "CREATE TABLE IF NOT EXISTS items  
(
    id INT NOT NULL AUTO_INCREMENT,
    uname VARCHAR(20) NOT NULL,
    email VARCHAR(20) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    s3rawurl VARCHAR(256) NOT NULL,
    s3finishedurl VARCHAR(256) NOT NULL,
    jpgfilename VARCHAR(255) NOT NULL,
    state TINYINT(3) NOT NULL,
    datecolumn TIMESTAMP DEFAULT CURRENT_TIMESTAMP, 
    PRIMARY KEY(id)
)";
$link->query($sql);
?>
