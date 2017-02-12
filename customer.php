<?php 
session_start();
 ?>
<html>
<head><title>CheapBooks</title></head>
<body>
<!--

Student : Vinay Bhutada, 1001385654

 -->

<div align="center">
<h1> Welcome! Please login.</h1>
<hr>
<form action="customer.php" name="login" method="POST">
<table>
  <tr>
    <td>
      Username : 
    </td>
    <td>
      <input type="text" name="user">
    </td>
  </tr>
  <tr>
    <td>
      Password : 
    </td>
    <td>
      <input type="password" name="pass">
    </td>
  </tr>
  <tr>
    <td>
      <input type="submit" name="Login" value="Login">
    </td>
    <td>
      <input type="submit" name="New_User" value="New users must register here">
    </td>
  </tr>
</table>
</form>
</div>
<div align="center" id="message">
  
</div>

<?php
error_reporting(E_ALL);
ini_set('display_errors','On');

function login()
{

  try {
  $dbh = new PDO("mysql:host=127.0.0.1:3306;dbname=cheapbooks","root","root",array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
  $username = $_POST["user"];
  $password = md5($_POST["pass"]);
  $var = $dbh->prepare('select * from Customers where username="'.$username.'" and password = "'.$password.'"');
  $var->execute();
  $row_count = $var->rowCount();
  if ($row_count == 1) {
    $_SESSION['login'] = true;
    $_SESSION['userName'] = $_POST["user"];
    header("location:landing_page.php"); 
  } else {
    echo "<script>document.getElementById('message').innerHTML = '<b><i>Invalid Credentials. Please enter correct values</i></b>';</script>";  
  }
  } catch (PDOException $e) {
    print "Error!: " . $e->getMessage() . "<br/>";
    die();
  }
  
}

function redirect(){
  header("location:register.php");
}

if(isset($_POST['Login']))
{
  login();
} elseif (isset($_POST['New_User'])) {
  redirect();
}

?>

<?php
error_reporting(E_ALL);
ini_set('display_errors','On');
?>
</body>
</html>
