<?php

/** NOT TO BE CALLED ALONE **/

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
					date_first,
					date_last) 
				VALUES(
					'".$xml->cid."',
					'".$xml->uid."',
					'".$xml->slot."',
					'".$xml->shard."',
					'".$xml->race."',
					'".$xml->gender."',
					'".$apikey."',
					'".$servertick."',
					'".$servertick."')";
		$success = mysql_query($sql);
		//echo $sql."<br/>\n";
		
		// character_info_history
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
		//echo $sql."<br/>\n";
		
		// character_phys_caracs_history
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
		//echo $sql."<br/>\n";
		
		// character_phys_scores_history
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
		//echo $sql."<br/>\n";
		
		// character_equiphands_history
		$sql = "INSERT INTO character_equiphands_history(
					cid,";
		$sql2 = "'".$xml->cid."',";
		
		// equipments/*
		$result = $xml->xpath('/character/equipments/*');
		while(list( , $item) = each($result))
		{
			$sql .= "	equipments_".$item['part'].",";
			$sql2 .= "'".$item."#".$item['c']."#".$item['q']."',";
		}
		// hands/*
		$result = $xml->xpath('/character/hands/*');
		while(list( , $item) = each($result))
		{
			$sql .= "	hands_".$item['part'].",";
			$sql2 .= "'".$item."#".$item['q']."#".$item['sap']."',";
		}
		$sql .= "	date) 
				VALUES(";
		$sql2 .= "	'".$servertick."')";
		
		$sql .= $sql2;
		
		$success = mysql_query($sql);
		//echo $sql."<br/>\n";
		
		// character_faction_points_history
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
		//echo $sql."<br/>\n";
		
		// character_fames_history
		$sql = "INSERT INTO character_fames_history(cid,";
		$sqlpara = "";
		$sqlvalue = "";
		$result = $xml->xpath('/character/fames/*');
		while(list( , $item) = each($result))
		{
			$sqlpara .= $item->getName().",";
			$sqlvalue .= "'".$item."',";
		}
		$sql .= $sqlpara . "date) VALUES('".$xml->cid."'," . $sqlvalue . "'".$servertick."')";
		$success = mysql_query($sql);
		
		// character_skills_history
		$sql = "INSERT INTO character_skills_history(cid,";
		$sqlpara = "";
		$sqlvalue = "";
		$result = $xml->xpath('/character/skills/*');
		while(list( , $item) = each($result))
		{
			$sqlpara .= $item->getName().",";
			$sqlvalue .= "'".$item."',";
		}
		$sql .= $sqlpara . "date) VALUES('".$xml->cid."'," . $sqlvalue . "'".$servertick."')";
		$success = mysql_query($sql);
		//echo $sql."<br/>\n";
		
		// character_pets_history
		$result = $xml->xpath('/character/pets/*');
		$sql0 = "'','','','','','',''";
		$sql1 = "'','','','','','',''";
		$sql2 = "'','','','','','',''";
		$sql3 = "'','','','','','',''";
		while(list( , $item) = each($result))
		{
			if( $item['id'] == 0 ){
				$sql0 = "'".$item['sheet']."','".$item['price']."','".$item['satiety']."','".$item['status']."','".$item['stable']."','".$item->position['x']."','".$item->position['y']."'";
			} else if( $item['id'] == 1 ){
				$sql1 = "'".$item['sheet']."','".$item['price']."','".$item['satiety']."','".$item['status']."','".$item['stable']."','".$item->position['x']."','".$item->position['y']."'";
			} else if( $item['id'] == 2 ){
				$sql2 = "'".$item['sheet']."','".$item['price']."','".$item['satiety']."','".$item['status']."','".$item['stable']."','".$item->position['x']."','".$item->position['y']."'";
			} else if( $item['id'] == 3 ){
				$sql3 = "'".$item['sheet']."','".$item['price']."','".$item['satiety']."','".$item['status']."','".$item['stable']."','".$item->position['x']."','".$item->position['y']."'";
			}
		}
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
		//echo $sql."<br/>\n";

?>