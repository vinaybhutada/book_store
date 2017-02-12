<?php
session_start();
if (!isset($_SESSION['message'])) {
	$_SESSION['message'] = "";
}
?>

<html>
<head>
	<title>Register :: CheapBooks</title>
</head>
<body>
<div align="center">
<h1>Register @ CheapBooks</h1>
<hr><br><br>
<span id="output">
	
</span>
<br><br>
<form action="register.php" name="search" method="POST">
<table id="input_val">
	<tr>
		<td>
			Username : 
		</td>
		<td>
			<input type="text" name="username">
		</td>
	</tr>
	<tr>
		<td>
			Password : 
		</td>
		<td>
			<input type="password" name="password">
		</td>
	</tr>
	<tr>
		<td>
			Address : 
		</td>
		<td>
			<input type="text" name="addr">
		</td>
	</tr>
	<tr>
		<td>
			Phone : 
		</td>
		<td>
			<input type="text" name="phone">
		</td>
	</tr>
	<tr>
		<td>
			Email : 
		</td>
		<td>
			<input type="text" name="email">
		</td>
	</tr>
	<tr>
		<td>
			<input type="submit" name="register" value="Register">
		</td>
	</tr>
</table>
</form>



<?php 
echo "<script>document.getElementById('output').innerHTML = '<b>".$_SESSION['message']."</b>';</script>";

if (isset($_POST['redirect'])) {
	session_destroy();
	header("location:customer.php");
}

if (!empty($_POST['username']) && !empty($_POST['password']) && !empty($_POST['addr']) && !empty($_POST['phone']) && !empty($_POST['email'])) {
	$_SESSION['message'] = "";
	try {
	  $dbh = new PDO("mysql:host=127.0.0.1:3306;dbname=cheapbooks","root","root",array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
	  $dbh->beginTransaction();
	  $sql = $dbh->prepare('insert into customers	(username, password, address, phone, email) values ("'.$_POST['username'].'", "'.md5($_POST['password']).'", "'.$_POST['addr'].'", "'.$_POST['phone'].'", "'.$_POST['email'].'")');
	  $sql->execute();
	  $dbh->commit();
  	  $row_count = $sql->rowCount();
  	  if($row_count == 1){
  	  	echo "<script>document.getElementById('output').innerHTML = '<b>Successfully Registered!</b>';</script>";
  	  	echo "<script>document.getElementById('input_val').innerHTML = '<tr><td> Please go back and log in to the system.</td><td><input type=\"submit\" name=\"redirect\"></td></tr>';</script>";
  	  }

	} catch (PDOException $e) {
	    print "Error!: " . $e->getMessage() . "<br/>";
	    die();
	}
}else{
	$_SESSION['message'] = "Please enter all the details.";
	//header("location:register.php");
}
?>

</body>
</html>