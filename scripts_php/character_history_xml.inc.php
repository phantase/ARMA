<?php

	/** NOT TO BE CALLED ALONE **/
	/**
	 * INPUT: $apikey
	 * OUTPUT: XML file
	 **/

function WriteHistoryFromSQL($sql){
	$req = mysql_query($sql) or die('SQL Error !<br>'.$sql.'<br>'.mysql_error());
	$f = mysql_num_fields($req);
	for( $i = 0; $i < $f; $i++ ){
		$previous[$i] = "";
		$xmltab[$i] = "";
	}
	while( $data = mysql_fetch_array($req) ){
		for( $i = 0; $i < $f; $i++ ){
			if( mysql_field_name($req,$i) != 'cid' && mysql_field_name($req,$i) != 'date' && $data[$i] != $previous[$i] ){
				$previous[$i] = $data[$i];
				$xmltab[$i] .= "<".mysql_field_name($req,$i)." date=\"".$data['date']."\">".$data[$i]."</".mysql_field_name($req,$i).">";	
			}
		}
	}
	for( $i = 0; $i < $f; $i++ ){
		$tag_name = mysql_field_name($req,$i);
		if( $tag_name != 'cid' && $tag_name != 'date' ){
			if( $xmltab[$i] != "" )
				print "<".$tag_name."_history>".$xmltab[$i]."</".$tag_name."_history>";
		}
	}
}
	 
// Retrieve the part parameter of request
if( isset($_GET['part']) ){
	$part = $_GET['part'];
} else {
	$part = "infos";
}

// Retrieve cid (character id)
$sql = "SELECT * FROM character_history WHERE apikey = '$apikey' ";
$req = mysql_query($sql) or die('SQL Error !<br>'.$sql.'<br>'.mysql_error());
$data = mysql_fetch_assoc($req);
$cid = $data['cid'];

	printf("<cid>%s</cid>",$cid);
	printf("<date_history><first>%s</first><last>%s</last></date_history>",$data['date_first'],$data['date_last']);
	
// Retrieve basic info history
$sql = "SELECT * FROM character_info_history WHERE cid = '$cid' ORDER BY date ASC ";
WriteHistoryFromSQL($sql);

// Retrieve the equiphands history
print "<equipements_hands>";
$sql = "SELECT * FROM character_equiphands_history WHERE cid = '$cid' ORDER BY date ASC ";
$req = mysql_query($sql) or die('SQL Error !<br>'.$sql.'<br>'.mysql_error());
$f = mysql_num_fields($req);
for( $i = 0; $i < $f; $i++ ){
	$previous[$i] = "";
	$xmltab[$i] = "";
}
while( $data = mysql_fetch_array($req) ){
	for( $i = 0; $i < $f; $i++ ){
		if( mysql_field_name($req,$i) != 'cid' && mysql_field_name($req,$i) != 'date' && $data[$i] != $previous[$i] ){
			$previous[$i] = $data[$i];
			$datasplit = split('#',$data[$i]);
			$c = ( strpos(mysql_field_name($req,$i),'equipments')!==false ) ? "c=\"{$datasplit[1]}\"" : ""  ;
			$q = ( strpos(mysql_field_name($req,$i),'equipments')!==false ) ? "q=\"{$datasplit[2]}\"" : "q=\"{$datasplit[1]}\""  ;
			$sap = ( strpos(mysql_field_name($req,$i),'equipments')!==false ) ? "" : ($datasplit[2]!="") ? "sap=\"{$datasplit[2]}\"" : "" ;
			$xmltab[$i] .= "<item $c $q $sap date=\"".$data['date']."\">".$datasplit[0]."</item>";	
		}
	}
}
for( $i = 0; $i < $f; $i++ ){
	$tag_name = mysql_field_name($req,$i);
	if( $tag_name != 'cid' && $tag_name != 'date' ){
		if( $xmltab[$i] != "" )
			print "<".$tag_name."_history>".$xmltab[$i]."</".$tag_name."_history>";
	}
}
print "</equipements_hands>";

// Retrieve the faction_points history
print "<faction_points>";
$sql = "SELECT * FROM character_faction_points_history WHERE cid = '$cid' ORDER BY date ASC ";
WriteHistoryFromSQL($sql);
print "</faction_points>";

// Retrieve the phys_caracs history
print "<phys_caracs>";
$sql = "SELECT * FROM character_phys_caracs_history WHERE cid = '$cid' ORDER BY date ASC ";
WriteHistoryFromSQL($sql);
print "</phys_caracs>";

// Retrieve the phys_scores history
print "<phys_scores>";
$sql = "SELECT * FROM character_phys_scores_history WHERE cid = '$cid' ORDER BY date ASC ";
WriteHistoryFromSQL($sql);
print "</phys_scores>";

