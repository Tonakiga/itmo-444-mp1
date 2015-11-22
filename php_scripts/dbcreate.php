<html>
<head><title>Gallery</title>
</head>
<body>

<?php
session_start();
$email = $_POST["email"];
echo $email;
require 'vendor/autoload.php';

use Aws\Rds\RdsClient;
$client = RdsClient::factory(array(
'region'  => 'us-east-1'
));

$result = $client->describeDBInstances(array(
    'DBInstanceIdentifier' => 'itmo544jrhdb',
));

$endpoint = "";

foreach ($result->getPath('DBInstances/*/Endpoint/Address') as $ep) {
    // Do something with the message
    echo "============". $ep . "================";
    $endpoint = $ep;
}   
//echo "begin database";
$link = mysqli_connect($endpoint,"controller","ilovebunnies","itmo544db") or die("Error " . mysqli_error($link));

/* check connection */
if (mysqli_connect_errno()) {
    printf("Connect failed: %s\n", mysqli_connect_error());
    exit();
}

//below line is unsafe - $email is not checked for SQL injection -- don't do this in real life or use an ORM instead
$link->real_query("SELECT * FROM items WHERE email = '$email'");
//$link->real_query("SELECT * FROM items");
$res = $link->use_result();
echo "Result set order...\n";
while ($row = $res->fetch_assoc()) {
    echo "<img src =\" " . $row['s3rawurl'] . "\" /><img src =\"" .$row['s3finishedurl'] . "\"/>";
echo $row['id'] . "Email: " . $row['email'];
}
$link->close();
?>
</body>
</html>
<?php

echo "begin database";
$link = mysqli_connect("itmo544jrhdb.cpyht2c1c9a4.us-east-1.rds.amazonaws.com","controller","ilovebunnies","itmo544db") or die("Error " . mysqli_error($link));

/* check connection */
if (mysqli_connect_errno()) {
    printf("Connect failed: %s\n", mysqli_connect_error());
    exit();
}


   # id INT NOT NULL AUTO_INCREMENT,
   # name VARCHAR(200) NOT NULL,
   # age INT NOT NULL,

/* Prepared statement, stage 1: prepare */
if (!($stmt = $link->prepare("INSERT INTO student (id, name, age) VALUES (NULL,?,?)"))) {
    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
}

$id = 1;
$name = "joe";
$age = 55;

$stmt->bind_param("si",$name,$age);

if (!$stmt->execute()) {
    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
}

printf("%d Row inserted.\n", $stmt->affected_rows);


/* explicit close recommended */
$stmt->close();

$link->real_query("SELECT * FROM student");
$res = $link->use_result();

echo "Result set order...\n";
while ($row = $res->fetch_assoc()) {
    echo " id = " . $row['id'] . "\n";
}


$link->close();




?>
<?php
/*
 * Copyright 2013. Amazon Web Services, Inc. All Rights Reserved.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
**/

// Include the SDK using the Composer autoloader
require 'vendor/autoload.php';

use Aws\S3\S3Client;

/*
 If you instantiate a new client for Amazon Simple Storage Service (S3) with
 no parameters or configuration, the AWS SDK for PHP will look for access keys
 in the AWS_ACCESS_KEY_ID and AWS_SECRET_KEY environment variables.

 For more information about this interface to Amazon S3, see:
 http://docs.aws.amazon.com/aws-sdk-php-2/guide/latest/service-s3.html#creating-a-client
*/
$client = S3Client::factory();

/*
 Everything uploaded to Amazon S3 must belong to a bucket. These buckets are
 in the global namespace, and must have a unique name.

 For more information about bucket name restrictions, see:
 http://docs.aws.amazon.com/AmazonS3/latest/dev/BucketRestrictions.html
*/
$bucket = uniqid("php-sdk-sample-", true);
echo "Creating bucket named {$bucket}\n";
$result = $client->createBucket(array(
    'Bucket' => $bucket
));

// Wait until the bucket is created
$client->waitUntilBucketExists(array('Bucket' => $bucket));

/*
 Files in Amazon S3 are called "objects" and are stored in buckets. A specific
 object is referred to by its key (i.e., name) and holds data. Here, we create
 a new object with the key "hello_world.txt" and content "Hello World!".

 For a detailed list of putObject's parameters, see:
 http://docs.aws.amazon.com/aws-sdk-php-2/latest/class-Aws.S3.S3Client.html#_putObject
*/
$key = 'hello_world.txt';
echo "Creating a new object with key {$key}\n";
$result = $client->putObject(array(
    'Bucket' => $bucket,
    'Key'    => $key,
    'Body'   => "Hello World!"
));

