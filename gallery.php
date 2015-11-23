<?php
session_start();
ini_set('display_errors',1); 
error_reporting(E_ALL);
require '/var/www/html/vendor/autoload.php'
$rds = new Aws\Rds\RdsClient([
    'version' => 'latest',
    'region'  => 'us-east-1',
	'credentials' => [
        'key'    => 'AKIAI37TXF3MPNO7NLWQ',
        'secret' => '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',
    ],
]);
$result = $rds->describeDBInstances(array(
    'DBInstanceIdentifier' => 'jss-itmo444-db',	
	'credentials' => [
        'key'    => 'AKIAI37TXF3MPNO7NLWQ',
        'secret' => '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',
    ],
));
$endpoint = $result['DBInstances'][0]['Endpoint']['Address'];
    echo "============\n". $endpoint . "================";
$link = mysqli_connect($endpoint,"controller","letmein1234","jss-itmo444-db");
$link->real_query("SELECT * FROM jssUserImages");
$link->close();
?>
