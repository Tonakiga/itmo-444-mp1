<html>
<head><title>JSS Gallery</title>
</head>
<body>

<?php
session_start();
ini_set('display_errors',1); 
$userEmail = $_POST['userInputEmail'];
echo $userEmail;
require '/var/www/html/vendor/autoload.php';

use Aws\Rds\RdsClient;
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
// Do something with the message
echo "============". $endpoint . "================ <br />";

//echo "begin database";
$link = mysqli_connect($endpoint,"controller","letmein1234","jssitmo444db") or die("Error " . mysqli_error($link));

/* check connection */
if (mysqli_connect_errno()) {
    printf("Connect failed: %s\n", mysqli_connect_error());
    exit();
}

//below line is unsafe - $userEmail is not checked for SQL injection -- don't do this in real life or use an ORM instead
$link->real_query("SELECT * FROM jssUserImages WHERE userEmailTable = '$userEmail'");
//$link->real_query("SELECT * FROM jssUserImages");
$res = $link->use_result();
echo "Result set order... <br />";

//if the session is valid it uses CSS to show a red header, if not CSS shows a blue header. If the session never occurs it shows a blank page.
while ($row = $res->fetch_assoc()) {
	if($_SESSION['gallerySession']){
		echo "<h1 style=color:red;margin-left:50px;>Finished URL Image: </h1><br />";
		echo "<img src =\" " . $row['finishedS3URLTable'] . "\" /> <br />";
	}
	else {
		echo "<h1 style=color:blue;margin-left:50px;>Unfinished URL Image: </h1><br />";
		echo $row['rawS3URLTable'];
		echo "<img src =\" " . $row['rawS3URLTable'] . "\" /> <br />";
	}
}

/*image-magick stuff
$imagemagick = new Imagick($finishedS3URLTable);
$imagemagick->adaptiveBlurImage(5,3);
echo $imagemagick;*/

$link->close();
?>
</body>
</html>
