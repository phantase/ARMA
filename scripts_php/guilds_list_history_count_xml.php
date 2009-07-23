<?php

	/*******************************/
	
	Header("content-type: application/xml");
	
	// Parameters for the database connection
	include('dbb_params.inc.php');
	
	$db_table = "guilds_list";
	
	if( ! isset($_GET['shardid']) )
		die("<guilds_count>No shardid provided</guilds_count>");
	$shardid = $_GET['shardid'];

	
	/*******************************/

	echo "<guilds_count>";
	
	$db = mysql_connect($db_host, $db_user, $db_pass)  or die('Erreur de connexion '.mysql_error());
	mysql_select_db($db_name,$db)  or die('Erreur de selection '.mysql_error());

	$sqlSELECTcd = "SELECT race, creation_date, count(*) AS c FROM  $db_table WHERE shardid='$shardid' AND deleted=0 GROUP BY race, creation_date ORDER BY creation_date ASC";
	$reqcd = mysql_query($sqlSELECTcd) or die('Erreur SQL !<br>'.$sql.'<br>'.mysql_error()); 
	
	$counts = array( 'fyros'=>0, 'matis'=>0, 'tryker'=>0, 'zorai'=>0 );

	$crea_date = 0;
	
	while($datacd = mysql_fetch_assoc($reqcd)) 
	{
		if( $datacd['race'] == "Fyros" ){
			$counts['fyros'] = $counts['fyros'] + $datacd['c'];
		} else if( $datacd['race'] == "Matis" ){
			$counts['matis'] = $counts['matis'] + $datacd['c'];
		} else if( $datacd['race'] == "Tryker" ){
			$counts['tryker'] = $counts['tryker'] + $datacd['c'];
		} else if( $datacd['race'] == "Zorai" ){
			$counts['zorai'] = $counts['zorai'] + $datacd['c'];
		}
		
		if( $crea_date != $datacd['creation_date'] && $crea_date != 0 ){
			echo "<count>";
			echo "<date>".$crea_date."</date>";
			echo "<fyros>".$counts['fyros']."</fyros>";
			echo "<matis>".$counts['matis']."</matis>";
			echo "<tryker>".$counts['tryker']."</tryker>";
			echo "<zorai>".$counts['zorai']."</zorai>";
			echo "</count>\n";
		}
		
		$crea_date = $datacd['creation_date'];
		
	}
	
	if( $crea_date != 0 ){
		echo "<count>";
		echo "<date>".$crea_date."</date>";
		echo "<fyros>".$counts['fyros']."</fyros>";
		echo "<matis>".$counts['matis']."</matis>";
		echo "<tryker>".$counts['tryker']."</tryker>";
		echo "<zorai>".$counts['zorai']."</zorai>";
		echo "</count>\n";
	}
		
	mysql_close();
	
	echo "</guilds_count>";
	
?>