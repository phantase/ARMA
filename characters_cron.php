<?php

// Parameters for the database connection
include('dbb_params.inc.php');

// Connect to the database
$db = mysql_connect($db_host, $db_user, $db_pass)  or die('Erreur de connexion '.mysql_error());
mysql_select_db($db_name,$db)  or die('Erreur de selection '.mysql_error());

// Retrieve all the apikey to be checked
$sqlAPIKEYS = "SELECT apikey FROM character_history";
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
	// Retrieve the xml character from official API
	$url = $ryzom_api_base_url."character.php?key=$apikey&part=full";
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
	
	// The character ID
	$cid = $xml->cid;
	
	// Retrieve the shardid for this character
	$shardid = substr($xml->shard,0,3);
	// Retrieve the current servertick
	$servertick = file_get_contents($ryzom_api_base_url."time.php?shardid=$shardid");
	
	// character_info_history
	$sha1 = sha1($xml->name.$xml->titleid.$xml->played_time.$xml->money.$xml->cult.$xml->civ.$xml->building.$xml->guild->gid);
	$sql = "SELECT sha1 FROM character_info_history WHERE cid = '".$cid."' ORDER BY date DESC LIMIT 1";
	$req = mysql_query($sql) or die('SQL Error !<br>'.$sql.'<br>'.mysql_error());
	$data = mysql_fetch_assoc($req);
	if( $data['sha1'] != $sha1 ){
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
		$updates ++;
	}
	
	// character_phys_caracs_history
	$sha1 = sha1($xml->phys_characs->constitution.$xml->phys_characs->metabolism.$xml->phys_characs->intelligence.$xml->phys_characs->wisdom.$xml->phys_characs->strength.$xml->phys_characs->wellbalanced.$xml->phys_characs->dexterity.$xml->phys_characs->will);
	$sql = "SELECT sha1 FROM character_phys_caracs_history WHERE cid = '".$cid."' ORDER BY date DESC LIMIT 1";
	$req = mysql_query($sql) or die('SQL Error !<br>'.$sql.'<br>'.mysql_error());
	$data = mysql_fetch_assoc($req);
	if( $data['sha1'] != $sha1 ){
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
		$updates ++;
	}
	
	// character_phys_scores_history
	$sha1 = sha1($xml->phys_scores->hitpoints.$xml->phys_scores->hitpoints['max'].$xml->phys_scores->stamina.$xml->phys_scores->stamina['max'].$xml->phys_scores->sap.$xml->phys_scores->sap['max'].$xml->phys_scores->focus.$xml->phys_scores->focus['max']);
	$sql = "SELECT sha1 FROM character_phys_scores_history WHERE cid = '".$cid."' ORDER BY date DESC LIMIT 1";
	$req = mysql_query($sql) or die('SQL Error !<br>'.$sql.'<br>'.mysql_error());
	$data = mysql_fetch_assoc($req);
	if( $data['sha1'] != $sha1 ){
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
		$updates ++;
	}
	
	// character_equiphands_history			/!\ WARNING /!\ This is really a dirty code, please comment or change it to something more undestandable
	$sql0 = "INSERT INTO character_equiphands_history(
				cid,";
	$sql2 = "'".$xml->cid."',";
	// equipments/*
	$sha1txt = "";
	$result = $xml->xpath('/character/equipments/*');
	while(list( , $item) = each($result))
	{
		$sha1txt .= $item['part'].$item."#".$item['c']."#".$item['q'];
		$sql0 .= "	equipments_".$item['part'].",";
		$sql2 .= "'".$item."#".$item['c']."#".$item['q']."',";
	}
	// hands/*
	$result = $xml->xpath('/character/hands/*');
	while(list( , $item) = each($result))
	{
		$sha1txt .= $item['part'].$item."#".$item['q']."#".$item['sap'];
		$sql0 .= "	hands_".$item['part'].",";
		$sql2 .= "'".$item."#".$item['q']."#".$item['sap']."',";
	}
	$sha1 = sha1($sha1txt);
	$sql0 .= "	sha1,date) 
			VALUES(";
	$sql2 .= "	'".$sha1."','".$servertick."')";
	$sql0 .= $sql2;
	$sql = "SELECT sha1 FROM character_equiphands_history WHERE cid = '".$cid."' ORDER BY date DESC LIMIT 1";
	$req = mysql_query($sql) or die('SQL Error !<br>'.$sql.'<br>'.mysql_error());
	$data = mysql_fetch_assoc($req);
	if( $data['sha1'] != $sha1 ){
		$success = mysql_query($sql);
		$updates ++;
	}
	
	// character_faction_points_history
	$sha1 = sha1($xml->faction_points->kami.$xml->faction_points->karavan.$xml->faction_points->fyros.$xml->faction_points->matis.$xml->faction_points->tryker.$xml->faction_points->zorai);
	$sql = "SELECT sha1 FROM character_faction_points_history WHERE cid = '".$cid."' ORDER BY date DESC LIMIT 1";
	$req = mysql_query($sql) or die('SQL Error !<br>'.$sql.'<br>'.mysql_error());
	$data = mysql_fetch_assoc($req);
	if( $data['sha1'] != $sha1 ){
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
		$updates ++;
	}
	
	// character_fames_history
	$result = $xml->xpath('/character/fames/*');
	while(list( , $item) = each($result))
	{
		$sql = "SELECT value FROM character_fames_history WHERE cid = '".$cid."' AND faction = '".$item->getName()."' ORDER BY date DESC LIMIT 1";
		$req = mysql_query($sql) or die('SQL Error !<br>'.$sql.'<br>'.mysql_error());
		/** TODO: it might have a bug here if the faction doesn't already exist in the fames list of the character **/
		$data = mysql_fetch_assoc($req);
		if( $data['value'] != $item ){
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
			$updates ++;
		}
	}
	
	// character_skills_history
	$result = $xml->xpath('/character/skills/*');
	while(list( , $item) = each($result))
	{
		$sql = "SELECT value FROM character_skills_history WHERE cid = '".$cid."' AND skill = '".$item->getName()."' ORDER BY date DESC LIMIT 1";
		$req = mysql_query($sql) or die('SQL Error !<br>'.$sql.'<br>'.mysql_error());
		/** TODO: it might have a bug here if the skill branch doesn't already exist in the skills branchs list of the character **/
		$data = mysql_fetch_assoc($req);
		if( $data['value'] != $item ){
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
			$updates ++;
		}
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
	$sql = "SELECT sha1 FROM character_pets_history WHERE cid = '".$cid."' ORDER BY date DESC LIMIT 1";
	$req = mysql_query($sql) or die('SQL Error !<br>'.$sql.'<br>'.mysql_error());
	$data = mysql_fetch_assoc($req);
	if( $data['sha1'] != $sha1 ){
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
		$updates ++;
	}
	
}

mysql_close();

printf( "NbKey: %d - NbKeyFailled: %d - NbUpdates: %d" , $apikeyNb , $apikeyFailed , $updates);

?>