// Retrieve the pets history
print "<pets>";
$sql = "SELECT * FROM character_pets_history WHERE cid = '$cid' ORDER BY date ASC ";
$req = mysql_query($sql) or die('SQL Error !<br>'.$sql.'<br>'.mysql_error());
$previouspet = array( '' , '' , '' , '' );
$xmlpet = array( '' , '' , '' , '' );
while( $data = mysql_fetch_array($req) ){
	$pet0 = $data['pet0_sheet'].$data['pet0_price'].$data['pet0_satiety'].$data['pet0_status'].$data['pet0_stable'].$data['pet0_position_x'].$data['pet0_position_y'];
	if( $previouspet[0] != $pet0 ){
		$previouspet[0] = $pet0;
		$xmlpet[0] .= '<pet date="'.$data['date'].'" sheet="'.$data['pet0_sheet'].'" price="'.$data['pet0_price'].'" satiety="'.$data['pet0_satiety'].'" status="'.$data['pet0_status'].'" stable="'.$data['pet0_stable'].'">';
		if( $data['pet0_status'] == 'landscape' ){
			$xmlpet[0] .= '<position x="'.$data['pet0_position_x'].'" y="'.$data['pet0_position_y'].'"/>';
		}
		$xmlpet[0] .= '</pet>';
	}
	$pet1 = $data['pet1_sheet'].$data['pet1_price'].$data['pet1_satiety'].$data['pet1_status'].$data['pet1_stable'].$data['pet1_position_x'].$data['pet1_position_y'];
	if( $previouspet[1] != $pet1 ){
		$previouspet[1] = $pet1;
		$xmlpet[1] .= '<pet date="'.$data['date'].'" sheet="'.$data['pet1_sheet'].'" price="'.$data['pet1_price'].'" satiety="'.$data['pet1_satiety'].'" status="'.$data['pet1_status'].'" stable="'.$data['pet1_stable'].'">';
		if( $data['pet1_status'] == 'landscape' ){
			$xmlpet[1] .= '<position x="'.$data['pet1_position_x'].'" y="'.$data['pet1_position_y'].'"/>';
		}
		$xmlpet[1] .= '</pet>';
	}
	$pet2 = $data['pet2_sheet'].$data['pet2_price'].$data['pet2_satiety'].$data['pet2_status'].$data['pet2_stable'].$data['pet2_position_x'].$data['pet2_position_y'];
	if( $previouspet[2] != $pet2 ){
		$previouspet[2] = $pet2;
		$xmlpet[2] .= '<pet date="'.$data['date'].'" sheet="'.$data['pet2_sheet'].'" price="'.$data['pet2_price'].'" satiety="'.$data['pet2_satiety'].'" status="'.$data['pet2_status'].'" stable="'.$data['pet2_stable'].'">';
		if( $data['pet2_status'] == 'landscape' ){
			$xmlpet[2] .= '<position x="'.$data['pet2_position_x'].'" y="'.$data['pet2_position_y'].'"/>';
		}
		$xmlpet[2] .= '</pet>';
	}
	$pet3 = $data['pet3_sheet'].$data['pet3_price'].$data['pet3_satiety'].$data['pet3_status'].$data['pet3_stable'].$data['pet3_position_x'].$data['pet3_position_y'];
	if( $previouspet[3] != $pet3 ){
		$previouspet[3] = $pet3;
		$xmlpet[3] .= '<pet date="'.$data['date'].'" sheet="'.$data['pet3_sheet'].'" price="'.$data['pet3_price'].'" satiety="'.$data['pet3_satiety'].'" status="'.$data['pet3_status'].'" stable="'.$data['pet3_stable'].'">';
		if( $data['pet3_status'] == 'landscape' ){
			$xmlpet[3] .= '<position x="'.$data['pet3_position_x'].'" y="'.$data['pet3_position_y'].'"/>';
		}
		$xmlpet[3] .= '</pet>';
	}
}
print '<pet_history id="0">'.$xmlpet[0].'</pet_history>';
print '<pet_history id="1">'.$xmlpet[1].'</pet_history>';
print '<pet_history id="2">'.$xmlpet[2].'</pet_history>';
print '<pet_history id="3">'.$xmlpet[3].'</pet_history>';
print "</pets>";

// Retrieve the skills history
print "<skills>";
$sql = "SELECT * FROM character_skills_history WHERE cid = '$cid' ORDER BY date ASC ";
WriteHistoryFromSQL($sql);
print "</skills>";

// Retrieve the fames history
print "<fames>";
$sql = "SELECT * FROM character_fames_history WHERE cid = '$cid' ORDER BY date ASC ";
WriteHistoryFromSQL($sql);
print "</fames>";

?>