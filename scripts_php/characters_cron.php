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
	
	// update character_history
	$sql = "UPDATE character_history SET date_last = $servertick WHERE cid = '".$xml->cid."' ";
	$req = mysql_query($sql) or die('SQL Error !<br>'.$sql.'<br>'.mysql_error());
	
	// character_info_history
	$sha1 = sha1($xml->name.$xml->titleid.$xml->played_time.$xml->money.$xml->cult.$xml->civ.$xml->building.$xml->guild->gid);
	$sql = "SELECT sha1(concat(name,titleid,played_time,money,cult,civ,building,guild_gid)) AS sha1 FROM character_info_history WHERE cid = '".$cid."' ORDER BY date DESC LIMIT 1";
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
					'".$servertick."')";
		$success = mysql_query($sql);
		$updates ++;
	}
	
	// character_phys_caracs_history
	$sha1 = sha1($xml->phys_characs->constitution.$xml->phys_characs->metabolism.$xml->phys_characs->intelligence.$xml->phys_characs->wisdom.$xml->phys_characs->strength.$xml->phys_characs->wellbalanced.$xml->phys_characs->dexterity.$xml->phys_characs->will);
	$sql = "SELECT sha1(concat(constitution,metabolism,intelligence,wisdom,strength,wellbalanced,dexterity,will)) AS sha1 FROM character_phys_caracs_history WHERE cid = '".$cid."' ORDER BY date DESC LIMIT 1";
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
					'".$servertick."')";
		$success = mysql_query($sql);
		$updates ++;
	}
	
	// character_phys_scores_history
	$sha1 = sha1($xml->phys_scores->hitpoints.$xml->phys_scores->hitpoints['max'].$xml->phys_scores->stamina.$xml->phys_scores->stamina['max'].$xml->phys_scores->sap.$xml->phys_scores->sap['max'].$xml->phys_scores->focus.$xml->phys_scores->focus['max']);
	$sql = "SELECT sha1(concat(hitpoints,hitpoints_max,stamina,stamina_max,sap,sap_max,focus,focus_max)) AS sha1 FROM character_phys_scores_history WHERE cid = '".$cid."' ORDER BY date DESC LIMIT 1";
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
					'".$servertick."')";
		$success = mysql_query($sql);
		$updates ++;
	}
	
	// character_equiphands_history			/!\ WARNING /!\ This is really a dirty code, please comment or change it to something more undestandable
	$equiphands = array( 
				'equipments_feet'=>'', 
				'equipments_hands'=>'',
				'equipments_legs'=>'', 
				'equipments_arms'=>'', 
				'equipments_chest'=>'', 
				'equipments_ankle_l'=>'', 
				'equipments_ankle_r'=>'', 
				'equipments_wrist_l'=>'', 
				'equipments_wrist_r'=>'', 
				'equipments_head_dress'=>'', 
				'equipments_necklace'=>'', 
				'equipments_finger_l'=>'', 
				'equipments_finger_r'=>'', 
				'equipments_ear_l'=>'', 
				'equipments_ear_r'=>'', 
				'hands_left'=>'', 
				'hands_right'=>'' );
	$sql0 = "INSERT INTO character_equiphands_history(
				cid,";
	$sql2 = "'".$xml->cid."',";
	// equipments/*
	$result = $xml->xpath('/character/equipments/*');
	while(list( , $item) = each($result))
	{
		$equiphands['equipments_'.$item['part']] = $item."#".$item['c']."#".$item['q'];
		
		$sql0 .= "	equipments_".$item['part'].",";
		$sql2 .= "'".$item."#".$item['c']."#".$item['q']."',";
	}
	// hands/*
	$result = $xml->xpath('/character/hands/*');
	while(list( , $item) = each($result))
	{
		$equiphands['hands_'.$item['part']] = $item."#".$item['q']."#".$item['sap'];
		
		$sql0 .= "	hands_".$item['part'].",";
		$sql2 .= "'".$item."#".$item['q']."#".$item['sap']."',";
	}
	$sql0 .= "	date) 
			VALUES(";
	$sql2 .= "	'".$servertick."')";
	$sql0 .= $sql2;
	$sha1 = sha1($equiphands['equipments_feet'].$equiphands['equipments_hands'].$equiphands['equipments_legs'].$equiphands['equipments_arms'].$equiphands['equipments_chest'].$equiphands['equipments_ankle_l'].$equiphands['equipments_ankle_r'].$equiphands['equipments_wrist_l'].$equiphands['equipments_wrist_r'].$equiphands['equipments_head_dress'].$equiphands['equipments_necklace'].$equiphands['equipments_finger_l'].$equiphands['equipments_finger_r'].$equiphands['equipments_ear_l'].$equiphands['equipments_ear_r'].$equiphands['hands_left'].$equiphands['hands_right']);
	$sql = "SELECT sha1(concat(equipments_feet,equipments_hands,equipments_legs,equipments_arms,equipments_chest,equipments_ankle_l,equipments_ankle_r,equipments_wrist_l,equipments_wrist_r,equipments_head_dress,equipments_necklace,equipments_finger_l,equipments_finger_r,equipments_ear_l,equipments_ear_r,hands_left,hands_right)) AS sha1 FROM character_equiphands_history WHERE cid = '".$cid."' ORDER BY date DESC LIMIT 1";
	$req = mysql_query($sql) or die('SQL Error !<br>'.$sql.'<br>'.mysql_error());
	$data = mysql_fetch_assoc($req);
	if( $data['sha1'] != $sha1 ){
		$success = mysql_query($sql0);
		$updates ++;
	}
	
	// character_faction_points_history
	$sha1 = sha1($xml->faction_points->kami.$xml->faction_points->karavan.$xml->faction_points->fyros.$xml->faction_points->matis.$xml->faction_points->tryker.$xml->faction_points->zorai);
	$sql = "SELECT sha1(concat(kami,karavan,fyros,matis,tryker,zorai)) AS sha1 FROM character_faction_points_history WHERE cid = '".$cid."' ORDER BY date DESC LIMIT 1";
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
					date)
				VALUES(
					'".$xml->cid."',
					'".$xml->faction_points->kami."',
					'".$xml->faction_points->karavan."',
					'".$xml->faction_points->fyros."',
					'".$xml->faction_points->matis."',
					'".$xml->faction_points->tryker."',
					'".$xml->faction_points->zorai."',
					'".$servertick."')";
		$success = mysql_query($sql);
		$updates ++;
	}
	
	// character_fames_history
	$sql = "SELECT * FROM character_fames_history WHERE cid = '".$cid."' ORDER BY date DESC LIMIT 1";
	$req = mysql_query($sql) or die('SQL Error !<br>'.$sql.'<br>'.mysql_error());
	$f = mysql_num_fields($req);
	$data = mysql_fetch_array($req);
	$falselvltotalbdd = 0;	// Note: false because levels in the API for master for example start at 201, not 0... so if you are master in something, it's 20 + 50 + 100 + 150 + 200 + 250 instead of just 250...
	for( $i = 0; $i < $f; $i++ ){
		$tag_name = mysql_field_name($req,$i);
		if( $tag_name != 'cid' && $tag_name != 'date' ){
			$falselvltotalbdd += $data[$i];
		}
	}
	$falselvltotalxml = 0;
	$sql = "INSERT INTO character_fames_history(cid,";
	$sqlpara = "";
	$sqlvalue = "";
	$result = $xml->xpath('/character/fames/*');
	while(list( , $item) = each($result))
	{
		$sqlpara .= $item->getName().",";
		$sqlvalue .= "'".$item."',";
		$falselvltotalxml += $item;
	}
	$sql .= $sqlpara . "date) VALUES('".$xml->cid."'," . $sqlvalue . "'".$servertick."')";
	if( $falselvltotalbdd != $falselvltotalxml ){
		$success = mysql_query($sql);
		$updates ++;
	}
	
	// character_skills_history
	$sql = "SELECT * FROM character_skills_history WHERE cid = '".$cid."' ORDER BY date DESC LIMIT 1";
	$req = mysql_query($sql) or die('SQL Error !<br>'.$sql.'<br>'.mysql_error());
	$f = mysql_num_fields($req);
	$data = mysql_fetch_array($req);
	$falselvltotalbdd = 0;	// Note: false because levels in the API for master for example start at 201, not 0... so if you are master in something, it's 20 + 50 + 100 + 150 + 200 + 250 instead of just 250...
	for( $i = 0; $i < $f; $i++ ){
		$tag_name = mysql_field_name($req,$i);
		if( $tag_name != 'cid' && $tag_name != 'date' ){
			$falselvltotalbdd += $data[$i];
		}
	}
	$falselvltotalxml = 0;
	$sql = "INSERT INTO character_skills_history(cid,";
	$sqlpara = "";
	$sqlvalue = "";
	$result = $xml->xpath('/character/skills/*');
	while(list( , $item) = each($result))
	{
		$sqlpara .= $item->getName().",";
		$sqlvalue .= "'".$item."',";
		$falselvltotalxml += $item;
	}
	$sql .= $sqlpara . "date) VALUES('".$xml->cid."'," . $sqlvalue . "'".$servertick."')";
	if( $falselvltotalbdd != $falselvltotalxml ){
		$success = mysql_query($sql);
		$updates ++;
	}
	
	// character_pets_history
	$petsheet = array( '','','','' );
	$result = $xml->xpath('/character/pets/*');
	$sha1txt0 = "0000";
	$sha1txt1 = "0000";
	$sha1txt2 = "0000";
	$sha1txt3 = "0000";
	$sql0 = "'','','','','','',''";
	$sql1 = "'','','','','','',''";
	$sql2 = "'','','','','','',''";
	$sql3 = "'','','','','','',''";
	while(list( , $item) = each($result))
	{
		if( $item['id'] == 0 ){
			$sha1txt0 = $item['sheet'].($item['price']!=""?$item['price']:0).$item['satiety'].$item['status'].$item['stable'].($item->position['x']!=""?$item->position['x']:0).($item->position['y']!=""?$item->position['y']:0);
			$sql0 = "'".$item['sheet']."','".$item['price']."','".$item['satiety']."','".$item['status']."','".$item['stable']."','".$item->position['x']."','".$item->position['y']."'";
		} else if( $item['id'] == 1 ){
			$sha1txt1 = $item['sheet'].($item['price']!=""?$item['price']:0).$item['satiety'].$item['status'].$item['stable'].($item->position['x']!=""?$item->position['x']:0).($item->position['y']!=""?$item->position['y']:0);
			$sql1 = "'".$item['sheet']."','".$item['price']."','".$item['satiety']."','".$item['status']."','".$item['stable']."','".$item->position['x']."','".$item->position['y']."'";
		} else if( $item['id'] == 2 ){
			$sha1txt2 = $item['sheet'].($item['price']!=""?$item['price']:0).$item['satiety'].$item['status'].$item['stable'].($item->position['x']!=""?$item->position['x']:0).($item->position['y']!=""?$item->position['y']:0);
			$sql2 = "'".$item['sheet']."','".$item['price']."','".$item['satiety']."','".$item['status']."','".$item['stable']."','".$item->position['x']."','".$item->position['y']."'";
		} else if( $item['id'] == 3 ){
			$sha1txt3 = $item['sheet'].($item['price']!=""?$item['price']:0).$item['satiety'].$item['status'].$item['stable'].($item->position['x']!=""?$item->position['x']:0).($item->position['y']!=""?$item->position['y']:0);
			$sql3 = "'".$item['sheet']."','".$item['price']."','".$item['satiety']."','".$item['status']."','".$item['stable']."','".$item->position['x']."','".$item->position['y']."'";
		}
	}
	$sha1 = sha1( $sha1txt0.$sha1txt1.$sha1txt2.$sha1txt3 );
	$sql = "SELECT sha1(concat(pet0_sheet,pet0_price,pet0_satiety,pet0_status,pet0_stable,pet0_position_x,pet0_position_y,pet1_sheet,pet1_price,pet1_satiety,pet1_status,pet1_stable,pet1_position_x,pet1_position_y,pet2_sheet,pet2_price,pet2_satiety,pet2_status,pet2_stable,pet2_position_x,pet2_position_y,pet3_sheet,pet3_price,pet3_satiety,pet3_status,pet3_stable,pet3_position_x,pet3_position_y)) AS sha1 FROM character_pets_history WHERE cid = '".$cid."' ORDER BY date DESC LIMIT 1";
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
					pet0_position_x,
					pet0_position_y,
					pet1_sheet,
					pet1_price,
					pet1_satiety,
					pet1_status,
					pet1_stable,
					pet1_position_x,
					pet1_position_y,
					pet2_sheet,
					pet2_price,
					pet2_satiety,
					pet2_status,
					pet2_stable,
					pet2_position_x,
					pet2_position_y,
					pet3_sheet,
					pet3_price,
					pet3_satiety,
					pet3_status,
					pet3_stable,
					pet3_position_x,
					pet3_position_y,
					date)
				VALUES(
					'".$xml->cid."',
					".$sql0.",
					".$sql1.",
					".$sql2.",
					".$sql3.",
					'".$servertick."')";
		$success = mysql_query($sql);
		$updates ++;
	}
	
}

mysql_close();

printf( "NbKey: %d - NbKeyFailled: %d - NbUpdates: %d" , $apikeyNb , $apikeyFailed , $updates);

?>