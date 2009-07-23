<?php

	/*******************************/
	
	// Parameters for the database connection
	include('dbb_params.inc.php');
	
	$db_table = "guilds_list";
	
	if( ! isset($_GET['shardid']) )
		die("No shardid provided");
	$shardid = $_GET['shardid'];
	
	/*******************************/
	
	$servertick = file_get_contents($ryzom_api_base_url."time.php?shardid=$shardid");
	
	$url = $ryzom_api_base_url."guilds.php?shardid=$shardid";
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_ENCODING , 'gzip');
	$output = curl_exec($ch);
	curl_close($ch);
	$xml = simplexml_load_string($output);
	
	$db = mysql_connect($db_host, $db_user, $db_pass)  or die('Erreur de connexion '.mysql_error());
	mysql_select_db($db_name,$db)  or die('Erreur de selection '.mysql_error());
	
	$sqlUPDATE = "UPDATE $db_table SET deleted = 1 WHERE shardid = '$shardid'";
	mysql_query($sqlUPDATE);
	
	$newguilds = 0;
	
	// Existing guilds
	
	$result = $xml->xpath('/guilds/*');
	while(list( , $guild) = each($result))
	{
		if( $guild->name != "" ){
			
			$sqlINSERT = "INSERT INTO $db_table(gid, shardid, name, race, icon, creation_date, description) VALUES(".$guild->gid.",'$shardid', '".addslashes($guild->name)."', '".$guild->race."', ".$guild->icon.", ".$guild->creation_date.", '".addslashes($guild->description)."' )";
			$success = mysql_query($sqlINSERT); 
			
			$newguilds ++;
			
			if( ! $success ){
				// Ok, the guild always exists, so change the deleted flag for this guild to 0
				$sqlUPDATE = "UPDATE $db_table SET deleted = 0 WHERE gid = ".$guild->gid;
				mysql_query($sqlUPDATE);
				
				$newguilds --;
				
			}
		}
	}
	
	// Deleted guilds
	
	$sqlUPDATE = "UPDATE $db_table SET deletion_date = $servertick WHERE deleted = 1 AND deletion_date = 0 ";
	mysql_query($sqlUPDATE);
	
	echo "Added: " . $newguilds . " guilds - Deleted: " . mysql_affected_rows() . " guilds";
	
	mysql_close();
	
?>