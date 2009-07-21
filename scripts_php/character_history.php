<?php
	Header("content-type: application/xml");

	printf("<character_history>");

// Parameters for the database connection
include('dbb_params.inc.php');

// Check if the key is a parameter
if( ! isset( $_GET['key'] ) )
	die("Need key parameter");

// Retrieve the key
$apikey = $_GET['key'];

// Connect to the database
$db = mysql_connect($db_host, $db_user, $db_pass)  or die('Erreur de connexion '.mysql_error());
mysql_select_db($db_name,$db)  or die('Erreur de selection '.mysql_error());

// Check if the key already exist in DBB
$sql = "SELECT * FROM character_history WHERE apikey = '$apikey' ";
$req = mysql_query($sql) or die('SQL Error !<br>'.$sql.'<br>'.mysql_error());

if( mysql_num_rows( $req ) < 1 ){
	// No result: the key is not in the database
	
	// Retrieve the xml corresponding to this apikey
	$url = $ryzom_api_base_url."character.php?key=$apikey&part=full";
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_ENCODING , 'gzip');
	$output = curl_exec($ch);
	curl_close($ch);
	$xml = simplexml_load_string($output);
	
	// Check if it's not an error (bad key, partial key instead of full key or something else...)
	if($xml->getName() == 'error') die($xml);
	
	// Retrieve the shardid for this character
	$shardid = substr($xml->shard,0,3);
	// Retrieve the current servertick
	$servertick = file_get_contents($ryzom_api_base_url."time.php?shardid=$shardid");

	// Check if it's a new key for an already tracking character
	$sql = "SELECT * FROM character_history WHERE cid = '".$xml->cid."' ";
	$req = mysql_query($sql) or die('SQL Error !<br>'.$sql.'<br>'.mysql_error());
	
	if( mysql_num_rows( $req ) < 1 ){
		// No result: this a new key for a new character not tracked by the api
		include("character_history_new.inc.php");
	} else {
		// A result: so this is only a new key for an already tracking character
		
		// Update the existing character in the database with this new apikey
		$sql = "UPDATE character_history SET apikey = $apikey WHERE cid = '".$xml->cid."' ";
		$req = mysql_query($sql) or die('SQL Error !<br>'.$sql.'<br>'.mysql_error());
		/** TO DO: maybe add a check of data for this character to see if something change since the last track... **/
	}
	
}

// The database was filled just before or using the CRON mechanism, so just create the xml with all the data
include("character_history_xml.inc.php");

mysql_close();

	printf("</character_history>");

?>