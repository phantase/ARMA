<?php

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
		
		// Add the xml to the database as a new character
		
		// character_history
		$sql = "INSERT INTO character_history(
					cid, 
					uid, 
					slot, 
					shard, 
					race, 
					gender, 
					apikey,
					date) 
				VALUES(
					'".$xml->cid."',
					'".$xml->uid."',
					'".$xml->slot."',
					'".$xml->shard."',
					'".$xml->race."',
					'".$xml->gender."',
					'".$apikey."',
					'".$servertick."')";
		$success = mysql_query($sql);
		//echo $sql."<br/>\n";
		
		// character_info_history
		$sha1 = sha1($xml->name.$xml->titleid.$xml->played_time.$xml->money.$xml->cult.$xml->civ.$xml->building.$xml->guild->gid);
		$sql = "INSERT INTO character_info_history(
					cid,
					name,
					titleid,
					played_time,
					money,
					cult,
					civ,
					building,
					guild_gid,
					sha1,
					date)
				VALUES(
					'".$xml->cid."',
					'".$xml->name."',
					'".$xml->titleid."',
					'".$xml->played_time."',
					'".$xml->money."',
					'".$xml->cult."',
					'".$xml->civ."',
					'".$xml->building."',
					'".$xml->guild->gid."',
					'".$sha1."',
					'".$servertick."')";
		$success = mysql_query($sql);
		//echo $sql."<br/>\n";
		
		// character_phys_caracs_history
		$sha1 = sha1($xml->phys_characs->constitution.$xml->phys_characs->metabolism.$xml->phys_characs->intelligence.$xml->phys_characs->wisdom.$xml->phys_characs->strength.$xml->phys_characs->wellbalanced.$xml->phys_characs->dexterity.$xml->phys_characs->will);
		$sql = "INSERT INTO character_phys_caracs_history(
					cid,
					constitution,
					metabolism,
					intelligence,
					wisdom,
					strength,
					wellbalanced,
					dexterity,
					will,
					sha1,
					date)
				VALUES(
					'".$xml->cid."',
					'".$xml->phys_characs->constitution."',
					'".$xml->phys_characs->metabolism."',
					'".$xml->phys_characs->intelligence."',
					'".$xml->phys_characs->wisdom."',
					'".$xml->phys_characs->strength."',
					'".$xml->phys_characs->wellbalanced."',
					'".$xml->phys_characs->dexterity."',
					'".$xml->phys_characs->will."',
					'".$sha1."',
					'".$servertick."')";
		$success = mysql_query($sql);
		//echo $sql."<br/>\n";
		
		// character_phys_scores_history
		$sha1 = sha1($xml->phys_scores->hitpoints.$xml->phys_scores->hitpoints['max'].$xml->phys_scores->stamina.$xml->phys_scores->stamina['max'].$xml->phys_scores->sap.$xml->phys_scores->sap['max'].$xml->phys_scores->focus.$xml->phys_scores->focus['max']);
		$sql = "INSERT INTO character_phys_scores_history(
					cid,
					hitpoints,
					hitpoints_max,
					stamina,
					stamina_max,
					sap,
					sap_max,
					focus,
					focus_max,
					sha1,
					date)
				VALUES(
					'".$xml->cid."',
					'".$xml->phys_scores->hitpoints."',
					'".$xml->phys_scores->hitpoints['max']."',
					'".$xml->phys_scores->stamina."',
					'".$xml->phys_scores->stamina['max']."',
					'".$xml->phys_scores->sap."',
					'".$xml->phys_scores->sap['max']."',
					'".$xml->phys_scores->focus."',
					'".$xml->phys_scores->focus['max']."',
					'".$sha1."',
					'".$servertick."')";
		$success = mysql_query($sql);
		//echo $sql."<br/>\n";
		
		// character_equiphands_history
		$sql = "INSERT INTO character_equiphands_history(
					cid,";
		$sql2 = "'".$xml->cid."',";
		
		// equipments/*
		$sha1txt = "";
		$result = $xml->xpath('/character/equipments/*');
		while(list( , $item) = each($result))
		{
			$sha1txt .= $item['part'].$item."#".$item['c']."#".$item['q'];
			$sql .= "	equipments_".$item['part'].",";
			$sql2 .= "'".$item."#".$item['c']."#".$item['q']."',";
		}
		// hands/*
		$result = $xml->xpath('/character/hands/*');
		while(list( , $item) = each($result))
		{
			$sha1txt .= $item['part'].$item."#".$item['q']."#".$item['sap'];
			$sql .= "	hands_".$item['part'].",";
			$sql2 .= "'".$item."#".$item['q']."#".$item['sap']."',";
		}
		$sha1 = sha1($sha1txt);
		$sql .= "	sha1,date) 
				VALUES(";
		$sql2 .= "	'".$sha1."','".$servertick."')";
		
		$sql .= $sql2;
		
		$success = mysql_query($sql);
		//echo $sql."<br/>\n";
		
		// character_faction_points_history
		$sha1 = sha1($xml->faction_points->kami.$xml->faction_points->karavan.$xml->faction_points->fyros.$xml->faction_points->matis.$xml->faction_points->tryker.$xml->faction_points->zorai);
		$sql = "INSERT INTO character_faction_points_history(
					cid,
					kami,
					karavan,
					fyros,
					matis,
					tryker,
					zorai,
					sha1,
					date)
				VALUES(
					'".$xml->cid."',
					'".$xml->faction_points->kami."',
					'".$xml->faction_points->karavan."',
					'".$xml->faction_points->fyros."',
					'".$xml->faction_points->matis."',
					'".$xml->faction_points->tryker."',
					'".$xml->faction_points->zorai."',
					'".$sha1."',
					'".$servertick."')";
		$success = mysql_query($sql);
		//echo $sql."<br/>\n";
		
		// character_fames_history
		$result = $xml->xpath('/character/fames/*');
		while(list( , $item) = each($result))
		{
			$sql = "INSERT INTO character_fames_history(
						cid,
						faction,
						value,
						date)
					VALUES(
						'".$xml->cid."',";
			$sql .= "	'".$item->getName()."',";
			$sql .= "	'".$item."',";
			$sql .= "	'".$servertick."')";
			$success = mysql_query($sql);
			//echo $sql."<br/>\n";
		}
		
		// character_skills_history
		$result = $xml->xpath('/character/skills/*');
		while(list( , $item) = each($result))
		{
			$sql = "INSERT INTO character_skills_history(
						cid,
						skill,
						value,
						date)
					VALUES(
						'".$xml->cid."',";
			$sql .= "	'".$item->getName()."',";
			$sql .= "	'".$item."',";
			$sql .= "	'".$servertick."')";
			$success = mysql_query($sql);
			//echo $sql."<br/>\n";
		}
		
		// character_pets_history
		$petsheet = array( '','','','' );
		$result = $xml->xpath('/character/pets/*');
		while(list( , $item) = each($result))
		{
			if( $item['id'] == 0 ){
				$sql0 = "'".$item['sheet']."','".$item['price']."','".$item['satiety']."','".$item['status']."','".$item['stable']."'";
			} else if( $item['id'] == 1 ){
				$sql1 = "'".$item['sheet']."','".$item['price']."','".$item['satiety']."','".$item['status']."','".$item['stable']."'";
			} else if( $item['id'] == 2 ){
				$sql2 = "'".$item['sheet']."','".$item['price']."','".$item['satiety']."','".$item['status']."','".$item['stable']."'";
			} else if( $item['id'] == 3 ){
				$sql3 = "'".$item['sheet']."','".$item['price']."','".$item['satiety']."','".$item['status']."','".$item['stable']."'";
			}
		}
		$sha1 = sha1( $sql0.$sql1.$sql2.$sql3 );
		$sql = "INSERT INTO character_pets_history(
					cid,
					pet0_sheet,
					pet0_price,
					pet0_satiety,
					pet0_status,
					pet0_stable,
					pet1_sheet,
					pet1_price,
					pet1_satiety,
					pet1_status,
					pet1_stable,
					pet2_sheet,
					pet2_price,
					pet2_satiety,
					pet2_status,
					pet2_stable,
					pet3_sheet,
					pet3_price,
					pet3_satiety,
					pet3_status,
					pet3_stable,
					sha1,
					date)
				VALUES(
					'".$xml->cid."',
					".$sql0.",
					".$sql1.",
					".$sql2.",
					".$sql3.",
					'".$sha1."',
					'".$servertick."')";
		$success = mysql_query($sql);
		//echo $sql."<br/>\n";
		
	} else {
		// A result: so this is only a new key for an already tracking character
		
		// Update the existing character in the database with this new apikey
		$sql = "UPDATE character_history SET apikey = $apikey WHERE cid = '".$xml->cid."' ";
		$req = mysql_query($sql) or die('SQL Error !<br>'.$sql.'<br>'.mysql_error());
		/** TO DO: maybe add a check of data for this character to see if something change since the last track... **/
	}
	
}

// The database was filled just before or using the CRON mechanism, so just create the xml with all the data
echo "SOON I WILL RETURN SOMETHING...";

mysql_close();

?>