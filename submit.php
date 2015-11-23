<?php
session_start();
ini_set('display_errors',1); 
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
    'region'  => 'us-east-1',
	'credentials' => [
        'key'    => 'AKIAI37TXF3MPNO7NLWQ',
        'secret' => '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',
    ],
]);
$bucket = uniqid("jss-userImages",false);
# AWS PHP SDK version 3 create bucket
$result = $s3->createBucket([
    'ACL' => 'public-read',
    'Bucket' => $bucket,
	'credentials' => [
        'key'    => 'AKIAI37TXF3MPNO7NLWQ',
        'secret' => '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',
    ],
]);
$result = $s3->putObject([
    'ACL' => 'public-read',
    'Bucket' => $bucket,
	'Key' => $bucket,
	'SourceFile' => $uploadfile,
	'credentials' => [
        'key'    => 'AKIAI37TXF3MPNO7NLWQ',
        'secret' => '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',
    ],
]);
$url = $result['ObjectURL'];
echo $url;
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
$link = mysqli_connect($endpoint,"controller","letmein1234","jss-itmo444-db") or die("Error" . mysql_error($link));
?>
