<?php
session_start();
if($_SESSION['login'] == true){
	if(empty($_SESSION['cart'])){
    	$_SESSION['cart'] = array();
	}
	if(empty($_SESSION['searchResult'])){
    	$_SESSION['searchResult'] = "";
	}
	if(empty($_SESSION['query'])){
    	$_SESSION['query'] = "";
	}
	if(empty($_SESSION['searchParam'])){
    	$_SESSION['searchParam'] = "";
	}
}else{
	header("location:customer.php");
}
?>

<!DOCTYPE html>
<html>
<head>
	<title>CheapBooks</title>
	<style type="text/css">
		a.button {
    -webkit-appearance: button;
    -moz-appearance: button;
    appearance: button;

    text-decoration: none;
    color: initial;
	}
	</style>
</head>
<body>
<div align="center">
<h1>CheapBooks</h1>
<!-- <a href="http://google.com" class="button">Go to Google</a> -->
<hr><br><br>
<form action="landing_page.php" name="search" method="POST">
<table>
	<tr>
		<span style="align: center;"><h3>Enter you search terms below:</h3><br></span>
		<input type="text" name="search_query" style="width: 50%;">
	</tr>
	<tr>
		<td>
			<input type="submit" name="SearchByAuthor" value="SearchByAuthor">
		</td>
		<td>
			<input type="submit" name="SearchByBookTitle" value="SearchByBookTitle">
		</td>
		<td>
			<input type="submit" name="ShoppingBasket" value="ShoppingBasket">
		</td>
		<td id="cart_counter">
		<i>0 items present in cart.</i>
		</td>
	</tr>
	<tr>
		<td>
			<input type="submit" name="Logout" value="Logout">
		</td>
	</tr>
</table>
</form>

<hr><br><br>

<span id="search_result">
	
</span>

</div>



<?php
Global $dbh;
//Global $total_stock = array();
try {
  $dbh = new PDO("mysql:host=127.0.0.1:3306;dbname=cheapbooks","root","root",array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
} catch (PDOException $e) {
    print "Error!: " . $e->getMessage() . "<br/>";
    die();
}

if(isset($_POST['SearchByAuthor'])){
  searchByAuthor($_POST['search_query']);
} elseif (isset($_POST['SearchByBookTitle'])) {
  searchByBookTitle($_POST['search_query']);
} elseif (isset($_POST['ShoppingBasket'])) {
  redirect();
} elseif (isset($_POST['Logout'])) {
  logout();
}

if(isset($_GET['ISBN']) && isset($_GET['DBQuan']) ){
		addToCart($_GET['ISBN'], $_GET['DBQuan']);
	}

function addToCart($ISBN, $DBQuan){
	//in_array("Irix", $os)
	//array_push($_SESSION['cart'], $query);
	$quantity = 0;
	if(array_key_exists($ISBN, $_SESSION['cart'])){
		$quantity = $_SESSION['cart'][$ISBN];
		if ($quantity < $DBQuan) {
			$quantity += 1;
			$_SESSION['cart'][$ISBN] = $quantity;
		}else{
			echo "Quantity Exceded than available";
		}
	}else{
		$_SESSION['cart'][$ISBN] = 1;
	}

	$count=0;
		foreach ($_SESSION['cart'] as $key => $value) {
			$count += $value;
		}
	echo "<script>document.getElementById('cart_counter').innerHTML = '<i>".$count." items present in cart.</i>';</script>";
	echo "<script>document.getElementById('search_result').innerHTML = '".$_SESSION['searchResult']."';</script>";
}

function searchByAuthor($query){
	Global $dbh;
	$_SESSION['query'] = $query;
	$_SESSION['searchParam'] = "SearchByAuthor";
	try{
		$sql = 'select * from book where author = "'.trim($query).'"';
	  	$result = "<ol>";
	  	foreach ($dbh->query($sql) as $row) {
	  		$result = $result."<li>".$row['title'].", ISBN : ".$row['ISBN'];
			
	  		$sql2 = 'select number, warehousecode from stocks where ISBN = "'.$row['ISBN'].'"';
	  		$row2 = $dbh->query($sql2);
	  		$total_stock = $row2->fetch(PDO::FETCH_ASSOC)['number'];
	  		$warehousecode = $row2->fetch(PDO::FETCH_ASSOC)['warehousecode'];
			
	  		$dbh->beginTransaction();
	  		$sql3 = $dbh->prepare('update stocks set number="'.$total_stock.'" where ISBN = "'.$row['ISBN'].'"');
	  		$sql3->execute();
	  		$dbh->commit();
	  		if ($total_stock > 0) {
	  			$result .= ", Number of books available : ".$total_stock.". <a href=\"landing_page.php?ISBN=".$row['ISBN']."&DBQuan=".$total_stock."\" class=\"button\">Add to Cart</a></li>";
	  		}elseif ($total_stock == null) {
	  			$result = "Not in Stock!";
	  		}
	  	}
	  	$result .= "</ol>";
	  	$_SESSION['searchResult']=$result;
	  	echo "<script>document.getElementById('search_result').innerHTML = '".$result."';</script>";
  	} catch (PDOException $e){
  		echo "<script>document.getElementById('search_result').innerHTML = '<b>No such author! Try again.</b>';</script>";
  		print "Error!: " . $e->getMessage() . "<br/>";
  	}
}

function searchByBookTitle($query){
	Global $dbh;
	$_SESSION['query'] = $query;
	$_SESSION['searchParam'] = "SearchByBookTitle";
	try{
		$sql = 'select * from book where title = "'.trim($query).'"';
	  	$result = "<ol>";
	  	foreach ($dbh->query($sql)  as $row) {
	  		$result = $result."<li>".$row['title'].", ISBN : ".$row['ISBN'];
	  		$sql2 = 'select number, warehousecode  from stocks where ISBN = "'.$row['ISBN'].'"';
	  		$row2 = $dbh->query($sql2);
	  		$total_stock = $row2->fetch(PDO::FETCH_ASSOC)['number'];
			$warehousecode = $row2->fetch(PDO::FETCH_ASSOC)['warehousecode'];
			
	  		$dbh->beginTransaction();
	  		$sql3 = $dbh->prepare('update stocks set number="'.$total_stock.'" where ISBN = "'.$row['ISBN'].'"');
	  		$sql3->execute();
	  		// - 1;
	  		/*

	  		*/
	  		//echo $total_stock;
	  		if ($total_stock > 0) {
	  			$result .= ", Number of books available : ".$total_stock.".<a href=\"landing_page.php?ISBN=".$row['ISBN']."&DBQuan=".$total_stock."\" class=\"button\">Add to Cart</a></li>";
	  		}elseif ($total_stock == null) {
	  			$result .= "Not in Stock";
	  		}
			
	  		
	  	}
	  	$result .= "</ol>";
		$_SESSION['searchResult']=$result;
	  	echo "<script>document.getElementById('search_result').innerHTML = '".$result."';</script>";

  	} catch (PDOException $e){
  		echo "<script>document.getElementById('search_result').innerHTML = '<b>No such Book! Try again.</b>';</script>";
  	}
}

function redirect(){
	header("location:shopping_basket.php");
}

function logout(){
	session_destroy();
	header("location:customer.php");
}

?>
</body>
</html>