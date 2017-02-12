<?php
session_start();
if($_SESSION['login'] != true){
header("location:customer.php");
}
if (!isset($_SESSION['msg'])) {
 	$_SESSION['msg'] = "";
 } 
?>
<!DOCTYPE html>
<html>
<head>
	<title>Shopping Basket :: CheapBooks</title>
</head>
<body>
<div align="center">
<h1>CheapBooks :: Shopping Basket</h1>
<h3> Welcome <?php echo $_SESSION["userName"]; ?></h3>
<hr><br><br>
<table>
	<tr>
		<th>
			Number
		</th>
		<th>
			Book
		</th>
		<th>
			ISBN
		</th>
		<th>
			Quantity
		</th>
		<th>
			Price
		</th>
	</tr>
<?php 
Global $dbh;
if(!empty($_SESSION['cart'])){
	try {
  		$dbh = new PDO("mysql:host=127.0.0.1:3306;dbname=cheapbooks","root","root",array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
	} catch (PDOException $e) {
    	print "Error!: " . $e->getMessage() . "<br/>";
    	die();
	}
	$index = 1;
	$total_amount = 0;
	foreach ($_SESSION['cart'] as $key => $value) {
		$sql = "select * from book where ISBN = '".trim($key)."'";
		$row = $dbh->query($sql);
		$res = $row->fetch(PDO::FETCH_ASSOC);
		//echo $res['title']."\n".$res['price'];
		$title = $res['title'];
		$price = $res['price'];
		echo "<tr><td> ".$index." </td> 
				<td>".$title."</td>
				<td>".$key."</td>
				<td>".$value."</td>
				<td>".$price."</td></tr>";
		$total_amount += intval($price)*$value;
		$index += 1;
	}
	echo "<tr><td>Total Amount : </td><td>".$total_amount."</td><td><form action='shopping_basket.php' method='post'><input type='submit' name='buy' value='buy'></td></tr>";

	if (isset($_POST['buy'])) {
		buy();
	}
}else{
	echo "Cart is Empty.";
	echo $_SESSION['msg'];
}


function buy(){
	Global $dbh;
			$query = "select max(basketId) as mbasketId from shoppingbasket";
			$row_fetch = $dbh->query($query);
			$basketId = $row_fetch->fetch(PDO::FETCH_ASSOC)['mbasketId'];
		if($basketId == null){
			echo "null";
			$basketId = 1;
			$sql = "insert into shoppingbasket(basketId, username) values ('".$basketId."','".$_SESSION['userName']."')";
			$row = $dbh->query($sql);

			foreach ($_SESSION['cart'] as $key => $value) {
				$sql2 = "insert into contains(ISBN, basketId, number) values('".$key."','".$basketId."','".$value."')";
				$row = $dbh->query($sql2);
				
				$sql3 = "select * from stocks where ISBN = '".$key."'";
				$row3 = $dbh->query($sql3);
				$res3 = $row3->fetch(PDO::FETCH_ASSOC);
				$warehousecode = $res3['warehousecode'];
				$total_stock = intval($res3['number']) - $value;
				$sql4 = "update stocks set number='".$total_stock."' where ISBN = '".$key."'";
				$row4 = $dbh->query($sql4);

				$sql5 = "insert into shippingorder(ISBN, warehousecode, username, number) values ('".$key."', '".$warehousecode."'
				, '".$_SESSION['userName']."', '".$value."')";
				$row5 = $dbh->query($sql5);
				$_SESSION['msg'] = "Sucessfully Purchased the Items.";
				unset($_SESSION['cart']);
				header("location:shopping_basket.php");
			}
			
		}else{
			
			echo "string";
			$sql = "insert into shoppingbasket(basketId, username) values ('".(intval($basketId) + 1)."','".$_SESSION['userName']."')";
			$row = $dbh->query($sql);

			foreach ($_SESSION['cart'] as $key => $value) {
				$sql2 = "insert into contains(ISBN, basketId, number) values('".$key."','".$basketId."','".$value."')";
				$row = $dbh->query($sql2);
				
				$sql3 = "select * from stocks where ISBN = '".$key."'";
				$row3 = $dbh->query($sql3);
				$res3 = $row3->fetch(PDO::FETCH_ASSOC);
				$warehousecode = $res3['warehousecode'];
				$total_stock = intval($res3['number']) - $value;
				$sql4 = "update stocks set number='".$total_stock."' where ISBN = '".$key."'";
				$row4 = $dbh->query($sql4);

				$sql5 = "insert into shippingorder(ISBN, warehousecode, username, number) values ('".$key."', '".$warehousecode."'
				, '".$_SESSION['userName']."', '".$value."')";
				$row5 = $dbh->query($sql5);
				$_SESSION['msg'] = "Sucessfully Purchased the Items.";
				unset($_SESSION['cart']);
				header("location:shopping_basket.php");

			}

		}
}
?>
</table>
</div>
</body>
</html>
