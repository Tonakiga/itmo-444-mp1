<?php session_start(); ?>
<html>
<head><title>JSS File Page</title>
</head>
<body>
<h1>Jean Sebastien Seck File Uploader:</h1>
<!-- Button for DB creation -->
<form enctype="multipart/form-data" action="setup.php" method="POST">
<h2>Click to create DB: </h2><br />
<input type="submit" value="Create Database" />
</form>
<hr />
<form enctype="multipart/form-data" action="createTable.php" method="POST">
<h2>Click to create table in DB: </h2><br />
<input type="submit" value="Create Table in DB" />
</form>
<hr />
<!-- The data encoding type (enctype) MUST be specified -->
<!-- This form captures data and sends it to submit.php -->
<form enctype="multipart/form-data" action="submit.php" method="POST">
<!-- MAX_FILE_SIZE must precede the file input field -->
<input type="hidden" name="MAX_FILE_SIZE" value="3000000" />
<!-- User file name sets name in $_FILES array -->
<h2>File to Upload:</h2><br /> 
<input type="file" name="userFileForm" /><br />
<h3><i>User Name:</i></h3><br />
<input type = "uname" name = "userNameForm"><br />
<h3><i>User Email:</i></h3><br />
<input type="email" name="userEmailForm"><br />
<h3><i>User Phone Number:</i></h3><br />
<h3><i>Ex: [1-800-867-5309]:</i></h3><br />
<input type="phone" name="userTelephoneForm"><br />
<h3><i>Click to Upload File:</i></h3><br />
<input type="submit" value="Send File" />
</form>
<!-- Adds a line to seperate gallery browsing from upload form -->
<hr />
<!-- The data encoding type, enctype, MUST be specified as below -->
<form enctype="multipart/form-data" action="gallery.php" method="POST">
    
Enter User Email to Browse Gallery: <br />
<input type="email" name="userInputEmail">
<input type="submit" value="Load Gallery" />
</form>
</body>
</html>
