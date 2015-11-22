<?php session_start(); ?>
<html>
<head><title>JSS File Page</title>
</head>
<body>
Jean Sebastien Seck File Uploader:
<!-- Button for DB creation -->
<form enctype="multipart/form-data" action="setup.php" method="POST">
Click to create DB: <br />
<input type="submit" value="Create Database" />
</form>
<hr />
<!-- The data encoding type (enctype) MUST be specified -->
<!-- This form captures data and sends it to submit.php -->
<form enctype="multipart/form-data" action="submit.php" method="POST">
<!-- MAX_FILE_SIZE must precede the file input field -->
<input type="hidden" name="MAX_FILE_SIZE" value="3000000" />
<!-- User file name sets name in $_FILES array -->
File to Upload:<br /> 
<input name="userfile" type="file" /><br />
User Name: <br />
<input type = "uname" name = "username"><br />
User Email: <br />
<input type="email" name="useremail"><br />
User Phone Number: <br />
Ex: [1-800-867-5309]: <br />
<input type="phone" name="phone"><br />
Click to Upload File: <br />
<input type="submit" value="Send File" />
</form>
<!-- Adds a line to seperate gallery browsing from upload form -->
<hr />
<!-- The data encoding type, enctype, MUST be specified as below -->
<form enctype="multipart/form-data" action="gallery.php" method="POST">
    
Enter User Email to Browse Gallery: <br />
<input type="email" name="email">
<input type="submit" value="Load Gallery" />
</form>
</body>
</html>
