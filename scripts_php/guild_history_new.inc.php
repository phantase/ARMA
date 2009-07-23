<?php

/** NOT TO BE CALLED ALONE **/

		// Add the xml to the database as a new guild
		
		// character_history
		$sql = "INSERT INTO guild_history(
					gid, 
					shard, 
					race, 
					icon, 
					creation_date,
					apikey,
					date_first,
					date_last) 
				VALUES(
					'".$xml->gid."',
					'".$xml->shard."',
					'".$xml->race."',
					'".$xml->icon."',
					'".$xml->creation_date."',
					'".$apikey."',
					'".$servertick."',
					'".$servertick."')";
		$success = mysql_query($sql);
		//echo $sql."<br/>\n";
		
		$nbmembers = 0;
		$result = $xml->xpath('/guild/members/*');
		while(list( , $member) = each($result))
		{
			$nbmembers ++;
		}
		
		// guild_info_history
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
		//echo $sql."<br/>\n";
		
		// guild_fames_history
		$sql = "INSERT INTO guild_fames_history(gid,";
		$sqlpara = "";
		$sqlvalue = "";
		$result = $xml->xpath('/guild/fames/*');
		while(list( , $item) = each($result))
		{
			$sqlpara .= $item->getName().",";
			$sqlvalue .= "'".$item."',";
		}
		$sql .= $sqlpara . "date) VALUES('".$xml->gid."'," . $sqlvalue . "'".$servertick."')";
		$success = mysql_query($sql);
		
?>