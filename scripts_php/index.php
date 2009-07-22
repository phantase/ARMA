<html>
	<head>
		<title>ARMA - Arma Reveals Memory of Atys</title>
	</head>
	<body style="font-family: Verdana;">
		
		<h1 style="background-image:url(arma.png);background-position:left top;background-repeat:no-repeat;height:70px;padding-left:74px;padding-top:10px;">ARMA - Arma Reveals Memory of Atys</h1>
		
<?php

// Parameters for the database connection
include('dbb_params.inc.php');

// Connect to the database
$db = mysql_connect($db_host, $db_user, $db_pass)  or die('Erreur de connexion '.mysql_error());
mysql_select_db($db_name,$db)  or die('Erreur de selection '.mysql_error());

$sql = "SELECT count(*) AS c FROM character_history ";
$req = mysql_query($sql) or die('SQL Error !<br>'.$sql.'<br>'.mysql_error());
$data = mysql_fetch_array($req);
$c = $data['c'];

printf("<h3>Number of registered key</h3> <div style=\"margin-left:200px;\">%s</div>" , $c );

mysql_close();

?>

<h3>Register a new key</h3>
<form action="character_history.php" method="GET" style="margin-left:50px;">
	<input type="text" name="key" size="40" style="border:1px solid black;" />
	<input type="submit" style="border:1px solid black;" />
</form>

	</body>
</html>