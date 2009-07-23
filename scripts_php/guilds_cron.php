<?php

// Parameters for the database connection
include('dbb_params.inc.php');

// Connect to the database
$db = mysql_connect($db_host, $db_user, $db_pass)  or die('Erreur de connexion '.mysql_error());
mysql_select_db($db_name,$db)  or die('Erreur de selection '.mysql_error());

// Retrieve all the apikey to be checked
$sqlAPIKEYS = "SELECT apikey FROM guild_history";
$reqAPIKEYS = mysql_query($sqlAPIKEYS) or die('SQL Error !<br>'.$sql.'<br>'.mysql_error());

$apikeyNb = 0;
$apikeyFailed = 0;
$updates = 0;

// For each apikey from the DB, do the following
while($dataAPIKEYS = mysql_fetch_assoc($reqAPIKEYS)) 
{
	$apikeyNb ++;
	// This is the current apikey
	$apikey = $dataAPIKEYS['apikey'];
	// Retrieve the xml guild from official API
	$url = $ryzom_api_base_url."guild.php?key=$apikey";
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_ENCODING , 'gzip');
	$output = curl_exec($ch);
	curl_close($ch);
	$xml = simplexml_load_string($output);
	
	// Check if it's not an error (bad key, partial key instead of full key or something else...)
	if($xml->getName() == 'error'){
		$apikeyFailed ++;	// Just increment the number of failure
		break;
	}
	
	// The guild ID
	$gid = $xml->gid;
	
	// Retrieve the shardid for this guild
	$shardid = substr($xml->shard,0,3);
	// Retrieve the current servertick
	$servertick = file_get_contents($ryzom_api_base_url."time.php?shardid=$shardid");
	
	// update guild_history
	$sql = "UPDATE guild_history SET date_last = $servertick WHERE gid = '".$xml->gid."' ";
	$req = mysql_query($sql) or die('SQL Error !<br>'.$sql.'<br>'.mysql_error());
	
	// guild_info_history
	$nbmembers = 0;
	$result = $xml->xpath('/guild/members/*');
	while(list( , $member) = each($result))
	{
		$nbmembers ++;
	}
	
	$sha1 = sha1($xml->name.$xml->description.$xml->money.$xml->cult.$xml->civ.$xml->building.$xml->motd.$nbmembers);
	$sql = "SELECT sha1(concat(name,description,money,cult,civ,building,motd,members)) AS sha1 FROM guild_info_history WHERE gid = '".$gid."' ORDER BY date DESC LIMIT 1";
	$req = mysql_query($sql) or die('SQL Error !<br>'.$sql.'<br>'.mysql_error());
	$data = mysql_fetch_assoc($req);
	if( $data['sha1'] != $sha1 ){
		$sql = "INSERT INTO guild_info_history(
					gid,
					name,
					description,
					money,
					cult,
					civ,
					building,
					motd,
					members,
					date)
				VALUES(
					'".$xml->gid."',
					'".$xml->name."',
					'".$xml->description."',
					'".$xml->money."',
					'".$xml->cult."',
					'".$xml->civ."',
					'".$xml->building."',
					'".$xml->motd."',
					'".$nbmembers."',
					'".$servertick."')";
		$success = mysql_query($sql);
		$updates ++;
	}
	
	// character_fames_history
	$sql = "SELECT * FROM guild_fames_history WHERE gid = '".$gid."' ORDER BY date DESC LIMIT 1";
	$req = mysql_query($sql) or die('SQL Error !<br>'.$sql.'<br>'.mysql_error());
	$f = mysql_num_fields($req);
	$data = mysql_fetch_array($req);
	$falselvltotalbdd = 0;
	for( $i = 0; $i < $f; $i++ ){
		$tag_name = mysql_field_name($req,$i);
		if( $tag_name != 'gid' && $tag_name != 'date' ){
			$falselvltotalbdd += $data[$i];
		}
	}
	$falselvltotalxml = 0;
	$sql = "INSERT INTO guild_fames_history(gid,";
	$sqlpara = "";
	$sqlvalue = "";
	$result = $xml->xpath('/guild/fames/*');
	while(list( , $item) = each($result))
	{
		$sqlpara .= $item->getName().",";
		$sqlvalue .= "'".$item."',";
		$falselvltotalxml += $item;
	}
	$sql .= $sqlpara . "date) VALUES('".$xml->gid."'," . $sqlvalue . "'".$servertick."')";
	if( $falselvltotalbdd != $falselvltotalxml ){
		$success = mysql_query($sql);
		$updates ++;
	}
	
}

mysql_close();

printf( "NbKey: %d - NbKeyFailled: %d - NbUpdates: %d \n" , $apikeyNb , $apikeyFailed , $updates);

?>