/*
 Download the object and read the body directly.

 For more examples of downloading objects, see the developer guide:
 http://docs.aws.amazon.com/aws-sdk-php-2/guide/latest/service-s3.html#downloading-objects

 Or the API documentation:
 http://docs.aws.amazon.com/aws-sdk-php-2/latest/class-Aws.S3.S3Client.html#_getObject
*/
echo "Downloading that same object:\n";
$result = $client->getObject(array(
    'Bucket' => $bucket,
    'Key'    => $key
));

echo "\n---BEGIN---\n";
echo $result['Body'];
echo "\n---END---\n\n";

/*
 Buckets cannot be deleted unless they're empty. With the AWS SDK for PHP, you
 have two options:

  - Use the clearBucket helper:
      http://docs.aws.amazon.com/aws-sdk-php-2/latest/class-Aws.S3.S3Client.html#_clearBucket
  - Or individually delete all objects.

 Since this sample created a new unique bucket and uploaded a single object,
 we'll just delete that object.
*/
echo "Deleting object with key {$key}\n";
$result = $client->deleteObject(array(
    'Bucket' => $bucket,
    'Key'    => $key
));

/*
 Now that the bucket is empty, it can be deleted.

 See the API documentation for more information on deleteBucket:
 http://docs.aws.amazon.com/aws-sdk-php-2/latest/class-Aws.S3.S3Client.html#_deleteBucket
*/
echo "Deleting bucket {$bucket}\n";
$result = $client->deleteBucket(array(
    'Bucket' => $bucket
));
<?php

require 'vendor/autoload.php';

use Aws\Rds\RdsClient;
$client = RdsClient::factory(array(
'region'  => 'us-east-1'
));


$result = $client->describeDBInstances(array(
    'DBInstanceIdentifier' => 'itmo544jrhdb',
));


$endpoint = ""; 


foreach ($result->getPath('DBInstances/*/Endpoint/Address') as $ep) {
    // Do something with the message
    echo "============". $ep . "================";
    $endpoint = $ep;
}



echo "begin database";
$link = mysqli_connect($endpoint,"controller","ilovebunnies","itmo544db") or die("Error " . mysqli_error($link));

/* check connection */
if (mysqli_connect_errno()) {
    printf("Connect failed: %s\n", mysqli_connect_error());
    exit();
}
/*
$delete_table = 'DELETE TABLE student';
$del_tbl = $link->query($delete_table);
if ($delete_table) {
        echo "Table student has been deleted";
}
else {
        echo "error!!";

}
*/
$create_table = 'CREATE TABLE IF NOT EXISTS items  
(
    id INT NOT NULL AUTO_INCREMENT,
    email VARCHAR(200) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    filename VARCHAR(255) NOT NULL,
    s3rawurl VARCHAR(255) NOT NULL,
    s3finishedurl VARCHAR(255) NOT NULL,
    status INT NOT NULL,
    issubscribed INT NOT NULL,
    PRIMARY KEY(id)
)';



$create_tbl = $link->query($create_table);
if ($create_table) {
	echo "Table is created or No error returned.";
}
else {
        echo "error!!";  
}
$link->close();
?>
<?php

require 'vendor/autoload.php';

use Aws\Rds\RdsClient;
$client = RdsClient::factory(array(
'region'  => 'us-east-1'
));


$result = $client->describeDBInstances(array(
    'DBInstanceIdentifier' => 'itmo544jrhdb',
));


$endpoint = ""; 


foreach ($result->getPath('DBInstances/*/Endpoint/Address') as $ep) {
    // Do something with the message
    echo "============". $ep . "================";
    $endpoint = $ep;
}



echo "begin database";
$link = mysqli_connect($endpoint,"controller","ilovebunnies","itmo544db") or die("Error " . mysqli_error($link));

/* check connection */
if (mysqli_connect_errno()) {
    printf("Connect failed: %s\n", mysqli_connect_error());
    exit();
}

$delete_table = 'DROP TABLE IF EXISTS items';
$del_tbl = $link->query($delete_table);
if ($delete_table) {
        echo "Table items has been deleted";
}
else {
        echo "error!!";

}

$link->close();
?>
